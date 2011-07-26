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
	
	if( typeof json != 'undefined' ){
		if(typeof json.facebook.auth_url != 'undefined' ){
			if( json.facebook.auth_url != null ){
				window.open(json.facebook.auth_url);
			}
		}
		if( typeof json.mixi.auth_url != 'undefined' ){
			if( json.mixi.auth_url != null ){
				window.open(json.mixi.auth_url);
			}
		}
		if( typeof json.twitter.auth_url != 'undefined' ){
			if( json.twitter.auth_url != null ){
				window.open(json.twitter.auth_url);
			}
		}	
	}
}