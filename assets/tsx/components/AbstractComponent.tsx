import React from 'react';

export default class AbstractComponent<P, S> extends React.Component<P, S> {

  setStateValue = (name: string, value: string): void => {
    const state = this.state;
    state[name] = value;

    this.setState(state);
  };
}
