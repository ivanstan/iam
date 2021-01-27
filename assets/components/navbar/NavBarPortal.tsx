import React from 'react';
import ReactDOM from 'react-dom';
import NavBar from './NavBar';

export class NavBarPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      return ReactDOM.createPortal(<NavBar elevation={0}/>, element);
    }

    return null;
  }
}
