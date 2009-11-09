<?php
require_once 'PHP/Compat/Function/file_get_contents.php';
require_once 'XML/XML_HTMLSax.php';
require_once 'Net/URL.php';
require_once 'Date.php';
require_once 'simplepie.inc';

class FeedParser
{
    var $uri = '';
    var $data = null;
    var $parser = null;

    function FeedParser($uri = null)
    {
        if (!is_null($uri)) {
            $this->uri = $uri;
        }
        $this->date = null;
        $this->parser = null;
    }

    function isValid()
    {
        $feed = new SimplePie();
        $feed->feed_url($this->uri);
        $feed->cache_location(SIMPLEPIE_CACHE_DIR);

        // parse
        $parse_result = @$feed->init();
        if ($parse_result === true) {
            return true;
        } else {
            trigger_error("FeedParser::parse(): Not valid feed. uri: {$this->uri}", E_USER_NOTICE);
            return false;
        }
    }

    function parse($caching = true)
    {
        $feed = new SimplePie();
        $feed->feed_url($this->uri);
        $feed->cache_location(SIMPLEPIE_CACHE_DIR);
        $feed->enable_caching($caching);

        // parse
        $parse_result = @$feed->init();
        if ($parse_result === false) {
            trigger_error("FeedParser::parse(): Failed to parse feed. uri: {$this->uri}", E_USER_NOTICE);
            return false;
        }

        $feed->handle_content_type();
        $link = $feed->get_feed_link();

        $channel = array(
            'title' => $feed->get_feed_title(),
            'link' => $link,
            'uri' => $this->uri,
            'last_modified' => SimplePie_Sanitize::parse_date($feed->data['last-modified']),
            'description' => $feed->get_feed_description()
        );

        // url
        $url = new Net_URL($feed->get_feed_link());

        $items = array();
        $feed_items = @$feed->get_items();
        if (is_array($feed_items)) {
            foreach ($feed_items as $item) {

                // category
                $categories = $item->get_category();
                if ($categories != '') {
                    $category = split(" ", $categories);
                    $category = array_trim($category);
                } else {
                    $category = '';
                }

                // author
                $author = '';
                if (is_array($authors = $item->get_authors())) {
                    $men = array();
                    foreach ($item->get_authors() as $man) {
                        $men[] = $man->get_name();
                    }
                    $author = join(', ', $men);
                }

                // description
                $description = $item->get_description();
                if (empty($description)) $description = '';
                $items[] = array(
                    'title' => $item->get_title(),
                    'uri' => $item->get_permalink(),
                    'description' => $description,
                    'date' => $item->get_date('U'),
                    'author' => $author,
                    'category' => $category
                );
            }
        } 

        $this->data = array(
            'channel' => $channel,
            'items' => $items
        );

        $this->parser =& $feed;
        unset($feed);
        return true;
    }

    function getTitle()
    {
        return $this->data['channel']['title'];
    }

    function getLink()
    {
        return $this->data['channel']['link'];
    }

    function getLastModified()
    {
        return $this->data['channel']['last_modified'];
    }

    function getDescription()
    {
        return $this->data['channel']['description'];
    }

    function getFavicon()
    {
        $handler = new FaviconHandler();
        $parser =& new XML_HTMLSax();
        $parser->set_object($handler);
        $parser->set_element_handler('openHandler', 'closeHandler');

        $link = $this->getLink();
        $doc = @file_get_contents($link);
        if ($doc == false) {
            trigger_error("FeedParser::getFavicon(): Failed to get contents: $link", E_USER_NOTICE);
        }
        $res = @$parser->parse($doc);
        $favicon = $handler->favicon;

        // cocolog
        if (preg_match("/^.+cocolog-nifty.com.*$/", $favicon)) {
            $favicon = 'http://app.cocolog-nifty.com/favicon.ico';
        }

        // jugem
        if (preg_match("/^.+jugem.cc.*$/", $favicon)) {
            $favicon = 'http://jugem.jp/jugem.ico';
        }

        // root path
        $url = new Net_URL($link);
        if (preg_match("/^\/.*/", $favicon)) {
            $favicon = $url->protocol . '://' . $url->host . $favicon;
        }
        
        // not found
        if (empty($favicon)) {
            $favicon = $url->protocol . '://' . $url->host . '/favicon.ico';
        }

        return $favicon;
    }

    function getChannel()
    {
        return array(
            'title'         => $this->getTitle(),
            'link'          => $this->getLink(),
            'uri'           => $this->uri,
            'favicon'       => $this->getFavicon(),
            'last_modified' => SimplePie_Sanitize::parse_date($this->getLastModified()),
            'description'   => $this->getDescription()
        );

        return $this->data['channel'];
    }

    function getItems()
    {
        return $this->data['items'];
    }

    function toArray()
    {
        return $this->data;
    }

    function toJSON()
    {
        $haj = new HTML_AJAX_JSON();
        return $haj->encode($this->toArray());
    }
}

class FaviconHandler
{
    var $favicon;
    function openHandler(&$parser, $name, $attrs)
    {
        if ($name == 'link' and isset($attrs['rel']) 
        and (strtolower($attrs['rel']) == 'shortcut icon'
        or strtolower($attrs['rel']) == 'icon')) {
            $this->favicon = $attrs['href'];
        }
    }
    function closeHandler(&$parser, $data) {}
}

function array_trim($array) {
    if (is_array($array)) {
        foreach ($array as $key => $value) {
            $array[$key] = trim($value);
        }
    }
    return $array;
}
?>
