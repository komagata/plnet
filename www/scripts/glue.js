var Utils = {
  setEventToClass: function(element, method, func) {
    document.getElementsByClassName(element).each(function(c){
      c[method] = func;
    });
  },
  setToClass: function(element, func) {
    document.getElementsByClassName(element).each(function(c){
      func(c);
    });
  },
  message : function(message, stats, target, withp) {
    message = message || '';
    stats = stats || 'none';
    target = target || 'message';
    withp = withp || false
    message_icon = '<img src="images/message.gif" class="icon" />';
    loading_icon = '<img src="images/loading.gif" class="icon" />';
    add_icon = '<img src="images/add.gif" class="icon" />';
    update_icon = '<img src="images/update.png" class="icon" />';
    remove_icon = '<img src="images/remove.png" class="icon" />';
    error_icon = '<img src="images/error.gif" class="icon" />';
    ok_icon = '<img src="images/ok.png" class="icon" />';
    if (stats == 'message') {
      message = message_icon + message;
    }
    if (stats == 'add') {
      message = add_icon + message;
    }
    if (stats == 'update') {
      message = update_icon + message;
    }
    if (stats == 'remove') {
      message = remove_icon + message;
    }
    if (stats == 'error') {
      message = error_icon + message;
    }
    if (stats == 'loading') {
      message = loading_icon + message;
    }
    if (stats == 'ok') {
      message = ok_icon + message;
    }
    if (stats == 'none') {
      message = '';
    }
    if (withp) message = '<p style="margin:6px">' + message + '</p>'
    Element.update(target, message);
  }
};

var Gluee = {
  getFeed : function(url, callback) {
    var url = 'proxy.php?uri=' + url;
    new Ajax.Request(url, {
      onComplete : function(req) {
        var feed = new Feed(req.responseText);
        feed.parse();
        var json = feed.getJSON();
        callback(json);
      },
      onFailure : function() {
        Utils.message('フィードの取得に失敗しました', 'error');
      }
    });
  },
  getCachedFeed : function(uri, callback) {
    var uri = 'index.php?m=Feed&a=Read&uri=' + uri;
    new Ajax.Request(uri, {
      onComplete : function(req) {
        eval('var json = ' + req.responseText);
        callback(json);
      },
      onFailure : function() {
        Utils.message('フィードの取得に失敗しました', 'error');
      }
    });
  }
};
