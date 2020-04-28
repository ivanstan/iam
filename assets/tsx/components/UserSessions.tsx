import React from 'react';
import { UserModel } from '../model/UserModel';
import Each from 'react-each';
import { translate } from 'react-polyglot';
import { IconButton } from '@material-ui/core';
import { DeleteIcon } from './icons';
import ConfirmationDialog from './ConfirmationDialog';
import UserService from '../services/UserService';

interface UserSessionsPropsInterface {
  user: UserModel;
}

class UserSessions extends React.Component<UserSessionsPropsInterface, any> {

  public readonly state = {
    sessions: [],
    open: false,
    item: null,
  };

  componentDidMount = (): void => {
    this.setState({
      sessions: this.props.user.sessions,
    });
  };

  onDelete = (item) => {
    this.setState({
      item: item,
      open: true,
    });
  };

  onCancel = () => {
    this.setState({
      item: null,
      open: false,
    });
  };

  onConfirm = async () => {
    try {
      await UserService.deleteSession(this.state.item);
    } catch (e) {

    } finally {
      this.onCancel();
      location.reload();
    }
  };

  render = (): React.ReactNode => {
    return (
      <div>
        <ConfirmationDialog
          onCancel={this.onCancel}
          onConfirm={this.onConfirm}
          text={'Do you wish to log out from the selected session?'}
          open={this.state.open} />
        <table className={'table'}>
          <tbody>
          <Each items={this.state.sessions}
                renderItem={(item) =>
                  (<tr>
                    <td>{item.lastAccess}</td>
                    <td>{item.ip}</td>
                    <td>{item.userAgent}</td>
                    <td>
                      <IconButton color="inherit" edge="start" onClick={() => this.onDelete(item)}>
                        <DeleteIcon />
                      </IconButton>
                    </td>
                  </tr>)
                }
          />
          </tbody>
        </table>
      </div>
    );
  };
}

export default translate()(UserSessions);
