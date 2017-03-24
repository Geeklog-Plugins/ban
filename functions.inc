<?php
//
// +---------------------------------------------------------------------------+
// | Ban Plugin 1.0 for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | functions.inc                                                             |
// | This file does two things: 1) it implements the necessary Geeklog Plugin  |
// | API method and 2) implements all the common code needed by the ban        |
// | PHP files.                                                                |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2002 by the following authors:                              |
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


if (strpos(strtolower($_SERVER['PHP_SELF']), 'functions.inc') !== false) {
    die('This file can not be used on its own.');
}

$plugin_path = $_CONF['path'] . 'plugins/ban/';


/**
* Check and see if we need to load the plugin configuration
*/
if (!isset($_BAN_CONF['log'])) {
    require_once $_CONF['path_system'] . 'classes/config.class.php';

    $ban_config = config::get_instance();
    $_BAN_CONF = $ban_config->get_config('ban');
}
require_once ($_CONF['path'] . 'plugins/ban/config.php');

/**
* Language file include
*/
$langfile = $plugin_path . 'language/' . $_CONF['language'] . '.php';
if (file_exists($langfile)) {
    require_once $langfile;
} else {
    require_once $plugin_path . 'language/english.php';
}


/**
 * Ban plugin table(s)
 */
$_TABLES['ban']  = $_DB_table_prefix . 'ban';	

// Validate config options
if ($_BAN_CONF['stopforumspam_file_date'] < 1) {
    $_BAN_CONF['stopforumspam_file_date'] = 7;
}
if ($_BAN_CONF['stopforumspam_retry_download'] < 28800) {
    $_BAN_CONF['stopforumspam_file_date'] = 28800;
}

// Make sure files match db install before we do ban check
$installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                "pi_name = 'ban'");
$code_version = plugin_chkVersion_ban();
if ($installed_version == $code_version) {
    // Check if need to download Stop Forum Spam database
    if ($_BAN_CONF['stopforumspam'] AND $_BAN_CONF['stopforumspam_auto_download']) {
            $db_location = $_CONF['path'] . 'plugins/ban/files/' . $_BAN_CONF['stopforumspam_database_name'];  
            if (file_exists($db_location)) {
                $stats = stat($db_location);
                if ($stats[9] > (time() - (86400 * $_BAN_CONF['stopforumspam_file_date']))) {
                    // db file is less than stop forum spam old date
                } else {
                    // Database to old so download
                    BAN_sfs_download();
                }
            }
    }    
    
    // Perform Ban check
    BAN_check();
}

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+

/**
* Return the current version of code.
* Used in the Plugin Editor to show the registered version and code version
*/
function plugin_chkVersion_ban()
{
    global $_CONF;

    require_once $_CONF['path'] . 'plugins/ban/autoinstall.php';          

    $inst_parms = plugin_autoinstall_ban('ban'); 

    return $inst_parms['info']['pi_version'];
}

/**
* Update the Ban plugin
*
* @return   int     Number of message to display (true = generic success msg)
*
*/
function plugin_upgrade_ban()
{
    global $_CONF, $_TABLES, $_DB_dbms;

    $installed_version = DB_getItem($_TABLES['plugins'], 'pi_version',
                                    "pi_name = 'ban'");
    $code_version = plugin_chkVersion_ban();
    if ($installed_version == $code_version) {
        // nothing to do
        return true;
    }

    require_once $_CONF['path'] . 'plugins/ban/autoinstall.php';

    if (! plugin_compatible_with_this_version_ban('ban')) {
        return 3002;
    }

    $inst_parms = plugin_autoinstall_ban('ban');
    $pi_gl_version = $inst_parms['info']['pi_gl_version'];

    require_once $_CONF['path'] . 'plugins/ban/sql/'
                                . $_DB_dbms . '_updates.php';
                                
    //require_once $_CONF['path'] . 'plugins/ban/install_updates.php';                                

    $current_version = $installed_version;
    $done = false;
    while (! $done) {
        switch ($current_version) {
        case '1.0':
        case '1.0.1':
        case '1.0.2':            
            $current_version = '1.0.3';
            break;

        case '1.0.3':
            if (isset($_UPDATES[$current_version])) {
                $_SQL = $_UPDATES[$current_version];
                foreach ($_SQL as $sql) {
                    DB_query($sql);
                }
            }
            
            //ban_update_ConfValues_1_0_3();
            //ban_update_ConfigSecurity_1_0_3();
            
            $current_version = '2.0.0';
            break;

        default:
            $done = true;
        }
    }

    DB_query("UPDATE {$_TABLES['plugins']} SET pi_version = '$code_version', pi_gl_version = '$pi_gl_version' WHERE pi_name = 'ban'");

    return true;
}

