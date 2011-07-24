<?php
/*
check.phpでつぶやけなかったら飛ばします！！
*/

session_start();
require_once('twitteroauth/twitteroauth.php');
require_once('config.php');

foreach($_REQUEST as $key=>$value){
	$data[$key] = $value;
}

if($data['twitter'] == '0'){
	return authTwitter();
}


function authTwitter(){
	$connection = new TwitterOAuth(
		$config['twitter']['consumer_key'],
		$config['twitter']['consumer_secret']);
 
	$request_token = $connection->getRequestToken(OAUTH_CALLBACK);

	$_SESSION['oauth_token'] = $token = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];

	switch ($connection->http_code) {
	  case 200:
	    /* Twitterに認証するリダイレクトURLを発行してるｙｐ */
 	   $url = $connection->getAuthorizeURL($token);
 	   return $url;
 	   //header('Location: ' . $url); 
 	   break;
 	 default:
  	  echo 'コンシューマートークンが違うです。';//
	}
}