<?php

class WebinarsysteemDbMigrations {

    private $DBVERSION;
    private $DB_MIGRATE_VERSIONS = array(10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21);
    private $CHARSET_COLLATE;

    /*
     * 
     * Contructor method
     * 
     */

    public function __construct() {
	$this->runMigrations();
    }

    /*
     * 
     * Run database migrations
     * 
     */

    public function runMigrations() {
	$this->setAttributes();
	$curr_db_version = $this->DBVERSION;
	if ($curr_db_version < end($this->DB_MIGRATE_VERSIONS)) {
	    foreach ($this->DB_MIGRATE_VERSIONS as $version) {
		if ($curr_db_version < $version) {
		    $function_to_call = "runDbMigration_$version";
		    if ($this->$function_to_call())
			$curr_db_version = $version;
		}
	    }
	}

	update_option(WSWEB_OPTION_PREFIX . 'db_version', $curr_db_version);
    }

    /*
     * 
     * Call dbDelta of Wordpress
     * 
     */

    private function calldbDelta($sql) {
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta($sql);
	return true;
    }

    /*
     * 
     * Set attributes needed for querying
     * 
     */

    private function setAttributes() {

	// change previously saved double value for the version into an integer.
	$saved_version = get_option(WSWEB_OPTION_PREFIX . 'db_version', 0);
	if ($saved_version == "1.0") {
	    update_option(WSWEB_OPTION_PREFIX . 'db_version', 10);
	}

	global $wpdb;

	$this->DBVERSION = (int) get_option(WSWEB_OPTION_PREFIX . 'db_version', 0);

	$charset_collate = '';
	if (!empty($wpdb->charset)) {
	    $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
	}
	if (!empty($wpdb->collate)) {
	    $charset_collate .= " COLLATE {$wpdb->collate}";
	}

	$this->CHARSET_COLLATE = $charset_collate;
    }

    /*
     * ------------------------------------------------------------------------
     * Migrations
     * ------------------------------------------------------------------------
     */

    /*
     * Migration 10 
     */

    private function runDbMigration_10() { // create first tables
	$sql1 = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "questions (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		name tinytext NOT NULL,
		email text NOT NULL,
		question text NOT NULL,
		webinar_id int(11) NOT NULL,
		PRIMARY KEY  (id)
	) $this->CHARSET_COLLATE;";

	return $this->calldbDelta($sql1);
    }

    /*
     * Migration 11 
     */

    private function runDbMigration_11() {
	$sql1 = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
                secretkey text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
		webinar_id int(11) NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";

	return $this->calldbDelta($sql1);
    }

    private function runDbMigration_12() {
	global $wpdb;
	$loop = new WP_Query(array('post_type' => 'wswebinars'));
	if ($loop->have_posts()) {
	    while ($loop->have_posts()) {
		$loop->the_post();
		$subs = get_post_meta(get_the_ID(), '_wswebinar_subscribers', false);
		foreach ($subs as $sub) {
		    $array = unserialize($sub);
		    $num = $wpdb->insert(
			    WSWEB_DB_TABLE_PREFIX . "subscribers", array(
			'name' => $array['name'],
			'email' => $array['email'],
			'time' => $array['date'],
			'secretkey' => $array['secretkey'],
			'webinar_id' => get_the_ID(),
			'onehourmailsent' => $array['1hourmailsent'] == true ? 1 : 0,
			'onedaymailsent' => $array['1daymailsent'] == true ? 1 : 0,
			'wbstartingmailsent' => $array['wbstartingmailsent'] == true ? 1 : 0,
			'replaymailsent' => $array['replaymailsent'] == true ? 1 : 0,
			    )
		    );
		}
	    }
	}
	return true;
    }

    private function runDbMigration_13() {
	$sql1 = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "notifications (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		attendee_id int(11) UNSIGNED NOT NULL,
        notification_type int(2) NOT NULL,
        sent_at datetime DEFAULT NOW() NOT NULL,
		PRIMARY KEY  (id),
        FOREIGN KEY (attendee_id) REFERENCES " . WSWEB_DB_TABLE_PREFIX . "subscribers(id) ON DELETE CASCADE
	) " . $this->CHARSET_COLLATE . ";";

	return $this->calldbDelta($sql1);
    }

    private function runDbMigration_14() {
	$sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
                secretkey text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
		webinar_id int(11) NOT NULL,
                exact_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                active int(1) UNSIGNED NOT NULL DEFAULT 1,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }

    private function runDbMigration_15() {
	$sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
                secretkey text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
		webinar_id int(11) NOT NULL,
                exact_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                last_seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                active int(1) UNSIGNED NOT NULL DEFAULT 1,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }

    private function runDbMigration_16() {
	$sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
                secretkey text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
		webinar_id int(11) NOT NULL,
                exact_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                last_seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                active int(1) UNSIGNED NOT NULL DEFAULT 1,
                high_five int(1) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }

    private function runDbMigration_17() {
	$sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "chats (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		webinar_id int(11) NOT NULL,
		admin int(1) UNSIGNED NOT NULL DEFAULT 0,
		private int(1) UNSIGNED NOT NULL DEFAULT 0,
                attendee_id int(11) UNSIGNED NOT NULL,
                content text NOT NULL,
                timestamp DATETIME NOT NULL,
                PRIMARY KEY  (id)
            )" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }

    private function runDbMigration_18() {
	$sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
                secretkey text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
		webinar_id int(11) NOT NULL,
                exact_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                last_seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                active int(1) UNSIGNED NOT NULL DEFAULT 1,
                high_five int(1) UNSIGNED NOT NULL DEFAULT 0,
                attended int(1) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }
    
    private function runDbMigration_19(){
        return null;
    }

    private function runDbMigration_20() {
	 $sql = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "subscribers (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		name tinytext NOT NULL,
		email text NOT NULL,
		custom_fields TEXT NULL,
                secretkey text NOT NULL,
                random_key text NOT NULL,
                onehourmailsent int(1) NOT NULL DEFAULT 0,
                onedaymailsent int(1) NOT NULL DEFAULT 0,
                wbstartingmailsent int(1) NOT NULL DEFAULT 0,
                replaymailsent int(1) NOT NULL DEFAULT 0,
                webinar_id int(11) NOT NULL,
                exact_time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                watch_day varchar(3),
                watch_time time,
                time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                last_seen datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
                active int(1) UNSIGNED NOT NULL DEFAULT 1,
                high_five int(1) UNSIGNED NOT NULL DEFAULT 0,
                attended int(1) UNSIGNED NOT NULL DEFAULT 0,
		PRIMARY KEY  (id)
	)" . $this->CHARSET_COLLATE . ";";
	return $this->calldbDelta($sql);
    }
    
    private function runDbMigration_21() {
	$sql1 = "CREATE TABLE " . WSWEB_DB_TABLE_PREFIX . "notifications (
		id int(11) UNSIGNED AUTO_INCREMENT,		
		attendee_id int(11) UNSIGNED NOT NULL,
        notification_type int(2) NOT NULL,
        sent_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
		PRIMARY KEY  (id),
        FOREIGN KEY (attendee_id) REFERENCES " . WSWEB_DB_TABLE_PREFIX . "subscribers(id) ON DELETE CASCADE,
	) " . $this->CHARSET_COLLATE . ";";

	return $this->calldbDelta($sql1);
    }
    
    

}
