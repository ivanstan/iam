import React from 'react';
import ReactDOM from 'react-dom';
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
        <UserSessions user={user.current} />
        <PasswordChangeForm />
        <EmailChangeForm />
        <DeactivateAccount />
        <DeleteAccount />
      </>
    );
  };
}
