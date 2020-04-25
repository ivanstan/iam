import { action, computed, configure, observable, toJS } from 'mobx';
import { BackendService } from '../BackendService';
import { createTransformer } from 'mobx-utils';
import { promisedComputedInternal } from 'computed-async-mobx/built/src/promisedComputed';

configure({ enforceActions: 'never' });

export class Settings extends BackendService {

  @observable public settings: any = {};

  queue: any[] = [];

  public getSettings = promisedComputedInternal({}, async () => {
    if (Object.keys(this.settings).length !== 0) {
      return this.settings;
    }

    this.settings = await this.refresh();

    return this.settings;
  });

  public async refresh() {
    let result = {};

    const data = await this.request(`api/settings`);

    for (let i in data) {
      if (!data.hasOwnProperty(i)) {
        continue;
      }

      let settings = data[i];
      let namespace = settings.namespace;
      let name = settings.name;

      if (!result.hasOwnProperty(namespace)) {
        result[namespace] = {};
      }

      result[namespace][name] = settings;
    }

    return result;
  }

  public set(item: any, value: any) {
    this.queue.push({
      namespace: item.namespace,
      name: item.name,
      value: value,
    });
  }

  @action
  public async flush() {
    await this.request('api/settings', {
      method: 'POST',
      body: JSON.stringify(this.queue),
    });

    this.queue = [];

    await this.refresh();
  }

  @computed
  public get value() {
    return createTransformer((param: any) => {
      const settings = toJS(this.getSettings.get());

      if (!settings.hasOwnProperty(param.namespace)) {
        return param.default;
      }

      if (!settings[param.namespace].hasOwnProperty(param.name)) {
        return param.default;
      }

      return settings[param.namespace][param.name].value;
    });
  }
}

export const SettingsStore = new Settings();
