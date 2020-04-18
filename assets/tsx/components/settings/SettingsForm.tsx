import React from 'react';
import ReactDOM from 'react-dom';
import { SettingService } from '../../services/SettingService';

export class SettingsForm extends React.Component<any, any> {
  render = () => {
    const test = SettingService.get('registration', 'enabled', true);

    console.log(test);

    return <div />;
  };
}

export class SettingsFormPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      return ReactDOM.createPortal(<SettingsForm />, element);
    }

    return null;
  }
}
