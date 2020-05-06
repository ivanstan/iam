import React from 'react';
import { SettingsStore } from './services/mobx/SettingsStore';
import { ActivityStore } from './services/mobx/ActivityStore';
import { UserStore } from './services/mobx/UserStore';
import { observer, Provider } from 'mobx-react';
import { I18n } from 'react-polyglot';
import { theme } from './components/Theme';
import { ThemeProvider } from '@material-ui/core';
import { If } from 'react-if';
import ProtectedRoute from './components/ProtectedRoute';
import { Role } from './model/Role';
import { HashRouter as Router, Route, Switch } from 'react-router-dom';
import HomePage from './pages/HomePage';
import { AccountPage } from './pages/AccountPage';
import AdminSettingsPage from './pages/AdminSettingsPage';
import LoaderTop from './components/LoaderTop';
import RegistrationPage from './pages/RegistrationPage';

@observer
export class ReactApplication extends React.Component<any, any> {

  componentDidMount = () => {
    Promise.all([UserStore.me(), SettingsStore.refresh()]).then(() => {
      ActivityStore.remove('init');
    });
  };

  render(): React.ReactNode {
    const { classes } = this.props;

    const isAdmin = UserStore.current && UserStore.current.hasRole(Role.Admin);
    const isUser = UserStore.current;

    return (
      <I18n allowMissing locale={'en'} messages={{}}>
        <Provider settings={SettingsStore} activity={ActivityStore} user={UserStore}>
          <ThemeProvider theme={theme}>
            <LoaderTop />
            <If condition={!ActivityStore.isPending('init')}>
              <Router>
                <Switch>
                  <ProtectedRoute exact path="/admin/settings" condition={isAdmin} component={<AdminSettingsPage />} />
                  <ProtectedRoute exact path="/user/account" condition={isUser} component={<AccountPage />} />
                  <Route exact path="/" component={HomePage} />
                  <Route exact path="/register" component={RegistrationPage} />
                </Switch>
              </Router>
            </If>
          </ThemeProvider>
        </Provider>
      </I18n>
    );
  };
}

export default ReactApplication;
