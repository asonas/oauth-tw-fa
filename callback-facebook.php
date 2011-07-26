<?php

require_once './lib/facebook.php';
require_once './config.php';
	$facebook = new Facebook(array(
		'appId' => $config['facebook']['consumer_key'],
		'secret' => $config['facebook']['consumer_secret'],
	));

$facebook->api('/me/feed', 'POST', array('message' => $_SESSION['message']));