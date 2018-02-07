<?php

class BotQuery{

  private $name;
  private $author;
  private $body;
  private $resolved;

  function __construct($name, $author, $body, $resolved){
    $this->name = $name;
    $this->author = $author;
    $this->body = $body;
    $this->resolved = $resolved;
  }

  function resolve(){
    # query google api, respond to comment.
  }

  function isResolved(){
    return $this->resolved;
  }


}


 ?>
