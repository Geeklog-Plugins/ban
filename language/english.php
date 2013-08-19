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
    'type'          => 'Ban Type',
    'data'          => 'Data',
    'desc'          => 'Ban Bad IPs and Bots from your website.',
    'info'          => 'You can ban and bots people from you website.  You can ban then by IP (use REMOTE_ADDR), referer (use HTTP_REFERER), user agent (use HTTP_USER_AGENT) or page (use SCRIPT_NAME).  Match patterns use case insensitive regular expressions (eregi). See <a href="' . $_CONF['site_admin_url'] . '/plugins/ban/readme.html">Readme</a><br><br>Here is what you have banned now.',
    'add'           => 'Add Ban',
    'delete'        => 'Delete Ban',
    'access_denied'     => 'Access Denied',
	'access_denied_msg' => 'Only Root Users or Ban Users have Access to this Page.  Your user name and IP have been recorded.',
	'install_success'	=> 'Installation Successful',
	'install_failed'	=> 'Installation Failed -- See your error log to find out why.',
	'install'           => 'Install',
	'uninstall'         => 'UnInstall',
    'installmsg3'       => 'STOP! Before you press install please read '
);

?>