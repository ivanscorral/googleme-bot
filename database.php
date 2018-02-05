<?php

include_once 'access_token.php';

class Database{

  private $msqli;

  function __construct(){
    $this->mysqli = new mysqli("", "", "", "");
    $this->create_auth_table();
  }

  function create_auth_table(){
    $query = file_get_contents('sql/create_auth_table.sql');
    if(!$this->mysqli->query($query)){
       echo "Falló la creación de la tabla: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
    }
  }

  function insertAuth($access_token, $refresh_token, $expiry){
    $query = strtr(file_get_contents('sql/insert_auth.sql'), array('replace1' => $access_token, 'replace2' => $refresh_token, 'replace3' => $expiry));
    if(!$this->mysqli->query($query)){
       echo "Falló la creación de la tabla: (" . $this->mysqli->errno . ") " . $this->mysqli->error;
    }
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
