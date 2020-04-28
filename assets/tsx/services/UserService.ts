import { BackendService } from './BackendService';
import { UserModel } from '../model/UserModel';

class User extends BackendService {

  changePassword(currentPassword: string, newPassword: string) {
    return this.request('api/user/password/change', {
      method: 'POST',
      body: JSON.stringify({
        currentPassword: currentPassword,
        newPassword: newPassword,
      }),
    });
  }

  deleteSession(session: any) {
    return this.request('api/session/' + session.id, {
      method: 'DELETE'
    })
  }


}

export default new User();
