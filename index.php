<?php

  require('db_secrets.php');
  $db = new mysqli($dbhost,$dbuser,$dbpass,$dbname);
  $db->set_charset('utf8');
  if ($db->connect_errno) {
    printf("Datenbankverbindung fehlgeschlagen: %s\n", $db->connect_error);
    exit();
  }

  session_start();

  require('general_functions.php');
  require('wb_functions.php');
  require('lsp_functions.php');

  $lsp_token_mail_template='lsp_token_mail.tpl';
  $lsp_token_system_name='JF Wettbewerbe';
  $lsp_token_system_email='wettbewerbe@jugendfeuerwehr-wiesbaden.de';

  if ($_GET['sort']=='punkte') {
    $_SESSION['sort']='punkte_gesamt DESC';;
  } 
  else {
    if ($_GET['sort']=='startnummer') {
      $_SESSION['sort']='startnummer';
    }
    else {
      if (!isset($_SESSION['sort'])) {
        $_SESSION['sort']='startnummer';
      }
    }
  }

  switch ($_POST['do']) {
    case 'selectwb':
      select_competition($_POST['wettbewerb']);
      break;
    case 'createwb': 
      create_competition($db,$_POST['datum'],$_POST['land'],$_POST['kreis'],$_POST['ort'],$_POST['art'],$_POST['typ']);
      break;
    case 'modifygrp':
      modify_team($db,$_POST['id'],$_POST['startnummer'],$_POST['name'],$_POST['typ']);
      break;
    case 'modifygrpmembers':
      modify_team_members($db);
      break;
    case 'modifyrate':
      modify_rating($db);
      break;
    case 'insertgrp': 
      insert_team($db);
      break;
    case 'removegrp':
      rem_team($db,$_POST['removeid']);
      break;
    case 'removewb':
      rem_competition($db,$_POST['removeid']);
      break;
    case 'createlsp':
      create_lsp($db,$_POST['datum'],$_POST['bundesland'],$_POST['kreis'],$_POST['ort'],$_POST['ab_name'],$_POST['ab_vorname'],$_POST['ab_ort'],$_POST['stempel'],$_POST['mzf']);
      break;
    case 'selectlsp':
      select_lsp($_POST['lsp']);
      break;
    case 'insertlspgrp':
      insert_lsp_group($db);
      break;
    case 'modifylspgrp':
      modify_lsp_group($db,$_SESSION['LSP'],$_POST['id']);
      break;
    case 'modifylspgrpmembers':
      modify_lsp_group_members($db,$_SESSION['LSP'],$_POST['id']);
      break;
    case 'removelspgrp':
      remove_lsp_group($db,$_SESSION['LSP'],$_POST['removeid']);
      break;
    case 'modifylsprate':
      modify_lsp_rating($db,$_SESSION['LSP']);
      $_POST['do']='ratelspgrp';
      break;
    case 'parselspgroupimport':
      parse_lsp_group_import($db,$_SESSION['LSP'],$_POST['gruppe'],$_POST['import']);
      $_POST['do']='editlspgrpmembers';
      break;
    case 'modifylspjudges':
      modify_lsp_judges($db,$_SESSION['LSP']);
      break;
    case 'newlsptoken':
      new_lsp_token($db,$_SESSION['LSP'],$_POST['email']);
      $_POST['do']='showlsptoken';
      break;
    case 'sendlsptoken':
      send_lsp_token($db,$_SESSION['LSP'],$_POST['token']);
      $_POST['do']='showlsptoken';
      break;
    case 'sendunsentlsptoken':
      send_unsent_lsp_token($db,$_SESSION['LSP']);
      $_POST['do']='showlsptoken';
      break;
  }

