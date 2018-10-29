<?php

function form_benutzer_login() {
  $output ='<form action="index.php" method="POST" id="login">';
  $output.='<input type="hidden" name="benutzer" value="login">';
  $output.='Benutzername <input type="text" name="benutzername"> ';
  $output.='Passwort <input type="password" name="passwort"> ';
  $output.='<input type="submit" name="einloggen" value="Einloggen">';
  $output.='</form>';
  $output.='<p>Oder <a href="token.php">Token verwenden</a></p>';
  return $output;
}

function button_benutzer_logout() {
  $output ='<form action="index.php" method="POST" id="logout">';
  $output.='<input type="hidden" name="benutzer" value="logout">';
  $output.='<input type="submit" name="ausloggen" value="Ausloggen">';
  $output.='</form>';
  return $output;
}

function button_benutzer_passwort_aendern() {
  $output ='<form action="index.php" method="POST" id="pwaendern">';
  $output.='<input type="hidden" name="benutzer" value="pwaendern">';
  $output.='<input type="submit" name="pwaendern" value="Passwort &auml;ndern">';
  $output.='</form>';
  return $output;
}

function button_benutzer_anlegen() {
  $output ='<form action="index.php" method="POST" id="neuenbenutzer">';
  $output.='<input type="hidden" name="benutzer" value="neuenbenutzer">';
  $output.='<input type="submit" name="neuenbenutzer" value="neuer Benutzer">';
  $output.='</form>';
  return $output;
}

function form_benutzer_anlegen($db) {
  $lkr=get_landkreise($db);
  $output ='<h1>Benutzer anlegen</h1>';
  $output.='<form action="index.php" method="POST" id="neuerbenutzer">';
  $output.='<input type="hidden" name="benutzer" value="neuerbenutzer">';
  $output.='<table>';
  $output.='<tr><th>Benutzername: </th><td><input type="text" name="benutzername"></td></tr>';
  $output.='<tr><th>E-Mail: </th><td><input type="text" name="email"></td></tr>';
  $output.='<tr><th>Vorname: </th><td><input type="text" name="vorname"></td></tr>';
  $output.='<tr><th>Nachname: </th><td><input type="text" name="nachname"></td></tr>';
  $output.='<tr><th>neues Passwort: </th><td><input type="password" name="pw1"></td></tr>';
  $output.='<tr><th>neues Passwort: </th><td><input type="password" name="pw2"></td></tr>';
  $output.='<tr><th>Landkreis: </th><td>';
  $output.='<select name="kreis">';
  foreach ($lkr as $lk) {
    $output.='<option value="'.$lk['id'].'">'.$lk['name'].'</option>';
  }
  $output.='</select>';
  $output.='</td></tr>';
  $output.='<tr><td><input type="submit" name="neuespw" value="Benutzer anlegen"></td><td>&nbsp;</td></tr>';
  $output.='</table>';
  $output.='</form>';
  return $output;
}

function form_benutzer_passwort_aendern() {
  $output ='<h1>Passwort &auml;ndern</h1>';
  $output.='<form action="index.php" method="POST" id="neuespw">';
  $output.='<input type="hidden" name="benutzer" value="neuespw">';
  $output.='<table>';
  $output.='<tr><th>aktuelles Passwort: </th><td><input type="password" name="alt"></td></tr>';
  $output.='<tr><th>neues Passwort: </th><td><input type="password" name="neu1"></td></tr>';
  $output.='<tr><th>neues Passwort: </th><td><input type="password" name="neu2"></td></tr>';
  $output.='<tr><td><input type="submit" name="neuespw" value="Passwort &auml;ndern"></td><td>&nbsp;</td></tr>';
  $output.='</table>';
  $output.='</form>';
  return $output;
}

function passwort_aendern($db,$alt,$neu1,$neu2) {
  global $error_output;
  $neu1=cut_after_spaces($neu1);
  $neu2=cut_after_spaces($neu2);
  $query="SELECT passwort FROM benutzer WHERE id=".$_SESSION['_BENUTZER'];
  if ($neu1 != $neu2) {
	  $error_output='Die neuen Passw&ouml;rter stimmen nicht &uuml;berein!';
	  return false;
  }
  if ($result = $db->query($query)) {
    if ($result->num_rows != 1) {
      $error_output='Da ist was in der Benutzerdatenbank kaputt... '.$db->error;
	  return false;
    }
	elseif ($data=$result->fetch_assoc()) {
		if (password_verify($alt,$data['passwort'])) {
			$query="UPDATE benutzer SET passwort='".password_hash($neu1,PASSWORD_DEFAULT)."' WHERE id=".$_SESSION['_BENUTZER'];
			$result->free();
			if (!$result = $db->query($query)) {
				$error_output='Das Datenbankupdate schlug fehl: '.$db->error;
				return false;
			}
			return true;
		}
		else {
			$error_output='Das bisherige Passwort ist falsch!';
			return false;
		}
	}
  }
  $error_output='Da ist was in der Benutzerdatenbank kaputt... '.$db->error;
  print_r($result);
  return false;
}

