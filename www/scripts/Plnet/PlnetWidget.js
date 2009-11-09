(function (modules) {
  var script = '';
  for (var i = 0; i < modules.length; i++) {
    script += "<script type=\"text/javascript\" src=\"" + modules[i]  + "\"></script>\n";
  }
  document.write(script);
})([
  'http://plnet.jp/scripts/MochiKit/Base.js',
  'http://plnet.jp/scripts/MochiKit/Iter.js',
  'http://plnet.jp/scripts/MochiKit/DOM.js',
  'http://plnet.jp/scripts/MochiKit/Style.js',
  'http://plnet.jp/scripts/MochiKit/Signal.js',
  'http://plnet.jp/scripts/MochiKit/Async.js',
  'http://plnet.jp/scripts/MochiKit/Logging.js',
  'http://plnet.jp/scripts/AsyncJSONP.js'
]);

(function (styles) {
  var link = '';
  for (var i=0; i<styles.length; i++) {
    link += "<link type=\"text/css\" rel=\"stylesheet\" media=\"all\" href=\"" + styles[i]  + "\"></link>\n";
  }
  document.write(link);
})([
  'http://plnet.jp/styles/widget.css'
]);

function PlnetWidget() {
  this.initialize.apply(this, arguments);
}

PlnetWidget.prototype = {
  'initialize': function() {},
  'draw': function(id, uid, count/* = 5 */) {
    var count = count || 5;
    var d = sendJSONPRequest(
      'http://plnet.jp/' + uid + '/json/entries?count=' + count, 'callback'
    );
    d.addCallback(function(entries) {

      // header
      var plnet_header = DIV(
        {'class': 'plnet_header'},
        A({'href': 'http://plnet.jp/' + uid + '/'}, uid + '\'s plnet')
      );

      // body
      var plnet_body = DIV({'class': 'plnet_body'},
        UL(null,
          map(
            function(entry) {
              return LI(
                null,
                A({'href': entry.uri}, entry.title)
              );
            },
            entries
          )
        )
      );

      // footer
      var plnet_footer = DIV(
        {'class': 'plnet_footer'},
        A({'href': 'http://plnet.jp/'}, 'powered by plnet')
      );
      var plnet_frame = DIV(
        {'class': 'plnet_frame'},
        plnet_header,
        plnet_body,
        plnet_footer
      );

      replaceChildNodes(id, plnet_frame);
    });
  }
};