/**
* Automatic uninstall function for plugins
*
* @return   array
*
* This code is automatically uninstalling the plugin.
* It passes an array to the core code function that removes
* tables, groups, features and php blocks from the tables.
* Additionally, this code can perform special actions that cannot be
* foreseen by the core code (interactions with other plugins for example)
*
*/
function plugin_autouninstall_ban ()
{
    $out = array (
        /* give the name of the tables, without $_TABLES[] */
        'tables' => array('ban'),
        /* give the full name of the group, as in the db */
        'groups' => array('Ban Admin'),
        /* give the full name of the feature, as in the db */
        'features' => array('ban.admin'), 
//                            'config.autotags.tab_autotag_permissions'),            
        /* give all vars with their name */
        'vars'=> array('ban_last_ttl_check', 'ban_last_sfsdownload')
    );
    return $out;
}

/**
* Returns the items for this plugin that should appear on the main menu
*
* NOTE: this MUST return the url/value pairs in the following format
* $<arrayname>[<label>] = <url>
*
*/
function plugin_getmenuitems_ban()
{
    $menuitems = array();
    return $menuitems;
}

/**
* Geeklog is checking to see if this plugin supports comments, tell it no!
* NOTE: to support comments you must use the same date/time based ID for your
* widget.  In other words, to make primary keys for your plugin you should call
* makesid().  Comments are keyed off of that...it is a limitation on how geeklog
* does comments.
*
*/
function plugin_commentsupport_ban() 
{
    return false;
}


/**
* This will put an option for ban in the command and control block on
* moderation.php
*
*/
function plugin_cclabel_ban() 
{
    global $LANG_BAN00, $_CONF;

    $retval = array();
    if ((SEC_inGroup('Root')) || (SEC_hasRights('ban.admin'))) {
        return array($LANG_BAN00['ban'],
                $_CONF['site_admin_url'] . '/plugins/ban/index.php',
                plugin_geticon_ban(), 'tools');
    }
    
    return false;
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_ban() 
{
    global $LANG_BAN00, $_CONF, $_TABLES;

    if ((SEC_inGroup('Root')) || (SEC_hasRights('ban.admin'))) {
        $result = DB_query ("SELECT count(*) AS cnt FROM {$_TABLES['ban']}");
        $A = DB_fetchArray ($result);
        $total_bans = $A['cnt'];
        
        return array($LANG_BAN00['ban'],$_CONF['site_admin_url'] . '/plugins/ban/index.php',$total_bans, 'tools');
    }
}

/**
* returns the user option for this plugin
*
*/
function plugin_getuseroption_ban() 
{
    return array();
}


//////////////////////////////////////////////////////////////////////////////////
// BAN API for other Plugins                                                    //
//////////////////////////////////////////////////////////////////////////////////

function BAN_for_plugins_check_access()
{
	$retval = false;
	
	if (SEC_hasRights ('ban.admin')) {
		$retval = true;
	}
	
	return $retval;
}

function BAN_for_plugins_ban_found($ip)
{
	global $_TABLES;
	
	$retval = false;
	
	if (BAN_for_plugins_check_access()) {
		if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
			$data = $ip;
			$type = 'REMOTE_ADDR';
			$status = CONST_BAN_STATUS_NORMAL;		
			if (DB_count($_TABLES['ban'], array('data', 'bantype', 'status'), array($data, $type, $status)) > 0) {
				$retval = true;
			}
		}
	}
	
	return $retval;
}

