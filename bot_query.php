<?php

include_once 'search.php';

class BotQuery{

  private $name;
  private $author;
  private $body;
  private $resolved;
  private $usernames = array('u/googleme-bot', '/u/googleme-bot', 'u/googleme-bot/', '/u/googleme-bot/');

  function __construct($name, $author, $body, $resolved){
    $this->name = $name;
    $this->author = $author;
    $this->body = strtolower($body);
    $this->resolved = $resolved;
  }

  function resolve(){
    # query google api, respond to comment.
    $results = $this->getSearchResults();
    $commentString = '';

    foreach ($results as $result) {
      $element = 'Title: '. $result->title . ', url: ' . $result->url . ', description: ' . $result->description;
      $commentString = $commentString.'<br>'.$element;
    }

    echo $commentString;
  }

  function getSearchResults(){
    $search_query = str_replace($this->usernames, '', $this->body);
    $search = new GoogleSearch($search_query);
    $results = $search->getSearchResults();
    return $results;
  }

  function isResolved(){
    return $this->resolved;
  }


}


 ?>
