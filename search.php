<?php

include_once 'searchresult.php';

error_reporting(E_ERROR | E_PARSE);


class GoogleSearch {

  private $search;
  private $url = 'https://www.google.com/search?lr=lang_en&cr=countryUS&as_qdr=all&tbs=lr%3Alang_1en%2Cctr%3AcountryUS&q=';
  private $user_agent = 'GoogleMeBot ALPHA';

  function __construct($search)
  {
    $this->search = str_replace(' ', '+', trim($search));
    $this->url = $this->url.$this->search;
  }

  function getSearchResults(){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $this->url);
    curl_setopt($ch, CURLOPT_USERAGENT, $this->user_agent);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    $result = curl_exec($ch);
    $doc = new DOMDocument();
    $doc->loadHTML($result);
    $node = $doc->getElementById('search');
    $search_result = null;
    $searches = $node->firstChild->firstChild->childNodes;

    foreach ($searches as $s) {
      $temp = $s->firstChild->firstChild->attributes->item(0)->value;
      $title = $s->firstChild->firstChild->nodeValue;
      $result_url = 'http://google.com/'. $temp;
      $search_span = $s->lastChild->childNodes;
      foreach($search_span as $search_here)
      {
        if($search_here->nodeName == 'span'){
          $result_description = $search_here->nodeValue;
          break;
        }else{
          $result_description = null;
        }

      }if($result_description != null){
        # its a proper link.

        $searchObj = new SearchResult($title, $result_description, $result_url);
        $search_result[] = $searchObj;
    }


    }
    return $search_result;
  }



}


 ?>