function BAN_for_plugins_ban_ip($ip, $plugin, $ban = true, $note = '')
{
    global $_PLUGINS, $LANG_BAN00, $_TABLES;

    $retval = false;
    
    if (BAN_for_plugins_check_access()) {
		if (in_array($plugin, $_PLUGINS)) {
			if (filter_var($ip, FILTER_VALIDATE_IP) !== false) {
				$data = $ip;
				$type = 'REMOTE_ADDR';
				$status = CONST_BAN_STATUS_NORMAL;
				
				if ($ban) {
					$note = sprintf($LANG_BAN00['ban_plugin_note'], $plugin) . $note;
					// Clean up the text
					$note = strip_tags($note);
					$note = addslashes($note);
					
					//DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('$type', '$data', $status, '$note')",1);
                    BAN_insertREMOT_ADDR($data, $status, $note);
					
					$retval = true;
				} else {
					
					if (DB_count($_TABLES['ban'], array('data', 'bantype', 'status'), array($data, $type, $status)) > 0) {
						DB_query( "DELETE FROM {$_TABLES['ban']} WHERE bantype='$type' AND data='$data' AND status = $status LIMIT 1", 1 );
						
						$retval = true;
					}
				}
			}
		}
	}
    
    return $retval;
	
}

//////////////////////////////////////////////////////////////////////////////////
// BAN Functions                                                                //
//////////////////////////////////////////////////////////////////////////////////


/*
* Check for banned websites
*/

