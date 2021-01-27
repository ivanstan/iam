import React from 'react';
import { translate } from 'react-polyglot';
import { Button, TextField } from '@material-ui/core';
import FormError from './FormError';
import { FilledInputProps } from '@material-ui/core/FilledInput';
import AbstractComponent from '../AbstractComponent';
import PasswordRepeatInput from './PasswordRepeatInput';
import { withStyles } from '@material-ui/core/styles';
import { EmailFormField } from '../EmailFormField';
import FormField from '../FormField';
import FlashMessages from '../FlashMessages';

const useStyles: any = theme => ({
  spacerBottom: {
    marginBottom: theme.spacing(3),
  },
});

class RegistrationForm extends AbstractComponent<any, any> {
  private form: any;
  private email: any;
  private password: any;

  public readonly state = {
    email: new EmailFormField('email', null, true),
    password: new FormField('password', null, true),
  };

  private onEmailFieldKeyPress: any;

  constructor(props: any, context: any) {
    super(props, context);

    this.form = React.createRef<HTMLFormElement>();
    this.email = React.createRef<HTMLInputElement>();
    this.password = React.createRef<HTMLInputElement>();
  }

  private isSubmitEnabled = (): boolean => {
    return FormField.validateFields([this.state.email, this.state.password]);
  };

  onPasswordChange = (valid: boolean, password: string) => {
    const state = this.state;
    state['password'].valid = valid;
    state['password'].value = password;

    this.setState(state);
  };

  render() {
    const { t, classes, error, formError } = this.props;
    const { email, password } = this.state;

    return (
      <div className="p-5">
        <h1 className="h3 mb-3">Register</h1>
        <form method="post" ref={ref => this.form = ref}>
          <FormError text={formError || error} />

          <FlashMessages/>

          <TextField
            id="login-form-email"
            InputProps={{ autoComplete: 'email' } as FilledInputProps}
            autoFocus
            name={email.name}
            label={t('Email')}
            variant="outlined"
            value={email.value}
            fullWidth
            onKeyPress={this.onEmailFieldKeyPress}
            error={email.valid}
            helperText={email.error}
            onChange={e => this.setStateFieldValue('email', e.target.value)}
            ref={input => this.email = input}
          />

          <PasswordRepeatInput onChange={this.onPasswordChange} value={password.value} />

          <div className="text-center mb-3">
            <a href="/login">{t('Already have account? Login')}</a>
          </div>

          <Button
            type="submit"
            fullWidth
            variant="contained"
            color="primary"
            size="large"
            disabled={!this.isSubmitEnabled()}
            data-test="submit"
          >
            {t('Register')}
          </Button>

        </form>
      </div>
    );
  }
}

export default translate()(withStyles(useStyles)(RegistrationForm));
