Behavior.register({
    '#authtype_login' : {
        onclick : function() {
            $('login_btn').value = 'Login';
            Element.hide('verify');
        }
    },
    '#authtype_register' : {
        onclick : function() {
            $('login_btn').value = 'Register';
            Element.show('verify');
        }
    }
});