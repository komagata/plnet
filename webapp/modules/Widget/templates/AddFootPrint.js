{literal}(function (styles) {
  var link = '';
  for (var i=0; i<styles.length; i++) {
    link += "<link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"" + styles[i]  + "\"></link>\n";
  }
  document.write(link);
})([
  {/literal}'{$SCRIPT_PATH}styles/widget/entries.css'{literal}
]);

(function(entries, site) {
  var html = "<div id=\"plnet_widget_entries\">\n";
  html += '<div class="header"><a href="' + site['link'] + '">';
  html += site['title'] + '</a>';
  html += ' <a href="' + site['uri'] + '">';
  html += '<img src="' + site['rss_icon'] + '" class="favicon" />';
  html += '</a></div>' + "\n";
  html += "<ul>\n";
  for (var i = 0; i < entries.length; i++) {
    var entry = entries[i];
    html += "<li>";
    html += '<a href="' + entry.link + '">';
    html += '<img src="' + entry.favicon + '" class="favicon" width="16" height="16" />';
    html += entry.title;
    html += '</a>';
    html += "</li>\n";
  }
  html += "</ul>\n";
  html += '<div class="footer"><a href="http://plnet.jp/">powered by plnet</a></div>' + "\n";
  html += '</div>';
  document.write(html);
})({/literal}{to_json from=$entries}, {to_json from=$site}{literal});{/literal}
