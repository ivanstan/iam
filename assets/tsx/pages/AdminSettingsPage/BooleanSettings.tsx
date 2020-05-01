import React from 'react';
import { FormControlLabel, Switch } from '@material-ui/core';
import { inject, observer } from 'mobx-react';

@inject('settings', 'activity')
@observer
export class BooleanSettings extends React.Component<any, any> {

  public async onChange(item: any, value: boolean) {
    const { settings, activity } = this.props;

    const activityName = BooleanSettings.getActivityName(item);
    activity.add(activityName);

    settings.set(item, value);
    await settings.flush();

    activity.remove(activityName);
  }

  private static getActivityName(item): string {
    return `update-${item.namespace}-${item.name}-setting`;
  }

  render(): React.ReactNode {
    const { item, settings, label, activity } = this.props;

    // ToDo: right margin should exist

    return <FormControlLabel
      control={
        <Switch
          disabled={activity.isPending(BooleanSettings.getActivityName(item))}
          checked={settings.value(item)}
          onChange={(event) => this.onChange(item, event.target.checked)} />
      }
      labelPlacement="start"
      className="justify-content-between"
      label={label}
    />;
  };
}
