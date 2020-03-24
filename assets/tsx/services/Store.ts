import { action, configure, observable } from 'mobx';

configure({ enforceActions: 'never' });

class Store {

  @action me = () => {
    const App: any = window['App'];

    if (!App.user) {
      return null;
    }

    return JSON.parse(App.user);
  };
}

export const store = new Store();
