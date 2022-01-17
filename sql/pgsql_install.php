<?php

/* Reminder: always indent with 4 spaces (no tabs). */
// +---------------------------------------------------------------------------+
// | Ban Plugin 2.0.0                                                          |
// +---------------------------------------------------------------------------+
// | Installation SQL                                                          |
// +---------------------------------------------------------------------------+
// | Copyright (C) 2000-2010 by the following authors:                         |
// |                                                                           |
// | Authors: Tony Bibbs        - tony AT tonybibbs DOT com                    |
// |          Mark Limburg      - mlimburg AT users DOT sourceforge DOT net    |
// |          Jason Whittenburg - jwhitten AT securitygeeks DOT com            |
// |          Dirk Haun         - dirk AT haun-online DOT de                   |
// |          Trinity Bays      - trinity93 AT gmail DOT com                   |
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


$_SQL[] = "
CREATE TABLE {$_TABLES['ban']} (
  id serial,
  bantype varchar(40) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  created timestamp without time zone NOT NULL default now(),
  status int NOT NULL default 1,
  note varchar(255) NOT NULL default '',
  PRIMARY KEY  (id)
);
CREATE INDEX {$_TABLES['ban']}_bantype ON {$_TABLES['ban']}(bantype);
CREATE INDEX {$_TABLES['ban']}_status ON {$_TABLES['ban']}(status);
";

// Geeklog variable for date of last ban TTL check
$_SQL[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('ban_last_ttl_check','') ";

// Geeklog variable for date of last attempt at a Stop Forum Database download
$_SQL[] = "INSERT INTO {$_TABLES['vars']} (name, value) VALUES ('ban_last_sfsdownload','') ";
