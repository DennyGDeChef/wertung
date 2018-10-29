<?php

function get_competitions($db) {
  if (isset($_SESSION['_BENUTZER'])) {
  $query="SELECT wettbewerb.*,bundesland.name as land,landkreis.name as kreis,wettbewerbsart.name as artname, wettbewerbstyp.name as typname FROM wettbewerb LEFT JOIN bundesland on wettbewerb.land=bundesland.id LEFT JOIN landkreis on landkreis.id=wettbewerb.kreis LEFT JOIN wettbewerbsart on wettbewerb.art=wettbewerbsart.id LEFT JOIN wettbewerbstyp on wettbewerb.typ=wettbewerbstyp.id WHERE besitzer=".$_SESSION['_BENUTZER']." ORDER BY wettbewerb.datum DESC";
  if ($result = $db->query($query)) {
    $output=array(); 
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
  }
  return false;
}

function get_competition($db,$id) {
  if (isset($_SESSION['_BENUTZER'])) {
  if ($result = $db->query("SELECT wettbewerb.*,bundesland.name as land,landkreis.name as kreis,wettbewerbsart.name as artname, wettbewerbstyp.name as typname FROM wettbewerb LEFT JOIN bundesland on wettbewerb.land=bundesland.id LEFT JOIN landkreis on landkreis.id=wettbewerb.kreis LEFT JOIN wettbewerbsart on wettbewerb.art=wettbewerbsart.id LEFT JOIN wettbewerbstyp ON wettbewerb.typ=wettbewerbstyp.id WHERE wettbewerb.id=".$id." AND besitzer=".$_SESSION['_BENUTZER'])) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
  }
  return false;
}

function new_competition($db,$datum,$land,$kreis,$ort,$art,$typ) {
  global $error_output;
  if ($result = $db->query("INSERT wettbewerb SET datum='".$datum."', land='".$land."', kreis='".$kreis."', ort='".$ort."', art=".$art.", typ=".$typ)) {
    return true;
  }
  $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  return false;
}

function get_max_competition($db) {
  if ($result = $db->query("SELECT max(id) as id FROM wettbewerb")) {
    while ($line = $result->fetch_row()){
      return $line[0];
    }
    return false;
  }
  else return false;

}

function get_teams($db,$id,$sort) {
  $query="SELECT * FROM mannschaft WHERE wettbewerb=".$id." ORDER BY ".$sort.",name";
  if ($result = $db->query($query)) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function get_team($db,$id) {
  if ($result = $db->query("SELECT * FROM mannschaft WHERE id=".$id)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}

function get_teamtypes($db) {
  if ($result = $db->query("SELECT * FROM mannschaftstyp")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function get_teamtype($db,$id) {
  if ($result = $db->query("SELECT * FROM mannschaftstyp WHERE id=".$id)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}


function get_teammembers($db,$id) {
  if ($result = $db->query("SELECT * FROM mannschaftsmitglieder WHERE mannschaft=".$id." ORDER BY position ASC")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function set_teamage($db,$id) {
  global $error_output;
  $wbk=get_competitionkind($db,get_competition($db,get_team($db,$id)['wettbewerb'])['art']);
  if ($result = $db->query("SELECT YEAR(CURRENT_DATE())-YEAR(geburt) AS jahre FROM mannschaftsmitglieder WHERE einsatz=1 AND mannschaft=".$id)) {
    $alter=0;
    while ($line = $result->fetch_assoc()) {
      $alter+=$line['jahre'];
    }
    $alter=round($alter/$wbk['anzahl']);
    if (($alter < 10) || ($alter > 18)) {
      $alter='NULL';
      $error_output="Die Geburtsdaten der eben ge&auml;nderten Mannschaft ergeben kein g&uuml;ltiges Alter!";
    }
    if (!($db->query("UPDATE mannschaft SET `alter`=".$alter." WHERE id=".$id))) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: ".$db->error;
    }
  } else $error_output="(".__FUNCTION__.") Datenbankfehler: ".$db->error;
  return true;
}

function get_competitiontypes($db) {
  if ($result = $db->query("SELECT * FROM wettbewerbstyp")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function get_competitionkinds($db) {
  if ($result = $db->query("SELECT * FROM wettbewerbsart")) {
    $output=array();
    while ($line = $result->fetch_assoc()){
      array_push($output,$line);
    }
    return $output;
  }
  else return false;
}

function get_competitiontype($db,$id) {
  global $error_output;
  if ($result = $db->query("SELECT * FROM wettbewerbstyp WHERE id=".$id)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else {
    $error_output=$db->error;
    return false;
  }
}

function get_competitionkind($db,$id) {
  global $error_output;
  if ($result = $db->query("SELECT * FROM wettbewerbsart WHERE id=".$id)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else {
    $error_output="(".__FUNCTION__.") Datenbankfehler: ".$db->error;
    return false;
  }
}

function get_competitionspecs($db,$art,$typ) {
  global $error_output;
  if ($result = $db->query("SELECT * FROM wettbewerbsvorgaben WHERE art=".$art." AND typ=".$typ)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else {
    $error_output="(".__FUNCTION__.") Datenbankfehler: ".$db->error;
    return false;
  }
}

function set_vorgabezeit_b($db,$id) {
  global $error_output;
  $gruppe = get_team($db,$id);
  $wb = get_competition($db,$gruppe['wettbewerb']);
  $wbt = get_competitiontype($db,$wb['typ']);
  $wbk = get_competitionkind($db,$wb['art']);
  $specs = get_competitionspecs($db,$wbk['id'],$wbt['id']);
  $vorgabezeit_b = $specs['vorgabezeit_b_10'];
  if ($gruppe['alter'] == '') {
    $vorgabezeit_b = 'NULL';
  }
  else {
    for($i=10;$i<$gruppe['alter'];$i++) {
      $vorgabezeit_b -= $specs['vorgabezeit_intervall'];
    }
  }
  if (!($db->query("UPDATE mannschaft set vorgabezeit_b=".$vorgabezeit_b." WHERE id=".$id))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: ".$db->error;
  }
}

function get_rating($db,$id) {
  $wb=get_competition($db,get_team($db,$id)['wettbewerb']);
  $query="";
  if ($wb['art']==1) {
    $query="SELECT * FROM staffelwertung WHERE mannschaft=".$id;
  }
  if ($wb['art']==2) {
    $query="SELECT * FROM gruppenwertung WHERE mannschaft=".$id;
  }
  if ($result = $db->query($query)) {
    while ($line = $result->fetch_assoc()){
      return $line;
    }
    return false;
  }
  else return false;
}

function set_points($db,$id) {
  global $error_output;
  $rat=get_rating($db,$id);
  $grp=get_team($db,$id);
  $wb=get_competition($db,$grp['wettbewerb']);
  if ($grp['alter']=='' || $grp['vorgabezeit_b']=='' || $rat['zeit_a']==0 || $rat['zeit_b']==0) {
    $punkte_a='NULL';
    $punkte_b='NULL';
    $punkte_gesamt='NULL';
  }
  else {
    $gesamteindruck=0;
    $punkte_a=1000;
    $punkte_b=400;
    switch ($wb['art']) {
      case 1:
        $punkte_a=$punkte_a-$rat['ef_f']-$rat['ma_f']-$rat['at_f']-$rat['wt_f']-$rat['zeittakt_a'];
        if ($rat['zeit_a'] > $grp['vorgabezeit_a']) $punkte_a=$punkte_a-($rat['zeit_a']-$grp['vorgabezeit_a']);
        $gesamteindruck=$gesamteindruck+$rat['ef_e']+$rat['ma_e']+$rat['at_e']+$rat['wt_e'];
        $punkte_b=$punkte_b-$rat['l1_f']-$rat['l2_f']-$rat['l3_f']-$rat['l4_f']-$rat['l5_f']-$rat['l6_f']+($grp['vorgabezeit_b']-$rat['zeit_b']);
        $gesamteindruck=$gesamteindruck+$rat['l1_e']+$rat['l2_e']+$rat['l3_e']+$rat['l4_e']+$rat['l5_e']+$rat['l6_e'];
        $gesamteindruck=round($gesamteindruck/10,1);
        break;
      case 2:
        $punkte_a=$punkte_a-$rat['ef_f']-$rat['ma_f']-$rat['at_f']-$rat['wt_f']-$rat['st_f']-$rat['zeittakt_a'];
        if ($rat['zeit_a'] > $grp['vorgabezeit_a']) $punkte_a=$punkte_a-($rat['zeit_a']-$grp['vorgabezeit_a']);
        $gesamteindruck=$gesamteindruck+$rat['ef_e']+$rat['ma_e']+$rat['at_e']+$rat['wt_e']+$rat['st_e'];
        $punkte_b=$punkte_b-$rat['l1_f']-$rat['l2_f']-$rat['l3_f']-$rat['l4_f']-$rat['l5_f']-$rat['l6_f']-$rat['l7_f']-$rat['l8_f']-$rat['l9_f']+($grp['vorgabezeit_b']-$rat['zeit_b']);
        $gesamteindruck=$gesamteindruck+$rat['l1_e']+$rat['l2_e']+$rat['l3_e']+$rat['l4_e']+$rat['l5_e']+$rat['l6_e']+$rat['l7_e']+$rat['l8_e']+$rat['l9_e'];
        $gesamteindruck=round($gesamteindruck/14,1);
        break;
    }
    $punkte_gesamt=$punkte_a+$punkte_b-$gesamteindruck;
  }
  $query="UPDATE mannschaft set punkte_a=".$punkte_a.", punkte_b=".$punkte_b.", punkte_gesamt=".$punkte_gesamt." WHERE id=".$id;
  if (!($db->query($query))) {
    $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
  }
}

function get_max_startnummer($db,$wb) {
  global $error_output;
  if (!($result=$db->query("SELECT max(startnummer) as max FROM mannschaft WHERE wettbewerb=".$wb))) {
    $error_output="Datenbankfehler: " . $db->error;
  }
  return($result->fetch_row()[0]+1);
}

function rem_competition($db,$id) {
  global $error_output;
  $wb=get_competition($db,$id);
  $teams=get_teams($db,$id,$_SESSION['sort']);
  if ($teams) {
	foreach ($teams as $team) {
		rem_team($db,$team['id']);
	}
  }
  $teams=get_teams($db,$id,$_SESSION['sort']);
  if ($teams) {
    $error_output="Irgendwas ging gewaltig schief beim Löschen...";
	return false;
  }
  else {
	if (!($result=$db->query("DELETE FROM wettbewerb WHERE id=".$id))) {
                $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
		return false;
	}
	select_competition('null');
	return true;	
  }
  $error_output="(".__FUNCTION__.") Wenn man das hier lesen kann, ging irgendwas total schief beim Löschen vom Wettbewerb.. Ein Programmierer sollte davon erfahren!";
  return false;
}

function rem_team($db,$id) {
  global $error_output;
  $wb=get_competition($db,$_SESSION['WB']);
  $continue=false;
  switch ($wb['art']) {
	case 1:
		if (!($result=$db->query("DELETE FROM staffelwertung WHERE mannschaft=".$id))) {
                        $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
			return false;
		}
		$continue=true;
		break;
	case 2:
		if (!($result=$db->query("DELETE FROM gruppenwertung WHERE mannschaft=".$id))) {
                        $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
			return false;
		}
		$continue=true;
		break;
  }
  if ($continue) {
	if (!($result=$db->query("DELETE FROM mannschaftsmitglieder WHERE mannschaft=".$id))) {
                $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
		return false;
	}
  else
	if (!($result=$db->query("DELETE FROM mannschaft WHERE id=".$id))) {
                $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
		return false;
	}
	return true;
  }
  $error_output="(".__FUNCTION__.") Wenn man das hier lesen kann, ging irgendwas total schief beim Löschen der Mannschaft. Ein Programmierer sollte davon erfahren!";
  return false;
}

function form_select_competition($db) {
  if ($wbs = get_competitions($db)) {
  $output='<h1>Wettbewerb ausw&auml;hlen</h1>';
  $output.='<table class="wbselecttable">
  <tr><th>Datum</th><th>Land</th><th>Kreis</th><th>Ort</th><th>Art</th><th>Typ</th></tr>';
  foreach ($wbs as $wb) {
    $output.='<tr><td>
	<form action="index.php" method="POST" id="selectwb'.$wb['id'].'"><input type="hidden" name="do" value="selectwb"><input type="hidden" name="wettbewerb" value="'.$wb['id'].'">
              <input class="selectwb" type="submit" value="'.date('d.m.Y',strtotime($wb['datum'])).'">
    </form>';
	$output.="</td><td>".$wb['land']."</td><td>".$wb['kreis']."</td><td>".$wb['ort']."</td><td>".$wb['artname']."</td><td>".$wb['typname']."</td></tr>";
  }
  $output.='</table>';  
  return $output;
  }
  return false;
}

function form_create_competition($db) {
  if ($wbtps=get_competitiontypes($db)) {
    if ($wbarts=get_competitionkinds($db)) {
      if ($blr=get_bundeslaender($db)) {
  $output ='<h1>Wettbewerb anlegen</h1>
  <form action="index.php" method="POST" id="newwb">
  <input type="hidden" name="do" value="createwb">
  <table>
  <tr><th>Datum</th><th>Land</th><th>Kreis</th><th>Ort</th></tr>
  <tr><td><input type="date" name="datum" value="'.date('d.m.Y').'"></td>
    <td><select name="land">';
        foreach ($blr as $bl) {
          $output.='<option value="'.$bl['name'].'"';
          if ($bl['id']==6) $output.=' selected';
          $output.='>';
          $output.=$bl['name'].'</option>';
        }
        $output.='
        </select></td>
    <td><input type="text" name="kreis"></td>
    <td><input type="text" name="ort"></td>
  </tr>
  <tr><th colspan="2">Art</th><th colspan="2">Typ</th></tr>
  <tr>
    <td colspan="2"><select name="art" size="1">';
    foreach ($wbarts as $wbart) {
      $output.='<option value="'.$wbart['id'].'"';
      if ($wbart['id']==$wb['art']) $output.=' selected';
      $output.='>'.$wbart['name'].'</option>';
    }
    $output.='</select></td>
    <td colspan="2"><select name="typ" size="1">';
    foreach ($wbtps as $wbtp) {
      $output.='<option value="'.$wbtp['id'].'"';
      if ($wbtp['id']==$wb['typ']) $output.=' selected';
      $output.='>'.$wbtp['name'].'</option>';
    }
    $output.='</select></td>
  </tr>
  <tr><td><input type="submit" value="Anlegen"></td><td colspan="5">&nbsp;</tr>
  </table>
  </form>';
  return $output;
  }
  return false;
  }
  return false;
  }
  return false;
}

function button_deselect_competition() {
  $output='<form action="index.php" method="POST" id="deselectwb">
  <input type="hidden" name="do" value="selectwb">
  <input type="hidden" name="wettbewerb" value="null">
  <input class="menubutton" type="submit" value="Wettbewerb ausw&auml;hlen"></form>';
  return $output;
}

function form_show_competition_teams($db,$wid,$sort) {
  if ($wb=get_competition($db,$wid)) {
    $output='<h1>'.$wb['artname'].'</h1>';
    $output.='<h2>'.date('d.m.Y',strtotime($wb['datum'])).' '.$wb['ort'].', '.$wb['kreis'].' ('.$wb['land'].') - '.$wb['typname'].'</h2>';
    if ($grps=get_teams($db,$wid,$sort)) {
        $output.='<table>
        <tr><th><a href="index.php?sort=startnummer">Start Nr</a></th><th>Name</th><th>Alter</th><th><a href="index.php?sort=punkte">Punkte</a></th><th colspan="3">&nbsp;</th></tr>';
        foreach ($grps as $grp) {
          $output.='<tr><td>'.$grp['startnummer'].'</td>
          <td><form action="index.php" method="POST" id="editgrp"><input type="hidden" name="do" value="editgrp"><input type="hidden" name="gruppe" value="'.$grp['id'].'">
              <input class="shwedtgrp" type="submit" value="'.$grp['name'].'">
          </form></td>
          <td>'.$grp['alter'].'</td>
          <td>'.$grp['punkte_gesamt'].'</td>
          <td><form action="index.php" method="POST" id="editgrpmember">
            <input type="hidden" name="do" value="editgrpmembers"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="edtgrpmbr" type="submit" value="Teilnehmer">
          </form></td>
          <td><form action="index.php" method="POST" id="rategrp">
            <input type="hidden" name="do" value="rategrp"><input type="hidden" name="gruppe" value="'.$grp['id'].'"><input class="rtggrp" type="submit" value="Wertungsbogen">
          </form></td></tr>';
        }
        $output.='</table>';
    }
    return $output;
  }
  return false;
}

function button_create_competition_team() {
  $output='<form action="index.php" method="POST" id="addgrp">
  <input type="hidden" name="do" value="addgrp">
  <input class="menubutton" type="submit" value="Neue Mannschaft"></form>';
  return $output;
}

function form_create_competition_team($db,$wid) {
  $wb=get_competition($db,$wid);
  $grptps=get_teamtypes($db);
  $output='<h1>Neue Mannschaft anlegen</h1>
  <h2>'.date('d.m.Y',strtotime($wb['datum'])).' '.$wb['ort'].', '.$wb['kreis'].' ('.$wb['land'].') - '.$wb['typname'].'</h2>
  <form action="index.php" method="POST" id="addgrp">
    <input type="hidden" name="do" value="insertgrp">
    <table>
      <tr><th>Start Nummer</th><th>Name</th><th>Typ</th></tr> 
      <tr><td><input name="startnummer" value="'.get_max_startnummer($db,$_SESSION['WB']).'"></td>
          <td><input name="name"></td>
          <td><select name="typ" size="1">';
          foreach ($grptps as $grptp) {
            $output.='<option value="'.$grptp['id'].'"';
            if ($grptp['id']==1) $output.=' selected';
            $output.='>'.$grptp['name'].'</option>';
          }
          $output.='</select></td>
      </tr>
      <tr><td><input class="button" type="submit" value="OK"></td></tr>
   </table></form>';
  return $output;
}

function form_edit_competition_team($db,$gid) {
  $grp=get_team($db,$gid);
  $grptps=get_teamtypes($db);
  $output='<h1>'.$grp['name'].' editieren</h1>
  <form action="index.php" method="POST" id="editgrp">
    <input type="hidden" name="do" value="modifygrp">
    <input type="hidden" name="id" value="'.$grp['id'].'">
    <table>
      <tr><th>Start Nummer</th><th>Name</th><th>Typ</th></tr>
      <tr>
        <td><input name="startnummer" value="'.$grp['startnummer'].'"></td>
        <td><input name="name" value="'.$grp['name'].'"></td>
        <td><select name="typ" size="1">';
        foreach ($grptps as $grptp) {
          $output.='<option value="'.$grptp['id'].'"';
          if ($grptp['id']==$grp['typ']) $output.=' selected';
          $output.='>'.$grptp['name'].'</option>';
        }
  $output.='</select></td></tr>
      <tr> <td><input class="button" type="submit" value="OK"></td></tr>
      </table>
  </form>';
  return $output;
}

function button_delete_competition_team($id) {
  $output.='<form action="index.php" method="POST" id="delgrp">
  <input type="hidden" name="do" value="removegrp"> 
  <input type="hidden" name="removeid" value="'.$id.'">
  <input class="menubutton" type="submit" value="!! Mannschaft L&ouml;schen !!" onClick="return confirm('."'Sicher?'".')">
  </form>';
  return $output;
}

function button_delete_competition() {
  $output.='<form action="index.php" method="POST" id="delwb">
  <input type="hidden" name="do" value="removewb"> 
  <input type="hidden" name="removeid" value="'.$_SESSION['WB'].'">
  <input class="menubutton" type="submit" value="!! Wettbewerb L&ouml;schen !!" onClick="return (confirm('."'Sicher?'".') && confirm('."'Wirklich Sicher?'".'))">
  </form>';
  return $output;
}

function form_edit_team_members($db,$gid) {
  $grp=get_team($db,$gid);
  $grpmbs=get_teammembers($db,$gid);
  $wb=get_competition($db,$grp['wettbewerb']);
  $wbk=get_competitionkind($db,$wb['art']);
  $teamsize=$wbk['anzahl']+$wbk['ersatz'];
  $output='<h1>Teilnehmerliste '.$grp['name'].'</h1>
  <form action="index.php" method="POST" id="editgrpmembers">
    <input type="hidden" name="do" value="modifygrpmembers">
    <input type="hidden" name="id" value="'.$grp['id'].'">
    <table class="grpmbs">
      <tr><th>Einsatz</th><th>Position</th><th>Name</th><th>Vorname</th><th>Geburtsdatum</th></tr>';
      if (count($grpmbs)>0) {
        foreach($grpmbs as $grpmb) {
          $output.='<tr><td><input type="hidden" name="neu[]" value="off"><input class="e" type="checkbox" name="einsatz['.($grpmb['position']-1).']"';
          if ($grpmb['einsatz']>0) $output.=' checked';
          $output.='></td>
          <td><input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$grpmb['position'].'"></td>
          <td><input class="sn" type="text" name="name[]" value="'.$grpmb['name'].'"></td>
          <td><input class="gn" type="text" name="vorname[]" value="'.$grpmb['vorname'].'"></td>
          <td><input class="dt" type="date" name="geburt[]" value="'.date('Y-m-d',strtotime($grpmb['geburt'])).'"></td>
          </tr>';
        }
      }
      for ($i=count($grpmbs)+1;$i<=$teamsize;$i++) {
        $output.='<tr>
        <td><input type="hidden" name="neu[]" value="on"><input class="e" type="checkbox" name="einsatz['.($i-1).']"';
        if ($i<$teamsize) $output.=' checked';
        $output.='></td>
        <td><input class="nr" type="number" min="1" max="'.$teamsize.'" name="position[]" value="'.$i.'"></td>
        <td><input class="sn" type="text" name="name[]" value=""></td>
        <td><input class="gn" type="text" name="vorname[]" value=""></td>
        <td><input class="dt" type="date" name="geburt[]" value=""></td>
        </tr>';
      }
      $output.='<tr><td><input class="button" type="submit" value="OK"></td></tr>
      </table></form>';
  return $output;
}

function form_rate_team($db,$gid) {
  $grp=get_team($db,$gid);
  $wb=get_competition($db,$grp['wettbewerb']);
  $rating=get_rating($db,$gid);
  $output.= '<h1>Wertungsbogen '.$grp['name'].'</h1>
    <form action="index.php" method="POST" id="rategrp">
      <input type="hidden" name="do" value="modifyrate">
      <input type="hidden" name="id" value="'.$grp['id'].'">
      <input type="hidden" name="neu" value="'.(!$rating ? 'on' : 'off').'">
      <table>';
      if (!$rating) {
        switch ($wb['art']) {
          case 1: 
            $rating=array(
              'gruppe'=>$_POST['gruppe'],
              'ef_e'       => 1,
              'ef_f'       => 0,
              'ma_e'       => 1,
              'ma_f'       => 0,
              'at_e'       => 1,
              'at_f'       => 0,
              'wt_e'       => 1,
              'wt_f'       => 0,
              'zeit_a'     => 0,
              'zeittakt_a' => 0,
              'l1_e'       => 1,
              'l1_f'       => 0,
              'l2_e'       => 1,
              'l2_f'       => 0,
              'l3_e'       => 1,
              'l3_f'       => 0,
              'l4_e'       => 1,
              'l4_f'       => 0,
              'l5_e'       => 1,
              'l5_f'       => 0,
              'l6_e'       => 1,
              'l6_f'       => 0,
              'zeit_b'     => 0
            );
          break;
          case 2:
            $rating=array(
              'gruppe'=>$_POST['gruppe'],
              'ef_e'       => 1,
              'ef_f'       => 0,
              'ma_e'       => 1,
              'ma_f'       => 0,
              'at_e'       => 1,
              'at_f'       => 0,
              'wt_e'       => 1,
              'wt_f'       => 0,
              'st_e'       => 1,
              'st_f'       => 0,
              'zeit_a'     => 0,
              'zeittakt_a' => 0,
              'l1_e'       => 1,
              'l1_f'       => 0,
              'l2_e'       => 1,
              'l2_f'       => 0,
              'l3_e'       => 1,
              'l3_f'       => 0,
              'l4_e'       => 1,
              'l4_f'       => 0,
              'l5_e'       => 1,
              'l5_f'       => 0,
              'l6_e'       => 1,
              'l6_f'       => 0,
              'l7_e'       => 1,
              'l7_f'       => 0,
              'l8_e'       => 1,
              'l8_f'       => 0,
              'l9_e'       => 1,
              'l9_f'       => 0,
              'zeit_b'     => 0
            );
          break;
         }
      }
  switch ($wb['art']) {
    case 1:
      $output.='<tr><td colspan="3"><h2>A-Teil</h2></td></tr>
      <tr><th>Position</th><th>Eindruck</th><th>Fehler</th></tr>
      <tr><td>Einheitsf&uuml;hrer</td><td><input type="number" min="1" max="5" step="2" name="ef_e" value="'.$rating['ef_e'].'"></td><td><input type="number" name="ef_f" value="'.$rating['ef_f'].'"></td></tr>
      <tr><td>Maschinist</td><td><input type="number" min="1" max="5" step="2" name="ma_e" value="'.$rating['ma_e'].'"></td><td><input type="number" name="ma_f" value="'.$rating['ma_f'].'"></td></tr>
      <tr><td>Angriffstrupp</td><td><input type="number" min="1" max="5" step="2" name="at_e" value="'.$rating['at_e'].'"></td><td><input type="number" name="at_f" value="'.$rating['at_f'].'"></td></tr>
      <tr><td>Wassertrupp</td><td><input type="number" min="1" max="5" step="2" name="wt_e" value="'.$rating['wt_e'].'"></td><td><input type="number" name="wt_f" value="'.$rating['wt_f'].'"></td></tr>
      <tr><th>Zeit</th><th>Minuten</th><th>Sekunden</th></tr>
      <tr><td>Gesamt</td><td><input type="number" name="zeit_a_min" value="'.intval($rating['zeit_a']/60).'"></td><td><input type="number" name="zeit_a_sek" value="'.($rating['zeit_a']%60).'"></td></tr>
      <tr><td>Zeittakt</td><td><input type="number" name="zeittakt_a_min" value="'.intval($rating['zeittakt_a']/60).'"></td><td><input type="number" name="zeittakt_a_sek" value="'.($rating['zeittakt_a']%60).'"></td></tr>
      <tr><td colspan="3"><h2>B-Teil</h2></td></tr>
      <tr><th>Position</th><th>Eindruck</th><th>Fehler</th></tr>
      <tr><td>L&auml;ufer 1</td><td><input type="number" min="1" max="5" step="2" name="l1_e" value="'.$rating['l1_e'].'"></td><td><input type="number" name="l1_f" value="'.$rating['l1_f'].'"></td></tr>
      <tr><td>L&auml;ufer 2</td><td><input type="number" min="1" max="5" step="2" name="l2_e" value="'.$rating['l2_e'].'"></td><td><input type="number" name="l2_f" value="'.$rating['l2_f'].'"></td></tr>
      <tr><td>L&auml;ufer 3</td><td><input type="number" min="1" max="5" step="2" name="l3_e" value="'.$rating['l3_e'].'"></td><td><input type="number" name="l3_f" value="'.$rating['l3_f'].'"></td></tr>
      <tr><td>L&auml;ufer 4</td><td><input type="number" min="1" max="5" step="2" name="l4_e" value="'.$rating['l4_e'].'"></td><td><input type="number" name="l4_f" value="'.$rating['l4_f'].'"></td></tr>
      <tr><td>L&auml;ufer 5</td><td><input type="number" min="1" max="5" step="2" name="l5_e" value="'.$rating['l5_e'].'"></td><td><input type="number" name="l5_f" value="'.$rating['l5_f'].'"></td></tr>
      <tr><td>L&auml;ufer 6</td><td><input type="number" min="1" max="5" step="2" name="l6_e" value="'.$rating['l6_e'].'"></td><td><input type="number" name="l6_f" value="'.$rating['l6_f'].'"></td></tr>
      <tr><th>Zeit</th><th>Minuten</th><th>Sekunden</th></tr>
      <tr><td>Soll</td><td>'.intval($grp['vorgabezeit_b']/60).'</td><td>'.($grp['vorgabezeit_b']%60).'</td></tr>
      <tr><td>Ist</td><td><input type="number" name="zeit_b_min" value="'.intval($rating['zeit_b']/60).'"></td><td><input type="number" name="zeit_b_sek" value="'.($rating['zeit_b']%60).'"></td></tr>
      <tr><td><input class="button" type="submit" value="OK"></td></tr>';
      break;
    case 2:
      $output.='<tr><td colspan="3"><h2>A-Teil</h2></td></tr>
      <tr><th>Position</th><th>Eindruck</th><th>Fehler</th></tr>
      <tr><td>Einheitsf&uuml;hrer/Melder</td><td><input type="number" min="1" max="5" step="2" name="ef_e" value="'.$rating['ef_e'].'"></td><td><input type="number" name="ef_f" value="'.$rating['ef_f'].'"></td></tr>
      <tr><td>Maschinist</td><td><input type="number" min="1" max="5" step="2" name="ma_e" value="'.$rating['ma_e'].'"></td><td><input type="number" name="ma_f" value="'.$rating['ma_f'].'"></td></tr>
      <tr><td>Angriffstrupp</td><td><input type="number" min="1" max="5" step="2" name="at_e" value="'.$rating['at_e'].'"></td><td><input type="number" name="at_f" value="'.$rating['at_f'].'"></td></tr>
      <tr><td>Wassertrupp</td><td><input type="number" min="1" max="5" step="2" name="wt_e" value="'.$rating['wt_e'].'"></td><td><input type="number" name="wt_f" value="'.$rating['wt_f'].'"></td></tr>
      <tr><td>Schlauchtrupp</td><td><input type="number" min="1" max="5" step="2" name="st_e" value="'.$rating['st_e'].'"></td><td><input type="number" name="st_f" value="'.$rating['st_f'].'"></td></tr>
      <tr><th>Zeit</th><th>Minuten</th><th>Sekunden</th></tr>
      <tr><td>Gesamt</td><td><input type="number" name="zeit_a_min" value="'.intval($rating['zeit_a']/60).'"></td><td><input type="number" name="zeit_a_sek" value="'.($rating['zeit_a']%60).'"></td></tr>
      <tr><td>Zeittakt</td><td><input type="number" name="zeittakt_a_min" value="'.intval($rating['zeittakt_a']/60).'"></td><td><input type="number" name="zeittakt_a_sek" value="'.($rating['zeittakt_a']%60).'"></td></tr>
      <tr><td colspan="3"><h2>B-Teil</h2></td></tr>
      <tr><th>Position</th><th>Eindruck</th><th>Fehler</th></tr>
      <tr><td>L&auml;ufer 1</td><td><input type="number" min="1" max="5" step="2" name="l1_e" value="'.$rating['l1_e'].'"></td><td><input type="number" name="l1_f" value="'.$rating['l1_f'].'"></td></tr>
      <tr><td>L&auml;ufer 2</td><td><input type="number" min="1" max="5" step="2" name="l2_e" value="'.$rating['l2_e'].'"></td><td><input type="number" name="l2_f" value="'.$rating['l2_f'].'"></td></tr>
      <tr><td>L&auml;ufer 3</td><td><input type="number" min="1" max="5" step="2" name="l3_e" value="'.$rating['l3_e'].'"></td><td><input type="number" name="l3_f" value="'.$rating['l3_f'].'"></td></tr>
      <tr><td>L&auml;ufer 4</td><td><input type="number" min="1" max="5" step="2" name="l4_e" value="'.$rating['l4_e'].'"></td><td><input type="number" name="l4_f" value="'.$rating['l4_f'].'"></td></tr>
      <tr><td>L&auml;ufer 5</td><td><input type="number" min="1" max="5" step="2" name="l5_e" value="'.$rating['l5_e'].'"></td><td><input type="number" name="l5_f" value="'.$rating['l5_f'].'"></td></tr>
      <tr><td>L&auml;ufer 6</td><td><input type="number" min="1" max="5" step="2" name="l6_e" value="'.$rating['l6_e'].'"></td><td><input type="number" name="l6_f" value="'.$rating['l6_f'].'"></td></tr>
      <tr><td>L&auml;ufer 7</td><td><input type="number" min="1" max="5" step="2" name="l7_e" value="'.$rating['l7_e'].'"></td><td><input type="number" name="l7_f" value="'.$rating['l7_f'].'"></td></tr>
      <tr><td>L&auml;ufer 8</td><td><input type="number" min="1" max="5" step="2" name="l8_e" value="'.$rating['l8_e'].'"></td><td><input type="number" name="l8_f" value="'.$rating['l8_f'].'"></td></tr>
      <tr><td>L&auml;ufer 9</td><td><input type="number" min="1" max="5" step="2" name="l9_e" value="'.$rating['l9_e'].'"></td><td><input type="number" name="l9_f" value="'.$rating['l9_f'].'"></td></tr>
      <tr><th>Zeit</th><th>Minuten</th><th>Sekunden</th></tr>
      <tr><td>Soll</td><td>'.intval($grp['vorgabezeit_b']/60).'</td><td>'.($grp['vorgabezeit_b']%60).'</td></tr>
      <tr><td>Ist</td><td><input type="number" name="zeit_b_min" value="'.intval($rating['zeit_b']/60).'"></td><td><input type="number" name="zeit_b_sek" value="'.($rating['zeit_b']%60).'"></td></tr>
      <tr><td><input class="button" type="submit" value="OK"></td></tr>';
      break;
 }
  $output.='  </table>
    </form>';
  return $output;
}

function get_winnerlist($db) {
  $wb=get_competition($db,$_SESSION['WB']);
  switch ($wb['art']) {
    case 1:
          $query="SELECT mannschaft.id, mannschaft.typ, mannschaft.name, mannschaft.punkte_gesamt, (gw.ef_f+ma_f+at_f+wt_f) as fehler_a, (gw.ef_f+ma_f+at_f+wt_f + gw.zeittakt_a + IF(gw.zeit_a>mannschaft.vorgabezeit_a,gw.zeit_a-vorgabezeit_a,0)) as minuspunkte_a, mannschaft.punkte_b as punkte_b, (gw.l1_f +gw.l2_f +gw.l3_f +gw.l4_f +gw.l5_f +gw.l6_f) as fehler_b, gw.zeittakt_a as zeittakt from mannschaft left join staffelwertung as gw on mannschaft.id=gw.mannschaft WHERE wettbewerb=".$_SESSION['WB']." ORDER BY mannschaft.typ, punkte_gesamt DESC, fehler_a ASC, minuspunkte_a ASC, punkte_b DESC, fehler_b ASC, zeittakt ASC";
          break;
    case 2:
          $query="SELECT mannschaft.id, mannschaft.typ, mannschaft.name, mannschaft.punkte_gesamt, (gw.ef_f+ma_f+at_f+wt_f+st_f) as fehler_a, (gw.ef_f+ma_f+at_f+wt_f+st_f + gw.zeittakt_a + IF(gw.zeit_a>mannschaft.vorgabezeit_a,gw.zeit_a-vorgabezeit_a,0)) as minuspunkte_a, mannschaft.punkte_b as punkte_b, (gw.l1_f +gw.l2_f +gw.l3_f +gw.l4_f +gw.l5_f +gw.l6_f +gw.l7_f + gw.l8_f + gw.l9_f) as fehler_b, gw.zeittakt_a as zeittakt from mannschaft left join gruppenwertung as gw on mannschaft.id=gw.mannschaft WHERE wettbewerb=".$_SESSION['WB']." ORDER BY mannschaft.typ, punkte_gesamt DESC, fehler_a ASC, minuspunkte_a ASC, punkte_b DESC, fehler_b ASC, zeittakt ASC";
          break;
  }
  if (!($result=$db->query($query))) {
    return false;
  }
  $list=array();
  while ($line = $result->fetch_assoc()) {
    array_push($list,$line);
  }
  return $list;
}

function select_competition($wb) {
  if ($wb=='null') {
    unset($_SESSION['WB']);
    unset($_SESSION['LSP']);
  }
  else {
    if ($wb=='lsp') {
      $_SESSION['WB']="lsp";
    }
    elseif (is_int((int)$wb)) {
      $_SESSION['WB']=(int)$wb;
    }
    else {
      exit('Keine Zahl?');
    }
  }
}

function create_competition($db,$datum,$land,$kreis,$ort,$art,$typ) {
  global $error_output;
  if (!(trim($datum=='')) && !(trim($land=='')) &&!(trim($kreis=='')) &&!(trim($ort=='')) &&!(trim($art=='')) &&!(trim($typ==''))) {
    new_competition($db,date('Y-m-d',strtotime($datum)),$land,$kreis,$ort,$art,$typ);
    select_competition(get_max_competition($db));
    return true;
  }
  $error_output="Das hat nicht geklappt, weil nicht alle Felder korrekt ausgef&uuml;llt wurden.";
  return false;
}

function insert_team($db) {
    global $error_output;
    if (!(trim($_POST['name'])=='')) {
      $wb=get_competition($db,$_SESSION['WB']);
      $specs=get_competitionspecs($db,$wb['art'],$wb['typ']);
      $query="INSERT mannschaft SET wettbewerb=".$_SESSION['WB'].", startnummer=".$_POST['startnummer'].", name='".$_POST['name']."', typ=".$_POST['typ'].", vorgabezeit_a=".$specs['vorgabezeit_a'];
      if (!($db->query($query))) {
        $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      }
    } else {
      $error_output='Ohne Name kann keine neue Mannschaft angelegt werden!';
    }
}

function modify_team($db,$grp,$startnummer,$name,$typ) {
    global $error_output;
    if (!($db->query("UPDATE mannschaft SET startnummer=".((int)$startnummer).", name='".$name."', typ=".((int)$typ)." WHERE id=".$grp) === TRUE)) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
      return false;
    }
    return true;
}

function modify_team_members($db) {
  global $error_output;
  $grp=get_team($db,$_POST['id']);
  $wb=get_competition($db,$grp['wettbewerb']);
  $wbk=get_competitionkind($db,$wb['art']);
  $teamsize=$wbk['anzahl']+$wbk['ersatz'];
  for ($i=0;$i<$teamsize;$i++) {
    if ($_POST['einsatz'][$i]=='on') $einsatz=1; else $einsatz=0;
    if ($_POST['neu'][$i]=='off') {
      $query="UPDATE mannschaftsmitglieder SET einsatz=".$einsatz.", name='".$_POST['name'][$i]."', vorname='".$_POST['vorname'][$i]."', geburt='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."' WHERE mannschaft=".$_POST['id']." AND position=".$_POST['position'][$i];
    } else {
      $query="INSERT mannschaftsmitglieder SET position=".$_POST['position'][$i].", einsatz=".$einsatz.", name='".$_POST['name'][$i]."', vorname='".$_POST['vorname'][$i]."', geburt='".date('Y-m-d',strtotime($_POST['geburt'][$i]))."', mannschaft=".$_POST['id'];
    }
    if (!($db->query($query))) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    }
  }
  set_teamage($db,$_POST['id']);
  set_vorgabezeit_b($db,$_POST['id']);
  set_points($db,$_POST['id']);
  unset($_POST);
}

function modify_rating($db) {
    global $error_output;
    $wb=get_competition($db,get_team($db,$_POST['id'])['wettbewerb']);
    switch ($wb['art']) {
      case 1:
        if ($_POST['neu']=='on') {
          $query="INSERT staffelwertung SET mannschaft=".$_POST['id'].", ";
        }
        else {
          $query="UPDATE staffelwertung SET ";
        }
        $query.="ef_e=".$_POST['ef_e'].", ef_f=".$_POST['ef_f'].",
                 ma_e=".$_POST['ma_e'].", ma_f=".$_POST['ma_f'].",
                 at_e=".$_POST['at_e'].", at_f=".$_POST['at_f'].",
                 wt_e=".$_POST['wt_e'].", wt_f=".$_POST['wt_f'].",
                 zeit_a=".($_POST['zeit_a_min']*60+$_POST['zeit_a_sek']).",
                 zeittakt_a=".($_POST['zeittakt_a_min']*60+$_POST['zeittakt_a_sek']).",
                 l1_e=".$_POST['l1_e'].", l1_f=".$_POST['l1_f'].",
                 l2_e=".$_POST['l2_e'].", l2_f=".$_POST['l2_f'].",
                 l3_e=".$_POST['l3_e'].", l3_f=".$_POST['l3_f'].",
                 l4_e=".$_POST['l4_e'].", l4_f=".$_POST['l4_f'].",
                 l5_e=".$_POST['l5_e'].", l5_f=".$_POST['l5_f'].",
                 l6_e=".$_POST['l6_e'].", l6_f=".$_POST['l6_f'].",
                 zeit_b=".($_POST['zeit_b_min']*60+$_POST['zeit_b_sek'])
         ;
         if ($_POST['neu']=='off') {
           $query.=" WHERE mannschaft=".$_POST['id'];
         }
       break;
      case 2:
        if ($_POST['neu']=='on') {
          $query="INSERT gruppenwertung SET mannschaft=".$_POST['id'].", ";
        }
        else {
          $query="UPDATE gruppenwertung SET ";
        }
        $query.="ef_e=".$_POST['ef_e'].", ef_f=".$_POST['ef_f'].",
                 ma_e=".$_POST['ma_e'].", ma_f=".$_POST['ma_f'].",
                 at_e=".$_POST['at_e'].", at_f=".$_POST['at_f'].",
                 wt_e=".$_POST['wt_e'].", wt_f=".$_POST['wt_f'].",
                 st_e=".$_POST['st_e'].", st_f=".$_POST['st_f'].",
                 zeit_a=".($_POST['zeit_a_min']*60+$_POST['zeit_a_sek']).",
                 zeittakt_a=".($_POST['zeittakt_a_min']*60+$_POST['zeittakt_a_sek']).",
                 l1_e=".$_POST['l1_e'].", l1_f=".$_POST['l1_f'].",
                 l2_e=".$_POST['l2_e'].", l2_f=".$_POST['l2_f'].",
                 l3_e=".$_POST['l3_e'].", l3_f=".$_POST['l3_f'].",
                 l4_e=".$_POST['l4_e'].", l4_f=".$_POST['l4_f'].",
                 l5_e=".$_POST['l5_e'].", l5_f=".$_POST['l5_f'].",
                 l6_e=".$_POST['l6_e'].", l6_f=".$_POST['l6_f'].",
                 l7_e=".$_POST['l7_e'].", l7_f=".$_POST['l7_f'].",
                 l8_e=".$_POST['l8_e'].", l8_f=".$_POST['l8_f'].",
                 l9_e=".$_POST['l9_e'].", l9_f=".$_POST['l9_f'].",
                 zeit_b=".($_POST['zeit_b_min']*60+$_POST['zeit_b_sek'])
         ;
         if ($_POST['neu']=='off') {
           $query.=" WHERE mannschaft=".$_POST['id'];
         }
       break;
     }
    if (!($db->query($query))) {
      $error_output="(".__FUNCTION__.") Datenbankfehler: " . $db->error;
    }
    set_points($db,$_POST['id']);
}

function button_show_winnerlist() {
  $output='<form action="index.php" method="POST" id="showwins">
  <input type="hidden" name="do" value="showwins">
  <input class="menubutton" type="submit" value="Siegerliste"></form>';
  return $output;
}

function show_winnerlist($db) {
  $list=get_winnerlist($db);
  $wb=get_competition($db,$_SESSION['WB']);
  print_r($tt);
  $platz=0;
  $typ=0;
  $output='<h1>Platzierungen</h1>
  <h2>'.$wb['artname'].'</h2>
  <h3>'.date('d.m.Y',strtotime($wb['datum'])).' '.$wb['ort'].', '.$wb['kreis'].' ('.$wb['land'].') - '.$wb['typname'].'</h3>
  <table>';
  foreach($list as $place) {
    if ($place['typ']!=$typ) {
      $typ=$place['typ'];
      $platz=0;
      $output.='<tr><th colspan="3">Wertung '.get_teamtype($db,$typ)['name'].'</th></tr>';
      $output.='<tr><th>Platz</th><th>Mannschaft</th><th>Punkte</th></tr>';
    }
    $platz++;
    $output.='<tr><td>'.$platz.'</td><td>
      <form action="index.php" method="POST" id="rating'.$place['id'].'">
        <input type="hidden" name="do" value="shwgrprtg">
        <input type="hidden" name="group" value="'.$place['id'].'">
        <input type="hidden" name="rtg" value="'.$platz.'">
        <input class="shwgrprtg" type="submit" value="'.$place['name'].'">
      </form></td><td>'.$place['punkte_gesamt'].'</td></tr>';
  }
  $output.='</table>';
  return $output;
}

function show_team_rating($db,$grp,$rtg) {
  $wb=get_competition($db,$_SESSION['WB']);
  $wbk=get_competitionkind($db,$wb['art']);
  $gruppe=get_team($db,$grp);
  $members=get_teammembers($db,$grp);
  $teamsize=$wbk['anzahl']+$wbk['ersatz'];
  $rating=get_rating($db,$grp);
  switch ($wb['art']) {
    case 1:
      $gesamteindruck=$rating['ef_e']+$rating['ma_e']+$rating['at_e']+$rating['wt_e']+$rating['l1_e']+$rating['l2_e']+$rating['l3_e']+$rating['l4_e']+$rating['l5_e']+$rating['l6_e'];
      break;
    case 2:
      $gesamteindruck=$rating['ef_e']+$rating['ma_e']+$rating['at_e']+$rating['wt_e']+$rating['st_e']+$rating['l1_e']+$rating['l2_e']+$rating['l3_e']+$rating['l4_e']+$rating['l5_e']+$rating['l6_e']+$rating['l7_e']+$rating['l8_e']+$rating['l9_e'];
      break;
  }
  if ($rating['zeit_a'] > $gruppe['vorgabezeit_a']) {
    $zeitfehler=$rating['zeit_a']-$gruppe['vorgabezeit_a'];
  }
  else {
    $zeitfehler=0;
  }
  $output='<h1>'.$wbk['name'].'</h1>
  <table class="finrtg">
    <tr><th>Jugendfeuerwehr:</th><td>'.$gruppe['name'].'</td><th>Platz:</th><td>'.$rtg.'</td></tr>
    <tr><th>Land:</th><td>'.$wb['land'].'</td><th>Startnummer:</th><td>'.$gruppe['startnummer'].'</td></tr>
    <tr><th>Kreis:</th><td>'.$wb['kreis'].'</td><th>Datum:</th><th>Wertung:</th></tr>
    <tr><th>Ort:</th><td>'.$wb['ort'].'</td><td>'.date('d.m.Y',strtotime($wb['datum'])).'</td><td>'.get_teamtype($db,$gruppe['typ'])['name'].'</td></tr>
  </table>
  <table class="finrtg">
    <tr><th colspan="5">Anmeldebogen</th></tr>
    <tr><th>Nr.</th><th>Name</th><th>Vorname</th><th>Geb. Datum</th><th>Alter</th></tr>';
  $gesamtalter=0;
  foreach ($members as $mbr) {
    $alter=(date('Y')-substr($mbr['geburt'],0,4));
    if ($mbr['einsatz']) $gesamtalter+=$alter;
    if ($mbr['name']=='');
    if ($mbr['name']=='') $geburtstag=''; else $geburtstag=date('d.m.Y',strtotime($mbr['geburt']));
    if ($mbr['position']>$wbk['anzahl']) $mbr['position']='E';
    $output.='<tr><td>'.$mbr['position'].'</td><td>'.$mbr['name'].'</td><td>'.$mbr['vorname'].'</td><td>'.$geburtstag.'</td><td>'.($mbr['einsatz']?$alter:'&nbsp;').'</td></tr>';
  }
  $output.='<tr><td colspan="3">&nbsp;</td><th>Gesamt Jahre</th><td>'.$gesamtalter.'</td></tr>
    <tr><td colspan="3">Die Richtigkeit der Personalien wird best&auml;tigt</td><th>Alter : '.$wbk['anzahl'].'</th><td>'.$gruppe['alter'].'</td></tr>
  </table>
  <table class="finrtg">
    <tr><th colspan="5">Auswertungsbogen</th></tr>
    <tr><th>Auswertung A-Teil</th><th>Eindruck</th><th>Vorgabepunkte</th><th>+</th><th>1000</th></tr>
    <tr><th>Einheitsf&uuml;hrer</th><td>'.$rating['ef_e'].'</td><td>Fehlerpunkte</td><td>'.$rating['ef_f'].'</td><td>&nbsp;</td></tr>
    <tr><th>Maschinist</th><td>'.$rating['ma_e'].'</td><td>Fehlerpunkte</td><td>'.$rating['ma_f'].'</td><td>&nbsp;</td></tr>
    <tr><th>Angriffstrupp</th><td>'.$rating['at_e'].'</td><td>Fehlerpunkte</td><td>'.$rating['at_f'].'</td><td>&nbsp;</td></tr>
    <tr><th>Wassertrupp</th><td>'.$rating['wt_e'].'</td><td>Fehlerpunkte</td><td>'.$rating['wt_f'].'</td><td>&nbsp;</td></tr>';
    if ($wb['art']==2) {
      $output.='<tr><th>Schlauchtrupp</th><td>'.$rating['st_e'].'</td><td>Fehlerpunkte</td><td>'.$rating['st_f'].'</td><td>&nbsp;</td></tr>';
    }
    switch ($wb['art']) {
      case 1:
        $output.='<tr><th colspan="3">Summe der Fehlerpunkte im A-Teil</th><td>-</td><td>'.($rating['ef_f']+$rating['ma_f']+$rating['at_f']+$rating['wt_f']).'</td></tr>';
        break;
      case 2:
        $output.='<tr><th colspan="3">Summe der Fehlerpunkte im A-Teil</th><td>-</td><td>'.($rating['ef_f']+$rating['ma_f']+$rating['at_f']+$rating['wt_f']+$rating['st_f']).'</td></tr>';
        break;
    }
    $output.='<tr><td>Gesamtzeit der &Uuml;bung:</td><td>'.intval($rating['zeit_a']/60).' Min</td><td>'.($rating['zeit_a']%60).' Sek</td><td>Sek. &uuml;. '.intval($gruppe['vorgabezeit_a']/60).':'.($gruppe['vorgabezeit_a']%60).' Min:</td><td>'.$zeitfehler.'</td></tr>
    <tr><td>Zeittakt f&uuml;r Knoten</td><td>'.intval($rating['zeittakt_a']/60).' Min</td><td>'.($rating['zeittakt_a']%60).' Sek</td><td>-</td><td>'.$rating['zeittakt_a'].'</td></tr>
    <tr><th colspan="4">Punkte A-Teil:</th><th>'.$gruppe['punkte_a'].'</th></tr>
    <tr><th>Auswertung B-Teil</th><th>Eindruck</th><th>Vorgabepunkte:</th><th>+</th><th>400</th></tr>
    <tr><td>L&auml;ufer 1</td><td>'.$rating['l1_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l1_f'].'</td><td>&nbsp;</td></tr>
    <tr><td>L&auml;ufer 2</td><td>'.$rating['l2_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l2_f'].'</td><td>&nbsp;</td></tr>
    <tr><td>L&auml;ufer 3</td><td>'.$rating['l3_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l3_f'].'</td><td>&nbsp;</td></tr>
    <tr><td>L&auml;ufer 4</td><td>'.$rating['l4_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l4_f'].'</td><td>&nbsp;</td></tr>
    <tr><td>L&auml;ufer 5</td><td>'.$rating['l5_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l5_f'].'</td><td>&nbsp;</td></tr>
    <tr><td>L&auml;ufer 6</td><td>'.$rating['l6_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l6_f'].'</td><td>&nbsp;</td></tr>';
    if ($wb['art']==2) {
      $output.='<tr><td>L&auml;ufer 7</td><td>'.$rating['l7_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l7_f'].'</td><td>&nbsp;</td></tr>
      <tr><td>L&auml;ufer 8</td><td>'.$rating['l8_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l8_f'].'</td><td>&nbsp;</td></tr>
      <tr><td>L&auml;ufer 9</td><td>'.$rating['l9_e'].'</td><td>Fehlerpunkte:</td><td>'.$rating['l9_f'].'</td><td>&nbsp;</td></tr>';
    }
    switch ($wb['art']) {
      case 1:
        $output.='<tr><th colspan="3">Summe der Fehlerpunkte im B-Teil</th><td>-</td><td>'.($rating['l1_f']+$rating['l2_f']+$rating['l3_f']+$rating['l4_f']+$rating['l5_f']+$rating['l6_f']).'</td></tr>';
        break;
      case 2:
        $output.='<tr><th colspan="3">Summe der Fehlerpunkte im B-Teil</th><td>-</td><td>'.($rating['l1_f']+$rating['l2_f']+$rating['l3_f']+$rating['l4_f']+$rating['l5_f']+$rating['l6_f']+$rating['l7_f']+$rating['l8_f']+$rating['l9_f']).'</td></tr>';
        break;
    }
    $output.='<tr><td>Soll - Zeit:</td><td>'.intval($gruppe['vorgabezeit_b']/60).' Min</td><td>'.($gruppe['vorgabezeit_b']%60).' Sek</td><td>&nbsp;</td><td>&nbsp;</td></tr>
    <tr><td>Ist - Zeit:</td><td>'.intval($rating['zeit_b']/60).' Min</td><td>'.($rating['zeit_b']%60).' Sek</td><td>+/-</td><td>'.abs($gruppe['vorgabezeit_b']-$rating['zeit_b']).'</td></tr>
    <tr><th colspan="4">Punkte B-Teil:</th><th>'.$gruppe['punkte_b'].'</th></tr>';
    switch ($wb['art']) {
      case 1:
        $output.='<tr><th>Gesamteindruck</th><td>'.$gesamteindruck.'</td><td>Summe : 10</td><td>-</td><th>'.($gesamteindruck/10).'</th></tr>';
        break;
      case 2:
        $output.='<tr><th>Gesamteindruck</th><td>'.$gesamteindruck.'</td><td>Summe : 14</td><td>-</td><th>'.($gesamteindruck/14).'</th></tr>';
        break;
    }
    $output.='<tr><th colspan="4">Endergebnis:</th><th>'.$gruppe['punkte_gesamt'].'</th></tr>
  </table>';
  return $output;
}

function button_create_competition() {
  $output='<form action="index.php" method="POST" id="addcomp">
  <input type="hidden" name="screen" value="addcomp">
  <input class="menubutton" type="submit" value="Neuer Wettbewerb"></form>';
  return $output;
}

?>
