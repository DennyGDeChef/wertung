<?php

/**
 * Legt den HTML-Head der HTML an
 */
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

/**
 * Legt das Ende der HTML an
 */
function html_foot() {
  echo '
  </body>
</html>';
}

/**
 * Generiert einen Fehler-Output
 */
function error_output() {
  global $error_output;
  if (strlen($error_output) > 0) {
    return '<div class="error">FEHLER: '.$error_output.'</div>';
  }
  else return;
}

/**
 * Läd alle Bundesländer, alphabetisch sortiert aus der Datenbank
 */
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

/**
 * Läd ein Bundesland mit einer bestimmten ID aus der Datenbank
 * @param int $id Bundesland-ID
 */
function get_bundesland($db,$id) {
  if ($result = $db->query("SELECT * FROM bundesland WHERE id='".$id."'")) {
    while ($line = $result->fetch_assoc()){
      return $line['name'];
    }
    return false;
  }
  else return false;
}

/**
 * Läd alle Landkreise alphabetisch sortiert aus der Datenbank
 */
function get_landkreise($db) {
  if ($result = $db->query("SELECT * FROM landkreis ORDER BY name")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

/**
 * Läd den Zusammenhang zwischen Bundesland und Landkreis aus der Datenbank
 * @param int $bundesland Bundesland-ID
 */
function get_landkreise_bundesland($db,$bundesland) {
  if ($result = $db->query("SELECT landkreis.* FROM landkreis LEFT JOIN bezirk on landkreis.bezirk=bezirk.id LEFT JOIN bundesland on bezirk.bundesland=bundesland.id WHERE bundesland.id='".$bundesland."' ORDER BY name")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

/**
 * Läd die Informationen eines Landkreises anhand seiner ID aus der Datenbank
 * @param int $id Landkreis-ID
 */
function get_landkreis($db,$id) {
  if ($result = $db->query("SELECT * FROM landkreis WHERE id='".$id."'")) {
    while ($line = $result->fetch_assoc()){
      return $line['name'];
    }
    return false;
  }
  else return false;
}

/**
 * Generiert einen Zurück-Button
 */
function button_back() {
  $output='<form action="index.php" method="POST" id="nothing">
  <input type="hidden" name="do" value="nothing">
  <input class="menubutton" type="submit" value="Zur&uuml;ck">
  </form>';
  return $output;
}

/**
 * Gibt die System-URL zurück
 */
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

/**
 * unknwn
 */
function js_post_function() {
  $output='function post(path, params, method) {
    method = method || "post";
    var form = document.createElement("form");
    form.setAttribute("method", method);
    form.setAttribute("action", path);
    for(var key in params) {
        if(params.hasOwnProperty(key)) {
            var hiddenField = document.createElement("input");
            hiddenField.setAttribute("type", "hidden");
            hiddenField.setAttribute("name", key);
            hiddenField.setAttribute("value", params[key]);
            form.appendChild(hiddenField);
        }
    }
    document.body.appendChild(form);
    form.submit();
  }
  ';
  return $output;
}

/**
 * Läd ein Bundesland anhand eines Landkreises
 * @param int $lkr Landkreis-ID
 */
function get_bundesland_from_landkreis($db,$lkr) {
  if ($result = $db->query("SELECT bundesland.* from bundesland LEFT JOIN bezirk on bundesland.id=bezirk.bundesland LEFT JOIN landkreis ON bezirk.id=landkreis.bezirk WHERE landkreis.id=".$lkr)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
  }
  else return false;
}

/**
 * Fügt den Dateinamen dem Download-Header an
 */
function download_head($filename,$filetype) {
  header('Content-Disposition: attachment; filename="'.$filename.'"');
  header("Content-Type: ".$filetype.";");
}

/**
 * Konvertiert ein Array zu einem CSV-String
 */
function make_csv($array) {
	for ($i=0;$i<sizeof($array);$i++) {
		$array[$i]='"'.$array[$i].'"';
	}
	$csv=implode(';',$array)."\n";
	return $csv;
}

/**
 * Konvertiert eine Uhrzeit in eine Zeitspanne in Sekunden
 */
function hmstosec($value) {
	return strtotime("1970-01-01 ".$value." UTC");
}

/**
 * Setzt eine Fake-Time?? 
 * unkwn
 */
function faketime($value) {
	return date("i:s:00",strtotime("1970-01-01 ".$value." UTC"));
}
?>
