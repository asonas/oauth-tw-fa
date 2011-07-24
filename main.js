$(document).ready(function(){
  $('#submit').click(function(){
	  var facebook = $('#facebook').is(':checked');
	  var twitter = $('#twitter').is(':checked');
	  var message = $('#message').attr('value');
	  
	  var params = '?' + 'twitter=' + twitter + '&facebook=' + facebook + '&message=' + message;
	  check(params);
	  //alert(params);
  })
})

function check(params){
	var req = new XMLHttpRequest();
	req.open('get', './check.php' + params, false);
	req.send(null);
	
	var json = eval("("+req.response+")");
	//alert(json.twitter.auth_url);
	window.open(json.facebook.auth_url);
	
	//alert(req.response.twitter);
	if(req.response == 200){
		alert(req.resposeText);
	}
}