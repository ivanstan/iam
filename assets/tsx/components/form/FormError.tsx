import React from 'react';
import { FormHelperText } from '@material-ui/core';

interface FormErrorPropsInterface {
  text: string | null;
}

export default class FormError extends React.Component<FormErrorPropsInterface, any> {
  render(): React.ReactNode {
    const { text } = this.props;

    return (
      <FormHelperText error={true} style={{ marginBottom: 30, marginTop: 15 }}>
        {text || ' '}
      </FormHelperText>
    );
  }
}
