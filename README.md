# getSeoSitemap v4.1.0 | 2020-11-29
PHP library to get sitemap.<br>
It crawls a whole domain checking all URLs.<br>
It makes Search Engine Optimization of URLs into sitemap only.<br>

[![donate via paypal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.me/johnbe4)<br>
![donate via bitcoin](https://img.shields.io/badge/donate-bitcoin-orange.svg)<br>
**Please support this project by making a donation via [PayPal](https://www.paypal.me/johnbe4) or via BTC bitcoin to the address 19928gKpqdyN6CHUh4Tae1GW9NAMT6SfQH**<br>

* **category**    Library
* **author**      Giovanni Bertone <red@redracingparts.com>
* **copyright**   2017-2020 Giovanni Bertone | RED Racing Parts
* **link**        https://www.redracingparts.com
* **source**      https://github.com/johnbe4/getSeoSitemap

# Warning
# Before moving from releases lower than 4.1.0 to 4.1.0 or higher, you must drop getSeoSitemap and getSeoSitemapExec tables into your dBase.

**Overview<br>**
This script creates a full gzip sitemap or multiple gzip sitemaps plus a gzip sitemap index.<br>
It includes change frequency, last modification date and priority setted following your own rules.<br>
Change frequency will be automatically selected between daily, weekly, monthly and yearly.<br>
Max URL lenght must be 767 characters, otherwise the script will fail.<br>
URLs with http response code different from 200 or with size = 0 will not be included into sitemap.<br>
It checks all internal and external links inside html pages and js sources (href URLs into 'a' tag plus form action URLs if method is get).<br>
It checks all internal and external sources.<br>
Mailto URLs will not be included into sitemap.<br>
URLs inside pdf files will not be scanned and will not be included into sitemap.<br>

To improve SEO following robots.txt rules of "User-agent: *", it checks:<br>
- http response code of all internal and external sources into domain (images, scripts, links, iframes, videos, audios)<br>
- malformed URLs into domain<br>
- page title of URLs into domain<br>
- page description of URLs into domain<br>
- page h1/h2/h3 of URLs into domain<br>
- page size of URLs into sitemap<br>
- image alt of URLs into domain<br>
- image title of URLs into domain.<br>

You can use absolute or relative URLs inside the site.<br>
This script will set automatically all URLs to skip and to allow into sitemap following the robots.txt rules of "User-agent: *" and robots tag into page head.<br>
There is not any automatic function to submit updated sitemap to search engines.<br>
Sitemap will be saved in the main directory of the domain.<br>
It rewrites robots.txt adding updated sitemap informations.<br>
Maximum limit of URLs to insert into sitemap is 2.5T.<br>

Other main features:<br>
- backup of all previous sitemaps into bak folder.<br>
- it repeats URL scan once after 5 sec in case of http response code is different from 200.<br>
- it prevents saving sitemap if total URLs percentage difference from previous successful 
exec is more than a preset value.<br>

Using getSeoSitemap, you will be able to give a better surfing experience to your clients.<br>

**Requirements<br>**
- PHP 7.3.<br>
- MariaDB 10.4.<br>

**Instructions<br>**
1 - copy getSeoSitemap folder in a protected zone of your server.<br>
2 - set all user parameters into config.php.<br>
3 - on your server cronotab schedule the script once each day preferable when your server is not too much busy.<br>
    A command line example to schedule the script every day at 7:45:00 AM is:<br>
    45 7  *    *    *    php /example/example/example/example/example/getSeoSitemap/getSeoSitemap.php<br>
    When you know how long it takes to execute all the script, you could add a cronotab timeout.

**Warning<br>**
**Before moving from releases lower than 4.1.0 to 4.1.0 or higher, you must drop getSeoSitemap and getSeoSitemapExec tables into your dBase.<br>**
**Do not save any file with name that starts with sitemap in the main directory, otherwise getSeoSitemap script could cancel it.<br>**
**The robots.txt file must be present into the main directory of the site otherwise getSeoSitemap will fail.<br>**
To run getSeoSitemap faster, using a script like Geoplugin you should exclude geoSeoSitemapBot user-agent from that.
