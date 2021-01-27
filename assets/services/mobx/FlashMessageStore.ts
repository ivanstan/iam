import { BackendService } from '../BackendService';

class FlashMessage extends BackendService {

  private _messages: any = [];

  init = async () => {
    try {
      this._messages = await this.request(`api/messages`);
    } catch (e) {
      this._messages = [];
    }
  };

  get messages(): any {
    const messages = this._messages;
    this.messages = [];

    return messages;
  }

  set messages(value: any) {
    this._messages = value;
  }

}

export const FlashMessageStore = new FlashMessage();
