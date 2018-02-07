<?php

class SearchResult{

  public $title;
  public $description;
  public $url;
  
  function __construct($title, $description, $url){
    $this->title = $title;
    $this->description = $description;
    $this->url = $url;
  }


}

?>
