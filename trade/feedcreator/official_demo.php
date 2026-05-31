<?
include("include/feedcreator.class.php");

$rss = new UniversalFeedCreator();
$rss->useCached();
$rss->title = "PHP news";
$rss->description = "daily news from the PHP scripting world";
$rss->link = "http://www.dailyphp.net/news";
$rss->syndicationURL = "http://www.dailyphp.net/".$PHP_SELF;

$image = new FeedImage();
$image->title = "dailyphp.net logo";
$image->url = "http://www.dailyphp.net/images/logo.gif";
$image->link = "http://www.dailyphp.net";
$image->description = "Feed provided by dailyphp.net. Click to visit.";
$rss->image = $image;

// get your news items from somewhere, e.g. your database:
$connection = mysqli_connect($dbHost, $dbUser, $dbPass, $dbName);
if ($connection) {
    $res = mysqli_query($connection, "SELECT * FROM news ORDER BY newsdate DESC");
    while ($res && ($data = mysqli_fetch_object($res))) {
        $item = new FeedItem();
        $item->title = $data->title;
        $item->link = $data->url;
        $item->description = $data->short;
        $item->date = $data->newsdate;
        $item->source = "http://www.dailyphp.net";
        $item->author = "John Doe";

        $rss->addItem($item);
    }
    mysqli_close($connection);
}

$rss->saveFeed("RSS1.0", "news/feed.xml");
?> 