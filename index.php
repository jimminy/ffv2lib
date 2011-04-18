<?php
session_start();
require_once('ff_oauth/friendfeedv2.php');
require_once('config.php');

// Only necessary ifyou are using OAuth

//if (empty($_SESSION['access_token']) || empty($_SESSION['access_token']['oauth_token']) || empty($_SESSION['access_token']['oauth_token_secret'])) {
//    header('Location: ./clearsessions.php');
//}

if (isset($_SESSION['access_token'])){
    $access_token = $_SESSION['access_token'];
}
else {
    session_destroy();
}

$session= FriendFeed::FriendFeed_OAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token, UA);

$session->test($session);