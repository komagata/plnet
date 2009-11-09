Event.observe(window, 'load', function() {
  updateTab();
  updateFeed(null);
});

function updateTab() {
  var url = 'index.php?m=Glue&a=MyTab';

  GlueUtils.message('タブを読み込んでいます', {loading : true});

  new Ajax.Request(url, {
    onComplete: function(req) {
      eval('var result = ' + req.responseText);
       list ='<ul class="channel">';
       result.each(function(content) {
         list += '<li id="channel-' + content.id + '">' + content.title + '</li>';
       });
       list += '</ul>';
      Element.update('channel', list);

      // event to display specific feed.
      Utils.setEventToClass('channel', 'onclick', function(){
        var id = this.paraentNode.id.split('-')[1];
         updateFeed(id);
      });
      Utils.setEventToClass('channel', 'onmouseover', function() {
        Element.setStyle(this, {'background-color' : '#eeeeee'});
      });
    }
  });
}

/*
function updateFeed(id) {
  if (url == null){
    var url = 'index.php?m=Glue&a=MyFeed';
  } else {
    var url = 'index.php?m=Glue&a=MyFeed&id=' + id;
  }

  GlueUtils.message('コンテンツを読み込んでいます', {loading : true});

  new Ajax.Request(url, {
    onComplete: function(req) {
      eval('var result = ' + req.responseText);
       list = '';
       result.each(function(content) {
         list += '<div class="feed">';
         list += '<div class="feed-title"><a href="' + content.link + '">' + content.title + '</a></div>';
         list += '<div class="feed-description">' + content.description + '</div>';
         list += '<div class="feed-date">' + content.date + '</div>';
         list += '</div>';
       });
      Element.update('result', list);
      GlueUtils.message('完了');
    }
  });
}
*/

// 並列読み込み実験中
function updateFeed() {
  GlueUtils.message('コンテンツを読み込んでいます', {loading : true});
  var urls = [
    'feed.php?uri=http://del.icio.us/rss/popular/',
    'feed.php?uri=http://del.icio.us/rss/popular/',
    'feed.php?uri=http://del.icio.us/rss/popular/',
    'feed.php?uri=http://del.icio.us/rss/popular/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://p0t.jp/mt/index.xml',
    'feed.php?uri=http://www.planet-php.org/rdf/',
    'feed.php?uri=http://www.doyouphp.jp/home.rss'
  ];

  var item_cnt = 0;
  var feed_cnt = 0;
  urls.each(function(url) {

    new Ajax.Request(url, {
      onComplete: function(req) {
        eval('var result = ' + req.responseText);
        result.items.each(function(item) {
          var list = '<div class="feed">';
          list += '<div class="feed-title"><a href="' + item.link + '">' + 
            item.title + '</a></div>';
          list += '<div class="feed-description">' + item.description + '</div>';
          list += '<div class="feed-date">' + item.date + '</div>';
          list += '</div>';
          new Insertion.Bottom('result', list);
          item_cnt++;
        });

        feed_cnt++;

        var icon = [];
        if (urls.length == feed_cnt) {
          GlueUtils.message('コンテンツの読み込みが完了しました', {message : true});
        } else {
          GlueUtils.message('コンテンツを読み込んでいます。フィード（' + feed_cnt + '/' + urls.length + '個）、コンテンツ（' + item_cnt + '個）', {loading : true});
        }
      }
    });
  });
}