function cut_after_spaces($input) {
  return (strpos($input,' '))?$input=substr($input,0,strpos($input,' ')):$input;
}

function benutzer_login($db,$benutzername,$passwort) {
  global $error_output;
  $benutzername=cut_after_spaces($benutzername);
  $passwort=cut_after_spaces($passwort);
  $query="SELECT id,passwort FROM benutzer WHERE benutzername='".$benutzername."'";
  if ($result = $db->query($query)) {
    if ($result->num_rows > 1) {
      $error_output='Da ist was in der Benutzerdatenbank kaputt...';
    }
    elseif ($result->num_rows ==0) {
      $error_output='Login falsch!';
    }
    else {
      $data=$result->fetch_assoc();
      if (password_verify($passwort,$data['passwort'])) {
        $_SESSION['_BENUTZER']=$data['id'];
        if (password_needs_rehash($data['passwort'], PASSWORD_DEFAULT)) {
          $db->query("UPDATE benutzer SET passwort='".password_hash($passwort,PASSWORD_DEFAULT)."' WHERE benutzername='".$benutzername."'");
        }
	    $_SESSION['_RECHTE']=get_benutzer_rechte($db,$_SESSION['_BENUTZER']);
      }
      else {
        $error_output='Login falsch';
      }
    }
  }
  else {
    $error_output='Login falsch!';
  }
  if ($error_output != '') {
    return false;
  }
  else {
    return true;
  }
}

function benutzer_logout($db) {
  unset($_SESSION['_RECHTE']);
  unset($_SESSION['_BENUTZER']);
  unset($_SESSION['WB']);
  unset($_SESSION['LSP']);
  session_destroy();
  return true;
}

function neuer_benutzer($db,$benutzername,$email,$vorname,$nachname,$pw1,$pw2,$kreis) {
  global $error_output;
  $benutzername=cut_after_spaces($benutzername);
  $email=cut_after_spaces($email);
  $vorname=cut_after_spaces($vorname);
  $nachname=cut_after_spaces($nachname);
  $pw1=cut_after_spaces($pw1);
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error_output="Keine g&uuml;ltige E-Mail Adresse!";
	return false;
  }
  if ($pw1 != $pw2) {
	  $error_output='Die Passw&ouml;rter stimmen nicht &uuml;berein!';
	  return false;
  }
  $query="SELECT id FROM benutzer WHERE benutzername='".$benutzername."'";
  if ($result=$db->query($query)) {
	  if ($result->num_rows >0) {
		  $error_output='Der Benutzername ist schon vergeben';
		  return false;
	  }
	  $result->free();
  }
  $query="INSERT INTO benutzer SET benutzername='".$benutzername."',email='".$email."',name='".$nachname."',vorname='".$vorname."',passwort='".password_hash($pw1,PASSWORD_DEFAULT)."',reg_datum=NOW(),ip='".$_SERVER[REMOTE_ADDR]."',landkreis=".$kreis;
  if ($result=$db->query($query)) {
	  return true;
  }
  $error_output='Beim Anlegen des Benutzers ging etwas schief... '.$db->error;
  return false;
}

function get_benutzer_rechte($db,$benutzer) {
	$query="SELECT recht.id as rechte FROM recht LEFT JOIN rolle_recht ON recht.id=rolle_recht.recht LEFT JOIN rolle ON rolle_recht.rolle=rolle.id LEFT JOIN benutzer_rolle ON rolle.id=benutzer_rolle.rolle WHERE benutzer_rolle.benutzer=".$benutzer;
	if ($result=$db->query($query)) {
		$rechte=array();
		while ($row = mysqli_fetch_row($result)) {
			array_push($rechte,$row[0]);
		}
		return $rechte;
	}
	return false;
}

function button_benutzer_bearbeiten() {
  $output ='<form action="index.php" method="POST" id="bearbeitebenutzer">';
  $output.='<input type="hidden" name="benutzer" value="bearbeitebenutzer">';
  $output.='<input type="submit" name="bearbeitebenutzer" value="Benutzer">';
  $output.='</form>';
  return $output;
}

function get_benutzer_namen($db) {
	$query="SELECT id,name,vorname FROM benutzer ORDER by name, vorname";
	if ($result=$db->query($query)) {
	  return $result->fetch_all(MYSQLI_ASSOC);
	}
	return false;
}

function get_benutzer($db,$benutzer) {
	$query="SELECT benutzer.*,landkreis.name AS kreisname FROM benutzer LEFT JOIN landkreis ON benutzer.landkreis=landkreis.id WHERE benutzer.id=".$benutzer;
	if ($result=$db->query($query)) {
		return $result->fetch_all(MYSQLI_ASSOC)[0];
	}
	return false;
}

function get_benutzer_rolle($db,$benutzer) {
	$query="SELECT rolle.id, rolle.name AS rolle FROM benutzer_rolle LEFT JOIN rolle ON benutzer_rolle.rolle = rolle.id WHERE benutzer_rolle.benutzer=".$benutzer;
	if ($result=$db->query($query)) {
		if ($result->num_rows>0) {
		  return $result->fetch_all(MYSQLI_ASSOC)[0];
		}
		else return array(id=>0,rolle=>'keine');
	}
	return false;
}

