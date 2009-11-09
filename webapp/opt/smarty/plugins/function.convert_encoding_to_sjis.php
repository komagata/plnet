<?
function convert_encoding_to_sjis($template_source, &$smart) {
  if (function_exists("mb_convert_encoding")) {
    return mb_convert_encoding($template_source, "SJIS", "EUC-JP");
  }
  return $template_source;
}
?>