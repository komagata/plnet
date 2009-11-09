<?php
function smarty_function_html_select_date_simple($params, &$smarty)
{
    if (isset($params['time'])) {
        list($year, $month, $day) =
            explode('-', $params['time']);
    } else {
        $year = 0; $month = 0; $day = 0;
    }


    $prefix = isset($params['prefix']) ? $params['prefix'].'_' : '';
    $selected = ' selected="selected"';

    // year
    $year_start = date("Y")-100;
    $year_end = date("Y");

    $year_output = "<select name=\"{$prefix}year\">\n";
    $year_output .= "<option label=\"----\" value=\"0\">----</option>\n";
    for ($i=$year_start;$i<=$year_end;$i++) {
        $s = $i == $year ? $selected : '';
        $year_output .= "<option label=\"{$i}\" value=\"{$i}\"{$s}>{$i}</option>\n";
    }
    $year_output .= "</select>";

    // month
    $month_start = 1;
    $month_end = 12;

    $month_output = "<select name=\"{$prefix}month\">\n";
    $month_output .= "<option label=\"--\" value=\"0\">--</option>\n";
    for ($i=$month_start;$i<=$month_end;$i++) {
        $s = $i == $month ? $selected : '';
        $month_output .= "<option label=\"{$i}\" value=\"{$i}\"{$s}>{$i}</option>\n";
    }
    $month_output .= "</select>";

    // day
    $day_start = 1;
    $day_end = 31;

    $day_output = "<select name=\"{$prefix}day\">\n";
    $day_output .= "<option label=\"--\" value=\"0\">--</option>\n";
    for ($i=$day_start;$i<=$day_end;$i++) {
        $s = $i == $day ? $selected : '';
        $day_output .= "<option label=\"{$i}\" value=\"{$i}\"{$s}>{$i}</option>\n";
    }
    $day_output .= "</select>";

    if ($params['locale'] == 'ja') {
        $output = $year_output."年\n".$month_output."月\n".$day_output."日\n";
    } else {
        $output = $year_output."/\n".$month_output."/\n".$day_output."\n";
    }
    return $output;
}
?>
