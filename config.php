<?php
//
// +---------------------------------------------------------------------------+
// | Ban Plugin 1.0 for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | config.php                                                                |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002, 2003 by the following authors:                        |
// |                                                                           |
// | Authors: Tom Willett       - twillett@users.sourceforge.net               |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is free software; you can redistribute it and/or             |
// | modify it under the terms of the GNU General Public License               |
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the             |
// | GNU General Public License for more details.                              |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+
//

global $_DB_table_prefix, $_TABLES, $_PLUGINS;


// Current IP Ban Status used by $_BAN_IP_STATUS global variable - Must be in this order
define("CONST_BAN_IP_STATUS_NOT_FLAGGED", 0); // not checked or only partially checked and still okay
define("CONST_BAN_IP_STATUS_BANNED", 1);
define("CONST_BAN_IP_STATUS_PASSED", 2); // So passed IP must have a status greater than this 
define("CONST_BAN_IP_STATUS_WHITE", 3); 

// Ban Database Record Status - Must be in this order
define("CONST_BAN_STATUS_WHITE", 0); 
define("CONST_BAN_STATUS_NORMAL", 1);
define("CONST_BAN_STATUS_TTL_SHORT", 2);
define("CONST_BAN_STATUS_TTL_MEDIUM", 3);
define("CONST_BAN_STATUS_TTL_LONG", 4);

$_BAN_CONF = [];

// GUS stats table. We set this since if ban plugin loaded before gus we will not know table name
// If ban plugin loaded after gus then gus will record banned ips stats
$_BAN_CONF['gus_userstats_table'] = "";
if (in_array("gus", $_PLUGINS)) {
	if (isset($_TABLES['gus_userstats'])) {
		$_BAN_CONF['gus_userstats_table'] = $_TABLES['gus_userstats'];
	} else {
		$gus_userstats_table = $_BAN_CONF['gus_userstats_table'];
		$_BAN_CONF['gus_userstats_table']  = $_DB_table_prefix . 'gus_userstats';	
	}
}

// Set this flag to true to enable logging of banned attempted accesses
$_BAN_CONF['logging'] = true; // master switch for all logging
$_BAN_CONF['logging_db'] = false; // log banned access based on data from database
$_BAN_CONF['logging_sfs'] = false; // log banned access based on stop forum spam database
$_BAN_CONF['logging_auto'] = true; // log new auto banned ips that match rules
$_BAN_CONF['logging_error'] = true; // log error limit banned ips that match limits

// Set this flag to true to enable emailing of newly added banned ips to system admin
$_BAN_CONF['email'] = true; // master switch for all email
$_BAN_CONF['email_auto'] = true; // email new auto banned ips that match rules
$_BAN_CONF['email_error'] = true; // email error limit banned ips that match limits

// Who Is URL which includes a %s so an IP can be added to the url
$_BAN_CONF['whois_url'] = 'https://whois.domaintools.com/%s';

// Set this variable to the filename to show banned users/bots or set to '' to show a blank page.
// For example if you moved the 404.html to your sites root directory (the Geeklog public_html directory) then it would look like:
// $_BAN_CONF['page'] = '/404.html';
$_BAN_CONF['page'] = '';

// 0 = Disable Time to Live for ban record. Number of MINUTES in a month (1440 = 1 day, 10080 = 1 week, 43829 = 1 month, 525949 = 1 year)
$_BAN_CONF['ban_ttl_check'] = 1440; // For when to do the next 3 checks. This number should be equal or smaller than ttl_short
$_BAN_CONF['ttl_short'] = 1440; // Day
$_BAN_CONF['ttl_meduium'] = 10080; // Week
$_BAN_CONF['ttl_long'] = 43829; // Month

// Default Status to select
$_BAN_CONF['default_status'] = CONST_BAN_STATUS_NORMAL;

// Enable Ban Status storage with Session Variable
// This means individual and ranges (regex) along with SFS database checks are only done once on first visit and result is saved with session. This is not used with autoban since that is behavior of visitor over time.
// Checks are done to see if new database or new records added, if so ban check will be done again.
// This is much faster since check is done on first visit only. Only caveat is that modified or deleted ban records are not detected during same session. So if ban record deleted, current session for this user will not be affected until it times out (or session data is deleted)
$_BAN_CONF['session_status_tracking'] = true;

// Ban IP by using stopforumspam banned ips list
$_BAN_CONF['stopforumspam'] = true;
$_BAN_CONF['stopforumspam_file_date'] = 7; // the number of days before the stop forum spam database file is considered old and will be auto downloaded. Must be 1 or greater. Default is 7
$_BAN_CONF['stopforumspam_auto_download'] = false; // Remember your IP is limited to 3 downloads per day. Auto download based on $_BAN_CONF['stopforumspam_file_date']  value
$_BAN_CONF['stopforumspam_retry_download'] = 28800; // In seconds. Value cannot be lower than 8 hours (28800). 1 Day = 86400 seconds
$_BAN_CONF['stopforumspam_database_zip_name'] = "bannedips.zip";
$_BAN_CONF['stopforumspam_database_name'] = "bannedips.csv";
$_BAN_CONF['stopforumspam_database_location'] = "https://www.stopforumspam.com/downloads/"; // Make sure to include the last backslash in the download location


