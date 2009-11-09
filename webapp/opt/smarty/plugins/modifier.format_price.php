<?php
function smarty_modifier_format_price($string)
{
    return '$'.number_format($string);
}
?>
