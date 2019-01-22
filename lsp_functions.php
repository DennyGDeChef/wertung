<?php

function form_create_leistungsspange($db,$land=01) {
  $blr=get_bundeslaender($db);
  $lkr=get_landkreise_bundesland($db,$land);
  $output='<script>';
  $output.=js_post_function();
  $output.='</script>';
  $output.='<h1>Leistungsspangenabnahme anlegen</h1>
  <form action="index.php" method="POST" id="newlsp">
    <input type="hidden" name="do" value="createlsp">
    <table>
      <tr><th colspan="4">Abnahme</th></tr>
      <tr><th>Datum</th><th>Land</th><th>Kreis</th><th>Ort</th></tr>
      <tr><td><input type="date" name="datum" value="'.date('d.m.Y').'"></td>
        <td><select id="bundesland" name="bundesland" onChange="post('."'".'index.php'."'".',{screen:'."'".'addlsp'."'".',bundesland:this.value});">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['id'].'"';
		  if ($bl['id']==$land) $output.=' selected';
		  $output.='>';
          $output.=$bl['name'].'</option>';
          
        }
  $output.='
        </select></td>
        <td><select name="kreis">';
        foreach ($lkr as $lk) {
          $output.='<option value="'.$lk['id'].'">';
          $output.=$lk['name'].'</option>';
          
        }
		$output.='</select></td>
        <td><input type="text" name="ort"></td>
      </tr>
      <tr><th colspan="4">Abnahmeberechtigter</th></tr>
      <tr><th>Name</th><th>Vorname</th><th>Ort</th><th>Stempel</th></tr>
      <tr>
        <td><input type="text" name="ab_name"></td>
        <td><input type="text" name="ab_vorname"></td>
        <td><input type="text" name="ab_ort"></td>
        <td><input type="number" name="stempel" min="001" max="999"></td>
      </tr>
      <tr>
        <td><input type="submit" value="Anlegen"></td>
        <td>&nbsp;</td>
        <th>Mehrzweckfeld</th>
        <td><input type="number" name="mzf" min="00" max="99"></td>
      </tr>
    </table>
  </form>
  ';
  return $output;
}

function create_lsp($db,$datum,$land,$kreis,$ort,$ab_name,$ab_vorname,$ab_ort,$stempel,$mzf) {
  global $error_output;
  if (!(trim($datum=='')) && !(trim($land=='')) &&!(trim($kreis=='')) &&!(trim($ort=='')) &&!(trim($ab_name=='')) &&!(trim($ab_vorname=='')) &&!(trim($ab_ort=='')) &&!(trim($stempel==''))) {
    if ($id=new_lsp($db,date('Y-m-d',strtotime($datum)),$land,$kreis,$ort,$ab_name,$ab_vorname,$ab_ort,$stempel,$mzf))
    {
      select_lsp($id);
      return true;
    }
    return false;
  }
  $error_output="Das hat nicht geklappt, weil nicht alle Felder korrekt ausgef&uuml;llt wurden.";
  return false;
}

function new_lsp($db,$datum,$land,$kreis,$ort,$ab_name,$ab_vorname,$ab_ort,$stempel,$mzf) {
  global $error_output;
  $id = substr($datum,2,2).'.'.$land.str_pad($mzf,2,'0',STR_PAD_LEFT).'.'.str_pad($stempel,3,'0',STR_PAD_LEFT).'.'.substr($datum,5,2).substr($datum,8,2);
  if (strlen($id)!=16) {
    $error_output="Fehler beim Generieren der Veranstaltungs ID: ".$id;
    return false;
  }
  $query="INSERT leistungsspange SET id='".$id."', bundesland='".$land."', mzf='".$mzf."', stempel='".$stempel."', datum='".$datum."', ort='".$ort."', kreis='".$kreis."', ab_name='".$ab_name."', ab_vorname='".$ab_vorname."', ab_ort='".$ab_ort."', besitzer=".$_SESSION['_BENUTZER'];
  if ($result = $db->query($query)) {
    return $id;
  }
  $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  return false;
}

function select_lsp($lsp) {
  select_competition("lsp");
  if ($lsp=='null') {
    unset($_SESSION['LSP']);
    unset($_SESSION['WB']);
  }
  else {
    if (strlen($lsp)==16) {
      $_SESSION['LSP']=$lsp;
    }
    else {
      exit('Keine gueltige LSP!');
    }
  }
}

function get_lsps($db) {
  global $error_output;
  if (isset($_SESSION['_BENUTZER'])) {
	if (in_array(6,$_SESSION['_RECHTE']) || in_array(9,$_SESSION['_RECHTE'])) {
	  $ben=get_benutzer($db,$_SESSION['_BENUTZER']);
	  $bld=get_bundesland_from_landkreis($db,$ben['landkreis']);
	  $lkr=get_landkreise_bundesland($db,$bld['id']);
	  $lkr_id_list=array();
	  foreach ($lkr as $kr) array_push($lkr_id_list,$kr['id']);
	}
    $query="SELECT leistungsspange.*,bundesland.name as land, landkreis.name as kreis FROM leistungsspange LEFT JOIN bundesland on leistungsspange.bundesland=bundesland.id LEFT JOIN landkreis on leistungsspange.kreis=landkreis.id WHERE besitzer=".$_SESSION['_BENUTZER'];
    if (in_array(6,$_SESSION['_RECHTE'])) $query.=" OR leistungsspange.kreis IN (".implode(',',$lkr_id_list).")";
	  elseif (in_array(9,$_SESSION['_RECHTE'])) $query.=" OR leistungsspange.kreis=".$ben['landkreis'];
    $query.=" ORDER BY leistungsspange.datum DESC";
    if ($result = $db->query($query)) {
      $output=array();
      while ($line = $result->fetch_assoc()) {
        array_push($output,$line);
      }
      return $output;
    }
    return "(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  }
  else return false;
}

function get_lsp($db,$lsp) {
  if (isset($_SESSION['_BENUTZER'])) {
	$ben=get_benutzer($db,$_SESSION['_BENUTZER']);
    $query="SELECT leistungsspange.*,bundesland.name as land, landkreis.name as kreis  FROM leistungsspange LEFT JOIN bundesland on leistungsspange.bundesland=bundesland.id LEFT JOIN landkreis on leistungsspange.kreis=landkreis.id WHERE leistungsspange.id='".$lsp."'";
	if (!in_array(6,$_SESSION['_RECHTE'])) {
	  if (in_array(9,$_SESSION['_RECHTE'])) {
		$query.= " AND leistungsspange.kreis=".$ben['landkreis'];
	  }
	  else $query.=" AND besitzer=".$_SESSION['_BENUTZER'];
	}
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()) {
      return $line;
    }
    return false;
  }
  return false;
  }
  return false;
}

