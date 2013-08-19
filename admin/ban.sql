CREATE TABLE gl_ban (
  bantype varchar(40) NOT NULL default '',
  data varchar(255) NOT NULL default '',
  KEY bantype (bantype)
) TYPE=MyISAM;
