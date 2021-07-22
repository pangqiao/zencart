# zencart
Zen Cart® truly is the art of e-commerce; free, user-friendly, open source shopping cart software. The ecommerce web site design program is developed by a group of like-minded shop owners, programmers, designers, and consultants that think ecommerce web design could be, and should be, done differently.
## The changes in the code
Zen Cart® provides basic functionality, but the skin and the layout is not good looking, and need some important modules. Such as database backup, source code backup,SEO,image handlesr and so on. I use v1.5.1 is because there are more useful mature plugins. The folder [Plugins](https://github.com/pangqiao/zencart/tree/master/Plugins) contains the import plugins in the changes, including:
```
ultimate_seo_urls_212  
Simple_Google_Analytics_6  
Image_Handler4_v4_3_2 
backup_mysql_plugin_v5 
WishLists 
backup_zc_v1 
Reset-Customer-Password 
sitemapXML(v1.3.8-v1.5.1)
zen-cart-vChinese-simplified-utf8
```
All the plugins are fully merged and debugged. List some plugins in Admin:
![admin](https://github.com/pangqiao/zencart/blob/master/images/Admin.jpg)

This is the product detailed page after merging image handler, GPE, product tag etc.
![detailed page](https://github.com/pangqiao/zencart/blob/master/images/productdetail2.jpg)

Please also look at the folder [images](https://github.com/pangqiao/zencart/tree/master/images) to see the all the differences.

## Code structure
/var/www: includes the source code for zencart.  
/etc/apache2: includes the configure file for apache, such as port 80.  
/db.sql: the database backup.  

## Server requirements
ubuntu14.04 + apache2 + php + mysql
```
sudo apt-get update
sudo apt-get install apache2

sudo add-apt-repository ppa:eugenesan/ppa
sudo apt-get install php5
sudo apt-get install phpmyadmin 
sudo ln -s /usr/share/phpmyadmin /var/www

sudo apt-get install mysql-client-core-5.6
sudo apt-get install mysql-client-5.6
sudo apt-get install mysql-server-5.6
```
## Configure apache
1. Add below lines in file /etc/apache2/apache2.conf  
	AddType application/x-httpd-php .php .htm .html  
	AddDefaultCharset UTF-8  
	ServerName 172.17.171.41  #your ip, not 127.0.0.1  
	DirectoryIndex index.htm index.html index.php  
2. Change "AllowOverride None" to  =>"AllowOverride All"  
3. Open port 80.  
  refer to: https://help.aliyun.com/knowledge_detail/59367.html#ubuntu  
	netstat -an | grep 80  
	tcp        0      0 0.0.0.0:80              0.0.0.0:*               LISTEN  
	tcp        0      0 172.17.171.41:41904     140.205.140.205:80      ESTABLISHED  
4. Change directory to /var/www/ 

## Move the station
[WampServer](http://www.wampserver.com/) is a Windows web development environment. It allows you to create web applications with Apache2, PHP and a MySQL database. Alongside, PhpMyAdmin allows you to manage easily your database.But you want your web get viewed by others, your should move it to the server and open the port 80. I use ECS of aliyun.  

This is the steps:  
1. Prepare the environment as before, linux, apach2, PHP, etc.  
2. Install the same zencart software V1.5.1. with the same username and password as WampServer. 
3. Backup /includes/configure.php and /admin/includes/configure.php on the server.
4. Overwrite all the files on the server with local code.
5. Restore /includes/configure.php and /admin/includes/configure.php on the server.
6. Log on the phpMyAdmin and clean the database, use new datebase [db.sql](https://github.com/pangqiao/zencart/tree/master/db.sql)
7. If you got error like 404, please check /var/www/.htaccess.
8. Open apache rewrite.
9. Set the configure.php as read only.

## The password of Admin
1. Change the file admin/login.php
```
define('ADMIN_PASSWORD_EXPIRES_INTERVAL', strtotime('- 90 day'));
```
or change $message to true, you can use any password. 
```
 if ($message == false) {
	$_SESSION['admin_id'] = $result->fields['admin_id'];
	if (SESSION_RECREATE == ‘True’) {
		zen_session_recreate();
	}
	zen_redirect(zen_href_link(FILENAME_DEFAULT, ”, ‘SSL’));
}
```
2. Enter into phpmyadmin, open database of admin and run below code in SQL mode. After reset, the password is admin.
```
DELETE FROM admin WHERE admin_name = 'Admin'; 
INSERT INTO admin (admin_name, admin_email, admin_pass, admin_profile) 
VALUES ('Admin', 'admin@localhost', '351683ea4e19efe34874b501fdbf9792:9b', 1);
```

## TODO
1. SSL, OpenSSL, CURL  
2. PayPal  
3. Test "west Union"
4. Find a way to clean "sql log"

## Reference
1. https://help.aliyun.com/knowledge_detail/40579.html?spm=5176.2020520129.105.5.25d9fd75bVSNQR
2. http://www.wampserver.com/
3. https://docs.zen-cart.com/dev/  
4. https://www.zen-cart.com/index.php
