<?php
/*
1．認証の確認
2．認証できなかったらURL発行
3，URLをハッシュにして返す
*/
ini_set('display_errors', 'On');
session_start();

require_once './lib/twitteroauth.php';
require_once './lib/facebook.php';
require_once './config.php';

foreach($_REQUEST as $key => $val){
	$data[$key] = $val; 
}

$_SESSION['message'] = $message = $data['message'];

//うまく書こう
if($data['facebook'] == 'true'){

	//$result['facebook']['result'] =  postFacebook($message, $config);
	$auth_checked['facebook']['status'] = '0';
	
}
if ($data['twitter'] == 'true') {
	$auth_checked['twitter']['status'] = postTwitter($message, $config);
}

if($auth_checked['facebook']['status'] == '0'){
	$result['facebook']['auth_url'] = genAuthURL4Facebook($config);
}
/*
if($auth_checked['twitter']['status'] == '0'){
	$result['twitter']['auth_url'] = genAuthURL4Twitter($config);
}
*/



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
		array('status' => $mes)
	);
	return $content;
}

function postFacebook($mes, $config){
Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYPEER] = false;
Facebook::$CURL_OPTS[CURLOPT_SSL_VERIFYHOST] = 2;

	$facebook = new Facebook(array(
		'appId' => $config['facebook']['consumer_key'],
		'secret' => $config['facebook']['consumer_secret'],
		'redirect_uri' => $config['facebook']['callback_url'],
	));
	$response = $facebook->api(array(
		'method' => 'stream.publish',
		'message' => 'ベースはよ買わんと死ぬ。'
	));
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

function genAuthURL4Facebook($config) {
	$facebook = new Facebook(array(
		'appId' => $config['facebook']['consumer_key'],
		'secret' => $config['facebook']['consumer_secret'],
		'redirect_uri' => $config['facebook']['callback_url'],
	));
	
	$user = $facebook->getUser();
	
	if ($user) {
		//
	} else {
		$url = $facebook->getLoginUrl(array(
			'canvas' => 1,
			'fbconnect' => 0,
			'scope' => 'status_update,publish_stream,manage_pages,offline_access',
			'redirect_uri' => $config['facebook']['callback_url'],
		));
		return $url;
	}
	
}
