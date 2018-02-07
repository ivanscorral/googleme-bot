<?php

error_reporting(E_ERROR | E_PARSE);


class GoogleSearch {

  private $search;
  private $url = 'https://www.google.es/search?q=';
  private $user_agent = 'GoogleMeBot ALPHA';

  function __construct($search)
  {
    $this->search = $search;
    $this->url = $this->url.$search;
  }

  function doSearch(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);
    echo $result;
    $doc = new DOMDocument();
    $doc->loadHTML($result);
    $node = $doc->getElementById('search');
    $searches = $node->firstChild->firstChild->childNodes;

    foreach ($searches as $result) {
      $temp = $result->firstChild->firstChild->attributes->item(0)->value;
      $result_url = 'http://google.com'. $temp;
      $search_span =$result->lastChild->childNodes;

      echo $result_url;

      foreach($search_span as $search_here)
      {
        if($search_here->nodeName == 'span'){
          echo '<br>';
          $result_description = $search_here->nodeValue;
        }

      }
      echo $result_description;

      echo '<br>--------------------------------<br>';
    }

    }



}

$b = new GoogleSearch("lamborghini+urus");
$b->doSearch();

 ?>
