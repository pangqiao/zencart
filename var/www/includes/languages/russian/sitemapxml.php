<?php
/**
 * Sitemap XML Feed
 *
 * @package Sitemap XML Feed
 * @copyright Copyright 2005-2012 Andrew Berezin eCommerce-Service.com
 * @copyright Copyright 2003-2012 Zen Cart Development Team
 * @copyright Portions Copyright 2003 osCommerce
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 * @link http://www.sitemaps.org/
 * @version $Id: sitemapxml.php, v 3.2.2 07.05.2012 19:12 AndrewBerezin $
 */

define('NAVBAR_TITLE', 'SiteMapXML');
define('HEADING_TITLE', 'SiteMapXML (' . SITEMAPXML_VERSION . ')');

define('TEXT_EXECUTION_TIME', 'Итого: Время выполнения %s, Запросов к БД %s, Время выполнения запросов к БД %s.');
define('TEXT_TOTAL_SITEMAP', 'Итого: файлов %s, позиций %s (%s байтов), время выполнения %s, запросов к БД %s, время выполнения запросов к БД %s.');
define('TEXT_FILE_SITEMAP_INFO', 'Файл <a href="%s" target="_blank">%s</a>. Записано %s позиций (%s байтов), Размер файла: %s байтов');
define('TEXT_WRITTEN', 'Записано %s позиций (%s байтов), Размер файла: %s байтов');

define('TEXT_URL_FILE', 'URL - ');
define('TEXT_INCLUDE_FILE', 'Include ');
define('TEXT_FILE_NOT_CHANGED', 'have not changed - using existing file(s)');
define('TEXT_FAILED_TO_OPEN', 'Failed to open file "%s"!!!');
define('TEXT_FAILED_TO_CREATE', 'Can not create file "%s". You may need to use your webhost control panel/file-manager to change the permissions effectively.');
define('TEXT_FAILED_TO_CHMOD', 'File "%s" is Read-Only. You may need to use your webhost control panel/file-manager to change the permissions effectively.');

define('TEXT_HEAD_SITEMAP_INDEX', 'Sitemap Index');
define('TEXT_HEAD_PING', 'Ping');

define('TEXT_ERROR_CURL_NOT_FOUND', 'CURL functions not found - required for ping/checkURL functions');
define('TEXT_ERROR_CURL_INIT', 'cURL Error: init cURL');
define('TEXT_ERROR_CURL_EXEC', 'cURL Error: "<b>%s</b>" reading "%s"');
define('TEXT_ERROR_CURL_NO_HTTPCODE', 'cURL Error: No http_code reading "%s"');
define('TEXT_ERROR_CURL_ERR_HTTPCODE', 'cURL Error: Error http_code "<b>%s</b>" reading "%s"');
define('TEXT_ERROR_CURL_0_DOWNLOAD', 'cURL Error: Zero download size reading "%s"');
define('TEXT_ERROR_CURL_ERR_DOWNLOAD', 'cURL Error: Reading less than page size "%s". Download = %s, Content length = %s.');

define('TEXT_HEAD_PRODUCTS', 'Products Sitemap');
define('TEXT_HEAD_CATEGORIES', 'Categories Sitemap');
define('TEXT_HEAD_MANUFACTURERS', 'Manufacturers Sitemap');
define('TEXT_HEAD_MAINPAGE', 'Mainpage Sitemap');
define('TEXT_HEAD_EZPAGES', 'Ezpages Sitemap');
define('TEXT_HEAD_REVIEWS', 'Reviews Sitemap');
define('TEXT_HEAD_TESTIMONIALS', 'Testimonials Sitemap');

define('TEXT_HEAD_NEWS', 'News Sitemap');
define('TEXT_HEAD_NEWS_ARTICLES', 'News Articles Sitemap');

define('TEXT_HEAD_PRODUCTS_VIDEO', 'Products Video Sitemap');

define('TEXT_ERRROR_EZPAGES_OUTOFBASE', 'EZ-Page ignored (out of base url): "<b>%s</b>" (%s)');
define('TEXT_ERRROR_EZPAGES_ROBOTS', 'EZ-Page ignored (found in ROBOTS_PAGES_TO_SKIP): "<b>%s</b>" (%s)');

define('TEXT_HEAD_BOXNEWS', 'News Box Manager Sitemap');

// EOF