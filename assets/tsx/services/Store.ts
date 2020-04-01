import { action, configure } from 'mobx';

configure({ enforceActions: 'never' });

class Store {

  @action me = () => {
    const App: any = window['App'];

    if (!App.user) {
      return null;
    }

    return JSON.parse(App.user);
  };

  registrationEnabled = () => {
    const settings: any = window['App']?.settings;

    return settings?.registrationEnabled;
  };
}

export const store = new Store();
