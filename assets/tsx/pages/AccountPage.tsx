import React from 'react';
import ReactDOM from 'react-dom';
import EmailChangeForm from '../components/form/EmailChangeForm';
import PasswordChangeForm from '../components/form/PasswordChangeForm';
import DeactivateAccount from '../components/DeactivateAccount';
import DeleteAccount from '../components/DeleteAccount';
import UserSessions from '../components/UserSessions';
import NavBar from '../components/navbar/NavBar';
import { inject, observer } from 'mobx-react';
import { If } from 'react-if';

@inject('user')
@observer
export class AccountPage extends React.Component<any, any> {

  render = (): React.ReactNode => {
    const { user } = this.props;

    console.log(user);

    return (
      <>
        <NavBar />
        <If condition={user.current}>
          <UserSessions user={user.current} />
          <PasswordChangeForm />
          <EmailChangeForm />
          <DeactivateAccount />
          <DeleteAccount />
        </If>
      </>
    );
  };
}

export class AccountPagePortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      return ReactDOM.createPortal(<AccountPage />, element);
    }

    return null;
  }
}
