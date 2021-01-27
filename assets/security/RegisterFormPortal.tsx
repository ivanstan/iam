import React from 'react';
import ReactDOM from 'react-dom';
import RegistrationForm from '../components/form/RegistrationForm';

export class RegisterFormPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      const form = (<RegistrationForm
          csrf={element.getAttribute('data-csrf')}
          error={element.getAttribute('data-error')}
        />
      );

      return ReactDOM.createPortal(form, element);
    }

    return null;
  }
}
