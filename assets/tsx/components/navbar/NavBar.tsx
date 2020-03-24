import React from 'react';
import { AppBar, Button, IconButton, Menu, MenuItem, Toolbar, Typography } from '@material-ui/core';
import { translate } from 'react-polyglot';
import { withStyles } from '@material-ui/core/styles';
import { AccountCircled } from '../icons';
import { store } from '../../services/Store';
import If from 'react-if';

const useStyles: any = theme => ({});

const ToolBarButton = withStyles(theme => ({
  root: {
    color: '#fff',
  },
}))(Button);


class NavBar extends React.Component<any, any> {

  public readonly state = {
    anchor: null,
  };

  public handleClick = event => {
    this.setState({ anchor: event.currentTarget });
  };

  public handleClose = () => {
    this.setState({ anchor: null });
  };

  public onLogout = () => {
    window.location.replace('/logout');

    this.handleClose();
  };

  public onLogin = () => {
    window.location.replace('/login');

    this.handleClose();
  };

  public onUsers = () => {
    window.location.replace('/admin/users');
  };

  render() {
    const user = store.me();

    // eslint-disable-next-line react/jsx-no-undef
    return (
      <AppBar position="static" elevation={0}>
        <Toolbar>
          <Typography variant="h6">{window.App['appName']}</Typography>
          <div className={'flex-grow-1'}>
            <ToolBarButton onClick={this.onUsers}>Users</ToolBarButton>
          </div>

          <If condition={user !== null}>
            <IconButton aria-label="menu" color="inherit" edge="start" onClick={this.handleClick}
                        aria-controls="simple-menu" aria-haspopup="true">
              <AccountCircled />
            </IconButton>
          </If>

          <If condition={user === null}>
            <IconButton aria-label="menu" color="inherit" edge="start" onClick={this.onLogin}
                        aria-controls="simple-menu" aria-haspopup="true">
              <AccountCircled />
            </IconButton>
          </If>
        </Toolbar>

        <Menu
          id="simple-menu"
          anchorEl={this.state.anchor}
          keepMounted
          open={Boolean(this.state.anchor)}
          onClose={this.handleClose}
        >
          <If condition={user !== null}>
            <MenuItem onClick={this.onLogout}>Logout</MenuItem>
          </If>
        </Menu>
      </AppBar>
    );
  }
}

export default translate()(withStyles(useStyles)(NavBar));
