<?php
/**
 * @package admin
 * @copyright Copyright 2003-2011 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @version $Id: admin_activity.php 19537 2011-09-20 17:14:44Z drbyte $
 */

define('HEADING_TITLE', '日志管理');
define('HEADING_SUB1', '查看或导出日志');
define('HEADING_SUB2', '清空日志');
define('TEXT_ACTIVITY_EXPORT_FORMAT', '导出文件的格式:');
define('TEXT_ACTIVITY_EXPORT_FILENAME', '导出文件名称:');
define('TEXT_ACTIVITY_EXPORT_SAVETOFILE','在服务器上保存文件吗? (否则直接下载)');
define('TEXT_ACTIVITY_EXPORT_DEST','目标: ');
define('TEXT_PROCESSED', ' 已处理。');
define('SUCCESS_EXPORT_ADMIN_ACTIVITY_LOG', '导出完成。');
define('FAILURE_EXPORT_ADMIN_ACTIVITY_LOG', '提示: 导出失败，无法写入文件 ');

define('TEXT_INSTRUCTIONS','<u>说明</u><br />本页用于导出 Zen Cart&reg; 管理日志到CSV文件。<br />请保存这些日志，万一网站被黑时可以用于调查。这是PCI条款所要求的。<br />
<ol><li>选择显示还是导出文件<li>输入文件名。<li>点击保存继续。<li>选择保存还是打开文件，根据浏览器有所不同。</ol>');

define('TEXT_INFO_ADMIN_ACTIVITY_LOG', '<strong>清空数据库中的管理日志<br />警告: 请先备份好数据库!</strong><br />管理日志用于记录后台的所有操作。<br />基于这个原因，数据量会比较大，需要经常清理。<br />超过5万条记录或者60天后系统会给出警告。<br /><span class="alert">提示: PCI 条款要求保留12个月的管理日志。<br />清空日志前，最好先选择导出到CSV来保存日志。</span>');
define('TEXT_ADMIN_LOG_PLEASE_CONFIRM_ERASE', '<strong><span class="alert">警告!: 即将从数据库中删除管理日志。</span></strong><br />请先确认您已备份了数据库。<br /><br />我愿意继续重置操作:<br />');
define('SUCCESS_CLEAN_ADMIN_ACTIVITY_LOG', '<strong>已删除</strong>管理日志');

