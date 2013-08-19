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
// $Id: 

$langfile = $_CONF['path'] . 'plugins/ban/language/' . $_CONF['language'] . '.php';

if (file_exists ($langfile)) 
{
    include_once ($langfile);
}
else 
{
    require_once ($_CONF['path'] . 'plugins/ban/language/english.php');
}

require_once ($_CONF['path'] . 'plugins/ban/config.php');

BAN_check();

// +---------------------------------------------------------------------------+
// | Geeklog Plugin API Implementations                                        |
// +---------------------------------------------------------------------------+

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
* Shows the statistics for the ban plugin on stats.php.  If
* $showsitestats is 1 then we are to only print the overall stats in the 'site
* statistics' box otherwise we show the detailed stats for the stats
*
* @showsitestate        int         Flag to let us know which stats to get
*/

function plugin_showstats_ban($showsitestats) 
{
    $retval = array();
	return $retval;
}

/**
* This will put an option for ban in the command and control block on
* moderation.php
*
*/
function plugin_cclabel_ban() 
{
    global $LANG_BAN00, $_CONF, $_ST_plugin_name;

    $retval = array();
    if ((SEC_inGroup('Root')) || (SEC_hasRights('ban.admin'))) {
        $retval = array($LANG_BAN00['ban'],$_CONF['site_admin_url'] . '/plugins/ban/index.php',$_CONF['site_admin_url'] . '/plugins/ban/ban.gif');
    }
    return $retval;
}

/**
* returns the administrative option for this plugin
*
*/
function plugin_getadminoption_ban() 
{
    global $LANG_BAN00, $_CONF;

    if ((SEC_inGroup('Root')) || (SEC_hasRights('ban.admin'))) {
        return array($LANG_BAN00['ban'],$_CONF['site_admin_url'] . '/plugins/ban/index.php',0);
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
// BAN Functions                                                                //
//////////////////////////////////////////////////////////////////////////////////


/*
* Check for banned websites
*/

function BAN_check()
{
    global $_TABLES, $_BAN_log, $_BAN_page;
    
		$HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; 
		$REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
		$HTTP_REFERER = $_SERVER['HTTP_REFERER'];
		$SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
		
    $result = DB_Query("SELECT * FROM {$_TABLES['ban']}",1);
    $nrows = DB_numRows( $result );
    for( $i = 1; $i <= $nrows; $i++ ) {
        $A = DB_fetchArray($result);
        
        if (eregi($A['data'],$$A['bantype'])) {
            if ($_BAN_log) {
                BAN_Log('Access Banned because ' . $$A['bantype'] . " matches {$A['data']}");
            }
            if ($_BAN_page != '') {
                echo COM_refresh($_BAN_page);
                die();
            } else {
                die();
            }
        }
    }
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
    global $_CONF, $LANG01;
    
    $timestamp = strftime( "%c" );
    $logfile = $_CONF['path_log'] . 'ban.log';
           
    if( !$file = fopen( $logfile, a ))
    {
        COM_errorLog($LANG01[33] . $logfile . ' (' . $timestamp . ')',1);
    }
               
    fputs( $file, "$timestamp - $logentry \n" );
              
    return $retval;
}
                                        

/**
* Removes the datastructures for this plugin from the Geeklog database
*
* This may get called by the install routine to undue anything done to this
* point.  To do that, $steps will have a list of steps to undo
*
* @steps   Array    Holds all the steps that have been completed by the install
*
*/  
function plugin_uninstall_ban($steps = '')
{
    global $_TABLES;

    $FEATURES = array ( 'ban.admin');
    $TABLES = array ('ban');
    
    // Unregister the plugin with Geeklog
    COM_errorLog('Attempting to unregister the Ban plugin from Geeklog',1);
    DB_query("DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'ban'",1);

    // Drop ban tables
    foreach($TABLES as $table) {
        $t = $_TABLES["$table"];
        COM_errorLog("Removing Table $t",1);
        DB_query("DROP TABLE $t",1);
    }

    // Remove Security for this plugin
    $grp_id = DB_getItem($_TABLES['vars'], 'value', "name = 'ban_group_id'");
    

    DB_query("DELETE FROM {$_TABLES['groups']} WHERE grp_id = $grp_id",1);
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE name = 'ban_group_id'");
    COM_errorLog("Removing root users from admin of ban");
    DB_query("DELETE FROM {$_TABLES['group_assignments']} WHERE ug_main_grp_id = $grp_id",1);
    COM_errorLog("Removing group id in vars");
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE value = $grp_id",1);
    
    
    foreach ($FEATURES as $feature) {
        COM_errorLog("Removing $feature feature and rights to it",1);
        $feat_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = '$feature'");
        DB_query("DELETE FROM {$_TABLES['access']} WHERE acc_ft_id = $feat_id",1);
        DB_query("DELETE FROM {$_TABLES['features']} WHERE ft_id = $feat_id",1);
    }
    COM_errorLog("Removing the {$_TABLES['vars']} reference to Ban");
    DB_query("DELETE FROM {$_TABLES['vars']} WHERE value = 'ban'",1);
    COM_errorLog('...success',1);
}

/**
* Called by the plugin Editor to display the current plugin code version
* This may be different then the version installed and registered currently.
* If newer then you may want to run the update
*/
function plugin_chkVersion_ban()
{
    global $_BAN_CONF;

    return $_BAN_CONF['version'];
}

/**
* Upgrade to current version of rating.
*
*
*/
function plugin_upgrade_ban()
{
	global $_TABLES;
	
	$res = DB_query( "SELECT pi_version FROM {$_TABLES['plugins']} WHERE pi_name = 'ban'" );
	$row = DB_fetchArray( $res, false );
	
	COM_errorLog( "Updating Ban plugin from v" . $row['pi_version'] . ' to v' . plugin_chkVersion_ban(), 1 );
	
	return ban_doUpgrades( $row['pi_version'] );
}

function ban_doUpgrades( $current_rating_version )
{
	global $_TABLES, $_CONF, $_DB;

	$_DB->setDisplayError( true );

	$done = false;

	while ( $done == false )
	{
		switch ( $current_rating_version )
		{
			case '1.0':
			case '1.0.1':
			case '1.0.2':
				$current_rating_version = '1.0.3';
				break;
			default:
				$done = true;
				break;
		}
	}

	DB_query( "UPDATE {$_TABLES['plugins']} SET pi_version = '{$current_rating_version}' WHERE pi_name = 'ban'" );
	
	return true;
}
?>