import React from 'react';
import { AppBar, Button, IconButton, Toolbar, Typography } from '@material-ui/core';
import { translate } from 'react-polyglot';
import { withStyles } from '@material-ui/core/styles';

const useStyles: any = theme => ({});

class NavBar extends React.Component<any, any> {
  render() {
    // eslint-disable-next-line react/jsx-no-undef
    return (
      <AppBar position="static">
        <Toolbar>
          <IconButton aria-label="menu" color="inherit" edge="start">
            {/*<AccessAlarmIcon />*/}
          </IconButton>
          <Typography variant="h6">
            News
          </Typography>
          <Button color="inherit">Login</Button>
        </Toolbar>
      </AppBar>
    );
  }
}

export default translate()(withStyles(useStyles)(NavBar));
