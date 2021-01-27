import React from 'react';
import { PasswordInput } from './PasswordInput';
import { translate } from 'react-polyglot';
import { TextField } from '@material-ui/core';
import { TranslateProps } from 'react-polyglot/src/translate';
import PasswordStrengthBar from 'react-password-strength-bar/dist';

interface PasswordRepeatInputPropsInterface extends TranslateProps {
  onChange?: Function,
  value?: string
}

class PasswordRepeatInput extends React.Component<PasswordRepeatInputPropsInterface, any> {

  public readonly state = {
    password: '',
    repeat: '',
    repeatError: null,
    score: 0,
  };

  componentDidMount = (): void => {
    const { value } = this.props;

    this.setState({
      password: value,
      repeat: value,
    });
  };

  isDirty = (name: string): boolean => {
    return this.state[name] !== null;
  };

  setStateValue = (name: string, value): void => {
    const state = this.state;
    state[name] = value;
    this.setState(state, () => {
      const { onChange } = this.props;

      if (onChange !== null && typeof onChange === 'function') {
        onChange(this.isValid(), this.state.password);
      }
    });
  };

  isValid = (): boolean => {
    const { password, repeat, score } = this.state;
    const { t } = this.props;

    let repeatError: string | null = null;
    let result: boolean = true;

    const passwordsMatch = password === repeat;

    if (!passwordsMatch || score < 3) {
      result = false;
    }

    if (!passwordsMatch && this.isDirty('repeat')) {
      repeatError = t('Passwords don\'t match.');
    }

    this.setState({
      repeatError: repeatError,
    });

    return result;
  };

  render(): React.ReactNode {
    const { t } = this.props;
    const { password, repeat, repeatError } = this.state;

    return <>
      <PasswordInput
        data-test="password"
        variant="outlined"
        fullWidth
        onChange={e => this.setStateValue('password', e.target.value)}
        value={password}
        label={t('New password')} name={'current'}
      />

      <TextField
        type="password"
        data-test="repeat-password"
        label={t('Repeat password')}
        variant="outlined"
        fullWidth
        value={repeat}
        onChange={e => this.setStateValue('repeat', e.target.value)}
        error={Boolean(this.isDirty('repeat') && repeatError)}
        helperText={repeatError}
      />

      <PasswordStrengthBar
        onChangeScore={score => this.setStateValue('score', score)}
        password={password || ''}
        barColors={['#dddddd', '#ef4836', '#f6b44d', '#2b90ef', '#25c281']}
        shortScoreWord={'too short'}
        minLength={6}
        scoreWords={['weak', 'weak', 'okay', 'good', 'strong']}
      />
    </>;
  };
}

export default translate()(PasswordRepeatInput);