function BAN_check()
{
    global $_TABLES, $_BAN_CONF, $_PLUGINS, $_TABLES, $LANG_BAN00, $_CONF;
    
    $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; 
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
    $HTTP_REFERER = ''; // May not always be set if no real referrer. Depends on web server 
    if (isset($_SERVER['HTTP_REFERER'])) {
        $HTTP_REFERER = $_SERVER['HTTP_REFERER'];
    }
    $SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
    
    $current_date = date("Y-m-d H:i:s");
    $dateinsec = strtotime($current_date);    
    
    $ans = 0; // flag for if anything is tripped (0 = not banned, 1 = banned, 2 = white)

    // Check our own Ban table
    $result = DB_Query("SELECT bantype, data, status FROM {$_TABLES['ban']} ORDER BY status ASC",1);
    $nrows = DB_numRows( $result );
    
    for ($i = 0; $i < $nrows; $i++) {
        $A = DB_fetchArray($result);
        
        $ip = $A['bantype']; // info like ip,domain to check
        $val = $A['data']; // What we are looking for to ban
        $status = $A['status']; // Ban Status
        $matches = false;

        // Allow for Remote Addresses CIDR and ranges along with preg_match (Borrowed from Spam-X)
        if ($A['bantype'] == 'REMOTE_ADDR') {
            if (strpos($val, '/') !== false) {
                $matches = BAN_matchCIDR($ip, $val);
            } elseif (strpos($val, '-') !== false) {
                $matches = BAN_matchRange($ip, $val); 
            } else {
                //04/10/13 06:07:56 - Access Banned because 46.118.123.107 matches 46.118.123.10 
                //$matches = (preg_match("#$val#i", $ip) == 0 ? false : true);
                //$matches = (($val == $ip) == 0 ? false : true);
                $matches = (preg_match("#^$val$#i", $ip) == 0 ? false : true);
                //$matches = (preg_match("#^$val#i", $ip) == 0 ? false : true);
            }
        } else {
            $matches = (preg_match("#$val#i", $ip) == 0 ? false : true);
        }
        
        if ($matches) {
            // quit on first positive match
            if ($status == CONST_BAN_STATUS_WHITE) {
                $ans = 2; // White Status
                
                break;
            } else {
                $ans = 1; 
                
                if ($_BAN_CONF['logging_db']) {
                    BAN_Log('Access Banned because ' . $A['bantype'] . " matches {$A['data']}");
                }
                
                break;
            }
        }
    }
    

    // Check stop forum spam banned ips list as long as $ans not tripped (white list or other wise)        
    if ($_BAN_CONF['stopforumspam'] AND $ans == 0) {
        $db_location = $_CONF['path'] . 'plugins/ban/files/bannedips.csv';
        $banned_ip_count = ipcheck_stopforumspam($REMOTE_ADDR, $db_location);
        if ($banned_ip_count >= 1){
            $ans = 1; 
            
            // We know ip not on white list since it did not trip above
            if ($_BAN_CONF['logging_sfs']) {
                BAN_Log("Access Banned because $REMOTE_ADDR matches Stop Forum Spam database");
            }
        }
    }    

    // Auto Ban
    if ($_BAN_CONF['ban_auto'] AND $ans == 0) { // Don't need to autoban if already banned or on whitelist

        if (in_array("gus", $_PLUGINS)) {
            // Find GUS stats table. We set this since if ban plugin loaded before gus we will not know table name
            // If ban plugin loaded after gus then gus will record banned ips stats
            if (isset($_TABLES['gus_userstats'])) {
                $gus_userstats_table = $_TABLES['gus_userstats'];
            } else {
                $gus_userstats_table = $_BAN_CONF['gus_userstats_table'];
            }

            // Check: Referrer in a given time by IP. If they match the text then ban
            if ($_BAN_CONF['gus_referrer'] AND $ans == 0) {
                $new_date = $dateinsec - $_BAN_CONF['gus_referrer_time'];
                $new_date = date('Y-m-d H:i:s', $new_date);
                
                foreach ($_BAN_CONF['gus_referrer_match'] as $match) {
                    if (strlen($match) > $_BAN_CONF['gus_referrer_min']) {
                        $comparison = ' LIKE \'' . BAN_getEncodedURL($match) . '%\' ';
                        
                        $sql = "SELECT ip 
                            FROM $gus_userstats_table  
                            WHERE ip = '{$REMOTE_ADDR}'
                            AND referer $comparison
                            AND  DATE_ADD(`date`, INTERVAL `time` HOUR_SECOND) >= '$new_date'";

                        $result = DB_Query($sql);
                        $nrows = DB_numRows($result);
        
                        if ($_BAN_CONF['gus_referrer_num'] <= $nrows) {
                            $ans = 1;
                            // IP has to many Referrers in a given time so ban
                            // DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('REMOTE_ADDR', '{$REMOTE_ADDR}', {$_BAN_CONF['gus_referrer_status']}, '{$LANG_BAN00['gus_referrer_note']}')",1);
                            BAN_insertREMOT_ADDR($REMOTE_ADDR, $_BAN_CONF['gus_referrer_status'], $LANG_BAN00['gus_referrer_note']);

                            if ($_BAN_CONF['logging_auto']) {
                                // BAN_Log("Access Banned because $REMOTE_ADDR has to many referrers in a given time. Added to Ban database.");
                                BAN_Log("Access Banned because $REMOTE_ADDR has $nrows referrers that match $match in the last {$_BAN_CONF['gus_referrer_time']} seconds. Added to Ban database.");
                            }                            
                            
                            break;
                        }
                    }
                }
                
            }

            // Check: page and query string in a given time by IP. If they match the text then ban
            if ($_BAN_CONF['gus_url'] AND $ans == 0) {
                $new_date = $dateinsec - $_BAN_CONF['gus_url_time'];
                $new_date = date('Y-m-d H:i:s', $new_date);
                
                // Loop through both arrays
                for ($counter = 1; $counter <= 2; $counter++) {
                    if ($counter == 1) {
                        $url_array = $_BAN_CONF['gus_urls_exact'];
                    } else {
                        $url_array = $_BAN_CONF['gus_urls_like'];
                    }
                    
                    foreach ($url_array as $url) {
                        if (!empty($url)) {
                            if ($counter == 1) {
                                $comparison = " = '" . urlencode(addslashes($url)) . "'";
                            } else {
                                $comparison = ' LIKE \'' . BAN_getEncodedURL($url) . '%\' ';
                            }
                            
                            $sql = "SELECT ip 
                                FROM $gus_userstats_table  
                                WHERE ip = '{$REMOTE_ADDR}'
                                AND CONCAT(page, CONCAT('%3F', query_string)) $comparison 
                                AND page <> '' 
                                AND  DATE_ADD(`date`, INTERVAL `time` HOUR_SECOND) >= '$new_date'";

                            $result = DB_Query($sql);
                            $nrows = DB_numRows($result);
            
                            if ($_BAN_CONF['gus_url_num'] <= $nrows) {
                                $ans = 1;
                                // IP has to many Referrers in a given time so ban
                                // DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('REMOTE_ADDR', '{$REMOTE_ADDR}', {$_BAN_CONF['gus_url_status']}, '{$LANG_BAN00['gus_url_note']}')",1);
                                BAN_insertREMOT_ADDR($REMOTE_ADDR, $_BAN_CONF['gus_url_status'], $LANG_BAN00['gus_url_note']);
        
                                if ($_BAN_CONF['logging_auto']) {
                                    // BAN_Log("Access Banned because $REMOTE_ADDR has requested to many times a matching page with query string in a given time. Added to Ban database.");
                                    BAN_Log("Access Banned because $REMOTE_ADDR has requested $url $nrows times in the last {$_BAN_CONF['gus_url_time']} seconds. Added to Ban database.");
                                }                            
                                
                                $counter = 3; // breack out of the array counter as well
                                break;
                            }
                        }
                    }
                }
            }
            
            
            // Check: Number of user agents in a given time by IP
            if ($_BAN_CONF['gus_user_agent'] AND $ans == 0) {
                $new_date = $dateinsec - $_BAN_CONF['gus_user_agent_time'];
                $new_date = date('Y-m-d H:i:s', $new_date);
    
                $sql = "SELECT ua_id 
                    FROM $gus_userstats_table  
                    WHERE ip = '{$REMOTE_ADDR}' 
                    AND  DATE_ADD(`date`, INTERVAL `time` HOUR_SECOND) >= '$new_date'
                    GROUP BY ua_id";
                
                $result = DB_Query($sql);
                $nrows = DB_numRows($result);

                if ($_BAN_CONF['gus_user_agent_num'] <= $nrows) {
                    $ans = 1;
                    // IP has to many user agents in a given time so ban
                    // DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('REMOTE_ADDR', '{$REMOTE_ADDR}', {$_BAN_CONF['gus_user_agent_status']}, '{$LANG_BAN00['gus_user_agent_note']}')",1);
                    BAN_insertREMOT_ADDR($REMOTE_ADDR, $_BAN_CONF['gus_user_agent_status'], $LANG_BAN00['gus_user_agent_note']);
                    
                    if ($_BAN_CONF['logging_auto']) {
                        // BAN_Log("Access Banned because $REMOTE_ADDR has to many user agents in a given time. Added to Ban database.");
                        BAN_Log("Access Banned because $REMOTE_ADDR has $nrows user agents in the last {$_BAN_CONF['gus_user_agent_time']} seconds. Added to Ban database.");
                    }                            
                    
                }
                
            }
            
            // Check: Number of hits in a certain time period for ip
            if ($_BAN_CONF['gus_hits'] AND $ans == 0) {
                $new_date = $dateinsec - $_BAN_CONF['gus_hits_time'];
                $new_date = date('Y-m-d H:i:s', $new_date);
    
                $sql = "SELECT COUNT(ip) count_ip
                    FROM $gus_userstats_table  
                    WHERE ip = '{$REMOTE_ADDR}' 
                    AND  DATE_ADD(`date`, INTERVAL `time` HOUR_SECOND) >= '$new_date'";
                
                $result = DB_Query($sql);
                $A = DB_fetchArray ($result);

                if ($_BAN_CONF['gus_hits_num'] <= $A['count_ip']) {
                    $ans = 1;
                    // IP has visited to many times in a given time so ban
                    // DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('REMOTE_ADDR', '{$REMOTE_ADDR}', {$_BAN_CONF['gus_hits_status']}, '{$LANG_BAN00['gus_hits_note']}')",1);
                    BAN_insertREMOT_ADDR($REMOTE_ADDR, $_BAN_CONF['gus_hits_status'], $LANG_BAN00['gus_hits_note']);
                    
                    if ($_BAN_CONF['logging_auto']) {
                        // BAN_Log("Access Banned because $REMOTE_ADDR has to many hits in a given time. Added to Ban database.");
                        BAN_Log("Access Banned because $REMOTE_ADDR has {$A['count_ip']} hits in the last {$_BAN_CONF['gus_hits_time']} seconds. Added to Ban database.");
                    }                      
                }
            }
            
        }
        
    }
    
    // TTL
    if ($_BAN_CONF['ban_auto'] AND $ans == 0) {
        // Figure out if we need to do ttl check. Check last time it was run.
        $new_date = $dateinsec - ($_BAN_CONF['ban_ttl_check'] * 60);
        $new_date = date('Y-m-d H:i:s', $new_date);
        
        if (DB_getItem($_TABLES['vars'], 'value', "name='ban_last_ttl_check'") <= $new_date) {
            //Set latest TTL check
            DB_query("UPDATE {$_TABLES['vars']} SET value='{$current_date}' WHERE name='ban_last_ttl_check'");        
        
            // Do 3 TTL checks
            for ($i = 1; $i < 4; $i++) {
                switch($i) {
                    case 1:    
                        $new_date = $dateinsec - ($_BAN_CONF['ttl_short'] * 60);
                        $status = CONST_BAN_STATUS_TTL_SHORT;
                        break;
                    case 2:
                        $new_date = $dateinsec - ($_BAN_CONF['ttl_meduium'] * 60);
                        $status = CONST_BAN_STATUS_TTL_MEDIUM;
                        break;
                    case 3:   
                        $new_date = $dateinsec - ($_BAN_CONF['ttl_long'] * 60);
                        $status = CONST_BAN_STATUS_TTL_LONG;
                        break;
                }
                $new_date = date('Y-m-d H:i:s', $new_date);
                
                $sql = "DELETE FROM {$_TABLES['ban']} WHERE status = $status AND created <= '$new_date'";
                DB_query($sql);
            }            
        }
    }
    
    if ($ans == 1) { // Something has triggered the ban so... (0 = not banned, 1 = banned, 2 = white)   
        if ($_BAN_CONF['page'] != '') {
            echo COM_refresh($_BAN_page);
            die();
        } else {
            die();
        }
    } elseif ($ans == 2) {
        // White listed so exit check
        return;
    }
}

