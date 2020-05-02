# Backlog

* [ ] Inform user of changed password
* [ ] Two factor authentication on new devices
* [ ] Inform user when someone tries to login as him
* [ ] Create language selector
* [ ] Admin should not be able to set its account inactive
* [ ] Admin dashboard
  * [ ] Active sessions
* [ ] Make invitation permanent
* [ ] Create react forms for registration and recovery
* [ ] Fix user edit form
* [ ] React settings form
* [ ] Password complexity on settings form
* [ ] Google, Facebook OAuth login
* [ ] Create documentation overview containing all the features
* [ ] Create helper text for certain options
* [ ] Improve texts
* [ ] Start documenting code (events, features)

# Tasks
* [x] Fix and link react admin settings form to menu
* [ ] Fix loading progress and fist load bar
* [ ] Add delete and deactivate account confirmation with password
* [ ] Consider new frontend translation library
* [ ] Move user/profile page to react
* [ ] Move user edit/new form to react
* [ ] Move register form to react
* [ ] Move password recovery form to react
* [ ] Move set a new password after recovery form to react
* [ ] Fix bug in NavBar menu

### Session management
* [x] User can see his active sessions
* [x] User can delete session
* [ ] Admin can see and delete any session
* [ ] Admin can ban ip from session
* [ ] User session user agents should be parsed, use https://github.com/yzalis/UAParser

### User avatar
* [ ] Implement resource entity, can be a local file or remote
* [ ] When user is signed in and has the avatar it is displayed as button of user menu
  in primary nav bar.
* [ ] On page `/user/profile` there is an option to upload image to be displayed as
  avatar.
* [ ] User should be able to edit image (crop, focus, zoom, rotate) in browser
  without uploading.
* [ ] Once user is satisfied with the image he can save it to server and use it
  as avatar image.
* [ ] There should be a file entity and image entity that extends it.
* [ ] Use: https://github.com/nhn/toast-ui.react-image-editor

### Elevated privilege mode ???
* [ ] Before entering page `/user/account`, user should be offered to enter password
  for safety.
* [ ] Flag is set in session with timestamp when password was entered last time,
  during period of n minutes from entering password user can change settings on
  account page (this is refereed to as privileged mode)
* [ ] Entering old password on password change form is now no longer necessary.

### Status report
* [ ] Check Drupal status page
* [ ] Use https://github.com/outcompute/PHPInfo

### Migrate symfony translations to front end translations
* [ ] All translations on a backend should be available to frontend as well.
* [ ] Create a command that will convert symfony yaml translations to json that
  are suitable for use on the frontend.

### User list improvements
* [x] Set default sort on user page
* [ ] Add last access column

### Timezone feature flag
* [ ] Timezone can be optional, add feature flag

### Google analytics feature
* [ ] On settings page there should be a form to enter GA tracking code
* [ ] Code is applied on all pages

### Settings page
* [ ] Test sending email, form with email input and submit to allow sending test
  mails.

### Mailbox improvements
* [ ] Sort by date
* [ ] Search
* [ ] Connect mailbox recipient and sender to user
* [ ] Better mail preview

### Contact form
* [ ] Create feature flag in admin settings
* [ ] Create page `/contact` that shows form containing:
  email, subject, body, optional name and last name
* [ ] Prevent robots from submitting form using CAPTCHA.
* [ ] Form should send email to address defined on `/admin/settings`
  page

### Event log
* [ ] Create page `/admin/log` that show contents of log from
  `var/log/env.log` directory.
* [ ] Log file should be selected depending on environment from
  `.env` file
* [ ] Page should list log newest first with ability to search.
* [ ] Consider https://github.com/ddtraceweb/monolog-parser

### Password history
* [ ] Create feature flag.
* [ ] All previous user password hashes should be stored and checked when user
  enters new password during account recovery.
* [ ] During account recovery user should not be able to enter any of the previous
  passwords.

## Done

### Self account deactivate
* [x] Create feature flag in admin settings
* [x] Create ban field on User and use it instead of active
* [x] Add ability for user to deactivate account on account page
* [x] Use the active field for activating/deactivating
* [x] Set account activated when user logs in

### Self account delete
* [x] Create feature flag in admin settings with number of days
* [x] Add ability for user to request account delete from account page
* [x] Create a cron job that will delete user accounts that are requested
  for delete after number of days set in admin settings had passed.
* [x] When user requests account delete, account will also be deactivated.
* [x] If user logs in before number of days had passed, account is activated and
  delete request is canceled.

### Change email
* [x] Create feature flag.
* [x] Create a feature for user to make a request for changing account's email
* [x] Form should exist on page `/user/account`, with ability to enter mail and send
  request.
* [x] Upon request system shall send two emails one to old email with confirmation
  link, upon clicking link email should be changed.
* [x] Another email should be sent to new email informing that this address is now
  owner of account on [Application name]. If owner agrees with change no action
  should be taken. If owner disagrees (mistake happened), he should be able to
  click link to cancel change request and revert mail change.
* [x] Number of days confirmation mails are active * TBD.
