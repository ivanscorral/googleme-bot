<?php

include_once 'search.php';

class BotQuery{

  private $name;
  private $author;
  private $db;
  private $bot;
  private $body;
  private $query;
  private $resolved;
  private $usernames = 'ugoogleme-bot';
  private $newLine ='

  ';

  function __construct($name, $author, $body, $resolved){
    $this->db = new Database();
    $this->name = $name;
    $this->query = '';
    $this->author = $author;
    $this->body = strtolower($body);
    $this->resolved = $resolved;
  }

  function setBot($bot){
    $this->bot = $bot;
  }

  function resolve(){
    # query google api, respond to comment.
    $results = $this->getSearchResults();
    $commentString = "Here's your Google Search for '".$this->query."', /u/" . $this->author . ':'. $this->newLine;

    foreach ($results as $result) {
      $element = '- ['. $result->title . '](' . $result->url . ')'.$this->newLine.' > '. $result->description;
      $commentString = $commentString.$this->newLine.$element;
    }

    # call bot to comment with $commentString


    $response = $this->bot->comment($this->name, $commentString);

    if(sizeof($response->json->errors) > 0){
      echo 'error';
      echo json_encode($response->json->errors);
    }else{
      $this->db->setResolved($this->name);
    }

  }

  function getSearchResults(){
    $search_query = str_replace('/', '', $this->body);
    $search_query = str_replace($this->usernames, '', $search_query);
    $search = new GoogleSearch($search_query);
    $this->query = $search->getSearch();
    $results = $search->getSearchResults();
    return $results;
  }

  function isResolved(){
    return $this->resolved;
  }


}


 ?>
