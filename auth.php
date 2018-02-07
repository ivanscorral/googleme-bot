<?php

include_once 'database.php';

$token = getAuthToken();

if($token == null){
  //No tokens in db, create the first one.
  if(isset($_GET['code'])){
      getAccessToken($_GET['code']);
  }else{
      printAuthUrl();
  }
}

function getAccessToken($oauth_code){
  $client_id = '';
  $client_secret = '';

  $user_pass = $client_id.':'.$client_secret;
  $base64 = base64_encode($user_pass);

  $url = 'https://www.reddit.com/api/v1/access_token';
  $data = array('grant_type' => 'authorization_code', 'code' => $oauth_code, 'redirect_uri' => 'http://localhost/reddit/googleme-bot/auth.php');

  $options = array(
  'http' => array(
      'header'  => "Content-Type: application/x-www-form-urlencoded\r\nAuthorization: Basic ".$base64."\r\n",
      'method'  => 'POST',
      'content' => http_build_query($data)
  )
  );
  $context  = stream_context_create($options);
  $result = file_get_contents($url, false, $context);

  if(strpos($result, 'error')){
    printAuthUrl();
    return NULL;
  }

  $database = new Database();

  $result = json_decode($result);


  $database->insertAuth($result->access_token, $result->refresh_token, $result->expires_in);
  header("Refresh:0; url=auth.php");
}

function printAuthUrl(){
  $client_id = '';
  $state = '';
  $url = 'https://www.reddit.com/api/v1/authorize?client_id='.$client_id.'&response_type=code&state='.$state.'&redirect_uri=http://localhost/reddit/googleme-bot/auth.php&duration=permanent&scope=privatemessages,identity';
  echo '<a href="'.$url.'">Autorizacion</a>';
}

function getAuthToken(){
  $db = new Database();
  $token = $db->getLastToken();

  //Renew token if expired, continue if not.
  if($token != NULL){
    if($token->hasExpired()){
      $token = $token->refresh();
    }else{
    }
  }

  return $token;
}


 ?>
