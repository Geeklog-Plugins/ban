<?php

// +---------------------------------------------------------------------------+
// | Ban Geeklog Plugin 2.0.0                                                    |
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

$ban_data = COM_applyFilter($_REQUEST['data']);
$ban_type = COM_applyFilter($_REQUEST['type']);
$ban_status = COM_applyFilter($_REQUEST['status'], true);
$ban_note = $_REQUEST['note'];
$action = $_REQUEST['action'];
echo $ban_status;
$clear_flag = false;
if (!empty($ban_data) AND !empty($ban_type) AND ($ban_status >= CONST_BAN_STATUS_WHITE AND $ban_status <= CONST_BAN_STATUS_TTL_LONG)) {
	if (!get_magic_quotes_gpc()) {
			$ban_data = addslashes($ban_data);
	}

	if ($action == $LANG_BAN00['add']) {
	    $clear_flag = true;
	    // Delete any first incase update
	    DB_query("DELETE FROM {$_TABLES['ban']} WHERE bantype='$ban_type' AND data='$ban_data' AND status=$ban_status",1);
	    // Add
	    $ban_note = addslashes($ban_note);
        DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('$ban_type', '$ban_data', $ban_status, '$ban_note')",1);
	} else if ($action == $LANG_BAN00['delete']) {
	    $clear_flag = true;
        DB_query("DELETE FROM {$_TABLES['ban']} WHERE bantype='$ban_type' AND data='$ban_data' AND status=$ban_status",1);
	} else if ($action == $LANG_BAN00['edit']) {
        // populate fields	    
        $result = DB_Query("SELECT bantype, status, created, data, note FROM {$_TABLES['ban']} WHERE bantype='$ban_type' AND data = '$ban_data'");
        $nrows = DB_numRows( $result );
        if ($nrows> 0) {
            $A = DB_fetchArray($result);
            
            $ban_data = $A['data'];
            $ban_type = $A['bantype'];
            $ban_status = $A['status'];
            $ban_created = $A['created'];
            $ban_note = stripslashes($A['note']);         
            
        }
    } else { // Clear
        $clear_flag = true;
	}
} else {
    $clear_flag = true;
}

if ($clear_flag) {
    $ban_data = '';
    $ban_type = 'REMOTE_ADDR';
    $ban_status = CONST_BAN_STATUS_NORMAL;
    $ban_created = '';
    $ban_note = '';
}

$display = COM_siteHeader();
$T = new Template($_CONF['path'] . 'plugins/ban/templates');
$T->set_file('admin', 'admin.thtml');
$T->set_block('admin','IP','ABlock');

$T->set_var('lang_type',$LANG_BAN00['type']);
$T->set_var('lang_data',$LANG_BAN00['data']);
$T->set_var('lang_status',$LANG_BAN00['status']);
$T->set_var('lang_created',$LANG_BAN00['created']);
$T->set_var('lang_note',$LANG_BAN00['note']);

$T->set_var('header', $LANG_BAN00['desc']);
$T->set_var('site_url',$_CONF['site_url']);
$T->set_var('site_admin_url', $_CONF['site_admin_url']);
$T->set_var('ban_msg',$LANG_BAN00['instructions']);

$T->set_var('ipbtnmsg', $LANG_BAN00['add']);
$T->set_var('dipbtnmsg', $LANG_BAN00['delete']);
$T->set_var('cipbtnmsg', $LANG_BAN00['clear']);

// Set type
$retval = '';
for( $i = 1; $i <= 4; $i++ ) {
    switch ($i) {
        case 1:
            $name = 'REMOTE_ADDR';
            break;
            
        case 2:
            $name = 'HTTP_REFERER';
            break;
            
        case 3:
            $name = 'HTTP_USER_AGENT';
            break;
            
        case 4:
            $name = 'SCRIPT_NAME';
            break;
            
    }
    $selected = '';    
    if ($ban_type == $name) {
        $selected = ' SELECTED';
    }
    $retval .= '<option value="' . $name . '"' . $selected . '>' .  $name . '</option>';
}

$T->set_var('options_type', $retval);    

// Set Status
$retval = '';
for( $i = CONST_BAN_STATUS_WHITE; $i <= CONST_BAN_STATUS_TTL_LONG; $i++ ) {
    $selected = '';
    if ($ban_status == $i) {
        $selected = ' SELECTED';
    }
    
    switch ($i) {
        case CONST_BAN_STATUS_TTL_SHORT:
            $name = $LANG_BAN00['status_ttl_short'];
            break;
            
        case CONST_BAN_STATUS_TTL_MEDIUM:
            $name = $LANG_BAN00['status_ttl_medium'];
            break;
            
        case CONST_BAN_STATUS_TTL_LONG:
            $name = $LANG_BAN00['status_ttl_long'];
            break;
            
        case CONST_BAN_STATUS_WHITE:
            $name = $LANG_BAN00['status_white'];
            break;
            
        default:
            $name = $LANG_BAN00['status_normal'];
            
    }
    $retval .= '<option value="' . $i . '"' . $selected . '>' .  $name . '</option>';
}
$T->set_var('options_status', $retval);

// Set Created
$T->set_var('created', $ban_created);

// Set Data
$T->set_var('data', $ban_data);

// Set Note
$T->set_var('note', $ban_note);

// Set ban list
$result = DB_Query("SELECT bantype, status, created, data, note FROM {$_TABLES['ban']} ORDER BY bantype, data");
$nrows = DB_numRows( $result );
for( $i = 1; $i <= $nrows; $i++ ) {
    $A = DB_fetchArray($result);
    $T->set_var('type1',$A['bantype']);
    
    switch ($A['status']) {
        case CONST_BAN_STATUS_TTL_SHORT:
            $retval =  $LANG_BAN00['status_ttl_short'];
            break;
            
        case CONST_BAN_STATUS_TTL_MEDIUM:
            $retval =  $LANG_BAN00['status_ttl_medium'];
            break;
            
        case CONST_BAN_STATUS_TTL_LONG:
            $retval =  $LANG_BAN00['status_ttl_long'];
            break;
            
        case CONST_BAN_STATUS_WHITE:
            $retval =  $LANG_BAN00['status_white'];
            break;
            
        default:
            $retval =  $LANG_BAN00['status_normal'];
            
    }
    $T->set_var('status_name',$retval);
    $T->set_var('status1',$A['status']);
    $T->set_var('created1',$A['created']);
    $T->set_var('data1',$A['data']);
    $note = '';
    if (trim($A['note']) != '') {
        $note = COM_getTooltip('', $A['note'], '', $LANG_BAN00['note'],'information');
    }
    $T->set_var('note1',$note);
    $T->set_var('action_delete', $LANG_BAN00['delete']);
    $T->set_var('action_edit', $LANG_BAN00['edit']);
    $T->Parse('ABlock','IP',true);        
}            
$T->parse('output','admin');
$display .= $T->finish($T->get_var('output'));
$display .= COM_siteFooter();

echo $display;
?>
