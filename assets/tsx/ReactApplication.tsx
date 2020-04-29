import React from 'react';
import { SettingsStore } from './services/mobx/SettingsStore';
import { ActivityStore } from './services/mobx/ActivityStore';
import { UserStore } from './services/mobx/UserStore';
import { observer, Provider } from 'mobx-react';
import { I18n } from 'react-polyglot';
import { theme } from './components/Theme';
import { LinearProgress, ThemeProvider } from '@material-ui/core';
import { If } from 'react-if';
import ProtectedRoute from './components/ProtectedRoute';
import { Role } from './model/Role';
import { HashRouter as Router, Route, Switch } from 'react-router-dom';
import { withStyles } from '@material-ui/core/styles';
import { AccountPage } from './pages/AccountPage';
import HomePage from './pages/HomePage';

const styles: any = theme => ({
  top: {
    position: 'fixed',
    width: '100%',
    zIndex: 10,
  },
});

@observer
export class ReactApplication extends React.Component<any, any> {

  public readonly state: any = {
    init: false,
  };

  componentDidMount = () => {
    Promise.all([UserStore.me(), SettingsStore.getSettings.get()]).then(() => {
      this.setState({ init: true });
    });
  };

  render = (): React.ReactNode => {
    const { classes } = this.props;

    const isAdmin = UserStore.current && UserStore.current.hasRole(Role.Admin);
    const isUser = UserStore.current;

    console.log(isAdmin);

    return (
      <I18n allowMissing locale={'en'} messages={{}}>
        <Provider settings={SettingsStore} activity={ActivityStore} user={UserStore}>
          <ThemeProvider theme={theme}>
            <If condition={ActivityStore.isPending({ activity: null })}>
              <LinearProgress color="secondary" className={classes.top} />
            </If>

            <Router>
              <Switch>
                <ProtectedRoute exact path="/user/account" condition={true} component={<AccountPage />} />
                <Route exact path="/" component={HomePage} />
              </Switch>
            </Router>

          </ThemeProvider>
        </Provider>
      </I18n>
    );
  };
}

export default (withStyles(styles)(ReactApplication));
