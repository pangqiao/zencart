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

    // None user settings
    $zc_dirlist = getFileList(DIR_FS_CATALOG, true, 1); // true
    $sqlfile = 0; // 1 = true 0 = false
    
    // Test php version
    if (phpversion() < '5') {
		$error = true;
		$messageStack->add(ERROR_PHP_VERSION, 'caution');
	}

    // Test if sql path writable
    if (!is_writable($sql_path)) {
		$error = true;
		$messageStack->add(ERROR_SQL_FOLDER . '<b>' . $sql_path .'</b>', 'caution');
	}

	// Test if archive path writable
    if (!is_writable($file_location)) {
		$error = true;
		$messageStack->add(ERROR_ARCHIVE_FOLDER . '<b>' . $file_location .'</b>', 'caution');
	}

	// Test if the excluded dir are here
    $dirPaths = $exclude_files;
	foreach($dirPaths as $path){
	   if (!is_dir($path)) {
		$error = true;
		$messageStack->add(ERROR_EXCLUDE_FOLDER1 . $path . ERROR_EXCLUDE_FOLDER2, 'caution');
	  }
	}

	// Get the filesize of a directory and all files and subdirectories
	function directorySize($zc_directory) {
    	
    	$zc_size = 0;
 
     	if(substr($zc_directory,-1) == '/')	{
        	$zc_directory = substr($zc_directory,0,-1);
    	}
 
    	if(!file_exists($zc_directory) || !is_dir($zc_directory) || !is_readable($zc_directory)) {
        	return -1;
    	}

    	if($handle = opendir($zc_directory)) {
        	while(($file = readdir($handle)) !== false)
        	{
            	$path = $zc_directory.'/'.$file;
            	if($file != '.' && $file != '..') {
                	if(is_file($path)) {
                    	$zc_size += filesize($path);
                	} elseif(is_dir($path)) {
                    	$handlesize = directorySize($path);
                    	if($handlesize >= 0) {
                        	$zc_size += $handlesize;
                    	} else {
                        	return -1;
                    	}
                	}
            	}
        	}
        	// close the directory
        	closedir($handle);
    	}
        return $zc_size;
	}

	// Get the list of directories
 	function getFileList($dir, $recurse=false, $depth=false) { 
 
 		$retval = array(); 

  		// open pointer to directory and read list of files only using directories
 		$d = @dir($dir) or die("getFileList: Failed opening directory $dir for reading"); 
 		
 		while(false !== ($entry = $d->read())) { 
 
 			if($entry[0] == ".") continue; 
 			
 			if(is_dir("$dir$entry")) { 
 			$retval[] = array(
  				"name" => "$dir$entry",
  				"type" => filetype("$dir$entry"), 
  				"size" => 0, 
  				"lastmod" => filemtime("$dir$entry") 
  			); 
     	} 
     	
     } 
     $d->close();
     return $retval;
	}
	
	// Make Files Size Look Nice
 	function formatSize($size) {
    	switch (true){
    		case ($size > 1099511627776):
        		$size /= 1099511627776;
        		$suffix = ' TB';
    		break;
    		case ($size > 1073741824):
        		$size /= 1073741824;
        		$suffix = ' GB';
    		break;
    		case ($size > 1048576):
        		$size /= 1048576;
        		$suffix = ' MB';   
    		break;
    		case ($size > 1024):
        		$size /= 1024;
        		$suffix = ' KB';
        	break;
    	default:
        	$suffix = ' B';
    }
    return round($size, 2).$suffix;
}

