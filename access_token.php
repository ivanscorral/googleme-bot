<?php

include_once 'database.php';

class AccessToken {

  private $access_token;
  private $client_id;
  private $client_secret;
  private $refresh_token;
  private $expiry;
  private $generated;

  function __construct($access_token, $refresh_token, $expiry, $generated){
    $this->access_token = $access_token;
    $this->refresh_token = $refresh_token;
    $this->expiry = $expiry;
    $this->client_id = trim(file_get_contents('auth/client_id'));
    $this->client_secret = trim(file_get_contents('auth/client_secret'));
    $this->generated = $generated;
  }

  function hasExpired(){
    $generated_time = strtotime(trim($this->generated));
    $expiry_time = $generated_time + ($this->expiry);
    $now = time();

    if($now > $expiry_time){
      return TRUE;
    }else{
      return FALSE;
    }
  }

  function get_access_token(){
    return $this->access_token;
  }

  function get_refresh_token(){
    return $this->refresh_token;
  }


  function refresh(){
    $user_pass = $this->client_id.':'.$this->client_secret;
    $base64 = base64_encode($user_pass);

    $url = 'https://www.reddit.com/api/v1/access_token';
    $data = array('grant_type' => 'refresh_token', 'refresh_token' => $this->refresh_token);

    $options = array(
    'http' => array(
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\nAuthorization: Basic ".$base64."\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
    );

    $context  = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    $result = json_decode($result);
    $date = date('YYYY-mm-dd HH:mm:ss', time());

    $refreshed_token = new AccessToken($result->access_token, $this->refresh_token, $result->expires_in, $date);
    $refreshed_token->store();
    return $refreshed_token;
  }

  function store(){
    $db = new Database();
    $db->insertAuth($this->access_token, $this->refresh_token, $this->expiry);
  }

}



?>
