<?php
/*
 ***********************************************************************
  $Id: backup_zconfig.php, v 1.1 2012/04/27

  ZenCart 1.5x
  Copyright 2003-2010 Zen Cart Development Team
  Portions Copyright 2004 osCommerce
  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

  Written By SkipWater <skip@ccssinc.net> 04.27.2012

 ***********************************************************************
*/  
  require(DIR_WS_CLASSES . 'archive.php'); // Load Archive class file
  require(DIR_WS_CLASSES . 'mysql_db_backup.class.php'); // Load MySQL DB Backup class file
  define(TEXT_ZCS_BACKUP_VERSION,'Version 1.1');
  define(FILENAME_ZC_DOWNLOAD, 'backup_zc_download.php');
  $zc_directory = DIR_FS_CATALOG; // Used for zen cart site size
  $zc_directory_base_count = strlen(DIR_FS_CATALOG);

  // ***********************************************************************
  // Grab a large chunk of memory
  // If your server allows this adjustment then adjust to meet your needs.
  // ***********************************************************************
  // ini_set("memory_limit","128M");
  // ini_set("memory_limit","192M");
  // ini_set("memory_limit","256M"); 
     ini_set("memory_limit","512M"); // default
  // ini_set("memory_limit","768M");
  // ini_set("memory_limit","1000M");

  // *********************************************************************** 
  // Note each directroy or file name must be surrounded with double quotes 
  // and a single comma with no space separating each.
  // DIR_FS_CATALOG = Zen Carts root files system path you must use this and 
  // just add the directory you want excluded.
  // Rember that directories names are case sensitive!
  // ***********************************************************************
  /* Sample Exclude
   $exclude_files = array("".DIR_FS_CATALOG."cache",
  						  "".DIR_FS_CATALOG."pub",
  						  "".DIR_FS_CATALOG."admin",
  						  "".DIR_FS_CATALOG."download"
  						  );

  */
  // ***********************************************************************
  // Set default exclude directories
  // ***********************************************************************
  $exclude_files = array("".DIR_FS_CATALOG."cache",
  						 "".DIR_FS_CATALOG."extras",
  						 "".DIR_FS_CATALOG."pub"
  						 );

  // ***********************************************************************
  // Default Setting
  // These are the settings that are used when you first request backup zc
  // You can edit them if you understand them.
  // ***********************************************************************
  $file_name = $_SERVER['HTTP_HOST'].'_'.date("mdY-Hi"); // Create File Name
  $file_ext = '.zip'; // Zip File Extention default
  $archive_type = 'zip_file'; // Set as default
  $file_location = DIR_FS_SQL_CACHE."/"; // Set cache directory as default where the arcive will be
  $base_directory = '.'; // Set ZC root as default
  $compress_level = 5; // 1-9 1 lowest 9 highest only works with zip file
  $recurse_dir = 1; // 1 = true 0 = false
  $backup_file_type = '0'; // 0 = zip 1 = gzip 2 = tar
  $download_backup = 0; // 1 = true 0 = false

  // ***********************************************************************
  // SQL Data Base info
  // ***********************************************************************
  $sql_path = DIR_FS_ADMIN.'backups/'; // path to SQL file default
  $sql_gzip = false; //Use GZip compression true or false if set true 7zip is needed to uncompress
  $sql_comments = true; //Add comments to the sql file
  $sql_file_name = $_SERVER['HTTP_HOST'].'_'.date("mdY-Hi").'.sql'; // Create SQL File Name
  
  // Do not edit below
  if ($sql_gzip == true) {
	$sql_file_name = $_SERVER['HTTP_HOST'].'_'.date("mdY-Hi").'_sql.zip';
  }
?>