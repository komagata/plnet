<?php
require_once 'FeedParser.php';
require_once 'DB/DataObject.php';
require_once 'Utils.php';
require_once 'LogUtils.php';
require_once 'DBUtils.php';

class Crawler
{
    var $db;

    function Crawler()
    {
        $this->db = DBUtils::connect(false);
    }

    function crawlAll()
    {
        LogUtils::debug('[Crawling start]');
        $feeds = $this->getFeeds();
        $cnt = count($feeds);
        $success = 0;
        foreach ($feeds as $uri) {
            $result = $this->crawl($uri);
            if ($result === false) {
                LogUtils::debug("Crawl failed: $uri");
            } else {
                $success++;
                LogUtils::debug("[Crawling: {$success}/{$cnt}]");
                LogUtils::debug("Memory usage: ".
                number_format(memory_get_usage()));
            }
        }
        LogUtils::debug("[Crawling finished: {$success}/{$cnt}]");
    }

    function crawl($uri)
    {
        $this->db->query('BEGIN');

        LogUtils::debug("Feed: $uri");

        $parser =& new FeedParser($uri);
        if ($parser->parse($caching) === false) {
            trigger_error("Crawler::crawl(): Failed to parse feed: $uri", E_USER_NOTICE);
            return false;
        }

        $channel = $parser->getChannel();

        // feed
        if ($this->feedIsExists($channel['uri'])) {
            LogUtils::debug("Feed is exsists");
            if ($this->feedIsUpdated($channel)) {
                LogUtils::debug("Feed is updated");
                $feedId = $this->feedUpdate($channel);
                if ($feedId === false) return false;
            } else {
                LogUtils::debug("Feed is not updated");
                $feedId = $this->getFeedId($channel['uri']);
            }
        } else {
            LogUtils::debug("Feed is not exsists");
            $feedId = $this->feedInsert($channel);
            if ($feedId === false) return false;
        }

        // items
        foreach ($parser->getItems() as $item) {
            LogUtils::debug("Entry: {$item['uri']}");

            if ($this->entryIsExists($feedId, $item['uri'])) {
                LogUtils::debug("Entry is exsists");

                if ($this->entryIsUpdated($feedId, $item)) {
                    LogUtils::debug("Entry is updated");
                    $entryId = $this->entryUpdate($feedId, $item);
                    if ($entryId === false) return false;
                } else {
                    LogUtils::debug("Entry is not updated");
                    $entryId = $this->getEntryId($feedId, $item['uri']);
                }
            } else {
                LogUtils::debug("Entry is not exsists");
                $entryId = $this->entryInsert($feedId, $item);
                if ($entryId === false) return false;
            }

            // tags
            if (is_array($item['category'])) {
                $item['category'] = $this->array_trim_lower_uniq($item['category']);
                LogUtils::debug("Tags is exsists");

                // tag
                foreach ($item['category'] as $tag) {
                    LogUtils::debug("Tag: $tag");
                    if ($this->tagIsExists($tag)) {
                        LogUtils::debug("Tag is exsists");
                        $tagId = $this->getTagId($tag);
                    } else {
                        LogUtils::debug("Tag is not exsists");
                        $tagId = $this->tagInsert($tag);
                        if ($tagId === false) return false;
                    }
                }

                // entry to tag
                if ($this->tagsIsUpdated($entryId, $item['category'])) {
                    // delete and insert
                    LogUtils::debug("Tags is updated");
                    $res = $this->tagsReplace($entryId, $item['category']);
                    if ($res === false) return false;

                } else {
                    LogUtils::debug("Tags is not updated");
                }
            } else {
                LogUtils::debug("Tags is not exsists");
            }
        }

        $this->db->commit();
        return true;
    }

