import React from 'react';
import { observer } from 'mobx-react';
import { Alert } from '@material-ui/lab';
import { FlashMessageStore } from '../services/mobx/FlashMessageStore';

@observer
class FlashMessages extends React.Component<any, any> {

  public normalizeType(type: string): any {
    if (type === 'danger') {
      return 'error';
    }

    return type;
  }

  render() {
    const messages = FlashMessageStore.messages;

    return <>
      {Object.keys(messages).map((type) => {
          return messages[type].map((message, index) => {
            return (<Alert key={type + index} severity={this.normalizeType(type)}>{message}</Alert>);
          });
        },
      )}
    </>;
  }
}


export default FlashMessages;
