import React from 'react';
import ReactDOM from 'react-dom';
import LoginForm from './LoginForm';

export class LoginFormPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      const form = (<LoginForm
          csrf={element.getAttribute('data-csrf')}
          error={element.getAttribute('data-error')}
          email={element.getAttribute('data-email')}
          registrationAllowed={element.getAttribute('data-registration-allowed')}
        />
      );

      return ReactDOM.createPortal(form, element);
    }

    return null;
  }
}
