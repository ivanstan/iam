import React from 'react';
import ReactDOM from 'react-dom';
import EmailChangeForm from '../components/EmailChangeForm';

class AccountPage extends React.Component<any, any> {
  render(): React.ReactNode {
    return <>

      <EmailChangeForm />

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
