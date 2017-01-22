# getSeoSitemap
*Php library to scan a whole website creating the sitemap checking all internal and external links*

[![donate via paypal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.me/johnbe4)
*Please support this project by making a donation via [PayPal](https://www.paypal.me/johnbe4) or with bitcoin to the address 1HRpDx1Tg24ThVT1axJESnoakiRMqq2ENz*<br>

* **category**    Library
* **author**      Giovanni Bertone <red@redracingparts.com>
* **copyright**   2016-2017 Giovanni Bertone - RED Racing Parts
* **link**        https://www.redracingparts.com
* **source**      https://github.com/johnbe4/getSeoSitemap

The script requires PHP 5.4 and a SQL database.<br>

This script creates a full sitemap.xml plus a full sitemap.xml.gz.<br>
It includes change frequency, last modification date and priority all setted following your own rules.<br>
Change frequency will be automatically selected between daily, weekly, monthly and yearly.<br>
URLs with http response code different from 200 or with size = 0 will not be included into sitemap.<br>
It checks all internal and external links.<br>
If failed (http response code different from 200 or with size = 0), external URLs from the domain will be included into failed URLs list.<br>
Mailto URLs with will not be included into sitemap.<br>
URLs inside pdf files will not be scanned and will not be included into sitemap.<br>
You have to use only absolute URLs inside the site.<br>
Before saving the new sitemap.xml and sitemap.xml.gz, this script creates two backup copies of the previous ones if they already exist.<br>
Those two copies will be named sitemap.back.xml and sitemap.back.xml.gz.<br>
There are not any automatic functions to submit updated sitemap to google or bing.<br>
That is because I discovered search engines prefer submission by their webmaster tools.<br>
In fact, submitting sitemap by their own link, they never update the last submission time inside webmaster tools.<br>
There is not any maximum limit of URLs number to scan and to add to sitemap.<br><br>
You will be able to fix all internal an external wrong links giving a better surfing experience to your clients.<br><br>
Instructions<br>
1 - after download the repository, remeber to rename the folder fron getSeoSitemap-master to getSeoSitemap.<br>
2 - copy the getSeoSitemap folder ina protected zone of your server.<br>
3 - all links of your website must be setted to absolute links ( including always http:// or https:// ).<br>
    That is very important because search engines do not like relative links and that prevent negative issues.<br>
    Only using absolute link you are 100% sure how the link will be treat by search engines, browsers etc.<br>
4 - create tables getSeoSitemapExec and getSeoSitemap running in order query 1, query 2 and query 3 in your phpMyAdmin.<br>
    Do that only the first time and only once.<br>
5 - set all user constants and parameters.<br>
6 - on your server cronotab schedule the script once each day prefereble when your server is not too much busy.<br>
    A command line example to schedule the script every day at 7:45:00 AM is:<br>
    45 7  *    *    *    php /path/sites/host/var/web/secure/getSeoSitemap/getSeoSitemap.php<br><br>
Notice<br>
To execute getSeoSitemp faster, using a script like geoplugin.class you should exclude geoSeoSitemap user-agent from that.<br><br>
Field url into dbase must setted varbinary type to set sensitive queries.<br>
That is very important when it search for url uppercase and lowercase.<br><br><br>
query 1<br><br>
CREATE TABLE `getSeoSitemapExec` (<br>
 `id` int(1) NOT NULL AUTO_INCREMENT,<br>
 `func` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,<br>
 `mDate` int(10) DEFAULT NULL COMMENT 'timestamp of last mod',<br>
 `exec` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,<br>
 `newData` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'set to y when new data are avaialble',<br>
 UNIQUE KEY `id` (`id`),<br>
 UNIQUE KEY `func` (`func`),<br>
 KEY `exec` (`exec`),<br>
 KEY `newData` (`newData`)<br>
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='execution of getSeoSitemap functions'<br>
<br><br>
query 2<br><br>
INSERT INTO getSeoSitemapExec (func, mDate, exec, newData) VALUES ('getSeoSitemap', 0, 'n', 'n')<br><br><br>
query 3<br><br>
CREATE TABLE `getSeoSitemap` (<br>
 `id` smallint(6) NOT NULL AUTO_INCREMENT,<br>
 `url` varbinary(330) NOT NULL,<br>
 `size` mediumint(7) NOT NULL,<br>
 `md5` varchar(32) COLLATE utf8_unicode_ci NOT NULL,<br>
 `lastmod` int(10) NOT NULL,<br>
 `changefreq` enum('daily','weekly','monthly','yearly') COLLATE utf8_unicode_ci NOT NULL,<br>
 `priority` decimal(2,1) DEFAULT NULL,<br>
 `state` varchar(10) COLLATE utf8_unicode_ci NOT NULL,<br>
 `httpCode` varchar(5) COLLATE utf8_unicode_ci NOT NULL,<br>
 PRIMARY KEY (`id`),<br>
 UNIQUE KEY `url` (`url`),<br>
 KEY `state` (`state`),<br>
 KEY `httpCode` (`httpCode`),<br>
 KEY `size` (`size`),<br>
 KEY `changefreq` (`changefreq`),<br>
 KEY `priority` (`priority`)<br>
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
