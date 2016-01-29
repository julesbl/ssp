# Simple Site Protection
Secure login system for php frameworks, applications and sites

These set of php routines are designed to allow php developers to easily secure
a site or an application.

Based on the ideas and information written about in [Innocent Code] (http:///www.amazon.co.uk/Innocent-Code-Security-Wake-up-Programmers/dp/0470857447/ref=sr_1_1?ie=UTF8&amp;s=books&amp;qid=1266594625&amp;sr=1-1) by the security consultant [Sverre H. Huseby] (http://shh.thathost.com/)  the code attempts to make the site resilient against most forms of
attack.

## Installation

composer require w34u/ssp

1. Move vendor/w34u/ssp/cfg to version controlled part of your project, preferably
outside the browser viewable part of your project.
2. Rename vendor/w34u/ssp/cfg/Configuration.change.php to Configuration.php and 
assign values to all the properties to do the database connection and secure your
site.
3. Add
    "autoload": {
        "psr-4": { "w34u\\\\ssp\\\\": "cfg/" }
    }, to composer.json so that the configurations load and then run 
'composer dumpautoload' to refresh the loader.

4. Move vendor/w34u/ssp/cfg/sspadmin to a browser viewable area and ensure 
sspadmin/includeheader.php requires the composer autoloader in vendor.
5. Point your favourite browser at sspadmin/setup and follow the instructions
to create the database and your first admin login.

[Originally hosted on source forge for old versions]
(https://sourceforge.net/projects/ssprotection/)


## System requirements

PHP >= 5.5 and up.

adodb/adodb-php >= 5.0

mbstring

mcrypt

## Attacks hardened against are:

  * Sql injection.
  * Invalid character injection in forms.
  * Javascript injection in forms.
  * Sesson theft.
  * Session takeover.
  * One forms out put being used into another.
  * Designed to be used with ssl thus helping to prevent man in the middle
    type attacks.

## Facilities provided by this set of libraries and routines:

  * Basic joinup routine.
  * Password recovery.
  * User admin.
  * User self admin.
  * Fully templated using fast simple template class.
  * Powerful (and paranoid) form building class.
  * Data checking class.
  * Useful lister and html menu list generation classes
  * Works with php 5.0 upwards
  * Uses database abstraction to work with most databases, has been used with MySql, Access and MS Sql Server.
  * Multi lingual capability with browser language checking.

## Highly configurable session, login and debug:

 * Http or Https.
 * Variable number of actals for ip checking.
 * Fully configurable on types of checks to be done.
 * Login by email or username.
 * Extend the login for other user inputs.
 * Error output either to screen or log file for live sites.


