JSAN.require('Widget.Dialog')
if (typeof Plnet == 'undefined') Plnet = {}

Plnet.Dialog = function() {
  var self = this
  this.id = (new Date).getTime()
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

Plnet.Dialog.prototype = new Widget.Dialog

Plnet.Dialog.prototype.alert = function(msg, options) {
  var self = this
  var options = Object.extend(this.options, options || {})
  var doverlay = this.getOverlay()
  var dwindow = this.getWindow(options.height, options.width)

  dwindow.appendChild(this._dmsg(msg))
  dwindow.appendChild(this._dbuttonOk(options.okText, options.onOk));

  doverlay.style.display = 'none'
  document.body.appendChild(doverlay)
  new Effect.Appear(doverlay, {'from':0, 'to':0.5, 'delay':0, 'duration':0.4})

  dwindow.style.display = 'none'
  document.body.appendChild(dwindow)
  new Effect.Appear(dwindow, {'from':0, 'delay':0, 'duration':0.4, 'afterFinish':function(){
    $('dbutton_ok').focus()
  }})
}

Plnet.Dialog.prototype.confirm = function(msg, options) {
  var self = this
  var options = Object.extend(this.options, options || {})
  var doverlay = this.getOverlay()
  var dwindow = this.getWindow(options.height, options.width)

  dwindow.appendChild(this._dmsg(msg))
  dwindow.appendChild(this._dbuttons(options))

  doverlay.style.display = 'none'
  document.body.appendChild(doverlay)
  new Effect.Appear(doverlay, {'from':0, 'to':0.5, 'delay':0, 'duration':0.4})

  dwindow.style.display = 'none'
  document.body.appendChild(dwindow)
  new Effect.Appear(dwindow, {'delay':0, 'duration':0.4, 'afterFinish':function(){
    $('dbutton_ok').focus()
  }})
}

Plnet.Dialog.prototype.prompt = function(msg, options) {
  var self = this
  var options = Object.extend(this.options, options || {})
  var doverlay = this.getOverlay()
  var dwindow = this.getWindow(options.height, options.width)

  dwindow.appendChild(this._dmsg(msg))
  dwindow.appendChild(this._dinput())
  dwindow.appendChild(this._dbuttons(options))

  doverlay.style.display = 'none'
  document.body.appendChild(doverlay)
  new Effect.Appear(doverlay, {'from':0, 'to':0.5, 'delay':0, 'duration':0.4})

  dwindow.style.display = 'none'
  document.body.appendChild(dwindow)
  new Effect.Appear(dwindow, {'delay':0, 'duration':0.4, 'afterFinish':function(){
    $('dinput').focus()
  }})
}

Plnet.Dialog.prototype.close = function(afterFinish) {
  new Effect.Fade('doverlay_'+this.id, {'delay':0, 'duration':0.3})
  new Effect.Fade('dwindow_'+this.id, {'delay':0, 'duration':0.3, 'afterFinish':afterFinish})
  return this
}

Plnet.Dialog.prototype._dmsg = function(msg) {
  var dmsg = document.createElement('div')
  dmsg.id = 'dmsg'
  dmsg.style.padding = '8px'
  dmsg.innerHTML = msg
  return dmsg
}

Plnet.Dialog.prototype._dinput = function() {
  var dinput = document.createElement('input')
  dinput.id = 'dinput'
  return dinput
}

Plnet.Dialog.prototype._dbuttonOk = function(text, func) {
  var dbuttonOk = document.createElement('button')
  dbuttonOk.id = 'dbutton_ok'
  dbuttonOk.className = 'dbutton'
  dbuttonOk.appendChild(document.createTextNode(text))
  dbuttonOk.onclick = func
  return dbuttonOk
}

Plnet.Dialog.prototype._dbuttonCancel = function(text, func) {
  var dbuttonCancel = document.createElement('button')
  dbuttonCancel.id = 'dbutton_cancel'
  dbuttonCancel.className = 'dbutton'
  dbuttonCancel.appendChild(document.createTextNode(text))
  dbuttonCancel.onclick = func
  return dbuttonCancel
}

Plnet.Dialog.prototype._dbuttons = function(opt) {
  var dbuttons = document.createElement('div')
  dbuttons.id = 'dbuttons'
  dbuttons.style.padding = '6px'
  dbuttons.appendChild(this._dbuttonOk(opt.okText, opt.onOk))
  dbuttons.appendChild(this._dbuttonCancel(opt.cancelText, opt.onCancel))
  return dbuttons
}
