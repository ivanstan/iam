import { observable } from 'mobx';
import { BackendService } from './BackendService';

class User extends BackendService {

  @observable public current: any = null;

  me = async () => {
    try {
      this.current = await this.request('api/user/me');
    } catch (e) {
      this.current = null;
    }

    return this.current;
  };
}

export const UserService = new User();
