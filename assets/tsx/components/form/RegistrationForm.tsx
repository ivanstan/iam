import React from 'react';
import { translate } from 'react-polyglot';

class RegistrationForm extends React.Component<any, any> {
  render() {
    const { t } = this.props;

    return (
      <>



        <div className="text-center mb-3">
          <a href="/login">{t('Already have account? Login')}</a>
        </div>

      </>
    );
  }
}

export default translate()(RegistrationForm);
