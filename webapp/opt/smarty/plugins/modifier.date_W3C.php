<?php
function smarty_modifier_date_W3C($time)
{
    $d = date('Y-m-d\TH:i:s', $time);
    $tz = date('O', $time);
    return $d . $tz{0} . $tz{1} . $tz{2} . ':' . $tz{3} . $tz{4};
}
