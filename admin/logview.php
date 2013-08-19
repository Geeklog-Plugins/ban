<?php

// +---------------------------------------------------------------------------+
// | LogView 1.0 for Geeklog - The Ultimate Weblog                             |
// +---------------------------------------------------------------------------+
// | logview.php                                                               |
// |                                                                           |
// | This Geeklog log file viewer.  Drop it into your admin directory --       |
// | edit the lib-common.php line and path line -- Your ready.                 |
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

//
// you will need to edit the path to lib-common.php if you put this file anywhere other than
// the Ban Plugin admin directory in a standard install
//
require_once('../../../lib-common.php');

// Path to this file
$path = $_CONF['site_admin_url'] . '/plugins/ban/';

// Only let Root users access this page
if (!SEC_inGroup('Root')) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the LogView page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock("Access Denied!!!");
    $display .= "You are illegally trying to access the File LogView page.  This attempt has been logged";
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}

/*
* Main Function
*/

$action = $_REQUEST['action'];
$log = $_REQUEST['log'];

$display = COM_siteHeader();
$display .= COM_startBlock("Geeklog Log File Viewer");
$display .= "<p>Views/Clear the Geeklog Log Files.<p>";
$display .= "<form method=\"post\" action=\"{$path}/logview.php\">";
$display .= "File:&nbsp;&nbsp;&nbsp;";
$files = array();
if ($dir = @opendir($_CONF['path_log'])) {
    while(($file = readdir($dir)) !== false) {
        if (is_file($_CONF['path_log'] . $file)) { array_push($files,$file); }
    }
    closedir($dir);
}
$display .= '<SELECT name="log">';
if (empty($log)) { $log = $files[0]; } // default file to show 
for ($i = 0; $i < count($files); $i++) {
    $display .= '<option value="' . $files[$i] . '"';
    if ($log == $files[$i]) { $display .= ' SELECTED'; }
    $display .= '>' . $files[$i] . '</option>';
    next($files);
}
$display .= "</SELECT>&nbsp;&nbsp;&nbsp;&nbsp;";
$display .= "<input type=\"submit\" name=\"action\" value=\"View Log File\">";
$display .= "&nbsp;&nbsp;&nbsp;&nbsp;";
$display .= "<input type=\"submit\" name=\"action\" value=\"Clear Log File\">";
$display .= "</form>";
if ($action == 'Clear Log File') {
    unlink($_CONF['path_log'] . $log);
    $timestamp = strftime( "%c" );
    $fd = fopen( $_CONF['path_log'] . $log, a );
    fputs( $fd, "$timestamp - Log File Cleared \n" );
    fclose($fd);
    $action = 'View Log File';
}
if ($action == 'View Log File') {
    $display .= "<hr><p><b>Log File: $log</b></p><pre>";
    $display .= implode('', file($_CONF['path_log'] . $log));
    $display .= "</pre>";
}
$display .= COM_endBlock();
$display .= COM_siteFooter(false);
echo $display;
?>