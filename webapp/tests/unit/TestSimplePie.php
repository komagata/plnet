<?php
require_once 'unit_tester.php';
require_once 'simplepie.inc';

class TestSimplePie extends UnitTestCase
{
    var $urls = array();

    function setUp()
    {
        $urls = array(
            'hatena1' => 'd.hatena.diary.m-komagata-rss.xml',
            'hatena2' => 'd.hatena.diary.m-komagata-rss2.xml',
            'delicious' => 'del.icio.us.komagata.rss.xml',
            'blogger' => 'komagata.blogspot.com-atom.xml',
            'mt' => 'p0t.jp-atom.xml'
        );

        foreach ($urls as $name => $value) {
            $this->urls[$name] = SCRIPT_PATH."test/$value";
        }
    }

    function testParse()
    {
        foreach ($this->urls as $url) {
            $sp = new SimplePie();
            $sp->feed_url($url);
            $sp->enable_caching(false);
            $this->assertTrue($sp->init());
        }
    }

    function testGetFeedTitle()
    {
        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena1']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_title(), 'm-komagataの日記');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena2']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_title(), 'm-komagataの日記');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['delicious']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_title(), 'del.icio.us/komagata');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['blogger']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_title(), 'Plnet ChangeLog');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['mt']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_title(), 'p0t');
    }

    function testGetFeedDescription()
    {
        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena1']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_description(), 'm-komagataの日記');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena2']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_description(), 'm-komagataの日記');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['delicious']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_description(), '');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['blogger']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_description(), 'Plnet開発日誌');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['mt']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_description(), 'computing and gaming');
    }

    function testGetFeedLink()
    {
        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena1']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_link(), 'http://d.hatena.ne.jp/m-komagata/');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena2']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_link(), 'http://d.hatena.ne.jp/m-komagata/');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['delicious']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_link(), 'http://del.icio.us/komagata');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['blogger']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_link(), 'http://komagata.blogspot.com');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['mt']);
        $sp->enable_caching(false);
        $sp->init();
        $this->assertEqual($sp->get_feed_link(), 'http://p0t.jp/mt/');
    }

    function testGetEntryTitle()
    {
        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena1']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'Ruby勉強日記');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'テスト３');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena2']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'Ruby勉強日記');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'テスト３');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['delicious']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'CSSとJavaScriptでブロック要素の角を自在に操るライブラリ『Transcorners』:phpspot開発日誌');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'ffdshowより便利なコーデックセット「CCCP」 - GIGAZINE');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['blogger']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), '新サーバへ移行（速くなった）');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'デベロッパー向けページ');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['mt']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), '新サーバが良い');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'DB  INI');
    }

    function testGetEntryTitle()
    {
        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena1']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'Ruby勉強日記');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'テスト３');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['hatena2']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'Ruby勉強日記');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'テスト３');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['delicious']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), 'CSSとJavaScriptでブロック要素の角を自在に操るライブラリ『Transcorners』:phpspot開発日誌');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'ffdshowより便利なコーデックセット「CCCP」 - GIGAZINE');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['blogger']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), '新サーバへ移行（速くなった）');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'デベロッパー向けページ');

        $sp = new SimplePie();
        $sp->feed_url($this->urls['mt']);
        $sp->enable_caching(false);
        $sp->init();
        $item = $sp->get_item(0);
        $this->assertEqual($item->get_title(), '新サーバが良い');
        $item1 = $sp->get_item(1);
        $this->assertEqual($item1->get_title(), 'DB  INI');
    }

}
?>
