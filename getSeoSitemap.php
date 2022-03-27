<?php

/*
getSeoSitemap v4.1.2 LICENSE | 2022-03-28

getSeoSitemap v4.1.2 is distributed under the following BSD-style license: 

Copyright (c) 2017-2022
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

//###################################################################################################
//# Please support this project by making a donation via PayPal to https://www.paypal.me/johnbe4 or #
//# with BTC bitcoin to the address 19928gKpqdyN6CHUh4Tae1GW9NAMT6SfQH                              #
//###################################################################################################

//#################################################
//##### WARNING: DO NOT CHANGE ANYTHING BELOW #####
//#################################################

require 'config.php';

class getSeoSitemap {

private $version = 'v4.1.2';
private $userAgent = 'getSeoSitemapBot/ver.';
private $scriptTitle = 'getSeoSitemap ver. by John';
private $url = null; // an aboslute URL ( ex. https://www.example.com/test/test1.php )
private $size = 0; // size of file in Kb
private $titleLength = [5, 100]; // min, max title length
private $descriptionLength = [50, 160]; // min, max description length
private $md5 = null; // md5 of string (hexadecimal)
private $changefreq = null; // change frequency of file (values: daily, weekly, monthly, yearly)
private $lastmod = null; // timestamp of last modified date of URL
private $state = null; // state of URL
/*
state values:
old = URL of previous scan
new = new URL to scan
scan = new URL already scanned
skip = new generic skipped URL (out of domain, video, image, iframe, audio and link)
mSkip = new skipped URL cause of mailto
rSkip = new skipped URL cause of robots.txt rules
niSkip = new no-index URL cause of robots meta rules
nfSkip = new no-follow URL cause of robots meta rules
noSkip = new no-index / no-follow URL cause of robots meta rules
*/
private $insUrl = null;
private $mysqli = null; // mysqli connection
private $ch = null; // curl connection
private $row = []; // array that includes row from query
private $pageLinks = []; // it includes all links inside a page
private $pageBody = null; // the page including header
private $httpCode = null; // the http response code
private $contentType = null; // the header content-type
private $rowNum = null; // number of rows into dbase
private $count = null; // count of rows (ex. 125)
private $query = null; // query
private $stmt = null; // statement for prepared query
private $stmt2 = null; // statement 2 for prepared query
private $stmt3 = null; // statement 3 for prepared query
private $stmt4 = null; // statement 4 for prepared query
private $stmt5 = null; // statement 5 for prepared query
private $stmt6 = null; // statement 6 for prepared query
private $stmt7 = null; // statement 7 for prepared query
private $startTime = null; // start timestamp
private $followExclusion = [ // do not follow links inside these file content types
'application/pdf',
];
private $seoExclusion = [ // file content types out of seo
'application/pdf',
'application/javascript',
'text/javascript'
];
private $indexExclusion = [ // file content types out of sitemap
'application/javascript',
'text/javascript'
];
private $changefreqArr = ['daily', 'weekly', 'monthly', 'yearly']; // changefreq accepted values
private $priorityArr = ['1.0', '0.9', '0.8', '0.7', '0.6', '0.5', '0.4', '0.3', '0.2', '0.1']; // priority accepted values
private $exec = 'n'; // execution value (could be y or n)
private $errCounter = 0; // error counter
private $maxErr = 20; // max number of errors to stop execution
private $getWarn = true; // print mysql warnings when true
private $warnCounter = 0; // warning counter
private $maxWarn = 100; // max number of warnings to print to prevent too long log
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
private $totUrls = null; // total URLs at the end 
private $sitemapMaxSize = 52428800; // max sitemap size (bytes)
private $sitemapNameArr = []; // includes names of all saved sitemaps at the end of the process
private $txtToAddOnMysqliErr = ' - fix it remembering to set exec => n on getSeoSitemapExec table.'; // additional error text
private $maxUrlLength = 767; // max URL length
private $maxPageSize = 16777215; // max page size | bytes
private $seoMaxPageSize = 135168; // page max file size in byte. this param is only for SEO
private $malfChars = [' ']; // list of characters to detect malformed URLs following a standard good practice
private $multipleSitemaps = null; // when multiple sitemaps are avaialble is true
private $logPath = null; // log path
private $skipUrl = []; // URLs to skip
private $allowUrl = []; // URLs to allow
private $robotsPath = null; // robots.txt path
private $robotsLines = []; // robots.txt lines
private $countUrlWithoutDesc = 0; // counter of URLs without description
private $countUrlWithMultiDesc = 0; // counter of URLs with multiple description
private $countUrlWithoutTitle = 0; // counter of URLs without title
private $countUrlWithMultiTitle = 0; // counter of URLs with multiple title
private $countUrlWithoutH1 = 0; // counter of URLs without h1
private $countUrlWithMultiH1 = 0; // counter of URLs with multiple h1
private $countUrlWithoutH2 = 0; // counter of URLs without h2
private $countUrlWithoutH3 = 0; // counter of URL without h3
private $callerUrl = null; // caller URL of normal URL
private $skipCallerUrl = null; // caller URL of skipped URL
private $countQuery = 0; // counter of queries with INSERT, UPDATE or DELETE on getSeoSitemap table
private $optimTimes = 500000; // exec optimize of getSeoSitemap table every x $countQuery
private $titDesLen = 250; // max title / description length to save (characters)

