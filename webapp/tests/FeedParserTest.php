<?php
require_once 'PHPUnit/TestCase.php';
require_once dirname(dirname(__FILE__)) . '/lib/FeedParser.php';

class FeedParserTest extends PHPUnit_TestCase
{
    function testRSS10()
    {
        $uri = 'http://plnet.jp/test_feed/d.hatena-rss10.xml';
        $feed = new FeedParser($uri);
        $feed->parse();
        $this->assertEquals('m-komagataの日記', $feed->getTitle());
        $this->assertEquals('http://d.hatena.ne.jp/m-komagata/', $feed->getLink());
        $this->assertEquals('http://d.hatena.ne.jp/images/de_favicon.ico', $feed->getFavicon());
        $this->assertEquals('テストタイトル２', $feed->data['items'][0]['title']);
        $this->assertEquals('http://d.hatena.ne.jp/m-komagata/20060709', $feed->data['items'][0]['link']);

        $uri = 'http://plnet.jp/test_feed/delicious-rss10.xml';
        $feed = new FeedParser($uri);
        $feed->parse();
        $this->assertEquals('del.icio.us/komagata', $feed->getTitle());
        $this->assertEquals('http://del.icio.us/komagata', $feed->getLink());
        $this->assertEquals('http://del.icio.us/favicon.ico', $feed->getFavicon());
        $this->assertEquals('zuzara.com » 日本で公開されているAPI一覧（下書き）', $feed->data['items'][0]['title']);
        $this->assertEquals('http://blog.zuzara.com/2006/07/20/98/', $feed->data['items'][0]['link']);
        $this->assertEquals('webservice', $feed->data['items'][0]['category'][1]);
//print_r($feed->f);
    }

    function testRSS20()
    {
        $uri = 'http://plnet.jp/test_feed/p0t-rss20.xml';
        $feed = new FeedParser($uri);
        $feed->parse();
        $this->assertEquals('p0t', $feed->getTitle());
        $this->assertEquals('http://p0t.jp/mt/', $feed->getLink());
        $this->assertEquals('http://p0t.jp/mt/favicon.ico', $feed->getFavicon());
print_r($feed->data);

        $uri = 'http://plnet.jp/test_feed/d.hatena-rss20.xml';
        $feed = new FeedParser($uri);
        $feed->parse();
        $this->assertEquals('m-komagataの日記', $feed->getTitle());
        $this->assertEquals('http://d.hatena.ne.jp/m-komagata/', $feed->getLink());
        $this->assertEquals('http://d.hatena.ne.jp/images/de_favicon.ico', $feed->getFavicon());

        $items = $feed->getItems();
        $this->assertEquals('テストタイトル２', $items[0]['title']);
    }

    function testAtom03()
    {
        $uri = 'http://plnet.jp/test_feed/blogger-atom03.xml';
        $feed = new FeedParser($uri);
        $feed->parse();
        $this->assertEquals('p0t blogger', $feed->getTitle());
        $this->assertEquals('http://komagata.blogspot.com', $feed->getLink());
        $this->assertEquals('http://komagata.blogspot.com/favicon.ico', $feed->getFavicon());
//print_r($feed->data);
    }
}
?>
