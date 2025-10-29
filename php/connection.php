<?php
  function Secure($text){return htmlspecialchars((string)$text,ENT_QUOTES,"UTF-8");}

  function Connection(){

    $host = "localhost";
    $dbname = "bd_vehiculos";
    $user = "root";
    $pass = "";
    
    $connection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$pass);
    $connection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    return $connection;
  }
?>