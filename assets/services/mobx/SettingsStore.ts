import { action, configure, observable } from 'mobx';
import { BackendService } from '../BackendService';
import { computedFn } from 'mobx-utils';

configure({ enforceActions: 'never' });

export class Settings extends BackendService {

  @observable public settings: any = {};

  queue: any[] = [];

  public async refresh() {
    let result = {};

    const data = await this.request(`api/settings`);

    return this.process(data);
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
    const data = await this.request('api/settings', {
      method: 'POST',
      body: JSON.stringify(this.queue),
    });

    this.queue = [];

    this.settings = this.process(data);
  }

  value = computedFn(function(this: any, param: any) {
    const settings = this.settings;

    if (!settings.hasOwnProperty(param.namespace)) {
      return param.default;
    }

    if (!settings[param.namespace].hasOwnProperty(param.name)) {
      return param.default;
    }

    return settings[param.namespace][param.name].value;
  });

  private process = (data: any) => {
    const result = {};

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
  };
}

export const SettingsStore = new Settings();
