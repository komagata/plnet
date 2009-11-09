if (typeof Photozou == 'undefined') Photozou = {};

JSAN.require('Photozou.Model');

Photozou.Model.PhotoComment = function() {}
Photozou.Model.PhotoComment.prototype = new Photozou.Model

Photozou.Model.PhotoComment.prototype.find_by_photo_id = function(photo_id, opt) {
  var self = this
  var opt = (function(d,s){for(var p in s)d[p]=s[p];return d})({
    'limit': false,
    'offset': false,
    'callback': new Function,
    'errback': self.raiseError
  }, opt || {});
  var url = '/xml/photo_comment/'+photo_id;
  new Ajax.Request(url, {
    method: 'get', 
    onComplete: function(req){
      if (self.isOk(req)) {
        opt.callback(self.req2comments(req))
      } else {
        opt.errback(req)
      }
    },
    onFailurer: opt.errback
  });
}

Photozou.Model.PhotoComment.prototype.save = function(ott, opt) {
//TODO: fixe photo_id setting
  var self = this
  var opt = (function(d,s){for(var p in s)d[p]=s[p];return d})({
    'callback': new Function,
    'errback': self.raiseError
  }, opt || {});
  var url = '/xml/photo_comment_add/'+self.photo_id;
  new Ajax.Request(url, {
    method: 'post', 
    parameters: {'comment':self.comment, 'ott':ott}, 
    onComplete: function(req) {
      if (self.isOk(req)) {
        opt.callback(req)
      } else {
        opt.errback(req)
      }
    },
    onFailurer: opt.errback
  });
}

Photozou.Model.PhotoComment.prototype.remove = function(photo_id, photo_comment_id, ott, opt) {
  var self = this
  var opt = (function(d,s){for(var p in s)d[p]=s[p];return d})({
    'callback': new Function,
    'errback': self.raiseError
  }, opt || {});
  var url = '/xml/photo_edit/?photo_id='+photo_id+'&mode=remove_comments&remove_comment[]='+photo_comment_id
  new Ajax.Request(url, {
    method: 'post',
    parameters: {'ott':ott},
    onComplete: function(req) {
      if (self.isOk(req)) {
        opt.callback(req)
      } else {
        opt.errback(req)
      }
    },
    onFailurer: opt.errback
  });
}

Photozou.Model.PhotoComment.prototype.remove_by_commenter = function(photo_comment_id, ott, opt) {
  var self = this
  var opt = (function(d,s){for(var p in s)d[p]=s[p];return d})({
    'callback': new Function,
    'errback': self.raiseError
  }, opt || {});
  var url = '/xml/photo_comment_remove/'+photo_comment_id
  new Ajax.Request(url, {
    method: 'post',
    parameters: {'ott':ott},
    onComplete: function(req) {
      if (self.isOk(req)) {
        opt.callback(req)
      } else {
        opt.errback(req)
      }
    },
    onFailurer: opt.errback
  });
}

Photozou.Model.PhotoComment.prototype.isOk = function(req) {
  var result = this.dom2obj(req.responseXML.documentElement)
  return result['@stat'] == 'ok' ? true : false
}

Photozou.Model.PhotoComment.prototype.req2comments = function(req) {
  var obj = this.dom2obj(req.responseXML.documentElement)
  var comments = []
  if (typeof obj.comments.comment != 'undefined') {
    if (obj.comments.comment.length > 0) {
      comments = obj.comments.comment
    } else {
      comments.push(obj.comments.comment)
    }
  }
  return comments
}
