import React from 'react';
import {
  AppBar,
  Button,
  Divider,
  IconButton,
  List,
  ListItem,
  ListItemText,
  Menu,
  MenuItem,
  SwipeableDrawer,
  Toolbar,
  Typography,
} from '@material-ui/core';
import { translate } from 'react-polyglot';
import { withStyles } from '@material-ui/core/styles';
import { AccountCircled, MenuIcon } from '../icons';
import { store } from '../../services/Store';
import { If } from 'react-if';

const useStyles: any = theme => ({
  adminMenu: {
    minWidth: 250,
  },
});

const ToolBarButton = withStyles(theme => ({
  root: {
    color: '#fff',
  },
}))(Button);


class NavBar extends React.Component<any, any> {

  public readonly state = {
    userMenuAnchor: null,
    adminMenuAnchor: null,
  };

  public handleClick = event => {
    this.setState({ userMenuAnchor: event.currentTarget });
  };

  public handleClose = () => {
    this.setState({ userMenuAnchor: null });
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

  public toggleAdminBar = open => (event) => {
    if (event.type === 'keydown' && (event.key === 'Tab' || event.key === 'Shift')) {
      return;
    }

    this.setState({ adminMenuAnchor: open });
  };

  public isAdmin(): boolean {
    const user = store.me();

    if (user === null || !user.hasOwnProperty('roles')) {
      return false;
    }

    return user.roles.indexOf('ROLE_ADMIN') > -1;
  }

  render() {
    const user = store.me();
    const win: any = window;
    const { classes, t } = this.props;

    const adminMenu = (
      <div>
        <Divider />
        <List className={classes.adminMenu}>
          <ListItem button>
            {/*<ListItemIcon>{index % 2 === 0 ? <InboxIcon /> : <MailIcon />}</ListItemIcon>*/}
            <ListItemText primary={'User management'} onClick={this.onUsers} />
          </ListItem>
          <ListItem button>
            <ListItemText primary={'Mailbox'} onClick={() => {
              window.location.replace('/admin/mailbox');
            }} />
          </ListItem>
          <ListItem button>
            <ListItemText primary={'Settings'} onClick={() => {
              window.location.replace('/admin/settings');
            }} />
          </ListItem>
        </List>
      </div>
    );

    // eslint-disable-next-line react/jsx-no-undef
    return (
      <AppBar position="static" elevation={0}>
        <Toolbar>

          <If condition={this.isAdmin()}>
            <IconButton color="inherit" onClick={this.toggleAdminBar(true)}>
              <MenuIcon />
            </IconButton>
          </If>

          <Typography variant="h6">{win.App['appName']}</Typography>
          <div className={'flex-grow-1'}>
            {/*<ToolBarButton onClick={this.onUsers}>Users</ToolBarButton>*/}
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
          id="user-menu"
          anchorEl={this.state.userMenuAnchor}
          keepMounted
          open={Boolean(this.state.userMenuAnchor)}
          onClose={this.handleClose}
        >
          <If condition={user !== null}>
            <MenuItem onClick={() => {
              window.location.replace('/user/profile');
            }}>{t('Profile')}</MenuItem>
          </If>

          <If condition={user !== null}>
            <MenuItem onClick={() => {
              window.location.replace('/user/account');
            }}>{t('Account')}</MenuItem>
          </If>
          <Divider/>
          <If condition={user !== null}>
            <MenuItem className="logout" onClick={this.onLogout}>{t('Logout')}</MenuItem>
          </If>
        </Menu>

        <SwipeableDrawer
          disableBackdropTransition
          anchor={'left'}
          open={Boolean(this.state.adminMenuAnchor)}
          onClose={this.toggleAdminBar(false)}
          onOpen={this.toggleAdminBar(true)}
        >
          {adminMenu}
        </SwipeableDrawer>
      </AppBar>
    );
  }
}

export default translate()(withStyles(useStyles)(NavBar));
