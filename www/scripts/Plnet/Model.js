if (typeof Photozou == 'undefined') Photozou = {};

Photozou.Model = function() {};

Photozou.Model.prototype.raiseError = function(msg) {
  alert(msg)
}

Photozou.Model.prototype.dom2obj = function(elem) {
  //  COMMENT_NODE
  if ( elem.nodeType == 7 ) {
    return;
  }

  //  TEXT_NODE CDATA_SECTION_NODE
  if ( elem.nodeType == 3 || elem.nodeType == 4 ) {
    var bool = elem.nodeValue.match( /[^\x00-\x20]/ );
    if ( bool == null ) return;     // ignore white spaces
    return elem.nodeValue;
  }

  var retval;
  var cnt = {};
  var sep = '@';
  var addNode = function ( hash, key, cnts, val ) {
    if ( cnts == 1 ) {                   // 1st sibling
      hash[key] = val;
    } else if ( cnts == 2 ) {                   // 2nd sibling
      hash[key] = [ hash[key], val ];
    } else {                                    // 3rd sibling and more
      hash[key][hash[key].length] = val;
    }
  }

  //  parse attributes
  if ( elem.attributes && elem.attributes.length ) {
    retval = {};
    for ( var i=0; i<elem.attributes.length; i++ ) {
      var key = elem.attributes[i].nodeName;
      if ( typeof(key) != "string" ) continue;
      var val = elem.attributes[i].nodeValue;
      if ( ! val ) continue;
      key = sep + key;
      if ( typeof(cnt[key]) == "undefined" ) cnt[key] = 0;
      cnt[key] ++;
      addNode( retval, key, cnt[key], val );
    }
  }

  //  parse child nodes (recursive)
  if ( elem.childNodes && elem.childNodes.length ) {
    var textonly = true;
    if ( retval ) textonly = false;        // some attributes exists
    for ( var i=0; i<elem.childNodes.length && textonly; i++ ) {
      var ntype = elem.childNodes[i].nodeType;
      if ( ntype == 3 || ntype == 4 ) continue;
      textonly = false;
    }
    if ( textonly ) {
      if ( ! retval ) retval = "";
      for ( var i=0; i<elem.childNodes.length; i++ ) {
          retval += elem.childNodes[i].nodeValue;
      }
    } else {
      if ( ! retval ) retval = {};
      for ( var i=0; i<elem.childNodes.length; i++ ) {
        var key = elem.childNodes[i].nodeName;
        if ( typeof(key) != "string" ) continue;
        var val = this.dom2obj( elem.childNodes[i] );
        if ( ! val ) continue;
        if ( typeof(cnt[key]) == "undefined" ) cnt[key] = 0;
        cnt[key] ++;
        addNode( retval, key, cnt[key], val );
      }
    }
  }
  return retval;
}
