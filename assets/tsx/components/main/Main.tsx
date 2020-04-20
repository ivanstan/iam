import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter as Router, Switch } from 'react-router-dom';
import ProtectedRoute from '../ProtectedRoute';
import { UserService } from '../../services/UserService';
import { Role } from '../../model/Role';
import { observer } from 'mobx-react';
import AdminSettingsPage from '../../pages/AdminSettingsPage';

@observer
class Main extends React.Component<any, any> {
  render = () => {
    return <Router>
      <Switch>
        <ProtectedRoute path="/admin/settings" exact
                        condition={UserService.current && UserService.current.hasRole(Role.Admin)}>
          <AdminSettingsPage />
        </ProtectedRoute>

        {/*<Route path={'/admin/settings'} component={AdminSettingsPage}/>*/}
      </Switch>
    </Router>;
  };
}

export default class MainPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      return ReactDOM.createPortal(<Main />, element);
    }

    return null;
  }
}
