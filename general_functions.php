<?php

function html_head() {
  echo '<!DOCTYPE html>
<html lang="de">
  <head>
    <meta charset="utf-8">
    <meta name="robots" content="noindex,nofollow">
    <meta name="author" content="Dennis Grebert">
    <title>Wettbewerbsauswertung</title>
    <link rel="stylesheet" href="main.css" type="text/css">
  </head>
  <body>';
//    <div class="logo"><img src="images/logo.gif" alt="Logo"></div>
//';
}

function html_foot() {
  echo '
  </body>
</html>';
}

function error_output() {
  global $error_output;
  if (sizeof($error_output) > 0) {
    return '<div class="error">FEHLER: '.$error_output.'</div>';
  }
  else return;
}

function get_bundeslaender($db) {
  if ($result = $db->query("SELECT * FROM bundesland ORDER BY name")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function get_bundesland($db,$id) {
  if ($result = $db->query("SELECT * FROM bundesland WHERE id='".$id."'")) {
    while ($line = $result->fetch_assoc()){
      return $line['name'];
    }
    return false;
  }
  else return false;
}

function button_back() {
  $output='<form action="index.php" method="POST" id="nothing">
  <input type="hidden" name="do" value="nothing">
  <input class="menubutton" type="submit" value="Zur&uuml;ck">
  </form>';
  return $output;
}

function get_system_url() {
  $url = $_SERVER['REQUEST_URI'];
  $parts = explode('/',$url);
  $dir = '';
  for ($i = 0; $i < count($parts) - 1; $i++) {
    $dir .= $parts[$i] . "/";
  }
  $system_url=(isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$dir";
  return $system_url;
}

?>
