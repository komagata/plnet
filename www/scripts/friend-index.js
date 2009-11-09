Event.observe(window, 'load', function() {
  show_services()
  show_registered_services()
  show_friends()
})

function show_services() {
  var url = '/api/contents'
  $('services').update(
    '<img src="/images/loading.gif" class="loading" /> '+msg('loading...')
  )

  new Ajax.Request(url, {
    method: 'get',
    onComplete: function(req) {
      var contents = eval(req.responseText)
      var foaf_contents = []
      for (var i=0;i<contents.length;i++) {
        if (contents[i].foaf_format != '')
          foaf_contents.push(contents[i])
      }

      $('services').innerHTML = TrimPath.processDOMTemplate(
        'services_template',
        {'foaf_contents':foaf_contents}
      )
    },
    onFailurer: function(req) { alert('Failed') }
  })
}

function show_registered_services(afterFinish) {
  var afterFinish = afterFinish || new Function
  var url = '/api/foafs?raw&account='+Plnet.Member.account

  $('registered_services').innerHTML = 
    '<img src="/images/loading.gif" class="loading" /> '+msg('loading...')

  new Ajax.Request(url, {
    method: 'get',
    onComplete: function(req) {
      var foafs = eval(req.responseText)
      $('registered_services').innerHTML = TrimPath.processDOMTemplate(
        'registered_services_template',
        {'foafs':foafs}
      )
      afterFinish()
    },
    onFailurer: function(req) { alert('Failed') }
  })
}

function show_friends(afterFinish) {
  var afterFinish = afterFinish || new Function
  var url = '/api/friends?raw&account='+Plnet.Member.account

  $('friends').innerHTML = 
    '<img src="/images/loading.gif" class="loading" /> '+msg('loading...')

  new Ajax.Request(url, {
    method: 'get',
    onComplete: function(req) {
      try {
        var friends = eval(req.responseText)
      } catch(e) {
        alert('Failed: '+e.description)
      }
/*
      for (var i=0;i<friends.length;i++) {
        
      }
*/
      $('friends').innerHTML = TrimPath.processDOMTemplate(
        'friends_template',
        {'friends':friends}
      )
      afterFinish()
    },
    onFailurer: function(req) { alert('Failed') }
  })
}

function add_service(name, target, format) {
  var m = msg('input target on name').replace(/target/, target).replace(/name/, name)
  var dialog = new Plnet.Dialog
  dialog.prompt(m, {
    'height': 100,
    'width': 340,
    'onOk': function() {
      $('dinput').disabled = true
      $('dbutton_ok').disabled = true
      $('dbutton_cancel').disabled = true
      $('dmsg').innerHTML = 
        '<img src="/images/loading.gif" class="icon" /> '+msg('adding...')
      new Ajax.Request('/api/foaf', {
        'method': 'post',
        'parameters':{
          'account':Plnet.Member.account,
          'url':format.replace(/##username##/, $F('dinput'))
        },
        'onComplete': function(req) {
          show_registered_services(function() {
            show_friends(function() {
              dialog.close()
            })
          })
        },
        'onFailurer': function(req) { alert('Failed') }
      })
    }
  })
}

function remove_service(foaf_id) {
  var dialog = new Plnet.Dialog
  dialog.confirm(msg('do you want to delete this service'), {
    'height': 80,
    'width': 340,
    'onOk': function() {
      $('dbutton_ok').disabled = true
      $('dbutton_cancel').disabled = true
      $('dmsg').innerHTML = 
        '<img src="/images/loading.gif" class="icon" /> '+msg('deleting...')
      new Ajax.Request('/api/foaf', {
        'method': 'delete',
        'parameters':{
          'account':Plnet.Member.account,
          'foaf_id':foaf_id
        },
        'onComplete': function(req) {
          show_registered_services(function() {
            show_friends(function() {
              dialog.close()
            })
          })
        },
        'onFailurer': function(req) { alert('Failed') }
      })
    }
  })
}

function register() {

}
