import React from 'react';
import ReactDOM from 'react-dom';
import { Settings } from '../../services/SettingsStore';

export class SettingsForm extends React.Component<any, any> {
  render = () => {
    const test = Settings.get('registration', 'enabled', true);

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
