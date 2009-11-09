var Feed = Class.create();
Feed.prototype = {
  initialize : function(data) {
    this.data = data
  },
  parse : function() {
    var xotree = new XML.ObjTree();
    var tree = xotree.parseXML(this.data);
    this.data = tree;

    var kind = this.getKind();
    eval('this.parser = new ' + kind);
    this.parser.data = this.data;
    this.parser.kind = kind;
  },
  getKind : function() {
    var kind;
    if (this.data['rdf:RDF'] &&
    this.data['rdf:RDF']['-xmlns'] == 'http://purl.org/rss/1.0/') {
      kind = 'RSS10';
    } else if (this.data['rss'] && this.data['rss']['-version'] == '2.0') {
      kind = 'RSS20';
    } else if (this.data['feed'] && this.data['feed']['-version'] == '0.3') {
      kind = 'Atom03';
    } else {
      kind = 'UnknownFeed';
    }
    return kind;
  },
  getTitle : function() {
    return this.parser.getTitle();
  },
  getLink : function() {
    return this.parser.getLink();
  },
  getItems : function() {
    return this.parser.getItems();
  },
  getJSON : function() {
    return {
      title : this.getTitle(),
      link : this.getLink(),
      items : this.getItems()
    };
  }
};

var RSS10 = Class.create();
RSS10.prototype = {
  initialize : function() {},
  getTitle : function() {
    return this.data['rdf:RDF'].channel.title;
  },
  getLink : function() {
    return this.data['rdf:RDF'].channel.link;
  },
  getItems : function() {
    var items = this.data['rdf:RDF'].item;
    var res = [];
    items.each(function(item) {
      item['date'] = item['date'] || item['dc:date'];
      res.push(item);
    });
    return res;
  }
};

var RSS20 = Class.create();
RSS20.prototype = {
  initialize : function() {},
  getTitle : function() {
    return this.data['rss'].channel.title;
  },
  getLink : function() {
    return this.data['rss'].channel.link;
  },
  getItems : function() {
    var items = this.data['rss'].channel.item;
    var res = [];
    items.each(function(item) {
      item['date'] = item['date'] || item['dc:date'] || item['pubDate'];
      res.push(item);
    });
    return res;
  }
};

var Atom03 = Class.create();
Atom03.prototype = {
  initialize : function() {},
  getTitle : function() {
    var title =  this.data['feed'].title;
    if (typeof(title) == 'string') {
      return title;
    } else {
      return title['#text'];
    }
  },
  getLink : function() {
    var link = this.data['feed'].link;
    var res = '';
    if (!link['-href']) {
      link.each(function(ln) {
        if (ln['-rel'] == 'alternate') {
          res = ln['-href'];
        }
      });
    } else {
      res = this.data['feed'].link['-href'];
    }
      return res;
  },
  getItems : function() {
    var items = this.data['feed'].entry;
    var res = [];
    items.each(function(item) {
      var title = item['title'] = item.title;
      if (typeof(title) == 'string') {
        item['title'] = title;
      } else {
        item['title'] = title['#text'];
      }
      item['date'] = item['date'] || item['dc:date'] || item['created'];
      if (typeof(item.link['-href']) != 'string') {
        item.link.each(function(ln) {
          if (ln['-rel'] == 'alternate') {
            item['link'] = ln['-href'];
          }
        });
      } else {
        item['link'] = item.link['-href'] || '';
      }

      if (typeof(item.content['#cdata-section']) == 'string') {
        item['description'] = item.content['#cdata-section'];
      } else {
        item['description'] = item.content.div['#text'];
      }
      res.push(item);
    });
    return res;
  }
};