//################################################################################
//################################################################################
public function start(){

$this->prep();
$this->fullScan();
$this->closeCurlConn();
$this->writeLog('Scan end'.PHP_EOL);
$this->end();

}
//################################################################################
//################################################################################
private function curlExec($url, $att){

$this->pageBody = curl_exec($this->ch);

if ($this->pageBody === false) {  
$this->writeLog('curl_exec failed on '.$att.'° attempt - cURL error: '.curl_error($this->ch).' - URL: '.$url);  

$this->getErrCounter();

$this->pageBody = '';
$this->httpCode = 'C01';
$this->size = 0;
$this->md5 = md5($this->pageBody);
$this->lastmod = time();

return;
}

$header = curl_getinfo($this->ch);

if ($header === false) {  
$this->writeLog('Execution has been stopped because of curl_getinfo failed - URL: '.$url);  
$this->stopExec();
}

$this->httpCode = $header['http_code'];
$this->contentType = $header['content_type'];
$this->size = $header['size_download'];
$this->md5 = md5($this->pageBody);
$this->lastmod = time();

}
//################################################################################
//################################################################################
private function getPage($url){

curl_setopt($this->ch, CURLOPT_URL, $url);

$this->curlExec($url, 1);

if ($this->httpCode !== 200){
usleep(5000000); // 5 sec
$this->curlExec($url, 2);
}

}
//################################################################################
//################################################################################
private function pageTest($url){

$this->insUrl = true;

// if mailto URL
if (strpos($url, 'mailto') === 0) {
$this->insSkipUrl($url, 'mSkip');
$this->insUrl = false;

return;
}

//### the 'if elseif below' is faster than two 'if + return'

// if out of domain URL
if (strpos($url, DOMAINURL) !== 0) {
$this->insSkipUrl($url, 'skip');
$this->insUrl = false;
}
// if robots skipped URL
elseif ($this->robotsSkipTest($url) === true) {
$this->insSkipUrl($url, 'rSkip');
$this->insUrl = false;
}

}
//################################################################################
//################################################################################
// open mysqli connection
private function openMysqliConn(){

$this->mysqli = new mysqli(DBHOST, DBUSER, DBPASS, DBNAME);

if ($this->mysqli->connect_errno !== 0) {
$this->writeLog('Execution has been stopped because of MySQL database connection error: '
.$this->mysqli->connect_error);

exit();
}

if ($this->mysqli->set_charset('utf8mb4') === false) {
$this->writeLog('Execution has been stopped because of MySQL error loading character set utf8mb4: '.lcfirst($this->mysqli->error));  
$this->stopExec();
} 

}
//################################################################################
//################################################################################
private function execQuery(){

// reset row
$this->row = [];

if (($result = $this->mysqli->query($this->query)) === false) {
$this->writeLog('Execution has been stopped because of MySQL error. Error ('.$this->mysqli->errno.'): '
.$this->mysqli->error.' - query: "'.$this->query.'"'.$this->txtToAddOnMysqliErr);   
exit();
}

// if query is select....
if (strpos($this->query, 'SELECT') === 0) {

// if query is SELECT SQL_NO_CACHE COUNT(*) AS count
if (strpos($this->query, 'SELECT SQL_NO_CACHE COUNT(*) AS count') === 0) {
$row = $result->fetch_assoc();
$this->count = $row['count'];
}
else {
$i = 0;

// this while is faster than the equivalent for
while (($row = $result->fetch_assoc()) !== null) {
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
//################################################################################
//################################################################################
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

if ($this->stmt7->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt7 close error: '.lcfirst($this->mysqli->error));   
$this->stopExec();
}

}
//################################################################################
//################################################################################
// close mysqli connection
private function closeMysqliConn(){

if ($this->mysqli->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL mysqli close error: '
.lcfirst($this->mysqli->error).$this->txtToAddOnMysqliErr); 

exit();
}

}
//################################################################################
//################################################################################
private function update(){

// to prevent error on empty page
if ($this->row[0]['size'] > 0) { 

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
//################################################################################
//################################################################################
private function getIndexFollowSeo($url){

// return if httpCode !== 200 (to prevent checking of failed pages) or 
// if $this->pageBody is empty (to prevent error on $dom->loadHTML($this->pageBody))
if ($this->httpCode !== 200 || empty($this->pageBody) === true) {
return;
}

$index = $this->getExclusion($this->contentType, $this->indexExclusion);
$follow = $this->getExclusion($this->contentType, $this->followExclusion);
$seo = $this->getExclusion($this->contentType, $this->seoExclusion);

$dom = new DOMDocument;

if (@$dom->loadHTML($this->pageBody) === false) {
$this->writeLog('DOMDocument parse error on URL '.$url);

return;
}

$descriptionCount = 0;

foreach ($dom->getElementsByTagName('meta') as $val) {
$valGetAttName = strtolower($val->getAttribute('name'));

if ($valGetAttName === 'robots') {
$valGetAttContent = $val->getAttribute('content');

switch (strtolower($valGetAttContent)) {

case 'noindex':
$index = false;
break;

case 'nofollow':
$follow = false;
break;

case 'none':
$index = $follow = $seo = false;
break;

case 'noindex, nofollow':
$index = $follow = $seo = false;
break;

default:
$this->writeLog('Content of robots tag is not included in the list: content '.$valGetAttContent.' - URL '.$url);
}

}
elseif ($valGetAttName === 'description') {
$description = $val->getAttribute('content');
$descriptionCount++;
}
}

if ($index === false && $follow === false && $seo === false){
$this->insSkipUrl($url, 'noSkip');
return;
}

if ($index === false && $follow === false){
$this->insSkipUrl($url, 'noSkip');
}
elseif ($index === false) {
$this->insSkipUrl($url, 'niSkip'); 
}
elseif ($follow === false) {
$this->insSkipUrl($url, 'nfSkip'); 
}

//### seo start
if ($seo === true) {
$skipUrl = [];

// count h1
$h1Count = $dom->getElementsByTagName('h1')->length;

if ($h1Count > 1) {
$this->writeLog('There are '.$h1Count.' h1 (SEO: h1 should be single) - URL '.$url);
$this->countUrlWithMultiH1++;
}
elseif ($h1Count === 0) {
$this->writeLog('H1 does not exist (SEO: h1 should be present) - URL '.$url);
$this->countUrlWithoutH1++;
}

if (CHECKH2 === true){
// count h2
if ($dom->getElementsByTagName('h2')->length === 0) {
$this->writeLog('H2 does not exist (SEO: h2 should be present) - URL '.$url);
$this->countUrlWithoutH2++;
}
}

if (CHECKH3 === true){
// count h3
if ($dom->getElementsByTagName('h3')->length === 0) {
$this->writeLog('H3 does not exist (SEO: h3 should be present) - URL '.$url);
$this->countUrlWithoutH3++;
}
}

$titleArr = $dom->getElementsByTagName('title');
$titleCount = $titleArr->length;

if ($titleCount === 1) {
$title = $titleArr->item(0)->textContent;
$titleLength = strlen($title);

if ($titleLength > $this->titDesLen) {
$this->writeLog('Title length: '.$titleLength
.' characters (title has not been registered into dBase because of its length is more than '.$this->titDesLen.' characters) - URL '.$url);
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

if ($descriptionCount === 1) {
$descriptionLength = strlen($description);

if ($descriptionLength > $this->titDesLen) {
$this->writeLog('Description length: '.$descriptionLength
.' characters (description has not been registered into dBase because of its length is more than '.$this->titDesLen.' characters) - URL '.$url);
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

$this->optimCheck();

// update title and description
if ($this->stmt5->bind_param('sss', $title, $description, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt5 bind_param error: '.lcfirst($this->stmt5->error));  
$this->stopExec();
}

if ($this->stmt5->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt5 execute error: '.lcfirst($this->stmt5->error)); 
$this->stopExec();
}

$this->showWarnings();

// iterate over extracted imgs and display their URLs
foreach ($dom->getElementsByTagName('img') as $img){

// get absolute URL of image
$absImg = $this->getAbsoluteUrl($img->getAttribute('src'), $url, 'img-src');
$skipUrl[] = $absImg;

// check if img title and img alt are present and length >= 1
if (strlen($img->getAttribute('title')) < 1){
$this->writeLog('Image without title: '.$absImg.' - URL: '.$url);
}

if (strlen($img->getAttribute('alt')) < 1){
$this->writeLog('Image without alt: '.$absImg.' - URL: '.$url);
}
}

// iterate over extracted links and display their URLs
foreach ($dom->getElementsByTagName('link') as $link){
$skipUrl[] = $this->getAbsoluteUrl($link->getAttribute('href'), $url, 'link-href');
}

// iterate over extracted iframes and display their URLs
foreach ($dom->getElementsByTagName('iframe') as $iframe){
$skipUrl[] = $this->getAbsoluteUrl($iframe->getAttribute('src'), $url, 'iframe-src');
}

// iterate over extracted video and display their URLs
foreach ($dom->getElementsByTagName('video') as $video){
$skipUrl[] = $this->getAbsoluteUrl($video->getAttribute('src'), $url, 'video-src');
}

// iterate over extracted audios and display their URLs
foreach ($dom->getElementsByTagName('audio') as $audio){
$skipUrl[] = $this->getAbsoluteUrl($audio->getAttribute('src'), $url, 'audio-src');
}

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $url;

// array_filter removes empty / false field
foreach (array_filter($skipUrl) as $v) {
$this->insSkipUrl($v, 'skip');
}
}
//### seo end

//### follow start
if ($follow === true){
// reset pageLinks
$this->pageLinks = [];

// iterate over extracted links and display their URLs
foreach ($dom->getElementsByTagName('a') as $a) {
$this->pageLinks[] = $this->getAbsoluteUrl($a->getAttribute('href'), $url, 'a-href');
}

// iterate over extracted forms and get their action URLs
foreach ($dom->getElementsByTagName('form') as $form){

// check and scan form with get method only
if ($form->getAttribute('method') === 'get'){
$this->pageLinks[] = $this->getAbsoluteUrl($form->getAttribute('action'), $url, 'get-method-action');
}
}

// iterate over extracted scripts and display their URLs
foreach ($dom->getElementsByTagName('script') as $script){
$scriptSrc = $script->getAttribute('src');

// get absolute URL script src if src exits only (this is to prevent error when script does not have src)
if ($scriptSrc !== ''){
$absScript = $this->getAbsoluteUrl($scriptSrc, $url, 'script-src');
$this->pageLinks[] = $absScript;
}
}

$this->pageLinks = array_unique(array_filter($this->pageLinks));
}
//### follow end

}
//################################################################################
//################################################################################
private function end(){

// delete old records of previous full scan
$this->query = "DELETE FROM getSeoSitemap WHERE state = 'old'";
$this->execQuery();

$this->writeLog('Deleted old URLs');

$this->query = "SELECT SQL_NO_CACHE COUNT(*) AS count FROM getSeoSitemap";
$this->execQuery();

$this->writeLog($this->count.' scanned URLs');
$this->writeLog($this->countUrlWithoutTitle.' URLs without title into domain | SEO: title should be present');
$this->writeLog($this->countUrlWithMultiTitle.' URLs with multiple title into domain | SEO: title should be single');
$this->writeLog($this->countUrlWithoutDesc.' URLs without description into domain | SEO: description should be present');
$this->writeLog($this->countUrlWithMultiDesc.' URLs with multiple description into domain | SEO: description should be single');
$this->writeLog($this->countUrlWithoutH1.' URLs without h1 into domain | SEO: h1 should be present');
$this->writeLog($this->countUrlWithMultiH1.' URLs with multiple h1 into domain | SEO: h1 should be single');

if (CHECKH2 === true){
$this->writeLog($this->countUrlWithoutH2.' URLs without h2 into domain | SEO: h2 should be present');
}

if (CHECKH3 === true){
$this->writeLog($this->countUrlWithoutH3.' URLs without h3 into domain | SEO: h3 should be present');
}

$this->openCurlConn();
$this->checkSkipUrls();
$this->closeCurlConn();

// close msqli statements
$this->closeMysqliStmt();

$this->query = "SELECT SQL_NO_CACHE * FROM getSeoSitemap WHERE httpCode != '200' AND state != 'mSkip' ORDER BY url";
$this->execQuery();

if ($this->rowNum > 0) {
$this->writeLog('##### Failed URLs | They are not included into sitemap');

foreach ($this->row as $value) {
if (array_key_exists($value['httpCode'], $this->errMsg) === true) {
$logMsg = $this->errMsg[$value['httpCode']].' '.$value['httpCode'].' - URL: '.$value['url'].' - caller URL: '.$value['callerUrl'];
}
else {
$logMsg = 'Http code '.$value['httpCode'].' - URL: '.$value['url'].' - caller URL: '.$value['callerUrl'];
}

$this->writeLog($logMsg);
}

$this->writeLog('##########');
}

$this->writeLog($this->rowNum.' failed URLs | They are not included into sitemap'.PHP_EOL);

$this->setPriority();
$this->optimTables();

$this->writeLog('##### SEO');
$this->getSizeList();
$this->getMinTitleLengthList();
$this->getMaxTitleLengthList();
$this->getDuplicateTitle();
$this->getMinDescriptionLengthList();
$this->getMaxDescriptionLengthList();
$this->getDuplicateDescription();
$this->getIntUrls();
$this->getPriority();

// write changefreq into log
foreach ($this->changefreqArr as $value) {

$this->query = "SELECT SQL_NO_CACHE COUNT(*) AS count FROM getSeoSitemap "
. "WHERE changefreq = '$value' AND state NOT IN ('skip', 'mSkip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
$this->execQuery();

$this->writeLog('Set '.$value.' change frequency to '.$this->count.' URLs into sitemap');
}

// write lastmod min and max values into log
$this->query = "SELECT SQL_NO_CACHE MIN(lastmod) AS minLastmod, MAX(lastmod) AS maxLastmod FROM getSeoSitemap "
. "WHERE state NOT IN ('skip', 'mSkip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
$this->execQuery();

$minLastmodDate = date('Y.m.d H:i:s', $this->row[0]['minLastmod']);
$maxLastmodDate = date('Y.m.d H:i:s', $this->row[0]['maxLastmod']);
$this->writeLog('Min last modified time into sitemap is '.$minLastmodDate);
$this->writeLog('Max last modified time into sitemap is '.$maxLastmodDate.PHP_EOL);

// save all sitemaps
if ($this->save() !== true){
$this->writeLog('Execution has been stopped because of save error');   
$this->stopExec();
}

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
$fullSitemapNameArr = $this->getSitemapPaths(SITEMAPPATH);

// create an array of all sitemaps to delete
$sitemapToDeleteArr = array_diff($fullSitemapNameArr, $this->sitemapNameArr);

// delete old missing sitemaps
foreach ($sitemapToDeleteArr as $value) {
$this->delete($value);

$fileName = $this->getFileName($value);
$this->writeLog('Deleted '.$fileName);
}

if ($this->checkSitemapSize() !== true){
$this->writeLog('Execution has been stopped because of checkSitemapSize error');   
$this->stopExec();
}

// set new sitemap is available
$this->newSitemapAvailable();

// rewrite robots.txt
$this->getRewriteRobots();

$this->getTotalUrls();
$this->getExtUrls();

// print type list if setted to true
if (PRINTTYPELIST === true) {
$this->getTypeList();
}

// print changefreq list if setted to true
if (PRINTCHANGEFREQLIST === true) {
$this->getChangefreqList();
}

// print priority list if setted to true
if (PRINTPRIORITYLIST === true) {
$this->getPriorityList();
}

// print malformed list if setted to true
if (PRINTMALFURLS === true) {
$this->getMalfList();
}

$endTime = time();

$this->writeLog('Total execution time '.gmdate('H:i:s', $endTime - $this->startTime));
$this->writeLog('##### Execution end'.PHP_EOL.PHP_EOL);

// update last execution params on getSeoSitemapExec
$this->query = "UPDATE getSeoSitemapExec "
. "SET version = '$this->version', mDate = '$endTime', exec = 'n', totUrls = '$this->totUrls' WHERE func = 'getSeoSitemap'";
$this->execQuery();

$this->showWarnings();

// close msqli connection
$this->closeMysqliConn();

}
//################################################################################
//################################################################################
private function resetVars(){

$this->resetVars2();

// reset row
$this->row = [];

}
//################################################################################
//################################################################################
private function resetVars2(){

$this->size = 0;
$this->md5 = null; 
$this->lastmod = null;
$this->changefreq = null; 
$this->state = null;
$this->httpCode = null;
$this->insUrl = null;
$this->pageBody = null; 

}
//################################################################################
//################################################################################
private function writeLog($logMsg) {

if (($ob = DateTime::createFromFormat('U.u', microtime(true))) === false){
error_log('writeLog/DateTime/createFromFormat failed', 0);
$date = 'unknown time';
}
elseif ($ob->setTimeZone(new DateTimeZone('Europe/Rome')) === false){
error_log('writeLog/setTimeZone failed', 0);
$date = 'unknown time';
}
elseif (($date = $ob->format('Y-m-d H:i:s.u')) === false){
error_log('writeLog/format failed', 0);
$date = 'unknown time';
}

$msgLine = '['.$date.'] '.$logMsg.PHP_EOL;

if (file_put_contents($this->logPath, $msgLine, FILE_APPEND | LOCK_EX) === false) {
error_log('getSeoSitemap execution has been stopped because of writeLog/file_put_contents cannot write '.$this->logPath, 0);
$this->stopExec();
}

}
//################################################################################
//################################################################################
private function setPriority(){

$this->optimCheck();

$this->query = "UPDATE getSeoSitemap SET priority = '".DEFAULTPRIORITY."' WHERE state != 'skip' AND state != 'rSkip'";

$this->execQuery();
$this->showWarnings();

foreach (PARTIALURLPRIORITY as $key => $value) {
foreach ($value as $v) {
$this->optimCheck();

$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
. "WHERE url LIKE '".$v."%' AND state NOT IN ('skip', 'mSkip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";

$this->execQuery();
$this->showWarnings();
}
}

foreach (FULLURLPRIORITY as $key => $value) {
foreach ($value as $v) {
$this->optimCheck();

$this->query = "UPDATE getSeoSitemap SET priority = '".$key."' "
. "WHERE url = '".$v."' AND state NOT IN ('skip', 'mSkip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";

$this->execQuery();
$this->showWarnings();
}
}

$this->writeLog('Set priority');

}
//################################################################################
//################################################################################
private function getPriority(){

$priority = array_merge(array_keys(PARTIALURLPRIORITY), array_keys(FULLURLPRIORITY));
$priority[] = DEFAULTPRIORITY;
$priority = array_unique($priority);
rsort($priority);

foreach ($priority as $value) {

$this->query = "SELECT SQL_NO_CACHE COUNT(*) AS count FROM getSeoSitemap "
. "WHERE priority = '".$value."' AND state NOT IN ('skip', 'mSkip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
$this->execQuery();

$this->writeLog("Set priority ".$value." to ".$this->count." URLs into sitemap");
}

}
//################################################################################
//################################################################################
private function getTotalUrls() {

$this->writeLog('################################');
$this->writeLog('Included '.$this->totUrls.' URLs into sitemap');
$this->writeLog('################################'.PHP_EOL);

}
//################################################################################
//################################################################################
private function newSitemapAvailable(){

$this->query = "UPDATE getSeoSitemapExec SET newData = 'y' WHERE func = 'getSeoSitemap'";
$this->execQuery();

$this->showWarnings();

}
//################################################################################
//################################################################################
private function getIntUrls() {

$this->query = "SELECT SQL_NO_CACHE url, callerUrl FROM getSeoSitemap WHERE state IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND url LIKE '".DOMAINURL."%'";
$this->execQuery();

// print list of URLs into domain out of sitemap if PRINTSKIPURLS === true
if (PRINTSKIPURLS === true) {
$this->writeLog('##### URLs into domain out of sitemap');

if ($this->rowNum > 0) {
asort($this->row);

foreach ($this->row as $value) {
$this->writeLog('URL: '.$value['url'].' - caller URL: '.$value['callerUrl']);
}
}

$this->writeLog('##########');
}

$this->writeLog($this->rowNum.' URLs into domain out of sitemap'.PHP_EOL);

}
//################################################################################
//################################################################################
private function getExtUrls() {

$this->query = "SELECT SQL_NO_CACHE url, callerUrl FROM getSeoSitemap WHERE state = 'skip' AND url NOT LIKE '".DOMAINURL."%'";
$this->execQuery();

// print list of URLs out of domain out of sitemap if PRINTSKIPURLS === true
if (PRINTSKIPURLS === true) {
$this->writeLog('##### URLs out of domain out of sitemap');

if ($this->rowNum > 0) {
asort($this->row);

foreach ($this->row as $value) {
$this->writeLog('URL: '.$value['url'].' - caller URL: '.$value['callerUrl']);
}
}

$this->writeLog('##########');
}

$this->writeLog($this->rowNum.' URLs out of domain out of sitemap');

}
//################################################################################
//################################################################################
private function checkSkipUrls() {

$this->query = "SELECT SQL_NO_CACHE url FROM getSeoSitemap WHERE state IN ('skip', 'rSkip', 'niSkip', 'noSkip')";
$this->execQuery();

if ($this->rowNum > 0) {

$this->stmt6 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "httpCode = ? "
. "WHERE url = ?");

if ($this->stmt6 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt6 prepare error: '.lcfirst($this->mysqli->error)); 
$this->stopExec();
}

foreach ($this->row as $value) {
$url = $value['url'];
$this->getPage($url);
$this->checkPageSize($url);

$this->optimCheck();

if ($this->stmt6->bind_param('sss', $this->size, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt6 bind_param error: '.lcfirst($this->stmt6->error));    
$this->stopExec();
}

if ($this->stmt6->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt6 execute error: '.lcfirst($this->stmt6->error)); 
$this->stopExec();
}

$this->showWarnings();
}

if ($this->stmt6->close() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt6 close error: '.lcfirst($this->mysqli->error));   
$this->stopExec();
}
}

$this->writeLog('Checked skipped URLs');

}
//################################################################################
//################################################################################
private function insNewUrl($url){

$this->resetVars();

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $this->callerUrl;

$this->pageTest($url);

if ($this->insUrl === true) {
$this->insUpdNewUrlQuery($url);
}

}
//################################################################################
//################################################################################
private function insUpdNewUrlQuery($url){

$this->checkUrlLength($url);
$this->optimCheck();

if ($this->stmt2->bind_param('sss', $url, $this->callerUrl, $this->callerUrl) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt2 bind_param error: '.$this->stmt2->error); 
$this->stopExec();
}

if ($this->stmt2->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt2 execute error: '.$this->stmt2->error); 
$this->stopExec();
}

$this->showWarnings();

}
//################################################################################
//################################################################################
private function linksScan(){

foreach ($this->pageLinks as $url) {
$this->insNewUrl($url);
}

}
//################################################################################
//################################################################################
private function scan($url){

$this->resetVars2();
$this->getPage($url);

// set skipCallerUrl to prepare pageTest in case of calling insSkipUrl from pageTest
$this->skipCallerUrl = $this->callerUrl;

$this->pageTest($url);

if ($this->insUrl === true) {
$this->changefreq = 'daily';

$this->update();
$this->checkPageSize($url);
$this->optimCheck();

if (
$this->stmt3->bind_param('ssssss', $this->size, $this->md5, $this->lastmod, $this->changefreq, $this->httpCode, $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt3 bind_param error: '.lcfirst($this->stmt3->error)); 
$this->stopExec();
}

if ($this->stmt3->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt3 execute error: '.lcfirst($this->stmt3->error)); 
$this->stopExec();
}

$this->showWarnings();
}

}
//################################################################################
//################################################################################
private function getChangefreqList(){

foreach ($this->changefreqArr as $value) {

$this->query = "SELECT SQL_NO_CACHE url FROM getSeoSitemap "
. "WHERE changefreq = '$value' AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
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
//################################################################################
//################################################################################
private function getPriorityList(){

foreach ($this->priorityArr as $value) {

$this->query = "SELECT SQL_NO_CACHE url FROM getSeoSitemap WHERE priority = '".$value
. "' AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
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
//################################################################################
//################################################################################
private function getSizeList(){

$kbBingMaxSize = $this->getKb($this->seoMaxPageSize);

$this->query = "SELECT SQL_NO_CACHE url, size FROM getSeoSitemap WHERE size > '".$this->seoMaxPageSize
. "' AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
$this->execQuery();

$this->writeLog('##### URLs with size > '.$kbBingMaxSize.' Kb into sitemap | SEO: page size should be lower than '
.$kbBingMaxSize.' Kb');

$i = 0;

if ($this->rowNum > 0) {
asort($this->row);

foreach ($this->row as $v) {
$this->writeLog('Size: '.$this->getKb($v['size']).' Kb - URL: '.$v['url']);
$i++;
}
}

$this->writeLog('##########');
$this->writeLog($i.' URLs with size > '.$kbBingMaxSize.' Kb into sitemap'.PHP_EOL);

}
//################################################################################
//################################################################################
// get Kb from byte rounded 2 decimals and formatted 2 decimals
private function getKb($byte){

return sprintf('%0.2f', round($byte / 1024, 2));

}
//################################################################################
//################################################################################
private function getMinTitleLengthList(){

$this->query = "SELECT SQL_NO_CACHE url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) < "
.$this->titleLength[0]." AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with title length < '.$this->titleLength[0]
. ' characters into sitemap | SEO: page title length should be higher than '.$this->titleLength[0].' characters');

asort($this->row);

foreach ($this->row as $v){
$this->writeLog('Title length: '.$v['titleLength'].' characters - URL: '.$v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with title length < '.$this->titleLength[0].' characters into sitemap');

}
//################################################################################
//################################################################################
private function getMaxTitleLengthList(){

$this->query = "SELECT SQL_NO_CACHE url, CHAR_LENGTH(title) AS titleLength FROM getSeoSitemap WHERE CHAR_LENGTH(title) > "
.$this->titleLength[1]." AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with title length > '.$this->titleLength[1]
. ' characters into sitemap | SEO: page title length should be lower than '.$this->titleLength[1].' characters');

asort($this->row);

foreach ($this->row as $v) {
$this->writeLog('Title length: '.$v['titleLength'].' characters - URL: '.$v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with title length > '.$this->titleLength[1].' characters into sitemap'.PHP_EOL);

}
//################################################################################
//################################################################################
private function getDuplicateTitle(){

$this->query = "SELECT SQL_NO_CACHE title FROM getSeoSitemap WHERE state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND"
. " title IS NOT NULL GROUP BY title HAVING COUNT(*) > 1";
$this->execQuery();

$row = $this->row;

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with duplicate title into sitemap (SEO: URLs should have unique title into the website)');

asort($row);

foreach ($row as $v){
$this->query = "SELECT SQL_NO_CACHE url, title FROM getSeoSitemap WHERE title = '"
.$v['title']."' AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
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
//################################################################################
//################################################################################
private function getMinDescriptionLengthList(){

$this->query = "SELECT SQL_NO_CACHE url, CHAR_LENGTH(description) AS descriptionLength FROM getSeoSitemap WHERE CHAR_LENGTH(description) < "
.$this->descriptionLength[0]." AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND title IS NOT NULL";
$this->execQuery();

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with description length < '.$this->descriptionLength[0]
. ' characters into sitemap | SEO: page description length should be higher than '.$this->descriptionLength[0].' characters');

asort($this->row);

foreach ($this->row as $v) {
$this->writeLog('Description length: '.$v['descriptionLength'].' characters - URL: '.$v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with description length < '.$this->descriptionLength[0].' characters into sitemap');

}
//################################################################################
//################################################################################
private function getMaxDescriptionLengthList(){

$this->query = "SELECT SQL_NO_CACHE url, CHAR_LENGTH(description) AS descriptionLength FROM getSeoSitemap WHERE CHAR_LENGTH(description) > "
.$this->descriptionLength[1]." AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND description IS NOT NULL";
$this->execQuery();

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with description length > '.$this->descriptionLength[1]
. ' characters into sitemap (SEO: page description length should be lower than '.$this->descriptionLength[1].' characters)');

asort($this->row);

foreach ($this->row as $v) {
$this->writeLog('Description length: '.$v['descriptionLength'].' characters - URL: '.$v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with description length > '.$this->descriptionLength[1].' characters into sitemap');

}
//################################################################################
//################################################################################
private function getDuplicateDescription(){

$this->query = "SELECT SQL_NO_CACHE description FROM getSeoSitemap WHERE state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200' AND"
. " description IS NOT NULL GROUP BY description HAVING COUNT(*) > 1";
$this->execQuery();

$row = $this->row;

$i = 0;

if ($this->rowNum > 0){
$this->writeLog('##### URLs with duplicate description into sitemap (SEO: URLs should have unique description into the website)');

asort($row);

foreach ($row as $v){
$this->query = "SELECT SQL_NO_CACHE url, description FROM getSeoSitemap WHERE description = '"
.$v['description']."' AND state NOT IN ('skip', 'rSkip', 'niSkip', 'noSkip') AND httpCode = '200'";
$this->execQuery();

foreach ($this->row as $v2){
$this->writeLog('Duplicate description: '.$v2['description'].' - URL: '.$v2['url']);
$i++;
}
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with duplicate description into sitemap');

}
//################################################################################
//################################################################################
// print all URLs into sitemap in an alphaberic order
private function getTypeList(){

$this->query = "SELECT SQL_NO_CACHE url FROM getSeoSitemap WHERE httpCode = '200' AND state IN ('scan', 'nfSkip')";
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
//################################################################################
//################################################################################
// open curl connection
private function openCurlConn(){

if (($this->ch = curl_init()) === false) {
$this->writeLog('Execution has been stopped because of curl_init error'); 
$this->stopExec();
}

if (curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, 1) === false) {
$this->writeLog('Execution has been stopped because of curl_setopt CURLOPT_RETURNTRANSFER error'); 
$this->stopExec();
}

if (curl_setopt($this->ch, CURLOPT_USERAGENT, $this->userAgent) === false) {
$this->writeLog('Execution has been stopped because of curl_setopt CURLOPT_USERAGENT error'); 
$this->stopExec();
}

}
//################################################################################
//################################################################################
// close curl connection
private function closeCurlConn(){

curl_close($this->ch);

}
//################################################################################
//################################################################################
// update execution value
private function updateExec(){

$this->query = "UPDATE getSeoSitemapExec SET exec = '$this->exec' WHERE func = 'getSeoSitemap'";
$this->execQuery();

$this->showWarnings();

}
//################################################################################
//################################################################################
// update error counter
private function getErrCounter(){

$this->errCounter++;

if ($this->errCounter >= $this->maxErr) {
$this->writeLog('Execution has been stopped because of errors are more than '.$this->maxErr);  
$this->stopExec();
}

}
//################################################################################
//################################################################################
// delete a file
private function delete($fileName){

if (unlink($fileName) === false){
$this->writeLog('Execution has been stopped because of delete/unlink error on '.$fileName); 
$this->stopExec();
}

}
//################################################################################
//################################################################################
// get URL entity escaping
private function entityEscaping($url){

foreach ($this->escapeCodeArr as $key => $value) {
$url = str_replace($key, $value, $url);
}

return $url;

}
//################################################################################
//################################################################################
private function save(){

// set total URLs into sitemap of previous scan
$prevTotUrls = $this->getPrevTotUrls();

$this->query = "SELECT SQL_NO_CACHE url, lastmod, changefreq, priority FROM getSeoSitemap "
. "WHERE httpCode = '200' AND state IN ('scan', 'nfSkip')";
$this->execQuery();

// set total URLs into sitemap of this scan
$this->totUrls = $this->rowNum;

if($prevTotUrls > 0){
$percTotUrlsDiff = abs($this->totUrls - $prevTotUrls) / $prevTotUrls * 100;

// stop exec if percentage difference of total URLs is higher than MAXPERCTOTURLSDIFF
if ($percTotUrlsDiff > MAXPERCTOTURLSDIFF) {
$this->writeLog("Execution has been stopped because of total URLs to insert into sitemap is $this->totUrls "
. "and percentage difference from previous value of $prevTotUrls is ".$percTotUrlsDiff."% and higher than the set value of "
. "".MAXPERCTOTURLSDIFF."% | increase MAXPERCTOTURLSDIFF or double-check what happened"); 
$this->stopExec();
}
}

// stop exec if total URLs to insert is higher than $maxTotalUrls
if ($this->totUrls > $this->maxTotalUrls) {
$this->writeLog("Execution has been stopped because of total URLs to insert into sitemap is $this->totUrls "
. "and higher than max limit of $this->maxTotalUrls"); 
$this->stopExec();
}

// set sitemap counter start value
$sitemapCount = null;

if ($this->totUrls > $this->maxUrlsInSitemap) {
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
<!-- Created on $this->scriptTitle -->

EOD;

}

$dT = new DateTime();
$dT->setTimestamp($value['lastmod']);
$lastmod = $dT->format(DATE_W3C);

$url = $this->entityEscaping($value['url']);

$txt .= '<url><loc>'.$url.'</loc><lastmod>'.$lastmod.'</lastmod>'
. '<changefreq>'.$value['changefreq'].'</changefreq><priority>'.$value['priority'].'</priority></url>
';

if ($sitemapIntCount === $this->maxUrlsInSitemap || $genCount === $this->totUrls) {
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

if ($this->multipleSitemaps === true && $genCount !== $this->totUrls) {
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
<!-- Created with $this->scriptTitle -->

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

if ($this->detectUtf8Enc($txt) !== true) {
$this->writeLog('Execution has been stopped because of '.$sitemapFile.' is not UTF-8 encoded');  
$this->stopExec();
}
}

return true;

}
//################################################################################
//################################################################################
private function gzip($fileName){

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

}
//################################################################################
//################################################################################
// get file name without the rest of path
private function getFileName($filePath){

return str_replace(SITEMAPPATH, '', $filePath);

}
//################################################################################
//################################################################################
// detect if enconding is UTF-8
private function detectUtf8Enc($str){

if (mb_detect_encoding($str, 'UTF-8', true) === 'UTF-8') {
return true;
}
else {
return false;
}

}
//################################################################################
//################################################################################
private function stopExec(){

$this->exec = 'n';
$this->updateExec();
exit();

}
//################################################################################
//################################################################################
// check if URL length is > $maxUrlLength
private function checkUrlLength($url){

$urlLength = strlen($url);

if ($urlLength > $this->maxUrlLength) {
$this->writeLog('Execution has been stopped because of length is > '.$this->maxUrlLength.' characters for URL: '.$url); 
$this->stopExec();
}

}
//################################################################################
//################################################################################
// check if page size is > $maxPageSize
private function checkPageSize($url){

if ($this->size > $this->maxPageSize) {
$this->writeLog('Execution has been stopped because of size is > '.$this->maxPageSize.' bytes for URL: '.$url); 
$this->stopExec();
}

}
//################################################################################
//################################################################################
// check all sitemap sizes. they must be non larger than $sitemapMaxSize
private function checkSitemapSize(){

if (PRINTSITEMAPSIZELIST === true) {
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

if (PRINTSITEMAPSIZELIST === true) {
$this->writeLog('Size: '.round($size * 0.0009765625, 2).' Kb - sitemap: '.$fileName);
}
}

if (PRINTSITEMAPSIZELIST === true) {
$this->writeLog('##########'.PHP_EOL);
}

return true;

}
//################################################################################
//################################################################################
// rewrite robots.txt with new sitemap infos
private function getRewriteRobots(){

// remove all old sitemap lines from robots.txt
foreach ($this->robotsLines as $key => $value) {
if ($value === '# Sitemap' || $value === '# Sitemapindex' || strpos($value, 'Sitemap: ') === 0) {
unset($this->robotsLines[$key]);
}
}

if ($this->multipleSitemaps !== true) {
$this->robotsLines[] = '# Sitemap';
$this->robotsLines[] = 'Sitemap: '.DOMAINURL.'/sitemap.xml.gz';
}
else {
$this->robotsLines[] = '# Sitemapindex';
$this->robotsLines[] = 'Sitemap: '.DOMAINURL.'/sitemapindex.xml.gz';
}

$newCont = null;

// get new file content
foreach ($this->robotsLines as $key => $value) {
$newCont .= $value.PHP_EOL;
}

// rewrite file
if (file_put_contents($this->robotsPath, $newCont) === false) {
$this->writeLog('Execution has been stopped because of file_put_contents cannot write robots.txt');  
$this->stopExec();
}

$this->writeLog('Wrote robots.txt'.PHP_EOL);

}
//################################################################################
//################################################################################
// check tables
private function checkTables(){

$this->query = "SHOW TABLES LIKE 'getSeoSitemapExec'";
$this->execQuery();

if ($this->rowNum === 0) {

$this->query = "CREATE TABLE `getSeoSitemapExec` (
 `id` tinyint(1) NOT NULL AUTO_INCREMENT,
 `func` varchar(20) NOT NULL COLLATE utf8mb4_unicode_ci,
 `version` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'v0.0.0',
 `mDate` int(10) NOT NULL DEFAULT '0' COMMENT 'timestamp of last mod',
 `exec` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n',
 `totUrls` bigint(10) NOT NULL DEFAULT '0' COMMENT 'total URLs into sitemap of the last successfull exec',
 `newData` varchar(1) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'n' COMMENT 'set to y when new data are avaialble',
 UNIQUE KEY `id` (`id`),
 UNIQUE KEY `func` (`func`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='execution of getSeoSitemap functions'";
$this->execQuery();

$this->showWarnings();

$this->query = "INSERT INTO getSeoSitemapExec SET func = 'getSeoSitemap'";
$this->execQuery();

$this->showWarnings();
}

$this->query = "SHOW TABLES LIKE 'getSeoSitemap'";
$this->execQuery();

if ($this->rowNum === 0) {
$this->query = "CREATE TABLE `getSeoSitemap` (
 `url` varbinary(767) NOT NULL,
 `callerUrl` varbinary(767) DEFAULT NULL,
 `size` mediumint(8) unsigned NOT NULL DEFAULT 0 COMMENT 'byte',
 `title` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `description` varchar(250) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `md5` char(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `lastmod` int(10) unsigned NOT NULL DEFAULT 0,
 `changefreq` enum('daily','weekly','monthly','yearly') COLLATE utf8mb4_unicode_ci NOT NULL,
 `priority` enum('0.1','0.2','0.3','0.4','0.5','0.6','0.7','0.8','0.9','1.0') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 `state` enum('new','scan','skip','mSkip','rSkip','old','niSkip','nfSkip','noSkip') COLLATE utf8mb4_unicode_ci NOT NULL,
 `httpCode` char(3) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
 PRIMARY KEY (`url`),
 KEY `state` (`state`),
 KEY `httpCode` (`httpCode`),
 KEY `size` (`size`),
 KEY `changefreq` (`changefreq`),
 KEY `priority` (`priority`),
 KEY `lastmod` (`lastmod`),
 KEY `title` (`title`),
 KEY `description` (`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
$this->execQuery();

$this->showWarnings();
}

}
//################################################################################
//################################################################################
// optimize tables
private function optimTables(){

foreach (['getSeoSitemap', 'getSeoSitemapExec'] as $v) {
$this->query = "ANALYZE TABLE $v";
$this->execQuery();

$this->query = "CHECK TABLE $v";
$this->execQuery();

$this->query = "CHECKSUM TABLE $v";
$this->execQuery();

// defrag
$this->query = "ALTER TABLE $v ENGINE = InnoDB";
$this->execQuery();

$this->showWarnings();

$this->writeLog("Defragged $v table"); 

// optimize
$this->optim($v);
}

}
//################################################################################
//################################################################################
// optimize table
private function optim($table){

$query = "OPTIMIZE TABLE ".$table;

if (($result = $this->mysqli->query($query)) === false) {
$this->writeLog('Execution has been stopped because of MySQL error. Error ('.$this->mysqli->errno.'): '
.$this->mysqli->error.' - query: "'.$query.'"'.$this->txtToAddOnMysqliErr);   
exit();
}

$this->writeLog('Optimized '.$table.' table'); 

}
//################################################################################
//################################################################################
private function getMalfList(){

$i = 0;

foreach ($this->malfChars as $value) {

$this->query = "SELECT SQL_NO_CACHE url FROM getSeoSitemap WHERE url LIKE '%".$value
."%' AND url LIKE '".DOMAINURL."%' AND state = 'skip'";
$this->execQuery();

if ($this->rowNum > 0) {
$this->writeLog("##### URLs with '$value' malformed character into domain (good pratice: do not use that character in URL "
."address)");

asort($this->row);

foreach ($this->row as $v) {
$this->writeLog($v['url']);

$i++;
}

$this->writeLog('##########');
}

$this->writeLog($i.' URLs with malformed characters into domain out of sitemap'.PHP_EOL);
}

}
//################################################################################
//################################################################################
private function fullScan(){

do {
// reset row
$this->row = [];

if ($this->stmt7->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt7 execute error: '.$this->stmt->error); 
$this->stopExec();
}

if (($result = $this->stmt7->get_result()) === false) {    
$this->writeLog('Execution has been stopped because of MySQL stmt7 get_result error: '.$this->stmt->error);
$this->stopExec();
}

$this->row[0] = $result->fetch_assoc();
$rowNum = $this->rowNum = $result->num_rows;
$result->free_result();

// if there is almost 1 record into getSeoSitemap table with state new....
if ($rowNum === 1){ 
$this->url = $this->row[0]['url'];
$url = $this->url;
$this->scan($url);
$this->getIndexFollowSeo($url);
$this->callerUrl = $url;
$this->linksScan();
$this->optimCheck();

if ($this->stmt->bind_param('s', $url) !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt bind_param error: '.$this->stmt->error); 
$this->stopExec();
}

if ($this->stmt->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt execute error: '.$this->stmt->error); 
$this->stopExec();
}

$this->showWarnings();
}
}
while ($rowNum === 1);

}
//################################################################################
//################################################################################
private function prep(){

$time = time();

// set log path: it will remain the same from the start to the end of execution
$this->logPath = LOGPATH.date('Ymd', $time).'.log';

// set robots.txt path
$this->robotsPath = SITEMAPPATH.'robots.txt';

// set start time
$this->startTime = $time;

// set userAgent version
$this->userAgent = str_replace('ver.', ltrim($this->version, 'v'), $this->userAgent);

// set scriptTitle version
$this->scriptTitle = str_replace('ver.', $this->version, $this->scriptTitle);

// open mysqli connection
$this->openMysqliConn();

// check tables into dbase
$this->checkTables();

// read robots.txt
$this->readRobots();

// set $skipUrl
$this->getRobotsData();

$this->query = "SELECT SQL_NO_CACHE exec FROM getSeoSitemapExec WHERE func = 'getSeoSitemap'";
$this->execQuery();

// check if getSeoSitemap is already running and stop it to prevent double execution
if ($this->row[0]['exec'] === 'y') {
$this->writeLog('An error has occurred: execution has been stopped; '
. 'maybe the previous scan was not ended correctly. Double-check log to fix it.'.$this->txtToAddOnMysqliErr);

exit();
}
// check if prevous full scan was ended to start a new full scan
elseif ($this->row[0]['exec'] === 'n') {
$this->writeLog('## getSeoSitemap '.$this->version);
$this->writeLog('Execution start');
}
else {
$this->writeLog('Value of state in getSeoSitemapExec table is not correct: '
. 'execution has been stopped. Double-check log to fix it.'.$this->txtToAddOnMysqliErr);

exit();
}

// set execution of function to y
$this->exec = 'y';
$this->updateExec();

// backup of previous sitemap
$this->bak();

$this->optimCheck();

// update all states to old to be ready for the new full scan
$this->query = "UPDATE getSeoSitemap SET state = 'old'";
$this->execQuery();

$this->showWarnings();
$this->writeLog('Scan start');

// prepare mysqli statements
$this->prepMysqliStmt();

// insert or update DOMAINURL
$this->insUpdNewUrlQuery(DOMAINURL);

$this->openCurlConn();

}
//################################################################################
//################################################################################
// prepare mysqli statements
private function prepMysqliStmt(){

$this->stmt = $this->mysqli->prepare("UPDATE getSeoSitemap SET state = IF(state = 'new', 'scan', state) WHERE url = ?");

if ($this->stmt === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt prepare error: '.lcfirst($this->mysqli->error));  
$this->stopExec();
}

$this->stmt2 = $this->mysqli->prepare("INSERT INTO getSeoSitemap (url, callerUrl, state) VALUES (?, ?, 'new') "
. "ON DUPLICATE KEY UPDATE state = IF(state = 'old', 'new', state), callerUrl = ?");

if ($this->stmt2 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt2 prepare error: '.lcfirst($this->mysqli->error)); 
$this->stopExec();
}

$this->stmt3 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "size = ?, "
. "md5 = ?, "
. "lastmod = ?, "
. "changefreq = ?, "
. "httpCode = ? "
. "WHERE url = ?");

if ($this->stmt3 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt3 prepare error: '.lcfirst($this->mysqli->error));
$this->stopExec();
}

$this->stmt4 = $this->mysqli->prepare("INSERT INTO getSeoSitemap ("
. "url, "
. "callerUrl, "
. "size, "
. "state, "
. "httpCode) "
. "VALUES ("
. "?, "
. "?, "
. "?, "
. "?, "
. "?) "
. "ON DUPLICATE KEY UPDATE "
. "callerUrl = ?, "
. "size = ?, "
. "state = ?, "
. "httpCode = ?");

if ($this->stmt4 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt4 prepare error: '.lcfirst($this->mysqli->error));   
$this->stopExec();
}

$this->stmt5 = $this->mysqli->prepare("UPDATE getSeoSitemap SET "
. "title = ?, "
. "description = ? "
. "WHERE url = ?");

if ($this->stmt5 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt5 prepare error: '.lcfirst($this->mysqli->error));
$this->stopExec();
}

$this->stmt7 = $this->mysqli->prepare("SELECT SQL_NO_CACHE url, size, md5, lastmod FROM getSeoSitemap WHERE state = 'new' LIMIT 1");

if ($this->stmt7 === false) {  
$this->writeLog('Execution has been stopped because of MySQL stmt7 prepare error: '.lcfirst($this->mysqli->error));
$this->stopExec();
}

}
//################################################################################
//################################################################################
// get absolute url from relative url
private function getAbsoluteUrl($relativeUrl, $baseUrl, $ref){

if (empty($relativeUrl) === true) {
$this->writeLog('Empty '.$ref.' on '.$baseUrl);
return false;
}

// if already absolute URL 
if (parse_url($relativeUrl, PHP_URL_SCHEME) !== null) {
return $relativeUrl;
}

$rel0 = $relativeUrl[0];

// queries and anchors
if ($rel0 === '#' || $rel0 === '?') {
return $baseUrl.$relativeUrl;
}

// parse base URL and convert to: $scheme, $host, $path, $query, $port, $user, $pass
extract(parse_url($baseUrl));

// if base URL contains a path remove non-directory elements from $path
if (isset($path) === true) {
$path = preg_replace('#/[^/]*$#', '', $path);
}
else {
$path = '';
}

// if relative URL starts with //
if (substr($relativeUrl, 0, 2) === '//') {
return $scheme.':'.$relativeUrl;
}

// if relative URL starts with /
if ($rel0 === '/') {
$path = null;
}

$abs = null;

// if relative URL contains a user
if (isset($user) === true) {
$abs .= $user;

// if relative URL contains a password
if (isset($pass) === true) {
$abs .= ':'.$pass;
}

$abs .= '@';
}

$abs .= $host;

// if relative URL contains a port
if (isset($port) === true) {
$abs .= ':'.$port;
}

$abs .= $path.'/'.$relativeUrl.(isset($query) === true ? '?'.$query : null);

// replace // or /./ or /foo/../ with /
for ($n = 1; $n > 0; $abs = preg_replace(['#(/\.?/)#', '#/(?!\.\.)[^/]+/\.\./#'], '/', $abs, -1, $n)) {
}

// return absolute URL
return $scheme.'://'.$abs;

}
//################################################################################
//################################################################################
private function readRobots(){

// if robots.txt exists...
if (file_exists($this->robotsPath) === true) {

// insert robots.txt line by line into an array
$this->robotsLines = file($this->robotsPath, FILE_IGNORE_NEW_LINES);

if ($this->robotsLines === false) {
$this->writeLog('Execution has been stopped because of file cannot read robots.txt');   
$this->stopExec();
}
}
else {
$this->writeLog('Execution has been stopped because of robots.txt does not exist');   
$this->stopExec();
}

}
//################################################################################
//################################################################################
// get data from robots.txt to set $skipUrl and allowUrl
private function getRobotsData(){

$userAgentAll = false;

foreach ($this->robotsLines as $value) {
if ($value === 'User-agent: *'){
$userAgentAll = true;
}
else {
if ($userAgentAll === true) {
if (substr($value, 0, 12) === 'User-agent: ') {
break;
}
elseif (substr($value, 0, 10) === 'Disallow: ') {
$this->skipUrl[] = DOMAINURL.substr($value, 10);
}
elseif (substr($value, 0, 7) === 'Allow: ') {
$this->allowUrl[] = DOMAINURL.substr($value, 7);
}
}
}
}

}
//################################################################################
//################################################################################
// print mysqli warnings | useful on: INSERT, UPDATE, LOAD DATA, CREATE TABLE, ALTER TABLE
private function showWarnings(){

if ($this->mysqli->warning_count > 0) {
if ($this->getWarn === true) {
if (($warnRes = $this->mysqli->query("SHOW WARNINGS")) !== false) {
$warnRow = $warnRes->fetch_row();

$warnMsg = sprintf("%s (%d): %s", $warnRow[0], $warnRow[1], lcfirst($warnRow[2]));
$this->writeLog($warnMsg.' - query: "'.$this->query.'"');   
$warnRes->close();
}

$this->getWarnCounter();
}
}

}
//################################################################################
//################################################################################
// update warning counter
private function getWarnCounter(){

$this->warnCounter++;

if ($this->warnCounter >= $this->maxWarn) {
$this->writeLog('Warnings are not longer printed because of they are more than '.$this->maxWarn);  
$this->getWarn = false;
}

}
//################################################################################
//################################################################################
// insert all kinds of skipped URLs into dbase
private function insSkipUrl($url, $state){

$this->checkUrlLength($url);
$this->checkPageSize($url);

$this->optimCheck();

if ($this->stmt4->bind_param('sssssssss', $url, $this->skipCallerUrl, $this->size, $state, $this->httpCode, $this->skipCallerUrl, 
$this->size, $state, $this->httpCode) !== true) { 
$this->writeLog('Execution has been stopped because of MySQL stmt4 bind_param error: '.lcfirst($this->stmt4->error)); 
$this->stopExec();
}

if ($this->stmt4->execute() !== true) {  
$this->writeLog('Execution has been stopped because of MySQL stmt4 execute error: '.lcfirst($this->stmt4->error));  
$this->stopExec();
}

$this->showWarnings();


}
//################################################################################
//################################################################################
// test URL to robots skip: return true to robots skip, false otherwise
private function robotsSkipTest($url){

foreach ($this->skipUrl as $v){

if (strpos($url, $v) === 0 || fnmatch($v, $url) === true) {

if (empty($this->allowUrl) === false) {

foreach ($this->allowUrl as $v2) {
if (strpos($url, $v2) !== 0) {
if (strpos($url, '*') !== false) {
if (fnmatch($v2, $url) === false) {
return true;
}
}
else {
return true;
}
}
else {
break 1;
}
}
}
else {
return true;
}
}
}

return false;

}
//################################################################################
//################################################################################
// get exclusion
private function getExclusion($contentType, $exclusion){

$include = true;

foreach ($exclusion as $v) {
if (strpos($contentType, $v) !== false) {
$include = false;
break;
}
}

return $include;

}
//################################################################################
//################################################################################
// get total urls of the previous successfull exec
private function getPrevTotUrls(){

$this->query = "SELECT SQL_NO_CACHE totUrls FROM getSeoSitemapExec WHERE func = 'getSeoSitemap'";
$this->execQuery();

return $this->row[0]['totUrls'];

}
//################################################################################
//################################################################################
// backup old sitemaps
private function bak(){

$bakPath = GETSEOSITEMAPPATH.'bak/';

$bakSitemapPaths = $this->getSitemapPaths($bakPath);

// rename previous backup sitemap
foreach ($bakSitemapPaths as $v0){
if (rename($v0, $v0.'.bak') === false){
$this->writeLog('Execution has been stopped because of bak/rename error on '.$v0);   
$this->stopExec();
}
}

$this->writeLog('Renamed previous backup sitemap');

$sitemapPaths = $this->getSitemapPaths(SITEMAPPATH);

// save backup sitemap
foreach ($sitemapPaths as $v2) {
$fileName = basename($v2);

if (copy($v2, $bakPath.$fileName) === false){
$this->writeLog('Execution has been stopped because of bak/copy error on '.$v2);   
$this->stopExec();
}
}

$this->writeLog('Saved backup sitemap');

// delete previous backup sitemap
foreach ($bakSitemapPaths as $v){
$this->delete($v.'.bak');
}

$this->writeLog('Deleted previous backup sitemap');

}
//################################################################################
//################################################################################
// get all sitemap paths included into $path
private function getSitemapPaths($path){

$sitemapPaths = glob($path.'sitemap*.xml*');

if ($sitemapPaths !== false) {
return $sitemapPaths;
}
else {
$this->writeLog('Execution has been stopped because of getSitemapPaths/glob error on '.$path);   
$this->stopExec();
}

}
//################################################################################
//################################################################################
// detect if getSeoSitemap table should be optimized following $countQuery

private function optimCheck(){

$this->countQuery++;

if (is_int($this->countQuery / $this->optimTimes) === true) {
$this->optim('getSeoSitemap');
}

}

//################################################################################
//################################################################################
}

$gS = new getSeoSitemap();
$gS->start();
