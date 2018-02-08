<?php

class SearchResult{

  public $title;
  public $description;
  public $url;

  function __construct($title, $description, $url){
    $this->title = $title;
    $this->description = $description;
    $this->url = $this->getRedirectUrl(strtr($url, array('(' => '%28', ')' => '%29')));
  }

  function getRedirectUrl($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true); // Must be set to true so that PHP follows any "Location:" header
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $a = curl_exec($ch); // $a will contain all headers

    $url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL); // This is what you need, it will return you the last effective URL

    return $url;
  }


}

?>
