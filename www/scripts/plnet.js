var Utils = {
  message : function(message, stats, target) {
    message = message || '';
    stats = stats || 'none';
    target = target || 'message';
    if (typeof(target) != 'object') target = $(target);
    message_icon = '<img src="images/message.gif" style="margin-right:3px;vertical-align:middle" />';
    loading_icon = '<img src="images/loading.gif" style="margin-right:3px;vertical-align:middle" />';
    add_icon = '<img src="images/add.png" style="margin-right:3px;vertical-align:middle" />';
    update_icon = '<img src="images/update.png" style="margin-right:3px;vertical-align:middle" />';
    remove_icon = '<img src="images/remove.png" style="margin-right:3px;vertical-align:middle" />';
    error_icon = '<img src="images/error.gif" style="margin-right:3px;vertical-align:middle" />';
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
    if (stats == 'none') {
      message = '';
    }
    Element.update(target, message);
  }
};
