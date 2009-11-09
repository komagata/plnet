<?php
/**
 * PHP_Mixi
 * mixi(mixi.jp)にアクセスするためのクラス
 *
 * @package PHP_Mixi
 * @author riaf <webmaster@riaf.org>
 * @since PHP 4.0.3
 * @version $Id: PHP_Mixi.php,v 0.1.1 2006/02/08 riaf Exp $
 */
class PHP_Mixi {

    /**
     * @var string ログインメールアドレス
     */
    var $email = "";

    /**
     * @var string ログインパスワード
     */
    var $password = "";

    /**
     * @var string Mixiのアドレス
     */
    var $base_url = "http://mixi.jp/";

    /**
     * @var int キャッシュの設定
     */
    var $use_cache = false;

    /**
     * @var string キャッシュディレクトリ
     */
    var $cache_dir = "./cache/";

    /** 
     * @var string キャッシュファイルの接頭語 
     */ 
    var $cache_prefix = "phpmixi_";

    /** 
     * @var string キャッシュファイルの拡張子(PHPとして動作するもの)
     */ 
    var $cache_ext = ".tmp.php";

    /** 
     * @var string キャッシュの有効時間(秒)
     */ 
    var $cache_time = 3600; 

    /** 
     * @var string キャッシュファイル名のハッシュキー 
     */ 
    var $cache_salt = "phpMixi"; 

    var $mixi_cal_icon = array(
        'i_sc-.gif' => '予定',
        'i_bd.gif'  => '誕生日',
        'i_iv1.gif' => '参加イベント',
        'i_iv2.gif' => 'イベント'
    ); 
    
    var $mixi_diary_formval = array(
        'id',
        'news_id',
        'diary_title',
        'diary_body',
        'photo1',
        'photo2',
        'photo3',
        'orig_size',
        'packed',
        'post_key'
    ); 

    var $snoopy = null; 

    var $contents = array(); 

    /** 
     * 初期設定 
     *  
     * @param string $email  
     * @param string $password  
     * @param bool $use_cache  
     * @return bool always true 
     */ 
    function PHP_Mixi($email = "", $password = "", $use_cache = 0) /*{{{*/
    { 
        $this->email = $email; 
        $this->password = $password; 
        $this->use_cache = $use_cache; 
        if ($this->use_cache) { 
            if (!defined('PHPMIXI_CACHE')) { 
                define("PHPMIXI_CACHE", 1); 
            }  
        }  
        require_once 'Snoopy.class.php'; 
        $this->snoopy = new Snoopy; 
        $this->snoopy->agent = "phpMixi/0.1.1"; 
        return true; 
    }/*}}}*/

    /** 
     * ログイン 
     *  
     * @param string $url ログイン後に読み込むページ　設定することで、多少の高速化が図れる(?) 
     * @return bool  
     */ 
    function login($url = "home.pl")/*{{{*/
    { 
        if ($this->is_logined()) { 
            return true; 
        }  
        $param = array('email' => $this->email, 
            'password' => $this->password, 
            'next_url' => "/" . $url, 
            'sticky' => "on" 
            ); 
        $this->snoopy->submit($this->base_url . "login.pl", $param); 
        $this->contents[$this->base_url . $url] = $this->snoopy->results; 
        return empty($this->snoopy->cookies['BF_SESSION']) ? false : true; 
    }/*}}}*/

    /** 
     * ログイン状態の確認。 
     *  
     * 手抜きもイイトコなので、何とかしたほうがいいかも… 
     *  
     * @return bool  
     */ 
    function is_logined() // 手抜き /*{{{*/
    {
        return empty($this->snoopy->cookies['BF_SESSION']) ? false : true; 
    }/*}}}*/

    /** 
     * 簡略ログイン 

     * cookie情報を強引に設定してログイン処理を省略 
     *  
     * @param string $session $_COOKIE['BF_SESSION'] 
     * @param string $stamp $_COOKIE['BF_STAMP'] 
     * @return bool always true 
     */ 
    function ez_login($session, $stamp)/*{{{*/
    {
        $this->snoopy->cookies['BF_SESSION'] = $session; 
        $this->snoopy->cookies['BF_STAMP'] = $stamp; 
        return true; 
    }/*}}}*/

    /** 
     * ページ取得 
     *  
     * @param string $url URL 
     * @param bool $cache false指定でキャッシュを無視する 
     * @return string ページデータ 
     */ 
    function fetch($url, $cache = true)/*{{{*/
    {
        $url = (strpos($url, "http://") !== false) ? $url : $this->base_url . $url; 

        if ($this->use_cache && $cache) { 
            $this->cache_set($url, $this->cache_salt); 
        }  

        if (empty($this->contents[$url])) { 
            $this->snoopy->fetch($url); 
            $this->contents[$url] = $this->snoopy->results; 
        }  

        if ($this->use_cache == 1 && $cache) { 
            $this->cache_make($url, $this->cache_salt); 
        }  

        return $this->contents[$url]; 
    }/*}}}*/

