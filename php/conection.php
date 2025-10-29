<?php
  function Secure($text){return htmlspecialchars((string)$text,ENT_QUOTES,"UTF-8");}

  function Conection(){

    $host = "localhost";
    $dbname = "bd_vehiculos";
    $user = "root";
    $pass = "";
    
    $conection = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8",$user,$pass);
    $conection->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);

    return $conection;
  }
?>