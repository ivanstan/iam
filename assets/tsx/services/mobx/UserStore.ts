import { observable } from 'mobx';
import { BackendService } from '../BackendService';
import { UserModel } from '../../model/UserModel';
import {plainToClass} from "class-transformer";

class User extends BackendService {

  @observable public current: any = null;

  me = async () => {
    try {
      const response = await this.request('api/user/me');

      this.current = plainToClass(UserModel, response);
    } catch (e) {
      this.current = null;
    }

    return this.current;
  };
}

export const UserStore = new User();