/**
 * Used for function.inc to insert bans from other plugins and auto bans. Ban type must be REMOTE_ADDR
 */
function BAN_insertREMOT_ADDR($data, $status, $note = '')
{
    global $_TABLES;

    // Confirm Ban doesn't exist first for exact data (ip or range, etc.. If ip is in range will not catch match)
    // If white found then will not insert
    $type = 'REMOTE_ADDR';
    if (DB_count($_TABLES['ban'], array('data', 'bantype'), array($data, $type)) == 0) {
        DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('$type', '$data', $status, '$note')",1);
    } else {
        // Duplicate found which should not have happen so report it in log
        COM_errorLog("Ban Plugin tried to insert a ban for type '$type' and data '$data' which already exists (status is '$status' and note is '$note'). This should not have happen.",1);
    }
}

/**
 * Method to match an IP address against a CIDR
 *
 * @param   string  $iptocheck  IP address to check
 * @param   string  $CIDR       IP address range to check against
 * @return  boolean             true if IP falls into the CIDR, else false
 * @access  private
 * @todo    CIDR support for IPv6 addresses
 *
 * Original author: Ian B, taken from
 * @link http://www.php.net/manual/en/function.ip2long.php#71939
 *
 */
function BAN_matchCIDR($iptocheck, $CIDR)
{
    // not for IPv6 addresses
    if (strpos($iptocheck, ':') !== false) {
        return false;
    }

    // get the base and the bits from the ban in the database
    list($base, $bits) = explode('/', $CIDR);

    // now split it up into its classes
    $classes = explode('.', $base);
    $elements = count($classes);
    if ($elements < 4) {
        for ($i = $elements; $i < 4; $i++) {
            $classes[$i] = 0;
        }
    }
    list($a, $b, $c, $d) = $classes;

    // now do some bit shifting/switching to convert to ints
    $i = ($a << 24) + ($b << 16) + ($c << 8) + $d;
    $mask = $bits == 0 ? 0 : (~0 << (32 - $bits));

    // here's our lowest int
    $low = $i & $mask;

    // here's our highest int
    $high = $i | (~$mask & 0xFFFFFFFF);

    // now split the ip we're checking against up into classes
    $ex = explode('.', $iptocheck);

    if (count($ex) == 4) {
        // now convert the ip we're checking against to an int
        $check = ($ex[0] << 24) + ($ex[1] << 16) + ($ex[2] << 8) + $ex[3];

        // if the ip is within the range, including
        // highest/lowest values, then it's witin the CIDR range
        if (($check >= $low) && ($check <= $high)) {
            return true;
        }
    }

    return false;
}

