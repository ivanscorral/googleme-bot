<?php

include_once 'auth.php';

class Api{

  private $authorization;
  private $db;
  private $valid = false;
  private $api_url = 'https://oauth.reddit.com/';
  private $user_agent = 'GoogleMeBot/alpha';

  private $POST = 'post';
  private $GET = 'get';


  function __construct($token){
    if($token != null){
      $this->authorization = 'Authorization: bearer ' .$token->get_access_token();
      $this->valid = true;
      $this->ch = curl_init();
      $this->db = new Database();
      curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json', $this->authorization));
      curl_setopt($this->ch, CURLOPT_USERAGENT, $this->user_agent);
      curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }
  }

  function getMe(){
    $url = $this->api_url.'api/v1/me';
    return $this->executeQuery($url);
  }

  function getMentions(){
    $url = $this->api_url.'message/inbox';
    $messages = $this->executeQuery($url, $this->GET, null);

    $result = null;

    foreach ($messages->data->children as  $message) {
      $username = '/u/GoogleMe-Bot';
      $body = $message->data->body;
      if(!(strpos($body, $username) === false)){

        $result[] = $message;
      }
    }

    return $result;
  }

  function hasMail(){
    $me = $this->getMe();
    return $me->has_mail;
  }

  function resolveAll(){
    var_dump($this->db->getUnresolvedQueries());
  }

  function storeMentions(){
    $mentions = $this->getMentions();

    if($mentions != null){
      $nameArray;
      foreach ($mentions as $mention) {
        $mention = $mention->data;
        # check that the mention is a comment (should be but just a double check)
        if($mention->was_comment){
          # it is a comment, store in bd.
          $this->db->insertMention($mention->name, $mention->author, $mention->body, $mention->created_utc);
          # add name to array
          $nameArray[] = $mention->name;
        }
      }

      #convert array to comma separated string
      if($nameArray != NULL){
        $messageIds = implode(", ", $nameArray);
        $messageIds = array('id' => $messageIds);
        $this->setMessagesAsRead($messageIds);
      }
    }

  }

  //input parameter: fullnames (t1_xxxx) separated by commas

  function setMessagesAsRead($messageIds){
    $url = $this->api_url.'api/read_message';
    $parameters = $messageIds;
    return $this->executeQuery($url, $this->POST, $parameters);
  }

  private function executeQuery($url, $method, $parameters){
    $result = false;
    if($this->valid){
      if($method == 'get'){
        curl_setopt($this->ch, CURLOPT_URL, $url);
        $result = curl_exec($this->ch);
      }else if($method == 'post'){

        $options = array(
        'http' => array(
            'header'  => $this->authorization ."\r\n"."User-Agent: gmebot/0.1,alpha"."\r\n"."Content-Type: application/x-www-form-urlencoded",
            'method'  => 'POST',
            'content' => http_build_query($parameters)
        ));
        $context  = stream_context_create($options);
        $result = file_get_contents($url, false, $context);
      }


      $result = json_decode($result);
    }
    return $result;
  }

}

$a = new Api($token);
$a->resolveAll();


?>
