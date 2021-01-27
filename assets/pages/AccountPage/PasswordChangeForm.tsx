import React from 'react';
import { translate } from 'react-polyglot';
import { PasswordInput } from '../../components/form/PasswordInput';
import AbstractComponent from '../../components/AbstractComponent';
import { Button } from '@material-ui/core';
import PasswordRepeatInput from '../../components/form/PasswordRepeatInput';
import UserService from '../../services/UserService';
import { If } from 'react-if';
import { Alert } from '@material-ui/lab';

class PasswordChangeForm extends AbstractComponent<any, any> {

  public readonly state = {
    error: null,
    current: '',
    password: '',
    valid: false,
    message: null,
  };

  isSubmitEnabled = (): boolean => {
    const { current, valid, password } = this.state;

    if (!valid) {
      return false;
    }

    if (current === '' || password === '') {
      return false;
    }

    return true;
  };

  submit = async () => {
    const { current, password } = this.state;
    const { t } = this.props;

    if (!this.isSubmitEnabled()) {
      return;
    }

    try {
      const response = await UserService.changePassword(current || '', password || '');

      // ToDo: should clear for inputs

      this.setState({
        message: t('You have successfully changed your password.'),
        error: null,
      });

    } catch (e) {
      this.setState({ error: e.message, message: null });
    } finally {
      // ToDo: stop activity, enable button again
    }
  };

  onChange = (valid: boolean, password: string) => {
    this.setState({
      password: password,
      valid: valid,
    });
  };

  render(): React.ReactNode {
    const { current, password, error, message } = this.state;
    const { t } = this.props;

    return <div className="my-3 password-change-form">
      <If condition={Boolean(this.state.error)}>
        <Alert severity="error">{this.state.error}</Alert>
      </If>

      <If condition={Boolean(this.state.message)}>
        <Alert severity="success">{this.state.message}</Alert>
      </If>

      <PasswordInput
        data-test="current-password"
        variant="outlined"
        fullWidth
        onChange={e => this.setStateValue('current', e.target.value)}
        value={current}
        label={t('Current password')} name={'current'}
      />

      <PasswordRepeatInput onChange={this.onChange} value={password} />

      <Button
        data-test="submit"
        variant="contained"
        color="primary"
        size="large"
        disabled={!this.isSubmitEnabled()}
        onClick={this.submit}
      >{t('Change password')}</Button>
    </div>;
  };
}

export default translate()(PasswordChangeForm);
