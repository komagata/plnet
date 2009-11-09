var Validator = Class.create();
Validator.prototype = {
  'form' : {},
  'elements' : {},
  'initialize' : function(){
  },
  'addRule' : function(element_name, rules) {
    var rules = rules || {'require' : true};
    var element = $(element_name);
    element.status = false;
    element.rules = {};
    element.observer = this['observeElement'];

    for (var rule_name in rules) {
      element.rules[rule_name] = {
        'status' : false,
        'rule' : typeof(rules[rule_name]) == 'function' ? rules[rule_name] : this[rule_name],
        'handle' : {
          'onSuccess' : this['onSuccess'],
          'onFailure' : this['onFailure']
        }
      };

      // event
      Event.observe(element_name, 'keyup', function() {
        var element = this.elements[element_name];
        for (var rule_name in element.rules) {
          var res = element.rules[rule_name]['rule'](element.value);

          if (element.verify == false) {
          } else if (res) {
            element.rules[rule_name]['handle']['onSuccess'](element.id);
            element.rules[rule_name]['status'] = true;
            element.observer(this);
          } else {
            element.rules[rule_name]['handle']['onFailure'](element.id);
            element.rules[rule_name]['status'] = false;
            element.observer(this);
            break;
          }
        }
      }.bind(this));
    }
    this.elements[element_name] = element;
  },
  'observeElement' : function(validator) {
    var res = true;
    var element = this;
    for (var rule_name in element.rules) {
      if (!element.rules[rule_name]['status']) res = false;
    }
    element.status = res;
    validator.observeForm();
  },
  'observeForm' : function() {
    var res = true;
    for (var element_name in this.elements) {
      if (this.elements[element_name]['status'] == false) res = false;
    }
    if (res) {
      this.onComplete();
    } else {
      this.onIncomplete();
    }
  },
  'onSuccess' : function(id) {
    Element.update(id + '_info', 'O');
  },
  'onFailure' : function(id) {
    Element.update(id + '_info', 'X');
  },
  'onComplete' : function(form_name, rule_name) {
    var form_name = 'submit';
    Element.update(form_name + '_info', 'Complete!');
    $(form_name).disabled = false;
  },
  'onIncomplete' : function(form_name, rule_name) {
    var form_name = 'submit';
    Element.update(form_name + '_info', 'Incomplete!');
    $(form_name).disabled = true;
  },
  'require' : function(value) {
    return value == '' ? false : true;
  },
  'email' : function(value) {
    if (value.match(/^[a-z0-9\-\._]+@[a-z0-9]([0-9a-z\-]*[a-z0-9]\.){1,}[a-z]{1,4}$/i)) {
      return true;
    } else {
      return false;
    }
  }
}

// validator default setting
Validator.prototype['onSuccess'] = function(id) {
  Element.update(id + '_info', '<img src="images/success.gif" style="vertical-align:middle" />');
};
Validator.prototype['onFailure'] = function(id) {
  Element.update(id + '_info', '<img src="images/failure.gif" style="vertical-align:middle" />');
};
Validator.prototype['onComplete'] = function() {
  $('submit').disabled = false;
  Element.update('submit_info', '<img src="images/success.gif" style="vertical-align:middle" />');
};
Validator.prototype['onIncomplete'] = function() {
  $('submit').disabled = true;
  Element.update('submit_info', '<img src="images/failure.gif" style="vertical-align:middle" />');
};
