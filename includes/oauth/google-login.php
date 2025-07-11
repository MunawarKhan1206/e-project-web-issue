<?php
require_once __DIR__.'/../../vendor/autoload.php';
$client = new Google_Client();
$client->setClientId('GOOGLE_CLIENT_ID');
$client->setClientSecret('GOOGLE_CLIENT_SECRET');
$client->setRedirectUri('http://localhost/e-project/includes/oauth/google-callback.php');
$client->addScope(['email','profile']);
header('Location: '.$client->createAuthUrl());
exit;
