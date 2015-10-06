<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Ban Plugin 2.0                                                            |
// +---------------------------------------------------------------------------+
// | mysql_updates.php                                                         |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2008-2011 by the following authors:                         |
// |                                                                           |
// | Authors: Dirk Haun         - dirk AT haun-online DOT de                   |
// +---------------------------------------------------------------------------+
// |                                                                           |
// | This program is licensed under the terms of the GNU General Public License|
// | as published by the Free Software Foundation; either version 2            |
// | of the License, or (at your option) any later version.                    |
// |                                                                           |
// | This program is distributed in the hope that it will be useful,           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of            |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.                      |
// | See the GNU General Public License for more details.                      |
// |                                                                           |
// | You should have received a copy of the GNU General Public License         |
// | along with this program; if not, write to the Free Software Foundation,   |
// | Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.           |
// |                                                                           |
// +---------------------------------------------------------------------------+

/**
* MySQL updates
*
* @package BAN
*/

$_UPDATES = array(

    '1.0.3' => array(
        "ALTER TABLE `{$_TABLES['ban']}` ADD `id` int(11) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST",
        "ALTER TABLE `{$_TABLES['ban']}` ADD `created` timestamp NOT NULL default CURRENT_TIMESTAMP AFTER `data`",
        "ALTER TABLE `{$_TABLES['ban']}` ADD `status` int(11) NOT NULL default '1' AFTER `created`",
        "ALTER TABLE `{$_TABLES['ban']}` ADD `note` varchar(255) NOT NULL default '' AFTER `status`",
        "ALTER TABLE `{$_TABLES['ban']}` ADD INDEX (status);",
        "UPDATE {$_TABLES['plugins']} SET pi_gl_version = '1.8.0', pi_homepage = 'http://code.google.com/p/geeklog/'  WHERE pi_name = 'ban'", // Update plugin requirements
        "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('ban_last_ttl_check','');", // New Geeklog variable for date of last ban TTL check
        "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('ban_last_sfsdownload','');" // New Geeklog variable for date of last attempt at a Stop Forum Database download
    )
    
);

/*
function ban_update_ConfValues_1_0_3()
{
    global $_CONF, $_PO_DEFAULT;

    require_once $_CONF['path_system'] . 'classes/config.class.php';
    require_once $_CONF['path'] . 'plugins/polls/install_defaults.php';

    $c = config::get_instance();
    
    // meta tag config options.
    $c->add('meta_tags', $_PO_DEFAULT['meta_tags'], 'select', 0, 0, 0, 100, true, 'polls');

    return true;
}

function ban_update_ConfigSecurity_1_0_3()
{
    global $_TABLES;
    
    // Add in security rights for Polls Admin
    $group_id = DB_getItem($_TABLES['groups'], 'grp_id',
                            "grp_name = 'Polls Admin'");

    if ($group_id > 0) {
        $ft_id = DB_getItem($_TABLES['features'], 'ft_id', "ft_name = 'config.polls.tab_poll_block'");   
        $sql = "INSERT INTO {$_TABLES['access']} (acc_ft_id, acc_grp_id) VALUES ($ft_id, $group_id)";
        DB_query($sql);    
    }    

}
*/

?>
