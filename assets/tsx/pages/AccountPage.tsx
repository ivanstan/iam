import React from 'react';
import ReactDOM from 'react-dom';
import EmailChangeForm from '../components/form/EmailChangeForm';
import PasswordChangeForm from '../components/form/PasswordChangeForm';
import DeactivateAccount from '../components/DeactivateAccount';
import DeleteAccount from '../components/DeleteAccount';
import UserSessions from '../components/UserSessions';
import { UserStore } from '../services/mobx/UserStore';

class AccountPage extends React.Component<any, any> {
  render(): React.ReactNode {
    return <>
      <UserSessions user={UserStore.current} />
      <PasswordChangeForm />
      <EmailChangeForm />
      <DeactivateAccount />
      <DeleteAccount />
    </>;
  }
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
