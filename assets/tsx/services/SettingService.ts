import { observable } from 'mobx';
import { BackendService } from './BackendService';

class Settings extends BackendService {

  @observable
  settings = {};

  queue: any[] = [];

  public async init() {
    const data = await this.request(`/api/settings`);

    for (let i in data) {
      if (!data.hasOwnProperty(i)) {
        continue;
      }

      let settings = data[i];
      let namespace = settings.namespace;
      let name = settings.name;

      if (this.settings.hasOwnProperty(namespace)) {
        settings[namespace] = {};
      }

      settings[namespace][name] = data;
    }
  }

  public get(namespace: string, name: string, missing: any = null) {
    if (!this.settings.hasOwnProperty(namespace)) {
      return missing;
    }

    if (!this.settings[namespace].hasOwnProperty(name)) {
      return missing;
    }

    return this.settings[namespace][name].value;
  }

  public set(namespace: string, name: string, value) {
    this.queue.push({
      namespace: namespace,
      name: name,
      value: value,
    });
  }

  public async flush() {



    await this.init();
  }
}

export const SettingService = new Settings();
