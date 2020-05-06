import React from 'react';
import { translate } from 'react-polyglot';

class RegistrationPage extends React.Component<any, any> {
  render() {
    const { t } = this.props;

    return (
      <div className="container mx-auto vertical-center d-flex">
        <div className="max-w-sm rounded overflow-hidden shadow-lg mx-auto p-5">


          <div className="text-center mb-3">
            <a href="{{ path('app_login') }}">{t('Already have account? Login')}</a>
          </div>


        </div>
      </div>
    );
  }
}

export default translate()(RegistrationPage);
