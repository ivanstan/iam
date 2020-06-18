import { computed, observable } from 'mobx';
import { promisedComputed } from 'computed-async-mobx';
import Polyglot from 'node-polyglot';

class Locale {
  private _messages: any = {};

  public polyglot = new Polyglot({
    allowMissing: true
  });

  @observable current = window.localStorage.getItem('locale') || 'en';

  @computed get messages() {
    return this.getMessagesAsync.get();
  }

  private getMessagesAsync = promisedComputed({}, async () => {
    if (typeof this._messages[this.current] !== 'undefined') {
      return this._messages[this.current];
    }

    const response = await fetch(`/translations/messages.${this.current}.json`);
    const data = await response.json();

    this._messages[this.current] = data;

    return data;
  });
}

export const LocaleStore = new Locale();
