<?php
/*
 ***********************************************************************
  $Id: backup_zc.php, v 1.1 2012/04/27

  ZenCart 1.5x
  Copyright 2003-2010 Zen Cart Development Team
  Portions Copyright 2004 osCommerce
  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

  Written By SkipWater <skip@ccssinc.net> 04.27.2012

 ***********************************************************************
*/


// the following are the language definitions
define('BACKUP_COMPLETE_TEXT', '<h1>Backup Completed</h1> Archive was created in ');
define('BACKUP_DOWNLOAD', 'Backup file location will be downloaded <b>NOT SAVED ON SERVER</b>');
define('BACKUP_FILE_NAME_TEXT', 'Backup File Name: ');
define('BACKUP_LOCATION', 'Backup file location: ');
define('BACKUP_TEXT', 'BackUp ');
define('BACKUP_EXCLUDED', 'Excluded ');

define('COMPLETE_TEXT', 'Complete Site ');
define('CREATE_SQL_BACKUP', 'Create a SQL BackUp File From <b>'.DB_DATABASE.'</b> Database: ');

define('DEFAULT_SETTINGS', 'Default Settings');
define('DEFAULT_TEXT', ' (Defualt) ');
define('DID_NOT_RUN_SQL_TEXT', 'You did NOT Run MySQL BackUp ');
define('DOWNLOAD_ARCHIVE_TEXT', 'Download Archive:');
define('DIR_FILES_EXCLUDED', 'Directory and Files excluded from archive: ');

define('ERROR_PHP_VERSION','<b>WARNING:</b> You should upgrade to PHP Version 5 or higher. ');
define('ERROR_SQL_FOLDER','<b>WARNING:</b> You must make Writeable ');
define('ERROR_ARCHIVE_FOLDER','<b>WARNING:</b> You must make Writeable ');
define('ERROR_EXCLUDE_FOLDER1','<b>WARNING:</b> You have entered <b>');
define('ERROR_EXCLUDE_FOLDER2','</b> in backup_zconfig.php as a EXCLUDED directory BUT it DOES NOT EXIST on your system.');

define('FILE_NAME_TEXT', '- File Name: ');
define('FILE_TYPE_ZIP', 'File type Zip ');
define('FILE_TYPE_GZIP', 'File type Gzip ');
define('FILE_TYPE_TAR', 'File type Tar ');
define('FTP_NOTE_TEXT', 'Please use FTP to download it to free up server space. Thank You<br />');

define('HEADING_FILES', 'Files Only ');
define('HEADING_OPTIONS', 'Options');
define('HEADING_NOTES', 'Notes: ');
define('HEADING_TITLE', 'Backup Zen Cart');

define('MEMORY_MESSAGE_TEXT', '<b>Due to memory limits on some servers save the archive to the server. (Default)</b><br />or do one directory download at a time.');

define('NO_TEXT', 'No ');
define('NOT_ARCHIVED_TEXT', 'This directory is not archived so any files in it will NOT be added. ');
define('ONLY_ONCE', 'You need to select this ONLY once per backup session. ');

define('ROOT_FILE_TEXT', 'Web Site Root Files Only ');

define('SAVE_TO_TEXT', 'Save BackUp File To: ');
define('SECONDS_TEXT', ' seconds. ');
define('SQL_FILE_TEXT', 'All SQL Files Only ');
define('SQL_FILE_NAME_TEXT', 'SQL File Name ');
define('SQL_FILE_LOCATION_TEXT', 'SQL file location: ');
define('SQL_FILE_ARCHIVE_NOTE', '
			Your SQL File will be in your arcive backup file. Only if you did a complete backup, sql file backup or Complete Admin backup.<br />
			Please use FTP to download or delete it to free up server space. Thank You<br /><br />
			');

define('TEXT_NOTES', '
			 The options to the right allow you to select what you want to archive.<br /><br />
			 Backup MySQL database will export your Zen Cart database to a SQL file.<br />
			 When completed BackUp ZC will continue to create a archive file of your selected options. The SQL file will only be added to the archived if you selected Complete Site, All SQL Files or Your Admin Directory.<br /><br />
			 The Highlighted Excluded Directory listing to the right are set in backup_zconfig.php file.<br /><br />
			 If you select a directory to backup the directory name will be added to the archives file name. This will allow you to idenify the files contents easily.<br /><br />
			 Selecting \'Yes\' in the Download Archive dropdown you will get a new window to download your archive.<br /><br />
			 If you get a blank page php memory has been over used. To fix you can edit backup_zconfig.php in your admin folder and raise the memory usage.<br /> <br />
			 <b>Please be patient scanning your Zen Cart directory structure.</b><br />
			 ');

define('YES_TEXT', 'Yes ');
define('ZIP_GZIP_COMPRESSION', 'Compression level Zip or GZip: ');
//
?>