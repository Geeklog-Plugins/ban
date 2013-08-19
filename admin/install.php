<?php

// +---------------------------------------------------------------------------+
// | Ban Plugin 1.0 for Geeklog - The Ultimate Weblog                          |
// +---------------------------------------------------------------------------+
// | install.php                                                               |
// |                                                                           |
// | This file installs and removes the data structures for the ban            |
// | plugin for Geeklog.                                                       |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2003 by the following authors:                              |
// |                                                                           |
// | Authors: Tom Willett        - twillett@users.sourceforge.net              |
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

require_once('../../../lib-common.php');
require_once($_CONF['path'] . 'plugins/ban/functions.inc');

//
// Universal plugin install variables
//
$pi_version = '1.0.2';                   // Plugin Version
$gl_version = '1.3.8';                  // GL Version plugin for
$pi_url = 'http://gplugs.sourceforge.net';   // Plugin Homepage

//
// $NEWTABLE contains table name(s) and sql to create it(them)
//
$NEWTABLE = array();
$NEWTABLE['ban'] = "CREATE TABLE " . $_TABLES['ban'] . " ("
    . " bantype varchar(40) NOT NULL default '',"
    . " data varchar(255) NOT NULL default '',"
    . " KEY bantype (bantype)"
    . ") TYPE=MyISAM";

//
// Default data
//
$DEFVALUES = array();

// Security Feature to add
$NEWFEATURE = array();
$NEWFEATURE['ban.admin']="Ban Admin";

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the Ban install/uninstall page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_BAN00['access_denied']);
    $display .= $LANG_BAN00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

/**
* Puts the datastructures for this plugin into the Geeklog database
*
*/
function plugin_install_ban()
{
    global $pi_version, $gl_version, $pi_url, $NEWTABLE, $DEFVALUES, $NEWFEATURE;
    global $_TABLES, $_CONF, $LANG_BAN00;

    COM_errorLog("Attempting to install the Ban Plugin",1);

    // Create the Plugins Tables
    
    foreach ($NEWTABLE as $table => $sql) {
        COM_errorLog("Creating $table table",1);
        DB_query($sql,1);
        if (DB_error()) {
            COM_errorLog("Error Creating $table table",1);
            plugin_uninstall_ban();
            return false;
            exit;
        }
        COM_errorLog("Success - Created $table table",1);
    }
    
    // Create the plugin admin security group
    COM_errorLog("Attempting to create Ban admin group", 1);
    DB_query("INSERT INTO {$_TABLES['groups']} (grp_name, grp_descr) "
        . "VALUES ('Ban Admin', 'Users in this group can administer the Ban plugin')",1);
    if (DB_error()) {
        plugin_uninstall_ban();
        return false;
        exit;
    }
    COM_errorLog('...success',1);
    $group_id = DB_insertId();
    
    // Save the grp id for later uninstall
    COM_errorLog('About to save group_id to vars table for use during uninstall',1);
    DB_query("INSERT INTO {$_TABLES['vars']} VALUES ('ban_group_id', $group_id)",1);
    if (DB_error()) {
        plugin_uninstall_ban();
        return false;
        exit;
    }
    COM_errorLog('...success',1);
    
    // Add plugin Features
    foreach ($NEWFEATURE as $feature => $desc) {
        COM_errorLog("Adding $feature feature",1);
        DB_query("INSERT INTO {$_TABLES['features']} (ft_name, ft_descr) "
            . "VALUES ('$feature','$desc')",1);
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature",1);
            plugin_uninstall_ban();
            return false;
            exit;
        }
        $feat_id = DB_insertId();
        COM_errorLog("Success",1);
        COM_errorLog("Adding $feature feature to admin group",1);
        DB_query("INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($feat_id, $group_id)");
        if (DB_error()) {
            COM_errorLog("Failure adding $feature feature to admin group",1);
            plugin_uninstall_ban();
            return false;
            exit;
        }
        COM_errorLog("Success",1);
    }        
    
    // OK, now give Root users access to this plugin now! NOTE: Root group should always be 1
    COM_errorLog("Attempting to give all users in Root group access to Ban admin group",1);
    DB_query("INSERT INTO {$_TABLES['group_assignments']} VALUES ($group_id, NULL, 1)");
    if (DB_error()) {
        plugin_uninstall_ban();
        return false;
        exit;
    }

    // Register the plugin with Geeklog
    COM_errorLog("Registering Ban plugin with Geeklog", 1);
    DB_query( "DELETE FROM {$_TABLES['plugins']} WHERE pi_name = 'ban'");
    DB_query("INSERT INTO {$_TABLES['plugins']} (pi_name, pi_version, pi_gl_version, pi_homepage, pi_enabled) "
        . "VALUES ('ban', '$pi_version', '$gl_version', '$pi_url', 1)");

    if (DB_error()) {
        plugin_uninstall_ban();
        return false;
        exit;
    }

    COM_errorLog("Succesfully installed the Ban Plugin!",1);
    return true;
}

/* 
* Main Function
*/

$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/ban/templates');
$T->set_file('install', 'install.thtml');
$T->set_var('install_header', $LANG_BAN00['desc']);
$T->set_var('img',$_CONF['site_admin_url'] . '/plugins/ban/ban.gif');
$T->set_var('cgiurl', $_CONF['site_admin_url'] . '/plugins/ban/install.php');
$T->set_var('admin_url', $_CONF['site_admin_url'] . '/plugins/ban/index.php');
$T->set_var('readme_url', $_CONF['site_admin_url'] . '/plugins/ban/readme.html');
$T->set_var('install_url', $_CONF['site_admin_url'] . '/plugins/ban/install.html');
$T->set_var('installfile', $LANG_BAN00['install']);
$T->set_var('installmsg3', $LANG_BAN00['installmsg3']);

$action = $_REQUEST['action'];
if ($action == 'install_confirmed') {
    if (plugin_install_ban()) {
        $T->set_var('installmsg1',$LANG_BAN00['install_success']);
    } else {
        $T->set_var('installmsg1',$LANG_BAN00['install_failed']);
    }
} else if ($action == "uninstall") {
   plugin_uninstall_ban();
   $T->set_var('installmsg1',$LANG_BAN00['uninstall_msg']);
}

if (DB_count($_TABLES['plugins'], pi_name, 'ban') == 0) {
    $T->set_var('installmsg2', $LANG_BAN00['uninstalled']);
	$T->set_var('btnmsg', $LANG_BAN00['install']);
	$T->set_var('action','install_confirmed');
} else {
    $T->set_var('installmsg2', $LANG_BAN00['installed']);
	$T->set_var('btnmsg', $LANG_BAN00['uninstall']);
	$T->set_var('action','uninstall');
}
$T->parse('output','install');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter(true);

echo $display;
?>