    function feedIsExists($uri)
    {
        $cnt = $this->db->getOne('SELECT COUNT(id) FROM feed WHERE uri = ?', array($uri));
        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    function feedIsUpdated($feed)
    {
        $sql = 'SELECT * FROM feed WHERE uri = ?';
        $storedFeed = $this->db->getRow($sql, array($feed['uri']));

//        print_r($storedFeed); print_r($feed);

        if ($storedFeed['link'] == $feed['link'] and 
        $storedFeed['title'] == $feed['title'] and 
        $storedFeed['description'] == $feed['description'] and
        $storedFeed['favicon'] == $feed['favicon']) {
            return false;
        } else {
            return true;
        }
    }

    function getFeedId($uri)
    {
        $sql = 'SELECT id FROM feed WHERE uri = ?';
        $id = $this->db->getOne($sql, array($uri));
        return $id;
    }

    function feedInsert($feed)
    {
        $fields = array(
            'uri'             => $feed['uri'],
            'link'            => $feed['link'],
            'title'           => $feed['title'],
            'description'     => $feed['description'],
            'favicon'         => $feed['favicon'],
            'lastupdatedtime' => $feed['last_modified']
        );

        $res = $this->db->autoExecute('feed', $fields, DB_AUTOQUERY_INSERT);
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::feedInsert(): Failed to insert. '.$res->toString(), E_USER_WARNING);
            return false;
        } else {
            // specific mysql
            $id = $this->db->getOne('SELECT LAST_INSERT_ID()');
            LogUtils::debug("Feed insert: $id");
            return $id;
        }
    }

