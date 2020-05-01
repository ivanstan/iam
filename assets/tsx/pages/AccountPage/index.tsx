import React from 'react';
import EmailChangeForm from './EmailChangeForm';
import PasswordChangeForm from './PasswordChangeForm';
import DeactivateAccount from './DeactivateAccount';
import DeleteAccount from './DeleteAccount';
import UserSessions from './UserSessions';
import NavBar from '../../components/navbar/NavBar';
import { inject, observer } from 'mobx-react';

@inject('user')
@observer
export class AccountPage extends React.Component<any, any> {

  render(): React.ReactNode {
    const { user } = this.props;

    return (
      <>
        <NavBar />

        <div className="container">

          <div className="row">
            <div className="col-md-6">
              <PasswordChangeForm />
            </div>
            <div className="col-md-6">
              <EmailChangeForm />
            </div>
          </div>

          <div className="row">
            <div className="col-md-6">
              <DeactivateAccount />
            </div>
            <div className="col-md-6">
              <DeleteAccount />
            </div>
          </div>

          <div className="row">
            <div className="col-12">
              <UserSessions user={user.current} />
            </div>
          </div>
        </div>
      </>
    );
  };
}
