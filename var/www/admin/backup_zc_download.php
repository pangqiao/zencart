<?php
/*
 ***********************************************************************
  $Id: backup_zc_download.php, v 1.1 2012/27/04

  ZenCart 1.5x
  Copyright 2003-2010 Zen Cart Development Team
  Portions Copyright 2004 osCommerce
  http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0

  Written By SkipWater <skip@ccssinc.net> 04.27.2012

 ***********************************************************************
 BackUp Zen Cart uses Devin Doucette archive class to handle all the backup files
 TAR/GZIP/BZIP2/ZIP ARCHIVE CLASSES 2.1
 By Devin Doucette
 Copyright (c) 2005 Devin Doucette
 Email: darksnoopy@shaw.ca

 ***********************************************************************
 BackUp Zen Cart uses Luis Arturo Aguilar Mendoza class to handle all the MySQL backups
 MySQL DB backup class, version 1.0.1
 written by Luis Arturo Aguilar Mendoza <minibikec@prodigy.net.mx>
 Released under GNU Public license

 ***********************************************************************
*/

  require('includes/application_top.php');
    // Check for configure file if exists
	if (!file_exists('backup_zconfig.php')) {
		exit('<h2>ERROR: FILE NOT FOUND backup_zconfig.php file.<br />Check That YOU have Uploaded the file.<br /> And its location must be in the same directory as this file.</h2>');
	}
  require_once('backup_zconfig.php');

  $exclude_files = $_SESSION['exclude_files']; // loaded from backup_zc

  $sqlfile = $_SESSION['sqlfile'];

// archive file type
  switch ($_SESSION['file_type']) {
      case '0':
        $archive_type = 'zip_file';
        $file_ext = '.zip';
      break;

      case '1':
        $archive_type = 'gzip_file';
        $file_ext = '.tgz';
      break;

      case '2':
        $archive_type = 'tar_file';
        $file_ext = '.tar';
      break;

  }

// What is being backup
  switch ($_SESSION['bulevel']) {
      case 'all':
        $add_zcs_backup = '../*.*';
      break;

      case 'root':
        $add_zcs_backup='../*.*';
        $recurse_dir = 0;
        $file_name = $_SERVER['HTTP_HOST'].'-ROOTFILES-'.date("mdY-Hi"); // Rename File Name
      break;
      
      case 'sql':
        $add_zcs_backup='../*.sql';
        $file_name = $_SERVER['HTTP_HOST'].'-SQLFILES-'.date("mdY-Hi"); // Rename File Name
      break;

   	default:
   	    $add_zcs_backup = '../'.substr($_SESSION['bulevel'], $zc_directory_base_count).'/*.*'; 
   	    // User selected a directory to backup add directory name to file name.
   	    $file_name = $_SERVER['HTTP_HOST'].'-'.substr($_SESSION['bulevel'], $zc_directory_base_count).'-'.date("mdY-Hi"); // Rename File Name

  }

if ((isset($_POST['download']) ? $_POST['download'] : '')){ 

		// Create a SQL backup file
        if ($sqlfile != 0) {
        	set_time_limit(0); // set run for ever
        	$backup_obj = new MySQL_DB_Backup();
			$backup_obj->server = DB_SERVER;
			$backup_obj->port = 3306;
			$backup_obj->username = DB_SERVER_USERNAME;
			$backup_obj->password = DB_SERVER_PASSWORD;
			$backup_obj->database = DB_DATABASE;
			// Tables you wish to backup. All tables in the database will be backed up if this array is null.
			$backup_obj->tables = array();
			//Add DROP TABLE IF EXISTS queries before CREATE TABLE in backup file.
			$backup_obj->drop_tables = true;
			//No table structure will be backed up if false
			$backup_obj->create_tables = true;
			//Only structure of the tables will be backed up if true.
			$backup_obj->struct_only = false;
			//Add LOCK TABLES before data backup and UNLOCK TABLES after
			$backup_obj->locks = false;
			//Include comments in backup file if true.
			$backup_obj->comments = $sql_comments; // set in backup_zconfig.php
			//Directory on the server where the backup file will be placed. Used only if task parameter equals MSX_SAVE.
			$backup_obj->backup_dir = $sql_path; // set in backup_zconfig.php
			//Default file name format.
			$backup_obj->fname_format = 'm_d_Y';
			//Values you want to be intrerpreted as NULL
			$backup_obj->null_values = array( '0000-00-00', '00:00:00', '0000-00-00 00:00:00');
			//Set max_allowed packet value to 64 MB 
			// Increase to 128 MB if getting error 2006 MySQL server has gone away
			$backup_obj->max_packet = 256 * 1024 * 1024; // 256 MB
			
			/*
				Task: 
					MSX_STRING - Return SQL commands as a single output string.
					MSX_SAVE - Create the backup file on the server.
					MSX_DOWNLOAD - Download backup file to the user's computer.
					MSX_APPEND - Append the backup to the file on the server.
			*/

			$task = MSX_SAVE ;
			
			//Optional name of backup file if using 'MSX_APPEND', 'MSX_SAVE' or 'MSX_DOWNLOAD'. If nothing is passed, the default file name format will be used.
			$filename = $sql_file_name; // set in backup_zconfig.php
			
			//Use GZip compression if using 'MSX_APPEND', 'MSX_SAVE' or 'MSX_DOWNLOAD'?
			$use_gzip = $sql_gzip; // set in backup_zconfig.php

            $result_bk = $backup_obj->Execute($task, $filename, $use_gzip);
		}

		// Change to wild cards for archive class
		$zcwildir = "../";
		$zcwildfile = "/*.*";
		$zcexclude_files = str_replace(DIR_FS_CATALOG,$zcwildir,$exclude_files);
		$exclude_files = substr_replace($zcexclude_files,'/*.*',20,0);
		
		/* Debug	  
	  	print_r($zcexclude_files);
	  	echo '<br />';
	  	print_r($exclude_files);
	  	die();
		*/

		set_time_limit(0); // set run for ever

		// call class and run backup
		$zcsite = new $archive_type($file_location.$file_name.$file_ext);
		$zcsite->set_options(array('basedir' => ".", 'overwrite' => 1, 'level' => $compress_level));
		$zcsite->set_options(array('inmemory' => 1, 'recurse' => $recurse_dir, 'storepaths' => 1));
		$zcsite->add_files(array($add_zcs_backup));
		$zcsite->exclude_files($exclude_files);
		$zcsite->create_archive();
		$zcsite->download_file(); // download do not store

		// Clear sessions we are done
		unset ($_SESSION['file_type']);
        unset ($_SESSION['sqlfile']);
        unset ($_SESSION['org']);
		unset ($_SESSION['download']);
		unset ($_SESSION['bulevel']);
		unset ($_SESSION['bulocation']);
		unset ($_SESSION['exclude_files']);

}

