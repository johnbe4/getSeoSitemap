<?php

/*
getSeoSitemap v3.6.0 LICENSE (2019-01-11)

getSeoSitemap v3.6.0 is distributed under the following BSD-style license: 

Copyright (c) 2016-2018 
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

##### start of user constants
const DOMAINURL = 'https://www.example.com'; // domain url (value must be absolute).
// every URL must contain this value at the beginning
const STARTURL = 'https://www.example.com'; // starting url to crawl (value must be absolute)
const DEFAULTPRIORITY = '0.5'; // default priority for URLs not included in $fullUrlPriority and $partialUrlPriority
const DBHOST = DATABASE_HOST_I; // database host
const DBUSER = DATABASE_USER_I; // database user
const DBPASS = DATABASE_PASSWORD_I; // database password
const DBNAME = DATABASE_NAME_I; // database name

 // getSeoSitemap path inside server
const GETSITEMAPPATH = '/example/example/example/example/example/example/example/getSeoSitemap/';

const SITEMAPPATH = '/example/example/example/example/example/example/'; // sitemap path inside server
const PRINTINTSKIPURLS = false; // set to false if you do not want the list of internal skipped URLs in your log file

 // set to true to get a list of container URLs of skipped URLs. It is useful to fix wrong URLs.
const PRINTCONTAINEROFSKIPPED = false;
##### end of user constants

class getSeoSitemap {

##### start of user parameters
private $skipUrl = [ // skip all urls that start or are equal these values (values must be absolute)
'https://www.example.com/example/',
'https://www.example.com/example/example/example/example/example/example.php',
'https://www.example.com/example/example/example/example/example/example.php',
'https://www.example.com/example/example.php',
];
// set $fileToAdd to true to follow and add all kind of URLs.
// set $fileToAdd to an array to follow and add only some kinds of URLs (example: $fileToAdd = ['php','pdf',];).
private $fileToAdd = [
'php',
'pdf',
];
// priority values must be 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1.0. other values are not accepted.
private $fullUrlPriority = [ // set priority of particular URLs that are equal these values (values must be absolute)
'1.0' => [
'https://www.example.com'
],
'0.9' => [
'https://www.example.com/example/example/example/11/22/example.php',
'https://www.example.com/example/example/example/11/22/example.php'
],
];
private $partialUrlPriority = [ // set priority of particular URLs that start with these values (values must be absolute)
'0.8' => [
'https://www.example.com/example/example/example/11/22/',
'https://www.example.com/example/example/example/11/22/',
],
'0.7' => [
'https://www.example.com/example/example/example/example/intro/',
'https://www.example.com/example/example/example/general/intro/',
],
];
private $printChangefreqList = false; // set to true to print URLs list following changefreq
private $printPriorityList = false; // set to true to print URLs list following priority
private $printTypeList = false; // set to true to print URLs list following type                                                                                                             
private $extUrlsTest = true; // set to false to skip external URLs test (default value is true)
private $printSitemapSizeList = false; // set to true to print a size list of all sitemaps   
private $printMysqlWarn = true; // set to true to print MySQL warnings    
private $printMalfUrls = true; // set to true to print a malformed URL list following a standard good practice
private $rewriteRobots = false; // set to true to rewrite robots.txt including updated sitemap infos
##### end of user parameters

#################################################
##### WARNING: DO NOT CHANGE ANYTHING BELOW #####
#################################################

private $version = 'v3.6.0';
private $userAgent = 'getSeoSitemap ver. by John';
private $url = null; // an aboslute URL (ex. https://www.example.com/test/test1.php )
private $size = null; // size of file in Kb
private $titleLength = [5, 112]; // min, max title length
private $descriptionLength = [50, 160]; // min, max description length
private $md5 = null; // md5 of string (hexadecimal)
private $changefreq = null; // change frequency of file (values: daily, weekly, monthly, yearly)
private $lastmod = null; // timestamp of last modified date of URL
private $state = null; // state of URL (values: old = URL of previous scan, new = new URL to scan, 
// scan = new URL already scanned, skip = new skipped URL)
private $insUrl = null;
private $mysqli = null; // mysqli connection
private $ch = null; // curl connection
private $row = []; // array that includes row from query
private $pageLinks = []; // it includes all links inside a page
private $pageBody = null; // the page including header
private $httpCode = null; // the http response code
private $rowNum = null; // number of rows into dbase
private $count = null; // count of rows (ex. 125)
private $query = null; // query
private $stmt = null; // statement for prepared query
private $stmt2 = null; // statement 2 for prepared query
private $stmt3 = null; // statement 3 for prepared query
private $stmt4 = null; // statement 4 for prepared query
private $stmt5 = null; // statement 5 for prepared query
private $startTime = null; // start timestamp
private $succ = null; // success of a function (value can be true or false)
private $doNotFollowLinksIn = [ // do not follow links inside these file types
'pdf',
];
private $seoExclusion = [ // file type to exclude from seo functions
'pdf',
];
private $changefreqArr = ['daily', 'weekly', 'monthly', 'yearly']; // changefreq accepted values
private $priorityArr = ['1.0', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1']; // priority accepted values
private $exec = 'n'; // execution value (could be y or n)
private $errCounter = 0; // error counter
private $maxErr = 20; // max number of errors to stop execution
private $errMsg = [
'C01' => 'cURL error for multiple choices server response'
];
private $escapeCodeArr = [ // escape code conversions
'&' => '&amp;',
"'" => "&apos;",
'"' => '&quot;',
'>' => '&gt;',
'<' => '&lt;',
];
private $maxUrlsInSitemap = 50000; // max number of URLs into a single sitemap
private $maxTotalUrls = 2500000000; // max total number of URLs
private $totUrls = null; // total URLs at the end of 
private $sitemapMaxSize = 52428800; // max sitemap size (bytes)
private $sitemapNameArr = []; // includes names of all saved sitemaps at the end of the process
// text to add on some MySQL errors
private $txtToAddOnMysqliErr = ' - fix it remembering to set exec to n in getSeoSitemapExec table.'; 
private $pageMaxSize = 135168; // page max file size in byte. this param is only for SEO
private $maxUrlLength = 767; // max URL length
private $malfChars = [' ']; // list of characters to detect malformed URLs following a standard good practice
private $multipleSitemaps = null; // when multiple sitemaps are avaialble is true
private $logPath = null; // log path
private $scriptVerNum = null; // version number of the script
private $dBaseVerNum = null; // version number of database
private $countUrlWithoutDesc = 0; // counter of URL without description
private $countUrlWithMultiDesc = 0; // counter of URL with multiple description
private $countUrlWithoutTitle = 0; // counter of URL without title
private $countUrlWithMultiTitle = 0; // counter of URL with multiple title
private $callerUrl = null; // caller URL of normal URL
private $skipCallerUrl = null; // caller URL of skipped URL

################################################################################
################################################################################
public function start(){

$this->prep();
$this->fullScan();
$this->closeCurlConn();
$this->writeLog('## Scan end'.PHP_EOL);
$this->end();

}
################################################################################
################################################################################
private function getPage($url){

curl_setopt($this->ch, CURLOPT_URL, $url);

$this->pageBody = curl_exec($this->ch);

if ($this->pageBody === false) {  
$this->writeLog('curl_exec failed (cURL error: '.curl_error($this->ch).') calling URL '.$url);  

$this->getErrCounter();

$this->pageBody = '';
$this->httpCode = 'C01';
$this->size = 0;
$this->md5 = md5($this->pageBody);
$this->lastmod = time();

return;
}

$this->httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);
if ($this->httpCode === false) {  
$this->writeLog('Execution has been stopped because of curl_getinfo failed calling URL '.$url);  

$this->stopExec();
}

$this->size = mb_strlen($this->pageBody, '8bit');

if ($this->size === false) {  
$this->writeLog('Execution has been stopped because of mb_strlen failed calling URL '.$url);  

$this->stopExec();
}

$this->md5 = md5($this->pageBody);
$this->lastmod = time();

}
################################################################################
################################################################################
private function pageTest($url){

$this->insUrl = true;

// if url is not into domain
if (strpos($url, DOMAINURL) !== 0) {
$this->insSkipUrl($url);
$this->insUrl = false;
return;
}

// if url is mailto
if (strpos($url, 'mailto') === 0) {
$this->insSkipUrl($url);
$this->insUrl = false;
return;
}

// if url is to skip
foreach ($this->skipUrl as $value){
if (strpos($url, $value) === 0) {
$this->insSkipUrl($url);
$this->insUrl = false;
return;
}
}

// if file is not to add
if ($url !== STARTURL) { // detect if URL is the starting URL to prevent false skip
$this->insUrl = false;

// skip URL for type if $fileToAdd is an array correctly valueted
if ($this->fileToAdd !== true) {
foreach ($this->fileToAdd as $value) {
$fileExt = $this->getUrlExt($url);

if ($value === $fileExt) {
$this->insUrl = true;
}
}
}
else {
$this->insUrl = true;
}

if ($this->insUrl === false) {
$this->insSkipUrl($url);
return;
}
}

}
################################################################################
################################################################################
// open mysqli connection
private function openMysqliConn(){

$this->mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);
if ($this->mysqli->connect_errno) {
$this->writeLog('Execution has been stopped because of MySQL database connection error: '
.$this->mysqli->connect_error.$this->txtToAddOnMysqliErr);   
exit();
}

if (!$this->mysqli->set_charset('utf8')) {
$this->writeLog('Execution has been stopped because of MySQL error loading character set utf8: '.lcfirst($this->mysqli->error));  

$this->stopExec();
} 

}
################################################################################
################################################################################
private function execQuery(){

// reset row
$this->row = [];
 
if (!($result = $this->mysqli->query($this->query))) { 
$this->writeLog('Execution has been stopped because of MySQL error. Error ('.$this->mysqli->errno.'): '
.$this->mysqli->error.' - query: "'.$this->query.'"'.$this->txtToAddOnMysqliErr);   
exit();
}

// write query warnings
if ($this->printMysqlWarn === true) {

if ($this->mysqli->warning_count > 0) {
if ($warnRes = $this->mysqli->query("SHOW WARNINGS")) {
$warnRow = $warnRes->fetch_row();

$warnMsg = sprintf("%s (%d): %s", $warnRow[0], $warnRow[1], lcfirst($warnRow[2]));
$this->writeLog($warnMsg.' - query: "'.$this->query.'"');   
        
$warnRes->close();
}
}
}

// if query is select....
if (strpos($this->query, 'SELECT') === 0) {

// if query is SELECT COUNT(*) AS count
if (strpos($this->query, 'SELECT COUNT(*) AS count') === 0) {
$row = $result->fetch_assoc();
$this->count = $row['count'];
}
else {
// the while below is faster than the equivalent for
$i = 0;
while ($row = $result->fetch_assoc()) {
$this->row[$i] = $row;
$i++;
}

$this->rowNum = $result->num_rows;
}

$result->free_result();
}
// else if query is show....
elseif (strpos($this->query, 'SHOW') === 0) {
$this->rowNum = $result->num_rows;
$result->free_result();
}

}
################################################################################
################################################################################
private function execMultiQuery(){

if ($this->mysqli->multi_query($this->query)) {
do {
if ($result = $this->mysqli->store_result()) {
$result->free_result();
}
} 
while ($this->mysqli->next_result());
}
else {$this->writeLog('Execution has been stopped because of MySQL multi_query error. Error ('
.$this->mysqli->errno.'): '.lcfirst($this->mysqli->error).' - query: '.$this->query.$this->txtToAddOnMysqliErr);   
exit();
}

if ($this->mysqli->errno) {
$this->writeLog('Execution has been stopped because of MySQL multi_query error. Error ('
.$this->mysqli->errno.'): '.lcfirst($this->mysqli->error).' - query: '.$this->query.$this->txtToAddOnMysqliErr);   
exit();
}

// write query warnings
if ($this->printMysqlWarn === true) {

if ($this->mysqli->warning_count > 0) {
if ($warnRes = $this->mysqli->query("SHOW WARNINGS")) {
$warnRow = $warnRes->fetch_row();

$warnMsg = sprintf("%s (%d): %s", $warnRow[0], $warnRow[1], lcfirst($warnRow[2]));
$this->writeLog($warnMsg.' - query: "'.$this->query.'"');   
        
$warnRes->close();
}
}
}

}
################################################################################
################################################################################
// close mysqli statements
private function closeMysqliStmt(){

if ($this->stmt->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt close error: '.lcfirst($this->mysqli->error));   

$this->stopExec();
}

if ($this->stmt2->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt2 close error: '.lcfirst($this->mysqli->error));  

$this->stopExec();
}

if ($this->stmt3->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt3 close error: '.lcfirst($this->mysqli->error));   

$this->stopExec();
}

if ($this->stmt4->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt4 close error: '.lcfirst($this->mysqli->error)); 

$this->stopExec();
}

if ($this->stmt5->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt5 close error: '.lcfirst($this->mysqli->error));   

$this->stopExec();
}

}
################################################################################
################################################################################
// close mysqli connection
private function closeMysqliConn(){

if ($this->mysqli->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL mysqli close error: '
.lcfirst($this->mysqli->error).$this->txtToAddOnMysqliErr);   
exit();
}

}
################################################################################
################################################################################
private function update(){

if ($this->row[0]['size'] > 0) { // to prevent error on empty page
$sizeDiff = abs($this->size - $this->row[0]['size']);

if ($this->row[0]['md5'] !== $this->md5) {
$newLastmod = $this->lastmod;
}
else {
$newLastmod = $this->row[0]['lastmod'];
}

$lastmodDiff = $this->lastmod - $this->row[0]['lastmod'];

// set changefreq weekly if lastmod date difference is more than 1 week
if ($lastmodDiff > 604799 && $lastmodDiff < 2678400) {
$this->changefreq = 'weekly';
}
// set changefreq monthly if lastmod date difference is more than 31 days
elseif ($lastmodDiff > 2678399 && $lastmodDiff < 31536000) {
$this->changefreq = 'monthly';
}
// set changefreq yearly if lastmod date difference is more than 365 days
elseif ($lastmodDiff > 31535999) {
$this->changefreq = 'yearly';
}

$this->lastmod = $newLastmod;
}

}
################################################################################
################################################################################
private function getHref($url){

$html = $this->pageBody;

// reset pageLinks
$this->pageLinks = [];

// return if httpCode is not 200 to prevent checking of failed pages
if ($this->httpCode !== 200) {
return;
}

// return if $html is empty to prevent error on $dom->loadHTML($html)
if (empty($html) === true) {
return;
}

// do not search links inside $doNotFollowLinksIn
foreach ($this->doNotFollowLinksIn as $value) {
$fileExt = $this->getUrlExt($url);

if ($value === $fileExt) {
return;
}
}

$dom = new DOMDocument;

if (@$dom->loadHTML($html) === false) {
$this->writeLog('DOMDocument parse error on URL '.$url);
}

// get all as
$as = $dom->getElementsByTagName('a'); 

// get all imgs
$imgs = $dom->getElementsByTagName('img'); 

 // get all scripts
$scripts = $dom->getElementsByTagName('script');

// get all links
$links = $dom->getElementsByTagName('link');

// get all iframes
$iframes = $dom->getElementsByTagName('iframe');

// get all videos
$videos = $dom->getElementsByTagName('video'); 

 // get all audios
$audios = $dom->getElementsByTagName('audio');

$titleArr = $dom->getElementsByTagName('title');
$titleCount = $titleArr->length;

if ($titleCount === 1) {
$title = $titleArr->item(0)->textContent;
$titleLength = strlen($title);

if ($titleLength > 300) {
$this->writeLog('Title length: '.$titleLength
.' characters (title has not been registered into dBase because of its length is more than 300 characters) - URL '.$url);
$title = null;
}
}
elseif ($titleCount > 1) {
$this->writeLog('There are '.$titleCount.' titles (title has not been registered into dBase because is not single) - URL '.$url);
$title = null;
$this->countUrlWithMultiTitle++;
}
elseif ($titleCount === 0) {
$this->writeLog('Title does not exist (SEO: title should be present) - URL '.$url);
$title = null;
$this->countUrlWithoutTitle++;
}

$metaArr = $dom->getElementsByTagName('meta');

$descriptionCount = 0;

foreach ($metaArr as $val) {
if (strtolower($val->getAttribute('name')) == 'description') {
$description = $val->getAttribute('content');
$descriptionCount++;
}
}

if ($descriptionCount === 1) {
$descriptionLength = strlen($description);

if ($descriptionLength > 300) {
$this->writeLog('Description length: '.$descriptionLength
.' characters (description has not been registered into dBase because of its length is more than 300 characters) - URL '.$url);
$description = null;
}
}
elseif ($descriptionCount > 1) {
$this->writeLog('There are '.$descriptionCount.' descriptions '
. '(description has not been registered into dBase because is not single) - URL '.$url);
$description = null;
$this->countUrlWithMultiDesc++;
}
elseif ($descriptionCount === 0) {
$this->writeLog('Description does not exist (SEO: description should be present) - URL '.$url);
$description = null;
$this->countUrlWithoutDesc++;
}

if ($this->stmt5->bind_param('sss', $title, $description, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.lcfirst($this->stmt5->error));  

$this->stopExec();
}

if ($this->stmt5->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.lcfirst($this->stmt5->error)); 

$this->stopExec();
}

// iterate over extracted links and display their URLs
foreach ($as as $a){

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $url;

// get absolute URL of href
$absHref = $this->getAbsoluteUrl($a->getAttribute('href'), $url);

// add only links to include
$this->pageTest($absHref);

if ($this->insUrl === true) {
$this->pageLinks[] = $absHref;
}
// print URL of the page that includes skipped URL into log
elseif (PRINTCONTAINEROFSKIPPED === true) {
$this->writeLog('Into '.$url.' skipped '.$absHref);
}
}

// iterate over extracted imgs and display their URLs
foreach ($imgs as $img){
// get absolute URL of image
$absImg = $this->getAbsoluteUrl($img->getAttribute('src'), $url);

// check if img title and img alt are present and length >= 1
if (strlen($img->getAttribute('title')) < 1){
$this->writeLog('Image without title: '.$absImg.' - URL: '.$url);
}

if (strlen($img->getAttribute('alt')) < 1){
$this->writeLog('Image without alt: '.$absImg.' - URL: '.$url);
}

// insert img URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absImg);
}

// iterate over extracted scripts and display their URLs
foreach ($scripts as $script){
$scriptSrc = $script->getAttribute('src');

// get absolute URL script src if src exits only (this is to prevent error when script does not have src)
if ($scriptSrc !== ''){
// get absolute URL of script
$absScript = $this->getAbsoluteUrl($scriptSrc, $url);

// insert acript URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absScript);
}
}

// iterate over extracted links and display their URLs
foreach ($links as $link){

// get absolute URL of link
$absLink = $this->getAbsoluteUrl($link->getAttribute('href'), $url);

// insert link URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absLink);
}

// iterate over extracted iframes and display their URLs
foreach ($iframes as $iframe){
// get absolute URL of iframe
$absIframe = $this->getAbsoluteUrl($iframe->getAttribute('src'), $url);

// insert iframe URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absIframe);
}

// iterate over extracted video and display their URLs
foreach ($videos as $video){
// get absolute URL of video
$absVideo = $this->getAbsoluteUrl($video->getAttribute('src'), $url);

// insert video URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absVideo);
}

// iterate over extracted audios and display their URLs
foreach ($audios as $audio){
// get absolute URL of audio
$absAudio = $this->getAbsoluteUrl($audio->getAttribute('src'), $url);

// insert audio URL as skipped...in that way the class will check http response code
$this->insSkipUrl($absAudio);
}

$this->pageLinks = array_unique($this->pageLinks);

}
################################################################################
################################################################################
public function end(){

// delete old records of previous full scan
$this->query = "DELETE FROM getSeoSitemap WHERE state = 'old'";
$this->execQuery();

$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap WHERE state != 'skip'";
$this->execQuery();
$this->writeLog($this->count.' scanned URLs (skipped URLs are not included - failed URls are included)');
$this->writeLog($this->countUrlWithoutTitle.' URLs without title (SEO: title should be present)');
$this->writeLog($this->countUrlWithMultiTitle.' URLs with multiple title (SEO: title should be single)');
$this->writeLog($this->countUrlWithoutDesc.' URLs without description (SEO: description should be present)');
$this->writeLog($this->countUrlWithMultiDesc.' URLs with multiple description (SEO: description should be single)');

if ($this->extUrlsTest === true) {
$this->openCurlConn();
$this->testExtUrls();
$this->closeCurlConn();
}

// close msqli statements
$this->closeMysqliStmt();

$this->query = "SELECT * FROM getSeoSitemap WHERE httpCode != '200' OR size = 0 ORDER BY url";
$this->execQuery();

if ($this->rowNum > 0) {
$this->writeLog('##### Failed URLs (external URLs are included)');

foreach ($this->row as $value) {
if ($value['httpCode'] !== '200') {

if (array_key_exists($value['httpCode'], $this->errMsg) === true) {
$logMsg = $this->errMsg[$value['httpCode']].' '.$value['httpCode'].' - URL: '.$value['url'].' - caller URL: '.$value['callerUrl'];
}
else {
$logMsg = 'Http code '.$value['httpCode'].' - URL: '.$value['url'].' - caller URL: '.$value['callerUrl'];
}

}
else {
$logMsg = 'Empty file: '.$value['url'];
}
$this->writeLog($logMsg);
}

$this->writeLog('##########');
}

$this->writeLog($this->rowNum.' failed URLs (external URLs are included)'.PHP_EOL);

// get total URLs to insert into sitemap
$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
."WHERE httpCode = '200' AND size != 0 AND state = 'scan'";
$this->execQuery();
$this->totUrls = $this->count;

// stop exec if total URLs to insert is higher than $maxTotalUrls
if ($this->totUrls > $this->maxTotalUrls) {
$this->writeLog("Execution has been stopped because of total URLs to insert into sitemap is $this->totUrls "
. "and higher than max limit of $this->maxTotalUrls"); 

$this->stopExec();
}

$this->writeLog('##### SEO');
$this->getSizeList();
$this->getMinTitleLengthList();
$this->getMaxTitleLengthList();
$this->getDuplicateTitle();
$this->getMinDescriptionLengthList();
$this->getMaxDescriptionLengthList();
$this->getDuplicateDescription();
$this->getIntUrls();
$this->setPriority();

// write changefreq into log
foreach ($this->changefreqArr as $value) {
$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
."WHERE changefreq = '$value' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$this->writeLog('Setted '.$value.' change frequency to '.$this->count.' URLs into sitemap');
}

// write lastmod min and max values into log
$this->query = "SELECT MIN(lastmod) AS minLastmod, MAX(lastmod) AS maxLastmod FROM getSeoSitemap "
."WHERE state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$minLastmodDate = date('Y.m.d H:i:s', $this->row[0]['minLastmod']);
$maxLastmodDate = date('Y.m.d H:i:s', $this->row[0]['maxLastmod']);
$this->writeLog('Min last modified time is '.$minLastmodDate);
$this->writeLog('Max last modified time is '.$maxLastmodDate.PHP_EOL);

// save all sitemaps
$this->save();

// gzip all sitemaps
foreach ($this->sitemapNameArr as $key => $value) {
$this->gzip($value);

$newValue = $value.'.gz';
$fileName = $this->getFileName($newValue);
$this->writeLog('Saved '.$fileName);

// updte filePath into array
$this->sitemapNameArr[$key] = $newValue;
}

// get full sitemap
$fullSitemapNameArr = $this->getSitemapNames();

// create an array of all sitemaps to delete
$sitemapToDeleteArr = array_diff($fullSitemapNameArr, $this->sitemapNameArr);

// delete old missing sitemaps
foreach ($sitemapToDeleteArr as $value) {
$this->delete($value);

$fileName = $this->getFileName($value);
$this->writeLog('Deleted '.$fileName.PHP_EOL);
}

$this->checkSitemapSize();

// set new sitemap is available
$this->newSitemapAvailable();

// rewrite robots.txt
if ($this->rewriteRobots === true ) {
$this->getRewriteRobots();
}

$this->getTotalUrls();
$this->getExtUrls();

// print type list if setted to true
if ($this->printTypeList === true) {
$this->getTypeList();
}

// print changefreq list if setted to true
if ($this->printChangefreqList === true) {
$this->getChangefreqList();
}

// print priority list if setted to true
if ($this->printPriorityList === true) {
$this->getPriorityList();
}

// print malformed list if setted to true
if ($this->printMalfUrls === true) {
$this->getMalfList();
}

// optimize tables
$this->optimTables();

$endTime = time();
$execTime = gmdate('H:i:s', $endTime - $this->startTime);

$this->writeLog('Total execution time '.$execTime);
$this->writeLog('##### Execution end'.PHP_EOL.PHP_EOL);

// update last execution time and set exec to n (a full scan has been successfully done) plus write version of getSeoSitemap
$this->query = "UPDATE getSeoSitemapExec "
. "SET version = '$this->version',  mDate = '$endTime', exec = 'n' WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

// close msqli connection
$this->closeMysqliConn();

}
################################################################################
################################################################################
private function resetVars(){

$this->resetVars2();

// reset row
$this->row = [];

}
################################################################################
################################################################################
private function resetVars2(){

$this->size = null; 
$this->md5 = null; 
$this->lastmod = null;
$this->changefreq = null; 
$this->state = null;
$this->httpCode = null;
$this->insUrl = null;
$this->pageBody = null; 

}
################################################################################
################################################################################
private function writeLog($logMsg) {

$msgLine = date('Y-m-d H:i:s').' - '.$logMsg.PHP_EOL;

if (file_put_contents($this->logPath, $msgLine, FILE_APPEND | LOCK_EX) === false) {
error_log('Execution has been stopped because of file_put_contents cannot write '.$this->logPath, 0);

$this->stopExec();
}

}
################################################################################
################################################################################
private function setPriority(){

$this->query = "UPDATE getSeoSitemap SET priority = '".DEFAULTPRIORITY."' WHERE state != 'skip'";
$this->execQuery();

foreach ($this->partialUrlPriority as $key => $value) {
foreach ($value as $v) {
$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
."WHERE url LIKE '".$v."%' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
}
}

foreach ($this->fullUrlPriority as $key => $value) {
foreach ($value as $v) {
$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
."WHERE url = '".$v."' AND state != 'skip' AND httpCode = '200' AND size != 0 LIMIT 1";
$this->execQuery();
}
}

// $priority includes all priority values
$priority = [];
$priority = array_merge(array_keys($this->partialUrlPriority), array_keys($this->fullUrlPriority));
$priority[] = DEFAULTPRIORITY;
rsort($priority);

foreach ($priority as $value) {
$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
."WHERE priority = '".$value."' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$this->writeLog("Setted priority ".$value." to ".$this->count." URLs into sitemap");
}

}
################################################################################
################################################################################
private function getTotalUrls() {

// count all kind of different URLs if $fileToAdd is an array
if ($this->fileToAdd !== true){
// if start url has not the extension file included into $fileToAdd wrote that separately...
$n = true;
foreach ($this->fileToAdd as $value) {
if (strpos(strrev(STARTURL), strrev($value)) === 0) {
$n = false;
}
}

if ($n === true) {
$this->writeLog('Included 1 start URL into sitemap');
}

foreach ($this->fileToAdd as $value) {
$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
."WHERE httpCode = '200' AND size != 0 AND url LIKE '%".$value."' AND state = 'scan'";
$this->execQuery();

$this->writeLog('Included '.$this->count.' '.$value.' URLs into sitemap');
}
}

$this->writeLog('################################');
$this->writeLog('Included '.$this->totUrls.' URLs into sitemap');
$this->writeLog('################################'.PHP_EOL);

}
################################################################################
################################################################################
private function newSitemapAvailable(){

$this->query = "UPDATE getSeoSitemapExec SET newData = 'y' WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

}
################################################################################
################################################################################
private function getIntUrls() {

$this->query = "SELECT url FROM getSeoSitemap WHERE state = 'skip' AND url LIKE '".DOMAINURL."%'";
$this->execQuery();

// print list of internal skipped URLs if PRINTINTSKIPURLS === true
if (PRINTINTSKIPURLS === true) {
$this->writeLog('##### Internal skipped URLs');

if ($this->rowNum > 0) {
asort($this->row);

foreach ($this->row as $value) {
$this->writeLog($value['url']);
}
}

$this->writeLog('##########');
}

$this->writeLog($this->rowNum.' internal skipped URLs');

}
################################################################################
################################################################################
private function getExtUrls() {

$this->query = "SELECT url FROM getSeoSitemap WHERE state = 'skip' AND url NOT LIKE '".DOMAINURL."%'";
$this->execQuery();

// print list of external skipped URLs
$this->writeLog('##### External skipped URLs');

if ($this->rowNum > 0) {
// sort ascending
asort($this->row);

foreach ($this->row as $value) {
$this->writeLog($value['url']);
}
}

$this->writeLog('##########');
$this->writeLog($this->rowNum.' external skipped URLs'.PHP_EOL);

}
################################################################################
################################################################################
private function testExtUrls() {

$this->query = "SELECT url FROM getSeoSitemap "
. "WHERE state = 'skip' AND url NOT LIKE '".DOMAINURL."%' AND url NOT LIKE 'mailto:%'";
$this->execQuery();

if ($this->rowNum > 0) {
$this->stmt = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "httpCode = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error)); 

$this->stopExec();
}

foreach ($this->row as $value) {
$url = $value['url'];
$this->getPage($url);

if ($this->stmt->bind_param('sss', $this->size, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.lcfirst($this->stmt->error));    

$this->stopExec();
}

if ($this->stmt->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.lcfirst($this->stmt->error)); 

$this->stopExec();
}
}
}

}
################################################################################
################################################################################
private function insNewUrl($url){

$this->resetVars();

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $this->callerUrl;

$this->pageTest($url);

if ($this->insUrl === true) {
$this->insUpdNewUrlQuery($url);
}

}
################################################################################
################################################################################
private function insUpdNewUrlQuery($url){

$this->checkUrlLength($url);
 
if ($this->stmt2->bind_param('sss', $url, $this->callerUrl, $this->callerUrl) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt2->error); 

$this->stopExec();
}

if ($this->stmt2->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt2->error); 

$this->stopExec();
}

}
################################################################################
################################################################################
private function linksScan(){

foreach ($this->pageLinks as $url) {
$this->insNewUrl($url);
}

}
################################################################################
################################################################################
private function scan($url){

$this->resetVars2();
$this->getPage($url);

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $this->callerUrl;

$this->pageTest($url);

if ($this->insUrl === true) {
$this->changefreq = 'daily';

$this->update();

if (
$this->stmt3->bind_param('ssssss', $this->size, $this->md5, $this->lastmod, $this->changefreq, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.lcfirst($this->stmt3->error)); 

$this->stopExec();
}

if ($this->stmt3->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.lcfirst($this->stmt3->error)); 

$this->stopExec();
}
}

}
################################################################################
################################################################################
private function insSkipUrl($url){

$this->checkUrlLength($url);
 
if ($this->stmt4->bind_param('sssssss', $url, $this->skipCallerUrl, $this->size, $this->httpCode, $this->skipCallerUrl, $this->size, $this->httpCode) !== true) { 

$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.lcfirst($this->stmt4->error)); 

$this->stopExec();
}

if ($this->stmt4->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.lcfirst($this->stmt4->error));  

$this->stopExec();
}

}
################################################################################
################################################################################
private function getChangefreqList(){

foreach ($this->changefreqArr as $value) {
$this->query = "SELECT url FROM getSeoSitemap "
. "WHERE changefreq = '$value' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$this->writeLog('##### URLs with '.$value.' change frequency into sitemap');

if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
$this->writeLog($v['url']);
}
}

$this->writeLog('##########'.PHP_EOL);
}

}
################################################################################
################################################################################
private function getPriorityList(){

foreach ($this->priorityArr as $value) {
$this->query = "SELECT url FROM getSeoSitemap WHERE priority = '".$value
."' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$this->writeLog('##### URLs with '.$value.' priority into sitemap');

if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
$this->writeLog($v['url']);
}
}

$this->writeLog('##########'.PHP_EOL);
}

}
################################################################################
################################################################################
private function getSizeList(){

$kbBingMaxSize = $this->getKb($this->pageMaxSize);

$this->query = "SELECT url, size FROM getSeoSitemap WHERE size > '".$this->pageMaxSize
."' AND state != 'skip' AND httpCode = '200'";
$this->execQuery();
$this->writeLog('##### URLs with size > '.$kbBingMaxSize.' Kb into sitemap (SEO: page size should be lower than '
.$kbBingMaxSize.' Kb)');

$i = 0;
if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
foreach ($this->seoExclusion as $value) {
$fileExt = $this->getUrlExt($v['url']);

if ($value !== $fileExt) {
$this->writeLog('Size: '.$this->getKb($v['size']).' Kb - URL: '.$v['url']);
$i++;
}
}
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with size > '.$kbBingMaxSize.' Kb into sitemap'.PHP_EOL);

}
################################################################################
################################################################################
// get Kb from byte rounded 2 decimals and formatted 2 decimals
private function getKb($byte){

return sprintf('%0.2f', round($byte / 1024, 2));

}
################################################################################
################################################################################
private function getMinTitleLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) < "
.$this->titleLength[0]." AND state != 'skip' AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;
if ($this->rowNum > 0){
$this->writeLog('##### URLs with title length < '.$this->titleLength[0]
.' characters into sitemap (SEO: page title length should be higher than '.$this->titleLength[0].' characters)');

asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value){
$fileExt = $this->getUrlExt($v['url']);

if ($value !== $fileExt) {
$this->writeLog('Title length: '.$v['titleLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with title length < '.$this->titleLength[0].' characters into sitemap');

}
################################################################################
################################################################################
private function getMaxTitleLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) > "
.$this->titleLength[1]." AND state != 'skip' AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;
if ($this->rowNum > 0){
$this->writeLog('##### URLs with title length > '.$this->titleLength[1]
.' characters into sitemap (SEO: page title length should be lower than '.$this->titleLength[1].' characters)');

asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value) {
$fileExt = $this->getUrlExt($v['url']);

if ($value !== $fileExt) {
$this->writeLog('Title length: '.$v['titleLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with title length > '.$this->titleLength[1].' characters into sitemap');

}
################################################################################
################################################################################
private function getDuplicateTitle(){

$this->query = "SELECT title FROM getSeoSitemap WHERE state != 'skip' AND httpCode = '200' AND"
." title IS NOT NULL GROUP BY title HAVING COUNT(*) > 1";
$this->execQuery();

$rowNum = $this->rowNum;
$row = $this->row;

$i = 0;

if ($rowNum > 0){
$this->writeLog('##### URLs with duplicate title into sitemap (SEO: URLs should have unique title into the website)');

asort($row);

foreach ($row as $v){
$this->query = "SELECT url, title FROM getSeoSitemap WHERE title = '"
.$v['title']."' AND state != 'skip' AND httpCode = '200'";
$this->execQuery();

foreach ($this->row as $v2){
$this->writeLog('Duplicate title: '.$v2['title'].' - URL: '.$v2['url']);
$i++;
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with duplicate title into sitemap');

}
################################################################################
################################################################################
private function getMinDescriptionLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(description) AS descriptionLength FROM getSeoSitemap WHERE CHAR_LENGTH(description) < "
.$this->descriptionLength[0]." AND state != 'skip' AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;
if ($this->rowNum > 0){
$this->writeLog('##### URLs with description length < '.$this->descriptionLength[0]
.' characters into sitemap (SEO: page description length should be higher than '.$this->descriptionLength[0].' characters)');

asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value){
$fileExt = $this->getUrlExt($v['url']);

if ($value !== $fileExt) {
$this->writeLog('Description length: '.$v['descriptionLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with description length < '.$this->descriptionLength[0].' characters into sitemap');

}
################################################################################
################################################################################
private function getMaxDescriptionLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(description) AS descriptionLength FROM getSeoSitemap WHERE CHAR_LENGTH(description) > "
.$this->descriptionLength[1]." AND state != 'skip' AND httpCode = '200' AND description IS NOT NULL";
$this->execQuery();

$i = 0;
if ($this->rowNum > 0){
$this->writeLog('##### URLs with description length > '.$this->descriptionLength[1]
.' characters into sitemap (SEO: page description length should be lower than '.$this->descriptionLength[1].' characters)');

asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value) {
$fileExt = $this->getUrlExt($v['url']);

if ($value !== $fileExt) {
$this->writeLog('Description length: '.$v['descriptionLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with description length > '.$this->descriptionLength[1].' characters into sitemap');

}
################################################################################
################################################################################
private function getDuplicateDescription(){

$this->query = "SELECT description FROM getSeoSitemap WHERE state != 'skip' AND httpCode = '200' AND"
." title IS NOT NULL GROUP BY title HAVING COUNT(*) > 1";
$this->execQuery();

$rowNum = $this->rowNum;
$row = $this->row;

$i = 0;

if ($rowNum > 0){
$this->writeLog('##### URLs with duplicate description into sitemap (SEO: URLs should have unique description into the website)');

asort($row);

foreach ($row as $v){
$this->query = "SELECT url, description FROM getSeoSitemap WHERE description = '"
.$v['description']."' AND state != 'skip' AND httpCode = '200'";
$this->execQuery();

foreach ($this->row as $v2){
$this->writeLog('Duplicate description: '.$v2['description'].' - URL: '.$v2['url']);
$i++;
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with duplicate description into sitemap'.PHP_EOL);

}
################################################################################
################################################################################
private function getTypeList(){

// print all kind of different URLs separately if $fileToAdd is an array. 
// or print all URLs that are into sitemap altogether in an alphaberic order.
if ($this->fileToAdd !== true) {
// if start url has not the extension file included into $fileToAdd wrote that separately...
$n = true;
foreach ($this->fileToAdd as $value){
if(strpos(strrev(STARTURL), strrev($value)) === 0) {
$n = false;
}
}
if ($n === true) {
$this->writeLog('##### Start URL into sitemap');
$this->writeLog(STARTURL);
$this->writeLog('##########'.PHP_EOL);
}

foreach ($this->fileToAdd as $value) {
$this->query = "SELECT url FROM getSeoSitemap WHERE httpCode = '200' AND size != 0 AND url LIKE '%"
.$value."' AND state = 'scan'";
$this->execQuery();

$this->writeLog('##### '.$value.' URLs into sitemap');

if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
$this->writeLog($v['url']);
}
}

$this->writeLog('##########'.PHP_EOL);
}
}
else {
$this->query = "SELECT url FROM getSeoSitemap WHERE httpCode = '200' AND size != 0 AND state = 'scan'";
$this->execQuery();

$this->writeLog('##### All URLs into sitemap');

if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
$this->writeLog($v['url']);
}
}

$this->writeLog('##########'.PHP_EOL);
}

}
################################################################################
################################################################################
// open curl connection
private function openCurlConn(){

$this->ch = curl_init();
curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent);

}
################################################################################
################################################################################
// close curl connection
private function closeCurlConn(){

curl_close($this->ch);

}
################################################################################
################################################################################
// update execution value
private function updateExec(){

$this->query = "UPDATE getSeoSitemapExec SET exec = '$this->exec' WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

}
################################################################################
################################################################################
// update error counter
private function getErrCounter(){

$this->errCounter++;

if ($this->errCounter >= $this->maxErr) {
$this->writeLog('Execution has been stopped because of errors are more than '.$this->maxErr);  

$this->stopExec();
}

}
################################################################################
################################################################################
// delete a file
private function delete($fileName){

$this->succ = false;

if (unlink($fileName) === false){
$this->writeLog('Execution has been stopped because of unlink cannot delete sitemap.xml'); 

$this->stopExec();
}

$this->succ = true;

}
################################################################################
################################################################################
// get URL entity escaping
private function entityEscaping($url){

foreach ($this->escapeCodeArr as $key => $value) {
$url = str_replace($key, $value, $url);
}

return $url;

}
################################################################################
################################################################################
private function save(){

$this->succ = false;

$this->query = "SELECT url, lastmod, changefreq, priority FROM getSeoSitemap "
."WHERE httpCode = '200' AND size != 0 AND state = 'scan'";
$this->execQuery();

// set sitemap counter start value
$sitemapCount = null;
if ($this->rowNum > $this->maxUrlsInSitemap) {
$sitemapCount = 1;
$this->multipleSitemaps = true;
}

 // general row counter + sitemap internal row counter
$genCount = $sitemapIntCount = 1;

foreach ($this->row as $value) {

if ($sitemapCount > $this->maxUrlsInSitemap) {
$this->writeLog('Execution has been stopped because total sitemaps are more than '.$this->maxUrlsInSitemap);  

$this->stopExec();
}

if ($sitemapIntCount === 1) {

$txt = <<<EOD
<?xml version='1.0' encoding='UTF-8'?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<!-- Created with $this->userAgent -->

EOD;

}

$dT = new DateTime();
$dT->setTimestamp($value['lastmod']);
$lastmod = $dT->format(DATE_W3C);

$url = $this->entityEscaping($value['url']);

$txt .= '<url><loc>'.$url.'</loc><lastmod>'.$lastmod.'</lastmod>'
.'<changefreq>'.$value['changefreq'].'</changefreq><priority>'.$value['priority'].'</priority></url>
';

if ($sitemapIntCount === $this->maxUrlsInSitemap || $genCount === $this->rowNum) {
$sitemapIntCount = 0;

$txt .= <<<EOD
</urlset>
EOD;

$sitemapFile = 'sitemap'.$sitemapCount.'.xml';

if (file_put_contents(SITEMAPPATH.$sitemapFile, $txt) === false) {
$this->writeLog('Execution has been stopped because of file_put_contents cannot write '.$sitemapFile); 

$this->stopExec();
}

$this->writeLog('Saved '.$sitemapFile);
$this->sitemapNameArr[] = SITEMAPPATH.$sitemapFile;

$utf8Enc = $this->detectUtf8Enc($txt);

if ($utf8Enc !== true) {
$this->writeLog('Execution has been stopped because of '.$sitemapFile.' is not UTF-8 encoded');  

$this->stopExec();
}

if ($this->multipleSitemaps === true && $genCount !== $this->rowNum) {
$sitemapCount++;
}

}

$sitemapIntCount++;
$genCount++;
}

// if there are multiple sitemaps, save sitemapindex 
if ($this->multipleSitemaps === true) {
$time = time();

$dT = new DateTime();
$dT->setTimestamp($time);
$lastmod = $dT->format(DATE_W3C);

$txt = <<<EOD
<?xml version='1.0' encoding='UTF-8'?>
<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<!-- Created with $this->userAgent -->

EOD;

foreach ($this->sitemapNameArr as $value) {
// get sitemap URL
$sitemapUrl = DOMAINURL.'/'.$this->getFileName($value).'.gz';

$txt .= '<sitemap><loc>'.$sitemapUrl.'</loc><lastmod>'.$lastmod.'</lastmod></sitemap>
';
}

$txt .= <<<EOD
</sitemapindex>
EOD;

$sitemapFile = 'sitemapindex.xml';

if (file_put_contents(SITEMAPPATH.$sitemapFile, $txt) === false) {
$this->writeLog('Execution has been stopped because of file_put_contents cannot write '.$sitemapFile); 

$this->stopExec();
}

$this->writeLog('Saved '.$sitemapFile);
$this->sitemapNameArr[] = SITEMAPPATH.$sitemapFile;

$utf8Enc = $this->detectUtf8Enc($txt);

if ($utf8Enc !== true) {
$this->writeLog('Execution has been stopped because of '.$sitemapFile.' is not UTF-8 encoded');  

$this->stopExec();
}

}

$this->succ = true;

}
################################################################################
################################################################################
private function gzip($fileName){

$this->succ = false;

$gzFile = $fileName.'.gz';

$fp = gzopen($gzFile, 'w9');

if ($fp === false){
$this->writeLog('Execution has been stopped because of gzopen cannot open '.$gzFile);   

$this->stopExec();
}

$fileCont = file_get_contents($fileName);
if ($fileCont === false){
$this->writeLog('Execution has been stopped because of file_get_contents cannot get content of '.$fileName);   

$this->stopExec();
}

gzwrite($fp, $fileCont);

if (gzclose($fp) !== true) {
$this->writeLog('Execution has been stopped because of gzclose cannot close '.$gzFile);   

$this->stopExec();
}  
else {
$this->succ = true;
}

}
################################################################################
################################################################################
// get file name without the rest of path
private function getFileName($filePath){

$this->succ = false;

$fileName = str_replace(SITEMAPPATH, '', $filePath);

$this->succ = true;
return $fileName;

}
################################################################################
################################################################################
// get all sitemap names included in SITEMAPPATH
private function getSitemapNames(){

$this->succ = false;

$sitemapNameArr = glob(SITEMAPPATH.'sitemap*.xml*');

if ($sitemapNameArr !== false) {
$this->succ = true;
return $sitemapNameArr;
}
else {
$this->writeLog('Execution has been stopped because of glob error');   

$this->stopExec();
}

}
################################################################################
################################################################################
// detect if enconding is UTF-8
private function detectUtf8Enc($str){

if (mb_detect_encoding($str, 'UTF-8', true) === 'UTF-8') {
return true;
}
else {
return false;
}

}
################################################################################
################################################################################
private function stopExec(){

$this->exec = 'n';
$this->updateExec();

exit();

}
################################################################################
################################################################################
// check if URL length is > $maxUrlLength
private function checkUrlLength($url){

$urlLength = strlen($url);
if ($urlLength > $this->maxUrlLength) {
$this->writeLog('Execution has been stopped because of length is > '.$this->maxUrlLength.' characters for URL: '.$url); 

$this->stopExec();
}

}
################################################################################
################################################################################
// get URL extension
private function getUrlExt($url){

$fileExt = '';

$parse = parse_url($url);

if ($parse !== false) {
if (isset($parse['path']) === true) {
$path = $parse['path'];
$fileExt = pathinfo($path, PATHINFO_EXTENSION);
}

return $fileExt;
}

}
################################################################################
################################################################################
// check all sitemap sizes. they must be non larger than $sitemapMaxSize
private function checkSitemapSize(){

$this->succ = false;

if ($this->printSitemapSizeList === true) {
$this->writeLog('##### Sitemap sizes');
}

foreach ($this->sitemapNameArr as $value) {
$fileName = $this->getFileName($value);

$size = filesize($value);

if ($size === false) {
$this->writeLog('Execution has been stopped because of filesize error checking '.$fileName);   

$this->stopExec();
}
elseif ($size > $this->sitemapMaxSize) {
$this->writeLog('Warnuing: size of '.$fileName.' is larger than '.$this->sitemapMaxSize.' - double-check that file to fix it!');
}

if ($this->printSitemapSizeList === true) {
$this->writeLog('Size: '.round($size * 0.0009765625, 2).' Kb - sitemap: '.$fileName);
}
}

if ($this->printSitemapSizeList === true) {
$this->writeLog('##########'.PHP_EOL);
}

$this->succ = true;

}
################################################################################
################################################################################
// rewrite robots.txt with new sitemap infos
private function getRewriteRobots(){

$file = 'robots.txt';
$filePath = SITEMAPPATH.$file;
$fileLines = [];

// if file exists
if (file_exists($filePath) === true) {
// get file line by line into an array
$fileLines = file($filePath, FILE_IGNORE_NEW_LINES);

if ($fileLines === false) {
$this->writeLog('Execution has been stopped because of file cannot read '.$file);   

$this->stopExec();
}

// remove all old sitemap lines from robots.txt
foreach ($fileLines as $key => $value) {
if ($value === '# Sitemap' || $value === '# Sitemapindex' || strpos($value, 'Sitemap: ') === 0) {
unset($fileLines[$key]);
}
}
}

if ($this->multipleSitemaps !== true) {
$fileLines[] = '# Sitemap';
$fileLines[] = 'Sitemap: '.DOMAINURL.'/sitemap.xml.gz';
}
else {
$fileLines[] = '# Sitemapindex';
$fileLines[] = 'Sitemap: '.DOMAINURL.'/sitemapindex.xml.gz';
}

$newCont = null;

// get new file content
foreach ($fileLines as $key => $value) {
$newCont .= $value.PHP_EOL;
}

// rewrite file
if (file_put_contents($filePath, $newCont) === false) {
$this->writeLog('Execution has been stopped because of file_put_contents cannot write '.$file);  

$this->stopExec();
}

$this->writeLog('Wrote '.$file);

}
################################################################################
################################################################################
// check tables
private function checkTables(){

$this->query = "SHOW TABLES LIKE 'getSeoSitemapExec'";
$this->execQuery();

if ($this->rowNum === 0) {

$this->query = "CREATE TABLE `getSeoSitemapExec` (
 `id` int(1) NOT NULL AUTO_INCREMENT,
 `func` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
 `version` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mDate` int(10) DEFAULT NULL COMMENT 'timestamp of last mod',
 `exec` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
 `step` int(2) NOT NULL DEFAULT '0' COMMENT 'passed step',
 `newData` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'set to y when new data are avaialble',
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `func` (`func`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='execution of getSeoSitemap functions'";
$this->execQuery();

$this->query = "INSERT INTO getSeoSitemapExec (func, mDate, exec, newData) 
SELECT 'getSeoSitemap', 0, 'n', 'n' FROM DUAL WHERE NOT EXISTS 
(SELECT func FROM getSeoSitemapExec WHERE func='getSeoSitemap')";
$this->execQuery();
}
elseif ($this->rowNum === 1) {
$this->getDbaseVerNum();

if ($this->dBaseVerNum < 310) {
$this->query = "SHOW COLUMNS FROM getSeoSitemapExec WHERE FIELD = 'step'";
$this->execQuery();

if ($this->rowNum === 0) {
$this->query = "ALTER TABLE getSeoSitemapExec ADD COLUMN step int(2) NOT NULL DEFAULT '0' COMMENT 'passed step' AFTER exec";
$this->execQuery();
}
}
}

$this->query = "SHOW TABLES LIKE 'getSeoSitemap'";
$this->execQuery();

if ($this->rowNum === 0) {
$this->query = "CREATE TABLE `getSeoSitemap` (
 `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
 `url` varbinary(767) NOT NULL,
 `callerUrl` varbinary(767),
 `size` int(10) unsigned NOT NULL COMMENT 'byte',
 `title` text COLLATE utf8_unicode_ci,
 `description` text COLLATE utf8_unicode_ci,
 `md5` char(32) COLLATE utf8_unicode_ci NOT NULL,
 `lastmod` int(10) unsigned NOT NULL,
 `changefreq` enum('daily','weekly','monthly','yearly') COLLATE utf8_unicode_ci NOT NULL,
 `priority` enum('0.1','0.2','0.3','0.4','0.5','0.6','0.7','0.8','0.9','1.0') COLLATE utf8_unicode_ci DEFAULT NULL,
 `state` enum('new','scan','skip','old') COLLATE utf8_unicode_ci NOT NULL,
 `httpCode` char(3) COLLATE utf8_unicode_ci NOT NULL,
 PRIMARY KEY (`id`),
 UNIQUE KEY `url` (`url`),
 KEY `state` (`state`),
 KEY `httpCode` (`httpCode`),
 KEY `size` (`size`),
 KEY `changefreq` (`changefreq`),
 KEY `priority` (`priority`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
$this->execQuery();
}
elseif ($this->rowNum === 1) {
$this->getDbaseVerNum();

if ($this->dBaseVerNum < 330) {
$this->query = "SHOW COLUMNS FROM getSeoSitemap WHERE FIELD = 'callerUrl'";
$this->execQuery();

if ($this->rowNum === 0) {
$this->query = "ALTER TABLE getSeoSitemap ADD COLUMN callerUrl varbinary(767) AFTER url";
$this->execQuery();
}
}
}

}
################################################################################
################################################################################
// optimize tables
private function optimTables(){

// remove gaps in id primary key of getSeoSitemap
$this->query = "SET @count = 0; "
. "UPDATE getSeoSitemap SET id = @count := @count + 1";
$this->execMultiQuery();

// optimize getSeoSitemap
$this->query = "OPTIMIZE TABLE getSeoSitemap";
$this->execQuery();

$this->writeLog('Optimized getSeoSitemap table');  

}
################################################################################
################################################################################
private function getMalfList(){

$i = 0;

foreach ($this->malfChars as $value) {
$this->query = "SELECT url FROM getSeoSitemap WHERE url LIKE '%".$value
."%' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();

if ($this->rowNum > 0) {
$this->writeLog("##### URLs with '$value' malformed character into sitemap (good pratice - do not use that character in URL address)");

asort($this->row);
foreach ($this->row as $v) {
$this->writeLog($v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with malformed characters into sitemap'.PHP_EOL);
}

}
################################################################################
################################################################################
// get number from version (examples: v12.2 => 1220, v11.2.2 => 1122, v3.1.1 => 311, v3.1 => 310)
private function getVerNum($ver){

// return digits only
$verNum = filter_var($ver, FILTER_SANITIZE_NUMBER_INT);

if ($verNum === false) {
$this->writeLog("Execution has been stopped because of filter_var cannot filter value '".$ver."'"); 

$this->stopExec();
}

$mainNo = substr($ver, 1, 2);

if (ctype_digit($mainNo) === true) {
$digits = 4;
}
else{
$digits = 3;
}

$verNum = str_pad($verNum, $digits, '0');

return $verNum;

}
################################################################################
################################################################################
// get version number of the script
private function getScriptVerNum(){

$this->scriptVerNum = $this->getVerNum($this->version);

}
################################################################################
################################################################################
// get version number of database
private function getDbaseVerNum(){

$this->query = "SELECT version FROM getSeoSitemapExec WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

$this->dBaseVerNum = $this->getVerNum($this->row[0]['version']);

}
################################################################################
################################################################################
private function fullScan(){

do {
$this->query = "SELECT url, size, md5, lastmod FROM getSeoSitemap WHERE state = 'new' LIMIT 1";
$this->execQuery();
$rowNum = $this->rowNum;

// if there is almost 1 record into getSeoSitemap table with state new....
if ($rowNum === 1){ 
$this->url = $this->row[0]['url'];
$url = $this->url;

$this->scan($url);
$this->getHref($url);

$this->callerUrl = $url;

$this->linksScan();

if ($this->stmt->bind_param('s', $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt->error); 

$this->stopExec();
}

if ($this->stmt->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt->error); 

$this->stopExec();
}
}

}
while ($rowNum === 1);

}
################################################################################
################################################################################
private function prep(){

$time = time();

// set log path: it will remain the same from the start to the end of execution
$this->logPath = GETSITEMAPPATH.'log/'.date('Ymd', $time).'.log';

// set start time
$this->startTime = $time;

// set version in userAgent
$this->userAgent = str_replace('ver.', $this->version, $this->userAgent);

// open mysqli connection
$this->openMysqliConn();

$this->query = "SELECT exec FROM getSeoSitemapExec WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

// check if getSeoSitemp is already running and stop it to prevent double execution
if ($this->row[0]['exec'] === 'y') {
$this->writeLog('An error has occoured: execution has been stopped; '
.'maybe the previous scan was not ended correctly. Double-check log to fix it.'.$this->txtToAddOnMysqliErr);

exit();
}
// check if prevous full scan was ended to start a new full scan
elseif ($this->row[0]['exec'] === 'n') {
$this->writeLog('## getSeoSitemap '.$this->version);
$this->writeLog('## Execution start');
}
else {
$this->writeLog('Value of state in getSeoSitemapExec table is not correct: '
.'execution has been stopped. Double-check log to fix it.'.$this->txtToAddOnMysqliErr);

exit();
}

// set execution of function to y
$this->exec = 'y';
$this->updateExec();

// check tables into dbase
$this->checkTables();

// update all states to old to be ready for the new full scan
$this->query = "UPDATE getSeoSitemap SET state = 'old'";
$this->execQuery();

$this->writeLog('## Scan start');

// prepare mysqli statements
$this->prepMysqliStmt();

// insert or update STARTURL
$this->insUpdNewUrlQuery(STARTURL);

$this->openCurlConn();

}
################################################################################
################################################################################
// prepare mysqli statements
private function prepMysqliStmt(){

$this->stmt = $this->mysqli->prepare("UPDATE getSeoSitemap SET state = 'scan' WHERE url = ? LIMIT 1");
if ($this->stmt === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error));  

$this->stopExec();
}

$this->stmt2 = $this->mysqli->prepare("INSERT INTO getSeoSitemap (url, callerUrl, state) VALUES (?, ?, 'new') "
."ON DUPLICATE KEY UPDATE state = IF(state = 'old', 'new', state), callerUrl = ?");

if ($this->stmt2 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error)); 

$this->stopExec();
}

$this->stmt3 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "md5 = ?, "
. "lastmod = ?, "
. "changefreq = ?, "
. "httpCode = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt3 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error));

$this->stopExec();
}

$this->stmt4 = $this->mysqli->prepare("INSERT INTO getSeoSitemap ("
. "url, "
. "callerUrl, "
. "size, "
. "md5, "
. "lastmod, "
. "changefreq, "
. "priority, "
. "state, "
. "httpCode) "
. "VALUES ("
. "?, "
. "?, "
. "?, "
. "'', "
. "'', "
. "'', "
. "NULL, "
. "'skip', "
. "?) "
. "ON DUPLICATE KEY UPDATE "
. "callerUrl = ?, "
. "size = ?, "
. "md5 = '', "
. "lastmod = 0, "
. "changefreq = '', "
. "priority = NULL, "
. "state = 'skip', "
. "httpCode = ?");

if ($this->stmt4 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error));   

$this->stopExec();
}

$this->stmt5 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "title = ?, "
. "description = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt5 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.lcfirst($this->mysqli->error));

$this->stopExec();
}

}
################################################################################
################################################################################
// update step
private function updateStep($step){

$this->query = "UPDATE getSeoSitemapExec SET step = '$step' WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

}
################################################################################
################################################################################
// get absolute url from relative url
private function getAbsoluteUrl($relativeUrl, $baseUrl){

// if already absolute URL 
if (parse_url($relativeUrl, PHP_URL_SCHEME) !== null){
return $relativeUrl;
}

// queries and anchors
if ($relativeUrl[0] === '#' || $relativeUrl[0] === '?'){
return $baseUrl.$relativeUrl;
}

// parse base URL and convert to: $scheme, $host, $path, $query, $port, $user, $pass
extract(parse_url($baseUrl));

// if base URL contains a path remove non-directory elements from $path
if (isset($path) === true){
$path = preg_replace('#/[^/]*$#', '', $path);
}
else {
$path = '';
}

// if relative URL starts with //
if (substr($relativeUrl, 0, 2) === '//'){
return $scheme.':'.$relativeUrl;
}

// if relative URL starts with /
if ($relativeUrl[0] === '/'){
$path = null;
}

$abs = null;

// if relative URL contains a user
if (isset($user) === true){
$abs .= $user;

// if relative URL contains a password
if (isset($pass) === true){
$abs .= ':'.$pass;
}

$abs .= '@';
}

$abs .= $host;

// if relative URL contains a port
if (isset($port) === true){
$abs .= ':'.$port;
}

$abs .= $path.'/'.$relativeUrl.(isset($query) === true ? '?'.$query : null);

// replace // or /./ or /foo/../ with /
$re = ['#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'];
for ($n = 1; $n > 0; $abs = preg_replace($re, '/', $abs, -1, $n)) {
}

// return absolute URL
return $scheme.'://'.$abs;

}
################################################################################
################################################################################
}

$gS = new getSeoSitemap();
$gS->start();
