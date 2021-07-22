<?php
/*
  $Id: uninstall_backup_zc_files.php, v 1.1 2012/04/27  $																                                                         
  Written By SkipWater <skip@ccssinc.net>
                                                      
  Powered by Zen-Cart (www.zen-cart.com)              
  Portions Copyright (c) 2006 The Zen Cart Team

  Released under the GNU General Public License       
  available at www.zen-cart.com/license/2_0.txt       
  or see "license.txt" in the downloaded zip 

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com
  Originally Written by Carl Skerritt for osC
  Copyright (c) 2007

  Released under the GNU General Public License

  This will remove all the BackUp ZC Files
  Use phpMyAdmin to remove SQL Data

*/
require('includes/application_top.php');


        // ------------ Check Files ------------------
        // Check Admin Files
        if (is_file(DIR_FS_ADMIN . 'backup_zc.php')){
			unlink(DIR_FS_ADMIN.'backup_zc.php');
			echo 'backup_zc.php removed..<br/>';
        }
        
        if (is_file(DIR_FS_ADMIN . 'backup_zconfig.php')){
			unlink(DIR_FS_ADMIN.'backup_zconfig.php');
			echo 'backup_zconfig.php removed..<br/>';
        }

        if (is_file(DIR_FS_ADMIN . 'backup_zc_download.php')){
			unlink(DIR_FS_ADMIN.'backup_zc_download.php');
			echo 'backup_zc_download.php removed..<br/>';
        }

        // Check Classes Files
        if (is_file(DIR_FS_ADMIN . 'includes/classes/archive.php')){
			unlink(DIR_FS_ADMIN.'includes/classes/archive.php');
			echo 'includes/classes/archive.php removed..<br/>';
        }
        
        if (is_file(DIR_FS_ADMIN . 'includes/classes/archive_readme.txt')){
			unlink(DIR_FS_ADMIN.'includes/classes/archive_readme.txt');
			echo 'includes/classes/archive_readme.txt removed..<br/>';
        }
        
        if (is_file(DIR_FS_ADMIN . 'includes/classes/mysql_db_backup.class.php')){
			unlink(DIR_FS_ADMIN.'includes/classes/mysql_db_backup.class.php');
			echo 'includes/classes/mysql_db_backup.class.php removed..<br/>';
        }
        
        if (is_file(DIR_FS_ADMIN . 'includes/classes/mysql_db_backup.class_readme.txt')){
			unlink(DIR_FS_ADMIN.'includes/classes/mysql_db_backup.class_readme.txt');
			echo 'includes/classes/mysql_db_backup.class_readme.txt removed..<br/>';
        }
       
        // Check Extra Datafiles Files
        if (is_file(DIR_FS_ADMIN . 'includes/extra_datafiles/backup_zc.php')){
			unlink(DIR_FS_ADMIN.'includes/extra_datafiles/backup_zc.php');
			echo 'includes/extra_datafiles/backup_zc.php removed..<br/>';
        }

        // Check Languages Files
        if (is_file(DIR_FS_ADMIN . 'includes/languages/english/backup_zc.php')){
			unlink(DIR_FS_ADMIN.'includes/languages/english/backup_zc.php');
			echo 'includes/languages/english/backup_zc.php removed..<br/>';
        }

        if (is_file(DIR_FS_ADMIN . 'includes/languages/english/backup_zc_download.php')){
			unlink(DIR_FS_ADMIN.'includes/languages/english/backup_zc_download.php');
			echo 'includes/languages/english/backup_zc_download.php removed..<br/>';
        }

        // ----------- Remove Backup ZC From admin menu
        if (zen_page_key_exists('backup_zc')){ // Test if in admin pages
        	zen_deregister_admin_pages('backup_zc');
        	echo 'BackUp ZC Menu Item Removed';
        // DELETE FROM `zen15`.`admin_pages` WHERE `admin_pages`.`page_key` = 'backup_zc';
		}

?>