/* Debug
echo 'Sites Size '.directorySize($zc_directory).' Bytes<br />';

  if (directorySize($zc_directory) > 536870912) {
    $messageStack->add('SITE IS TO LARGE', 'warning');
  }
*/
if ((isset($_POST['org']) ? $_POST['org'] : '')){

// Start Time
$start = (float) array_sum(explode(' ',microtime())); 

// Download or store archive
$download = (isset($_POST['download']) ? $_POST['download'] : '');
        if ($download != 0){
			$download_backup = $download;

			// Reset if user came back and runs again
			unset ($_SESSION['file_type']);
        	unset ($_SESSION['sqlfile']);
        	unset ($_SESSION['org']);
			unset ($_SESSION['download']);
			unset ($_SESSION['bulevel']);
			unset ($_SESSION['bulocation']);
			unset ($_SESSION['exclude_files']);

			$_POST['exclude_files']=$exclude_files; // Pass on the exclude file list
			$_SESSION = array_merge($_SESSION, $_POST);
			header('Location: '. zen_href_link(FILENAME_ZC_DOWNLOAD,'','SSL') .''); // Call download with SSL if set
			exit();
}

// Create a SQL backup file
$sqlfile = (isset($_POST['sqlfile']) ? $_POST['sqlfile'] : '');
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

// archive file type
  switch ($_POST['file_type']) {
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
  switch ($_POST['bulevel']) {
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
   	    $add_zcs_backup = '../'.substr($_POST['bulevel'], $zc_directory_base_count).'/*.*';
   	    // User selected a directory to backup add directory name to file name.
   	    $file_name = $_SERVER['HTTP_HOST'].'-'.substr($_POST['bulevel'], $zc_directory_base_count).'-'.date("mdY-Hi"); // Rename File Name

  }

 /* Debug
  echo 'Archive Type '.$archive_type;
  echo '<br />';
  echo 'File ext '.$file_ext;
  echo '<br />';
  echo 'Start mem '.memory_get_usage();
  echo '<br />';
  echo 'What we are backing Up '.$add_zcs_backup;
  echo '<br />';
  echo 'file location - file name - file ext '.$file_location.$file_name.$file_ext;
  echo '<br />';
  echo 'Download Yes/No '.$download_backup;
  echo '<br />';
  echo 'Exclude Files '.$exclude_files;
  echo '<br />';
  echo $compress_level;
  die();
*/

	// Change to wild cards for archive class
	$zcwildir = "../";
	$zcwildfile = "/*.*";
	$zcexclude_files = str_replace(DIR_FS_CATALOG,$zcwildir,$exclude_files);
	$exclude_files = substr_replace($zcexclude_files,'/*.*',20,0);

	set_time_limit(0); // set run for ever

	// call class and run backup
	$zcsite = new $archive_type($file_location.$file_name.$file_ext);
	$zcsite->set_options(array('basedir' => ".", 'overwrite' => 1, 'level' => $compress_level));
	$zcsite->set_options(array('inmemory' => $download_backup, 'recurse' => $recurse_dir, 'storepaths' => 1));
	$zcsite->add_files(array($add_zcs_backup));
	$zcsite->exclude_files($exclude_files); //$exclude_files

	$zcsite->create_archive();
	
	if ($download_backup == 1){
		$zcsite->download_file(); // download do not store
	}

/* debug
	echo 'End mem '.memory_get_usage().'<br />';
	echo 'Peek mem '.memory_get_peak_usage().'<br />';
*/

// End Time
$end = (float) array_sum(explode(' ',microtime()));
}


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
<?php require(DIR_WS_INCLUDES . 'header.php'); ?>
<!-- header_eof //-->

<!-- body //-->

<table border="0" width="100%" cellspacing="2" cellpadding="2">
  <tr>
<!-- body_text //-->
    <td width="100%" valign="top">
    <table border="0" width="100%" cellspacing="0" cellpadding="2">
      <tr>
        <td>
        
<?php
/* debug
print_r ($_SESSION);
echo 'post_max_size = ' . ini_get('post_max_size') . "\n";
*/

if (!(isset($_POST['org']) ? $_POST['org'] : '')){
?>
      <form name="zc_site_backup" method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
 
      <table width="100%">
            <tr>
            <td class="main" width="40%" valign="top"><?php echo '<h1>'.HEADING_TITLE.'</h1>' . TEXT_ZCS_BACKUP_VERSION .'<br />'; ?>

         <?php 
            echo '<b>'.DEFAULT_SETTINGS.'</b><br />';
            if ($backup_file_type == 0)
                echo FILE_TYPE_ZIP.FILE_NAME_TEXT .'<b>'. $file_name.$file_ext . '</b><br />';
            if ($backup_file_type == 1)
            	echo FILE_TYPE_GZIP.FILE_NAME_TEXT. '<b>'. $file_name.$file_ext . '</b><br />';
            if ($backup_file_type == 2)
            	echo FILE_TYPE_TAR.FILE_NAME_TEXT. '<b>'. $file_name.$file_ext . '</b><br />';
            if ($download_backup !=1) {
  				echo BACKUP_LOCATION. '<b>'. $file_location . '</b><br />';
  			} else {
  				echo BACKUP_DOWNLOAD.'<br />';
  			}
			echo SQL_FILE_LOCATION_TEXT . '<b>'. $sql_path . '</b><br />'; 
  			echo ZIP_GZIP_COMPRESSION. '<b>'. $compress_level . '</b><br />';
  		    echo DIR_FILES_EXCLUDED.'<b>'. implode(",",$exclude_files) . '</b><br />';
          ?>
            <br />

            <h1><?php echo HEADING_NOTES; ?></h1>
            <?php echo TEXT_NOTES; ?>
            </td>
		 	<td class="main" width="50%" align="right">
		 		<h1><?php echo HEADING_OPTIONS; ?></h1>
		 		<input name="org" type="hidden" value="1" />
		 		<?php echo ONLY_ONCE; ?><br />
		 		<?php echo CREATE_SQL_BACKUP; ?>
		 				 	<select name="sqlfile" >
    						<option value="0">No</option>
    						<option value="1">Yes</option>
    						</select>
    						<br/>
    			<hr>
		 		<?php echo FILE_TYPE_ZIP.DEFAULT_TEXT; ?> <input type="radio" name="file_type" value="0" checked><br/>
         		<?php echo FILE_TYPE_GZIP; ?>: <input type="radio" name="file_type" value="1"><br/>
         		<?php echo FILE_TYPE_TAR; ?>: <input type="radio" name="file_type" value="2"><br/>
         		<hr>
    <?php
    echo "<table>";
	$zc_total=0;
	echo 'Directories Loaded ...';

	foreach($zc_dirlist as $file) 
	{
		$exclude_from_list = array_diff($file,$exclude_files);
 
		if ($file['name'] == $exclude_from_list['name']){
		  if (function_exists('ob_flush')) @ob_flush();{
			echo '<tr><td>'.BACKUP_TEXT.$file['name'].'</td><td>'. formatSize(directorySize($file['name'])).'</td><td><input type="radio" name="bulevel" value="'.$file['name'].'"></td></tr>'; 
		  	echo (str_repeat('.',2));
		  // Send output to browser
		  @flush();
		  }
			$zc_total = directorySize($file['name'])+$zc_total;
		} else {
		  if (function_exists('ob_flush')) @ob_flush();{
			echo '<tr><td><b>'.BACKUP_EXCLUDED.$file['name'].'</b></td><td><i>'. formatSize(directorySize($file['name'])).'</i></td><td><b>XX</b></td></tr>'; 
		  // Send output to browser
		  @flush();
		  }
		}
	}

	echo '<tr><td>'.BACKUP_TEXT.DIR_FS_CATALOG.COMPLETE_TEXT.DEFAULT_TEXT.'</td><td><strong>'.formatSize($zc_total).'</strong></td><td><input type="radio" name="bulevel" value="all" checked></td></tr>';
	echo '<tr><td colspan="3"><b>'.HEADING_FILES.'</b><hr></td></tr>';
	echo '<tr><td>'.BACKUP_TEXT.SQL_FILE_TEXT.'</td><td>....</td><td><input type="radio" name="bulevel" value="sql"></td></tr>';
	echo '<tr><td>'.BACKUP_TEXT.ROOT_FILE_TEXT.'</td><td>....</td><td><input type="radio" name="bulevel" value="root"></td></tr>';
	echo "</table>";

	?>
				<br />
				<hr>
         		<?php echo MEMORY_MESSAGE_TEXT; ?><br />
         		
         		<?php echo DOWNLOAD_ARCHIVE_TEXT; ?> 	
         							<select name="download" >
    								<option value="0"><?php echo NO_TEXT; ?></option>
    								<option value="1"><?php echo YES_TEXT; ?></option>
    								</select>
    								<br/><br/><hr>

         	</td>
            <td align="center" valign="bottom">

            <?php 
             echo (zen_image_submit('button_backup.gif', 'Backup') ); 
            ?>
            </td>
         </tr>
        </form>   
      </table>
<?php
}
?>
        </td>
      </tr>
      <tr>
        <td>
<?php
         if ((isset($_POST['org']) ? $_POST['org'] : '')){

         echo '<br><hr>';
         echo '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td align="center">';
         echo BACKUP_COMPLETE_TEXT.sprintf("%.4f", ($end-$start)). SECONDS_TEXT. '</td><td>';
            if ($sqlfile !=1) {
            	echo DID_NOT_RUN_SQL_TEXT.'<br /><br />';
            } else {
				echo SQL_FILE_NAME_TEXT.'<b>' . $sql_file_name . '</b><br /><br />' ;
				echo SQL_FILE_LOCATION_TEXT.'<b>' . $sql_path . '</b><br /><br />';
				echo SQL_FILE_ARCHIVE_NOTE. '<br /><br />';
			//	echo DIR_FILES_EXCLUDED.'<b>'. implode(",",$exclude_files) . '</b><br /><br />';
            }
            
            if ($download_backup !=1) {
            	echo BACKUP_FILE_NAME_TEXT.'<b>'. $file_name.$file_ext . '</b><br /><br />';
  				echo BACKUP_LOCATION.'<b>'. $file_location . '</b><br /><br />';
  				echo FTP_NOTE_TEXT. '<br /><br />';
  			//	echo DIR_FILES_EXCLUDED.'<b>'. implode(",",$exclude_files) . '</b><br /><br />';
  			} else {
  				echo BACKUP_DOWNLOAD;
  			}
  		echo '</td><td>';
  		echo '<a href="' . zen_href_link(FILENAME_BACKUP_ZC). '">' . zen_image_button('button_back.gif', IMAGE_BACK) . '</a>' ;
  		echo '</td></tr></table>';
  		echo '<hr><br>';

		}
?>
            </td>
          </tr>
        </table></td>
      </tr>
    </table></td>
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