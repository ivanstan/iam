import React from 'react';
import ReactDOM from 'react-dom';
import NavBar from './NavBar';

export class NavBarPortal extends React.Component<any, any> {
  render() {
    const element = document.getElementById(this.props.id);
    if (element) {
      const form = (<NavBar
        user={element.getAttribute('data-user')}
        />
      );

      return ReactDOM.createPortal(form, element);
    }

    return null;
  }
}
