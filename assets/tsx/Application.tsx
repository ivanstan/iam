import React from 'react';
import { I18n } from 'react-polyglot';
import { createMuiTheme, withStyles } from '@material-ui/core/styles';
import blue from '@material-ui/core/colors/blue';
import { LinearProgress, ThemeProvider } from '@material-ui/core';
import { LoginFormPortal } from './security/LoginFormPortal';
import { NavBarPortal } from './components/navbar/NavBarPortal';
import DeleteConfirmation from './components/DeleteConfirmation';
import { ActivityStore } from './services/mobx/ActivityStore';
import BanIpDialog from './components/BanIpDialog';
import { UserStore } from './services/mobx/UserStore';
import { SettingsStore } from './services/mobx/SettingsStore';
import MainPortal from './components/main/Main';
import { Provider } from 'mobx-react';
import { If } from 'react-if';
import { AccountPagePortal } from './pages/AccountPage';

const theme = createMuiTheme({
  palette: {
    primary: blue,
  },
});

const useStyles: any = theme => ({
  top: {
    position: 'fixed',
    width: '100%',
    zIndex: 10,
  },
});

class Application extends React.Component<any, any> {

  public readonly state: any = {
    init: false,
  };

  componentDidMount = () => {
    Promise.all([UserStore.me(), SettingsStore.getSettings.get()]).then(() => {
      this.setState({ init: true });
    });
  };

  render = () => {
    const { init } = this.state;
    const { classes } = this.props;

    return (
      <>
        <I18n allowMissing locale={'en'} messages={{}}>
          <ThemeProvider theme={theme}>
            <Provider settings={SettingsStore} activity={ActivityStore} user={UserStore}>

              <If condition={ActivityStore.isPending({ activity: null })}>
                <LinearProgress color="secondary" className={classes.top} />
              </If>

              <DeleteConfirmation />
              <BanIpDialog />
              <NavBarPortal id="react-navbar" />

              {init && <AccountPagePortal id="react-account-page" />}
              {init && <LoginFormPortal id="login-form" />}
            </Provider>

          </ThemeProvider>
        </I18n>
      </>
    );
  };
}

export default (withStyles(useStyles)(Application));
