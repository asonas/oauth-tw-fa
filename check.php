<?php
/*
1．認証の確認
2．認証できなかったらURL発行
3，URLをハッシュにして返す
*/
ini_set('display_errors', 'On');
session_start();

require_once './lib/twitteroauth.php';
require_once './config.php';

foreach($_REQUEST as $key => $val){
	$data[$key] = $val; 
}

$_SESSION['message'] = $message = $data['message'];


if($data['facebook'] == 'true'){

	//$result['facebook']['result'] =  postFacebook($mes);
	
} elseif ($data['twitter'] == 'true') {
	$auth_checked['twitter']['status'] = postTwitter($message, $config);

}

if($auth_checked['twitter']['status'] == '0'){
	$result['twitter']['auth_url'] = genAuthURL4Twitter($config);
}



echo json_encode($result);

//呼び出されたら
function postTwitter($mes, $config){

	if(empty($_SESSION['access_token'])
	|| empty($_SESSION['access_token']['oauth_token'])
	|| empty($_SESSION['access_token']['oauth_token_secret']))
	{
		$result = '0';//なにを入れるかは悩むよ
		return $result;
	}
	$access_token = $_SESSION['access_token'];
	
	$connection = new TwitterOAuth(
		$config['twitter']['consumer_key'],
		$config['twitter']['consumer_secret'],
		$access_token['oauth_token'],
		$access_token['oauth_token_secret']);
		
	$content = $connection->get('account/verify_credentials');

	$connection->post(
		'statuses/update',
		array('status' => 'Hello! Small World!!')
	);
	
	return $content;
}

function genAuthURL4Twitter($config){
	$connection = new TwitterOAuth(
		$config['twitter']['consumer_key'],
		$config['twitter']['consumer_secret']);
 
	$request_token = $connection->getRequestToken($config['twitter']['callback_url']);

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
  	  return 'コンシューマートークンが違うです';
	}	
}

