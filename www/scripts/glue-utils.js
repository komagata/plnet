var GlueUtils = {
  message: function(message, options) {
    options = options || {
      message : false,
      add : false,
      update : false,
      remove : false,
      error : false,
      loading : false
    };
    message_icon = '<img src="images/message.gif" />&nbsp;';
    loading_icon = '<img src="images/loading.gif" />&nbsp;';
    add_icon = '<img src="images/add.png" />&nbsp;';
    update_icon = '<img src="images/update.png" />&nbsp;';
    remove_icon = '<img src="images/remove.png" />&nbsp;';
    error_icon = '<img src="images/error.gif" />&nbsp;';
    if (options.message) {
      message = message_icon + message;
    }
    if (options.add) {
      message = add_icon + message;
    }
    if (options.update) {
      message = update_icon + message;
    }
    if (options.remove) {
      message = remove_icon + message;
    }
    if (options.error) {
      message = error_icon + message;
    }
    if (options.loading) {
      message = loading_icon + message;
    }
    Element.update('message', message);
  }
};

var Task = Class.create();
Task.prototype = {
  initialize: function(cnt) {
    this.cnt = cnt;
  },
  start: function(name, msg, icon) {
    this.cur++;
  },
  end: function(name, msg, icon) {
    
  },
  setFinish: function(msg, icon) {
    this.finish = function() {
      GlueUtils.message(msg, {icon : true});
    }
  }
}

var Utils = {
  setEventToClass: function(element, method, func) {
    document.getElementsByClassName(element).each(function(c){
      c[method] = func;
    });
  }
}
