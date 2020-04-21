import { computed, configure, observable, toJS } from 'mobx';
import { BackendService } from '../BackendService';
import { createTransformer } from 'mobx-utils';
import { promisedComputedInternal } from 'computed-async-mobx/built/src/promisedComputed';

configure({ enforceActions: 'never' });

export class Settings extends BackendService {

  @observable public settings: any = {};

  queue: any[] = [];

  public getSettings = promisedComputedInternal({}, async () => {
    const data = await this.request(`api/settings`);

    if (Object.keys(this.settings).length !== 0) {
      return this.settings;
    }

    let result = {};

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

    this.settings = result;

    return this.settings;
  });

  // @computed public get registrationEnabled() {
  //   return this.getValue('registration', 'enabled', false);
  // }
  //
  // public set(namespace: string, name: string, value) {
  //   this.queue.push({
  //     namespace: namespace,
  //     name: name,
  //     value: value,
  //   });
  // }
  //
  // @action
  // public async flush() {
  //
  //
  //   // await this.init();
  // }

  @computed
  public get value() {
    return createTransformer((param: any) => {
      const settings = toJS(this.getSettings.get());

      if (!settings.hasOwnProperty(param.namespace)) {
        return param.missing;
      }

      if (!settings[param.namespace].hasOwnProperty(param.name)) {
        return param.missing;
      }

      return settings[param.namespace][param.name].value;
    });
  }
}

export const SettingsStore = new Settings();
