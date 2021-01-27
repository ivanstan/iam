import FormField from './FormField';
import { LocaleStore } from '../services/mobx/LocaleStore';
import * as EmailValidator from 'email-validator';

export class EmailFormField extends FormField {

  public validate(): void {
    super.validate();

    if (!this._valid) {
      return;
    }

    if (!EmailValidator.validate(this._value)) {
      this._error = LocaleStore.polyglot.t('Please enter valid email.');
      this._valid = false;
      return;
    }

    this._error = null;
    this._valid = true;
  };

}
