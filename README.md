# zencart
Zen Cart® truly is the art of e-commerce; free, user-friendly, open source shopping cart software. The ecommerce web site design program is developed by a group of like-minded shop owners, programmers, designers, and consultants that think ecommerce web design could be, and should be, done differently.
## the changes in the code
Zen Cart® provides basic functionality, but the skin and the layout is not good looking, and need some important modules. Such as database backup, source code backup,SEO,image handlesr and so on. I use v1.15.1 is because there are more useful mature plugins. The fold [Plugins](https://github.com/pangqiao/zencart/tree/master/Plugins) contains the import plugins in the change, including:
```
ultimate_seo_urls_212  
Simple_Google_Analytics_6  
Image_Handler4_v4_3_2 
backup_mysql_plugin_v5 
WishLists 
backup_zc_v1 
Reset-Customer-Password 
sitemapXML(v1.3.8-v1.5.1)
```
All the plugins are fully debugged. There are the plugins in Admin:
![admin](https://github.com/pangqiao/zencart/blob/master/images/Admin.jpg)

This is the product detailed page after merging image handler, GPE, product tag etc.
![detailed page](https://github.com/pangqiao/zencart/blob/master/images/productdetail2.jpg)

Please also look at the folder [images](https://github.com/pangqiao/zencart/tree/master/images)for the comparation.

## Code structure
/var/www: includes the source code for zencart.  
/etc/apache2: includes the configure file for apache, such as port 80.  
/db.sql: the database backup.  

## Server requirements
ubuntu14.04 + apache2 + php+mysql+phalcon
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