function get_lsp_groups($db,$abnahme,$sort) {
  $query="SELECT * FROM lsp_gruppe WHERE abnahme='".$abnahme."'";
  if ($result = $db->query($query)) {
    $output=array();
    while($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  return false;
}

function get_lsp_group_count($db,$abnahme) {
  $query="SELECT count(id) AS count FROM lsp_gruppe WHERE abnahme='".$abnahme."'";
  if ($result = $db->query($query)) {
    $output=$result->fetch_array()[0];
    return $output;
  }
  return false;
}

function get_lsp_groups_by_token($db,$token,$sort) {
  $query="SELECT * FROM lsp_gruppe WHERE token='".$token."'";
  if ($result = $db->query($query)) {
    $output=array();
    while($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  return false;
}


function get_lsp_group($db,$abnahme,$grp) {
  $query="SELECT * FROM lsp_gruppe WHERE abnahme='".$abnahme."' AND id='".$grp."'";
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}

function get_lsp_tokens($db,$abnahme) {
  $query="SELECT t.*, count(g.id) as mannschaften FROM lsp_token AS t LEFT JOIN lsp_gruppe AS g ON t.id=g.token WHERE t.abnahme='".$abnahme."' group by t.id";
  if ($result = $db->query($query)) {
    $output=array();
    while($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  return false;
}

function get_lsp_token($db,$token) {
  $query="SELECT * FROM lsp_token WHERE token='".$token."'";
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}

function get_lsp_id_token($db,$abnahme,$id) {
  $query="SELECT * FROM lsp_token WHERE id='".$id."' AND abnahme='".$abnahme."'";
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}

function get_lsp_candidate_count($db,$abnahme) {
  $query="SELECT g.id as grp,sum(if(t.geschlecht='w',1,0)) as bewerber_w, sum(if(t.geschlecht='m',1,0)) as bewerber_m FROM `lsp_gruppe` AS g JOIN lsp_teilnehmer AS t ON g.abnahme=t.abnahme AND g.id = t.gruppe WHERE g.abnahme='18.0601.009.0915' AND t.bewerber='X' GROUP BY g.id";
  if ($result = $db->query($query)) {
    $output=array();
    while($line = $result->fetch_assoc()){
      $output[(int)$line['grp']]=array('w'=>$line['bewerber_w'],'m'=>$line['bewerber_m']);
    }
    return $output;
  }
  return false;
}

function getNewToken($length){
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet.= "0123456789";
    $max = strlen($codeAlphabet); // edited
    for ($i=0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max-1)];
    }
    return $token;
}

function new_lsp_token($db,$abnahme,$email) {
  global $error_output;
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_output = "Ungültiges E-Mail Format";
    return false;
  }
  $token = getNewToken(20);
  $query="INSERT lsp_token SET abnahme='".$abnahme."', email='".$email."', token='".$token."'";
  if (!($db->query($query))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  }
  return true;
}

function get_max_lsp_startnummer($db,$lsp) {
  global $error_output;
  if (!($result=$db->query("SELECT max(startnummer) as max FROM lsp_gruppe WHERE abnahme='".$lsp."'"))) {
    $error_output="Datenbankfehler: " . $db->error;
  }
  $maxid=$result->fetch_row()[0];
  if ($maxid==NULL) return 0;
  return($maxid);
}

function get_max_lsp_group_id($db,$lsp) {
  global $error_output;
  if (!($result=$db->query("SELECT max(id) as max FROM lsp_gruppe WHERE abnahme='".$lsp."'"))) {
    $error_output="Datenbankfehler: " . $db->error;
  }
  $maxid=$result->fetch_row()[0];
  if ($maxid==NULL) return 0;
  return($maxid);
}

function form_select_lsp($db) {
  if ($lsps = get_lsps($db)) {
    $output='<h1>Leistungsspangenabnahme ausw&auml;hlen</h1>';
    $output.='<table class="wbselecttable"><tr><th>Datum</th><th>Land</th><th>Kreis</th><th>Ort</th><th>Vorname</th><th>Name</th></tr>';
    foreach ($lsps as $lsp) {
      $output.='<tr><td>';
      $output.='<form action="index.php" method="POST" id="selectlsp'.$lsp['id'].'">';
      $output.='<input type="hidden" name="do" value="selectlsp">';
      $output.='<input type="hidden" name="lsp" value="'.$lsp['id'].'">';
      $output.='<input class="selectwb" type="submit" value="'.date('d.m.Y',strtotime($lsp['datum'])).'">';
      $output.='</form>';     
      $output.="</td><td>".$lsp['land']."</td><td>".$lsp['kreis']."</td><td>".$lsp['ort']."</td><td>".$lsp['ab_vorname']."</td><td>".$lsp['ab_name']."</td>";
      $output.='</td></tr>';
    }
    $output.='</table>';
    return $output;
  }
  return false;
}

function button_deselect_lsp() {
  $output='<form action="index.php" method="POST" id="deselectlsp">
  <input type="hidden" name="do" value="selectlsp">
  <input type="hidden" name="lsp" value="null">
  <input class="menubutton" type="submit" value="Abnahme ausw&auml;hlen"></form>';
  return $output;
}

function get_cls($field,$value,$option) {
//  echo $field.'/'.$value.'/'.$option;
  switch($field) {
    case "bwX":
      $age=substr($option,0,4)-substr($value,0,4);
      if ($age >= 15 && $age <= 18) return "bw";
      break;
    case "bwL":
      $age=substr($option,0,4)-substr($value,0,4);
      if ($age > 15 && $age <= 18) return "bw";
      break;
    case "bw":
      return "bw";
      break;
    case "dt":
      $age=substr($option,0,4)-substr($value,0,4);
      if ($age > 10 && $age <= 18) return "dt";
      break;
    case "aw":
      if ($value!='000000' && strlen($value)==6) return "aw";
      break;
  }
  return "error";
}

function form_show_lsp_groups($db,$lspid,$sort) {
  global $error_output;
  if ($lsp=get_lsp($db,$lspid)) {
    $output='<h1>Leistungsspange der DJF</h1>';
    $output.='<h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>';
    if ($grps=get_lsp_groups($db,$lspid,$sort)) {
      $output.='<table>
        <tr><th><a href="index.php?sort=startnummer">Start Nr</a></th><th>Name</th><th>Land</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>';
      foreach ($grps as $grp) {
        $output.='<tr><td>'.$grp['startnummer'].'</td>
          <td><form action="index.php" method="POST" id="editlspgrp"><input type="hidden" name="do" value="editlspgrp"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="shwedtgrp" type="submit" value="'.$grp['name'].'"></form></td>
          <td>'.get_bundesland($db,$grp['bundesland']).'</td><td>'.$grp['bezirk'].'</td><td>'.$grp['kreis'].'</td><td>'.$grp['ort'].'</td>
          <td><form action="index.php" method="POST" id="editlspgrpmember">
            <input type="hidden" name="do" value="editlspgrpmembers"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="edtlspgrpmbr" type="submit" value="Teilnehmer">
          </form></td>
          <td><form action="index.php" method="POST" id="ratelspgrp">
            <input type="hidden" name="do" value="ratelspgrp"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="rtglspgrp" type="submit" value="Wertung">
          </form></td>
          </tr>';
      }
      $output.='</table>';
    }
    return $output;
  }
  $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  return false;
}

function button_create_lsp_group() {
  $output='<form action="index.php" method="POST" id="addlspgrp">
  <input type="hidden" name="do" value="addlspgrp">
  <input class="menubutton" type="submit" value="Neue Gruppe"></form>';
  return $output;
}

function form_create_lsp_group($db,$lspid) {
  $lsp=get_lsp($db,$lspid);
  $blr=get_bundeslaender($db);
  $output='<h1>Neue Gruppe anlegen</h1>
  <h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>
  <form action="index.php" method="POST" id="addlspgrp">
    <input type="hidden" name="do" value="insertlspgrp">
    <table>
      <tr><th>Start Nummer</th><th>Name</th><th>Bundesland</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>
      <tr><td><input name="startnummer" value="'.(get_max_lsp_startnummer($db,$_SESSION['LSP'])+1).'"></td>
        <td><input name="name"></td>
        <td><select name="bundesland">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['id'].'"';
          if ($lsp['bundesland']==$bl['id']) $output.=' selected';
          $output.='>'.$bl['name'].'</option>';
        }
  $output.='
        </select></td>
        <td><input name="bezirk"></td>
        <td><input name="kreis"></td>
        <td><input name="ort"></td>
      </tr>
      <tr><td><input class="button" type="submit" value="OK"></td></tr>  
    </table>
  </form>';
  return $output;
}

function insert_lsp_group($db) {
  global $error_output;
  if (!(trim($_POST['name'])=='') && !(trim($_POST['bezirk'])=='') && !(trim($_POST['kreis'])=='') && !(trim($_POST['ort'])=='')) {
    $lsp=get_lsp($db,$_SESSION['LSP']);
    $nextid=get_max_lsp_group_id($db,$_SESSION['LSP'])+1;
    $query="INSERT lsp_gruppe SET abnahme='".$_SESSION['LSP']."', id='".$nextid."', startnummer=".$_POST['startnummer'].", name='".$_POST['name']."', bundesland='".$_POST['bundesland']."', bezirk='".$_POST['bezirk']."', kreis='".$_POST['kreis']."', ort='".$_POST['ort']."'";
    if (!($db->query($query))) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    }
  } else {
    $error_output='Alle Felder m&uuml;ssen ausgef&uuml;llt werden!';
  }
}

function form_edit_lsp_group($db,$abnahme,$gid) {
  $lsp=get_lsp_group($db,$abnahme,$gid);
  $blr=get_bundeslaender($db);
  $output='<h1>'.$lsp['name'].' editieren</h1>
  <form action="index.php" method="POST" id="editgrp">
    <input type="hidden" name="do" value="modifylspgrp">
    <input type="hidden" name="id" value="'.$lsp['id'].'">
    <table>
      <tr><th>Start Nummer</th><th>Name</th><th>Typ</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>
      <tr>
        <td><input name="startnummer" value="'.$lsp['startnummer'].'"></td>
        <td><input name="name" value="'.$lsp['name'].'"></td>
        <td><select name="bundesland">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['id'].'"';
          if ($lsp['bundesland']==$bl['id']) $output.=' selected';
          $output.='>'.$bl['name'].'</option>';
        }
        $output.='
        </select></td>
        <td><input name="bezirk" value="'.$lsp['bezirk'].'"></td>
        <td><input name="kreis" value="'.$lsp['kreis'].'"></td>
        <td><input name="ort" value="'.$lsp['ort'].'"></td>
      </tr>
      <tr> <td><input class="button" type="submit" value="OK"></td></tr>
      </table>
  </form>';
  return $output;
}

function modify_lsp_group($db,$abnahme,$id) {
  global $error_output;
  $query="UPDATE lsp_gruppe SET startnummer=".((int)$_POST['startnummer']).", name='".$_POST['name']."', bundesland=".((int)$_POST['bundesland']).", bezirk='".$_POST['bezirk']."', kreis='".$_POST['kreis']."', ort='".$_POST['ort']."' WHERE abnahme='".$abnahme."' AND id='".$id."'";
  if (!(trim($_POST['name'])=='') && !(trim($_POST['bezirk'])=='') && !(trim($_POST['kreis'])=='') && !(trim($_POST['ort'])=='')) {
    if (!($db->query($query) === TRUE)) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      return false;
    }
    return true;
  }
}

function form_edit_lsp_group_members($db,$abnahme,$gid) {
  $lsp=get_lsp($db,$abnahme);
  $grp=get_lsp_group($db,$abnahme,$gid);
  $lspmembers=get_lsp_group_members($db,$abnahme,$gid);
  $blr=get_bundeslaender($db);
  $groupsize=9;
  $spare=2;
  $teamsize=$groupsize+$spare;
  $output='<h1>Teilnehmermeldung '.$grp['name'].'</h1>
    <form action="index.php" method="POST" id="editlspgrpmembers">
      <input type="hidden" name="do" value="modifylspgrpmembers">
      <input type="hidden" name="id" value="'.$grp['id'].'">
      <table class="lspgrpmbs">
        <tr><th>Einsatz</th><th>Position</th><th>Bewerber</th><th>Name</th><th>Vorname</th><th>Geburtsdatum</th><th>Eintritt</th><th>Ausweis-Nr.</th><th>Geschlecht</th><th>Ausl&auml;nder</th><th>Bundesland</th></tr>';
        if (count($lspmembers)>0) {
          foreach($lspmembers as $lspmbr) {
          $output.='<tr>
          <td><input type="hidden" name="neu[]" value="off"><input class="e" type="checkbox" name="einsatz['.($lspmbr['position']-1).']"';
          if ($lspmbr['einsatz']>0) $output.=' checked';
          $output.='></td>
          <td><input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$lspmbr['position'].'"></td>
          <td><select class="'.get_cls("bw".$lspmbr['bewerber'],date('Y-m-d',strtotime($lspmbr['geburtstag'])),$lsp['datum']).'" name="bewerber[]"><option value="">&nbsp;</option><option value="X"';
          if ($lspmbr['bewerber']=='X') $output.=' selected';
          $output.='>X</option><option value="L"';
          if ($lspmbr['bewerber']=='L') $output.=' selected';
          $output.='>L</option></select></td>
          <td><input class="sn" type="text" name="name[]" value="'.$lspmbr['name'].'"></td>
          <td><input class="gn" type="text" name="vorname[]" value="'.$lspmbr['vorname'].'"></td>
          <td><input class="'.get_cls("dt",date('Y-m-d',strtotime($lspmbr['geburtstag'])),$lsp['datum']).'" type="date" name="geburt[]" value="'.date('Y-m-d',strtotime($lspmbr['geburtstag'])).'"></td>
          <td><input class="et" type="date" name="eintritt[]" value="'.date('Y-m-d',strtotime($lspmbr['eintritt'])).'"></td>
          <td><input class="'.get_cls("aw",$lspmbr['ausweisnr'],null).'" type="text" name="ausweis[]" size="6" minlength="6" maxlength="6" value="'.$lspmbr['ausweisnr'].'"></td>
          <td><select class="gs" name="geschlecht[]"><option value="m"';
          if ($lspmbr['geschlecht']=='m') $output.=' selected';
          $output.='>M&auml;nnlich</option><option value="w"';
          if ($lspmbr['geschlecht']=='w') $output.=' selected';
          $output.= '>Weiblich</option></select></td>
          <td><input class="al" type="checkbox" name="auslaender[]"';
          if ($lspmbr['auslaender']>0) $output.=' checked';
          $output.='></td>
          <td><select class="bl" name="bundesland[]">';
            foreach ($blr as $bl) {
              $output.='<option value="'.$bl['id'].'"';
              if ($bl['id']==$lspmbr['bundesland']) $output.=' selected';
              $output.='>';
              $output.=$bl['name'].'</option>';
            }
            $output.='
          </select></td>
          </tr>';
          }
        }
        for ($i=count($lspmembers)+1;$i<=$teamsize;$i++) {
          $output.='<tr>
          <td><input type="hidden" name="neu[]" value="on"><input class="e" type="checkbox" name="einsatz['.($i-1).']"';
          if ($i<=$groupsize) $output.=' checked';
          $output.='></td>
          <td><input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$i.'"></td>
          <td><select class="bw" name="bewerber[]"><option value="">&nbsp;</option><option value="X">X</option><option value="L">L</option></select></td>
          <td><input class="sn" type="text" name="name[]" value=""></td>
          <td><input class="gn" type="text" name="vorname[]" value=""></td>
          <td><input class="dt" type="date" name="geburt[]" value=""></td>
          <td><input class="et" type="date" name="eintritt[]" value=""></td>
          <td><input class="aw" type="text" name="ausweis[]" size="6" minlength="6" maxlength="6" value=""></td>
          <td><select class="gs" name="geschlecht[]"><option value="m">M&auml;nnlich</option><option value="w">Weiblich</option></select></td>
          <td><input class="al" type="checkbox" name="auslaender[]"></td>
          <td><select class="bl" name="bundesland[]">';
            foreach ($blr as $bl) {
              $output.='<option value="'.$bl['id'].'"';
              if ($bl['id']==$lsp['bundesland']) $output.=' selected';
              $output.='>';
              $output.=$bl['name'].'</option>';
            }
            $output.='
          </select></td>
          </tr>';
        }
        $output.='<tr><td><input class="button" type="submit" value="OK"></td></tr>
          </table></form>';
  return $output;
}

