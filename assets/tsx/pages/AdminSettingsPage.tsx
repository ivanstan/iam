import React from 'react';
import { BooleanSettings } from './components/BooleanSettings';
import { RegistrationEnabledSettings } from '../model/Settings';
import { translate } from 'react-polyglot';
import { observer } from 'mobx-react';

@observer
class AdminSettingsPage extends React.Component<any, any> {

  render = (): any => {
    const { t } = this.props;

    return <div className="container mt-3">
      <div className="card pt-3 px-3 pb-2">
        <BooleanSettings
          item={RegistrationEnabledSettings}
          label={t('Registration enabled')}
        />
      </div>
    </div>;
  };
}

export default translate()(AdminSettingsPage);
