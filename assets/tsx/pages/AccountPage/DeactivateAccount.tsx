import React from 'react';
import { translate } from 'react-polyglot';

function DeactivateAccount(props) {
  const { t } = props;

  return (
    <div className="alert alert-warning">
      <p>{t('You can temporally deactivate account')}</p>
      <form method="post" action="/api/account/deactivate">
        <button type="submit" className="btn btn-warning">{t('Deactivate')}</button>
      </form>
    </div>
  );
}

export default translate()(DeactivateAccount);