function get_lsp_group_members($db,$abnahme,$gid) {
  if ($result = $db->query("SELECT * FROM lsp_teilnehmer WHERE abnahme='".$abnahme."' AND gruppe=".$gid." ORDER BY position ASC")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function modify_lsp_group_members($db,$abnahme,$gid) {
  global $error_output;
  $lsp=get_lsp($db,$abnahme);
  $grp=get_lsp_group($db,$abnahme,$gid);
  $blr=get_bundeslaender($db);
  $groupsize=9;
  $spare=2;
  $teamsize=$groupsize+$spare;
  for ($i=0;$i<$teamsize;$i++) {
    if ($_POST['einsatz'][$i]=='on') $einsatz=1; else $einsatz=0;
    if ($_POST['auslaender'][$i]=='on') $auslaender=1; else $auslaender=0;
    if ($_POST['neu'][$i]=='off') {
      $query="UPDATE lsp_teilnehmer SET einsatz='".$einsatz."',"
                                      ."position='".$_POST['position'][$i]."',"
                                      ."bewerber='".$_POST['bewerber'][$i]."',"
                                      ."name='".$_POST['name'][$i]."',"
                                      ."vorname='".$_POST['vorname'][$i]."',"
                                      ."geburtstag='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."',"
                                      ."eintritt='".date('Y-m-d',strtotime($_POST['eintritt'][$i]))."',"
                                      ."ausweisnr='".$_POST['ausweis'][$i]."',"
                                      ."geschlecht='".$_POST['geschlecht'][$i]."',"
                                      ."auslaender='".$auslaender."',"
                                      ."bundesland='".$_POST['bundesland'][$i]."'"
                                      ." WHERE abnahme='".$abnahme."' AND gruppe='".$gid."' AND position='".$_POST['position'][$i]."'";
    } else {
      if ($_POST['ausweis'][$i] != '')
      $query="INSERT lsp_teilnehmer SET abnahme='".$abnahme."',"
                                      ."gruppe='".$gid."',"
                                      ."einsatz='".$einsatz."',"
                                      ."position='".$_POST['position'][$i]."',"
                                      ."bewerber='".$_POST['bewerber'][$i]."',"
                                      ."name='".$_POST['name'][$i]."',"
                                      ."vorname='".$_POST['vorname'][$i]."',"
                                      ."geburtstag='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."',"
                                      ."eintritt='".date('Y-m-d',strtotime($_POST['eintritt'][$i]))."',"
                                      ."ausweisnr='".$_POST['ausweis'][$i]."',"
                                      ."geschlecht='".$_POST['geschlecht'][$i]."',"
                                      ."auslaender='".$auslaender."',"
                                      ."bundesland='".$_POST['bundesland'][$i]."'";
      else $query='';
    }
    if (!$query=='') {
      if (!($db->query($query))) {
        $error_output.="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      }
    }
  }
  unset($_POST);
}

function button_delete_lsp_group($id) {
  $output.='<form action="index.php" method="POST" id="dellspgrp">
  <input type="hidden" name="do" value="removelspgrp">
  <input type="hidden" name="removeid" value="'.$id.'">
  <input class="menubutton" type="submit" value="!! Gruppe L&ouml;schen !!" onClick="return confirm('."'Sicher?'".')">
  </form>';
  return $output;
}

function remove_lsp_group($db,$abnahme,$id) {
  global $error_output;
  $continue=false;
  if (!($result=$db->query("DELETE FROM lsp_wertung WHERE abnahme='".$abnahme."' AND gruppe='".$id."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  if (!($result=$db->query("DELETE FROM lsp_teilnehmer WHERE abnahme='".$abnahme."' AND gruppe='".$id."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  if (!($result=$db->query("DELETE FROM lsp_gruppe WHERE abnahme='".$abnahme."' AND id='".$id."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  return true;
 
}

function get_lsp_rating($db,$abnahme,$gruppe) {
  $query="SELECT * FROM lsp_wertung WHERE abnahme='".$abnahme."' AND gruppe='".$gruppe."'";
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
  }
  return false;
}

function get_points_fastness($sekunden) {
  if (!is_numeric($sekunden)) return false;
  if ($sekunden <=0 ) return false;
  if ($sekunden > 75) return 0;
  if ($sekunden > 65) return 1;
  if ($sekunden > 60) return 2;
  if ($sekunden > 55) return 3;
  return 4;
}

function get_points_shot_put($meter) {
  if (!is_numeric($meter)) return false;
  if ($meter <  0 ) return false;
  if ($meter <  55) return 0;
  if ($meter <= 59) return 1;
  if ($meter <= 64) return 2;
  if ($meter <= 70) return 3;
  return 4;
}

function get_points_relay($sekunden) {
  if (!is_numeric($sekunden)) return false;
  if ($sekunden <=  0) return false;
  if ($sekunden > 250) return 0;
  if ($sekunden > 235) return 1;
  if ($sekunden > 220) return 2;
  if ($sekunden > 205) return 3;
  return 4;
}

function form_rate_lsp_group($db,$abnahme,$gid) {
  $lsp=get_lsp($db,$abnahme);
  $grp=get_lsp_group($db,$abnahme,$gid);
  $lsp_rating=get_lsp_rating($db,$abnahme,$gid);
  $output.= '<h1>Wertung '.$grp['name'].'</h1>
    <form action="index.php" method="POST" id="ratelspgrp">
      <input type="hidden" name="do" value="modifylsprate">
      <input type="hidden" name="gruppe" value="'.$grp['id'].'">
      <input type="hidden" name="neu" value="'.(!$lsp_rating ? 'on' : 'off').'">
     <table>';
  if (!$lsp_rating) {
            $lsp_rating=array(
              'abnahme'=> $abnahme,
              'gruppe'=> $gid,
              'schnelligkeit_zeit'       => 0,
              'schnelligkeit_eindruck'   => 0,
              'schnelligkeit_gueltig'    => 1,
              'schnelligkeit_zeit2'      => 0,
              'schnelligkeit_eindruck2'  => 0,
              'kugel_weite'              => 0,
              'kugel_eindruck'           => 0,
              'kugel_gueltig'            => 1,
              'kugel_weite2'             => 0,
              'kugel_eindruck2'          => 0,
              'staffel_zeit'             => 0,
              'staffel_eindruck'         => 0,
              'staffel_gueltig'          => 1,
              'staffel_zeit2'            => 0,
              'staffel_eindruck2'        => 0,
              'loeschangriff_punkte'     => 0,
              'loeschangriff_eindruck'   => 0,
              'fragen_punkte'            => 0,
              'fragen_eindruck'          => 0,
            );
  }
  else {
    $lsp_rating['schnelligkeit_zeit']=strtotime("1970-01-01 ".$lsp_rating['schnelligkeit_zeit']." UTC");
    $lsp_rating['schnelligkeit_zeit2']=strtotime("1970-01-01 ".$lsp_rating['schnelligkeit_zeit2']." UTC");
    $lsp_rating['staffel_zeit']=strtotime("1970-01-01 ".$lsp_rating['staffel_zeit']." UTC");
    $lsp_rating['staffel_zeit2']=strtotime("1970-01-01 ".$lsp_rating['staffel_zeit2']." UTC");
  }
  $gesamteindruck = round((
                    $lsp_rating['schnelligkeit_eindruck'] +
                    $lsp_rating['kugel_eindruck'] + 
                    $lsp_rating['staffel_eindruck'] + 
                    $lsp_rating['loeschangriff_eindruck'] + 
                    $lsp_rating['fragen_eindruck'])/5);
  $gesamtpunkte = get_points_fastness($lsp_rating['schnelligkeit_zeit']) + 
                  get_points_shot_put($lsp_rating['kugel_weite']) + 
                  get_points_relay($lsp_rating['staffel_zeit']) + 
                  $lsp_rating['loeschangriff_punkte'] + 
                  $lsp_rating['fragen_punkte'] + 
                  $gesamteindruck;
  if (get_points_fastness($lsp_rating['schnelligkeit_zeit'])==0 &&
            get_points_shot_put($lsp_rating['kugel_weite'])>0 &&
            get_points_relay($lsp_rating['staffel_zeit'])>0 &&
            $lsp_rating['loeschangriff_punkte']>0 &&
            $lsp_rating['fragen_punkte']>0 &&
            $gesamtpunkte >= 10) $wdh_schnelligkeit=true;
    else $wdh_schnelligkeit=false;
  if (get_points_shot_put($lsp_rating['kugel_weite'])==0 &&
            get_points_fastness($lsp_rating['schnelligkeit_zeit'])>0 &&
            get_points_relay($lsp_rating['staffel_zeit'])>0 &&
            $lsp_rating['loeschangriff_punkte']>0 &&
            $lsp_rating['fragen_punkte']>0 &&
            $gesamtpunkte >= 10) $wdh_kugel=true;
    else $wdh_kugel=false;
  if (get_points_relay($lsp_rating['staffel_zeit'])==0 &&
            get_points_fastness($lsp_rating['schnelligkeit_zeit'])>0 &&
            get_points_shot_put($lsp_rating['kugel_weite'])>0 &&
            $lsp_rating['loeschangriff_punkte']>0 &&
            $lsp_rating['fragen_punkte']>0 &&
            $gesamtpunkte >= 10) $wdh_staffel=true;
    else $wdh_staffel=false;
  ($lsp_rating['schnelligkeit_gueltig']==2 || $lsp_rating['kugel_gueltig']==2 || $lsp_rating['staffel_gueltig']==2)?$lock=true:$lock=false;
  $gesamteindruck = round((
                    ($lsp_rating['schnelligkeit_gueltig']>1?$lsp_rating['schnelligkeit_eindruck2']:$lsp_rating['schnelligkeit_eindruck']) +
                    ($lsp_rating['kugel_gueltig']>1?$lsp_rating['kugel_eindruck2']:$lsp_rating['kugel_eindruck']) +
                    ($lsp_rating['staffel_gueltig']>1?$lsp_rating['staffel_eindruck2']:$lsp_rating['staffel_eindruck']) +
                    ($lsp_rating['loeschangriff_eindruck']) +
                    ($lsp_rating['fragen_eindruck']))
                    /5);
  $gesamtpunkte = ($lsp_rating['schnelligkeit_gueltig']>1?get_points_fastness($lsp_rating['schnelligkeit_zeit2']):get_points_fastness($lsp_rating['schnelligkeit_zeit'])) +
                  ($lsp_rating['kugel_gueltig']>1?get_points_shot_put($lsp_rating['kugel_weite2']):get_points_shot_put($lsp_rating['kugel_weite'])) +
                  ($lsp_rating['staffel_gueltig']>1?get_points_relay($lsp_rating['staffel_zeit2']):get_points_relay($lsp_rating['staffel_zeit'])) +
                  $lsp_rating['loeschangriff_punkte'] + 
                  $lsp_rating['fragen_punkte'] + 
                  $gesamteindruck;
  $output.='<tr><td colspan="3"><h2>Schnelligkeits&uuml;bung</h2></td></tr>
    <tr><th>Zeit</th><th>Eindruck</th></th><th>Punkte</th><th>Wdh.?</th></tr>
    <tr><td><input type="number" name="schnelligkeit_zeit" value="'.($lsp_rating['schnelligkeit_zeit']).'"'.($lock?' readonly':'').'> Sekunden</td>
        <td><input type="number" min="0" max="4" name="schnelligkeit_eindruck" value="'.$lsp_rating['schnelligkeit_eindruck'].'"'.($lock?' readonly':'').'></td>
        <td>'.get_points_fastness($lsp_rating['schnelligkeit_zeit']).'</td>
        <td><input type="radio" name="schnelligkeit_gueltig" value="1"'.(($lsp_rating['schnelligkeit_gueltig']==1)?' checked':' disabled').'></td></tr>
    <tr><td><input type="number" name="schnelligkeit_zeit2" value="'.($lsp_rating['schnelligkeit_zeit2']).'"'.($wdh_schnelligkeit?'':' readonly').'> Sekunden</td>
        <td><input type="number" min="0" max="4" name="schnelligkeit_eindruck2" value="'.$lsp_rating['schnelligkeit_eindruck2'].'"'.($wdh_schnelligkeit?'':' readonly').'></td>
        <td>'.get_points_fastness($lsp_rating['schnelligkeit_zeit2']).'</td>
        <td><input type="radio" name="schnelligkeit_gueltig" value="2"'.(($lsp_rating['schnelligkeit_gueltig']==2)?' checked':'').
          ($wdh_schnelligkeit?'':' disabled').'></td></tr>
    <tr><td colspan="3"><h2>Kugelsto&szlig;en</h2></td></tr>
    <tr><th>Weite</th><th>Eindruck</th></th><th>Punkte</th><th>Wdh.?</th></tr>
    <tr><td><input type="number" name="kugel_weite" value="'.($lsp_rating['kugel_weite']).'"'.($lock?' readonly':'').'> Meter</td>
        <td><input type="number" min="0" max="4" name="kugel_eindruck" value="'.$lsp_rating['kugel_eindruck'].'"'.($lock?' readonly':'').'></td>
        <td>'.get_points_shot_put($lsp_rating['kugel_weite']).'</td>
        <td><input type="radio" name="kugel_gueltig" value="1"'.(($lsp_rating['kugel_gueltig']==1)?' checked':' disabled').'></td></tr>
    <tr><td><input type="number" name="kugel_weite2" value="'.($lsp_rating['kugel_weite2']).'"'.($wdh_kugel?'':' readonly').'> Meter</td>
        <td><input type="number" min="0" max="4" name="kugel_eindruck2" value="'.$lsp_rating['kugel_eindruck2'].'"'.($wdh_kugel?'':' readonly').'></td>
        <td>'.($wdh_kugel?get_points_shot_put($lsp_rating['kugel_weite2']):'').'</td>
        <td><input type="radio" name="kugel_gueltig" value="2"'.(($lsp_rating['kugel_gueltig']==2)?' checked':'').
          ($wdh_kugel?'':' disabled').'></td></tr>
   <tr><td colspan="3"><h2>Staffellauf</h2></td></tr>
    <tr><th>Zeit</th><th>Eindruck</th></th><th>Punkte</th><th>Wdh.?</th></tr>
    <tr><td><input type="number" name="staffel_zeit" value="'.($lsp_rating['staffel_zeit']).'"'.($lock?' readonly':'').'> Sekunden</td>
        <td><input type="number" min="0" max="4" name="staffel_eindruck" value="'.$lsp_rating['staffel_eindruck'].'"'.($lock?' readonly':'').'></td>
        <td>'.get_points_relay($lsp_rating['staffel_zeit']).'</td>
        <td><input type="radio" name="staffel_gueltig" value="1"'.(($lsp_rating['staffel_gueltig']==1)?' checked':' disabled').'></td></tr>
    <tr><td><input type="number" name="staffel_zeit2" value="'.($lsp_rating['staffel_zeit2']).'"'.($wdh_staffel?'':' readonly').'> Sekunden</td>
        <td><input type="number" min="0" max="4" name="staffel_eindruck2" value="'.$lsp_rating['staffel_eindruck2'].'"'.($wdh_staffel?'':' readonly').'></td>
        <td>'.get_points_relay($lsp_rating['staffel_zeit2']).'</td>
        <td><input type="radio" name="staffel_gueltig" value="2"'.(($lsp_rating['staffel_gueltig']==2)?' checked':'').
          ($wdh_staffel?'':' disabled').'></td></tr>
    <tr><td colspan="3"><h2>L&ouml;schangriff</h2></td></tr>
    <tr><th>&nbsp;</th><th>Eindruck</th></th><th>Punkte</th></tr>
    <tr><td>&nbsp;</td><td><input type="number" min="0" max="4" name="loeschangriff_eindruck" value="'.$lsp_rating['loeschangriff_eindruck'].'"'.($lock?' readonly':'').'></td><td><input type="number" min="0" max="4" name="loeschangriff_punkte" value="'.$lsp_rating['loeschangriff_punkte'].'"'.($lock?' readonly':'').'></td>
    <tr><td colspan="3"><h2>Fragen</h2></td></tr>
    <tr><th>&nbsp;</th><th>Eindruck</th></th><th>Punkte</th></tr>
    <tr><td>&nbsp;</td><td><input type="number" min="0" max="4" name="fragen_eindruck" value="'.$lsp_rating['fragen_eindruck'].'"'.($lock?' readonly':'').'></td><td><input type="number" min="0" max="4" name="fragen_punkte" value="'.$lsp_rating['fragen_punkte'].'"'.($lock?' readonly':'').'></td>
    <tr><td colspan="3"><h2>Gesamt</h2></td></tr>
    <tr><th>&nbsp;</th><th>Eindruck</th></th><th>Punkte</th></tr>
    <tr><td>&nbsp;</td><td>'.$gesamteindruck.'</td><td>'.$gesamtpunkte.'</td></tr>
    <tr><td><input class="button" type="submit" value="OK"></td></tr>
  ';

  $output.='  </table>
    </form>';
  return $output;
}

function modify_lsp_rating($db,$abnahme) {
  global $error_output;
  $lsp=get_lsp($db,$abnahme);
  $grp=get_lsp_group($db,$abnahme,$gid);
  if ($_POST['neu']=='on') {
    $query="INSERT lsp_wertung SET abnahme='".$abnahme."', gruppe='".$_POST['gruppe']."', ";
  }
  else {
    $query="UPDATE lsp_wertung SET ";
  }

  $query.="schnelligkeit_zeit='".gmdate("H:i:s",$_POST['schnelligkeit_zeit'])."',
           schnelligkeit_eindruck=".$_POST['schnelligkeit_eindruck'].",
           schnelligkeit_gueltig=".$_POST['schnelligkeit_gueltig'].",
           schnelligkeit_zeit2='".gmdate("H:i:s",$_POST['schnelligkeit_zeit2'])."',
           schnelligkeit_eindruck2=".$_POST['schnelligkeit_eindruck2'].",
           kugel_weite=".$_POST['kugel_weite'].",
           kugel_eindruck=".$_POST['kugel_eindruck'].",
           kugel_gueltig=".$_POST['kugel_gueltig'].",
           kugel_weite2=".$_POST['kugel_weite2'].",
           kugel_eindruck2=".$_POST['kugel_eindruck2'].",
           staffel_zeit='".gmdate("H:i:s",$_POST['staffel_zeit'])."',
           staffel_eindruck=".$_POST['staffel_eindruck'].",
           staffel_gueltig=".$_POST['staffel_gueltig'].",
           staffel_zeit2='".gmdate("H:i:s",$_POST['staffel_zeit2'])."',
           staffel_eindruck2=".$_POST['staffel_eindruck2'].",
           loeschangriff_punkte=".$_POST['loeschangriff_punkte'].",
           loeschangriff_eindruck=".$_POST['loeschangriff_eindruck'].",
           fragen_punkte=".$_POST['fragen_punkte'].",
           fragen_eindruck=".$_POST['fragen_eindruck']
  ;

  if ($_POST['neu']=='off') {
    $query.=" WHERE gruppe=".$_POST['gruppe'];
  }
  if (!($db->query($query))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  }
}

function button_show_lsp_results() {
  $output='<form action="index.php" method="POST" id="showlspresults">
  <input type="hidden" name="do" value="showlspresults">
  <input class="menubutton" type="submit" value="Ergebnisse"></form>';
  return $output;
}

function get_lsp_resultlist($db,$abnahme) {
  $query="SELECT * FROM lsp_wertung JOIN lsp_gruppe ON lsp_wertung.abnahme=lsp_gruppe.abnahme AND lsp_wertung.gruppe=lsp_gruppe.id WHERE lsp_gruppe.abnahme='$abnahme' ORDER BY lsp_gruppe.startnummer";
  if (!($result=$db->query($query))) {
    return false;
  }
  $list=array();
  while ($line = $result->fetch_assoc()) {
    array_push($list,$line);
  }
  return $list;
}

function get_lsp_group_gesamteindruck($item) {
  return round((
                    ($item['schnelligkeit_gueltig']>1?$item['schnelligkeit_eindruck2']:$item['schnelligkeit_eindruck']) +
                    ($item['kugel_gueltig']>1?$item['kugel_eindruck2']:$item['kugel_eindruck']) +
                    ($item['staffel_gueltig']>1?$item['staffel_eindruck2']:$item['staffel_eindruck']) +
                    ($item['loeschangriff_eindruck']) +
                    ($item['fragen_eindruck']))
                    /5);
}

function get_lsp_group_gesamtpunkte($item) {
  $gesamteindruck = get_lsp_group_gesamteindruck($item);
  if ((($item['schnelligkeit_gueltig']>1?get_points_fastness($item['schnelligkeit_zeit2']):get_points_fastness($item['schnelligkeit_zeit'])) *
                  ($item['kugel_gueltig']>1?get_points_shot_put($item['kugel_weite2']):get_points_shot_put($item['kugel_weite'])) *
                  ($item['staffel_gueltig']>1?get_points_relay($item['staffel_zeit2']):get_points_relay($item['staffel_zeit'])) *
                  $item['loeschangriff_punkte'] *
                  $item['fragen_punkte'] *
                  $gesamteindruck) > 0)
    {
      $gesamtpunkte = ($item['schnelligkeit_gueltig']>1?get_points_fastness($item['schnelligkeit_zeit2']):get_points_fastness($item['schnelligkeit_zeit'])) +
                  ($item['kugel_gueltig']>1?get_points_shot_put($item['kugel_weite2']):get_points_shot_put($item['kugel_weite'])) +
                  ($item['staffel_gueltig']>1?get_points_relay($item['staffel_zeit2']):get_points_relay($item['staffel_zeit'])) +
                  $item['loeschangriff_punkte'] +
                  $item['fragen_punkte'] +
                  $gesamteindruck;
    } else $gesamtpunkte=0;
  return $gesamtpunkte;
}

function show_lsp_results($db,$abnahme) {
  $lsp=get_lsp($db,$abnahme);
  $list=get_lsp_resultlist($db,$abnahme);
  $output='<h1>Ergebnisse</h1>';
  $output.='<h2>Abnahme der Leistungsspange am '.date("d.m.Y", strtotime($lsp['datum'])).' in '.$lsp['ort'].'</h2>';
  $output.='<table class="lspresulttable">';
  $output.='<tr><th>StNr</th><th>Gruppe</th><th>Schnell</th><th>E</th><th>Kugel</th><th>E</th><th>Staffel</th><th>E</th><th>L&ouml;sch</th><th>E</th><th>Fragen</th><th>E</th><th>Ges. E</th><th>Punkte</th></tr>';
  foreach($list as $item) {
    $item['schnelligkeit_zeit']=strtotime("1970-01-01 ".$item['schnelligkeit_zeit']." UTC");
    $item['schnelligkeit_zeit2']=strtotime("1970-01-01 ".$item['schnelligkeit_zeit2']." UTC");
    $item['staffel_zeit']=strtotime("1970-01-01 ".$item['staffel_zeit']." UTC");
    $item['staffel_zeit2']=strtotime("1970-01-01 ".$item['staffel_zeit2']." UTC");

    $gesamteindruck = get_lsp_group_gesamteindruck($item);
    $gesamtpunkte = get_lsp_group_gesamtpunkte($item);

    $output.='<tr>
              <td>'.$item['startnummer'].'</td>
              <td><form action="index.php" method="POST" id="lsprating">
                <input type="hidden" name="do" value="showlsprating">
                <input type="hidden" name="abnahme" value="'.$abnahme.'">
                <input type="hidden" name="group" value="'.$item['gruppe'].'">
                <input class="shwgrprtg" type="submit" value="'.$item['name'].'"></form></td>
              <td>'.$item['schnelligkeit_zeit'].' Sek. ('.get_points_fastness($item['schnelligkeit_zeit']).')';
              if ($item['schnelligkeit_gueltig']==2) $output.='<br>'.$item['schnelligkeit_zeit2'].' Sek. ('.get_points_fastness($item['schnelligkeit_zeit2']).')';
    $output.='</td>
              <td>'.$item['schnelligkeit_eindruck'];
              if ($item['schnelligkeit_gueltig']==2) $output.='<br>'.$item['schnelligkeit_eindruck2'];
    $output.='</td>
              <td>'.$item['kugel_weite'].' m ('.get_points_shot_put($item['kugel_weite']).')';
              if ($item['kugel_gueltig']==2) $output.='<br>'.$item['kugel_weite2'].' m ('.get_points_shot_put($item['kugel_weite2']).')';
    $output.='</td>
              <td>'.$item['kugel_eindruck'];
              if ($item['kugel_gueltig']==2) $output.='<br>'.$item['kugel_eindruck2'];
    $output.='</td>
              <td>'.date('i:s',$item['staffel_zeit']).' Min. ('.get_points_relay($item['staffel_zeit']).')';
              if ($item['staffel_gueltig']==2) $output.='<br>'.date('i:s',$item['staffel_zeit2']).' Min. ('.get_points_relay($item['staffel_zeit2']).')';
    $output.='</td>
              <td>'.$item['staffel_eindruck'];
              if ($item['staffel_gueltig']==2) $output.='<br>'.$item['staffel_eindruck2'];
    $output.='</td>
              <td>'.$item['loeschangriff_punkte'].'</td>
              <td>'.$item['loeschangriff_eindruck'].'</td>
              <td>'.$item['fragen_punkte'].'</td>
              <td>'.$item['fragen_eindruck'].'</td>
              <td>'.$gesamteindruck.'</td>
              <td>'.$gesamtpunkte.'</td>
              </tr>';
  } 
  $output.='</table>';
  return $output;
}

function show_lsp_rating($db,$abnahme,$group) {
  $lsp=get_lsp($db,$abnahme);
  $grp=get_lsp_group($db,$abnahme,$group);
  $rtg=get_lsp_rating($db,$abnahme,$group);
  $mbrs=get_lsp_group_members($db,$abnahme,$group);

  $rtg['schnelligkeit_zeit']=strtotime("1970-01-01 ".$rtg['schnelligkeit_zeit']." UTC");
  $rtg['schnelligkeit_zeit2']=strtotime("1970-01-01 ".$rtg['schnelligkeit_zeit2']." UTC");
  $rtg['staffel_zeit']=strtotime("1970-01-01 ".$rtg['staffel_zeit']." UTC");
  $rtg['staffel_zeit2']=strtotime("1970-01-01 ".$rtg['staffel_zeit2']." UTC");

  $gesamteindruck = get_lsp_group_gesamteindruck($rtg);
  $gesamtpunkte = get_lsp_group_gesamtpunkte($rtg);

  $output='<table class="wertungskopf">';
  $output.='<tr><td>Start-Nr: '.$grp['startnummer'].'</td>
               <th>B E W E R T U N G S B L A T T<br>
                   Leistungsbewertung zum Erwerb der Leistungsspange der Deutschen Jugendfeuerwehr</th></tr>';
  $output.='<tr><td>am: '.date('d.m.Y',strtotime($lsp['datum'])).'</td><td>in: '.$lsp['ort'].'</td></tr>';
  $output.='<tr><td>Kreis: '.$lsp['kreis'].'</td><td>Bundesland: '.get_bundesland($db,$lsp['bundesland']).'</td></tr>';
  $output.='<tr><td>zu bewertende Jugendfeuerwehr:</td><td>'.$grp['ort'].' '.$grp['name'].'</td></tr>';
  $output.='<tr><td>Kreis: '.$grp['kreis'].'</td><td>Bundesland: '.get_bundesland($db,$grp['bundesland']).'</td></tr>';
  $output.='</table>';

  $output.='<table class="bewerter">';
  $output.='<tr><th colspan="4">Bewertungsausschu&szlig;</th></tr>';
  $output.='<tr><td>Abnahmeberechtigter:</td><td>'.$lsp['ab_vorname'].'</td><td>'.$lsp['ab_name'].'</td><td>'.$lsp['ab_ort'].'</td></tr>';
  $output.='<tr><td>Wertungsrichter 1:</td><td>'.$lsp['wr1_vorname'].'</td><td>'.$lsp['wr1_name'].'</td><td>'.$lsp['wr1_ort'].'</td></tr>';
  $output.='<tr><td>Wertungsrichter 2:</td><td>'.$lsp['wr2_vorname'].'</td><td>'.$lsp['wr2_name'].'</td><td>'.$lsp['wr2_ort'].'</td></tr>';
  $output.='<tr><td>Wertungsrichter 3:</td><td>'.$lsp['wr3_vorname'].'</td><td>'.$lsp['wr3_name'].'</td><td>'.$lsp['wr3_ort'].'</td></tr>';
  $output.='<tr><td>Wertungsrichter 4:</td><td>'.$lsp['wr4_vorname'].'</td><td>'.$lsp['wr4_name'].'</td><td>'.$lsp['wr4_ort'].'</td></tr>';
  $output.='<tr><td>Wertungsrichter 5:</td><td>'.$lsp['wr5_vorname'].'</td><td>'.$lsp['wr5_name'].'</td><td>'.$lsp['wr5_ort'].'</td></tr>';
  $output.='</table>';

  $output.='<h4>Teilnehmer (x=Bewerber, L=Leistungsspange bereits erhalten)</h4>';
  $output.='<table class="teilnehmer">';
  $output.='<tr><th>Nr</th><th>X</th><th>Name</th><th>Vorname</th><th>w/m</th><th>Geb.-Datum</th><th>Eintritt</th><th>Ausweis-Nr</th><th>Ausl.</th><th>BL</th></tr>';
  foreach ($mbrs as $mbr) {
    if ($mbr['einsatz']==1)
      $output.='<tr><td>'.$mbr['position'].'</td><td>'.$mbr['bewerber'].'</td><td>'.$mbr['name'].'</td><td>'.$mbr['vorname'].'</td><td>'.$mbr['geschlecht'].'</td><td>'.date('d.m.Y',strtotime($mbr['geburtstag'])).'</td><td>'.date('d.m.Y',strtotime($mbr['eintritt'])).'</td><td>'.$mbr['ausweisnr'].'</td><td>'.$mbr['auslaender'].'</td><td>'.$mbr['bundesland'].'</td></tr>';
  } 
  $output.='</table>';
  $output.='<h4>Bewertung</h4>';
  $output.='<table class="bewertung">';
  $output.='<tr><th>Art der Leistung</th><th colspan="2">Leistung</th><th>g&uuml;ltig</th><th>Eindruck</th><th>Punkte</th><th>Unterschrift</th></tr>';
  $output.='<tr><th>Schnelligkeits&uuml;bung</th><td>I </td><td>'.$rtg['schnelligkeit_zeit'].' Sek.</td><td>'.(($rtg['schnelligkeit_gueltig']==1)?'X':'&nbsp;').'</td><td>'.$rtg['schnelligkeit_eindruck'].'</td><td>'.get_points_fastness($rtg['schnelligkeit_zeit']).'</td><td>&nbsp;</td></tr>';
  $output.='<tr><td>(Wertungsrichter 1)</td><td>II </td><td>'.$rtg['schnelligkeit_zeit2'].' Sek.</td><td>'.(($rtg['schnelligkeit_gueltig']==2)?'X':'&nbsp;').'</td><td>'.($rtg['schnelligkeit_gueltig']==2?$rtg['schnelligkeit_eindruck2']:'&nbsp;').'</td><td>'.get_points_fastness($rtg['schnelligkeit_zeit2']).'</td><td>&nbsp;</td></tr>';
  $output.='<tr><th>Kugelsto&szlig;en</th><td>I </td><td>'.$rtg['kugel_weite'].' m</td><td>'.(($rtg['kugel_gueltig']==1)?'X':'&nbsp;').'</td><td>'.$rtg['kugel_eindruck'].'</td><td>'.get_points_shot_put($rtg['kugel_weite']).'</td><td>&nbsp;</td></tr>';
  $output.='<tr><td>(Wertungsrichter 2)</td><td>II </td><td>'.$rtg['kugel_weite2'].' m</td><td>'.(($rtg['kugel_gueltig']==2)?'X':'&nbsp;').'</td><td>'.($rtg['kugel_gueltig']==2?$rtg['kugel_eindruck2']:'&nbsp;').'</td><td>'.($rtg['kugel_gueltig']==2?get_points_shot_put($rtg['kugel_weite2']):'&nbsp;').'</td><td>&nbsp;</td></tr>';
  $output.='<tr><th>Staffellauf</th><td>I </td><td>'.date('i:s',$rtg['staffel_zeit']).' Min.</td><td>'.(($rtg['staffel_gueltig']==1)?'X':'&nbsp;').'</td><td>'.$rtg['staffel_eindruck'].'</td><td>'.get_points_relay($rtg['staffel_zeit']).'</td><td>&nbsp;</td></tr>';
  $output.='<tr><td>(Wertungsrichter 3)</td><td>II </td><td>'.date('i:s',$rtg['staffel_zeit2']).' Min.</td><td>'.(($rtg['staffel_gueltig']==2)?'X':'&nbsp;').'</td><td>'.($rtg['staffel_gueltig']==2?$rtg['staffel_eindruck2']:'&nbsp;').'</td><td>'.get_points_relay($rtg['staffel_zeit2']).'</td><td>&nbsp;</td></tr>';
  $output.='<tr><th>L&ouml;schangriff</th><td rowspan="2" colspan="3">&nbsp;</td><td rowspan="2">'.$rtg['loeschangriff_eindruck'].'</td><td rowspan="2">'.$rtg['loeschangriff_punkte'].'</td><td rowspan="2">&nbsp;</td></tr>';
  $output.='<tr><td>(Wertungsrichter 4)</td></tr>';
  $output.='<tr><th>Fragenbeantwortung</th><td rowspan="2" colspan="3">&nbsp;</td><td rowspan=2">'.$rtg['fragen_eindruck'].'</td><td rowspan="2">'.$rtg['fragen_punkte'].'</td><td rowspan="2">&nbsp;</td></tr>';
  $output.='<tr><td>(Wertungsrichter 5)</td></tr>';
  $output.='<tr><td colspan="3">&nbsp;</td><td>'.$summeeindruck.'/5</td><td>'.$gesamteindruck.'</td><td colspan="2">Gesamteindruck</td></tr>';
  $output.='<tr><td colspan="4" rowspan="2">Ich versichere, die einzelnen &Uuml;bungen nach den Richtlinien der Deutschen Jugendfeuerwehr durchgef&uuml;hrt zu haben. Abnahmeberechtigter der Deutschen Jugendfeuerwehr:</td><th>'.$gesamtpunkte.'</th><th colspan="2">Gesamtpunkte</th></tr>';
  $output.='<tr><th colspan="2">'.$lsp['ab_name'].'</th></tr>';
  $output.='</table>';
  return $output;
}

function button_manage_lsp_judges() {
  $output='<form action="index.php" method="POST" id="managelspjudges">
  <input type="hidden" name="do" value="managelspjudges">
  <input class="menubutton" type="submit" value="Wertungsrichter"></form>';
  return $output;
}


function manage_lsp_judges($db,$abnahme) {
  $lsp=get_lsp($db,$abnahme);
  $judges=get_lsp_judges($db,$abnahme);
  $output='<h1>Wertungsrichter</h1>';
  $output.='<h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>';
  $output.='<form action="index.php" method="POST" id="modifylspjudges">
      <input type="hidden" name="do" value="modifylspjudges">';
  $output.='<table>';
  $output.='<tr><th>Wertungsrichter</th><th>Vorname</th><th>Name</th><th>Ort</th></tr>';
  $output.='<tr><th>1 Schnelligkeits&uuml;bung</th><td><input type="text" name="wr1name" size="25" maxlength="50" value="'.$judges['wr1_name'].'"></td><td><input type="text" name="wr1vorname" size="25" maxlength="50" value="'.$judges['wr1_vorname'].'"></td><td><input type="text" name="wr1ort" size="25" maxlength="50" value="'.$judges['wr1_ort'].'"></tr>';
  $output.='<tr><th>2 Kugelsto&szlig;en</th><td><input type="text" name="wr2name" size="25" maxlength="50" value="'.$judges['wr2_name'].'"></td><td><input type="text" name="wr2vorname" size="25" maxlength="50" value="'.$judges['wr2_vorname'].'"></td><td><input type="text" name="wr2ort" size="25" maxlength="50" value="'.$judges['wr2_ort'].'"></tr>';
  $output.='<tr><th>3 Staffellauf</th><td><input type="text" name="wr3name" size="25" maxlength="50" value="'.$judges['wr3_name'].'"></td><td><input type="text" name="wr3vorname" size="25" maxlength="50" value="'.$judges['wr3_vorname'].'"></td><td><input type="text" name="wr3ort" size="25" maxlength="50" value="'.$judges['wr3_ort'].'"></tr>';
  $output.='<tr><th>4 L&ouml;schangriff</th><td><input type="text" name="wr4name" size="25" maxlength="50" value="'.$judges['wr4_name'].'"></td><td><input type="text" name="wr4vorname" size="25" maxlength="50" value="'.$judges['wr4_vorname'].'"></td><td><input type="text" name="wr4ort" size="25" maxlength="50" value="'.$judges['wr4_ort'].'"></tr>';
  $output.='<tr><th>5 Fragenbeantwortung</th><td><input type="text" name="wr5name" size="25" maxlength="50" value="'.$judges['wr5_name'].'"></td><td><input type="text" name="wr5vorname" size="25" maxlength="50" value="'.$judges['wr5_vorname'].'"></td><td><input type="text" name="wr5ort" size="25" maxlength="50" value="'.$judges['wr5_ort'].'"></tr>';
  $output.='<tr><td><input class="button" type="submit" value="OK"></td></tr>';
  $output.='</table>';
  $output.='</form>';
  return $output;
}

function modify_lsp_judges($db,$abnahme) {
  $lsp=get_lsp($db,$abnahme);
  $query="UPDATE leistungsspange SET wr1_name='".$_POST['wr1name']."',"
                                   ."wr1_vorname='".$_POST['wr1vorname']."',"
                                   ."wr1_ort='".$_POST['wr1ort']."',"
                                   ."wr2_name='".$_POST['wr2name']."',"
                                   ."wr2_vorname='".$_POST['wr2vorname']."',"
                                   ."wr2_ort='".$_POST['wr2ort']."',"
                                   ."wr3_name='".$_POST['wr3name']."',"
                                   ."wr3_vorname='".$_POST['wr3vorname']."',"
                                   ."wr3_ort='".$_POST['wr3ort']."',"
                                   ."wr4_name='".$_POST['wr4name']."',"
                                   ."wr4_vorname='".$_POST['wr4vorname']."',"
                                   ."wr4_ort='".$_POST['wr4ort']."',"
                                   ."wr5_name='".$_POST['wr5name']."',"
                                   ."wr5_vorname='".$_POST['wr5vorname']."',"
                                   ."wr5_ort='".$_POST['wr5ort']."' "
                                   ."WHERE id='".$abnahme."'";
  if (!$query=='') {
    if (!($db->query($query))) {
      $error_output.="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    }
  }
  unset($_POST);
}

function get_lsp_judges($db,$abnahme) {
  if ($result = $db->query("SELECT wr1_name,wr1_vorname,wr1_ort,wr2_name,wr2_vorname,wr2_ort,wr3_name,wr3_vorname,wr3_ort,wr4_name,wr4_vorname,wr4_ort,wr5_name,wr5_vorname,wr5_ort FROM leistungsspange WHERE id='".$abnahme."'")) {
    return $result->fetch_assoc();
  }
  else return false;
}

function button_create_leistungsspange() {
  $output='<form action="index.php" method="POST" id="addlsp">
  <input type="hidden" name="screen" value="addlsp">
  <input class="menubutton" type="submit" value="Neue Leistungsspangenabnahme"></form>';
  return $output;
}

function button_show_lsp_token() {
  $output='<form action="index.php" method="POST" id="showlsptoken">
  <input type="hidden" name="do" value="showlsptoken">
  <input class="menubutton" type="submit" value="Token"></form>';
  return $output;
}

function form_show_lsp_token($db,$lspid) {
  global $error_output;
//  print_r($_POST);
//  print_r($_SESSION);
//  echo $lspid;
  if ($lsp=get_lsp($db,$lspid)) {
    $output='<h1>Leistungsspange der DJF</h1>';
    $output.='<h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>
    ';
    if ($tokens=get_lsp_tokens($db,$lspid)) {
      $output.='<table>';
      $output.='<tr><th>E-Mail</th><th>Token</th><th>Mannschaften</th><th>versandt</th><th>&nbsp;</th></tr>
      ';
      foreach ($tokens as $token) {
        $output.='<tr><td>'.$token['email'].'</td><td>'.$token['token'].'</td><td>'.$token['mannschaften'].'</td><td>'.($token['sent']?'Ja':'Nein').'</td>';
        $output.='<td><form action="index.php" method="POST" id="sendlsptoken">
                  <input type="hidden" name="do" value="sendlsptoken">
                  <input type="hidden" name="token" value="'.$token['id'].'">
                  <input type="submit" value="Senden"></form></td>';
        $output.='</tr>
        ';
      }
      $output.='</table>';
    }
    return $output;
  }
  $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  return false;
}

function button_create_lsp_token() {
  $output='<form action="index.php" method="POST" id="createlsptoken">
  <input type="hidden" name="do" value="createlsptoken">
  <input class="menubutton" type="submit" value="Neues Token"></form>';
  return $output;
}

function button_send_unsent_lsp_token() {
  $output='<form action="index.php" method="POST" id="sendunsentlsptoken">
  <input type="hidden" name="do" value="sendunsentlsptoken">
  <input class="menubutton" type="submit" value="Sende Ungesendete"></form>';
  return $output;
}


function form_create_lsp_token($db,$lspid) {
  if ($lsp=get_lsp($db,$lspid)) {
    $output='<h1>Token erstellen</h1>
      <form action="index.php" method="POST" id="newlsptoken">
        <input type="hidden" name="do" value="newlsptoken">
        <table>
          <tr><th>E-Mail</th></th>
          <tr><td><input type="text" size="50" name="email"></td></tr>
          <tr><td><input type="submit" value="Anlegen"></td></tr>

        </table>
      </form>';
   return $output;
  }
}

function send_lsp_token($db,$lspid,$token) {
  $token=get_lsp_id_token($db,$lspid,$token);
  if (mail_lsp_token($db,$lspid,$token['email'],$token['token'])) {
    $query="UPDATE lsp_token SET sent=1 WHERE id=".$token['id']; 
    if ($result = $db->query($query)) {
      return true;
    }
    return false;
  }
  return false;
}

function send_unsent_lsp_token($db,$lspid) {
  global $error_output;
  $tokens=get_lsp_tokens($db,$lspid);
  foreach ($tokens as $token) {
    if ($token['sent'] == 0) {
      if (mail_lsp_token($db,$lspid,$token['email'],$token['token'])) {
        $query="UPDATE lsp_token SET sent=1 WHERE id=".$token['id'];
        if (!$result = $db->query($query)) {
          $error_output.=$db->error;
        }
      }

    }
  }
  return false;
}


function mail_lsp_token($db,$lspid,$email,$token) {
  global $lsp_token_mail_template;
  global $lsp_token_system_name;
  global $lsp_token_system_email;
  $lsp=get_lsp($db,$lspid);
  $header = 'From: '.$lsp_token_system_name.' <'.$lsp_token_system_email.'>' . "\r\n" .
    'Reply-To: '.$lsp_token_system_email. "\r\n" .
    'X-Mailer:'.$lsp_token_system_name;
  $replace=array(
    'location' => $lsp['ort'],
    'date'     => date('d.m.Y',strtotime($lsp['datum'])),
    'url'      => get_system_url().'token.php',
    'token'    => $token,
  ); 
  return mail($email,"Einladung zu einer Leistungsspangenabnahme",parse_mail_template($lsp_token_mail_template,$replace),$header);
}

function parse_mail_template($template_file,$replacements) {
  if (!$template=fopen($template_file,'r')) die ('Fehler beim Laden des Templates!');
  $output='';
  while (!feof($template))
  {
    $output.=fgets($template);
  }
  fclose($template);

  $output=str_replace('{LSP__LOCATION}',$replacements['location'],$output);
  $output=str_replace('{LSP__DATE}',$replacements['date'],$output);
  $output=str_replace('{LSP__URL}',$replacements['url'],$output);
  $output=str_replace('{LSP__TOKEN}',$replacements['token'],$output);
  return $output;
}

function button_import_lsp_group_members($db,$lsp,$grp) {
  $output='<form action="index.php" method="POST" id="importlspgroupmembers">
  <input type="hidden" name="do" value="importlspgroupmembers">
  <input type="hidden" name="gruppe" value="'.$grp.'">
  <input class="menubutton" type="submit" value="Importieren"></form>';
  return $output;
}

function button_import_lsp_token_group_members($db,$token,$grp) {
  $output='<form action="token.php" method="POST" id="importlsptokengroupmembers">
  <input type="hidden" name="do" value="importlsptokengroupmembers">
  <input type="hidden" name="gruppe" value="'.$grp.'">
  <input class="menubutton" type="submit" value="Importieren"></form>';
  return $output;
}

function form_import_lsp_group_members($db,$lsp,$grp) {
 $output='<h1>Gruppe importieren</h1>';
 $output.='<form action="index.php" method="POST" id="importlspgroup">
            <input type="hidden" name="do" value="parselspgroupimport">
            <input type="hidden" name="gruppe" value="'.$grp.'">
            <table class="importlspgroup">
              <tr><td>
                <textarea name="import" rows="11" cols="100"></textarea>
              </td></tr>
              <tr><td>
                <input type="submit" name="submit" value="Import!">
              </td></tr>
            </table>
          </form>';
 $output.='<form action="index.php" method="post" id="back"><input type="hidden" name="do" value="editlspgrpmembers">
          <input type="hidden" name="gruppe" value="'.$grp.'"><input class="menubutton" type="submit" value="Zur&uuml;ck"></form>';
 return $output;
}

function form_import_lsp_token_group_members($db,$token,$grp) {
 $output='<h1>Gruppe importieren</h1>';
 $output.='<form action="token.php" method="POST" id="importlsptokengroup">
            <input type="hidden" name="do" value="parselsptokengroupimport">
            <input type="hidden" name="gruppe" value="'.$grp.'">
            <table class="importlspgroup">
              <tr><td>
                <textarea name="import" rows="11" cols="100"></textarea>
              </td></tr>
              <tr><td>
                <input type="submit" name="submit" value="Import!">
              </td></tr>
            </table>
          </form>';
 $output.='<form action="token.php" method="post" id="back"><input type="hidden" name="do" value="editlsptokengrpmembers">
          <input type="hidden" name="gruppe" value="'.$grp.'"><input class="menubutton" type="submit" value="Zur&uuml;ck"></form>';
 return $output;
}

function parse_lsp_group_import($db,$lsp,$grp,$import) {
  global $error_output;

  $data=explode("\n",$import);
  $count=count($data);
  for ($i=0; $i<$count; $i++) {
    $data[$i]=explode("\t",$data[$i]);
    if (count($data[$i]) < 7) {
      unset($data[$i]);
    }
  }
  if (((int)$data[0][0]) > 0) {
    $mode=1;
  } else {
    if (in_array($data[0][0],array('X','L',''))) {
      $mode=0;
    }
  }
  $count=count($data);
  for ($i=0; $i<$count; $i++) {
    if (strlen($data[$i][1+$mode])==0||strlen($data[$i][2+$mode])==0||strlen($data[$i][3+$mode])==0||strlen($data[$i][4+$mode])==0||strlen($data[$i][5+$mode])==0||strlen($data[$i][6+$mode])==0) {
      unset($data[$i]);
    } else
    {
    switch (trim($data[$i][0+$mode])) {
      case 'l': 
        $data[$i][0+$mode]='L';
        break;
      case 'L':
        $data[$i][0+$mode]='L';
        break;
      case 'x':
        $data[$i][0+$mode]='X';
        break;
      case 'X':
        $data[$i][0+$mode]='X';
        break;
      default:
        $data[$i][0+$mode]='';
        break;
     }
     $data[$i][1+$mode]=trim($data[$i][1+$mode]);
     $data[$i][2+$mode]=trim($data[$i][2+$mode]);
     $data[$i][3+$mode]=date('Y-m-d',strtotime($data[$i][3+$mode]));
     $data[$i][4+$mode]=date('Y-m-d',strtotime($data[$i][4+$mode]));
     switch (trim($data[$i][6+$mode])) {
       case 'W':
         $data[$i][6+$mode]='w';
         break;
       case 'w':
         $data[$i][6+$mode]='w';
         break;
       case 'M':
         $data[$i][6+$mode]='m';
         break;
       case 'm':
         $data[$i][6+$mode]='m';
         break;
       default:
         $data[$i][6+$mode]='m';
         break;
     }
     $data[$i][5+$mode]=trim($data[$i][5+$mode]);
     switch (trim($data[$i][7+$mode])) {
       case 'X':
         $data[$i][7+$mode]='1';
         break;
       case 'x':
         $data[$i][7+$mode]='1';
         break;
       case '':
         $data[$i][7+$mode]='0';
         break;
       default:
         $data[$i][7+$mode]='0';
         break;
     }
     }
  }
  $data=array_values($data);

  if ($mode==1) {
    $maxposition=0;
    for ($i=0; $i<count($data); $i++) {
      $maxposition++;
      if (trim($data[$i][0])=='E') $data[$i][0]=$maxposition;
    }
  }

  $group=get_lsp_group_members($db,$lsp,$grp);
  $abnahme=get_lsp($db,$lsp);
  
  for ($i=0; $i<count($data); $i++) {
    $entry=$data[$i];
    if ($mode==1) $position=$entry[0]; else $position=$i+1;
    $exists=0;
    foreach($group as $member) {
      if (($i+1)==$member['position']) $exists=1;
    }
    if ($exists) {    
      $query="UPDATE lsp_teilnehmer SET bewerber='".$entry[0+$mode]."', name='".$entry[1+$mode]."', vorname='".$entry[2+$mode]."', geburtstag='".$entry[3+$mode]."', eintritt='".$entry[4+$mode]."', ausweisnr='".$entry[5+$mode]."', geschlecht='".$entry[6+$mode]."', auslaender='".$entry[7+$mode]."', bundesland='".$abnahme['bundesland']."' WHERE abnahme='".$lsp."' AND gruppe='".$grp."' AND position='".$position."' ";
    }
    else {
      if ($position <= 9) $einsatz=1; else $einsatz=0;
      $query="INSERT lsp_teilnehmer SET abnahme='".$lsp."', gruppe='".$grp."', einsatz='".$einsatz."', position='".$position."', bewerber='".$entry[0+$mode]."', name='".$entry[1+$mode]."', vorname='".$entry[2+$mode]."', geburtstag='".$entry[3+$mode]."', eintritt='".$entry[4+$mode]."', ausweisnr='".$entry[5+$mode]."', geschlecht='".$entry[6+$mode]."', auslaender='".$entry[7+$mode]."', bundesland='".$abnahme['bundesland']."'";
    }

    if (!$query=='') {
      if (!($db->query($query))) {
        $error_output.="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      }
    }
  }
  
  return true;
}

function form_ask_lsp_token() {
  $output='<h1>Token eingeben</h1>';
  $output.='<form action="token.php" method="POST" id="gettoken">';

  $output.='<input type="hidden" name="do" value="gettoken">';
  $output.='<table class="gettoken">';
  $output.='<tr><td><input type="text" size="30" maxlength="20" minlength="20" name="token"></td>';
  $output.='<td><input type="submit" name="submit" value="Login"></tr>';
  $output.='</table></form>';
  return $output;
}

function check_lsp_token($db,$token) {
  if(!preg_match("/[a-zA-Z0-9]/",$token)) {
    return false;
  };
  $query="SELECT id FROM lsp_token WHERE token='".$token."'";
  if ($result = $db->query($query)) { 
    if ($result->num_rows == 1) return true;
  }
  return false;
}

function select_lsp_token($db,$token) {
  global $error_output;
  if ($token=='null') {
    unset($_SESSION['token']);
    return true;
  }
  unset($_SESSION['LSP']);
  unset($_SESSION['WB']);
  if (strlen($token)==20 && check_lsp_token($db,$token)) {
    $_SESSION['token']=$token;
  }
  else {
    $error_output='Keine g&uuml;ltiges Token!';
    return false;
  }
  return true;
}

function form_show_lsp_token_groups($db,$token,$sort) {
  global $error_output;
//  print_r($_POST);
//  print_r($_SESSION);
//  echo $lspid;
  $token=get_lsp_token($db,$token);
//  print_r($token);
  if ($lsp=get_lsp($db,$token['abnahme'])) {
    $output='<h1>Leistungsspange der DJF</h1>';
    $output.='<h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>';
    if ($grps=get_lsp_groups_by_token($db,$token['id'],$sort)) {
      $output.='<table>
        <tr><th><a href="token.php?sort=startnummer">Start Nr</a></th><th>Name</th><th>Land</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>';
      foreach ($grps as $grp) {
        $output.='<tr><td>'.$grp['startnummer'].'</td>
          <td><form action="token.php" method="POST" id="editlsptokengrp"><input type="hidden" name="do" value="editlsptokengrp"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="shwedtgrp" type="submit" value="'.$grp['name'].'"></form></td>
          <td>'.get_bundesland($db,$grp['bundesland']).'</td><td>'.$grp['bezirk'].'</td><td>'.$grp['kreis'].'</td><td>'.$grp['ort'].'</td>
          <td><form action="token.php" method="POST" id="editlsptokengrpmember">
            <input type="hidden" name="do" value="editlsptokengrpmembers"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="edtlspgrpmbr" type="submit" value="Teilnehmer">
          </form></td>
          </tr>';
      }
      $output.='</table>';
    }
    return $output;
  }
  $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  return false;
}

function button_lsp_token_logout() {
  $output='<form action="token.php" method="POST" id="logoutlsptoken">
  <input type="hidden" name="do" value="gettoken">
  <input type="hidden" name="token" value="null">
  <input class="menubutton" type="submit" value="Logout"></form>';
  return $output;
}

function form_edit_lsp_token_group($db,$token,$gid) {
  $token=get_lsp_token($db,$token);
  $lsp=get_lsp_group($db,$token['abnahme'],$gid);
  $blr=get_bundeslaender($db);
  $output='<h1>'.$lsp['name'].' editieren</h1>
  <form action="token.php" method="POST" id="editgrp">
    <input type="hidden" name="do" value="modifylsptokengrp">
    <input type="hidden" name="id" value="'.$lsp['id'].'">
    <table>
      <tr><th>Name</th><th>Typ</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>
      <tr>
        <td><input name="name" value="'.$lsp['name'].'"></td>
        <td><select name="bundesland">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['id'].'"';
          if ($lsp['bundesland']==$bl['id']) $output.=' selected';
          $output.='>'.$bl['name'].'</option>';
        }
        $output.='
        </select></td>
        <td><input name="bezirk" value="'.$lsp['bezirk'].'"></td>
        <td><input name="kreis" value="'.$lsp['kreis'].'"></td>
        <td><input name="ort" value="'.$lsp['ort'].'"></td>
      </tr>
      <tr> <td><input class="button" type="submit" value="OK"></td></tr>
      </table>
  </form>';
  return $output;
}

function modify_lsp_token_group($db,$token,$id) {
  global $error_output;
  $token=get_lsp_token($db,$token);
  $query="UPDATE lsp_gruppe SET name='".$db->real_escape_string($_POST['name'])."', bundesland=".((int)$db->real_escape_string($_POST['bundesland'])).", bezirk='".$db->real_escape_string($_POST['bezirk'])."', kreis='".$db->real_escape_string($_POST['kreis'])."', ort='".$db->real_escape_string($_POST['ort'])."' WHERE abnahme='".$token['abnahme']."' AND id=".$id;
  if (!(trim($_POST['name'])=='') && !(trim($_POST['bezirk'])=='') && !(trim($_POST['kreis'])=='') && !(trim($_POST['ort'])=='')) {
    if (!($db->query($query) === TRUE)) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      return false;
    }
    return true;
  }
}

function button_back_token() {
  $output='<form action="token.php" method="POST" id="nothing">
  <input type="hidden" name="do" value="nothing">
  <input class="menubutton" type="submit" value="Zur&uuml;ck">
  </form>';
  return $output;
}

function form_edit_lsp_token_group_members($db,$token,$gid) {
  $token=get_lsp_token($db,$token);
  $lsp=get_lsp($db,$token['abnahme']);
  $grp=get_lsp_group($db,$token['abnahme'],$gid);
  $lspmembers=get_lsp_group_members($db,$token['abnahme'],$gid);
  $blr=get_bundeslaender($db);
  $groupsize=9;
  $spare=2;
  $teamsize=$groupsize+$spare;
  $output='<h1>Teilnehmermeldung '.$grp['name'].'</h1>
    <form action="token.php" method="POST" id="editlspgrptokenmembers">
      <input type="hidden" name="do" value="modifylsptokengrpmembers">
      <input type="hidden" name="id" value="'.$grp['id'].'">
      <table class="lspgrpmbs">
        <tr><th>Position</th><th>Bewerber</th><th>Name</th><th>Vorname</th><th>Geburtsdatum</th><th>Eintritt</th><th>Ausweis-Nr.</th><th>Geschlecht</th><th>Ausl&auml;nder</th><th>Bundesland</th></tr>';
        if (count($lspmembers)>0) {
          foreach($lspmembers as $lspmbr) {
          $output.='<tr>';
          $output.='<td><input type="hidden" name="neu[]" value="off">';
          $output.='<input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$lspmbr['position'].'"></td>
          <td><select class="'.get_cls("bw".$lspmbr['bewerber'],date('Y-m-d',strtotime($lspmbr['geburtstag'])),$lsp['datum']).'" name="bewerber[]"><option value="">&nbsp;</option><option value="X"';
          if ($lspmbr['bewerber']=='X') $output.=' selected';
          $output.='>X</option><option value="L"';
          if ($lspmbr['bewerber']=='L') $output.=' selected';
          $output.='>L</option></select></td>
          <td><input class="sn" type="text" name="name[]" value="'.$lspmbr['name'].'"></td>
          <td><input class="gn" type="text" name="vorname[]" value="'.$lspmbr['vorname'].'"></td>
          <td><input class="'.get_cls("dt",date('Y-m-d',strtotime($lspmbr['geburtstag'])),$lsp['datum']).'" type="date" name="geburt[]" value="'.date('Y-m-d',strtotime($lspmbr['geburtstag'])).'"></td>
          <td><input class="et" type="date" name="eintritt[]" value="'.date('Y-m-d',strtotime($lspmbr['eintritt'])).'"></td>
          <td><input class="'.get_cls("aw",$lspmbr['ausweisnr'],null).'" type="text" name="ausweis[]" size="6" minlength="6" maxlength="6" value="'.$lspmbr['ausweisnr'].'"></td>
          <td><select class="gs" name="geschlecht[]"><option value="m">M&auml;nnlich</option><option value="w">Weiblich</option></select></td>
          <td><input class="al" type="checkbox" name="auslaender[]"';
          if ($lspmbr['auslaender']>0) $output.=' checked';
          $output.='></td>
          <td><select class="bl" name="bundesland[]">';
            foreach ($blr as $bl) {
              $output.='<option value="'.$bl['id'].'"';
              if ($bl['id']==$lspmbr['bundesland']) $output.=' selected';
              $output.='>';
              $output.=$bl['name'].'</option>';
            }
            $output.='
          </select></td>
          </tr>';
          }
        }
        for ($i=count($lspmembers)+1;$i<=$teamsize;$i++) {
          $output.='<tr>';
          $output.='<td><input type="hidden" name="neu[]" value="on"><input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$i.'"></td>
          <td><select class="bw" name="bewerber[]"><option value="">&nbsp;</option><option value="X">X</option><option value="L">L</option></select></td>
          <td><input class="sn" type="text" name="name[]" value=""></td>
          <td><input class="gn" type="text" name="vorname[]" value=""></td>
          <td><input class="dt" type="date" name="geburt[]" value=""></td>
          <td><input class="et" type="date" name="eintritt[]" value=""></td>
          <td><input class="aw" type="text" name="ausweis[]" size="6" minlength="6" maxlength="6" value=""></td>
          <td><select class="gs" name="geschlecht[]"><option value="m">M&auml;nnlich</option><option value="w">Weiblich</option></select></td>
          <td><input class="al" type="checkbox" name="auslaender[]"></td>
          <td><select class="bl" name="bundesland[]">';
            foreach ($blr as $bl) {
              $output.='<option value="'.$bl['id'].'"';
              if ($bl['id']==$lsp['bundesland']) $output.=' selected';
              $output.='>';
              $output.=$bl['name'].'</option>';
            }
            $output.='
          </select></td>
          </tr>';
        }
        $output.='<tr><td><input class="button" type="submit" value="OK"></td></tr>
          </table></form>';
  return $output;
}

function button_create_lsp_token_group() {
  $output='<form action="token.php" method="POST" id="addlsptokengrp">
  <input type="hidden" name="do" value="addlsptokengrp">
  <input class="menubutton" type="submit" value="Neue Gruppe"></form>';
  return $output;
}

function form_create_lsp_token_group($db,$token) {
  $token=get_lsp_token($db,$token);
  $lsp=get_lsp($db,$token['abnahme']);
  $blr=get_bundeslaender($db);
  $output='<h1>Neue Gruppe anlegen</h1>
  <h2>'.date('d.m.Y',strtotime($lsp['datum'])).' '.$lsp['ort'].', '.$lsp['kreis'].' ('.$lsp['land'].')</h2>
  <form action="token.php" method="POST" id="addlsptokengrp">
    <input type="hidden" name="do" value="insertlsptokengrp">
    <table>
      <tr><th>Name</th><th>Bundesland</th><th>Bezirk</th><th>Kreis</th><th>Ort</th></tr>
      <tr>
        <td><input name="name"></td>
        <td><select name="bundesland">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['id'].'"';
          if ($lsp['bundesland']==$bl['id']) $output.=' selected';
          $output.='>'.$bl['name'].'</option>';
        }
  $output.='
        </select></td>
        <td><input name="bezirk"></td>
        <td><input name="kreis"></td>
        <td><input name="ort"></td>
      </tr>
      <tr><td><input class="button" type="submit" value="OK"></td></tr>
    </table>
  </form>';
  return $output;
}

function insert_lsp_token_group($db,$token) {
  global $error_output;
  $token=get_lsp_token($db,$token);
  if (!(trim($_POST['name'])=='') && !(trim($_POST['bezirk'])=='') && !(trim($_POST['kreis'])=='') && !(trim($_POST['ort'])=='')) {
    $startnummer=get_max_lsp_startnummer($db,$token['abnahme'])+1;
    $lsp=get_lsp($db,$token['abnahme']);
    $nextid=get_max_lsp_group_id($db,$token['abnahme'])+1;
    $query="INSERT lsp_gruppe SET abnahme='".$token['abnahme']."', id='".$nextid."', token=".$token['id'].", startnummer=".$startnummer.", name='".$db->real_escape_string($_POST['name'])."', bundesland='".$db->real_escape_string($_POST['bundesland'])."', bezirk='".$db->real_escape_string($_POST['bezirk'])."', kreis='".$db->real_escape_string($_POST['kreis'])."', ort='".$db->real_escape_string($_POST['ort'])."'";
    if (!($db->query($query))) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    }
  } else {
    $error_output='Alle Felder m&uuml;ssen ausgef&uuml;llt werden!';
  }
}

function button_delete_lsp_token_group($id) {
  $output.='<form action="token.php" method="POST" id="dellsptokengrp">
  <input type="hidden" name="do" value="removelsptokengrp">
  <input type="hidden" name="removeid" value="'.$id.'">
  <input class="menubutton" type="submit" value="!! Gruppe L&ouml;schen !!" onClick="return confirm('."'Sicher?'".')">
  </form>';
  return $output;
}

function remove_lsp_token_group($db,$token,$grp) {
  global $error_output;
  $token=get_lsp_token($db,$token);
  $abnahme=$token['abnahme'];
  $continue=false;
  if (!($result=$db->query("DELETE FROM lsp_wertung WHERE abnahme='".$abnahme."' AND gruppe='".$grp."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  if (!($result=$db->query("DELETE FROM lsp_teilnehmer WHERE abnahme='".$abnahme."' AND gruppe='".$grp."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  if (!($result=$db->query("DELETE FROM lsp_gruppe WHERE abnahme='".$abnahme."' AND id='".$grp."'"))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    return false;
  }
  return true;
}

function modify_lsp_token_group_members($db,$token,$gid) {
  global $error_output;
  $token=get_lsp_token($db,$token);
  $lsp=get_lsp($db,$token['abnahme']);
  $grp=get_lsp_group($db,$token['abnahme'],$gid);
  $blr=get_bundeslaender($db);
  $groupsize=9;
  $spare=2;
  $teamsize=$groupsize+$spare;
  for ($i=0;$i<$teamsize;$i++) {
    if ($_POST['einsatz'][$i]=='on') $einsatz=1; else $einsatz=0;
    if ($_POST['auslaender'][$i]=='on') $auslaender=1; else $auslaender=0;
    if ($_POST['neu'][$i]=='off') {
      $query="UPDATE lsp_teilnehmer SET einsatz='".$einsatz."',"
                                      ."position='".$db->real_escape_string($_POST['position'][$i])."',"
                                      ."bewerber='".$db->real_escape_string($_POST['bewerber'][$i])."',"
                                      ."name='".$db->real_escape_string($_POST['name'][$i])."',"
                                      ."vorname='".$db->real_escape_string($_POST['vorname'][$i])."',"
                                      ."geburtstag='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."',"
                                      ."eintritt='".date('Y-m-d',strtotime($_POST['eintritt'][$i]))."',"
                                      ."ausweisnr='".$db->real_escape_string($_POST['ausweis'][$i])."',"
                                      ."geschlecht='".$db->real_escape_string($_POST['geschlecht'][$i])."',"
                                      ."auslaender='".$auslaender."',"
                                      ."bundesland='".$db->real_escape_string($_POST['bundesland'][$i])."'"
                                      ." WHERE abnahme='".$token['abnahme']."' AND gruppe='".$gid."' AND position='".$_POST['position'][$i]."'";
    } else {
      if ($_POST['ausweis'][$i] != '')
      $query="INSERT lsp_teilnehmer SET abnahme='".$token['abnahme']."',"
                                      ."gruppe='".$gid."',"
                                      ."einsatz='".$einsatz."',"
                                      ."position='".$db->real_escape_string($_POST['position'][$i])."',"
                                      ."bewerber='".$db->real_escape_string($_POST['bewerber'][$i])."',"
                                      ."name='".$db->real_escape_string($_POST['name'][$i])."',"
                                      ."vorname='".$db->real_escape_string($_POST['vorname'][$i])."',"
                                      ."geburtstag='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."',"
                                      ."eintritt='".date('Y-m-d',strtotime($_POST['eintritt'][$i]))."',"
                                      ."ausweisnr='".$db->real_escape_string($_POST['ausweis'][$i])."',"
                                      ."geschlecht='".$db->real_escape_string($_POST['geschlecht'][$i])."',"
                                      ."auslaender='".$auslaender."',"
                                      ."bundesland='".$db->real_escape_string($_POST['bundesland'][$i])."'";
      else $query='';
    }
    if (!$query=='') {
      if (!($db->query($query))) {
        $error_output.="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      }
    }
  }
  unset($_POST);
}

function button_show_lsp_statistics() {
  $output='<form action="index.php" method="POST" id="showlspstatistics">
  <input type="hidden" name="do" value="showlspstatistics">
  <input class="menubutton" type="submit" value="Statistik"></form>';
  return $output;
}

function show_lsp_statistics($db,$abnahme) {
  $lsp=get_lsp($db,$abnahme);
  $count=get_lsp_candidate_count($db,$abnahme);
  $list=get_lsp_resultlist($db,$abnahme);
  $gesamt=array('w'=>0,'m'=>0);
  $erfolg=array('w'=>0,'m'=>0);
  foreach($list as $item) {
    $item['schnelligkeit_zeit']=strtotime("1970-01-01 ".$item['schnelligkeit_zeit']." UTC");
    $item['schnelligkeit_zeit2']=strtotime("1970-01-01 ".$item['schnelligkeit_zeit2']." UTC");
    $item['staffel_zeit']=strtotime("1970-01-01 ".$item['staffel_zeit']." UTC");
    $item['staffel_zeit2']=strtotime("1970-01-01 ".$item['staffel_zeit2']." UTC");
    $gesamt['w']+=$count[$item['gruppe']]['w'];
    $gesamt['m']+=$count[$item['gruppe']]['m'];
    if (get_lsp_group_gesamtpunkte($item) > 10) {
      $erfolg['w']+=$count[$item['gruppe']]['w'];
      $erfolg['m']+=$count[$item['gruppe']]['m'];
    }
  }
//  print_r($gesamt);
//  print_r($erfolg);
//  print_r($list);
//  print_r($count);
  $output='<h1>Statistik</h1>';
  $output.='<table>';
  $output.='<tr><th>Gruppen</th><td>'.get_lsp_group_count($db,$abnahme).'</td></tr>';
  $output.='<tr><th>&nbsp;</th><th>weiblich</th><th>m&auml;nnlich</th><th>gesamt</th></tr>';
  $output.='<tr><th>Bewerber</th><td>'.$gesamt['w'].'</td><td>'.$gesamt['m'].'</td><td>'.($gesamt['w']+$gesamt['m']).'</td></tr>';
  $output.='<tr><th>erfolgreich</th><td>'.$erfolg['w'].'</td><td>'.$erfolg['m'].'</td><td>'.($erfolg['w']+$erfolg['m']).'</td></tr>';
  $output.='</table>';
  return $output;  
}

?>
