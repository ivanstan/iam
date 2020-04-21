import React from 'react';
import { FormControlLabel, Switch } from '@material-ui/core';
import { inject, observer } from 'mobx-react';

@inject('settings')
@observer
export default class AdminSettingsPage extends React.Component<any, any> {

  public onBooleanChange(namespace: string, name: string, value: boolean) {

    console.log(namespace, name, value);

  }

  render = (): any => {
    const { settings } = this.props;

    return <div className="container mt-3">
      <div className="card pt-3 px-3 mb-3">
        <FormControlLabel
          control={<Switch
            // checked={settings.registrationEnabled}
            onChange={(event) => this.onBooleanChange('registration', 'enabled', event.target.checked)} />}
          label="Enable registration"
        />
      </div>
    </div>;
  };
}