// Ban IP by other plugins
$_BAN_CONF['plugins_ban_ip_status'] = CONST_BAN_STATUS_TTL_LONG; // (anything except CONST_BAN_STATUS_WHITE is fine)


// Ban IP of user which reached max invalid login attempts (Available only on Geeklog v2.2.0 and higher)
// Note: This only bans the last IP used that attempted the login of a user account which has experienced the max number of invalid login attempts in a certain amount of time (this is set in the Geeklog Configuration)
$_BAN_CONF['max_invalid_logins'] = true;
$_BAN_CONF['max_invalid_logins_status'] = CONST_BAN_STATUS_TTL_MEDIUM; // (anything except CONST_BAN_STATUS_WHITE is fine)


// **********************************************************
// Careful with these below as it could ban bots you want like Googlebot, msnbot, etc... (hint add them to your white list)
// **********************************************************


// Error Limits (since Geeklog 2.2.2)
// Ban visitors who go over limits in x number of seconds
$_BAN_CONF['error_limit'] = true;
$_BAN_CONF['error_limit_status'] = CONST_BAN_STATUS_TTL_SHORT; // (anything except CONST_BAN_STATUS_WHITE is fine)
// Note: The following config options are original stored in Geeklogs lib-plugins.php so these are just over writing them
// Note: See plugins.php file for more info when you are considering changing these settings
// Config Options for the max number of allowed tries within speed limit (from 1 to ...)
$_CONF['speedlimit_max_error-403'] = 3; 
$_CONF['speedlimit_max_error-404'] = 20;
$_CONF['speedlimit_max_error-spam'] = 5;
$_CONF['speedlimit_max_error-speedlimit'] = 10;
// Config Options for the time window used in COM_clearSpeedlimit (in seconds)
$_CONF['speedlimit_window_error-403'] = 60; 
$_CONF['speedlimit_window_error-404'] = 120;
$_CONF['speedlimit_window_error-spam'] = 270; // Based on anonymous users and all speedlimits (comment, likes, etc.) enabled and set to 45 seconds. 
$_CONF['speedlimit_window_error-speedlimit'] = 540; // Based on 'error-spam' settings


// Turn on Auto Ban
$_BAN_CONF['ban_auto'] = true;
$_BAN_CONF['ban_auto_check'] = 0; // NOT USED YET - Number of seconds to wait to check IP again (IP is stored in DB. If 0 check IP every time.)
// Auto Ban - User Agents -  If IP exceeds x number of different user agents in X number of seconds then Ban. Based on GUS data.
$_BAN_CONF['gus_user_agent'] = true;
$_BAN_CONF['gus_user_agent_num'] = 10;
$_BAN_CONF['gus_user_agent_time'] = 10800; // (3 hours) Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_user_agent_status'] = CONST_BAN_STATUS_TTL_LONG;
// Auto Ban - Hits -  If IP exceeds X number of hits in X number of seconds then Ban. Based on GUS data.
// Careful with this as it could ban bots you want like Googlebot, msnbot, etc... (hint add them to your white list)
$_BAN_CONF['gus_hits'] = true;
$_BAN_CONF['gus_hits_num'] = 500; // 500 hits in an hour is just over 8 hits a minute
$_BAN_CONF['gus_hits_time'] = 3600; // Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_hits_status'] = CONST_BAN_STATUS_TTL_MEDIUM;
// Auto Ban - Referrer -  Ban IP that matches referrer in X number of seconds and for X number of times. Based on GUS data.
$_BAN_CONF['gus_referrer'] = false;
$_BAN_CONF['gus_referrer_match'] = array("");
$_BAN_CONF['gus_referrer_min'] = 20; // Protection against too small of a match. A match string must be longer than this
$_BAN_CONF['gus_referrer_num'] = 5;
$_BAN_CONF['gus_referrer_time'] = 3600; // Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_referrer_status'] = CONST_BAN_STATUS_TTL_MEDIUM;
// Auto Ban - URL - Ban IP that request matching URL hit X times in X number of seconds. Based on GUS data.
$_BAN_CONF['gus_url'] = true;
// Example array value would be "staticpages/index.php?page=staticpage_id"
$_BAN_CONF['gus_urls_exact'] = array("search.php?query=search&type=all&keytype=-1%27&mode=search"); // Url exactly matches this (no need to add domain name)
$_BAN_CONF['gus_urls_like'] = array(""); // Like match on end of query string (no need to add domain name)
$_BAN_CONF['gus_url_num'] = 8;
$_BAN_CONF['gus_url_time'] = 3600; // Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_url_status'] = CONST_BAN_STATUS_TTL_LONG;
