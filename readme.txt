getSeoSitemap v2.2 README (2018-01-23)

Php library to get sitemap.
It crawls a whole website checking all internal and external links.
It makes a Search Engine Optimization.

###################################################################################################
# Please support this project by making a donation via PayPal to https://www.paypal.me/johnbe4 or #
# with BTC bitcoin to the address 19928gKpqdyN6CHUh4Tae1GW9NAMT6SfQH                              #
###################################################################################################

The script requires PHP 5.4 and MySQL 5.5.

This script creates a full sitemap.xml plus a full sitemap.xml.gz.
It includes change frequency, last modification date and priority all setted following your own rules.
Change frequency will be automatically selected between daily, weekly, monthly and yearly.
URLs with http response code different from 200 or with size = 0 will not be included into sitemap.
It checks all internal and external links.
If failed (http response code different from 200 or with size = 0), external URLs from the domain will be included into failed URLs list.
Mailto URLs with will not be included into sitemap.
URLs inside pdf files will not be scanned and will not be included into sitemap.
It checks page title and page size to improve SEO.
You must use only absolute URLs inside the site.
Before saving the new sitemap.xml.gz, this script creates a backup copy of the previous one if it already exists.
This copy will be named sitemap.back.xml.gz.
There is not any automatic function to submit updated sitemap to google or bing.
That is because I discovered search engines prefer submission by their webmaster tools.
In fact, submitting sitemap by their own link, they never update the last submission time inside webmaster tools.
This script checks page title amd page size to improve SEO.
There is not any maximum limit of URLs number to scan and to add to sitemap.

Using getSeoSitemap, you will be able to give a better surfing experience to your clients.

Instructions
1 - copy getSeoSitemap folder in a protected zone of your server.
2 - all links of your website must be setted to absolute links ( including always http:// or https:// ).
    That is very important because search engines do not like relative links and that prevent negative issues.
    Only using absolute link you are 100% sure how the link will be treated by search engines, browsers etc.
3 - set all user constants and parameters.
4 - on your server cronotab schedule the script once each day prefereble when your server is not too much busy.
    A command line example to schedule the script every day at 7:45:00 AM is:
    45 7  *    *    *    php /example/websites/clients/client1/web5/example/example/getSeoSitemap/getSeoSitemap.php.

Notice
To run getSeoSitemp faster, using a script like geoplugin.class you should exclude geoSeoSitemap user-agent from that.
Before moving from releases 1.0 or 1.1 to 2.0 or higher, you must delete the getSeoSitemap table into your dBase.
Before moving from releases 1.0, 1.1, 2.0 or 2.1 to 2.2, you must delete the sitemap.xml file into your website.
