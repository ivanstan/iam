# Backlog

* Session management
* Password history
* Change email
* Inform user of changed password
* Two factor authentication on new devices
* Inform user when someone tries to login as him
* Write last user access in session table on Kernel shutdown

# Tasks

### Contact form
- Create feature flag in admin settings
- Create page `/contact` that shows form containing:
  email, subject, body, optional name and last name
- Prevent robots from submitting form using CAPTCHA.
- Form should send email to address defined on `/admin/settings`
  page

### Event log
- Create page `/admin/log` that show contents of log from
  `var/log/env.log` directory.
- Log file should be selected depending on environment from
  `.env` file
- Page should list log newest first with ability to search.

### Ban IP permanent
- Create a ban button on `/admin/ban` page that will open
  a form with ability to enter IP. This IP should be banned
  permanently.

### Self account deactivate
- Create feature flag in admin settings
- Create ban field on User and use it instead of active
- Add ability for user to deactivate account on account page
- Use active field for activating/deactivating
- Set account activated when user logs in

### Self account delete
- Create feature flag in admin settings with number of days
- Add ability for user to request account delete from account page
- Create a cron job that will delete user accounts that are requested
  for delete after number of days set in admin settings had passed.
- When user requests account delete, account will also be deactivated.
- If user logs in before number of days had passed, account is activated and
  delete request is canceled.
