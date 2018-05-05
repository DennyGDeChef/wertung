<?php

  $db = new mysqli('localhost','wertung','jfrating','wertung');
  $db->set_charset('utf8');
  if ($db->connect_errno) {
    printf("Datenbankverbindung fehlgeschlagen: %s\n", $db->connect_error);
    exit();
  }

  session_start();

  require('general_functions.php');
  require('lsp_functions.php');

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
    case 'insertlsptokengrp':
      insert_lsp_token_group($db,$_SESSION['token']);
      break;
    case 'modifylsptokengrp':
      modify_lsp_token_group($db,$_SESSION['token'],$_POST['id']);
      break;
    case 'gettoken':
      select_lsp_token($db,$_POST['token']);
      break;
    case 'removelsptokengrp':
      remove_lsp_token_group($db,$_SESSION['token'],$_POST['removeid']);      
      break;
    case 'modifylsptokengrpmembers':
      modify_lsp_token_group_members($db,$_SESSION['token'],$_POST['id']);
      break;
    case 'parselsptokengroupimport':
      parse_lsp_group_import($db,get_lsp_token($db,$_SESSION['token'])['abnahme'],$_POST['gruppe'],$_POST['import']);
      $_POST['do']='editlsptokengrpmembers';
      break;
  }

  html_head();
  echo error_output();

  if (!isset($_SESSION['token'])) {
    echo form_ask_lsp_token();
  } else
  switch ($_POST['do']) {
    case 'addlsptokengrp':
      echo form_create_lsp_token_group($db,$_SESSION['token']);
      echo button_back_token();
      break;
    case 'editlsptokengrpmembers':
      echo form_edit_lsp_token_group_members($db,$_SESSION['token'],$_POST['gruppe']);
      echo '<div class="menu"><table><tr><td>';
      echo button_back_token();
      echo '</td><td>';
      echo button_import_lsp_token_group_members($db,$_SESSION['token'],$_POST['gruppe']);
      echo '</td></tr></table></div>';
      break;
    case 'editlsptokengrp':
      echo form_edit_lsp_token_group($db,$_SESSION['token'],$_POST['gruppe']);
      echo '<div class="menu"><table><tr><td>';
      echo button_back_token();
      echo '</td><td>';
      echo button_delete_lsp_token_group($_POST['gruppe']);
      echo '</td></tr></table></div>';
      break;
    case 'importlsptokengroupmembers':
      echo form_import_lsp_token_group_members($db,$_SESSION['token'],$_POST['gruppe']);
      break;
    default:
      echo form_show_lsp_token_groups($db,$_SESSION['token'],$_SESSION['sort']);
      echo '<div class="menu"><table><tr><td>';
      echo button_lsp_token_logout();
      echo '</td><td>';
      echo button_create_lsp_token_group();
      echo '</td></tr></table></div>';
      break;
  }

  html_foot();

?>

