import React from 'react';
import { Button, Dialog, DialogActions, DialogContent, DialogContentText, DialogTitle } from '@material-ui/core';
import { translate } from 'react-polyglot';

class ConfirmationDialog extends React.Component<any, any> {

  cancel = () => {

  };

  confirm = () => {

  };

  render(): React.ReactNode {
    const { t, open, text, onCancel, onConfirm } = this.props;

    return (
      <Dialog fullScreen={false} onClose={this.cancel} open={open} data-test="confirmation-dialog">
        <DialogTitle>{t('Confirmation')}</DialogTitle>
        <DialogContent>
          <DialogContentText>
            {text}
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={onCancel} color="primary" data-test="no">
            {t('Cancel')}
          </Button>
          <Button onClick={onConfirm} color="primary" autoFocus data-test="yes">
            {t('Confirm')}
          </Button>
        </DialogActions>
      </Dialog>
    );
  };
}

export default translate()(ConfirmationDialog);
