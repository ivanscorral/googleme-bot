<?php

include_once 'access_token.php';
include_once 'bot_query.php';


class Database{

  private $msqli;

  function __construct(){
    $this->mysqli = new mysqli("localhost", "root", "", "test");
    $this->create_auth_table();
    $this->create_mention_table();
  }

  function setResolved($name){
    $query = strtr(file_get_contents('sql/set_resolved.sql'), array('p1' => $name));
    $this->mysqli->query($query);
  }

  function getUnresolvedQueries(){
    $resultado = $this->mysqli->query(file_get_contents('sql/select_unresolved_queries.sql'));
    $queries = NULL;

    for ($i=0; $i < $resultado->num_rows; $i++) {
      $resultado->data_seek($i);
      $fila = $resultado->fetch_assoc();

      $name = $fila['name'];
      $author = $fila['author'];
      $body = $fila['body'];

      $queries[] = new BotQuery($name, $author, $body, false);
    }

    return $queries;
  }

  function create_auth_table(){
    $query = file_get_contents('sql/create_auth_table.sql');
    $this->mysqli->query($query);
  }

  function create_mention_table(){
    $query = file_get_contents('sql/create_mention_table.sql');
    if(!$this->mysqli->query($query)){
       echo "Falló la creación de la tabla: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
    }
  }

  function insertAuth($access_token, $refresh_token, $expiry){
    $query = strtr(file_get_contents('sql/insert_auth.sql'), array('replace1' => $access_token, 'replace2' => $refresh_token, 'replace3' => $expiry));
    if(!$this->mysqli->query($query)){
       echo 'Falló: ' . $query;
    }
  }

  function insertMention($name, $author, $body, $created_utc){
    $query = strtr(file_get_contents('sql/insert_mention.sql'), array('p1' => $name, 'p2' => $author, 'p3' => $body, 'p4' => $created_utc, 'p5' => 'false'));
    $this->mysqli->query($query);
  }

  function getLastToken(){
    $resultado = $this->mysqli->query('SELECT * FROM `auth` ORDER BY ID DESC LIMIT 1');
    if($resultado->num_rows > 0){
      $resultado->data_seek(0);
      $fila = $resultado->fetch_assoc();

      $access_token = $fila['access_token'];
      $refresh_token = $fila['refresh_token'];
      $expiry = $fila['expiry'];
      $generated = $fila['generated'];

      $token = new AccessToken($access_token, $refresh_token, $expiry, $generated, $this);
      return $token;
    }else{
      return NULL;
    }
  }

}

?>
