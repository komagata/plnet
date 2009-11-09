<?php
/**
 * Generate An HTML Tag Cloud
 * @author astronote <http://astronote.jp/>
 */
class TagCloud
{
    var $counts;
    var $urls;
    var $isSorted;
    
    function TagCloud($isSorted = true)
    {
        $this->counts = array();
        $this->urls = array();
        $this->isSorted = $isSorted;
    }

    function add($tag, $url, $count)
    {
        $this->counts[$tag] = $count;
        $this->urls[$tag] = $url;
    }

    function css()
    {
        $css = '#htmltagcloud {}'."\n";
        for ($level = 0; $level <= 20; $level++) {
            $font = 10 + $level;
            $css .= "span.tagcloud$level {".
            "  font-size: ${font}px;".
            "  line-height: 125%;".
            "  letter-spacing: 0.5px;".
            "  margin-right: 2px;".
            "  margin-left: 2px;".
            "}\n";
            $css .= "span.tagcloud$level a {text-decoration: none;}\n";
        }
        return $css;
    }

    function html($limit = NULL)
    {
        $a = $this->counts;
        asort($a);
        $tags = array_keys(array_reverse($a));
        if (isset($limit)) {
            $tags = array_slice($tags, 0, $limit);
        }
        $n = count($tags);
        if ($n == 0) {
            return '';
        } elseif ($n == 1) {
            $tag = $tags[0];
            $url = $this->urls[$tag];
            return "<div id=\"htmltagcloud\"><span class=\"tagcloud1\"><a href=\"$url\">$tag</a></span></div>\n"; 
        }
        
        $min = sqrt($this->counts[$tags[$n - 1]]);
        $max = sqrt($this->counts[$tags[0]]);
        $factor = 0;
        
        // specal case all tags having the same count
        if (($max - $min) == 0) {
            $min -= 20;
            $factor = 1;
        } else {
            $factor = 20 / ($max - $min);
        }
        $html = '';
        if ($this->isSorted) {
          sort($tags); // order
        }
        foreach($tags as $tag) {
            $count = @$this->counts[$tag];
            $url   = @$this->urls[$tag];
            $level = (int)((sqrt($count) - $min) * $factor);
            $html .=  "<span class=\"tagcloud$level tag\"><a href=\"$url\">$tag</a></span>\n"; 
        }
        $html = "<div id=\"htmltagcloud\">$html</div>";
        return $html;
    }

    function htmlAndCSS($limit = NULL)
    {
        $html = "<style type=\"text/css\">\n" . $this->css() . "</style>" . $this->html($limit);
        return $html;
    }
}

/* test
$tags = array(
    array('tag' => 'blog', 'count' => 20),
    array('tag' => 'ajax', 'count' => 10),
    array('tag' => 'mysql', 'count'  => 5),
    array('tag' => 'hatena', 'count'  => 12),
    array('tag' => 'bookmark', 'count'  => 30),
    array('tag' => 'rss', 'count' => 1),
    array('tag' => 'atom', 'count' => 2),
    array('tag' => 'misc', 'count' => 10),
    array('tag' => 'javascript', 'count' => 11),
    array('tag' => 'xml', 'count' => 6),
    array('tag' => 'perl', 'count' => 32),
);

$cloud = new TagCloud();
foreach ($tags as $t) {
    $cloud->add($t['tag'], "http://<your.domain>/{$t['tag']}/", $t['count']);
}
print "<html><body>";
print $cloud->htmlAndCSS(20);
print "</body></html>";
*/
?>
