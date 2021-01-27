import React from 'react';
import FormControl from '@material-ui/core/FormControl';
import InputLabel from '@material-ui/core/InputLabel';
import OutlinedInput from '@material-ui/core/OutlinedInput';
import InputAdornment from '@material-ui/core/InputAdornment';
import IconButton from '@material-ui/core/IconButton';
import { Visibility, VisibilityOff } from '../icons';
import _ from 'lodash';
import { FormHelperText } from '@material-ui/core';

interface PasswordFieldPropsInterface {
  onChange: Function,
  onKeyPress?: Function,
  value: string | null,
  variant?: 'standard' | 'outlined' | 'filled';
  required?: boolean,
  fullWidth?: boolean,
  label: string,
  className?: string,
  inputRef?: any,
  error?: boolean,
  helperText?: string,
  name: string,
  id?: string,
}

export class PasswordInput extends React.Component<PasswordFieldPropsInterface, any> {

  readonly state: any = {
    value: '',
    show: false,
  };

  componentDidMount = (): void => {
    this.setState({
      value: this.props.value,
    });
  };

  static getDerivedStateFromProps(props, state) {
    if (props.value !== state.value) {
      return {
        value: props.value,
      };
    }

    return null;
  }

  handleClickShowPassword = () => {
    this.setState({ show: !this.state.show });
  };

  onChange = (e: any) => {
    this.setState({ value: e.target.value });

    this.props.onChange(e);
  };

  render() {
    const { value, show } = this.state;
    const { className, variant, label, fullWidth, required, onKeyPress, error } = this.props;

    const inputProps = {
      id: this.props?.id
    };

    if (this.props.hasOwnProperty('data-test')) {
      inputProps['data-test'] = this.props['data-test']
    }

    return (
      <FormControl fullWidth={fullWidth} required={required} variant={variant} className={className}>
        <InputLabel htmlFor="standard-adornment-password" variant={variant}
                    {..._.pick(this.props, ['error'])}
                    style={{ background: '#FFF', padding: '0 3px' }}>
          {label}
        </InputLabel>
        <OutlinedInput
          {..._.pick(this.props, ['inputRef', 'error', 'name'])}
          inputProps={inputProps}
          endAdornment={(
            <InputAdornment position="end">
              <IconButton
                aria-label="toggle password visibility"
                onClick={this.handleClickShowPassword}
                onMouseDown={e => e.preventDefault()}
              >
                {show ? <Visibility /> : <VisibilityOff />}
              </IconButton>
            </InputAdornment>
          )}
          id="standard-adornment-password"
          labelWidth={0}
          onChange={e => this.onChange(e)}
          type={show ? 'text' : 'password'}
          value={value}
        />

        <FormHelperText error={error}>
          {this.props.helperText || ' '}
        </FormHelperText>
      </FormControl>
    );
  }
}
