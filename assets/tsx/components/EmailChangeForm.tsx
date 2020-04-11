import React from 'react';
import { translate } from 'react-polyglot';
import ReactDOM from 'react-dom';
import { Button, TextField } from '@material-ui/core';
import { FilledInputProps } from '@material-ui/core/FilledInput';
import * as EmailValidator from 'email-validator';

class EmailChangeForm extends React.Component<any, any> {

  public readonly state: any = {
    dirty: true,
    error: '',
  };

  handleChange = (name: string, value: string) => {
    const state = this.state;
    state[name] = value;

    this.setState(state);
    this.validate();
  };

  private onKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && this.isSubmitEnabled()) {
      this.submit();
    }
  };

  private validate() {
    let valid = EmailValidator.validate(this.state.value);
    const {t} = this.props;

    if (!valid) {
      this.setState({error: t('Please enter valid email.')});
    } else {
      this.setState({error: ''});
    }
  }

  private isValid(): boolean {
    return EmailValidator.validate(this.state.value);
  }

  public isSubmitEnabled(): boolean {
    return this.isValid();
  }

  private submit = () => {
  };

  render = () => {
    const { t } = this.props;
    const { value, dirty, error } = this.state;

    return (<div className="my-3">
      <TextField
        className="mb-3"
        data-test="email-change"
        InputProps={{ autoComplete: 'email' } as FilledInputProps}
        name={'email'}
        label={t('Email')}
        variant="outlined"
        value={value}
        fullWidth
        onKeyPress={this.onKeyPress}
        error={dirty && (error !== '')}
        helperText={dirty && (error || ' ')}
        onChange={e => this.handleChange('value', e.target.value)}
      />
      <Button
        fullWidth
        variant="contained"
        color="primary"
        size="large"
        disabled={!this.isSubmitEnabled()}
        data-test="submit"
        onClick={this.submit()}
      >
        {t('Change')}
      </Button>
    </div>);
  };
}

const EmailChangeFormTranslated = translate()(EmailChangeForm);

export class EmailChangeFormPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      return ReactDOM.createPortal(<EmailChangeFormTranslated />, element);
    }

    return null;
  }
}
