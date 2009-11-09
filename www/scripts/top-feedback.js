window.onload = function() {
  showFeedbacks();
  Event.observe('submit', 'click', addFeedback);

  validator = new Validator();
  validator.addRule('feedback_form_name');
  validator.addRule('feedback_form_comment');
  Field.focus('feedback_form_name');
  $('submit').disabled = true;
}

function showFeedbacks(page) {
  page = page || 1;
  var url = '/api/feedbacks?page='+page;
  Element.update('feedbacks', '<img src="/images/loading.gif" />');
  new Ajax.Request(url, {
    method: 'get',
    onComplete: function(req) {
      Element.update('feedbacks', '');
      var feedbacks = eval('('+req.responseText+')');
      feedbacks.data.each(function(feedback){
        var html = '<div class="feedback">';
        html += '<div class="name">'+feedback.name+':</div>';
        html += '<div class="comment">';
        html += feedback.comment.replace(/\n/g, '<br />')+'</div>';
        html += '</div>';
        new Insertion.Bottom('feedbacks', html);
      });
      showPager(
        parseInt(feedbacks.page),
        parseInt(feedbacks.perPage),
        parseInt(feedbacks.total)
      );
    }
  });
}

function showPager(page, perPage, total) {
  var prev = '<a href="javascript:showFeedbacks('+(page-1)+')">&lt; Prev</a> '
  var next = ' <a href="javascript:showFeedbacks('+(page+1)+')">Next &gt;</a>'
  var html = '';
  if (page > 1) {
    html += prev;
  }
  if (page * perPage < total) {
    html += next;
  }
  document.getElementsByClassName('pager').each(function(c){
    Element.update(c, html);
  });
}

function addFeedback() {
  postFeedback({
    'name'    : $('feedback_form_name').value,
    'comment' : $('feedback_form_comment').value
  });
}

function postFeedback(feedback) {
  var url = '/api/feedback?name='+feedback.name;
  url += '&comment='+feedback.comment;
  var comment = feedback.comment;

  $('submit').disabled = true;
  Element.update('loading', '<img src="/images/loading.gif" />');

  new Ajax.Request(url, {
    postBody: $H({name:feedback.name, comment:feedback.comment}).toQueryString(),
    onComplete: function() {
      showFeedbacks();
      Element.update('loading', '');
      $('feedback_form_name').value = '';
      $('feedback_form_comment').value = '';
      $('submit').disabled = false;
    }
  });
}
