<?php 

session_start();
require_once 'php/config.php';

  if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
      header("location: index.php");
      exit;
  }

  $doc_id = 'htd-';
  $nur_id = 'htn-';
  $rec_id = 'htr-';

  //doc name
  $userinfo = $_SESSION["userinfo"];
  $name = ucwords($userinfo->name);

