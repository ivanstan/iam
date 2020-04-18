import { action, configure } from 'mobx';

configure({ enforceActions: 'never' });

class Store {

  locale = () => {
    const settings: any = window['App']?.settings;

    return settings?.locale || 'en';
  };

  registrationEnabled = () => {
    const settings: any = window['App']?.settings;

    return settings?.registrationEnabled;
  };
}

export const store = new Store();
