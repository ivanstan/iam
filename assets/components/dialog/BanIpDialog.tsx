import React from 'react';
import { translate } from 'react-polyglot';
import {
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
  TextField,
  withTheme,
} from '@material-ui/core';
import { FilledInputProps } from '@material-ui/core/FilledInput';

class BanIpDialog extends React.Component<any, any> {

  public readonly state = {
    value: '',
    open: false,
    error: '',
    dirty: true,
  };

  componentDidMount(): void {
    const buttons = document.querySelectorAll('.ban-dialog-button');

    for (let i = 0; i < buttons.length; ++i) {
      buttons[i].addEventListener('click', this.open, false);
    }
  }

  open = (event) => {
    this.setState({ item: event.target });
    this.setState({ open: true });
  };

  close = () => {
    this.setState({ open: false });
  };

  private onKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter' && this.isSubmitEnabled()) {
      this.submit();
    }
  };

  public isSubmitEnabled(): boolean {
    return this.isValid();
  }

  private isValid(): boolean {
    return this.validateIp(this.state.value);
  }

  private validate() {
    let valid = this.isValid();
    const { t } = this.props;

    if (!valid) {
      this.setState({ error: t('Please enter valid ip address.') });
    } else {
      this.setState({ error: '' });
    }
  }

  submit = () => {
    fetch('/api/ban', {
      method: 'POST',
      body: JSON.stringify({ ip: this.state.value }),
    }).then(() => {
      this.close();
      this.setState({
        value: '',
      });

      location.reload();
    });
  };

  handleChange = (name: string, value: string) => {
    const state = this.state;
    state[name] = value;

    this.setState(state);
    this.validate();
  };

  validateIp = (address: string) => {
    return (/^(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.(25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)$/.test(address));
  };

  render = () => {
    const { open, error, dirty } = this.state;
    const { t, theme } = this.props;

    return <Dialog fullScreen={false} onClose={this.close} open={open} data-test="ban-dialog">
      <DialogTitle>{t('Permanent IP ban')}</DialogTitle>
      <DialogContent>
        <DialogContentText>
          {t('Entered ip will permanently be denied access. Do you with to continue?')}
        </DialogContentText>

        <TextField
          className="mb-3"
          data-test="ip-input"
          InputProps={{ autoComplete: 'email' } as FilledInputProps}
          label={t('IP')}
          variant="outlined"
          value={this.state.value}
          fullWidth
          onKeyPress={this.onKeyPress}
          error={dirty && (error !== '')}
          helperText={dirty && (error || ' ')}
          onChange={e => this.handleChange('value', e.target.value)}
        />

      </DialogContent>
      <DialogActions>
        <Button onClick={this.close} color="primary" data-test="no">
          {t('Cancel')}
        </Button>
        <Button onClick={this.submit} color="primary" autoFocus data-test="yes" disabled={!this.isSubmitEnabled()}>
          {t('Ban')}
        </Button>
      </DialogActions>
    </Dialog>;
  };
}

export default translate()(withTheme(BanIpDialog));
