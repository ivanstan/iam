import React from 'react';
import { I18n } from 'react-polyglot';
import { createMuiTheme } from '@material-ui/core/styles';
import blue from '@material-ui/core/colors/blue';
import { ThemeProvider } from '@material-ui/core';
import { LoginFormPortal } from './security/LoginFormPortal';
import { NavBarPortal } from './components/navbar/NavBarPortal';
import DeleteConfirmation from './components/DeleteConfirmation';
import { EmailChangeFormPortal } from './components/EmailChangeForm';

const theme = createMuiTheme({
  palette: {
    primary: blue,
  },
});

class Application extends React.Component<any, any> {

  render() {
    return (
      <>
        <I18n allowMissing locale={'en'} messages={{}}>
          <ThemeProvider theme={theme}>
            <DeleteConfirmation />
            <NavBarPortal id="react-navbar" />
            <LoginFormPortal id="login-form" />
            <EmailChangeFormPortal id="react-email-change-form" />
          </ThemeProvider>
        </I18n>
      </>
    );
  }
}

export default Application;
