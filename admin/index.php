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

require_once ('../../../lib-common.php');
require_once ('../../auth.inc.php');

if (!SEC_hasRights ('ban.admin')) {
    $display = COM_showMessageText($MESSAGE[29], $MESSAGE[30]);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $MESSAGE[30]));
    COM_accessLog("User {$_USER['username']} tried to illegally access the ban administration screen.");
    COM_output($display);
    exit;
}

/**
* Displays the ban form 
*
* @param    array   $A      Data to display
* @param    string  $error  Error message to display
*
*/ 
function ban_form($A, $error = false) 
{
    global $_CONF, $LANG_BAN00, $LANG_ACCESS, $_TABLES, $MESSAGE;

    $display = '';

    if ($error) {
        // Error = 1
        $display .= COM_showMessageText($LANG_BAN00['error_editor_no_data'], $LANG_BAN00['ban_editor']);
    }
        
    $ban_template = COM_newTemplate(CTL_plugin_templatePath('ban', 'admin'));
    $ban_template->set_file('editor', 'editor.thtml');

    $ban_template->set_var('start_block_editor',
            COM_startBlock($LANG_BAN00['ban_editor']), '',
                    COM_getBlockTemplate ('_admin_block', 'header'));
    
    $ban_template->set_var('lang_id',$LANG_BAN00['id']);
    $ban_template->set_var('id', $A['id']);
    $ban_template->set_var('lang_type',$LANG_BAN00['type']);
    $ban_template->set_var('lang_data',$LANG_BAN00['data']);
    $ban_template->set_var('lang_status',$LANG_BAN00['status']);
    $ban_template->set_var('lang_created',$LANG_BAN00['created']);
    $ban_template->set_var('lang_note',$LANG_BAN00['note']);
    
    // Set type
    // REMOTE_ADDR, HTTP_REFERER, HTTP_USER_AGENT, SCRIPT_NAME
    // REMOTE_ADDR_RANGE, REMOTE_ADDR_CIDR, REMOTE_ADDR_REGEX, HTTP_REFERER_REGEX, HTTP_USER_AGENT_REGEX, SCRIPT_NAME_REGEX
    $retval = '';
    for( $i = 1; $i <= 10; $i++ ) {
        switch ($i) {
            case 1:
                $name = 'REMOTE_ADDR';
                break;
            case 2:
                $name = 'REMOTE_ADDR_RANGE';
                break;
            case 3:
                $name = 'REMOTE_ADDR_CIDR';
                break;
            case 4:
                $name = 'REMOTE_ADDR_REGEX';
                break;
                
            case 5:
                $name = 'HTTP_REFERER';
                break;
            case 6:
                $name = 'HTTP_REFERER_REGEX';
                break;
                
            case 7:
                $name = 'HTTP_USER_AGENT';
                break;
            case 8:
                $name = 'HTTP_USER_AGENT_REGEX';
                break;                
                
            case 9:
                $name = 'SCRIPT_NAME';
                break;
            case 10:
                $name = 'SCRIPT_NAME_REGEX';
                break;
        }
        $selected = '';    
        if ($A['bantype'] == $name) {
            $selected = ' SELECTED';
        }
        $retval .= '<option value="' . $name . '"' . $selected . '>' .  $name . '</option>';
    }
    $ban_template->set_var('options_type', $retval);    
    
    // Set Status
    $retval = '';
    if ($A['status'] == '') {
        $A['status'] = CONST_BAN_STATUS_NORMAL; // Default        
    }
    for( $i = CONST_BAN_STATUS_WHITE; $i <= CONST_BAN_STATUS_TTL_LONG; $i++ ) {
        $selected = '';
        if ($A['status'] == $i) {
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
    $ban_template->set_var('options_status', $retval);
    
    // Set Created
    $ban_template->set_var('created', $A['created']);
    
    // Set Data
    $ban_template->set_var('data', $A['data']);
    
    // Set Note
    $ban_template->set_var('note', stripslashes($A['note']));
    
    $ban_template->set_var('lang_save', $LANG_BAN00['save']);
    $ban_template->set_var('lang_cancel', $LANG_BAN00['cancel']);
    
    if ($A['id'] != "0") {
        $ban_template->set_var('allow_delete', true);
        $ban_template->set_var('lang_delete', $LANG_BAN00['delete']);
        $ban_template->set_var('confirm_message', $MESSAGE[76]);
        // Old delete option to support older themes
        $ban_template->set_var('delete_option', '<input type="submit" value="' . $LANG_BAN00['delete'] . '" name="mode" onclick="return confirm(' . "'" .  $MESSAGE[76] . "'" .  ');">');
    }

    $ban_template->set_var('end_block',
            COM_endBlock (COM_getBlockTemplate ('_admin_block', 'footer')));
    
    $display .= $ban_template->parse('output','editor');

    return $display;
}

function ban_list($database_age = '')
{
    global $_CONF, $_BAN_CONF, $_TABLES, $_IMAGE_TYPE, $LANG_ADMIN, $LANG_BAN00;
    
    require_once( $_CONF['path_system'] . 'lib-admin.php' );
    
    $retval = '';

    $header_arr = array(      # dislay 'text' and use table field 'field'
                    array('text' => $LANG_ADMIN['edit'], 'field' => 'edit', 'sort' => false),
                    array('text' => $LANG_BAN00['note'], 'field' => 'note', 'sort' => true),
                    array('text' => $LANG_BAN00['type'], 'field' => 'bantype', 'sort' => true),
                    array('text' => $LANG_BAN00['status'], 'field' => 'status', 'sort' => true),
                    array('text' => $LANG_BAN00['created'], 'field' => 'created', 'sort' => true),
                    array('text' => $LANG_BAN00['data'], 'field' => 'data', 'sort' => true)
    );
    $defsort_arr = array('field' => 'bantype', 'direction' => 'asc');

    if ($_BAN_CONF['stopforumspam']) {
        $menu_arr = array (
                        array('url' => $_CONF['site_admin_url'] . '/plugins/ban/index.php?mode=edit',
                              'text' => $LANG_ADMIN['create_new']),
                        array('url' => $_CONF['site_admin_url'] . '/plugins/ban/index.php?mode=sfs_download',
                              'text' => $LANG_BAN00['download_sfs']),
                        array('url' => $_CONF['site_admin_url'],
                              'text' => $LANG_ADMIN['admin_home'])
        );
    } else {
        $menu_arr = array (
                        array('url' => $_CONF['site_admin_url'] . '/plugins/ban/index.php?mode=edit',
                              'text' => $LANG_ADMIN['create_new']),
                        array('url' => $_CONF['site_admin_url'],
                              'text' => $LANG_ADMIN['admin_home'])
        );
    }

    $readme_url = $_CONF['site_admin_url'] . '/plugins/ban/readme.html';
    
    $retval .= COM_startBlock($LANG_BAN00['ban_list'], $readme_url,
                                                COM_getBlockTemplate('_admin_block', 'header'));
    
    
    $instructions = $LANG_BAN00['instructions'];
    if ($_BAN_CONF['stopforumspam']) {
        $destination = $_CONF['path'] . 'plugins/ban/files/';
        $filename = $_BAN_CONF['stopforumspam_database_name'];
        
        if (file_exists($destination . $filename)) {
            // SFS Database Age Message
            if ($database_age == '') {
                $database_age = $LANG_BAN00['not_available'];
            } else {
                $database_age = date("Y-m-d H:i:s", $database_age);
            }
            $instructions .= sprintf($LANG_BAN00['instructions_sfs'], $database_age);

            // Database File Size Message
            $file_size = filesize($destination . $filename);
            if ($file_size > 0) {
                $file_size = round($file_size / 1024); // Convert to bytes to KB
                if ($file_size > 1000) {
                    $instructions .= sprintf($LANG_BAN00['instructions_sfs_size'], $file_size);
                } else {
                    $instructions .= sprintf($LANG_BAN00['instructions_sfs_size_small'], $file_size);
                }
            } else {
                $instructions .= $LANG_BAN00['instructions_sfs_size_error'];
            }
        } else {
            $instructions .= sprintf($LANG_BAN00['instructions_sfs_db_missing'], $destination . $filename);
        }
    }

    $HTTP_USER_AGENT = $_SERVER['HTTP_USER_AGENT']; 
    $REMOTE_ADDR = $_SERVER['REMOTE_ADDR'];
    $HTTP_REFERER = ''; // May not always be set if no real referrer. Depends on web server 
    if (isset($_SERVER['HTTP_REFERER'])) {
        $HTTP_REFERER = $_SERVER['HTTP_REFERER'];
    }
    $SCRIPT_NAME = $_SERVER['SCRIPT_NAME'];
    $instructions .= sprintf($LANG_BAN00['instructions_info'], $HTTP_USER_AGENT, $REMOTE_ADDR, $HTTP_REFERER, $SCRIPT_NAME);
    
    $retval .= ADMIN_createMenu($menu_arr, $instructions, plugin_geticon_ban());
		
    $text_arr = array('has_extras'   => true,
                       'form_url' => $_CONF['site_admin_url'] . "/plugins/ban/index.php");

    $query_arr = array('table' => 'ban',
                       'sql' => "SELECT * "
                               ."FROM {$_TABLES['ban']} WHERE 1 ",
                       'query_fields' => array('bantype', 'data', 'note', 'created'),
                       'default_filter' => "");
    
    $listoptions = array('chkdelete' => true, 'chkfield' => 'id');
    
    $form_arr = array('bottom' => '<input type="hidden" name="mode" value="batchdeleteban"' . XHTML . '>');    

    $retval .= ADMIN_list ("ban", "plugin_getListField_ban", $header_arr, $text_arr,
                            $query_arr, $defsort_arr, '', '', $listoptions, $form_arr);
    
    $retval .= COM_endBlock(COM_getBlockTemplate('_admin_block', 'footer'));
		
    return $retval;

}

/**
* Displays the Ban Editor
*
* @tag          string      ban to edit
* @mode         string      Mode
* @error         string      Error number
*
*/
function ban_editor($id, $mode = '', $error = false)
{
    global $_TABLES;

    if (!empty($id) && $id !=0 && $mode == 'edit') {
        $query = DB_query("SELECT * FROM {$_TABLES['ban']} WHERE id = $id");
        $A = DB_fetchArray($query);
    } elseif ($mode == 'edit') {
        $A['id'] = 0;
    } else {
        $A = $_POST;
        $A['id'] = COM_applyFilter($A['id']);
    }
    
    return ban_form($A, $error);
}

/** 
* Saves a ban to the database
*
*/
// If making changes here make sure to change BAN_for_plugins_ban_ip in function.inc if needed
function ban_save($id, $type, $status, $data, $note)
{
    global $_CONF, $LANG_BAN00, $_TABLES;
    
    $retval = "";
    
    if (!empty($type) && !empty($data)) {
        // Clean up the text
        $data = addslashes($data);
        $note = addslashes(strip_tags($note));
        
        
            
        if ($id == 0) {
            // Make sure ban type and data is unique before inserting
            if (DB_count($_TABLES['ban'], array('data', 'bantype'), array($data, $type)) == 0) {
                DB_query("INSERT INTO {$_TABLES['ban']} (bantype, data, status, note) VALUES ('$type', '$data', $status, '$note')",1);
            } else {
                $retval = ban_editor($id, '', true);
                $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_BAN00['ban_editor']));                
                
                return $retval;
            }
        } else {
            // Make sure ban type and data is unique before updating
            $sql = "SELECT COUNT(id) count FROM {$_TABLES['ban']} WHERE bantype = '$type' AND data = '$data' AND id != $id";
            $result = DB_Query($sql);
            $A = DB_fetchArray($result);            
            $count = $A['count'];
            if ($count == 0) {
                DB_query("UPDATE {$_TABLES['ban']} SET bantype = '$type', data = '$data', status = $status, note = '$note' WHERE id = $id",1);
            } else {
                $retval = ban_editor($id, '', true);
                $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_BAN00['ban_editor']));                                
                
                return $retval;
            }
        }
        
        $retval = COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php?msg=1');
    } else {
        $retval = ban_editor($id, '', 1);
        $retval = COM_createHTMLDocument($retval, array('pagetitle' => $LANG_BAN00['ban_editor']));                                        
    }
    
    return $retval;
}

