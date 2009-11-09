<?php
require_once 'FeedParser.php';
require_once 'Crawler.php';
require_once 'MemberUtils.php';

class AddFeedAction extends Action
{
    function isSecure()
    {
        return true;
    }

    function execute(&$controller, &$request, &$user)
    {
        $uri = $request->getParameter('uri');
        $member = $user->getAttribute('member', GLU_NS);

        $redirect = $request->hasParameter('redirect')
            ? $request->getParameter('redirect') : null;

        $cc_id = $this->getContentCategoryIdByUri($uri)
            ? $this->getContentCategoryIdByUri($uri) : 8;

        // get feed
        $f = new FeedParser($uri);
        $parse_result = $f->parse();
        if ($parse_result === false) {
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
            "Failed to parse feed uri: $uri", E_USER_NOTICE);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'false';
            return VIEW_NONE;
        }

        $name = $f->getTitle();
        $favicon = $f->getFavicon() 
            ? $f->getFavicon() : SCRIPT_PATH.'images/favicon.ico';

        $db =& DBUtils::connect(false);
        $db->query('BEGIN');

        // get feedId
        $feedId = $this->getFeedIdByUri($uri);
        if ($feedId === false) {
            // add feed

            $fields = array(
                'uri'             => $uri,
                'link'            => $f->getLink(),
                'title'           => $f->getTitle(),
                'description'     => $f->getDescription(),
                'favicon'         => $favicon,
                'lastupdatedtime' => date("Y-m-d H:i:s")
            );

            $res = $db->autoExecute('feed', $fields, DB_AUTOQUERY_INSERT);
            if (DB::isError($res)) {
                $db->rollback();
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                "Failed to insert. ".$res->toString(), E_USER_ERROR);
                header('Content-Type: text/plain; charset=utf-8');
                echo 'false';
                return VIEW_NONE;
            }

            // specific mysql
            $feedId = $db->getOne('SELECT LAST_INSERT_ID()');
        }


        // add member_to_feed
        $m2f_fields = array(
            'member_id' => MemberUtils::get_id_by_account($member->account),
            'feed_id'   => $feedId
        );

        if (DBUtils::get('member_to_feed', $m2f_fields)) {
            echo "'already exists'"; // already exists
            return VIEW_NONE;
        }

        $res = $db->autoExecute(
            'member_to_feed',
            $m2f_fields,
            DB_AUTOQUERY_INSERT
        );
        if (DB::isError($res)) {
            $db->rollback();
            trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
            "Failed to insert m2f. ".$res->toString(), E_USER_WARNING);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'false';
            return VIEW_NONE;
        }

        // add member_to_content_category_to_feed
        $m2cc2f_fields = array(
            'member_id' => $member->id,
            'content_category_id' => $cc_id,
            'feed_id'   => $feedId
        );
        $sql = "SELECT count(*)
            FROM member_to_content_category_to_feed
            WHERE member_id = ?
            AND content_category_id = ?
            AND feed_id = ? ";
        if ($db->getOne($sql, array_values($m2cc2f_fields)) == 0) {
            $res = $db->autoExecute('member_to_content_category_to_feed', $m2cc2f_fields,
            DB_AUTOQUERY_INSERT);
            if (DB::isError($res)) {
                $db->rollback();
                trigger_error(__CLASS__.'::'.__FUNCTION__.'(): '.
                'Failed to insert m2cc2f. '.$res->toString(), E_USER_WARNING);
                header('Content-Type: text/plain; charset=utf-8');
                echo 'false';
                return VIEW_NONE;
            }
        }

        // try to crawl
        $crawler =& new Crawler();
        $crawl_result = $crawler->crawl($uri);
        if ($crawl_result === false) {
            $db->rollback();
            trigger_error("AddFeedAction::execute(): Failed to crawl: $uri"
            , E_USER_NOTICE);
            header('Content-Type: text/plain; charset=utf-8');
            echo 'false';
            return VIEW_NONE;
        }

        $db->commit();

        if ($redirect) {
            Controller::redirect(SCRIPT_PATH.'setting/feed');
        } else {
            header('Content-Type: text/plain; charset=utf-8');
            echo 'true';
        }
        return VIEW_NONE;
    }

    function getFeedIdByUri($uri)
    {
        $feed = DB_DataObject::factory('feed');
        $feed->get('uri', $uri);
        if ($feed->id > 0) {
            return $feed->id;
        } else {
            return false;
        }
    }

    function getContentCategoryIdByUri($uri)
    {
        $formats = array();
        $content = DB_DataObject::factory('content');
        $content->find();
        while ($content->fetch()) {
            if($this->compareFormat($content->format, $uri) === true) {
                return $content->content_category_id;
            };
        }
        return false;
    }

    function getUserName($str_base, $str_new) {
        $parts = explode('##username##', $str_base);
        return str_replace($parts, '', $str_new);
    }

    function compareFormat($str_base, $str_new) {
        $username = $this->getUserName($str_base, $str_new);
        $ary1 = explode('##username##', $str_base);
        $ary2 = explode($username, $str_new);
        $diff = array_diff($ary1, $ary2);
        if(empty($diff) === true) {
            return true;
        }
        return false;
    }
}
?>
