<?php
function smarty_modifier_date_RFC822($time)
{
    return date(DATE_RFC822, $time);
}
?>
