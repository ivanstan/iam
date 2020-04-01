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
  appTitle: {
    '&:hover': {
      color: '#fff',
      textDecoration: 'none',
    },
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

  public onLogin = () => {
    window.location.replace('/login');

    this.handleClose();
  };

  public adminNavigate = (url) => {
    window.location.replace(url);
  };

  public userNavigate = (url) => {
    window.location.replace(url);
    this.handleClose();
  };

  public toggleAdminBar = open => (event) => {
    if (event.type === 'keydown' && (event.key === 'Tab' || event.key === 'Shift')) {
      return;
    }

    this.setState({ adminMenuAnchor: open });
  };

  public isAdmin = (): boolean => {
    const user = store.me();

    if (user === null || !user.hasOwnProperty('roles')) {
      return false;
    }

    return user.roles.indexOf('ROLE_ADMIN') > -1;
  };

  render = () => {
    const user = store.me();
    const win: any = window;
    const { classes, t } = this.props;

    const adminMenu = (
      <div>
        <Divider />
        <List className={classes.adminMenu}>
          <ListItem button>
            {/*<ListItemIcon>{index % 2 === 0 ? <InboxIcon /> : <MailIcon />}</ListItemIcon>*/}
            <ListItemText primary={'User management'} onClick={() => this.adminNavigate('/admin/users')} />
          </ListItem>
          <ListItem button>
            <ListItemText primary={'Mailbox'} onClick={() => this.adminNavigate('/admin/mailbox')} />
          </ListItem>
          <ListItem button>
            <ListItemText primary={'Settings'} onClick={() => this.adminNavigate('/admin/settings')} />
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

          <a href="/" className={classes.appTitle}>
            <Typography variant="h6">{win.App['appName']}</Typography>
          </a>

          <div className={'flex-grow-1'}>
            {/*<ToolBarButton onClick={this.onUsers}>Users</ToolBarButton>*/}
          </div>

          <If condition={user !== null}>
            <div>
              <span className="pr-2">{user.email}</span>
              <IconButton aria-label="menu" color="inherit" edge="start" onClick={this.handleClick}
                          aria-controls="simple-menu" aria-haspopup="true">

                <AccountCircled />
              </IconButton>
            </div>
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
            <MenuItem onClick={() => this.userNavigate('/user/profile')}>{t('Profile')}</MenuItem>
          </If>

          <If condition={user !== null}>
            <MenuItem onClick={() => {
              this.userNavigate('/user/account');
            }}>{t('Account')}</MenuItem>
          </If>

          <Divider />

          <If condition={user !== null}>
            <MenuItem className="logout" onClick={() => this.userNavigate('/logout')}>{t('Logout')}</MenuItem>
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
  };
}

export default translate()(withStyles(useStyles)(NavBar));
