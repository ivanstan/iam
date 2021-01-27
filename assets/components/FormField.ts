import { LocaleStore } from '../services/mobx/LocaleStore';

export default class FormField {

  public name: string;
  public dirty: boolean = false;
  public required: boolean;

  public _value: any | null = null;
  public _error = null;
  public _valid: boolean = true;

  constructor(name: string, value: any, required: boolean = false) {
    this.name = name;
    this._value = value;
    this.required = required;
  }

  get value(): any {
    return this._value;
  }

  set value(value: any) {
    this._value = value;
    this.dirty = true;
    this.validate();
  }

  get error(): string {
    return this._error || ' ';
  }

  public validate(): void {
    if (this.required && !this._value) {
      this._error = LocaleStore.polyglot.t('%{name} is required.', { name: this.capitalize(this.name) });
      this._valid = false;
      return;
    }

    this._error = null;
    this._valid = true;
  };

  get valid(): boolean {
    return this.dirty && !this._valid;
  }

  set valid(value: boolean) {
    this._valid = value;
  }

  public capitalize(string: string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
  }

  public static validateFields(fields: FormField[]): boolean {
    for (let i in fields) {
      if (!fields[i]._valid) {
        return false;
      }
    }

    return true;
  }

}
