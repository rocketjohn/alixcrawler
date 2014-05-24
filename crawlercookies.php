<?php

// It may take a whils to crawl a site ...

// Inculde the phpcrawl-mainclass
include("libs/PHPCrawler.class.php");
include("classes/simple_html_dom.php");
include("db.php");

// Extend the class and override the handleDocumentInfo()-method 
class MyCrawler extends PHPCrawler 
{

    private $count = 0;

  function handleDocumentInfo($DocInfo) 
  {
set_time_limit(10000);
    $this->count++;

    $dsn = 'mysql:host=localhost;dbname=crawler';
    $username = 'crawler';
    $password = 'alix';
    $dbh = new PDO($dsn, $username, $password );

    // Just detect linebreak for output ("\n" in CLI-mode, otherwise "<br>").
    if (PHP_SAPI == "cli") $lb = "\n";
    else $lb = "<br />";

    // Print the URL and the HTTP-status-Code
    echo "Page number {$this->count} requested: ".$DocInfo->url." (".$DocInfo->http_status_code.")".$lb;
    
    // Print the refering URL
    echo "Referer-page: ".$DocInfo->referer_url.$lb;
    
    // Print if the content of the document was be recieved or not
 //    if ($DocInfo->received == true)
 //      echo "Content received: ".$DocInfo->bytes_received." bytes".$lb;
 //    else
 //    {
 //    	echo "Content not received".$lb; 
	// }
    
    // Now you should do something with the content of the actual
    // received page or file ($DocInfo->source), we skip it in this example 
    
    if (preg_match("#/Records/RecordDisplayTranscript.aspx# i", $DocInfo->url) ) 
    {
        // print "---" . $lb;
        // print "before load " . memory_get_usage (true) . $lb;
        $html = new simple_html_dom();
        $html->load($DocInfo->content);
        // print "after load " .memory_get_usage (true) . $lb;
        $tags = $html->find("div.transcription ul li");
        
        if (!empty($tags))
        {
            echo "Found a tag page!".$lb;
        
            $page_title = $html->find('div#content_sleeve h1', 0);
            // print "Page Title: " . trim($page_title->plaintext) . $lb;
            $record_title = $html->find('div.recordDisplaySleeve div.columns div.column h2', 0);
            // print "Record Title: " . trim($record_title->plaintext) . $lb;
            
            list( , $params) = explode("?", $DocInfo->url);
            $oid = $iid = "";
            foreach (explode("&",$params) as $pair)
            {
                list($key,$value) = explode("=",$pair);
                if ($key =="oid")
                    $oid = $value;
                if ($key == "iid")
                    $iid = $value;
            }
    
            // print "OID: " . $oid . $lb;
            // print "IID: " . $iid . $lb;
            // print "Tags (no entries here means automated tags only):" . $lb;
            foreach ($tags as &$tag)
            {
                $username = $tag->find("span.name", 0);
                $username_text =  trim($username->plaintext);
                if ($username_text == "")
                    continue;

                // print "\tUsername: " . $username_text . $lb;
                $tag_text = $tag->find("p",1);
                // print "\tTag: " . trim($tag_text->plaintext) . $lb;

                insert_page_content(
                        trim($page_title->plaintext), 
                        trim($record_title->plaintext) , 
                        $oid, 
                        $iid, 
                        trim($tag_text->plaintext), 
                        $username_text, 
                    $dbh);

            }
            // print "after tag procesing" . memory_get_usage (true) . $lb;
            
            unset($tags, $page_title, $record_title, $params, $oid, $iid, $pair, $username, $username_text, $dbh);
            // print "after page data unset" . memory_get_usage (true) . $lb;

        }

        $html->clear();
        // print "after clear " . memory_get_usage (true) . $lb;
        unset($html);
        // print "after unset html " . memory_get_usage (true) . $lb;
        

// <li class="first">
// <p class="meta">
// <span class="name">carolmoyer</span>
// <span class="date">20-July-2012 22:12:20</span>
// </p>
// <p>Birth of Bracebridge  Kent 1885</p>
// <p class="meta">
// <span class="unsuitable">Unsuitable or offensive?</span>
// <a class="report" href="http://www.lincstothepast.com/Records/Record_ReportTagType.aspx?oid=514689&amp;iid=75920&amp;tid=451317">
//                   Report this tag
//                 </a>
// </p>
// </li>







    }

    echo $lb;
    
    flush();
  } 
}

// Now, create a instance of your class, define the behaviour
// of the crawler (see class-reference for more options and details)
// and start the crawling-process.

$crawler = new MyCrawler($dbh);
$crawler->setStreamTimeout(60);
$crawler->setUrlCacheType(2); //SQLLite cache
$crawler->setWorkingDirectory("/dev/shm/"); // set working directory somewhere big
// URL to crawl
$crawler->addPostData("#^http://www.lincstothepast.com/SearchResults.aspx$#", array("rectype" => "img", "withallworld" => "", "withexactphrase" => "", "withatleastone" => "", "withoutword" => "") );
$crawler->setURL("http://www.lincstothepast.com/SearchResults.aspx"); // real start point 
// $crawler->setURL("http://www.lincstothepast.com/Records/RecordDisplayTranscript.aspx?oid=514689&iid=75920"); // multipage tags test
// $crawler->setURL("http://www.lincstothepast.com/Records/RecordDisplayTranscript.aspx?oid=778626&iid=572807"); // no tags test

// Only receive content of files with content-type "text/html"
$crawler->addContentTypeReceiveRule("#text/html#");

// Ignore links to pictures, dont even request pictures
$crawler->addURLFilterRule("#\.(jpg|jpeg|gif|png)$# i");
$crawler->addURLFilterRule("#\&size\=.*?$# i");
$crawler->addURLFilterRule("#RegisterAccount.aspx# i");
$crawler->addURLFilterRule("#SignIn.aspx# i");
$crawler->addURLFollowRule("#GoToPage#");
$crawler->addURLFollowRule("#\.record\?ImageID# i");
$crawler->addURLFollowRule("#/Records/RecordDisplayTranscript.aspx# i");
// Store and send cookie-data like a browser does
$crawler->enableCookieHandling(true);

$crawler->enableResumption();

if (!file_exists("/tmp/uk.co.dwarven.crawler.tmp"))
{
  $crawler_ID = $crawler->getCrawlerId();
  file_put_contents("/tmp/uk.co.dwarven.crawler.tmp", $crawler_ID);
}
// If the script was restarted again (after it was aborted), read the crawler-ID
// and pass it to the resume() method.
else
{
  $crawler_ID = file_get_contents("/tmp/uk.co.dwarven.crawler.tmp");
  $crawler->resume($crawler_ID);
}

// Thats enough, now here we go
$crawler->go();

unlink("/tmp/uk.co.dwarven.crawler.tmp");

// At the end, after the process is finished, we print a short
// report (see method getProcessReport() for more information)
$report = $crawler->getProcessReport();



if (PHP_SAPI == "cli") $lb = "\n";
else $lb = "<br />";
    
echo "Summary:".$lb;
echo "Links followed: ".$report->links_followed.$lb;
echo "Documents received: ".$report->files_received.$lb;
echo "Bytes received: ".$report->bytes_received." bytes".$lb;
echo "Process runtime: ".$report->process_runtime." sec".$lb; 
?>
