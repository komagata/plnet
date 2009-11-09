<?php
if (!defined('RDFAPI_INCLUDE_DIR')) {
    define('RDFAPI_INCLUDE_DIR', BASE_LIB_DIR.'rdfapi-php/api/');
}
include_once RDFAPI_INCLUDE_DIR.'RdfAPI.php';
include_once RDFAPI_INCLUDE_DIR.'syntax/RdfParser.php';
include_once RDFAPI_INCLUDE_DIR.'vocabulary/FOAF_C.php';

class FOAFWriter
{
//  var $profile;
//  var $interests;
//  var $knows;

    function FOAFWriter()
    {
        $this->profile   = array();
        $this->interests = array();
        $this->knows     = array();
    }

    function setProfile($profile)
    {
        $this->profile = $profile;
    }

    function addInterest($interest)
    {
        $this->interests[] = $interest;
    }

    function addKnow($know)
    {
        $this->knows[] = $know;
    }

    function setKnows($knows)
    {
        $this->knows = $knows;
    }

    function setInterests($interests)
    {
        $this->interests = $interests;
    }

    function serialize($kind = '01')
    {
        $profile = $this->_array_q($this->profile);
        $knows = $this->_array_q($this->knows);
        $interests = $this->_array_q($this->interests);

        switch ($kind) {
        case '01':
        default:
            $doc = "<rdf:RDF
    xmlns:rdf=\"http://www.w3.org/1999/02/22-rdf-syntax-ns#\"
    xmlns:rdfs=\"http://www.w3.org/2000/01/rdf-schema#\"
    xmlns:foaf=\"http://xmlns.com/foaf/0.1/\"
    xmlns:bio=\"http://purl.org/vocab/bio/0.1/\"
    xmlns:dc=\"http://purl.org/dc/elements/1.1/\">
    <foaf:PersonalProfileDocument rdf:about=\"\">
        <maker rdf:nodeID=\"me\"/>
        <primaryTopic rdf:nodeID=\"me\"/>
    </foaf:PersonalProfileDocument>
    <foaf:Person rdf:nodeID=\"me\">\n";
            if (isset($profile['name'])) {
                $doc .= "        ".
                "<foaf:name>{$profile['name']}</foaf:name>\n";
            }
            if (isset($profile['firstName'])) {
                $doc .= "        ".
                "<foaf:firstName>{$profile['firstName']}</foaf:firstName>\n";
            }
            if (isset($profile['surname'])) {
                $doc .= "        ".
                "<foaf:surname>{$profile['surname']}</foaf:surname>\n";
            }

            if (isset($profile['nick'])) {
                $doc .= "        <foaf:nick>{$profile['nick']}</foaf:nick>\n";
            }
            if (isset($profile['bio'])) {
                $doc .= "        <bio:olb>{$profile['bio']}</bio:olb>\n";
            }
            if (isset($profile['mbox_sha1sum'])) {
                $mbox_sha1sum = sha1($profile['mbox_sha1sum']);
                $doc .= "        ".
                "<foaf:mbox_sha1sum>{$mbox_sha1sum}</foaf:mbox_sha1sum>\n";
            }
            if (isset($profile['img'])) {
                $doc .= "        ".
                "<foaf:img rdf:resource=\"{$profile['img']}\" />\n";
            }
            if (isset($profile['weblog'])) {
                $doc .= "        ".
                "<foaf:weblog rdf:resource=\"{$profile['weblog']}\" />\n";
            }
            if (isset($profile['gender'])) {
                $doc .= "        ".
                "<foaf:gender>{$profile['gender']}</foaf:gender>\n";
            }
            if (isset($profile['aimChatID'])) {
                $doc .= "        ".
                "<foaf:aimChatID>{$profile['aimChatID']}</foaf:aimChatID>\n";
            }
            if (isset($profile['msnChatID'])) {
                $doc .= "        ".
                "<foaf:msnChatID>{$profile['msnChatID']}</foaf:msnChatID>\n";
            }
            if (isset($profile['yahooChatID'])) {
                $doc .= "        ".
                "<foaf:yahooChatID>{$profile['yahooChatID']}</foaf:yahooChatID>\n";
            }
            if (isset($profile['jabberID'])) {
                $doc .= "        ".
                "<foaf:jabberID>{$profile['jabberID']}</foaf:jabberID>\n";
            }

/*
            if (isset($profile['homepage'])) {
                $doc .= "        ".
                "<foaf:homepage rdf:resource=\"{$profile['homepage']}\" />\n";
            }
*/
            foreach ($interests as $interest) {
                $doc .= "        ".
                "<foaf:interest dc:title=\"{$interest['title']}\" rdf:resource=\"{$interest['uri']}\" />\n";
            }

            foreach ($knows as $person) {
                $doc .= "        <foaf:knows>\n";
                $doc .= "            <foaf:Person>\n";

                if (isset($person['nick'])) {
                    $doc .= "                ".
                    "<foaf:nick>{$person['nick']}</foaf:nick>\n";
                }
                if (isset($person['mbox_sha1sum'])) {
                    $mbox_sha1sum = sha1($person['mbox_sha1sum']);
                    $doc .= "                ".
                    "<foaf:mbox_sha1sum>{$mbox_sha1sum}</foaf:mbox_sha1sum>\n";
                }
                if (isset($person['weblog'])) {
                    $doc .= "                ".
                    "<foaf:weblog rdf:resource=\"{$person['weblog']}\" />\n";
                }
                if (isset($person['img'])) {
                    $doc .= "                ".
                    "<foaf:img rdf:resource=\"{$person['img']}\" />\n";
                }
                if (isset($person['seeAlso'])) {
                    $doc .= "                ".
                    "<rdfs:seeAlso rdf:resource=\"{$person['seeAlso']}\" />\n";
                }

                $doc .= "            </foaf:Person>\n";
                $doc .= "        </foaf:knows>\n";
            }

            $doc .= "    </foaf:Person>\n";
            $doc .= "</rdf:RDF>\n";
        }

        return $doc;
    }

    function display($kind = '01')
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
