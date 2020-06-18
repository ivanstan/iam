import React from 'react';
import { translate } from 'react-polyglot';
import RegistrationForm from '../components/form/RegistrationForm';

class RegistrationPage extends React.Component<any, any> {
  render() {
    const { t } = this.props;

    return (
      <div className="container mx-auto d-flex">
        <div className="max-w-sm rounded overflow-hidden shadow-lg mx-auto p-5">
          <RegistrationForm />
        </div>
      </div>
    );
  }
}

export default translate()(RegistrationPage);
