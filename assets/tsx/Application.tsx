import React from "react";
import { I18n } from "react-polyglot";
import { withStyles } from "@material-ui/core/styles";
import { ThemeProvider } from "@material-ui/core";
import { LoginFormPortal } from "./security/LoginFormPortal";
import { NavBarPortal } from "./components/navbar/NavBarPortal";
import DeleteConfirmation from "./components/dialog/DeleteConfirmationDialog";
import { ActivityStore } from "./services/mobx/ActivityStore";
import BanIpDialog from "./components/dialog/BanIpDialog";
import { UserStore } from "./services/mobx/UserStore";
import { SettingsStore } from "./services/mobx/SettingsStore";
import { Provider } from "mobx-react";
import { theme } from "./components/Theme";
import LoaderTop from "./components/LoaderTop";
import { RegisterFormPortal } from "./security/RegisterFormPortal";
import { FlashMessageStore } from "./services/mobx/FlashMessageStore";
import { LoginInputPortal } from "./security/LoginInput/LoginInputPortal";

const useStyles: any = theme => ({
  top: {
    position: "fixed",
    width: "100%",
    zIndex: 10
  }
});

class Application extends React.Component<any, any> {

  public readonly state: any = {
    init: false,
  };

  componentDidMount = () => {
    Promise.all([UserStore.me(), SettingsStore.refresh(), FlashMessageStore.init()]).then(() => {
      this.setState({ init: true });
      ActivityStore.remove('init');
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

              <LoaderTop/>

              <DeleteConfirmation />
              <BanIpDialog />
              <NavBarPortal id="react-navbar" />

              {init && <LoginFormPortal id="login-form" />}
              {init && <RegisterFormPortal id="register-form" />}
              {init && <LoginInputPortal id="login-input" />}
            </Provider>

          </ThemeProvider>
        </I18n>
      </>
    );
  };
}

export default (withStyles(useStyles)(Application));
