import { computed, observable } from 'mobx';
import { promisedComputed } from 'computed-async-mobx';

class SettingsStore {

  @observable settings = {};

  queue: any[] = [];

  @computed get init() {
    return this.getMessagesAsync.get();
  }

  protected getMessagesAsync = promisedComputed({}, async () => {
    const response = await fetch(`/admin/settings`);
    const data = await response.json();

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

    return data;
  });

  @observable
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

    this.settings[namespace][name] = {
      namespace: namespace,
      name: name,
      value: value,
    };
  }

  public flush() {

  }
}

export const Settings = new SettingsStore();
