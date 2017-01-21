getSeoSitemap v. 1.0 README

###################################################################################################
# Please support this project by making a donation via PayPal to https://www.paypal.me/johnbe4 or #
# with bitcoin to the address 1HRpDx1Tg24ThVT1axJESnoakiRMqq2ENz                                  #
###################################################################################################

This script creates a full sitemap.xml plus a full sitemap.xml.gz.
It includes change frequency, last modification date and priority all setted following your own rules.
Change frequency will be automatically selected between daily, weekly, monthly and yearly.
URLs with http response code different from 200 or with size = 0 will not be included into sitemap.
If failed (http response code different from 200 or with size = 0), external URLs from the domain will be included into failed URLs list.
Mailto URLs with will not be included into sitemap.
URLs inside pdf files will not be scanned and will not be included into sitemap.
You have to use only absolute URLs inside the site.
Before saving the new sitemap.xml and sitemap.xml.gz, this script creates two backup copies of the previous ones if they already exist.
Those two copies will be named sitemap.back.xml and sitemap.back.xml.gz.
There are not any automatic functions to submit updated sitemap to google or bing. 
That is because I discovered search engines prefer submission by their webmaster tools.
In fact, submitting sitemap by their own link, they never update the last submission time inside webmaster tools.
There is not any maximum limit of URLs number to scan and to add to sitemap.

You will be able to fix them giving a better surfing experience to your clients.

Instructions
1 - all links of your website must be setted to absolute links ( including always http:// or https:// ).
    That is very important because search engines do not like relative links and that prevent negative issues.
    Only using absolute link you are 100% sure how the link will be treat by search engines, browsers etc.
2 - create tables getSeoSitemapExec and getSeoSitemap running in order query 1, query 2 and query 3 in your phpMyAdmin.
    Do that only the first time and only once.
3 - set all user constants and parameters.
3 - on your server cronotab schedule the script once each day prefereble when your server is not too much busy.
    A command line example to schedule the script every day at 7:45:00 AM is:
    45 7  *    *    *    php /path/sites/host/var/web/secure/getSeoSitemap/getSeoSitemap.php

Notice
To execute getSeoSitemp faster, using a script like geoplugin.class you should exclude geoSeoSitemap user-agent from that.

Field url into dbase must setted varbinary type to set sensitive queries.
That is very important when it search for url uppercase and lowercase.

query 1
#####
CREATE TABLE `getSeoSitemapExec` (
 `id` int(1) NOT NULL AUTO_INCREMENT,
 `func` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mDate` int(10) DEFAULT NULL COMMENT 'timestamp of last mod',
 `exec` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
 `newData` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'set to y when new data are avaialble',
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `func` (`func`),
 KEY `exec` (`exec`),
 KEY `newData` (`newData`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='execution of getSeoSitemap functions'
#####

query 2
#####
INSERT INTO getSeoSitemapExec (func, mDate, exec, newData) VALUES ('getSeoSitemap', 0, 'n', 'n')
#####

query 3
#####
CREATE TABLE `getSeoSitemap` (
 `id` smallint(6) NOT NULL AUTO_INCREMENT,
 `url` varbinary(330) NOT NULL,
 `size` mediumint(7) NOT NULL,
 `md5` varchar(32) COLLATE utf8_unicode_ci NOT NULL,
 `lastmod` int(10) NOT NULL,
 `changefreq` enum('daily','weekly','monthly','yearly') COLLATE utf8_unicode_ci NOT NULL,
 `priority` decimal(2,1) DEFAULT NULL,
 `state` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
 `httpCode` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `url` (`url`),
 KEY `state` (`state`),
 KEY `httpCode` (`httpCode`),
 KEY `size` (`size`),
 KEY `changefreq` (`changefreq`),
 KEY `priority` (`priority`)
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci
#####
