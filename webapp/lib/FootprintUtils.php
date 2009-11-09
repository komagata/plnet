<?php
require_once 'DBUtils.php';

class FootprintUtils
{
    function find_by_owner_id($owner_id, $limit = null)/*{{{*/
    {
        $db =& DBUtils::connect();
        $sql = 'SELECT m.id, m.nickname, m.account, m.photo, 
            UNIX_TIMESTAMP(fp.created_on) AS created_on
            FROM footprint fp
            JOIN member m ON fp.visitor_id = m.id
            WHERE fp.owner_id = ?
            ORDER BY fp.created_on DESC';
        if (!is_null($limit)) $sql .= " LIMIT $limit";
        $result = $db->getAll($sql, array($owner_id));
        if (DB::isError($result)) {
            trigger_error('FootprintUtils::find_by_owner_id(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }
        return $result;
    }/*}}}*/

    function is_exist_today($owner_id, $visitor_id)
    {
        $one_day_before = date("Y-m-d H:i:s", time() - 86400);

        $db =& DBUtils::connect();
        $sql = 'SELECT id FROM footprint fp
            WHERE owner_id = ?
            AND visitor_id = ?
            AND created_on > ?';
        $result = $db->getOne($sql, array(
            $owner_id, 
            $visitor_id, 
            $one_day_before
        ));

        if (DB::isError($result)) {
            trigger_error('FootprintUtils::is_exist_tody(): '.
                $result->toString(), E_USER_ERROR);
            return false;
        }

        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    function get_footprints($owner_id, $visitor_id)
    {
        $fps = FootprintUtils::find_by_owner_id($owner_id, 5);
        $footprints = array();
        foreach ($fps as $fp) {
            $fp['profile_icon'] = $fp['photo']
                ? SCRIPT_PATH."photo.php?member_id={$fp['id']}"
                : SCRIPT_PATH.'images/profile_icon.jpg';
            $fp['name'] = $fp['nickname'] ? $fp['nickname'] : $fp['account'];
            $fp['link'] = SCRIPT_PATH.$fp['account'].'/';
            $footprints[] = $fp;
        }

        if (!$visitor_id) {
            array_unshift($footprints, array(
                'profile_icon' => SCRIPT_PATH.'images/profile_icon.jpg',
                'name' => msg('not logined user message'),
                'link' => SCRIPT_PATH
            ));

            if (count($footprints) > 5) array_pop($footprints);
        }
        return $footprints;
    }
}
?>