/*

  OUTPUT CREATION

*/

  html_head();
  echo error_output();
  if (!isset($_SESSION['WB'])) {
    if (isset($_POST['screen'])) {
      switch ($_POST['screen']) {
        case 'addcomp':
          echo form_create_competition($db);
          echo button_back();
          break;
        case 'addlsp':
          echo form_create_leistungsspange($db);
          echo button_back();
          break;
      }
    }
    else {
      echo form_select_competition($db);
      echo button_create_competition();
      echo form_select_lsp($db);
      echo button_create_leistungsspange();
    }
  }
  else {
    if ($_SESSION['WB']=='lsp') {
      switch ($_POST['do']) {
        case 'addlspgrp':
          echo form_create_lsp_group($db,$_SESSION['LSP']);
          echo button_back();
          break;
        case 'editlspgrp':
          echo form_edit_lsp_group($db,$_SESSION['LSP'],$_POST['gruppe']);
          echo '<div class="menu"><table><tr><td>';
          echo button_back();
          echo '</td><td>';
          echo button_delete_lsp_group($_POST['gruppe']);
          echo '</td></tr></table></div>';
          break;
        case 'editlspgrpmembers':
          echo form_edit_lsp_group_members($db,$_SESSION['LSP'],$_POST['gruppe']);
          echo '<div class="menu"><table><tr><td>';
          echo button_back();
          echo '</td><td>';
          echo button_import_lsp_group_members($db,$_SESSION['LSP'],$_POST['gruppe']);
          echo '</td></tr></table></div>';
          break;
        case 'importlspgroupmembers':
          echo form_import_lsp_group_members($db,$_SESSION['LSP'],$_POST['gruppe']);
          break;
        case 'ratelspgrp':
          echo form_rate_lsp_group($db,$_SESSION['LSP'],$_POST['gruppe']);
          echo button_back();
          break;
        case 'showlspresults':
          echo show_lsp_results($db,$_SESSION['LSP']);
          echo button_back();
          break;
        case 'showlsprating':
          echo show_lsp_rating($db,$_SESSION['LSP'],$_POST['group']);
          echo button_show_lsp_results();
          break;
        case 'managelspjudges':
          echo manage_lsp_judges($db,$_SESSION['LSP']);
          echo button_back();
          break;
        case 'showlsptoken':
          echo form_show_lsp_token($db,$_SESSION['LSP']);
          echo '<div class="menu"><table><tr><td>';
          echo button_back();
          echo '</td><td>';
          echo button_create_lsp_token();
          echo '</td><td>';
          echo button_send_unsent_lsp_token();
          echo '</td></tr></table></div>';
          break;
        case 'createlsptoken':
          echo form_create_lsp_token($db,$_SESSION['LSP']);
          echo button_show_lsp_token();
          break;
        default:
          echo form_show_lsp_groups($db,$_SESSION['LSP'],$_SESSION['sort']);
          echo '<div class="menu"><table><tr><td>';
          echo button_create_lsp_group();
          echo '</td><td>';
          echo button_show_lsp_token();
          echo '</td><td>';
          echo button_manage_lsp_judges();
          echo '</td><td>';
          echo button_show_lsp_results();
          echo '</td><td>';
          echo button_deselect_lsp();
          echo '</td></tr></table></div>';
          break;
      }
    }
    else {
    switch ($_POST['do']) {
      case 'editgrp':
        echo form_edit_competition_team($db,$_POST['gruppe']);
        echo '<div class="menu"><table><tr><td>';
        echo button_back();
        echo '</td><td>';
        echo button_delete_competition_team($_POST['gruppe']);
        echo '</td></tr></table></div>';
        break;
      case 'editgrpmembers':
        echo form_edit_team_members($db,$_POST['gruppe']);
        echo button_back();
        break;
      case 'rategrp':
        echo form_rate_team($db,$_POST['gruppe']);
        echo button_back();
        break;
      case 'addgrp':
        echo form_create_competition_team($db,$_SESSION['WB']);
        echo button_back();
        break;
      case 'showwins':
        echo show_winnerlist($db);
        echo button_back();
        break;
      case 'shwgrprtg':
        echo show_team_rating($db,$_POST['group'],$_POST['rtg']);
        echo button_show_winnerlist();
        break;
      default:
        echo form_show_competition_teams($db,$_SESSION['WB'],$_SESSION['sort']);
        echo '<div class="menu"><table><tr><td>';
        echo button_create_competition_team();
        echo '</td><td>';
        echo button_show_winnerlist();
        echo '</td><td>';
        echo button_deselect_competition();
        echo '</td><td>';
	echo button_delete_competition();
        echo '</td></tr></table></div>';
      break;
    }
    }
  }
  html_foot();
  $db->close();
?>
