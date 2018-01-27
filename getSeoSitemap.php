<?php

/*
getSeoSitemap v2.2 LICENSE (2018-01-23)

getSeoSitemap v2.2 is distributed under the following BSD-style license: 

Copyright (c) 2016-2018, 
Giovanni Bertone (RED Racing Parts) - https://www.redracingparts.com
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
const DOMAINURL = 'https://www.example.com'; // domain url (value must be absolute) - every URL must contain this value at the beginning
const STARTURL = 'https://www.example.com'; // starting url to crawl (value must be absolute)
const DEFAULTPRIORITY = '0.5'; // default priority for URLs not included in $fullUrlPriority and $partialUrlPriority
const DBHOST = DATABASE_HOST_I; // database host
const DBUSER = DATABASE_USER_I; // database user
const DBPASS = DATABASE_PASSWORD_I; // database password
const DBNAME = DATABASE_NAME_I; // database name
const GETSITEMAPPATH = '/example/example/example/example/example/example/example/getSeoSitemap/'; // getSeoSitemap path inside server
const SITEMAPPATH = '/example/example/example/example/example/example/'; // sitemap.xml plus sitemap.xml.gz path inside server
const SITEMAPURL = 'https://www.example.com/sitemap.xml.gz'; // sitemap url (value must be absolute) 
const PRINTINTSKIPURLS = false; // set to false if you do not want the list of internal skipped URLs in your log file
const PRINTCONTAINEROFSKIPPED = false; // set to true to get a list of container URLs of skipped URLs. It is useful to fix wrong URLs.
const BINGMAXSIZE = '125.00'; // bing max file size in Kb. this param is only for SEO.
##### end of user constants

class getSeoSitemap {

##### start of user parameters
private $skipUrl = [ // skip all urls that start or are equal these values (values must be absolute)
'https://www.example.com/shop/',
'https://www.example.com/example/motorbikesmotorcycles/productsandcomponents/general/intro/google_site_search.php',
'https://www.example.com/example/motocicli/prodottiecomponenti/generale/intro/google_site_search.php',
'https://www.example.com/example/currency.php',
];
// set $fileToAdd to true to follow and add all kind of URL.
// set $fileToAdd to an array to follow and add only some kind of URLs (example: $fileToAdd = ['php','pdf',];).
private $fileToAdd = [
'php',
'pdf',
];
// priority values must be 1.0, 0.9, 0.8, 0.7, 0.6, 0.5, 0.4, 0.3, 0.2 and 0.1. other values are not accepted.
private $fullUrlPriority = [ // set priority for specific urls that are equal these values (values must be absolute)
'1.0' => [
'https://www.example.com'
],
'0.9' => [
'https://www.example.com/example/motorbikesmotorcycles/introducingpages/11/22/hotproducts.php',
'https://www.example.com/example/motocicli/pagineintroduttive/11/22/hotproducts.php'
],
];
private $partialUrlPriority = [ // set priority for specific urls that start with these values (values must be absolute)
'0.8' => [
'https://www.example.com/example/motorbikesmotorcycles/introducingpages/11/22/',
'https://www.example.com/example/motocicli/pagineintroduttive/11/22/',
],
'0.7' => [
'https://www.example.com/example/motocicli/prodottiecomponenti/generale/intro/',
'https://www.example.com/example/motorbikesmotorcycles/productsandcomponents/general/intro/',
],
];
private $printChangefreqList = false; // set to true to print URLs list following changefreq
private $printPriorityList = false; // set to true to print URLs list following priority
private $printTypeList = false; // set to true to print URLs list following type                                                                                                             
private $extUrlsTest = true; // set to false to skip external URLs test (default value is true).                   
##### end of user parameters

#################################################
##### WARNING: DO NOT CHANGE ANYTHING BELOW #####
#################################################

private $url = null; // an aboslute url (ex. https://www.example.com/test/test1.php )
private $size = null; // size of file in Kb
private $bingTitleLength = [5, 100]; // min, max bing title length
private $md5 = null; // md5 of string (hexadecimal)
private $changefreq = null; // change frequency of file (values: always, hourly, daily, weekly, monthly, yearly, never)
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
private $userAgent = 'getSeoSitemap v2.2 by John';
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

################################################################################
public function start(){

$time = time();

// set start time
$this->startTime = $time;

// open mysqli connection
$this->openMysqliConn();

// check tables into dbase
$this->checkTables();

$this->query = "SELECT exec FROM getSeoSitemapExec WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

// check if getSeoSitemp is already running and stop it to prevent double execution
if ($this->row[0]['exec'] === 'y') {
$this->writeLog('An error has occoured: execution has been stopped; '
. 'maybe the previous scan was not ended correctly. Double-check log to fix it.');
exit();
}
// check if prevous full scan was ended to start a new full scan
elseif ($this->row[0]['exec'] === 'n') {
$this->writeLog('## Execution start');
}
else {
$this->writeLog('Value of state in getSeoSitemapExec table is not correct: '
. 'execution has been stopped. Double-check log to fix it.');
exit();
}

// set function execution to y
$this->exec = 'y';
$this->updateExec();

// update all states to old to be ready for the new full scan
$this->query = "UPDATE getSeoSitemap SET state = 'old'";
$this->execQuery();

$this->writeLog('## Scan start');

$this->stmt2 = $this->mysqli->prepare("INSERT INTO getSeoSitemap (url, state) VALUES (?, 'new') "
. "ON DUPLICATE KEY UPDATE state = IF(state = 'old', 'new', state)");
if ($this->stmt2 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

$this->stmt3 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "md5 = ?, "
. "lastmod = ?, "
. "changefreq = ?, "
. "httpCode = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt3 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.');

$this->exec = 'n';
$this->updateExec();

exit();
}

$this->stmt4 = $this->mysqli->prepare("INSERT INTO getSeoSitemap ("
. "url, "
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
. "'', "
. "'', "
. "'', "
. "NULL, "
. "'skip', "
. "?) "
. "ON DUPLICATE KEY UPDATE "
. "size = ?, "
. "md5 = '', "
. "lastmod = 0, "
. "changefreq = '', "
. "priority = NULL, "
. "state = 'skip', "
. "httpCode = ?");
if ($this->stmt4 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.');   

$this->exec = 'n';
$this->updateExec();

exit();
}

$this->stmt5 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "title = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt5 === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.');

$this->exec = 'n';
$this->updateExec();

exit();
}

// insert or update STARTURL
$this->insUpdNewUrlQuery(STARTURL);

$this->openCurlConn();

$this->stmt = $this->mysqli->prepare("UPDATE getSeoSitemap SET state = 'scan' WHERE url = ? LIMIT 1");
if ($this->stmt === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.');  

$this->exec = 'n';
$this->updateExec();

exit();
}

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
$this->linksScan();

if ($this->stmt->bind_param('s', $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt->error.'.');  

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt->error.'.');   

$this->exec = 'n';
$this->updateExec();

exit();
}

}

}
while ($rowNum === 1);

$this->closeCurlConn();
$this->writeLog('## Scan end');
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

$this->exec = 'n';
$this->updateExec();

exit();
}

$this->size = round(mb_strlen($this->pageBody, '8bit') * 0.0009765625, 2);
if ($this->size === false) {  
$this->writeLog('Execution has been stopped because of mb_strlen failed calling URL '.$url);  

$this->exec = 'n';
$this->updateExec();

exit();
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

$fileExt = pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION);
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
$this->writeLog('Execution has been stopped because of MySQL database connection error: '.$this->mysqli->connect_error);   

$this->exec = 'n';
$this->updateExec();

exit();
}

if (!$this->mysqli->set_charset('utf8')) {
$this->writeLog('Execution has been stopped because of MySQL error loading character set utf8: '.$this->mysqli->error);   

$this->exec = 'n';
$this->updateExec();

exit();
} 

}
################################################################################
################################################################################
private function execQuery(){

// reset row
$this->row = [];

$result = $this->mysqli->query($this->query); 
if (!$result) {  
$this->writeLog('Execution has been stopped because of MySQL query error: '.$this->mysqli->error.' - query: '.$this->query);  

$this->exec = 'n';
$this->updateExec();

exit();
}

// if query is select....
if (strpos($this->query, 'SELECT') === 0) {

// if query is select COUNT(*) AS count
if (strpos($this->query, 'SELECT COUNT(*) AS count') === 0) {
$row = $result->fetch_assoc();
$this->count = $row['count'];
}
else {

// i choose the while below cause it is just a little bit faster than the equivalent for
$i = 0;
while ($row = $result->fetch_assoc()) {
$this->row[$i] = $row;
$i++;
}

$this->rowNum = $result->num_rows;
}
}

}
################################################################################
################################################################################
// close mysqli connection
private function closeMysqliConn(){

$this->stmt->close();
$this->stmt2->close();
$this->stmt3->close();
$this->stmt4->close();
$this->stmt5->close();
$this->mysqli->close();

}
################################################################################
################################################################################
private function update(){

if ($this->row[0]['size'] > 0) { // to prevent error on empty page
$sizeDiff = abs($this->size - $this->row[0]['size']);

$newLastmod = $this->row[0]['lastmod'];

if ($this->row[0]['md5'] !== $this->md5) {
$newLastmod = $this->lastmod;
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

// return if $html is empty to prevent error on $dom->loadHTML($html)
if (empty($html) === true){return;}

// do not search links inside $doNotFollowLinksIn
foreach ($this->doNotFollowLinksIn as $value) {
$fileExt = pathinfo(parse_url($url)['path'], PATHINFO_EXTENSION);
if ($value === $fileExt) {
return;
}
}

$dom = new DOMDocument;

if (@$dom->loadHTML($html) === false) {
$this->writeLog('DOMDocument parse error on URL '.$url);
}

$links = $dom->getElementsByTagName('a'); // get all links

$titleArr = $dom->getElementsByTagName('title');
$titleCount = $titleArr->length;

if ($titleCount === 1) {
$title = $titleArr->item(0)->textContent;
$titleLength = strlen($title);

if ($titleLength > 300) {
$this->writeLog('Title length: '.$titleLength.' characters (title has not been registered into dBase because its lenght is more than 300 characters) - URL '.$url);
$title = null;
}
}
elseif ($titleCount > 1) {
$this->writeLog('Title No.: '.$titleCount.' (title has not been registered into dBase because is not single) - URL '.$url);
$title = null;
}
elseif ($titleCount === 0) {
$this->writeLog('Title does not exist - URL '.$url);
$title = null;
}

if ($this->stmt5->bind_param('ss', $title, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt5->error.'.');   

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt5->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt5->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

foreach ($links as $link){ // iterate over extracted links and display their URLs
$href = $link->getAttribute('href');// extract href attribute

// add only link to include
$this->pageTest($href);
if ($this->insUrl === true) {
$this->pageLinks[] = $href;
}
// print URL of the page that includes skipped URL into log
elseif (PRINTCONTAINEROFSKIPPED === true) {
$this->writeLog('Into '.$url.' skipped '.$href);
}
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
$this->writeLog($this->count.' scanned URLs (skipped URLs are not included - failed URls are included)'.PHP_EOL);

if ($this->extUrlsTest === true) {
$this->openCurlConn();
$this->testExtUrls();
$this->closeCurlConn();
}

$this->query = "SELECT * FROM getSeoSitemap WHERE httpCode != '200' OR size = 0 ORDER BY url";
$this->execQuery();

if ($this->rowNum > 0) {
$this->writeLog('##### Failed URLs (external URLs are included)');

foreach ($this->row as $value) {
if ($value['httpCode'] !== '200') {

if (array_key_exists($value['httpCode'], $this->errMsg) === true) {
$logMsg = $this->errMsg[$value['httpCode']].' '.$value['httpCode'].' - URL: '.$value['url'];
}

else {
$logMsg = 'Http code '.$value['httpCode'].' - URL: '.$value['url'];
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

$this->writeLog('##### SEO');
$this->getSizeList();
$this->getMinTitleLengthList();
$this->getMaxTitleLengthList();
$this->getDuplicateTitle();

$this->getIntUrls();
$this->setPriority();

// write changefreq into log
foreach ($this->changefreqArr as $value) {
$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
. "WHERE changefreq = '$value' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$this->writeLog('Setted '.$value.' change frequency to '.$this->count.' URLs into sitemap');
}

// write lastmod min and max values into log
$this->query = "SELECT MIN(lastmod) AS minLastmod, MAX(lastmod) AS maxLastmod FROM getSeoSitemap "
. "WHERE state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
$minLastmodDate = date('Y.m.d H:i:s', $this->row[0]['minLastmod']);
$maxLastmodDate = date('Y.m.d H:i:s', $this->row[0]['maxLastmod']);
$this->writeLog('Min last modified time is '.$minLastmodDate);
$this->writeLog('Max last modified time is '.$maxLastmodDate);

// save sitemap.xml
$this->succ = false;
$this->save();
if ($this->succ === true) {
$this->writeLog('## Saved sitemap.xml');
}

// save back copy of sitemap.xml.gz
$this->succ = false;
if (file_exists(SITEMAPPATH.'sitemap.xml.gz') === true) {
$this->copy(SITEMAPPATH.'sitemap.xml.gz', SITEMAPPATH.'sitemap.back.xml.gz');
if ($this->succ === true) {
$this->writeLog('## Saved sitemap.back.xml.gz');
}
}
else {
$this->writeLog('## Previous sitemap.xml.gz does not exist and sitemap.back.xml.gz has not been saved. '
. 'It might be the first time you run getSeoSitemap.');
}

// save sitemap.xml.gz
$this->succ = false;
$this->gzip();
if ($this->succ === true) {
$this->writeLog('## Saved sitemap.xml.gz');
}

// delete sitemap.xml
$this->succ = false;
$this->delete();
if ($this->succ === true) {
$this->writeLog('## Deleted sitemap.xml');
}

// set new sitemap is available
$this->newSitemapAvailable();

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

$endTime = time();
$execTime = gmdate('H:i:s', $endTime - $this->startTime);

$this->writeLog('Total execution time '.$execTime);
$this->writeLog('##### Execution end');

// update last execution time and set exec to n (that means a full scan has been successfully done)
$this->query = "UPDATE getSeoSitemapExec SET mDate = '".$endTime."', exec = 'n' WHERE func = 'getSeoSitemap' LIMIT 1";
$this->execQuery();

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
private function save(){

$this->query = "SELECT url, lastmod, changefreq, priority FROM getSeoSitemap "
. "WHERE httpCode = '200' AND size != 0 AND state = 'scan'";
$this->execQuery();

$fp = fopen(SITEMAPPATH.'sitemap.xml','w');
if ($fp === false){
$this->writeLog('Execution has been stopped because fopen cannot open sitemap.xml');    

$this->exec = 'n';
$this->updateExec();

exit();
}

$txt = <<<EOD
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<!-- Created with $this->userAgent -->

EOD;

foreach ($this->row as $value){
$dT = new DateTime();
$dT->setTimestamp($value['lastmod']);
$lastmod = $dT->format(DATE_W3C);

$url = $this->entityEscaping($value['url']);

$txt .= '<url><loc>'.$url.'</loc><lastmod>'.$lastmod.'</lastmod>'
. '<changefreq>'.$value['changefreq'].'</changefreq><priority>'.$value['priority'].'</priority></url>
';
}

$txt .= <<<EOD
</urlset>
EOD;

if (fwrite($fp, $txt) === false){
$this->writeLog('Execution has been stopped because fwrite cannot write sitemap.xml');  

$this->exec = 'n';
$this->updateExec();

exit();
}

if (fclose($fp) !== true){
$this->writeLog('Execution has been stopped because fclose cannot close sitemap.xml'); 

$this->exec = 'n';
$this->updateExec();

exit();
}
else {$this->succ = true;}

}
################################################################################
################################################################################
private function gzip(){

$file = SITEMAPPATH.'sitemap.xml';
$gzfile = $file.'.gz';

$fp = gzopen($gzfile, 'w9');

if ($fp === false){
$this->writeLog('Execution has been stopped because gzopen cannot open sitemap.xml.gz');   

$this->exec = 'n';
$this->updateExec();

exit();
}

$fileCont = file_get_contents($file);
if ($fileCont === false){
$this->writeLog('Execution has been stopped because file_get_contents cannot get content of sitemap.xml');   

$this->exec = 'n';
$this->updateExec();

exit();
}

gzwrite($fp, $fileCont);

if (gzclose($fp) !== true) {
$this->writeLog('Execution has been stopped because gzclose cannot close sitemap.xml.gz');   

$this->exec = 'n';
$this->updateExec();

exit();
}  
else {
$this->succ = true;
}

}
################################################################################
################################################################################
private function writeLog($logMsg) {

$fp = fopen(GETSITEMAPPATH.'log/'.date('Ymd').'.log','a');    

$msgLine = date('Y-m-d H:i:s').' - '.$logMsg.PHP_EOL;

fwrite($fp, $msgLine);  
fclose($fp);

}
################################################################################
################################################################################
private function setPriority(){

$this->query = "UPDATE getSeoSitemap SET priority = '".DEFAULTPRIORITY."' WHERE state != 'skip'";
$this->execQuery();

foreach ($this->partialUrlPriority as $key => $value) {
foreach ($value as $v) {
$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
. "WHERE url LIKE '".$v."%' AND state != 'skip' AND httpCode = '200' AND size != 0";
$this->execQuery();
}
}

foreach ($this->fullUrlPriority as $key => $value) {
foreach ($value as $v) {
$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
. "WHERE url = '".$v."' AND state != 'skip' AND httpCode = '200' AND size != 0 LIMIT 1";
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
. "WHERE priority = '".$value."' AND state != 'skip' AND httpCode = '200' AND size != 0";
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
. "WHERE httpCode = '200' AND size != 0 AND url LIKE '%".$value."' AND state = 'scan'";
$this->execQuery();

$this->writeLog('Included '.$this->count.' '.$value.' URLs into sitemap');
}
}

$this->query = "SELECT COUNT(*) AS count FROM getSeoSitemap "
. "WHERE httpCode = '200' AND size != 0 AND state = 'scan'";
$this->execQuery();
$this->writeLog('Included '.$this->count.' URLs into sitemap'.PHP_EOL);

}
################################################################################
################################################################################
private function copy($file, $newFile){

$fileName = basename($file);  

if (file_exists($file) === true) {

if (copy($file, $newFile) !== true) {
$this->writeLog('Back copy of the previous '.$fileName.' has not been saved and execution has been stopped');
exit();
}
else {
$this->succ = true;
}

}
else {
$this->writeLog('Back copy of the previous '.$fileName.' has not been saved because this file does not exist '
. '(probably this is the first time you are using getSeoSitemap)');
}

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

$this->query = "SELECT url FROM getSeoSitemap WHERE state = 'skip' AND url NOT LIKE '".DOMAINURL."%' AND url NOT LIKE 'mailto:%'";
$this->execQuery();

if ($this->rowNum > 0) {
$this->stmt = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "httpCode = ? "
. "WHERE url = ? LIMIT 1");
if ($this->stmt === false) {  
$this->writeLog('Execution has been stopped because of MySQL prepare error: '.$this->mysqli->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

foreach ($this->row as $value) {
$url = $value['url'];
$this->getPage($url);

if ($this->stmt->bind_param('sss', $this->size, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt->error.'.');    

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

}
}

}
################################################################################
################################################################################
private function insNewUrl($url){

$this->resetVars();
$this->pageTest($url);

if ($this->insUrl === true) {
$this->insUpdNewUrlQuery($url);
}

}
################################################################################
################################################################################
private function insUpdNewUrlQuery($url){

if ($this->stmt2->bind_param('s', $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt2->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt2->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt2->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

}
################################################################################
################################################################################
private function linksScan(){

foreach ($this->pageLinks as $url){$this->insNewUrl($url);}

}
################################################################################
################################################################################
private function scan($url){

$this->resetVars2();
$this->getPage($url);

$this->pageTest($url);

if ($this->insUrl === true) {
$this->changefreq = 'daily';

$this->update();

if ($this->stmt3->bind_param('ssssss', $this->size, $this->md5, $this->lastmod, $this->changefreq, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt3->error.'.');   

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt3->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt3->error.'.'); 

$this->exec = 'n';
$this->updateExec();

exit();
}

}

}
################################################################################
################################################################################
private function insSkipUrl($url){

if ($this->stmt4->bind_param('sssss', $url, $this->size, $this->httpCode, $this->size, $this->httpCode) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL error binding parameters: '.$this->stmt4->error.'.');  

$this->exec = 'n';
$this->updateExec();

exit();
}

if ($this->stmt4->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL execute error: '.$this->stmt4->error.'.');  

$this->exec = 'n';
$this->updateExec();

exit();
}

}
################################################################################
################################################################################
private function getChangefreqList(){

foreach ($this->changefreqArr as $value) {
$this->query = "SELECT url FROM getSeoSitemap WHERE changefreq = '$value' AND state != 'skip' AND httpCode = '200' AND size != 0";
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
$this->query = "SELECT url FROM getSeoSitemap WHERE priority = '".$value."' AND state != 'skip' AND httpCode = '200' AND size != 0";
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

$this->query = "SELECT url, size FROM getSeoSitemap WHERE size > '".BINGMAXSIZE."' AND state != 'skip' AND httpCode = '200'";
$this->execQuery();
$this->writeLog('##### URLs with size > '.BINGMAXSIZE.' Kb into sitemap (Bing SEO)');

$i = 0;
if ($this->rowNum > 0) {
asort($this->row);
foreach ($this->row as $v) {
foreach ($this->seoExclusion as $value) {
$fileExt = pathinfo(parse_url($v['url'])['path'], PATHINFO_EXTENSION);

if ($value !== $fileExt) {
$this->writeLog('Size: '. $v['size'].' Kb - URL: '.$v['url']);
$i++;
}
}
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with size > '.BINGMAXSIZE.' Kb into sitemap (Bing SEO)'.PHP_EOL);

}
################################################################################
################################################################################
private function getMinTitleLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) < ".$this->bingTitleLength[0]." AND state != 'skip' AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();
$this->writeLog('##### URLs with title length < '.$this->bingTitleLength[0].' characters into sitemap (Bing SEO)');

$i = 0;
if ($this->rowNum > 0){
asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value){
$fileExt = pathinfo(parse_url($v['url'])['path'], PATHINFO_EXTENSION);

if ($value !== $fileExt) {
$this->writeLog('Title length: '. $v['titleLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with title length < '.$this->bingTitleLength[0].' characters into sitemap (Bing SEO)'.PHP_EOL);

}
################################################################################
################################################################################
private function getMaxTitleLengthList(){

$this->query = "SELECT url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) > ".$this->bingTitleLength[1]." AND state != 'skip' AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();
$this->writeLog('##### URLs with title length > '.$this->bingTitleLength[1].' characters into sitemap (Bing SEO)');

$i = 0;
if ($this->rowNum > 0){
asort($this->row);
foreach ($this->row as $v){
foreach ($this->seoExclusion as $value){
$fileExt = pathinfo(parse_url($v['url'])['path'], PATHINFO_EXTENSION);

if ($value !== $fileExt) {
$this->writeLog('Title length: '. $v['titleLength'].' characters - URL: '.$v['url']);
$i++;
}
}
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with title length > '.$this->bingTitleLength[1].' characters into sitemap (Bing SEO)'.PHP_EOL);

}
################################################################################
################################################################################
private function getDuplicateTitle(){

$this->query = "SELECT title FROM getSeoSitemap WHERE state != 'skip' AND httpCode = '200' AND title IS NOT NULL GROUP BY title HAVING COUNT(*) > 1";
$this->execQuery();

$rowNum = $this->rowNum;
$row = $this->row;

$this->writeLog('##### URLs with duplicate title into sitemap (Google SEO)');

$i = 0;

if ($rowNum > 0){
asort($row);

foreach ($row as $v){
$this->query = "SELECT url, title FROM getSeoSitemap WHERE title = '".$v['title']."' AND state != 'skip' AND httpCode = '200'";
$this->execQuery();

foreach ($this->row as $v2){
$this->writeLog('Duplicate title: '. $v2['title'].' - URL: '.$v2['url']);
$i++;
}
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with duplicate title into sitemap (Google SEO)'.PHP_EOL);

}
################################################################################
################################################################################
private function getTypeList(){

// print all kind of different URLs separately if $fileToAdd is an array. 
// otherwise print all URLs that are into sitemap altogether in an alphaberic order.
if ($this->fileToAdd !== true){
// if start url has not the extension file included into $fileToAdd wrote that separately...
$n = true;
foreach ($this->fileToAdd as $value){
if(strpos(strrev(STARTURL), strrev($value)) === 0){$n = false;}
}
if ($n === true){
$this->writeLog('##### Start URL into sitemap');
$this->writeLog(STARTURL);
$this->writeLog('##########'.PHP_EOL);
}

foreach ($this->fileToAdd as $value) {
$this->query = "SELECT url FROM getSeoSitemap WHERE httpCode = '200' AND size != 0 AND url LIKE '%".$value."' AND state = 'scan'";
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

$this->exec = 'n';
$this->updateExec();
exit();
}

}
################################################################################
################################################################################
// delete sitemap.xml
private function delete(){

if (unlink(SITEMAPPATH.'sitemap.xml') === false){
$this->writeLog('Execution has been stopped because unlink cannot delete sitemap.xml');    

$this->exec = 'n';
$this->updateExec();

exit();
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
// check tables
private function checkTables(){

$this->query = "CREATE TABLE IF NOT EXISTS `getSeoSitemapExec` (
 `id` int(1) NOT NULL AUTO_INCREMENT,
 `func` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
 `mDate` int(10) DEFAULT NULL COMMENT 'timestamp of last mod',
 `exec` varchar(1) COLLATE utf8_unicode_ci DEFAULT NULL,
 `newData` varchar(1) COLLATE utf8_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'set to y when new data are avaialble',
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `func` (`func`),
 KEY `exec` (`exec`),
 KEY `newData` (`newData`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='execution of getSeoSitemap functions'";
$this->execQuery();

$this->query = "INSERT INTO getSeoSitemapExec (func, mDate, exec, newData) 
SELECT 'getSeoSitemap', 0, 'n', 'n' FROM DUAL WHERE NOT EXISTS 
(SELECT func FROM getSeoSitemapExec WHERE func='getSeoSitemap')";
$this->execQuery();

$this->query = "CREATE TABLE IF NOT EXISTS `getSeoSitemap` (
 `id` smallint(6) NOT NULL AUTO_INCREMENT,
 `url` varbinary(330) NOT NULL,
 `size` decimal(7,2) NOT NULL,
 `title` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
 `description` varchar(320) COLLATE utf8_unicode_ci DEFAULT NULL,
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
) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci";
$this->execQuery();

}
################################################################################
}

$gS = new getSeoSitemap();
$gS->start();
