<?php
function smarty_modifier_tofavicon($string)
{
    return SCRIPT_PATH.'icon.php?url='.$string;
}
?>
