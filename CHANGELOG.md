# Change log

## 3.1.0 - 2017 03 01

Big update with a lot of improvements and fixes

- Database upgrade and migration tool implemented, used in improved installation and upgrade operations.
- Form classes moved to separate directory, broken into individual files and documented.
- Administration application re-implemented using slim framework.
- Fast double click no longer kills session on slow sites.
- User with ip checking can now have multiple addresses.
- HTML option for system emails.
- Stub and option for two factor authentication.
- Added testing framework and unit tests for critical global functions.

## 3.0.9 - 2016 11 23

Password now allows all ASCII7 printable characters, changed default login and session cleanup to shorter values

## 3.0.8 - 2016 08 04

Backtrace on error now optional

## 3.0.7 - 2016 04 18

Improved path generation

## 3.0.6 - 2016 04 08

Split up main protection routines into smaller and more comprehsensible routines

## 3.0.5 - 2016 03 14

Fixed user admin create user bug

## 3.0.4 - 2016 03 09

Improved SSL configuration, cleaned up admin user cration routines

## 3.0.3 - 2016 02 04

Improved file upload class, ProtectConfig now in its own file

## 3.0.2 - 2016 02 02

Added mbstring and mcrypt dependencies, improved form element access, improving config and db access process

## 3.0.1 - 2016 01 26

Some more bugs flushed

## 3.0.0 - 2016 01 26

Complete revamp of file names, class names, directory structure and install methods to comply with PSR-1 and PSR-4

Many fixes to do with php7 compatibility, improved configuration, genral usability
