# Backlog

* Session management
* Inform user of changed password
* Two factor authentication on new devices
* Inform user when someone tries to login as him
* Write last user access in session table on Kernel shutdown
* Add option for user avatar
* Create language selector
* Better mail preview ?
* Test for optional registration
* Admin should not be able to set its account inactive
* Secure /account page with password for 15 minutes
* Migrate symfony translations to front end translations
* Active sessions
* Admin dashboard
* Make invitation permanent

# Tasks

### User list improvements
- Sortable columns
- Search

### Timezone feature flag

### Google analytics feature

### Mailbox improvements
- Search
- Connect mailbox recipient and sender to user

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

### Change email
- Create feature flag.
- Create a feature for user to make a request for changing account's email
- Form should exist on page `/user/account`, with ability to enter mail and send
  request.
- Upon request system shall send two emails one to old email with confirmation
  link, upon clicking link email should be changed.
- Another email should be sent to new email informing that this address is now
  owner of account on [Application name]. If owner agrees with change no action
  should be taken. If owner disagrees (mistake happened), he should be able to
  click link to cancel change request and revert mail change.
- Number of days confirmation mails are active - TBD.

### Password history
- Create feature flag.
- All previous user password hashes should be stored and checked when user
  enters new password during account recovery.
- During account recovery user should not be able to enter any of the previous
  passwords.
