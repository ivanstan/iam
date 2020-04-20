import React from 'react';
import { I18n } from 'react-polyglot';
import { createMuiTheme } from '@material-ui/core/styles';
import blue from '@material-ui/core/colors/blue';
import { LinearProgress, ThemeProvider } from '@material-ui/core';
import { If } from 'react-if';
import { LoginFormPortal } from './security/LoginFormPortal';
import { NavBarPortal } from './components/navbar/NavBarPortal';
import DeleteConfirmation from './components/DeleteConfirmation';
import { EmailChangeFormPortal } from './components/EmailChangeForm';
import { activity } from './services/ActivityStore';
import BanIpDialog from './components/BanIpDialog';
import { UserService } from './services/UserService';
import { SettingService } from './services/SettingService';
import MainPortal from './components/main/Main';
import { Provider } from 'mobx-react';

const theme = createMuiTheme({
  palette: {
    primary: blue,
  },
});

class Application extends React.Component<any, any> {

  // LinearProgress should have position fixed

  public state: any = {
    init: false,
  };

  componentDidMount = () => {
    Promise.all([UserService.me(), SettingService.init()]).then(() => this.setState({ init: true }));
  };

  render() {
    return (
      <>
        <I18n allowMissing locale={'en'} messages={{}}>
          <ThemeProvider theme={theme}>
            <Provider settings={SettingService}>

            <If condition={activity.isPending({ activity: null })}>
              <LinearProgress color="secondary" />
            </If>
            <DeleteConfirmation />
            <BanIpDialog />
            <NavBarPortal id="react-navbar" />
            <LoginFormPortal id="login-form" />
            <EmailChangeFormPortal id="react-email-change-form" />

            <If condition={this.state.init}>
              <MainPortal id="root" />
            </If>
            </Provider>

          </ThemeProvider>
        </I18n>
      </>
    );
  }
}

export default Application;
