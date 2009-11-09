JSAN.require('Widget.Window');
if (!Widget.Dialog) Widget.Dialog = {};

Widget.Dialog.VERSION = '0.02';

Widget.Dialog = function() {
  var self = this
  this.options = {
    'modal': true,
    'overlay': true,
    'opacity': 0.5,
    'height': 100,
    'width': 200,
    'okText': 'OK',
    'cancelText': 'Cancel',
    'onOk': function() { self.close() },
    'onCancel': function() { self.close() }
  }
}

Widget.Dialog.confirm = function(msg, options) {
  var dialog = new Widget.Dialog
  dialog.confirm(msg, options)
  return dialog
}

Widget.Dialog.prompt = function(msg, options) {
  var dialog = new Widget.Dialog
  dialog.prompt(msg, options)
  return dialog
}

Widget.Dialog.prototype = new Widget.Window;

Widget.Dialog.prototype.confirm = function(msg, options) {
  var self = this;
  var options = (function(d,s){for(var p in s)d[p]=s[p];return d})(this.options, options || {});

  var dwindow = this.getWindow(options.height, options.width);

  // msg
  var dmsg = document.createElement('div');
  dmsg.id = 'dmsg';
  dmsg.style.padding = '6px';
  dmsg.appendChild(document.createTextNode(msg));
  dwindow.appendChild(dmsg);

  // buttons
  var dbuttons = document.createElement('div');
  dbuttons.id = 'dbuttons';
  dbuttons.style.padding = '6px';

  // ok
  var dbuttonOk = document.createElement('button');
  dbuttonOk.id = 'dbutton_ok';
  dbuttonOk.className = 'dbutton';
  dbuttonOk.appendChild(document.createTextNode(options.okText));
  dbuttonOk.onclick = function() {
    options.onOk();
  };

  dbuttons.appendChild(dbuttonOk);

  // cancel
  var dbuttonCancel = document.createElement('button');
  dbuttonCancel.id = 'dbutton_cancel';
  dbuttonCancel.className = 'dbutton';
  dbuttonCancel.appendChild(document.createTextNode(options.cancelText));
  dbuttonCancel.onclick = function() {
    options.onCancel();
  }
  dbuttons.appendChild(dbuttonCancel);

  dwindow.appendChild(dbuttons);

  if (this.options.overlay) this.addOverlay();
  document.body.appendChild(dwindow);
}

Widget.Dialog.prototype.prompt = function(msg, options) {
  var self = this;
  var options = (function(d,s){for(var p in s)d[p]=s[p];return d})(this.options, options || {});

  var dwindow = this.getWindow(options.height, options.width);

  // msg
  var dmsg = document.createElement('div');
  dmsg.id = 'dmsg';
  dmsg.style.padding = '8px';
  dmsg.appendChild(document.createTextNode(msg));
  dwindow.appendChild(dmsg);

  // input
  var dinput = document.createElement('input');
  dinput.id = 'dinput';
  dwindow.appendChild(dinput);

  // buttons
  var dbuttons = document.createElement('div');
  dbuttons.id = 'dbuttons';
  dbuttons.style.padding = '6px';

  // ok
  var dbuttonOk = document.createElement('button');
  dbuttonOk.id = 'dbutton_ok';
  dbuttonOk.className = 'dbutton';
  dbuttonOk.appendChild(document.createTextNode(options.okText));
  dbuttonOk.onclick = function() {
    options.onOk();
  };

  dbuttons.appendChild(dbuttonOk);

  // cancel
  var dbuttonCancel = document.createElement('button');
  dbuttonCancel.id = 'dbutton_cancel';
  dbuttonCancel.className = 'dbutton';
  dbuttonCancel.appendChild(document.createTextNode(options.cancelText));
  dbuttonCancel.onclick = function() {
    options.onCancel();
  }
  dbuttons.appendChild(dbuttonCancel);

  dwindow.appendChild(dbuttons);

  if (this.options.overlay) this.addOverlay();
  document.body.appendChild(dwindow);
  dinput.focus()
}
