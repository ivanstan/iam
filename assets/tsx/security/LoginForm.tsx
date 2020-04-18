import React from 'react';
import { Button, FormHelperText, StyledComponentProps, TextField } from '@material-ui/core';
import { PasswordField } from '../components/PasswordField';
import * as EmailValidator from 'email-validator';
import { FilledInputProps } from '@material-ui/core/FilledInput';
import { translate, TranslateProps } from 'react-polyglot';
import { withStyles } from '@material-ui/core/styles';
import { If } from 'react-if';
import { SettingService } from '../services/SettingService';

const useStyles: any = theme => ({
  container: {
    height: '100vh',
  },
  spacerBottom: {
    marginBottom: theme.spacing(3),
  },
});

interface LoginFormPropsInterface extends TranslateProps, StyledComponentProps {
  csrf: string,
  error: string,
  email: string,
}

class LoginForm extends React.Component<LoginFormPropsInterface, any> {
  private form: any;
  private email: any;
  private password: any;

  readonly state: any = {
    dirty: false,
    email: '',
    emailError: '',
    password: '',
    passwordError: '',
    loading: false,
    formError: '',
  };

  constructor(props: any, context: any) {
    super(props, context);

    this.state.formError = props.error;
    this.state.email = props.email;

    this.form = React.createRef<HTMLFormElement>();
    this.email = React.createRef<HTMLInputElement>();
    this.password = React.createRef<HTMLInputElement>();
  }

  submit = async () => {
    const { email, password } = this.state;
    const { t } = this.props;

    this.setState({ loading: true });
  };

  handleChange = (prop: string, event: any) => {
    let state = this.state;
    state[prop] = event.target.value;
    state['dirty'] = true;
    this.setState(state);
    this.validate();
  };

  validate = () => {
    const { t } = this.props;
    let emailError = '';
    let passwordError = '';

    if (!this.state.email) {
      emailError = t('Email is required.');
    }

    if (!this.isEmailValid()) {
      emailError = t('Please enter valid email.');
    }

    if (!this.state.password) {
      passwordError = t('Password is required.');
    }

    this.setState({ emailError: emailError, passwordError: passwordError });
  };

  isValid = (): boolean => {
    const { emailError, passwordError } = this.state;

    return !emailError && !passwordError;
  };

  isSubmitEnabled = (): boolean => {
    const { email, password } = this.state;

    if (!email || !password) {
      return false;
    }

    return !this.state.loading && this.isValid();
  };

  private isEmailValid = (): boolean => {
    return EmailValidator.validate(this.state.email);
  };

  private onEmailFieldKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && this.isEmailValid()) {
      this.password.focus();
    }
  };

  private onPasswordFieldKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && this.isSubmitEnabled()) {
      this.form.dispatchEvent(new Event('submit'));
    }
  };

  render() {
    const { t, classes, error } = this.props;
    const { email, password, emailError, passwordError, dirty, formError } = this.state;

    const registrationAllowed = SettingService.get('registration', 'enable', true);

    return <>
      <div className="p-5">
        <h1 className="h3 mb-3">Login</h1>
        <form method="post" ref={ref => this.form = ref}>
          <FormHelperText error={true} style={{ marginBottom: 30, marginTop: 15 }}>
            {formError || error || ' '}
          </FormHelperText>

          {/*<Alert severity="error">*/}
          {/*  {formError || error || ' '}*/}
          {/*</Alert>*/}

          <TextField
            id="login-form-email"
            className={classes?.spacerBottom}
            InputProps={{ autoComplete: 'email' } as FilledInputProps}
            autoFocus
            name={'email'}
            label={t('Email')}
            variant="outlined"
            value={email}
            fullWidth
            onKeyPress={this.onEmailFieldKeyPress}
            error={dirty && (emailError !== '')}
            helperText={dirty && (emailError || ' ')}
            onChange={e => this.handleChange('email', e)}
            ref={input => this.email = input}
          />

          <PasswordField
            id="login-form-password"
            name={'password'}
            label={t('Password')}
            variant="outlined" value={password}
            fullWidth
            onChange={(e: Event) => this.handleChange('password', e)}
            onKeyPress={this.onPasswordFieldKeyPress}
            error={dirty && (passwordError !== '')}
            helperText={passwordError}
            inputRef={ref => this.password = ref}
            className={classes?.spacerBottom}
          />

          <input type="hidden" name="_csrf_token" value={this.props.csrf} />
          <input type="checkbox" name="_remember_me" defaultChecked className="d-none" />

          <Button
            type="submit"
            fullWidth
            variant="contained"
            color="primary"
            size="large"
            disabled={!this.isSubmitEnabled()}
            data-test="submit"
          >
            {t('Login')}
          </Button>
        </form>
      </div>

      <div className="d-flex px-5 pb-5 justify-content-between">
        <Button color="primary" href="/recovery">{t('Forgot your password?')}</Button>
        <If condition={registrationAllowed}>
          <Button variant="contained" color="primary" href="/register"
                  data-test="register-link">{t('Register')}</Button>
        </If>
      </div>
    </>;
  }
}

export default translate()(withStyles(useStyles)(LoginForm));
