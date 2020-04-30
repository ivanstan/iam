import React from 'react';
import { FormControlLabel, Switch } from '@material-ui/core';
import { inject, observer } from 'mobx-react';

@inject('settings', 'activity')
@observer
export class BooleanSettings extends React.Component<any, any> {

  public async onChange(item: any, value: boolean) {
    const { settings, activity } = this.props;

    settings.set(item, value);

    const activityName = this.getActivityName(item);

    activity.add(activityName);

    await settings.flush();

    activity.remove(activityName);
  }

  private getActivityName(item): string {
    return `update-${item.namespace}-${item.name}-setting`;
  }

  render = (): any => {
    const { item, settings, label, activity } = this.props;

    // ToDo: right margin should exist

    return <FormControlLabel
      control={
        <Switch
          disabled={activity.isPending(this.getActivityName(item))}
          checked={settings.value(item)}
          onChange={(event) => this.onChange(item, event.target.checked)} />
      }
      labelPlacement="start"
      className="justify-content-between"
      label={label}
    />;
  };
}
