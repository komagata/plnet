var Validator = Class.create();
Validator.prototype = {
  'field' : {},
  'initialize' : function(){},
  'add' : function(id, rules, handles) {
    var rules = rules || {'require' : true};
    var handles = handles || {};
    handles.onSuccess = handles.onSuccess || this.onSuccess;
    handles.onFailure = handles.onFailure || this.onFailure;

    // initialize
    this.field[id] = {};
    for(var key in rules) {
      this.field[id][key] = {};
      this.field[id][key]['rule'] = typeof(rules[key]) == 'function' 
        ? rules[key] : this[key];
      this.field[id][key]['stat'] = false;
      this.field[id][key]['handle'] = handles;
    }

    Event.observe(id, 'keyup', function() {

      for (var rule_name in this.field[id]) {
        var rsh = this.field[id][rule_name];

          if (rsh['rule']($F(id))) {
            rsh['handle']['onSuccess'](id, rule_name);
            this.setStat(id, rule_name, true);
            this.observeField(id, rule_name);
          } else {
            rsh['handle']['onFailure'](id, rule_name);
            this.setStat(id, rule_name, false);
            this.observeField(id, rule_name);
            break;
          }
      }
      this.observeForm();
    }.bind(this));
  },
  'observeForm' : function() {
    var res = true;
    for (var field_name in this.field) {
      for (var rule_name in this.field[field_name]) {
        if (!this.field[field_name][rule_name]['stat']) res = false;
      }
    }

    if (res) {
      this.onComplete();
    } else {
      this.onIncomplete()
    }
  },
  'getStat' : function(id, rule_name) {
    return this.field[id][rule_name]['stat'];
  },
  'setStat' : function(id, rule_name, stat) {
    this.field[id][rule_name]['stat'] = stat;
  },
  'observeField' : function(id, rule_name) {
    var res = true;
    for (var r_n in this.field[id]) {
      if (!this.field[id][r_n]['stat']) res = false;
    }
    this.observeForm();
  },
  'onSuccess' : function(id) {
      Element.update(id + '_info', 'o');
  },
  'onFailure' : function(id) {
      Element.update(id + '_info', 'x');
  },
  'onComplete' : function() {
      Element.update('submit_info', 'Complete!');
      $('submit').disabled = false;
  },
  'onIncomplete' : function() {
      Element.update('submit_info', 'Incomplete!');
      $('submit').disabled = true;
  },
  'require' : function(value) {
    if (value != '') {
      return true;
    } else {
      return false;
    }
  },
  'email' : function(value) {
    if (value.match(/[!#-9A-~]+@+[a-z0-9]+.+[!#-9A-~]/i)) {
      return true;
    } else {
      return false;
    }
  }
};