function batchdeleteban()
{
    global $_CONF, $LANG28;
            
    $msg = '';
    $field_list = array();
    if (isset($_POST['delitem'])) {
        $field_list = $_POST['delitem'];
    }

    if (count($field_list) == 0) {
        $msg = $LANG28[72] . "<br>";
    }
    $c = 0;

    if (isset($_POST['delitem']) AND is_array($_POST['delitem'])) {
        foreach($_POST['delitem'] as $delitem) {
            $delitem = COM_applyFilter($delitem);
            if (!ban_delete($delitem, true)) {
                $msg .= "<strong>{$LANG28[2]} $delitem {$LANG28[70]}</strong><br>\n";
            } else {
                $c++; // count the deleted links
            }
        }
    }

    // Since this function is used for deletion only, its necessary to say that
    // zero where deleted instead of just leaving this message away.
    COM_numberFormat($c); // just in case we have more than 999)..
    $msg .= "{$LANG28[71]}: $c<br>\n";
    
    return $msg;
}

// If making changes here make sure to change BAN_for_plugins_ban_ip in function.inc if needed 
function ban_delete($id, $return_flag = 0)
{
    global $_CONF, $_TABLES, $_USER;

    $sql = "SELECT id 
        FROM {$_TABLES['ban']} 
        WHERE id = $id";
        
    $result = DB_query($sql);

    $A = DB_fetchArray($result);

    // Security Check
    if ((!SEC_hasRights('ban.admin')) OR (DB_numRows ($result) == 0)) {
        if ($return_flag) {
            return false;
        } else {
            COM_accessLog ("User {$_USER['username']} tried to illegally delete ban id=$id.");
            return COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php');
        }        
    }    

    // Actual Delete
    DB_delete ($_TABLES['ban'], 'id', $id);
    
    if ($return_flag) {
        return true;
    } else {
        return COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php?msg=2');
    }
}




