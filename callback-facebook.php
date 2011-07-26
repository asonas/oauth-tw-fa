<?php
session_start();
require_once './lib/facebook.php';
require_once './config.php';


$facebook = new Facebook(array(
  'appId' => $config['facebook']['consumer_key'],
  'secret' => $config['facebook']['consumer_secret']
));

$facebook->api('/me/feed', 'POST', array('message' => $_SESSION['message']));

if( !empty($_SESSION['mixi']['auth_url']) ){
  echo '<a href="'.$_SESSION['mixi']['auth_url'].'">mixi</a>';
} elseif(!empty($_SESSION['twitter']['auth_url']) ){
  echo '<a href="'.$_SESSION['twitter']['auth_url'].'">mixi</a>';
}