/**
 * Method to match an IP address against an address range
 *
 * @param   string  $ip     IP address to check
 * @param   string  $range  IP address range to check against
 * @return  boolean         true if IP falls into the IP range, else false
 * @access  private
 *
 * Original authors: dh06 and Stephane, taken from
 * @link http://www.php.net/manual/en/function.ip2long.php#70707
 *
 */
function BAN_matchRange($ip, $range)
{
    // not for IPv6 addresses
    if (strpos($ip, ':') !== false) {
        return false;
    }

    $d = strpos($range, '-');
    if ($d !== false) {
       $from = ip2long(trim(substr($range, 0, $d)));
       $to = ip2long(trim(substr($range, $d + 1)));

       $ip = ip2long($ip);
       return (($ip >= $from) && ($ip <= $to));
    }

    return false;
}

/**
* Logs message to ban.log
*
* This will print a message to the Ban log
*
* @logentry        string      $string         Message to write to log
*
*/

function BAN_log( $logentry )
{
    global $_CONF, $LANG01, $_BAN_CONF;
    
    if ($_BAN_CONF['logging']) { // Double check if enabled
        $timestamp = strftime( "%c" );
        $logfile = $_CONF['path_log'] . 'ban.log';
               
        if( !$file = fopen( $logfile, a ))
        {
            COM_errorLog($LANG01[33] . $logfile . ' (' . $timestamp . ')',1);
        }
                   
        fputs( $file, "$timestamp - $logentry \n" );
    }
}

