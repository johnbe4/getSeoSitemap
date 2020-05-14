<?php

/*
getSeoSitemap v3.9.6 LICENSE (2019-12-02)

getSeoSitemap v3.9.6 is distributed under the following BSD-style license: 

Copyright (c) 2017-2020
Giovanni Bertone (RED Racing Parts)
https://www.redracingparts.com
red@redracingparts.com
All rights reserved.

Redistribution and use in source and binary forms, with or without
modification, are permitted provided that the following conditions are met:
1. Redistributions of source code must retain the above copyright
   notice, this list of conditions and the following disclaimer.
2. Redistributions in binary form must reproduce the above copyright
   notice, this list of conditions and the following disclaimer in the
   documentation and/or other materials provided with the distribution.
3. All advertising materials mentioning features or use of this software
   must display the following acknowledgement:
   This product includes software developed by the RED Racing Parts.
4. Neither the name of the RED Racing Parts nor the
   names of its contributors may be used to endorse or promote products
   derived from this software without specific prior written permission.

THIS SOFTWARE IS PROVIDED BY GIOVANNI BERTONE ''AS IS'' AND ANY
EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
DISCLAIMED. IN NO EVENT SHALL GIOVANNI BERTONE BE LIABLE FOR ANY
DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
(INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
(INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
*/

###################################################################################################
# Please support this project by making a donation via PayPal to https://www.paypal.me/johnbe4 or #
# with BTC bitcoin to the address 19928gKpqdyN6CHUh4Tae1GW9NAMT6SfQH                              #
###################################################################################################

##### start of user parameters
const DOMAINURL = 'https://www.example.com'; // domain (or subdomain) URL: every URL must include it at the beginning
/*
DOMAINURL value must be absolute and cannot end with /
value could be https://www.example.com for a domain or https://www.example1.example.com for a subdomain
*/
const DEFAULTPRIORITY = '0.5'; // default priority for URLs not included in $fullUrlPriority and $partialUrlPriority
const DBHOST = DBHOST2; // database host
const DBUSER = DBUSER2; // database user (warning: user must have permissions to create / alter table)
const DBPASS = DBPASS2; // database password
const DBNAME = DBNAME2; // database name
const GETSEOSITEMAPPATH = '/example/example/getSeoSitemap/'; // getSeoSitemap path into server
const SITEMAPPATH = '/example/web/'; // sitemap path into server (must be the same path of robots.txt)
const PRINTSKIPURLS = false; // set to true to print the list of URLs out of sitemap into log file
const PRINTCHANGEFREQLIST = false; // set to true to print URLs list following changefreq
const PRINTPRIORITYLIST = false; // set to true to print URLs list following priority
const PRINTTYPELIST = false; // set to true to print URLs list following type                                                                                                             
const PRINTSITEMAPSIZELIST = false; // set to true to print a size list of all sitemaps   
const PRINTMALFURLS = true; // set to true to print a malformed URL list following a standard good practice
const CHECKH2 = true; // set to true to check if h2 is present in all pages
const CHECKH3 = true; // set to true to check if h3 is present in all pages
// priority values must be 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0. other values are not accepted.
$GLOBALS['fullUrlPriority'] = [ // set priority of particular URLs that are equal these values (values must be absolute)
'1.0' => [
'https://www.example.com'
],
'0.9' => [
'https://www.example.com/english/example.php',
'https://www.example.com/italiano/example.php'
],
];
$GLOBALS['partialUrlPriority'] = [ // set priority of particular URLs that start with these values (values must be absolute)
'0.8' => [
'https://www.example.com/english/example/introducingpages/11/22/',
'https://www.example.com/italiano/example/pagineintroduttive/11/22/',
],
'0.7' => [
'https://www.example.com/italianoexample/prodottiecomponenti/generale/intro/',
'https://www.example.com/english/example/productsandcomponents/general/intro/',
],
'0.6' => [
'https://www.example.com/catalog.php?p=',
],
];
##### end of user parameters
