import React from 'react';
import ReactDOM from 'react-dom';
import LoginInput from './LoginInput';

export class LoginFormPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      const form = (
        <LoginInput
          csrf={element.getAttribute('data-csrf')}
          email={element.getAttribute('data-email')}
          error={element.getAttribute('data-error')}
        />
      );

      return ReactDOM.createPortal(form, element);
    }

    return null;
  }
}