function plugin_geticon_ban ()
{
    global $_CONF, $LANG_BAN00;

    return $_CONF['site_admin_url'] . '/plugins/ban/images/ban.png';
}

function plugin_getListField_ban($fieldname, $fieldvalue, $A, $icon_arr) {
    global $_CONF, $_AUTO_CONF, $LANG_ACCESS, $LANG_BAN00;

    switch($fieldname) {
        case "edit":
            if (SEC_hasRights('ban.admin')) {
                $retval = "<a href=\"{$_CONF['site_admin_url']}/plugins/ban/index.php?mode=edit&amp;id={$A['id']}\">{$icon_arr['edit']}</a>";
            }
            break;
        case "note":
            $retval = '';
            if (trim($A['note']) != '') {
                $retval = COM_getTooltip('', stripslashes($A['note']), '', $LANG_BAN00['note'],'information');
            }
            break;
        case "type":
            $retval = $A['bantype'];
            break;
        case "status":
            switch ($A['status']) {
                case CONST_BAN_STATUS_TTL_SHORT:
                    $retval = $LANG_BAN00['status_ttl_short'];
                    break;
                    
                case CONST_BAN_STATUS_TTL_MEDIUM:
                    $retval = $LANG_BAN00['status_ttl_medium'];
                    break;
                    
                case CONST_BAN_STATUS_TTL_LONG:
                    $retval = $LANG_BAN00['status_ttl_long'];
                    break;
                    
                case CONST_BAN_STATUS_WHITE:
                    $retval = $LANG_BAN00['status_white'];
                    break;
                    
                default:
                    $retval = $LANG_BAN00['status_normal'];
                    
            }
            break;
        default:
            $retval = $fieldvalue;
            break;
    }
    return $retval;
}

