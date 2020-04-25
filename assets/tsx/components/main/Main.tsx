import React from 'react';
import ReactDOM from 'react-dom';
import { HashRouter as Router, Switch, Route } from 'react-router-dom';
import AdminSettingsPage from '../../pages/AdminSettingsPage';
import { inject, observer } from 'mobx-react';
import ProtectedRoute from '../ProtectedRoute';
import { Role } from '../../model/Role';

@inject('user')
class Main extends React.Component<any, any> {
  render = () => {
    const { user } = this.props;

    return <Router>
      <Switch>
        <ProtectedRoute path="/admin/settings" exact
                        condition={user.current && user.current.hasRole(Role.Admin)}>
          <AdminSettingsPage />
        </ProtectedRoute>
      </Switch>
    </Router>;
  };
}

export default class MainPortal extends React.Component<any, any> {
  render = () => {
    const element = document.getElementById(this.props.id);

    if (element) {
      return ReactDOM.createPortal(<Main />, element);
    }

    return null;
  };
}
