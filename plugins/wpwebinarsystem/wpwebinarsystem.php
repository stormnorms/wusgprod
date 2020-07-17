<?php
/*
  Plugin Name: WP WebinarSystem Pro
  Plugin URI: https://www.wpwebinarsystem.com
  Description: WP WebinarSystem allows you to run Live and Automated webinars within your Wordpress website, and customize everything around it.
  Version: 2.14
  Author: WP WebinarSystem
  Author URI: https://www.wpwebinarsystem.com
  License: GPLv2 or later
  Text Domain: _wswebinar
  Domain Path: ./localization/
 */

include 'includes/core-import.php';

$plug_version = '2.14';

new WebinarSysteem(__FILE__, $plug_version);