function form_benutzer_bearbeiten($db,$benutzer=0) {
  $benutzerliste=get_benutzer_namen($db);
  $output ='<h1>Benutzer bearbeiten</h1>';
  $output.='<form action="index.php" method="POST" id="bearbeitebenutzer">';
  $output.='<input type="hidden" name="benutzer" value="bearbeitebenutzer">';
  $output.='<table>';
  $output.='<tr><th>Benutzer w&auml;hlen: </th><td>';
  $output.='<select name="benutzerid">';
  foreach ($benutzerliste as $element) {
    $output.='<option value="'.$element['id'].'"';
	if ($element['id']==$benutzer) $output.=' selected';
	$output.='>'.$element['vorname'].' '.$element['name'].'</option>';
  }
  $output.='</select>';
  $output.='</td>';
  $output.='<td><input type="submit" name="benutzerwahl" value="Benutzer ausw&auml;hlen"></td><td>&nbsp;</td></tr>';
  $output.='</table>';
  $output.='</form>';
  if ($benutzer!=0) {
	$daten=get_benutzer($db,$benutzer);
	$rolle=get_benutzer_rolle($db,$benutzer);
	$output.='<table>';
	$output.='<tr><th>Benutzername:</th><td>'.$daten['benutzername'].'</td></tr>';
	$output.='<tr><th>E-Mail:</th><td>'.$daten['email'].'</td></tr>';
	$output.='<tr><th>Name:</th><td>'.$daten['name'].'</td></tr>';
	$output.='<tr><th>Vorname:</th><td>'.$daten['vorname'].'</td></tr>';
	$output.='<tr><th>Landkreis:</th><td>'.$daten['kreisname'].'</td></tr>';
	$output.='<tr><th>Rolle:</th><td>'.$rolle['rolle'].'</td></tr>';
	$output.='</table>';  
  }
  return $output;
}

function get_rollen($db) {
	$query="SELECT id,name FROM rolle ORDER by name";
	if ($result=$db->query($query)) {
	  return $result->fetch_all(MYSQLI_ASSOC);
	}
	return false;
}

function get_rolle($db,$rolle) {
	$query="SELECT id,name FROM rolle WHERE id=".$rolle;
	if ($result=$db->query($query)) {
		if ($result->num_rows>0) {
		  return $result->fetch_all(MYSQLI_ASSOC)[0];
		}
		else return array(id=>0,rolle=>'keine');
	}
	return false;
}

function button_rolle_bearbeiten() {
  $output ='<form action="index.php" method="POST" id="bearbeiterolle">';
  $output.='<input type="hidden" name="benutzer" value="bearbeiterolle">';
  $output.='<input type="submit" name="bearbeiterolle" value="Rolle">';
  $output.='</form>';
  return $output;
}

function get_rolle_rechte($db,$rolle){
	$query="SELECT recht,id,name FROM (SELECT * FROM rolle_recht WHERE rolle_recht.rolle=".$rolle.") AS inside RIGHT OUTER JOIN recht ON inside.recht=recht.id ORDER BY name";
	if ($result=$db->query($query)) {
	  $output=array();
	  while ($row=$result->fetch_assoc()) {
		$output[$row['id']]=array('recht'=>($row['recht']==$row['id']?true:false),'name'=>$row['name']);
	  }
	  return $output;
	}
	return false;
}

function form_rolle_bearbeiten($db,$rolle=0) {
  $rollenliste=get_rollen($db);
  $output ='<h1>Rolle bearbeiten</h1>';
  $output.='<form action="index.php" method="POST" id="bearbeiterolle">';
  $output.='<input type="hidden" name="benutzer" value="bearbeiterolle">';
  $output.='<table>';
  $output.='<tr><th>Rolle w&auml;hlen: </th><td>';
  $output.='<select name="rolleid">';
  foreach ($rollenliste as $element) {
    $output.='<option value="'.$element['id'].'"';
	if ($element['id']==(int)$rolle) $output.=' selected';
	$output.='>'.$element['name'].'</option>';
  }
  $output.='</select>';
  $output.='</td>';
  $output.='<td><input type="submit" name="rollewahl" value="Rolle ausw&auml;hlen"></td><td>&nbsp;</td></tr>';
  $output.='</table>';
  $output.='</form>';
  if ($rolle!=0) {
	  $rrechte=get_rolle_rechte($db,$rolle);
	  $output.='<table>';
	  $output.='<tr><th>Rolle</th><th>'.get_rolle($db,$rolle)['name'].'</th></tr>';
	  foreach($rrechte as $key=>$value) {
		  $output.='<tr><th>'.$value['name'].'</td><td>'.($value['recht']?'Ja':'Nein').'</td></tr>';
	  }
	  $output.='</table>';
  }
  return $output;
}

?>