    /** 
     * FORM送信 
     *  
     * @param string $action 送信先 
     * @param array $param 送信するデータ 
     * @param string $files 送信するファイルのパス 
     * @return string ページデータ 
     */ 
    function submit($action, $param, $files = "")/*{{{*/
    { 
        $action = (strpos($action, "http://") !== false) ? $action : $this->base_url . $action; 
        if (empty($files)) { 
            $this->snoopy->submit($action, $param); 
        } else { 
            $this->snoopy->submit($action, $param, $files); 
        }  
        return $this->snoopy->results; 
    }/*}}}*/

    /** 
     * メインメニューを取得 
     *  
     * @param string $url メインメニューを取得するページ 
     * @return array  
     */ 
    function parse_mainmenu($url = "home.pl")/*{{{*/
    { 
        $content = $this->fetch($url); 
        $item = array(); 
        if (preg_match('/<map name=mainmenu>(.*?)<\/map>/s', $content, $match)) { 
            preg_match_all('/<area .*?alt=[\"\']?([^\s<>]*?)[\"\']? .*?href=[\"\']?([^\s<>]*?)[\"\']?>/i', $match[1], $result, PREG_SET_ORDER); 
            foreach ($result as $myrow) { 
                $item[] = array('link' => $this->base_url . $myrow[2], 
                    'title' => $myrow[1] 
                    ); 
            }  
        }  
        return $item; 
    }/*}}}*/

