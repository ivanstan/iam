import { action, observable } from 'mobx';
import { createTransformer } from 'mobx-utils';

class Activity {

  @observable private _pending: string[] = ['init'];

  @action
  public add(name: string): void {
    this._pending.push(name as never);
  }

  @action
  public remove(name: string): void {
    const index = this._pending.indexOf(name as never);
    if (index > -1) {
      this._pending.splice(index, 1);
    }
  }

  @observable
  public isPending = createTransformer(() => {
    return this._pending.length > 0;
  });

}

export const ActivityStore = new Activity();
