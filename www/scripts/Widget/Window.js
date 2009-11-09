if (!Widget) var Widget = {};
if (!Widget.Window) Widget.Window = {};

Widget.Window.VERSION = '0.02';

Widget.Window = function(options) {
  this.id = (new Date).getTime()
//  this.id = Math.random()
  this.options = /* Object.extend */(function(d,s){for(var p in s)d[p]=s[p];return d})/**/({
    'modal': false,
    'opacity': 0.5,
    'height': 100,
    'width': 200
  }, options || {});
};

Widget.Window.prototype.open = function(content) {
  var content = content || '';
  this.addOverlay();
  var dwindow = this.getWindow(this.options.height, this.options.width);

  if (typeof content == 'string') {
    dwindow.innerHTML = content;
  } else {
    dwindow.appendChild(content);
  }

  document.body.appendChild(dwindow);
  return this;
};


Widget.Window.prototype.addOverlay = function() {
  document.body.appendChild(this.getOverlay());
}

Widget.Window.prototype.getOverlay = function() {
  var doverlay = document.createElement('div')
  doverlay.id = 'doverlay_'+this.id
  doverlay.className = 'doverlay'
  with(doverlay.style) {
    top = '0px';
    left = '0px';
    position = 'absolute';
    background = '#000';
  }

  if (!this.options.modal) {
    var self = this;
    doverlay.onclick = function() {
      self.close();
    };
  }

  /* Element.setOpacity */(function(e,v){if(typeof e=='string')e=document.getElementById(e);if(v==1){e.style.opacity=(/Gecko/.test(navigator.userAgent)&&!/Konqueror|Safari|KHTML/.test(navigator.userAgent))?0.999999:1.0;if(/MSIE/.test(navigator.userAgent) && !window.opera)e.style.filter=e.style.filter.replace(/alpha\([^\)]*\)/gi,'');}else{if(v<0.00001)v=0;e.style.opacity=v;if(/MSIE/.test(navigator.userAgent) && !window.opera)e.style.filter=e.style.filter.replace(/alpha\([^\)]*\)/gi,'')+'alpha(opacity='+v*100+')';}return e;})/**/(doverlay, this.options.opacity);
  var yScroll = (function(){if(window.innerHeight && window.scrollMaxY){return window.innerHeight + window.scrollMaxY}else if(document.body.scrollHeight > document.body.offsetHeight){return document.body.scrollHeight}else{return document.body.offsetHeight}})()
  var xScroll = (function(){if(window.innerHeight && window.scrollMaxY){return document.body.scrollWidth}else if(document.body.scrollHeight > document.body.offsetHeight){return document.body.scrollWidth}else{return document.body.offsetWidth}})()

  doverlay.style.height = yScroll+'px';
  doverlay.style.width = xScroll+'px';
  return doverlay
};

Widget.Window.prototype.removeOverlay = function() {
  document.body.removeChild(document.getElementById('doverlay_'+this.id));
};

Widget.Window.prototype.getWindow = function(height, width) {
  var dwindow = document.createElement('div')
  dwindow.id = 'dwindow_'+this.id
  dwindow.className = 'dwindow'
  var pageSize = this._getPageSize();
  var pos = this._realOffset(document.body);
  dwindow.style.top = (pageSize.windowHeight/2 - height/2 + pos[1])+'px';
  dwindow.style.left = (pageSize.windowWidth/2 - width/2 + pos[0])+'px';
  dwindow.style.height = height+'px';
  dwindow.style.width = width+'px';
  dwindow.style.position = 'absolute';
  dwindow.style.textAlign = 'center';
  return dwindow;
}

Widget.Window.prototype.close = function() {
  this.removeOverlay()
  document.body.removeChild(document.getElementById('dwindow_'+this.id))
  return this
}

Widget.Window.prototype._realOffset = function(element) {
  var valueT = 0, valueL = 0;
  do {
    valueT += element.scrollTop  || 0;
    valueL += element.scrollLeft || 0;
    element = element.parentNode;
  } while (element);
  return [valueL, valueT];
};

Widget.Window.prototype._getPageSize = function() {
  var xScroll, yScroll;
  if (window.innerHeight && window.scrollMaxY) {
    xScroll = document.body.scrollWidth;
    yScroll = window.innerHeight + window.scrollMaxY;
  } else if (document.body.scrollHeight > document.body.offsetHeight){
    // all but Explorer Mac
    xScroll = document.body.scrollWidth;
    yScroll = document.body.scrollHeight;
  } else {
    // Explorer Mac...would also work in Explorer 6 Strict,
    // Mozilla and Safari
    xScroll = document.body.offsetWidth;
    yScroll = document.body.offsetHeight;
  }

  var windowWidth, windowHeight;
  if (self.innerHeight) {      // all except Explorer
    windowWidth = self.innerWidth;
    windowHeight = self.innerHeight;
  } else if (document.documentElement
  && document.documentElement.clientHeight) {
    // Explorer 6 Strict Mode
    windowWidth = document.documentElement.clientWidth;
    windowHeight = document.documentElement.clientHeight;
  } else if (document.body) { // other Explorers
    windowWidth = document.body.clientWidth;
    windowHeight = document.body.clientHeight;
  }

  // for small pages with total height less then height of the viewport
  if(yScroll < windowHeight){
    pageHeight = windowHeight;
  } else {
    pageHeight = yScroll;
  }

  // for small pages with total width less then width of the viewport
  if(xScroll < windowWidth){
    pageWidth = windowWidth;
  } else {
    pageWidth = xScroll;
  }

  return {
    'pageWidth':pageWidth,
    'pageHeight':pageHeight,
    'windowWidth':windowWidth,
    'windowHeight':windowHeight,
    'yScroll':yScroll,
    'xScroll':xScroll
  };
};

/*

=head1 NAME

Widget.Window - Simple Window Library

=head1 SYNOPSIS

// Functional Example
Widget.Window.alert('Alert!!!');
Widget.Window.confirm('Confirm!!!');
Widget.Window.prompt('Prompt!!!');

// OO-Style Example
var Window = new Widget.Window();
Window.alert('Alert!!!');

// Getting input value
Widget.Window.prompt('Input here.', {
  onOk: function(val) {
    alert(val);
    Widget.Window.close();
  }
});

=head1 DESCRIPTION

Widget.Window is simple modal Window library.
like a alert, confirm and prompt.

=head1 METHODS

=head2 Widget.Window.alert(msg[, options]);

This method show alert modal Window box.

=head2 Widget.Window.confirm(msg[, options]);

This method show confirm modal Window box.

=head2 Widget.Window.prompt(msg[, options]);

This method show prompt modal Window box.

=head1 AUTHOR

Masaki Komagata <komagata@gmail.com>

=head1 COPYRIGHT

  Copyright (C) 2006 Masaki Komagata <komagata@gmail.com> 
      All rights reserved.
      This is free software with ABSOLUTELY NO WARRANTY.

  You can redistribute it and/or modify it under the terms of 
  the GNU General Public License version 2.

=cut

*/
