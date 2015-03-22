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
    'instructions'  => 'You can ban bots and people from you website.  You can ban by IP (use REMOTE_ADDR), referer (use HTTP_REFERER), user agent (use HTTP_USER_AGENT) or page (use SCRIPT_NAME).  To match patterns use case insensitive regular expressions (preg_match). With REMOTE_ADDR you may also add IP address ranges, either in CIDR notation or as simple from-to ranges. See the Ban Plugin <a href="' . $_CONF['site_admin_url'] . '/plugins/ban/readme.html">Read Me</a> for more information.<br><br>Here is what you have banned now:',
    'save'          => 'Save',
    'cancel'        => 'Cancel',
    'delete'        => 'Delete',
    'edit'          => 'Edit',
    'ban_editor'    => 'Ban Editor',
    'ban_list'      => 'Ban List',
    'log_viewer'    => 'Log Viewer',

    'status_normal'         => 'Normal',
    'status_ttl_short'      => 'TTL Short',
    'status_ttl_medium'     => 'TTL Medium',
    'status_ttl_long'       => 'TTL Long',
    'status_white'          => 'White',
    
    'ban_plugin_note'		=> 'Banned by Plugin %s. ', 
    
    'gus_user_agent_note'   => "GUS User Agents exceeded ({$_BAN_CONF['gus_user_agent_num']}) for ip within the last {$_BAN_CONF['gus_user_agent_time']} seconds.",
    'gus_hits_note'         => "GUS Hits exceeded ({$_BAN_CONF['gus_hits_num']}) for ip within the last {$_BAN_CONF['gus_hits_time']} seconds.",
    'gus_referrer_note'     => "GUS referrer exceeded ({$_BAN_CONF['gus_referrer_num']}) matches for ip within the last {$_BAN_CONF['gus_referrer_time']} seconds.",
    'gus_url_note'          => "GUS page and query string requests exceeded ({$_BAN_CONF['gus_url_num']}) matches for ip within the last {$_BAN_CONF['gus_url_time']} seconds.",
    
    'stopforumspam_note'   => "Your Stop Forum Spam database is older than {$_BAN_CONF['stopforumspam_file_date']} days. Please download a new database at the <a href='http://www.stopforumspam.com/downloads/' target='_blank'>Stop Forum Spam</a> website.",
    
    'error_editor_no_data'  => 'Your ban must have a type, status and data.',
    'access_denied'         => 'Access Denied',
	'access_denied_msg'     => 'Only Root Users or Ban Users have Access to this Page.  Your user name and IP have been recorded.'
);

###############################################################################
# Messages for COM_showMessage the submission form

$PLG_ban_MESSAGE1 = "Your ban has been saved successfully.";
$PLG_ban_MESSAGE2 = 'Your ban has been deleted successfully.';
$PLG_ban_MESSAGE3 = 'Your selected bans have been deleted.';

?>