// MAIN
$mode = '';
if (isset($_REQUEST['mode'])) {
    $mode = COM_applyFilter ($_REQUEST['mode']);
}

$id = 0;
if (isset($_REQUEST['id'])) {
    $id = COM_applyFilter ($_REQUEST['id'], true);
}



if (($mode == $LANG_BAN00['delete']) && !empty ($LANG_BAN00['delete'])) {
    if (empty($id)) { 
        COM_errorLog ('Attempted to delete ban id=' . $id );
        $display .= COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php');
    } else {
        $display .= ban_delete($id); 
    }    
} else if ($mode == 'batchdeleteban') {
    $msg = batchdeleteban();
    $display .= COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php?msg=3');     
} else if ($mode == 'sfs_download') {
    if (BAN_sfs_download()) {
        $display .= COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php?msg=4');     
    } else {
        $display .= COM_redirect($_CONF['site_admin_url'] . '/plugins/ban/index.php?msg=5');     
    }
} else if ($mode == 'edit') {
    $display = ban_editor($id, $mode);
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_BAN00['ban_editor']));
} else if (($mode == $LANG_BAN00['save']) && !empty ($LANG_BAN00['save'])) {
    $display = ban_save(
        COM_applyFilter($_POST['id'], true),
        COM_applyFilter($_POST['type']),
        COM_applyFilter($_POST['status'], true),
        $_POST['data'],
        $_POST['note']);                     
} else {
    if (isset ($_REQUEST['msg'])) {
        $msg = COM_applyFilter($_REQUEST['msg'], true);
        if ($msg > 0) {
            $display = COM_showMessage ($msg, 'ban');
        }
    }
    
    $database_age = '';
    if ($_BAN_CONF['stopforumspam']) {
        $db_location = $_CONF['path'] . 'plugins/ban/files/' . $_BAN_CONF['stopforumspam_database_name'];  
        if (file_exists($db_location)) {
            $stats = stat($db_location);
            $database_age = $stats[9]; 
            if ($database_age > (time() - (86400 * $_BAN_CONF['stopforumspam_file_date']))) {
                // db file is less than stop forum spam old date
            } else {
                //$display .= COM_showMessage ($msg, 'ban');
                $display .= COM_showMessageText($LANG_BAN00['stopforumspam_note']);
            }
        }
    }        
    
    $display .= ban_list($database_age);
    
    $display = COM_createHTMLDocument($display, array('pagetitle' => $LANG_BAN00['ban_list']));
}

COM_output($display);

?>
