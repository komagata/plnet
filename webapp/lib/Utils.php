<?php
class Utils
{
    function env()
    {
        $env = parse_ini_file(dirname(dirname(__FILE__)).'/configs/env.ini');
        return isset($env['environment']) ? $env['environment'] : 'production';
    }

    function conf($env = null)
    {
        $env = is_null($env) ? Utils::env() : $env;
        $conf = parse_ini_file(dirname(dirname(__FILE__)).'/configs/config.ini', true);
        $c = $conf[$env];
        return $c;
    }

    function to_json($array)
    {
        include_once 'HTML/AJAX/JSON.php';
        $haj =& new HTML_AJAX_JSON();
        return $haj->encode($array);
    }

    function remove_private_property(&$instance)
    {
        foreach ($instance as $name => $property) {
            if (preg_match('/^_/', $name)) unset($instance->$name);
        }
    }

    function array_remove_private_property(&$instances)
    {
        foreach ($instances as $key => $instance)
            Utils::remove_private_property($instances[$key]);
    }

    function pager($page, $total, $path, $per_page = 10, $next_str = '&gt;', $prev_str = '&lt;') {
        $result = array();
        $offset = $page * $per_page;
        if (($page+1) * $per_page <= $total) {
            $result['has_next'] = true;
            $result['next'] = "<a href=\"{$path}?page=".($page+1)."\">$next_str</a>";
        }
        if (($page-1) * $per_page > 0) {
            $result['has_prev'] = true;
            $result['prev'] = "<a href=\"{$path}?page=".($page-1)."\">$prev_str</a>";
        }
        return $result;
    }
}

function msg($name)
{
    $c =& Controller::getInstance();
    $messages = $c->request->getAttribute('messages');
    $messages[$name] = str_replace('\n', "\n", $messages[$name]);
    return $messages[$name];
}

if(!function_exists('memory_get_usage')) {
    function memory_get_usage()
    {
        if ( substr(PHP_OS,0,3) == 'WIN') {
            $output = array();
            exec( 'tasklist /FI "PID eq ' . getmypid() . '" /FO LIST', $output );
            return preg_replace( '/[\D]/', '', $output[5] ) * 1024;
        } else {
            $pid = getmypid();
            exec("ps -eo%mem,rss,pid | grep $pid", $output);
            $output = explode("  ", $output[0]);
            return $output[1] * 1024;
        }
    }
}
?>
