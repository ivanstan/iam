import React from 'react';
import { Route, Redirect } from 'react-router';

export default class ProtectedRoute extends React.Component<any, any> {
  render() {
    const { children, condition, ...rest } = this.props;
    return (
      <Route
        {...rest}
        render={({ location }) =>
          condition ? (
            children
          ) : (
            <Redirect
              to={{
                pathname: '/login',
                state: { from: location },
              }}
            />
          )
        }
      />
    );
  }
}
