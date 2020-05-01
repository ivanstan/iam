import React from 'react';
import { BooleanSettings } from './BooleanSettings';
import { RegistrationEnabledSettings } from '../../model/Settings';
import { translate } from 'react-polyglot';
import NavBar from '../../components/navbar/NavBar';

class AdminSettingsPage extends React.Component<any, any> {

  render(): React.ReactNode {
    const { t } = this.props;

    return (
      <>
        <NavBar />
        <div className="container mt-3">
          <div className="card pt-3 px-3 pb-2">
            <BooleanSettings
              item={RegistrationEnabledSettings}
              label={t('Registration enabled')}
            />
          </div>
        </div>
      </>
    );
  };
}

export default translate()(AdminSettingsPage);
