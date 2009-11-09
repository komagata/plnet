<?php
require_once 'DBUtils.php';
require_once 'FOAFParser.php';

class FriendUtils
{
    function find_by_account($account, $limit = null)
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT f.id, f.url AS url FROM foaf f
            JOIN member m ON f.member_id = m.id
            WHERE m.account = ?';

        $result = $db->getAll($sql, array($account));
        if (DB::isError($result)) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
            $result->toString(), E_USER_ERROR);
            return null;
        }

        $parser =& new FOAFParser(true, CACHE_LITE_DIR, 60*60*24*1);

        $foafs = array();
        foreach ($result as $foaf) {
            if (@$parser->parse($foaf['url']) === false) {
                continue;
            }
            $people = $parser->getKnowsPerson();
            foreach ($people as $index => $person) {
                $people[$index]['foaf_id'] = $foaf['foaf_id'];
                $people[$index]['foaf_url'] = $foaf['url'];


                $p =& new FOAFParser(true, CACHE_LITE_DIR, 60*60*24*1);
                if ($p->parse($person['seeAlso'])) {
                    $person['img'] = $p->getImg();
                }

                $foafs[$person['seeAlso']] = $person;
            }
        }
        $res = array();
        foreach ($foafs as $foaf) $res[] = $foaf;
        return $res;
    }

    function asoc_merge($asoc1, $asoc2)
    {
        foreach ($asoc2 as $key => $value) {
            $asoc1[] = $value;
        }
        return $asoc1;
    }
}
?>
