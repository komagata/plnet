{literal}(function (styles) {
  var link = '';
  for (var i=0; i<styles.length; i++) {
    link += "<link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"" + styles[i]  + "\"></link>\n";
  }
  document.write(link);
})([
  {/literal}'{$SCRIPT_PATH}styles/widget/footprints.css'{literal}
]);

(function(footprints, site) {
  var html = "<div id=\"plnet_widget_footprints\">\n";
  html += '<div class="header"><a href="http://plnet.jp/setting/footprint">';
  html += '{/literal}{'footprint'|msg}{literal}</a>';
  html += '</div>' + "\n";
  html += "<ul>\n";
  for (var i = 0; i < footprints.length; i++) {
    var footprint = footprints[i];
    html += "<li>";
    html += '<a href="' + footprint.link + '">';
    html += '<img src="' + footprint.profile_icon + '" width="35" height="35" />';
    html += '</a>';
    html += '<a href="' + footprint.link + '">';
    html += footprint.name;
    html += '</a>';
    html += "</li>\n";
  }
  html += "</ul>\n";
  html += '<div class="footer"><a href="http://plnet.jp/">';
  html += {/literal}'{'lets add the footprint function to blog'|msg}<br /> powered by plnet.jp';{literal}
  html += '</a></div>' + "\n";
  html += '</div>';
  document.write(html);
})({/literal}{to_json from=$footprints}, {to_json from=$site}{literal});{/literal}
