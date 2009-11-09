<?php
require_once dirname(dirname(__FILE__)).'/webapp/config.php';
require_once 'MemberUtils.php';

session_cache_limiter('public');

$member_id = $_REQUEST['member_id'];
$member = MemberUtils::get_by_id($member_id);
echo $member['photo'];
?>
