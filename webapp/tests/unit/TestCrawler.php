<?php
require_once 'unit_tester.php';
require_once 'Crawler.php';
require_once 'INILoader.php';

class TestCrawler extends UnitTestCase
{
    var $urls = array(
        'hatena1' => 'd.hatena.diary.m-komagata-rss.xml',
        'hatena2' => 'd.hatena.diary.m-komagata-rss2.xml',
        'delicious' => 'del.icio.us.komagata.rss.xml',
        'blogger' => 'komagata.blogspot.com-atom.xml',
        'mt' => 'p0t.jp-atom.xml'
    );

    function setUp()
    {
        global $conf;
        load_ini(FIXTURES_DIR, $conf['dsn']);

        foreach ($this->urls as $name => $value) {
            $this->urls[$name] = SCRIPT_PATH."test/$value";
        }
    }

    function testParse()
    {
        foreach ($this->urls as $url) {
            $crawler =& new Crawler();
            //$crawler->crawl($url);
        }
    }

    function tearDown()
    {
        global $conf;
        load_ini(FIXTURES_DIR, $conf['dsn']);
    }
}
?>
