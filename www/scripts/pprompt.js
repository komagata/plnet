//
// pprompt.js - simple window library.
//
// Copyright (C) 2005 Masaki Komagata <komagata@gmail.com> 
//     All rights reserved.
//     This is free software with ABSOLUTELY NO WARRANTY.
//
// You can redistribute it and/or modify it under the terms of 
// the GNU General Public License version 2.
//
var PPrompt = {
  alert: function(msg, options) {
    var options = this._extend(this.getOptions(), options || {});
    this.addOverlay();

    var pwindow = this.getWindow(options.height, options.width);

    // msg
    var pmsg = document.createElement('div');
    pmsg.className = 'pmsg';
    pmsg.style.padding = '6px';
    pmsg.appendChild(document.createTextNode(msg));

    pwindow.appendChild(pmsg);

    // buttons
    var pbuttons = document.createElement('div');
    pbuttons.id = 'pbuttons';
    pbuttons.style.padding = '6px';

    // ok
    var pbuttonOk = document.createElement('button');
    pbuttonOk.className = 'pbutton';
    pbuttonOk.appendChild(document.createTextNode(options.labelOk));
    pbuttonOk.onclick = options.onOk;
    pbuttons.appendChild(pbuttonOk);

    pwindow.appendChild(pbuttons);
    document.body.appendChild(pwindow);
  },
  confirm: function(msg, options) {
    var options = this._extend(this.getOptions(), options || {});
    this.addOverlay();

    var pwindow = this.getWindow(options.height, options.width);

    // msg
    var pmsg = document.createElement('div');
    pmsg.className = 'pmsg';
    pmsg.style.padding = '6px';
    pmsg.appendChild(document.createTextNode(msg));
    pwindow.appendChild(pmsg);

    // buttons
    var pbuttons = document.createElement('div');
    pbuttons.id = 'pbuttons';
    pbuttons.style.padding = '6px';

    // ok
    var pbuttonOk = document.createElement('button');
    pbuttonOk.className = 'pbutton';
    pbuttonOk.appendChild(document.createTextNode(options.labelOk));
    pbuttonOk.onclick = options.onOk;
    pbuttons.appendChild(pbuttonOk);

    // cancel
    var pbuttonCancel = document.createElement('button');
    pbuttonCancel.className = 'pbutton';
    pbuttonCancel.appendChild(document.createTextNode(options.labelCancel));
    pbuttonCancel.onclick = options.onCancel;
    pbuttons.appendChild(pbuttonCancel);

    pwindow.appendChild(pbuttons);
    document.body.appendChild(pwindow);
  },
  prompt: function(msg, options) {
    var opt = this.getOptions();
    opt.height = 80;
    var options = this._extend(opt, options || {});

    this.addOverlay();

    var pwindow = this.getWindow(options.height, options.width);

    // msg
    var pmsg = document.createElement('div');
    pmsg.className = 'pmsg';
    pmsg.style.padding = '6px';
    pmsg.appendChild(document.createTextNode(msg));
    pwindow.appendChild(pmsg);

    // buttons
    var pbuttons = document.createElement('div');
    pbuttons.id = 'pbuttons';
    pbuttons.style.padding = '6px';

    // input
    var pinput = document.createElement('input');
    pinput.id = 'pinput';
    pinput.style.width = '260px';
    pinput.setAttribute('type', 'text');
    pwindow.appendChild(pinput);

    // ok
    var pbuttonOk = document.createElement('button');
    pbuttonOk.className = 'pbutton';
    pbuttonOk.appendChild(document.createTextNode(options.labelOk));
    pbuttonOk.onclick = options.onOk;
    pbuttons.appendChild(pbuttonOk);

    // cancel
    var pbuttonCancel = document.createElement('button');
    pbuttonCancel.className = 'pbutton';
    pbuttonCancel.appendChild(document.createTextNode(options.labelCancel));
    pbuttonCancel.onclick = options.onCancel;
    pbuttons.appendChild(pbuttonCancel);

    pwindow.appendChild(pbuttons);

    document.body.appendChild(pwindow);
  },
  addOverlay: function() {
    var poverlay = document.createElement('div');
    poverlay.id = 'poverlay';
    poverlay.style.top = '0px';
    poverlay.style.left = '0px';
    poverlay.style.position = 'absolute';
    poverlay.style.background = '#000';

    this._setOpacity(poverlay, 0.5);
    var pageSize = this._getPageSize();
    poverlay.style.height = pageSize.pageHeight+'px';
    poverlay.style.width = '100%';
    document.body.appendChild(poverlay);
  },
  removeOverlay: function() {
    document.body.removeChild(document.getElementById('poverlay'));
  },
  getWindow: function(height, width) {
    document.body.style.padding = '0';
    var pwindow = document.createElement('div');
    pwindow.id = 'pwindow';
    var pageSize = this._getPageSize();
    var pos = this._realOffset(document.body);
    pwindow.style.top = (pageSize.windowHeight/2 - height/2 + pos[1])+'px';
    pwindow.style.left = (pageSize.windowWidth/2 - width/2 + pos[0])+'px';
    pwindow.style.height = height+'px';
    pwindow.style.width = width+'px';
    pwindow.style.position = 'absolute';
    pwindow.style.background = '#fff';
    pwindow.style.border = '6px solid #ccc';
    pwindow.style.padding = '6px';
    pwindow.style.textAlign = 'center';
    return pwindow;
  },
  close: function() {
    this.removeOverlay();
    document.body.removeChild(document.getElementById('pwindow'));
  },
  getOptions: function() {
    return {
      'height'      : 70,
      'width'       : 300,
      'labelOk'     : 'OK',
      'labelCancel' : 'Cancel',
      'onOk'        : function() {
        PPrompt.close();
      },
      'onCancel'    : function() {
        PPrompt.close();
      }
    };
  },
  _extend: function(destination, source) {
    for (var property in source) {
      destination[property] = source[property];
    }
    return destination;
  },
  _realOffset: function(element) {
    var valueT = 0, valueL = 0;
    do {
      valueT += element.scrollTop  || 0;
      valueL += element.scrollLeft || 0;
      element = element.parentNode;
    } while (element);
    return [valueL, valueT];
  },
  _setOpacity: function(element, value){
    if (typeof element == 'string')
      element= $(element);
    if (value == 1){
      element.style.opacity = (/Gecko/.test(navigator.userAgent) && !/Konqueror|Safari|KHTML/.test(navigator.userAgent)) ? 0.999999 : 1.0 ;
      if(/MSIE/.test(navigator.userAgent) && !window.opera)
        element.style.filter = element.style.filter.replace(/alpha\([^\)]*\)/gi,'');
    } else {
      if(value < 0.00001) value = 0;
      element.style.opacity = value;
      if(/MSIE/.test(navigator.userAgent) && !window.opera)
        element.style.filter = element.style.filter.replace(/alpha\([^\)]*\)/gi,'') + 'alpha(opacity='+value*100+')';
    }
    return element;
  },
  _getPageSize: function() {
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
  }
};
