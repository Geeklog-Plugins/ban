<?php

###############################################################################
# lang.php
# This is the English language page for the Geeklog Ban Plug-in!
#
# Copyright (C) 2003 Tom Willett
# twillett@users.sourceforge.net
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
#
###############################################################################

## $Id: 

###############################################################################
# Array Format: 
# $LANGXX[YY]:	$LANG - variable name
#		  	XX - file id number
#			YY - phrase id number
###############################################################################

/**
* General language
*/

$LANG_BAN00 = array (
    'ban'           => 'Ban',
    'type'          => 'Type',
    'data'          => 'Data',
    'id'            => 'ID',
    'status'        => 'Status',
    'created'       => 'Created',
    'ttl'           => 'Time To Live',
    'note'          => 'Note',
    'desc'          => 'Ban Bad IPs and Bots from your website.',
    'instructions'  => 'You can ban bots and people from you website.  You can ban by exact IP (use REMOTE_ADDR), exact referer (use HTTP_REFERER), exact user agent (use HTTP_USER_AGENT) or exact page (use SCRIPT_NAME).  To match patterns use case insensitive regular expressions (preg_match). With REMOTE_ADDR_RANGE you may also add IP address ranges, and with REMOTE_ADDR_CIDR use  CIDR notation or as simple from-to ranges. See the Ban Plugin <a href="' . $_CONF['site_admin_url'] . '/plugins/ban/readme.html" target="_blank">Read Me</a> for more information.<br' . XHTML . '><br' . XHTML . '>If you are using Auto Ban with the GUS plugin please make sure the plugin load order is set so the GUS plugin is loaded before the Ban plugin.',
    'instructions_sfs'              => '<br' . XHTML . '><br' . XHTML . '>The last time your Stop Forum Spam (SFS) database was updated, was on: %s',
    'instructions_sfs_size'         => '<br' . XHTML . '><br' . XHTML . '>Your current Stop Forum Spam (SFS) database size is %s KB and looks valid. Usually the file size is over 2000 KB.',
    'instructions_sfs_size_small'   => '<br' . XHTML . '><br' . XHTML . '><em>Warning</em>: Your current Stop Forum Spam (SFS) database size is %s KB. Usually the file size is over 2000 KB. You should confirm that the SFS database is valid.',
    'instructions_sfs_size_error'   => '<br' . XHTML . '><br' . XHTML . '><em>Warning</em>: Your current Stop Forum Spam (SFS) database size could not be determined. The file may be corrupt. It is recommended you download the SFS database again or disable this feature as valid IPs could be accidentally banned.',
    'instructions_sfs_db_missing'   => '<br' . XHTML . '><br' . XHTML . '><em>Warning</em>: Stop Forum Spam is enabled but the database file %s cannot be found on your server. Please either download the file or disable the Stop Forum Spam Ban config option.',
    'instructions_info' => '<br' . XHTML . '><br' . XHTML . '>For reference your browser information is:<br' . XHTML . '><br' . XHTML . '>HTTP_USER_AGENT: %s<br' . XHTML . '><br' . XHTML . '>REMOTE_ADDR: %s<br' . XHTML . '><br' . XHTML . '>HTTP_REFERER: %s<br' . XHTML . '><br' . XHTML . '>SCRIPT_NAME: %s<br' . XHTML . '><br' . XHTML . '>',
    'not_available' => 'Not Available',
    'save'          => 'Save',
    'cancel'        => 'Cancel',
    'delete'        => 'Delete',
    'edit'          => 'Edit',
    'ban_editor'    => 'Ban Editor',
    'ban_list'      => 'Ban List',
    'log_viewer'    => 'Log Viewer',
    'download_sfs'  => 'Download SFS Database',

    'status_normal'         => 'Normal',
    'status_ttl_short'      => 'TTL Short',
    'status_ttl_medium'     => 'TTL Medium',
    'status_ttl_long'       => 'TTL Long',
    'status_white'          => 'White',
    
    'ban_plugin_note'		=> 'Banned by Plugin %s. ', 
    
    'ban_invalid_logins_note'		=> 'Max number of invalid logins reached for user id: %s', // specify id since user cannot change that
    
    'gus_user_agent_note'   => "GUS User Agents exceeded ({$_BAN_CONF['gus_user_agent_num']}) for ip within the last {$_BAN_CONF['gus_user_agent_time']} seconds.",
    'gus_hits_note'         => "GUS Hits exceeded ({$_BAN_CONF['gus_hits_num']}) for ip within the last {$_BAN_CONF['gus_hits_time']} seconds.",
    'gus_referrer_note'     => "GUS referrer exceeded ({$_BAN_CONF['gus_referrer_num']}) matches for ip within the last {$_BAN_CONF['gus_referrer_time']} seconds.",
    'gus_url_note'          => "GUS page and query string requests exceeded ({$_BAN_CONF['gus_url_num']}) matches for ip within the last {$_BAN_CONF['gus_url_time']} seconds.",
    
    'stopforumspam_note'   => "Your Stop Forum Spam database is older than {$_BAN_CONF['stopforumspam_file_date']} days. Please download a new database at the <a href='http://www.stopforumspam.com/downloads/' target='_blank'>Stop Forum Spam website</a>. To automatically download it now, <a href='/admin/plugins/ban/index.php?mode=sfs_download'>click here</a>. Remember for automatic download to work you need to make sure that your website has write access for the SFS database file.",
    
    'error_editor_no_data'  => 'Your ban must have a type, status and data. The type and data combination must also not already exist in the database.',
    'access_denied'         => 'Access Denied',
	'access_denied_msg'     => 'Only Root Users or Ban Users have Access to this Page.  Your user name and IP have been recorded.'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_ban_MESSAGE1 = "Your ban has been saved successfully.";
$PLG_ban_MESSAGE2 = 'Your ban has been deleted successfully.';
$PLG_ban_MESSAGE3 = 'Your selected bans have been deleted.';
$PLG_ban_MESSAGE4 = 'Your Stop Forum Spam database has been updated.';
$PLG_ban_MESSAGE5 = 'There was a problem downloading or unzipping the Stop Forum Spam database. Depending on your settings you only have a minimum of 1 download attempt every 8 hours. Please check your sites error log file for more information.';

?>