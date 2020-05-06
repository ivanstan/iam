import { If } from 'react-if';
import { LinearProgress } from '@material-ui/core';
import React from 'react';
import { withStyles } from '@material-ui/core/styles';
import { ActivityStore } from '../services/mobx/ActivityStore';
import { observer } from 'mobx-react';

const styles: any = theme => ({
  top: {
    position: 'fixed',
    width: '100%',
    zIndex: 10,
  },
});

@observer
class LoaderTop extends React.Component<any> {
  render() {
    const { classes } = this.props;

    return <If condition={ActivityStore.isPending('')}>
      <LinearProgress color="secondary" className={classes.top} />
    </If>;
  }
}

export default withStyles(styles)(LoaderTop);
