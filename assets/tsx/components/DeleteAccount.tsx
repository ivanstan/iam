import React from 'react';
import { translate } from 'react-polyglot';

function DeleteAccount(props) {
  const { t } = props;

  return (
    <div className="alert alert-danger">
      <p>{t('Delete account. This action cannot be undone.')}</p>
      <form method="post" action="/api/account/delete">
        <button type="submit" className="btn btn-danger">{t('Delete')}</button>
      </form>
    </div>
  );
}

export default translate()(DeleteAccount);
