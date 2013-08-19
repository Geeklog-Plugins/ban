<?php

// +---------------------------------------------------------------------------+
// | Ban Geeklog Plugin 1.2                                                    |
// +---------------------------------------------------------------------------+
// | index.php                                                                 |
// | Administration page.                                                      |
// |                                                                           |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2003 by the following authors:                              |
// |                                                                           |
// | Author: Tom Willett       - twillett@users.sourceforge.net                |
// |                                                                           |
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

require_once('../../../lib-common.php');


// Only let admin users access this page
if ((!SEC_inGroup('Root')) && (!SEC_hasRights('ban.admin'))) {
    // Someone is trying to illegally access this page
    COM_errorLog("Someone has tried to illegally access the Ban Admin page.  User id: {$_USER['uid']}, Username: {$_USER['username']}, IP: $REMOTE_ADDR",1);
    $display = COM_siteHeader();
    $display .= COM_startBlock($LANG_BAN00['access_denied']);
    $display .= $LANG_BAN00['access_denied_msg'];
    $display .= COM_endBlock();
    $display .= COM_siteFooter(true);
    echo $display;
    exit;
}
 
/**
* Main 
*/

$newdata = $_REQUEST['newdata'];
$newtype = $_REQUEST['newtype'];
$action = $_REQUEST['action'];

if (!empty($newdata) AND !empty($newtype)) {
	if (!get_magic_quotes_gpc()) {
			$newdata = addslashes($newdata);
	}
	
	if ($action == $LANG_BAN00['add']) {
			DB_query("INSERT INTO {$_TABLES['ban']} VALUES ('$newtype', '$newdata')",1);
	} else if ($action == $LANG_BAN00['delete']) {
			DB_query("DELETE FROM {$_TABLES['ban']} WHERE bantype='$newtype' AND data='$newdata'",1);
	}
}

$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/ban/templates');
$T->set_file('admin', 'admin.thtml');
$T->set_block('admin','IP','ABlock');
$T->set_var('site_url',$_CONF['site_url']);
$T->set_var('bantype',$LANG_BAN00['type']);
$T->set_var('bandata',$LANG_BAN00['data']);
$T->set_var('header', $LANG_BAN00['desc']);
$T->set_var('site_admin_url', $_CONF['site_admin_url']);
$T->set_var('ban_msg',$LANG_BAN00['info']);
$T->set_var('ipbtnmsg', $LANG_BAN00['add']);
$T->set_var('dipbtnmsg', $LANG_BAN00['delete']);

$result = DB_Query("SELECT * FROM {$_TABLES['ban']}");
$nrows = DB_numRows( $result );
for( $i = 1; $i <= $nrows; $i++ ) {
    $A = DB_fetchArray($result);
    $T->set_var('type1',$A['bantype']);
    $T->set_var('data1',$A['data']);
    $T->Parse('ABlock','IP',true);        
}            
$T->parse('output','admin');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter(true);

echo $display;
?>
