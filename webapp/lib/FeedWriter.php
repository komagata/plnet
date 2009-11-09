<?php
class FeedWriter
{
    function FeedWriter()
    {
        $this->feed = array(
            'channel' => array(), 
            'items' => array(), 
        );
    }

    function setChannel($channel)
    {
        $this->feed['channel'] = $channel;
    }

    function setItems($items)
    {
        $this->feed['items'] = $items;
    }

    function addItem($item)
    {
        $this->feed['items'][] = $item;
    }

    function serialize($kind = 'rss10')
    {
        $doc = '';
        $feed =& $this->feed;
        $channel =& $feed['channel'];
        $items =& $feed['items'];
        $channel = $this->_array_q($channel);
        $items = $this->_array_q($items);

        switch ($kind) {
        case 'rss20':
            $doc = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
            $doc .= "<rss version=\"2.0\"\n";
            $doc .= "    xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
            $doc .= "    xmlns:content=".
            "\"http://purl.org/rss/1.0/modules/content/\"\n";
            $doc .= "    xml:lang=\"ja\">\n";
            $doc .= "    <channel>\n";
            $doc .= "        <title>{$channel['title']}</title>\n";
            $link = htmlspecialchars($channel['link'], ENT_QUOTES);
            $doc .= "        <link>{$link}</link>\n";
            $doc .= "        <description>".
            "{$channel['description']}</description>\n";

            foreach ($items as $item) {
                $doc .= "        <item>\n";
                $doc .= "            <title>{$item['title']}</title>\n";

                $doc .= "            <link>{$item['link']}</link>\n";
                $doc .= "            ".
                "<description>{$item['description']}".
                "</description>\n";
                $doc .= "            ".
                "<dc:creator>{$item['author']}</dc:creator>\n";

                if (isset($item['tags']) and is_array($item['tags'])) {
                    foreach ($item['tags'] as $tag) {
                        $doc .= "            ".
                        "<dc:subject>{$tag}</dc:subject>\n";
                    }
                }

                $doc .= "            <pubDate>".
                date(DATE_RFC822, $item['date'])."</pubDate>\n";
                $doc .= "        </item>\n";
            }
             
            $doc .= "    </channel>\n";
            $doc .= "</rss>\n";

            break;
        case 'atom03':
            break;
        case 'rss10':
        default:
            $doc = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
            $doc .= "<rdf:RDF\n";
            $doc .= "    xmlns=\"http://purl.org/rss/1.0/\"\n";
            $doc .= "    ".
            "xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"\n";
            $doc .= "    ".
            "xmlns:content=\"http://purl.org/rss/1.0/modules/content/\"\n";
            $doc .= "    ".
            "xmlns:dc=\"http://purl.org/dc/elements/1.1/\"\n";
            $doc .= "    xml:lang=\"ja\">\n";
            $doc .= "    <channel rdf:about=\"{$channel['uri']}\">\n";
            $doc .= "        <title>{$channel['title']}</title>\n";
            $doc .= "        <link>{$channel['link']}</link>\n";
            $doc .= "        <description>".
            "{$channel['description']}</description>\n";
            $doc .= "        <items>\n";
            $doc .= "            <rdf:Seq>\n";
            foreach ($items as $item) {
                $doc .= "                ".
                "<rdf:li rdf:resource=\"{$item['link']}\" />\n";
            }
            $doc .= "            </rdf:Seq>\n";
            $doc .= "        </items>\n";
            $doc .= "    </channel>\n";
            foreach ($items as $item) {
                $doc .= "    <item rdf:about=\"{$item['link']}\">\n";
                $doc .= "        <title>{$item['title']}</title>\n";
                $doc .= "        <link>{$item['link']}</link>\n";
                $doc .= "        ".
                "<description>{$item['description']}</description>\n";
                $doc .= "        ".
                "<dc:creator>{$item['author']}</dc:creator>\n";

                if (isset($item['tags']) and is_array($item['tags'])) {
                    foreach ($item['tags'] as $tag) {
                        $doc .= "        ".
                        "<dc:subject>{$tag}</dc:subject>\n";
                    }
                }

                $date_w3c = $this->_to_w3cdtf($item['date']);
                $doc .= "        <dc:date>{$date_w3c}</dc:date>\n";
                $doc .= "    </item>\n";
            }

            $doc .= "</rdf:RDF>\n";
        }

        return $doc;
    }

    function display($kind = 'rss10')
    {
        header('Content-Type: application/xml; charset=utf-8');
        echo $this->serialize($kind);
    }

    function _array_q($target)
    {
        if (is_array($target)) {
            foreach ($target as $key => $value) {
                $target[$key] = $this->_array_q($value);
            }
            return $target;
        } else {
            return $this->_q($target);
        }
    }

    function _q($str)
    {
        return htmlspecialchars($str, ENT_QUOTES);
    }

    function _to_w3cdtf($date)
    {
         $d = date('Y-m-d\TH:i:s', $date);
         $tz = date('O', $date);
         return $d . $tz{0} . $tz{1} . $tz{2} . ':' . $tz{3} . $tz{4};
    }
}
?>