/* debug
print_r ($_SESSION);
echo 'post_max_size = ' . ini_get('post_max_size') . "\n";
*/
?>
<!doctype html public "-//W3C//DTD HTML 4.01 Transitional//EN">
<html <?php echo HTML_PARAMS; ?>>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo CHARSET; ?>">
<title><?php echo TITLE; ?></title>
<link rel="stylesheet" type="text/css" href="includes/stylesheet.css">
<link rel="stylesheet" type="text/css" href="includes/cssjsmenuhover.css" media="all" id="hoverJS">
<script language="javascript" src="includes/menu.js"></script>
<script language="javascript" src="includes/general.js"></script>
<script type="text/javascript">
  <!--
  function init()
  {
    cssjsmenu('navbar');
    if (document.getElementById)
    {
      var kill = document.getElementById('hoverJS');
      kill.disabled = true;
    }
  }
  // -->
</script>
</head>
<body onLoad="init()">
<!-- header //-->
<?php require(DIR_WS_INCLUDES . 'header.php'); 

?>
<!-- header_eof //-->

<!-- body //-->
<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td><form name="zc_site_download" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input name="download" type="hidden" value="1" />

<?php
         // Debug print_r($_SESSION);
         echo '<br><hr>';
         echo '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">';
        // echo BACKUP_COMPLETE_TEXT.sprintf("%.4f", ($end-$start)). SECONDS_TEXT. '</td><td>';
         echo '<h1>'.HEADING_DOWNLOAD.'</h1>'. TEXT_ZCS_BACKUP_VERSION .'<br /></td><td>';
            if ($sqlfile !=1) {
            	echo DID_NOT_RUN_SQL_TEXT.'<br /><br />';
            } else {
				echo SQL_FILE_NAME_TEXT.'<b>' . $sql_file_name . '</b><br />' ;
				echo SQL_FILE_LOCATION_TEXT.'<b>' . $sql_path . '</b><br />';
				echo SQL_FILE_ARCHIVE_NOTE;
            }
            	echo FILE_TO_DOWNLOAD_TEXT.'<b>'. $file_name.$file_ext . '</b><br /><br />';
  				echo YOUR_ARCHIVE_CONTAIN_TEXT.'<b>'. $add_zcs_backup . '</b><br /><br />';
  				echo DIR_FILES_EXCLUDED.'<b>'. implode(",",$exclude_files) . '</b><br /><br />';
  				// echo FILE_TO_DOWNLOAD_NOTE_TEXT;

  		echo '</td><td align="right">';
  		echo  zen_image_submit('button_download_now.gif', 'Download') .'<br /><br />'; 
  		echo '<a href="' . zen_href_link(FILENAME_BACKUP_ZC). '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>' ;
  		echo '</td></tr></table>';
  		echo '<hr><br>';
?>
        </td>
      </tr>
   </table>

<!-- body_text_eof //-->
  </tr>
</table>
<!-- body_eof //-->

<!-- footer //-->
<?php require(DIR_WS_INCLUDES . 'footer.php'); ?>
<!-- footer_eof //-->
<br />
</body>
</html>
<?php require(DIR_WS_INCLUDES . 'application_bottom.php'); ?>