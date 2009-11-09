<?php
/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {debug} function plugin
 *
 * Type:     function<br>
 * Name:     debug<br>
 * Date:     July 1, 2002<br>
 * Purpose:  popup debug window
 * @link http://smarty.php.net/manual/en/language.function.debug.php {debug}
 *       (Smarty online manual)
 * @author   Monte Ohrt <monte@ispi.net>
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string output from {@link Smarty::_generate_debug_output()}
 */
function convert_encoding_to_eucjp($template_source, &$smart) {

  //autoが正しく判別しない場合
  return mb_convert_encoding($template_source, "EUC-JP", "SJIS");

/*
  if (function_exists("mb_convert_encoding")) {
    // mbstringモジュールが利用可能でテンプレートがEUC-JPでない場合は
    // 文字コードを変換する
    $enc = mb_detect_encoding($template_source, "auto");
    if ($enc != "EUC-JP") {
      return mb_convert_encoding($template_source, "EUC-JP", $enc);
    }
  }
  return $template_source;
*/

}

/* vim: set expandtab: */

?>
