# classroombookings Changelog

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
