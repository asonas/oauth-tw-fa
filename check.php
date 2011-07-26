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

//失敗してたらフラグをたてて、成功したら折る
$auth_checked = array(
	'facebook' => array('status' => ''),
	'twitter' => array('status' => ''),
);
$_SESSION['message'] = $message = $data['message'];

//うまく書こう
if($data['facebook'] == 'true'){ //成功
	$_SESSION['facebook']['result'] = $auth_checked['facebook']['result'] =  postFacebook($message, $config);
} else { //チェックされてなかった
	$auth_checked['facebook']['status'] = 'disposted';
}

if ($data['twitter'] == 'true') {
	$_SESSION['twitter']['result'] = $auth_checked['twitter']['result'] = postTwitter($message, $config);
} else {
	$auth_checked['twitter']['status'] = 'disposted';
}

if(!empty($auth_checked['facebook']['status']) || $auth_checked['facebook']['status'] == 'disposted'){
	$_SESSION['facebook']['auth_url'] = $result['facebook']['auth_url'] = genAuthURL4Facebook($message, $config);
}

if(empty($auth_checked['twitter']['status']) || $auth_checked['twitter']['status'] == 'disposted'){
	$_SESSION['twitter']['auth_url'] = $result['twitter']['auth_url'] = genAuthURL4Twitter($config);
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
		array('status' => $mes)
	);
	return 'success';
}

function postFacebook($mes, $config){
	$facebook = new Facebook(array(
		'appId' => $config['facebook']['consumer_key'],
		'secret' => $config['facebook']['consumer_secret'],
	));
	$response = $facebook->api(array(
		'method' => 'stream.publish',
		'message' => $mes
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
