# getSeoSitemap v3.7.0 (2019-02-18)
Php library to get sitemap.<br>
It crawls a whole website checking all links and sources.<br>
It makes a Search Engine Optimization.<br>

[![donate via paypal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.me/johnbe4)<br>
![donate via bitcoin](https://img.shields.io/badge/donate-bitcoin-orange.svg)<br>
**Please support this project by making a donation via [PayPal](https://www.paypal.me/johnbe4) or via BTC bitcoin to the address 19928gKpqdyN6CHUh4Tae1GW9NAMT6SfQH<br><br>
Please star this package to support!**<br>

* **category**    Library
* **author**      Giovanni Bertone <red@redracingparts.com>
* **copyright**   2017-2019 Giovanni Bertone - RED Racing Parts
* **link**        https://www.redracingparts.com
* **source**      https://github.com/johnbe4/getSeoSitemap

It requires PHP 5.5 and MySQL 5.5.

This script creates a full gzip sitemap or multiple gzip sitemaps plus a gzip sitemap index.<br>
It includes change frequency, last modification date and priority all setted following your own rules.<br>
Change frequency will be automatically selected between daily, weekly, monthly and yearly.<br>
Max URL lenght must be 767 characters, otherwise the script will fail.<br>
URLs with http response code different from 200 or with size = 0 will not be included into sitemap.<br>
It checks all internal and external links and sources.<br>
Mailto URLs with will not be included into sitemap.<br>
URLs inside pdf files will not be scanned and will not be included into sitemap.<br>

To improve SEO, it checks:<br>
- malformed URLs<br>
- http response code of all internal and external sources (images, scripts, links, iframes, videos, audios)<br>
- page title<br>
- page description<br>
- page h1/h2/h3<br>
- page size<br>
- image alt<br>
- image title.<br>

You can use absolute or relative URLs inside the site.<br>
There is not any automatic function to submit updated sitemap to google or bing.<br>
That is because I discovered search engines prefer submission by their webmaster tools.<br>
In fact, submitting sitemap by their own link, they never update the last submission time inside webmaster tools.<br>
It rewrites robots.txt adding updated sitemap informations.<br>
Maximum limit of URLs to insert into sitemap is 2.5T.<br>

Using getSeoSitemap, you will be able to give a better surfing experience to your clients.<br>

**Instructions<br>**
1 - copy getSeoSitemap folder in a protected zone of your server.<br>
2 - set all user constants and parameters.<br>
3 - on your server cronotab schedule the script once each day preferable when your server is not too much busy.<br>
    A command line example to schedule the script every day at 7:45:00 AM is:<br>
    45 7  *    *    *    php /example/example/example/example/example/getSeoSitemap/getSeoSitemap.php<br>
    When you know how long it takes to execute all the script, you could add a cronotab timeout.

**Warning<br>**
Before moving from releases lower than 3.0 to 3.0 or higher, you must drop getSeoSitemap and getSeoSitemapExec tables into your dBase.<br>
Do not save any file with name that starts with sitemap in the same folder of sitemaps, otherwise getSeoSitemap script could cancel it.
