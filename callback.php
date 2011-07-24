<?php
/*
リファラ見てどの認証が終わったかをチェックするといいかも

リファラ
facebook
mixi
twitter
それ以外はエラー
*/

ini_set('display_errors', 'On');
session_start();
require_once('./lib/twitteroauth.php');
require_once('config.php');

if(isset($_REQUEST['oauth_token']) && $_SESSION['oauth_token'] !== $_REQUEST['oauth_token']) {
  $_SESSION['oauth_status'] = 'oldtoken';
  //セッションがなかった場合にはセッションを一度クリアしてあげてからエラーを表示してあげよう。
  //セッションが切れました。こちらから再度お願いします的な
  //header('Location: ./clearsessions.php');
  
}


$connection = new TwitterOAuth(
	$config['twitter']['consumer_key'],
	$config['twitter']['consumer_secret'],
	$_SESSION['oauth_token'],
	$_SESSION['oauth_token_secret']
);

$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);

$_SESSION['access_token'] = $access_token;

unset($_SESSION['oauth_token']);
unset($_SESSION['oauth_token_secret']);

if (200 == $connection->http_code) {
  /* The user has been verified and the access tokens can be saved for future use */
  $_SESSION['status'] = 'verified';
  

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
		array('status' => $_SESSION['message'])
	);
		
	return $content;

  //header('Location: ./index.php');
} else {
  /* Save HTTP status for error dialog on connnect page.*/
  header('Location: ./clearsessions.php');
}