# classroombookings Changelog

## [2.8.0] 2023-09-15

### Added:
- Schedules: Create different schedules for different room groups.

### Updated:
- Room groups is no longer an opt-in experimental feature.


## [2.7.3] 2023-02-10

### Updated
- Room Groups: Create a default group


## [2.7.2] 2023-02-08

### Updated
- Room Groups: Ability to add/remove rooms when adding/editing a group.


## [2.7.1] 2023-02-06

### Fixed
- Error when deleting a room photo.


## [2.7.0] 2023-02-05

### Added
- Room Groups: Create different groups for different rooms and allow custom ordering.
- Settings: Experimental Features: Opt-in to experimental features.

### Changed
- Various internal updates for Cloud platform and Demo mode.


## [2.6.5] 2022-10-31

### Fixed
- Typo in Bookings process.
- Redirection issues in bookings process when running under IIS.
- Several PHP warnings.

### Changed
- Extra checks for https scheme detection.



## [2.6.4] 2022-07-22

### Fixed
- Issue where a user with no department creating multiple single bookings would result in no bookings being created.


## [2.6.3] 2022-06-29

### Fixed
- Issue where Session calendar might not display the last month, depending on start/end date of Session.


## [2.6.2] 2022-03-26

### Fixed
- Bookings Grid display issues in Firefox - cells should now all be the same size.


## [2.6.1] 2022-03-02

### Fixed
- Issue when creating a recurring booking with no user selected.


## [2.6.0] 2022-01-29

### Added
- Sessions feature, to replace Academic Years. Create as many as you need and switch between them.
- Settings: Option to set timezone.
- Users page: Search filter.
- Dashboard: improved interface for teacher users.
- Bookings: Multiple selection mode.
- Bookings: Preview recurring bookings.
- Bookings: View detailed information for bookings.
- Bookings: View reasons when rooms/periods are unavailable.
- Bookings: Edit or Cancel individual instances of recurring bookings.
- Bookings: Ability for users to switch between Sessions.

### Changed
- Bookings section overhauled with many new features.
- Timetable Weeks: these are now configured within each Session.
- Holidays: These are now configured within each Session.
- Timetable Weeks: improved colour picker widget.

### Fixed
- Timetable weeks: now have a default colour, instead of none at all.


## [2.5.0] 2021-04-27

### Added
- Room Access Control feature - set visibility of each room to specific Users or Departments.

### Changed
- Updated sizing for Date Picker widget.
- Added cache-busting query strings to JS/CSS assets.

### Fixed
- Issue with loading override language entries from database.


## [2.4.1] 2020-12-09

### Fixed
- Potential SQL security issue when importing users from CSV file.


## [2.4.0] 2020-03-10

### Added
- Support for setting a custom message to appear on the login page.
- Support for controlling the visibility of booking user details to other users.

### Changed
- Improvements for back/next navigation between days; will now skip days that don't have any periods.


## [2.3.2] 2020-09-25

### Fixed
- Issue saving school details when an error occurs.


## [2.3.1] 2020-09-25

### Fixed
- Issue when saving school details when an error occurs.


## [2.3.0] 2020-08-26

### Added
- Support for LDAP authentication.
- Support for language line overrides in the database.

### Changed
- General javascript tidy-up and library updates.
- New style of room information popup on Bookings pgae.

### Fixed
- Removed erroneous debugging output from Weeks model.


## [2.2.0] 2020-06-06

### Added
- New settings page for additional settings.
- New setting for 'maximum active bookings': specify how many active bookings a user can have at one time.
- Date and Time display formats on Bookings page can now be customised.
- License details.

### Changed
- Updated icons to better quality PNG format.

### Fixed
- Issue where bookings on Sundays were not being displayed.


## [2.1.3] 2020-03-11

### Fixed
- Fixed another situation where existing bookings were being detected incorrectly.


## [2.1.2] 2019-12-03

### Fixed
- Fixed previous fix for database detection during installation which affected post-install.


## [2.1.1] 2019-12-02

### Fixed
- Fixed issue where Install page wouldn't load/would display errors when trying to load database.


## [2.1.0] 2019-11-13

Introducing the new 'Maintenance Mode' feature.

When enabled, Maintenance Mode prevents Teacher user accounts from viewing and making changes to bookings. The message can be customised, and will be displayed at the top of all pages.


### Added
- Added a new section to the School Details settings page to manage Maintenance Mode.

### Fixed
- Fixed an issue with one of the database migrations that might occur when updating from a pervious version.


## [2.0.5] 2019-09-30

Another update to "existing booking" check and minor tweaks.

### Changed
- Updated "Existing Bookings" check to make sure the Weekday was properly included.
- Updated the 'Recurring' section of the 'Make a booking' page to default the weekday value to the weekday of the chosen date.
- Updated 'Add week' page to have a default contrasting background colour.



## [2.0.4] 2019-08-31

Another update to make the "existing booking" check more robust.

### Changed
- Updated "Existing Bookings" check to make sure the Week was properly included.


## [2.0.3] 2019-08-31

Small update to address a Holiday display issue.

### Changed
- Updated Bookings to display Holiday details, when applicable, instead of an existing static or recurring booking.


## [2.0.2] 2019-06-19

One small bugfix.

### Changed
- Fixed an issue with User and Department lists having default limit applied when they shouldn't.


## [2.0.1] - 2019-01-26

Minor fix to day settings for periods and addition of favicon.

### Added
- Favicon to help classroombookings stand out in tabs and windows.

### Changed
- Fixed an issue relating to possible issues with period days being shifted by one if upgraded from v1 to v2.


## [2.0.0] - 2019-01-02

The big one! Major update to support modern PHP, plus others.

### Added
- PHP Requirement for minimum version 5.5, and support for 7.x.
- Use Composer for dependencies.
- Database migrations.
- New installer.
- New upgrader for v1 => v2.

### Changed
- Updated CodeIgniter framework to version 3.
- Updated all class files for compatibility with CodeIgniter 3.
- Updated folder structure and configuration file methods.
- Security updates for HTML escaping.
- Fixed various bugs.
- Bitmask library for period/lesson time days.

### Removed
- Header image colour generation.
