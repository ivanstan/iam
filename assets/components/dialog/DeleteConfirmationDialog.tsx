import React from 'react';
import {
  Button,
  Dialog,
  DialogActions,
  DialogContent,
  DialogContentText,
  DialogTitle,
  withTheme,
} from '@material-ui/core';
import { translate } from 'react-polyglot';

class DeleteConfirmationDialog extends React.Component<any, any> {

  public readonly state = {
    item: {},
    open: false,
  };

  componentDidMount(): void {
    const buttons = document.querySelectorAll('.delete-button');

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

  submit = () => {
    const item: any = this.state.item;
    const token = item.getAttribute('data-token');

    fetch(item.getAttribute('data-action'), {
      method: 'DELETE',
      body: '_token=' + token,
      headers: { 'Content-type': 'application/x-www-form-urlencoded' },
    }).then(() => {
      this.close();
      location.reload();
    });
  };

  render = () => {
    const { open } = this.state;
    const { t, theme } = this.props;

    return <Dialog fullScreen={false} onClose={this.close} open={open} data-test="delete-dialog">
      <DialogTitle>{t('Confirm delete?')}</DialogTitle>
      <DialogContent>
        <DialogContentText>
          {t('Selected item will be permanently deleted. Do you with to continue?')}
        </DialogContentText>
      </DialogContent>
      <DialogActions>
        <Button onClick={this.close} color="primary" data-test="no">
          {t('No')}
        </Button>
        <Button onClick={this.submit} color="primary" autoFocus data-test="yes">
          {t('Yes, delete')}
        </Button>
      </DialogActions>
    </Dialog>;
  };
}

export default translate()(withTheme(DeleteConfirmationDialog));
