<?php
/**
 * @package Akismet
 */
/*
Plugin Name: Calendar WP Rest Script
Plugin URI: https://dotend.digital
Description: Plugin de Calendário com Rest API e integração via script cross
Version: 1.0.4
Requires at least: 5.0
Requires PHP: 5.2
Author: Thiago Gaspar e Vinicius Tom (Dotend Digital)
Author URI: https://automattic.com/wordpress-plugins/
License: GPLv2 or later
Text Domain: dotend.digital
*/

require 'class.calendar-admin.php';
require 'class.calendar-wp-rest.php';

new CalendarAdmin();
new CalendarWPRest();