    function feedUpdate($feed)
    {
        $sql = 'SELECT id FROM feed WHERE uri = ?';
        $id = $this->db->getOne($sql, array($feed['uri']));

        $fields = array(
            'uri'             => $feed['uri'],
            'link'            => $feed['link'],
            'title'           => $feed['title'],
            'description'     => $feed['description'],
            'favicon'         => $feed['favicon'],
            'lastupdatedtime' => $feed['last_modified']
        );

        $res = $this->db->autoExecute('feed', $fields, DB_AUTOQUERY_UPDATE, "id = $id");
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::feedUpdate(): Failed to update. '.$res->toString(), E_USER_WARNING);
            return false;
        } else {
            LogUtils::debug("Feed update: $id");
            return $id;
        }
    }

    function entryIsExists($feedId, $entryUri)
    {
        $sql = 'SELECT COUNT(id) FROM entry WHERE feed_id = ? AND uri_md5 = ?';
        $cnt = $this->db->getOne($sql, array($feedId, md5($entryUri)));
        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    function entryIsUpdated($feedId, $entry)
    {
        $sql = 'SELECT id, uri, title, author, 
            description, UNIX_TIMESTAMP(date) AS date 
            FROM entry WHERE feed_id = ? AND uri_md5 = ?';
        $storedEntry = $this->db->getRow($sql, array($feedId, md5($entry['uri'])));

//        print_r($storedEntry); print_r($entry);

        if ($storedEntry['title'] == $entry['title'] and 
        $storedEntry['description'] == $entry['description'] and 
        $storedEntry['author'] == $entry['author'] and 
        $storedEntry['date'] == $entry['date']) {
            return false;
        } else {
            return true;
        }
    }

    function getEntryId($feedId, $entryUri)
    {
        $sql = 'SELECT id FROM entry WHERE feed_id = ? AND uri_md5 = ?';
        $id = $this->db->getOne($sql, array($feedId, md5($entryUri)));
        return $id;
    }

    function entryInsert($feedId, $entry)
    {
        $fields = array(
            'feed_id'         => $feedId,
            'uri'             => $entry['uri'],
            'uri_md5'         => md5($entry['uri']),
            'title'           => $entry['title'],
            'description'     => $entry['description'],
            'author'          => $entry['author'],
            'date'            => date("Y-m-d H:i:s", $entry['date']),
            'lastupdatedtime' => date("Y-m-d H:i:s")
        );

        $res = $this->db->autoExecute('entry', $fields, DB_AUTOQUERY_INSERT);
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::entryInsert(): Failed to insert. '.$res->toString(), E_USER_WARNING);
            return false;
        } else {
            // specific mysql
            $id = $this->db->getOne('SELECT LAST_INSERT_ID()');
            LogUtils::debug("Entry insert: $id");
            return $id;
        }
    }

    function entryUpdate($feedId, $entry)
    {
        $sql = 'SELECT id FROM entry WHERE feed_id = ? AND uri_md5 = ?';
        $id = $this->db->getOne($sql, array($feedId, md5($entry['uri'])));

        $fields = array(
            'feed_id'         => $feedId,
            'uri'             => $entry['uri'],
            'uri_md5'         => md5($entry['uri']),
            'title'           => $entry['title'],
            'description'     => $entry['description'],
            'author'          => $entry['author'],
            'date'            => date("Y-m-d H:i:s", $entry['date']),
            'lastupdatedtime' => date("Y-m-d H:i:s")
        );

        $res = $this->db->autoExecute('entry', $fields, DB_AUTOQUERY_UPDATE, "id = $id");
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::entryUpdate(): Failed to update. '.$res->toString(), E_USER_WARNING);
            return false;
        } else {
            LogUtils::debug("Entry update: $id");
            return $id;
        }
    }

    function tagIsExists($tag)
    {
        $sql = 'SELECT COUNT(id) FROM tag WHERE name = ?';
        $cnt = $this->db->getOne($sql, array($tag));
        if ($cnt > 0) {
            return true;
        } else {
            return false;
        }
    }

    function getTagId($tag)
    {
        $sql = 'SELECT id FROM tag WHERE name = ?';
        $id = $this->db->getOne($sql, array($tag));
        return $id;
    }

    function tagInsert($tag)
    {
        $fields = array(
            'name'        => $tag,
            'updatedtime' => date("Y-m-d H:i:s")
        );

        $res = $this->db->autoExecute('tag', $fields, DB_AUTOQUERY_INSERT);
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::tagInsert(): Failed to insert. '
            .$res->toString(), E_USER_WARNING);
            return false;
        } else {
            // specific mysql
            $id = $this->db->getOne('SELECT LAST_INSERT_ID()');
            LogUtils::debug("Tag insert: $id");
            return $id;
        }
    }

    function tagsIsUpdated($entryId, $tags)
    {
        $sql = 'SELECT name FROM tag t
            JOIN entry_to_tag e2t ON t.id = e2t.tag_id
            WHERE entry_id = ?';
        $stored = $this->db->getAll($sql, array($entryId));
        if (DB::isError($stored)) {
            $this->db->rollback();
            trigger_error('Crawler::tagsIsUpdated(): Failed to select. '
            .$stored->toString(), E_USER_WARNING);
        }

        $storedTags = array();
        foreach ($stored as $row) {
            $storedTags[] = $row['name'];
        }

        LogUtils::debug('Compare: '.join(" ", $storedTags).' <-> '.
        join(" ", $tags));

        $res = $this->array_compare($storedTags, $tags);
        if ($res) {
            return false;
        } else {
            return true;
        }
    }

    function tagsReplace($entryId, $tags)
    {
        $sql = 'DELETE FROM entry_to_tag WHERE entry_id = ?';
        $res = $this->db->query($sql, array($entryId));
        if (DB::isError($res)) {
            $this->db->rollback();
            trigger_error('Crawler::tagsReplace(): Failed to delete. Feed URL:'
            .$this->uri.' '.$res->toString(), E_USER_WARNING);
            return false;
        }

        LogUtils::debug("EntryToTag: Delete at entry_id = $entryId");

        $date = date("Y-m-d H:i:s");
        foreach ($tags as $tag) {
            $tagId = $this->getTagId($tag);

            $fields = array(
                'entry_id' => $entryId,
                'tag_id' => $tagId
            );

            $res = $this->db->autoExecute(
            'entry_to_tag', $fields, DB_AUTOQUERY_INSERT);
            if (DB::isError($res)) {
                $this->db->rollback();
                trigger_error('Crawler::tagsReplace(): Failed to insert. '
                .$res->toString(), E_USER_WARNING);
                return false;
            }
        }
    }

    function getFeeds()
    {
        $sql = 'SELECT uri FROM feed ORDER BY uri';
        $res = $this->db->getAll($sql);
        if (DB::isError($res)) {
            trigger_error('Crawler::getFeeds(): Failed to select. '
            .$res->toString(), E_USER_WARNING);
        }

        $feeds = array();
        foreach ($res as $row) {
            $feeds[] = $row['uri'];
        }
        return $feeds;
    }

    function array_compare($aru, $imi)
    {
        sort($aru);
        sort($imi);
        $max = count($aru) > count($imi) ? count($aru) : count($imi);
        $res = true;
        for ($i = 0; $i <$max; $i++) {
            if (mb_strtolower($aru[$i]) !== mb_strtolower($imi[$i])) $res = false;
        }
        return $res;
    }

    function array_trim_lower_uniq($array)
    {
        $result = array();
        foreach ($array as $key => $value) {
            $result[$key] = mb_strtolower(trim($value));
        }
        return array_unique($result);
    }
}
?>
