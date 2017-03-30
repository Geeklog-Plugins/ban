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

// Ban Record Status - Must be in this order
define("CONST_BAN_STATUS_WHITE", 0); 
define("CONST_BAN_STATUS_NORMAL", 1);
define("CONST_BAN_STATUS_TTL_SHORT", 2);
define("CONST_BAN_STATUS_TTL_MEDIUM", 3);
define("CONST_BAN_STATUS_TTL_LONG", 4);

// GUS stats table. We set this since if ban plugin loaded before gus we will not know table name
// If ban plugin loaded after gus then gus will record banned ips stats
$_BAN_CONF['gus_userstats_table']  = $_DB_table_prefix . 'gus_userstats';	

// Set this flag to true to enable logging of banned attempted accesses
$_BAN_CONF['logging'] = true; // master switch for all logging
$_BAN_CONF['logging_db'] = true; // log banned access based on data from database
$_BAN_CONF['logging_sfs'] = true; // log banned access based on stop forum spam database
$_BAN_CONF['logging_auto'] = true; // log new auto banned ips that match rules

// Set this variable to the filename to show banned users/bots or set to '' to show a blank page.
$_BAN_CONF['page'] = '';

// Ban IP by using stopforumspam banned ips list
$_BAN_CONF['stopforumspam'] = false;
$_BAN_CONF['stopforumspam_file_date'] = 7; // the number of days before the stop forum spam database file is considered old. Must be 1 or greater. Default is 7
$_BAN_CONF['stopforumspam_auto_download'] = false; // Remember your IP is limited to 3 downloads per day. Auto download based on $_BAN_CONF['stopforumspam_file_date']  value
$_BAN_CONF['stopforumspam_retry_download'] = 28800; // In seconds. Value cannot be lower than 8 hours (28800). 1 Day = 86400 seconds
$_BAN_CONF['stopforumspam_database_zip_name'] = "bannedips.zip";
$_BAN_CONF['stopforumspam_database_name'] = "bannedips.csv";
$_BAN_CONF['stopforumspam_database_location'] = "https://www.stopforumspam.com/downloads";

// 0 = Disable Time to Live for ban record. Number of MINUTES in a month (1440 = 1 day, 10080 = 1 week, 43829 = 1 month, 525949 = 1 year)
$_BAN_CONF['ban_ttl_check'] = 1440; // For when to do the next 3 checks. This number should be equal or smaller than ttl_short
$_BAN_CONF['ttl_short'] = 1440; // Day
$_BAN_CONF['ttl_meduium'] = 10080; // Week
$_BAN_CONF['ttl_long'] = 43829; // Month

// Default Status to select
$_BAN_CONF['default_status'] = CONST_BAN_STATUS_NORMAL;

// ******* Careful with this as it could ban bots you want like Googlebot, msnbot, etc... (hint add them to your white list)
// Turn on Auto Ban
$_BAN_CONF['ban_auto'] = false;
$_BAN_CONF['ban_auto_check'] = 0; // Number of seconds to wait to check IP again (IP is stored in DB. If 0 check IP everytime.
// Auto Ban - User Agents -  If IP exceeds x number of different user agents in X number of seconds then Ban. Based on GUS data.
$_BAN_CONF['gus_user_agent'] = true;
$_BAN_CONF['gus_user_agent_num'] = 10;
$_BAN_CONF['gus_user_agent_time'] = 10800; // (3 hours) Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_user_agent_status'] = CONST_BAN_STATUS_TTL_LONG;
// Auto Ban - Hits -  If IP exceeds X number of hits in X number of seconds then Ban. Based on GUS data.
// Careful with this as it could ban bots you want like Googlebot, msnbot, etc... (hint add them to your white list)
$_BAN_CONF['gus_hits'] = false;
$_BAN_CONF['gus_hits_num'] = 200;
$_BAN_CONF['gus_hits_time'] = 3600; // Number of SECONDS to check back (3600 = 1 hour, 86400 = 1 day)
$_BAN_CONF['gus_hits_status'] = CONST_BAN_STATUS_TTL_MEDIUM;
// Auto Ban - Referrer -  Ban IP that matches referrer in X number of seconds and for X number of times. Based on GUS data.
$_BAN_CONF['gus_referrer'] = false;
$_BAN_CONF['gus_referrer_match'] = array("");
$_BAN_CONF['gus_referrer_min'] = 20; // Protection agains't too small of a match. A match string must be longer than this
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

?>