    /** 
     * ツールバー(?)を取得 
     *  
     * @param string $url ツールバーを取得するページ 
     * @return array  
     */ 
    function parse_toolbar($url = "home.pl") /*{{{*/
    { 
        $content = $this->fetch($url); 
        $item = array(); 
        preg_match_all('/<td><img src=http:\/\/img\.mixi\.jp\/img\/b_left\.gif width=22 height=23><\/td>(.*?)<td><img src=http:\/\/img\.mixi\.jp\/img\/b_right\.gif width=23 height=23><\/td>/si', $content, $match); 
        preg_match_all('/<a href=([^<> ]*?) .*?><img .*?alt=([^<> ]*?) .*?><\/a>/i', $match[1][0], $result, PREG_SET_ORDER); 
        foreach ($result as $myrow) { 
            $item[] = array("link" => $this->base_url . $myrow[1], 
                "title" => $myrow[2] 
                ); 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * 運営者からのお知らせ を取得 
     *  
     * @return array  
     */ 
    function parse_information() /*{{{*/
    { 
        $content = $this->fetch("home.pl"); 
        $item = array(); 
        preg_match_all('/<!-- start: お知らせ -->(.*?)<\/table>/s', $this->ts_strip_nl($content), $match); 
        preg_match_all('/<tr><td>(.*?)<\/td><td>(.*?)<\/td><td>(.*?)<\/td><\/tr>/i', $match[1][0], $result, PREG_SET_ORDER); 
        foreach ($result as $myrow) { 
            preg_match('/<a href=[\"\']?(.*?)[\"\']?.*?>(.*?)<\/a>/i', $myrow[3], $ret); 
            $item[] = array('link' => $this->base_url . $ret[1], 
                'title' => preg_replace('/^・&nbsp;/', '', strip_tags($myrow[1])), 
                'value' => $ret[2] 
                ); 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * コミュニティ最新書き込み を取得 
     *  
     * 50件以上も取得できるようにしたいね…… 
     *  
     * @param int $max 最大取得件数(50まで) 
     * @return array  
     */ 
    function parse_new_bbs($max = 10) /*{{{*/
    { 
        $content = $this->fetch("new_bbs.pl"); 
        $item = array(); 
        preg_match('/<table border=0 cellspacing=1 cellpadding=4 width=630>(.*?)<\/table>/is', $content, $match); 
        preg_match_all('/<td WIDTH=180><img.*?>(\d{4})年(\d{2})月(\d{2})日\s(\d{1,2}):(\d{2})<\/td>\s<td WIDTH=450>\s<a href=[\"\']?(view_[a-z]+\.pl\?id=\d+)[\"\']?>(.*?) \((\d+)\)<\/a> \((.*?)\)\s<\/td>/is', $match[1], $result, PREG_SET_ORDER); 
        $i = 1; 
        foreach($result as $myrow) { 
            if ($i > $max) { 
                break; 
            }  
            $item[] = array('date' => array('year' => $myrow[1], 'month' => $myrow[2], 'day' => $myrow[3], 'hour' => $myrow[4], 'minute' => $myrow[5]), 
                'link' => $this->base_url . $myrow[6], 
                'title' => $myrow[7], 
                'count' => $myrow[8], 
                'community' => $myrow[9] 
                ); 
            $i++; 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * カレンダーを取得 
     *  
     * @param int $year 年 
     * @param int $month 月 
     * @return array  
     */ 
    function parse_show_calendar($year = 0, $month = 0) /*{{{*/
    { 
        if (empty($year) || empty($month)) { 
            $url = "show_calendar.pl"; 
        } else { 
            $url = "show_calendar.pl?year=" . intval($year) . "&month=" . intval($month); 
        }  
        $content = $this->fetch($url); 
        $term = $this->get_cal_term("", $content); 
        preg_match('/<table width="670" border="0" cellspacing="1" cellpadding="3">(.*?)<\/table>/s', $content, $match); 
        $content = preg_replace('/<tr align=center bgcolor=#FFF1C4>.*?<\/tr>/is', '', $match[1]); 
        preg_match_all('/<td height=65 [^<>]*><font color=#996600>(\S*?)<\/font>(.*?)<\/td>/is', $content, $result, PREG_SET_ORDER); 
        foreach($result as $myrow) { 
            // $date = array("year" => $term['year'], "month" => $term['month'], "day" => intval($myrow[1])); 
            $date = $this->mktime(0, 0, 0, $term['month'], $myrow[1], $term['year']); 
            $schedule = explode("<br>", $myrow[2]); 
            foreach($schedule as $desc) { 
                if (preg_match('/<img SRC=(.*?) width=16 height=16 align=middle><a href=(\S*?)>(.*?)<\/a>/i', $desc, $ret)) { 
                    $icon = pathinfo($ret[1]); 
                    $item[] = array('title' => $ret[3], 'link' => $this->base_url . $ret[2], 'date' => $date , "category" => $this->mixi_cal_icon[$icon['basename']]); 
                } else if (preg_match('/<a href=".*?" onClick="MM_openBrWindow\(\'(view_schedule\.pl\?id=\d+)\'.*?\)"><img src=(\S*?).*?>(.*?)<\/a>/i', $desc, $ret)) { 
                    $icon = pathinfo($ret[2]); 
                    $item[] = array('title' => $ret[3], 'link' => $this->base_url . $ret[1], 'date' => $date , "category" => $this->mixi_cal_icon[$icon['basename']]); 
                }  
            }  
        }  
        return $item; 
    }  /*}}}*/

    function parse_calendar($year = 0, $month = 0){ /*{{{*/
        return parse_show_calendar($year, $month); 
    }  /*}}}*/

    function &get_cal_term($act = "", &$content) /*{{{*/
    { 
        $result = array(); 
        switch ($act) { 
            case 'next': 
                preg_match('/<a href="(calendar\.pl\?.*?)">([^<>]+?)&nbsp;&gt;&gt;/', $content, $match); 
                $result = array("link" => $match[1], "title" => $match[2]); 
                break; 

            case 'prev': 
                preg_match('/<a href="(calendar\.pl\?.*?)">&lt;&lt;&nbsp;([^<>]+)/', $content, $match); 
                $result = array("link" => $match[1], "title" => $match[2]); 
                break; 

            default: 
                preg_match('/<a href="calendar\.pl\?year=(\d+)&month=(\d+).*?">[^&]*?<\/a>/', $content, $match); 
                $result = array("year" => intval($match[1]), "month" => intval($match[2])); 
        }  
        return $result; 
    }  /*}}}*/

    /** 
     * 日記を取得 
     *  
     * @param int $id 日記のID 
     * @param int $owner_id 著者のID 
     * @return array  
     */ 
    function parse_view_diary($id, $owner_id = 0) /*{{{*/
    { 
        if (!empty($owner_id)) { 
            $owner_id = "&owner_id=" . intval($owner_id); 
        }  
        $url = "view_diary.pl?id=" . intval($id) . $owner_id; 
        $content = $this->fetch($url); 
        $item = array(); 
        $pattern = '/<tr valign=top>.*?<td align=center rowspan=2 nowrap width=95 bgcolor=#FFD8B0>(\d{4})年(\d{2})月(\d{2})日<br>(\d{1,2}):(\d{2})<\/td>'; 
        $pattern .= '.*?<td bgcolor=#FFF4E0 width=430>&nbsp;(.*?)<\/td>.*?<td class=h12>(.*?)<\/td>(.*)/is'; 
        preg_match($pattern, $content, $match);  
        // $item['date'] = array("year" => $match[1], 
        // "month" => $match[2], 
        // "day" => $match[3], 
        // "hour" => $match[4], 
        // "minute" => $match[5], 
        // "str" => sprintf("%04d年%02d月%02d日 %02d:%02d", $match[1], $match[2], $match[3], $match[4], $match[5]) 
        // ); 
        $item['date'] = $this->mktime($match[4], $match[5], 0, $match[2], $match[3], $match[1]); 
        $item['subject'] = $match[6]; 
        $item['content'] = $match[7]; 
        preg_match_all('/<td rowspan="2" align="center" width="95" bgcolor="#f2ddb7" nowrap>\n(\d{4})年(\d{2})月(\d{2})日<br>(\d{1,2}):(\d{2})<br>.*?<a href=[\"\']?(.+?)[\"\']?>(.+?)<\/a>.*?<td class=h12>(.+?)<\/td>/is', $match[8], $comment, PREG_SET_ORDER); 
        foreach($comment as $res) { 
            $item['comment'][] = array('date' => array("year" => $res[1], "month" => $res[2], "day" => $res[3], "hour" => $res[4], "minute" => $res[5], "str" => sprintf("%04d年%02d月%02d日 %02d:%02d", $res[1], $res[2], $res[3], $res[4], $res[5])), 
                'poster' => $res[6], 
                'name' => $res[7], 
                'content' => $res[8] 
                ); 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * 日記のリストを取得 
     *  
     * アタマの処理が酷いけど…… 許してヽ( ﾟдﾟ)ﾉｸﾚﾖ 
     *  
     * @param int $id 著者のID (省略で自分の) 
     * @param int $year 年 
     * @param int $month 月 
     * @return array  
     */ 
    function parse_list_diary($id = 0, $year = 0, $month = 0) /*{{{*/
    { 
        $url = empty($id) ? "list_diary.pl" : "list_diary.pl?id=" . intval($id); 
        if (!empty($year) && !empty($month)) { 
            $url = empty($id) ? $url . "?year=" . $year . "&month=" . $month : $url . "&year=" . $year . "&month=" . $month; 
        }  
        $now = getdate(); 
        $year = empty($year) ? $now["year"] : $year; 
        $content = $this->fetch($url); 
        $pattern = '/<tr VALIGN=top>.*?<font COLOR=#996600>(\d{2})月(\d{2})日<br>(\d{1,2}):(\d{2})<\/font>'; 
        $pattern .= '.*?<td bgcolor=#F2DDB7>&nbsp;(.+?)<\/td>.*?<td CLASS=h120>\n(.*?)\n(.+?)\n<br>\n\n<\/td>'; 
        $pattern .= '.*?<a href="?(.+?)"?>コメント\((\d+)\)<\/a>/is'; 

        preg_match_all($pattern, $content, $result, PREG_SET_ORDER); 
        $item = array(); 
        foreach($result as $myrow) { 
            $date = $this->mktime($myrow[3], $myrow[4], 0, 
                $myrow[1], $myrow[2], $year);
//echo date("Y-m-d", $date)." <br />";
/*
            var_dump(date("Y-m-d", $date));
*/
            list($myrow[8], $sharp) = split("#", $myrow[8]);
            $item[] = array('date' => $this->mktime($myrow[3], $myrow[4], 0, $myrow[1], $myrow[2], $year), 
                'subject' => $myrow[5], 
                'description' => $this->ts_strip_nl(strip_tags($myrow[7])), 
                'link' => $this->base_url . $myrow[8], 
                'count' => intval($myrow[9]) 
                ); 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * マイミクシィ最新日記を取得 
     *  
     * @param int $max 最大取得件数 
     * @return array  
     */ 
    function parse_new_friend_diary($max = 10) /*{{{*/
    { 
        $content = $this->fetch("new_friend_diary.pl"); 
        $item = array(); 
        preg_match('/<table border=0 cellspacing=1 cellpadding=4 width=630>(.*?)<\/table>/is', $content, $match); 
        preg_match_all('/<td width=180><img.*?>(\d{4})年(\d{2})月(\d{2})日\s(\d{1,2}):(\d{2})<\/td>\s<td width=450>\s?<a href=[\"\']?(view_diary\.pl\?[^>\s]+)[\"\']?>(.*?)<\/a>\s\((.*?)\)\s<\/td>/is', $match[1], $result, PREG_SET_ORDER); 
        $i = 1; 
        foreach($result as $myrow) { 
            if ($i > $max) { 
                break; 
            }  
            $item[] = array( 
                // 'date' => array('year' => $myrow[1], 'month' => $myrow[2], 'day' => $myrow[3], 'hour' => $myrow[4], 'minute' => $myrow[5]), 
                'date' => $this->mktime($myrow[4], $myrow[5], 0, $myrow[2], $myrow[3], $myrow[1]), 
                'link' => $this->base_url . $myrow[6], 
                'title' => $myrow[7], 
                'name' => $myrow[8] 
                ); 
            $i++; 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * お気に入りを取得 
     *  
     * @param int $max 最大取得件数 
     * @return array  
     */ 
    function parse_list_bookmark($max = 10) /*{{{*/
    { 
        $content = $this->fetch("list_bookmark.pl"); 
        preg_match('/<table border=0 cellspacing=1 cellpadding=4 width=630>(.*?)<!--フッタ-->/is', $content, $match); 
        $pattern = '/<td width=90 .*?><a href="([^"]*show_friend\.pl\?id=\d+)"><img src="([^"]*)".*?>'; 
        $pattern .= '.*?<td colspan=2 bgcolor=#FFFFFF>(.*?) \((.*?)\)<\/td>'; 
        $pattern .= '.*?<td colspan=2 bgcolor=#FFFFFF>(.*?)<\/td>'; 
        $pattern .= '.*?<td bgcolor=#FFFFFF width=140>(.*?)<\/td>/is'; 
        preg_match_all($pattern, $match[1], $result, PREG_SET_ORDER); 
        $item = array(); 
        $i = 1; 
        foreach($result as $myrow) { 
            if ($i > $max) { 
                break; 
            }  
            $item[] = array('link' => $this->base_url . $myrow[1], 
                'image' => $myrow[2], 
                'name' => $myrow[3], 
                'sex' => $myrow[4], 
                'comment' => str_replace("<br>", "", $myrow[5]), 
                'lastlogin' => $myrow[6] 
                ); 
            $i++; 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * コミュニティ一覧を取得 
     *  
     * 全頁を自動で拾ったほうが良いかな？ 
     *  
     * @param int $id 誰の？ 
     * @param int $page 何ページ目？ 
     * @return array  
     */ 
    function parse_list_community($id = 0, $page = 0) /*{{{*/
    { 
        $id = empty($id) ? $this->my_info('id') : $id; 
        $page = empty($page) ? "" : "&page=" . $page; 
        $content = $this->fetch("list_community.pl?id=" . $id . $page); 

        preg_match('/<table border=0 cellspacing=1 cellpadding=2 width=560>(.+?)<\/table>/is', $content, $match); 
        preg_match_all('/<tr align=center bgcolor=#FFFFFF>(.*?)<tr align=center bgcolor=#FFF4E0>(.*?)<\/tr>/is', $match[1], $set); 

        $logos = ""; 
        foreach($set[1] as $val) { 
            $logos .= $val; 
        }  
        $names = ""; 
        foreach($set[2] as $val) { 
            $names .= $val; 
        }  
        preg_match_all('/<td width=20% height=100 background=http:\/\/img\.mixi\.jp\/img\/[0-9a-z-_]+\.gif>(<a.*?)<\/td>/i', $logos, $logo_set, PREG_SET_ORDER); 
        preg_match_all('/<td>(.*?)<\/td>/i', $names, $name_set, PREG_SET_ORDER); 

        $item = array(); 
        for($i = 0;$i < count($logo_set);$i++) { 
            preg_match('/<a href=(.*?)><img SRC=(.*?) border=0><\/a>/', $logo_set[$i][1], $logo_matchs); 
            preg_match('/(.*?)\((\d+)\)\s*$/', $name_set[$i][1], $name_matchs); 
            $item[] = array('name' => $name_matchs[1], 
                'count' => intval($name_matchs[2]), 
                'link' => $this->base_url . $logo_matchs[1], 
                'logo' => $logo_matchs[2] 
                ); 
        }  
        return $item; 
    }  /*}}}*/

    function get_all_friend($id)
    {
        return $this->parse_list_friend($id); 
    }

    /** 
     * マイミクシィ一覧を取得 
     *  
     * 全頁を自動で拾ったほうが良いかな？ 
     *  
     * @param int $id 誰の？ 
     * @param int $page 何ページ目？ 
     * @return array  
     */ 
    function parse_list_friend($id = 0, $page = 0) /*{{{*/
    { 
        $id = empty($id) ? "" : "id=" . $id; 
        $page = empty($page) ? "" : "page=" . $page; 
        $sep = ""; 
        $sep2 = ""; 
        if (!empty($id) || !empty($page)) { 
            $sep = "?"; 
        }  
        if (!empty($id) && !empty($page)) { 
            $sep2 = "&"; 
        }  
        $content = $this->fetch("list_friend.pl" . $sep . $id . $sep2 . $page); 
        //preg_match_all('/<tr align=center bgcolor=#FFFFFF>(.*?)<tr align=center bgcolor=#FFF4E0>(.*?)<\/tr>/is', $match[1], $set); 
        preg_match_all('/<tr ALIGN=center BGCOLOR=#FFFFFF>(.*?)<tr ALIGN=center BGCOLOR=#FFF4E0>(.*?)<\/tr>/is', $content, $set); 

        $logos = ""; 
        foreach($set[1] as $val) { 
            $logos .= $val; 
        }  

        $names = ""; 
        foreach($set[2] as $val) { 
            $names .= $val; 
        }

//<a href=show_friend.pl?id=5736810><img SRC=http://img-p2.mixi.jp/photo/member/68/10/5736810_3127471233s.jpg border=0></a></td>

        
        preg_match_all('/<td WIDTH=20% HEIGHT=100 background=http:\/\/img.+?\.mixi\.jp\/.+?\.gif>(<a.+?)<\/td>/is', $logos, $logo_set, PREG_SET_ORDER); 
        preg_match_all('/<td valign=top>(.*?)<\/td>/', $names, $name_set, PREG_SET_ORDER); 
/*
var_dump($names);
var_dump($name_set);
*/
        $item = array(); 
        for($i = 0;$i < count($logo_set);$i++) { 
            preg_match('/<a href=(.*?)><img SRC=(.*?) border=0><\/a>/', $logo_set[$i][1], $logo_matchs); 

            preg_match('/(.*?)\((\d+)\)\s*$/', $name_set[$i][1], $name_matchs); 

            $name = preg_replace("/さん$/", '', $name_matchs[1]);
            list($url, $id) = split("=", $logo_matchs[1]);
//            var_dump($id);
//            var_dump($logo_matchs[1]);
            $item[] = array(
                'name' => $name, 
                'count' => intval($name_matchs[2]), 
                'id' => $id, 
                'link' => $this->base_url . $logo_matchs[1], 
                'logo' => $logo_matchs[2] 
            ); 
        }  
        return $item; 
    }  /*}}}*/

    /** 
     * プロフィールを取得 
     *  
     * @param int $id ID 
     * @return array  
     */ 
    function parse_show_friend($id = 0) /*{{{*/
    { 
        $url = empty($id) ? "show_profile.pl" : "show_friend.pl?id=".$id; 
        $content = $this->fetch($url); 
        $item = array(); 
        // name
        preg_match('/<!--プロフィール-->(.*?)<!--プロフィールここまで-->/s', $content, $match); 

        if (empty($match)) { 
            preg_match('/<!-- start: プロフィール -->(.*?)<!-- end: プロフィール -->/is', $content, $match); 
        }
        preg_match_all('/<td bgcolor=#F2DDB7.*?>(.*?)<\/td>.*?<td.*?>(.*?)<\/td>/is', $match[1], $result, PREG_SET_ORDER);


        $item = array();
        if (!empty($id)) {
            //preg_match('/<img alt="\*" src="http:\/\/img\.mixi\.jp\/img\/dot0\.gif" width="1" height="5"><br>(.*?)\((\d*)\)<br>.*?<span class="f08x">\((.*?)\)<\/span><br>/is', $content, $info);
            preg_match_all('/<\/font><\/td>\n<td( width="345")?>(.+)<\/td><\/tr>/', $content, $info);
            preg_match('/<td bgcolor="#F2DDB7" width="80"><font color="#996600">.+<\/font><\/td>\n<td class="h120">(.+)<\/td><\/tr>/', $content, $match_prof);
            preg_match('/<td align="center" background="http:\/\/img.mixi.jp\/img\/bg_line.gif"><img src="(.+)" alt=".+" vspace="2" \/>/', $content, $match_img);
/*
var_dump($match_prof);
var_dump($info);
var_dump($match_img);
*/
            $name = $this->ts_strip_nl($info[2][0]);
            $item['name'] = $name;
            $item['image'] = $match_img[1];

            if (isset($info[2][4])) {
              $item['interests'] = $info[2][4];
            }
            $item['description'] = $match_prof[1];
            $item['count'] = intval($info[2]);
            $item['lastlogin'] = $info[3];
        }
        foreach($result as $ret) {
            $ret[1] = $this->ts_strip_nl(str_replace("&nbsp;", "", strip_tags($ret[1])));
            if (in_array($ret[1], array("最新の日記", "最新のアルバム", "最新のおすすめレビュー"))) {
                continue;
            }
            $item[$ret[1]] = $ret[2];
        }

        return $item;
    }  /*}}}*/

    function parse_list_message() /*{{{*/
    { 
        return array(); 
    }  /*}}}*/

    /** 
     * ページ上部のバナー取得 
     *  
     * WWW:Mixiがこれを実装してるとのことなので、一応。 
     *  
     * @param string $url  
     * @return array  
     */ 
    function parse_banner($url = "home.pl") /*{{{*/
    { 
        $content = $this->fetch($url); 
        $item = array(); 
        preg_match('/<a href=(".*?"|\'.*?\'|[^<> ]*)\s[^<>]*?><img src=["\']?([^<>]*?)[\'"]? border=0 width=468 height=60 alt=["\']?([^<>]*?)[\'"]?><\/a>/is', $content, $match); 
        $link = preg_replace('/(^"|^\'|"$|\'$)/', '', $match[1]); 
        $item['link'] = (strpos($link, "http") !== false) ? $link : $this->base_url . $link; 
        $item['image'] = $match[2]; 
        $item['subject'] = $match[3]; 
        return $item; 
    }  /*}}}*/

    /** 
     * 日記を書く 
     *  
     * 返り値は bool であるべきでしょか？(日記全般で) 
     *  
     * @param string $diary_title 題名 
     * @param string $diary_body 本文 
     * @param string $photo1 写真１のパス 
     * @param string $photo2 写真２のパス 
     * @param string $photo3 写真３のパス 
     * @param int $orig_size 圧縮設定 
     * @param string $news_id 
     * @return string  
     */ 
    function add_diary($diary_title, $diary_body, $photo1 = "", $photo2 = "", $photo3 = "", $orig_size = 1, $news_id = "") /*{{{*/
    { 
        $param = $this->_preview_diary($diary_title, $diary_body, $photo1, $photo2, $photo3, $orig_size, $news_id); 
        $param['submit'] = "confirm"; 
        return $this->submit("add_diary.pl", $param); 
    }  /*}}}*/

    /** 
     * 日記を編集する 
     *  
     * @param int $id 編集する日記のID 
     * @param string $diary_title 題名 
     * @param string $diary_body 本文 
     * @param string $photo1 写真１のパス 
     * @param string $photo2 写真２のパス 
     * @param string $photo3 写真３のパス 
     * @param int $orig_size 圧縮設定 
     * @return string  
     */ 
    function edit_diary($id, $diary_title, $diary_body, $photo1 = "", $photo2 = "", $photo3 = "", $orig_size = 1, $news_id = "") /*{{{*/
    { 
        $url = "edit_diary.pl?id=" . $id; 
        $content = $this->fetch($url); 
        preg_match('/post_key value="(.+?)"/', $content, $match); 
        $post['post_key'] = $match[1]; 
        $post['diary_title'] = $diary_title; 
        $post['diary_body'] = $diary_body; 
        $post['orig_size'] = $orig_size; 
        $post['news_id'] = $news_id; 
        $post['submit'] = "main"; 
        $post['form_date'] = "date"; 
        $file['photo1'] = $photo1; 
        $file['photo2'] = $photo2; 
        $file['photo3'] = $photo3; 
        $action = "edit_diary.pl?id=" . $id; 
        $this->snoopy->set_submit_multipart(); 
        $return = $this->submit($action, $post, $file); 
        return $return; 
    }  /*}}}*/

    /** 
     * 日記の削除 
     *  
     * @param int $id  
     * @return string  
     */ 
    function delete_diary($id) /*{{{*/
    { 
        $url = "delete_diary.pl?id=" . $id; 
        $content = $this->fetch($url); 
        preg_match('/post_key value="(.+?)"/', $content, $match); 
        $post['post_key'] = $match[1]; 
        $post['submit'] = "confirm"; 
        $action = "delete_diary.pl?id=" . $id; 
        $this->snoopy->set_submit_normal(); 
        return $this->submit($action, $post); 
    }  /*}}}*/

    /** 
     * 日記にコメントを付ける 
     *  
     * @param int $id コメントを付ける日記のID 
     * @param string $comment コメント本文 
     * @return string  
     */ 
    function add_comment_diary($id, $comment) /*{{{*/
    { 
        $post['comment_body'] = $comment; 
        $action = "add_comment.pl?diary_id=" . $id; 
        $this->snoopy->set_submit_normal(); 
        $return = $this->submit($action, $post); 
        preg_match('/<form action="([^\s"]+)" method=post>(.*)書き込み/is', $return, $match); 
        preg_match_all('/<input type=["\']?([^<>\s]*?)["\']? name=["\']?([^<>\s]*?)["\']? value=["\']?(.*?)["\']?>/is', $match[2], $post_part, PREG_SET_ORDER); 
        $post = array(); 
        foreach($post_part as $part) { 
            $post[$part[2]] = $part[3]; 
        }  
        return $this->submit($match[1], $post); 
    }  /*}}}*/

    function _preview_diary($diary_title, $diary_body, $photo1 = "", $photo2 = "", $photo3 = "", $orig_size = 1, $news_id = "") /*{{{*/
    { 
        $url = "list_diary.pl"; 
         
        $post = array(); 
        $post['id'] = $this->my_info('id'); 
        $post['news_id'] = $news_id; 
        $post['diary_title'] = $diary_title; 
        $post['diary_body'] = $diary_body; 
        $post['orig_size'] = $orig_size; 
        $post['submit'] = "main"; 
        $file['photo1'] = $photo1; 
        $file['photo2'] = $photo2; 
        $file['photo3'] = $photo3; 
        $this->snoopy->set_submit_multipart(); 
        $return = $this->submit("add_diary.pl", $post, $file); 
         
        preg_match_all('/<input type=["\']?([^<>\s]*?)["\']? name=["\']?([^<>\s]*?)["\']? value=["\']?(.*?)["\']?>/is', $return, $match, PREG_SET_ORDER); 
        $part = array(); 
        foreach($match as $part) { 
            if (!in_array($part[2], $this->mixi_diary_formval)) { 
                continue; 
            }  
            $vars[$part[2]] = $part[3]; 
        }  
        return $vars; 
    }  /*}}}*/

    function cache_set($url, $h = "") /*{{{*/
    { 
        $path = $this->cache_url2filename($url); 
        $filename = $this->cache_dir . $this->cache_prefix . md5($h . $this->email . $this->password . $path) . $this->cache_ext; 
        if (file_exists($filename) && ((time() - filemtime($filename)) < $this->cache_time) && (empty($this->contents[$this->base_url . $path]))) { 
            include($filename); 
            if (!empty($cache)) { 
                $this->contents[$this->base_url . $path] = $cache; 
            }  
        }  
    }  /*}}}*/

    function cache_make($url, $h = "") /*{{{*/
    { 
        $path = $this->cache_url2filename($url); 
        $filename = $this->cache_dir . $this->cache_prefix . md5($h . $this->email . $this->password . $path) . $this->cache_ext; 
        if ((time() - @filemtime($filename)) > $this->cache_time) { 
            $dat = '<?php if(!defined("PHPMIXI_CACHE")) exit; ?>'; 
            $dat .= '<?php' . "\n"; 
            $dat .= '$cache = <<<DAT' . "\n"; 
            $dat .= $this->contents[$this->base_url . $path]; 
            $dat .= "\n" . 'DAT;'; 
            $dat .= "\n" . '?>'; 

            $fp = fopen($filename, "w"); 
            flock($fp, 2); 
            fputs($fp, $dat); 
            fclose($fp); 
        }  
    }  /*}}}*/

    function cache_clear($url, $h = "") /*{{{*/
    { 
        $path = $this->cache_url2filename($url); 
        $filename = $this->cache_dir . $this->cache_prefix . md5($h . $this->email . $this->password . $path) . $this->cache_ext; 
        if (file_exists($filename)) { 
            unlink($filename); 
        }  
    }  /*}}}*/

    function cache_clear_all() /*{{{*/
    { 
        if ($dh = opendir($this->cache_dir)) { 
            while (($file = readdir($dh)) !== false) { 
                if (preg_match('/^' . preg_quote($this->cache_prefix) . '/', $file)) { 
                    unlink($this->cache_dir . $file); 
                }  
            }  
            closedir($dh); 
        }  
    }  /*}}}*/

    function cache_url2filename($url) /*{{{*/
    { 
        $parse = parse_url($url); 
        $filename = str_replace('/', '', strrchr($parse['path'], "/")); 
        if (isset($parse['query'])) { 
            $filename .= "?" . $parse['query']; 
        }  
        return $filename; 
    }  /*}}}*/

    function my_info($key) /*{{{*/
    { 
        if (!$this->is_logined()) { 
            return false; 
        }  

        switch ($key) { 
            case 'id': 
                preg_match('/^([0-9]+)_.+?/', $this->snoopy->cookies['BF_SESSION'], $match); 
                return intval($match[1]); 
                break; 
        }  
    }  /*}}}*/

    function mktime($hour = 0, $minute = 0, $second = 0, $month = 0, $day = 0, $year = 0, $is_dst = 0) /*{{{*/
    { 
        return mktime($hour, $minute, $second, $month, $day, $year, $is_dst); 
    }  /*}}}*/

    function ts_strip_nl($text) /*{{{*/
    { 
        $text = str_replace("\r\n", "\n", $text); 
        $text = str_replace("\r", "\n", $text); 
        $text = str_replace("\n", "", $text); 
        return $text; 
    }  /*}}}*/

    /**
     * 新着日記一覧 を取得
     *
     * @return array
     */
    function search_diary()/*{{{*/
    {
        $url = "search_diary.pl";
        $content = $this->fetch($url);
        $item = array();
        $pattern = '/photo\/member\/(.+?)\".+?FFFFFF>(.+?) \((.+?)\).+?FFFFFF>(.+?)<\/td>.+?FFFFFF>(.+?)<\/td>.+?(\d{2})月(\d{2})日 (\d{2}):(\d{2}).+?view_diary\.pl\?id=(\d+)&owner_id=(\d+)/is';
        preg_match_all($pattern, $content, $match);

        for ( $i = 0; $i < count ( $match[1] ) - 1; $i++ )
        {
            $item[$i]['image']   = '<img src="http://img-p2.mixi.jp/photo/member/' . $match[1][$i] . '">';
            $item[$i]['name']    = $match[2][$i];
            $item[$i]['sex']     = $match[3][$i];
            $item[$i]['title']   = $match[4][$i];
            $item[$i]['content'] = $match[5][$i];
            $item[$i]['month']   = $match[6][$i];
            $item[$i]['date']    = $match[7][$i];
            $item[$i]['hour']    = $match[8][$i];
            $item[$i]['minute']  = $match[9][$i];
            $item[$i]['id']      = $match[10][$i];
            $item[$i]['owner_id']= $match[11][$i];
            $item[$i]['url'] = $this->base_url . 'view_diary.pl?id=' . $item[$i]['id'] . '&owner_id=' . $item[$i]['owner_id'];
        }

        return $item;
    }/*}}}*/

    /**
     * コミュニティー一覧 を取得
     * @param string $sort ソート
     * @param string $type キーワード検索
     * @param string $submit main
     * @param string $keyword キーワード
     * @param int $category_id カテゴリーID
     * @param int $page ページ
     *
     * @return array
    */
    function search_community($sort=member, $type=com, $submit=main, $keyword, $category_id=0, $page=1)/*{{{*/
    {
        $aryData["sort"]    = $sort;
        $aryData["type"]    = $type;
        $aryData["submit"]  = $submit;
        $aryData["keyword"] = $keyword;
        $aryData["category_id"] = $category_id;
        $aryData["page"]    = $page;
        $aryKeys = array_keys ( $aryData );
        foreach ( $aryKeys as $strKey )
        {
            $item[] = $strKey . '=' . $aryData[$strKey];
        }
        $url = "search_community.pl?" . join ( '&', $item );
        $content = $this->fetch($url);
        $item = array();
        if ( preg_match( "/\[ .*?$page <a/i", $content ) )
        {
            $item[0]["nextpage"] = TRUE;
        }

        $pattern = '/photo\/comm\/(.+?)\".+?FFFFFF>(.+?).+?FFFFFF>(.+?)<\/td>.+?FFFFFF>(.+?)<\/td>.+?FFFFFF>(.+?)<\/td>.+?view_community\.pl\?id=(\d+)/is';
        preg_match_all($pattern, $content, $match);
        for ( $i = 0; $i < count ( $match[1] ) - 1; $i++ )
        {
            $item[$i]['image']    = 'http://img-c3.mixi.jp/photo/comm/' . $match[1][$i];
            echo $item[$i]['name']     = $match[2][$i];
            $item[$i]['member']   = $match[3][$i];
            $item[$i]['discript'] = $match[4][$i];
            $item[$i]['category'] = $match[5][$i];
            $item[$i]['id']       = $match[6][$i];
            $item[$i]['url'] = $this->base_url . 'view_community.pl?id=' . $item[$i]['id'];
        }

        return $item;
    }/*}}}*/
}
?>
