Event.observe(window, 'load', function() {
  show_services();
  show_registered_services();
  Event.observe('add-customfeed', 'click', add_original_feed);
});

function show_services() {
  var url = '/api/contents';

  $('services').update(
    '<img src="/images/loading.gif" class="loading" /> '+msg('loading...')
  )

  new Ajax.Request(url, {
    method: 'get',
    onComplete: function(req) {
      var results = eval(req.responseText)

      var contents_categories = {}
      for (var i=0;i<results.length;i++) {
        var c = results[i]
        c['category_name'] = msg(c['category_name'])
        if (typeof contents_categories[c['category_id']] == 'undefined') {
          contents_categories[c['category_id']] = {}
        }
        var cc = contents_categories[c['category_id']]
        cc['name'] = c['category_name']
        if (typeof cc['contents'] == 'undefined') cc['contents'] = []
        if (c.format != '') cc['contents'].push(c)
      }

      $('services').innerHTML = TrimPath.processDOMTemplate(
        'services_template',
        {'contents_categories':contents_categories}
      )
    },
    onFailurer: function(req) { alert('Failed') }
  })
}

function show_registered_services(afterFinish) {
  var afterFinish = afterFinish || new Function
  var url = '/api/feeds/'+Plnet.Member.account

  $('registered_services').update(
    '<img src="/images/loading.gif" class="loading" /> '+msg('loading...')
  )

  get_content_categories(function(content_categories) {
    new Ajax.Request(url, {
      'method': 'get',
      'onComplete': function(req) {
        var feeds = eval(req.responseText)

        for (var i=0;i<feeds.length;i++) {
          var feed = feeds[i]
          for (var n=0;n<content_categories.length;n++) {
            var content_category = content_categories[n]
            if (typeof content_category['feeds'] == 'undefined')
              content_category['feeds'] = []

            if (content_category['id'] == feed['category_id'])
              content_category['feeds'].push(feed)
          }
        }

        $('registered_services').innerHTML = TrimPath.processDOMTemplate(
          'registered_services_template',
          {'content_categories':content_categories}
        )
        Sortable.create('registered_feeds', {
          'onUpdate':function(el){
            var seq = Sortable.serialize(el)
            var regs = seq.replace(/registered_feeds\[\]=/g, '').split(/&/)
            var cur_cat_id = 8 // magic number(other category id)
            var sorted_feeds = []
            var formated_params = {}
            for (var i=0;i<regs.length;i++) {
              if (regs[i].match(/^0/)) {
                cur_cat_id = regs[i].replace(/^0/, '')
              } else {
                sorted_feeds.push({'category_id':cur_cat_id, 'id':regs[i]})
                formated_params['feeds['+i+'][category_id]'] = cur_cat_id
                formated_params['feeds['+i+'][id]'] = regs[i]
              }
            }

            // put sorted feeds
            new Ajax.Request(url, {
              'method': 'put',
              'parameters':formated_params,
              'onComplete':function(req) {},
              'onFailurer':function() { alert('Failed to sort') }
            })
          }
        })
        afterFinish()
      },
      'onFailurer': function(req) { alert('Failed') }
    })
  })
}

function add_service(icon, name, target, format) {
  var m = '<img src="/icon.php?url='+icon+'" class="favicon" /> '+msg('input target on name').replace(/target/, target).replace(/name/, name)
  var dialog = new Plnet.Dialog
  dialog.prompt(m, {
    'height': 100,
    'width': 340,
    'cancelText': msg('cancel'),
    'onOk': function() {
      $('dinput').disabled = true
      $('dbutton_ok').disabled = true
      $('dbutton_cancel').disabled = true
      $('dmsg').innerHTML =
        '<img src="/images/loading.gif" class="icon" /> '+msg('adding...')

      var onFailurer = function(res) {
        dialog.close()
        if (res == 'already exists') {
          var m = msg('its service is already registered')
        } else {
          var m = msg('could not add a feed')
        }

        var d = new Plnet.Dialog
        d.alert('<img src="/images/error.gif" class="icon" /> '+m, {'height':100, 'width':340})
      }

      new Ajax.Request('/api/feed', {
        'method': 'post',
        'parameters':{
          'account':Plnet.Member.account,
          'uri':format.replace(/##username##/, $F('dinput'))
        },
        'onComplete': function(req) {
          try {
            var res = eval(req.responseText)
          } catch(e) {
            onFailurer(res)
            return false
          }
          if (res == true) {
            show_registered_services(function() {
              dialog.close(function() {
                $('registered_services').visualEffect('Highlight', {'startcolor':'#f57900'})
              })
            })
          } else {
            onFailurer(res)
          }
        },
        'onFailurer':onFailurer
      })
    }
  })
}

function remove_service(feed_id) {
  var dialog = new Plnet.Dialog
  dialog.confirm(msg('do you want to delete this service'), {
    'height': 80,
    'width': 340,
    'cancelText': msg('cancel'),
    'onOk': function() {
      $('dbutton_ok').disabled = true
      $('dbutton_cancel').disabled = true
      $('dmsg').innerHTML = 
        '<img src="/images/loading.gif" class="icon" /> '+msg('deleting...')

      var onFailurer = function() {
        dialog.close()
        var d = new Plnet.Dialog
        d.alert('<img src="/images/error.gif" class="icon" /> '+msg('could not delete a feed'), {'height':80, 'width':340})
      }

      new Ajax.Request('/index.php?m=MyContent&a=RemoveFeed', {
        'method':'delete',
        'parameters':{
          'id':feed_id
        },
        'onComplete': function(req) {
          try {
            var res = eval(req.responseText)
          } catch(e) {
            onFailurer() 
            return false
          }
          if (res == true) {
            show_registered_services(function() {
              dialog.close(function() {
                $('registered_services').visualEffect('Highlight', {'startcolor':'#f57900'})
              })
            })
          } else {
            onFailurer() 
          }
        },
        'onFailurer':onFailurer
      })
    }
  })
}

function add_original_feed() {
  var dialog = new Plnet.Dialog 
  dialog.prompt(msg('fill in the origin url of feed'), {
    'height': 100,
    'width': 340,
    'cancelText': msg('cancel'),
    'onOk': function() {
      $('dinput').disabled = true
      $('dbutton_ok').disabled = true
      $('dbutton_cancel').disabled = true
      $('dmsg').innerHTML = 
        '<img src="/images/loading.gif" class="icon" /> '+msg('adding...')

      var onFailurer = function() {
        dialog.close()
        var d= new Plnet.Dialog
        d.alert('<img src="/images/error.gif" class="icon" /> '+msg('could not add a feed'), {'height':100, 'width':340})
      }

      new Ajax.Request('/api/feed', {
        'method': 'post',
        'parameters':{
          'uri':$F('dinput')
        },
        'onComplete':function(req) {
          try {
            var res = eval(req.responseText)
          } catch(e) {
            onFailurer() 
            return false
          }
          if (res == true) {
            show_registered_services(function() {
              dialog.close(function() {
                $('registered_services').visualEffect('Highlight', {'startcolor':'#f57900'})
              })
            })
          } else {
            onFailurer()
          }
        },
        'onFailurer':onFailurer
      })
    }
  })
}

function get_content_categories(callback) {
  var callback = callback || new Function
  new Ajax.Request('/api/content_categories', {
    'method': 'get',
    'onComplete': function(req) {
      callback(eval(req.responseText))
    },
    'onFailurer': function(req) { alert('Failed') }
  })
}
