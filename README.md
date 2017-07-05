# getSeoSitemap
*Php library to get the sitemap.<br>
It crawls a whole website checking all internal and external links.*<br>

[![donate via paypal](https://img.shields.io/badge/donate-paypal-87ceeb.svg)](https://www.paypal.me/johnbe4)<br>
(https://img.shields.io/badge/donate-bitcoin-orange.svg)<br>
*Please support this project by making a donation via [PayPal](https://www.paypal.me/johnbe4) or with bitcoin to the address 1HRpDx1Tg24ThVT1axJESnoakiRMqq2ENz*<br>

* **category**    Library
* **author**      Giovanni Bertone <red@redracingparts.com>
* **copyright**   2016-2017 Giovanni Bertone - RED Racing Parts
* **link**        https://www.redracingparts.com
* **source**      https://github.com/johnbe4/getSeoSitemap

The script requires PHP 5.4 and MySQL 5.5.<br>

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
There is not any maximum limit of URLs number to scan and to add to sitemap.<br>

You will be able to fix all internal an external wrong links giving a better surfing experience to your clients.<br>

Instructions<br>
1 - copy getSeoSitemap folder in a protected zone of your server.<br>
2 - all links of your website must be setted to absolute links ( including always http:// or https:// ).<br>
    That is very important because search engines do not like relative links and that prevent negative issues.<br>
    Only using absolute link you are 100% sure how the link will be treat by search engines, browsers etc.<br>
3 - set all user constants and parameters.<br>
4 - on your server cronotab schedule the script once each day prefereble when your server is not too much busy.<br>
    A command line example to schedule the script every day at 7:45:00 AM is:<br>
    45 7  *    *    *    php /example/websites/clients/client1/web5/example/example/getSeoSitemap/getSeoSitemap.php<br>

Notice<br>
To execute getSeoSitemp faster, using a script like geoplugin.class you should exclude geoSeoSitemap user-agent from that.
