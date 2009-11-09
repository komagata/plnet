<?php
require_once dirname(__FILE__) . '/FeedParser.php';
require_once 'HTML/AJAX/JSON.php';

class CachedFeed
{
    var $uri = '';
    var $struct = null;

    function CachedFeed($uri)
    {
        $this->uri = $uri;
        $config = parse_ini_file(dirname(dirname(__FILE__)) . '/configs/db.ini');
        $options = &PEAR::getStaticProperty('DB_DataObject' ,'options');
        $options = $config;
    }

    function parse()
    {
        $feed = DB_DataObject::factory('feed');
        $feed->uri = $this->uri;

        // was cached
        if ($feed->count() > 0) {
           $this->struct = $this->_getCache($this->uri); 

        // no cache
        } else {
            $f = new FeedParser($this->uri);
            $f->parse();
            $this->struct = $f->toArray();
        }

    }

    function _getCache($uri)
    {
        $feed = DB_DataObject::factory('feed');
        $feed->uri = $uri;
        $feed->find();
        $feed->fetch();
        
        $channel = array(
            'title' => $feed->title,
            'link' => $feed->link,
            'favicon' => $feed->favicon
        );

        // items
        $items = array();
        $f2e = DB_DataObject::factory('feed_to_entry');
        $f2e->feed_id = $feed->id;
        $f2e->find();
        while ($f2e->fetch()) {
            $entry = DB_DataObject::factory('entry');
            $entry->id = $f2e->entry_id;
            $entry->find();
            while ($entry->fetch()) {
                $item = array();
                $item['title'] = $entry->title;
                $item['link'] = $entry->uri;
                $item['description'] = $entry->description;
                $item['date'] = $entry->date;
                $items[] = $item;
            }
        }

        $struct = array(
            'channel' => $channel,
            'items' => $items
        );
        return $struct;
    }

    function getTitle()
    {
        $channel = $this->getChannel();
        return $channel['title'];
    }

    function getLink()
    {
        $channel = $this->getChannel();
        return $channel['link'];
    }

    function getFavicon()
    {
        $channel = $this->getChannel();
        return $channel['favicon'];
    }

    function getChannel()
    {
        return $this->struct['channel'];
    }

    function getItems()
    {
        return $this->struct['items'];

    }

    function toArray()
    {
        return $this->struct;
    }

    function toJSON()
    {
        $haj = new HTML_AJAX_JSON();
        return $haj->encode($this->toArray());
    }
}
?>
