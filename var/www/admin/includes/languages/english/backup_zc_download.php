<?php
/*
 ***********************************************************************
  $Id: backup_zc_download.php, v 1.1 2012/04/27

  ZenCart 1.5x
  Copyright 2003-2010 Zen Cart Development Team
  Portions Copyright 2004 osCommerce
  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

  Written By SkipWater <skip@ccssinc.net> 04.27.2012

 ***********************************************************************
*/


// the following are the language definitions

define('DID_NOT_RUN_SQL_TEXT', 'You did NOT Run MySQL BackUp ');
define('FILE_TO_DOWNLOAD_TEXT', 'File to be downloaded '); 
define('HEADING_DOWNLOAD', 'Download Backup Archive '); 
define('HEADING_TITLE', 'Backup Zen Cart');
define('DIR_FILES_EXCLUDED', 'Directory and Files excluded from archive: ');
define('SQL_FILE_TEXT', 'All SQL Files Only ');
define('SQL_FILE_NAME_TEXT', 'SQL File Name ');
define('SQL_FILE_LOCATION_TEXT', 'SQL file location ');
define('SQL_FILE_ARCHIVE_NOTE', '
			Your SQL File will be in your arcive backup file. Only if you did a complete backup, sql file backup or Complete Admin backup.<br />
			Please use FTP to download or delete it to free up server space. Thank You<br /><br />
			');
define('YOUR_ARCHIVE_CONTAIN_TEXT', 'Your archive will contain files from '); 
?>