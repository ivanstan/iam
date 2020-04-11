import { action, computed, observable } from "mobx";
import { createTransformer } from "mobx-utils";

class ActivityStore {

  @observable private _pending: string[] = [];

  @action public add(name: string): void {
    this._pending.push(name as never);
  }

  @action public remove(name: string): void {
    const index = this._pending.indexOf(name as never);
    if (index > -1) {
      this._pending.splice(index, 1);
    }
  }

  @observable
  public isPending = createTransformer((name = null) => {
    return this._pending.length > 0;
  });

}

export const activity = new ActivityStore();