// *FUNCTION
// ipcheckdb3($funcIP, $zbdbfile)
// check database $zbdbfile for existance of $funcIP, return count.
// *** IP MUST PRECEEDED AND FOLLOWED BY A COMMA! ***

function ipcheck_stopforumspam($funcIP, $zbdbfile) {
    /// Load Database
    $zbaaa = "";
    if (file_exists($zbdbfile)){$zbaaa = @file_get_contents($zbdbfile);} //0.4.9 HARDENING
    /// Count Matches
    $funcIP = "," . $funcIP . "," ;
    $zbaab = 0 ;
    $zbaab = @substr_count($zbaaa,$funcIP);
    unset($zbaaa);
    /// return count
    return $zbaab;
}

// Based on the gus function GUS_getEncodedURL to convert urls for the database (since using entities)
// Because the referrers are stored encoded, we need our URLs to look similar for SQL comparison
//	- make it look like the referrers by encoding it
//	- escape '%' for the LIKE clause
function BAN_getEncodedURL($url) {
	$encoded_site_url = urlencode(addslashes($url));
	$encoded_site_url = str_replace('%', '\%', $encoded_site_url);
	
	return $encoded_site_url;
}

// BAN function for download stop forum database
function BAN_sfs_download()
{
    global $_TABLES, $_CONF, $_BAN_CONF;
    
    $retval = false;
    
    // Check curl installed
    if (function_exists('curl_version')){
        // Figure out when last download happened
        $sfs_last_download_date = DB_getItem($_TABLES['vars'], 'value', "name='ban_last_sfsdownload'"); // put it here to make the check as fast as possible
        /*
        $current_date = date("Y-m-d H:i:s"); 
        
        $dateinsec = strtotime($current_date);    
        $new_date = $dateinsec - ($_BAN_CONF['stopforumspam_file_date'] * 60 * 60 * 24);
        $new_date = date('Y-m-d H:i:s', $new_date);
        */
        
        $now = DateTime::createFromFormat('U.u', microtime(true));
        $current_date = $now->format("Y-m-d H:i:s.u");        
        
        $now->sub(new DateInterval('P' . $_BAN_CONF['stopforumspam_file_date'] . 'D')); // minus number of days sfs database is allowed to be old
        $new_date = $now->format("Y-m-d H:i:s.u");        
        
        if ($sfs_last_download_date <= $new_date) {
            //Set latest SFS download attempt
            DB_query("UPDATE {$_TABLES['vars']} SET value='{$current_date}' WHERE name='ban_last_sfsdownload'");          
        
            // Download sfs database zip file
            $destination = $_CONF['path'] . 'plugins/ban/files/';
            $source = $_BAN_CONF['stopforumspam_database_location'] . $_BAN_CONF['stopforumspam_database_zip_name'];
            $filename = $_BAN_CONF['stopforumspam_database_zip_name'];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $source);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_SSLVERSION,3); // Use if file location uses ssl
            $data = curl_exec ($ch);
            $error = curl_error($ch); 
            curl_close ($ch);
            
            // Check if error with download via curl
            if ($error === false) { 
                COM_errorLog('Ban plugin could not download the Stop Forum Spam database - Curl error: ' . $error, 1);
            } else {
                if (is_writable($destination . $filename)) {
                    $file = fopen($destination . $filename, "w+");
                    fputs($file, $data);
                    fclose($file);    
                    
                    // Unzip file
                    $zip = new ZipArchive;
                    if ($zip->open($destination . $filename) === TRUE) {
                        $zip->extractTo($destination);
                        $zip->close();
                        
                        $retval = true;
                    } else {
                        COM_errorLog('Could not download or unzip Stop Forum Spam database file.',1);
                    }
                } else {
                    COM_errorLog("Please check file/directory permissions. Could not write to file: $destination$filename",1);
                }
            }
        } else {
            COM_errorLog("Ban plugin did not download SFS database file since the last download at $sfs_last_download_date is less than the next time is is allowed to download (which is $new_date).",1);
        }
    } else {
        COM_errorLog('Ban plugin could not download the Stop Forum Spam database file because the curl PHP extension is missing.',1);
    }   
    
    return $retval;
}

?>