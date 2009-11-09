<?php
class OPMLWriter
{
    function OPMLWriter()
    {
        $this->struct = array(
            'head'     => array(), 
            'outlines' => array(), 
        );
    }

    function setHead($channel)
    {
        $this->struct['head'] = $channel;
    }

    function setOutlines($outlines)
    {
        $this->struct['outlines'] = $outlines;
    }

    function addOutline($outline)
    {
        $this->struct['outlines'][] = $outline;
    }

    function serialize($kind = 'opml11')
    {
        $struct = $this->_array_q($this->struct);
        $head =& $struct['head'];
        $outlines =& $struct['outlines'];

        switch ($kind) {
        case 'opml11':
        default:
            $date = $this->_to_rfc822($head['date']);

            $doc = "<?xml version=\"1.0\" encoding=\"utf-8\" ?>\n";
            $doc .= "<opml version=\"1.1\">\n";
            $doc .= "    <head>\n";
            $doc .= "        <title>{$head['title']}</title>\n";
            $doc .= "        <dateCreated>{$date}</dateCreated>\n";
            $doc .= "        <ownerName>{$head['owner']}</ownerName>\n";
            $doc .= "    </head>\n";
            $doc .= "    <body>\n";
            foreach ($outlines as $outline) {
                $doc .= "        <outline\n".
                "            title=\"{$outline['title']}\"\n".
                "            text=\"{$outline['text']}\"\n".
                "            htmlUrl=\"{$outline['link']}\"\n".
                "            xmlUrl=\"{$outline['uri']}\"\n".
                "            type=\"rss\" />\n";
            }
            $doc .= "    </body>\n";
            $doc .= "</opml>\n";
        }

        return $doc;
    }

    function display($kind = 'opml11')
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

    function _to_rfc822($date)
    {
        return date("D, d M Y H:i:s T", $date);
    }
}
?>
