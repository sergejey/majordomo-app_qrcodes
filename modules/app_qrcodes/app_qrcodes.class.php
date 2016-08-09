<?php
/**
* App_qrcodes 
*
* App_qrcodes
*
* @package project
* @author Serge J. <jey@tut.by>
* @copyright http://www.atmatic.eu/ (c)
* @version 0.1 (wizard, 18:02:30 [Feb 04, 2015])
*/
//
//
class app_qrcodes extends module {
/**
* app_qrcodes
*
* Module class constructor
*
* @access private
*/
function app_qrcodes() {
  $this->name="app_qrcodes";
  $this->title="QR Codes";
  $this->module_category="<#LANG_SECTION_APPLICATIONS#>";
  $this->checkInstalled();
}
/**
* saveParams
*
* Saving module parameters
*
* @access public
*/
function saveParams($data=0) {
 $p=array();
 if (IsSet($this->id)) {
  $p["id"]=$this->id;
 }
 if (IsSet($this->view_mode)) {
  $p["view_mode"]=$this->view_mode;
 }
 if (IsSet($this->edit_mode)) {
  $p["edit_mode"]=$this->edit_mode;
 }
 if (IsSet($this->tab)) {
  $p["tab"]=$this->tab;
 }
 return parent::saveParams($p);
}
/**
* getParams
*
* Getting module parameters from query string
*
* @access public
*/
function getParams() {
  global $id;
  global $mode;
  global $view_mode;
  global $edit_mode;
  global $tab;
  if (isset($id)) {
   $this->id=$id;
  }
  if (isset($mode)) {
   $this->mode=$mode;
  }
  if (isset($view_mode)) {
   $this->view_mode=$view_mode;
  }
  if (isset($edit_mode)) {
   $this->edit_mode=$edit_mode;
  }
  if (isset($tab)) {
   $this->tab=$tab;
  }
}
/**
* Run
*
* Description
*
* @access public
*/
function run() {
 global $session;
  $out=array();
  $this->admin($out);
  if (IsSet($this->owner->action)) {
   $out['PARENT_ACTION']=$this->owner->action;
  }
  if (IsSet($this->owner->name)) {
   $out['PARENT_NAME']=$this->owner->name;
  }
  $out['VIEW_MODE']=$this->view_mode;
  $out['EDIT_MODE']=$this->edit_mode;
  $out['MODE']=$this->mode;
  $out['ACTION']=$this->action;
  $out['TAB']=$this->tab;
  if ($this->single_rec) {
   $out['SINGLE_REC']=1;
  }
  $this->data=$out;
  $p=new parser(DIR_TEMPLATES.$this->name."/".$this->name.".html", $this->data, $this);
  $this->result=$p->result;
}
/**
* BackEnd
*
* Module backend
*
* @access public
*/
function admin(&$out) {
 global $qr;
 if (isset($this->data_source) && !$_GET['data_source'] && !$_POST['data_source']) {
  $out['SET_DATASOURCE']=1;
 }
 if ($this->data_source=='qrcodes' || $this->data_source=='') {
  if ($this->view_mode=='create' && $qr) {
   $old_rec=SQLSelectOne("SELECT * FROM qrcodes WHERE QRCODE LIKE '".DBSafe(trim($qr))."'");
   if (!$old_rec['ID']) {
    $rec=array();
    $rec['TITLE']='New code '.$qr;
    $rec['HTML']='';
    $rec['QRCODE']=trim($qr);
    $rec['ID']=SQLInsert('qrcodes', $rec);
   }
   $this->redirect(ROOTHTML.'popup/app_qrcodes.html?qr='.urlencode(trim($qr)));
  }
  if ($qr) {
   $this->codeReceived($out, $qr);
   $this->view_mode='received';
  }
  if ($this->view_mode=='' || $this->view_mode=='search_qrcodes') {
   $this->search_qrcodes($out);
  }
  if ($this->view_mode=='edit_qrcodes') {
   $this->edit_qrcodes($out, $this->id);
  }
  if ($this->view_mode=='delete_qrcodes') {
   $this->delete_qrcodes($this->id);
   $this->redirect("?");
  }
 }
}

/**
* Title
*
* Description
*
* @access public
*/
 function codeReceived(&$out, $code) {

  if (defined('SETTINGS_HOOK_BARCODE') && SETTINGS_HOOK_BARCODE!='') {
   eval(SETTINGS_HOOK_BARCODE);
  }

  $rec=SQLSelectOne("SELECT * FROM qrcodes WHERE QRCODE LIKE '".DBSafe($code)."'");

  if (!$rec['ID']) {

   $out['NOT_FOUND']=1;

  } else {

  if ($rec['SCRIPT_ID']) {
   runScript($rec['SCRIPT_ID']);
  }
  if ($rec['LINKED_OBJECT'] && $rec['LINKED_METHOD']) {
   callMethod($rec['LINKED_OBJECT'].'.'.$rec['LINKED_METHOD']);
  }
  if ($rec['CODE']) {
   eval($rec['CODE']);
  }
  if ($rec['MENU_ID']) {
   header("Location:".ROOTHTML.'menu/'.$rec['MENU_ID'].'.html');
   exit;
  } elseif ($rec['EXT_URL']!='' && $rec['EXT_URL']!='http://') {
   header("Location:".$rec['EXT_URL']);
   exit;
  }

  outHash($rec, $out);
  $out['TITLE']=processTitle($out['TITLE']);
  $out['HTML']=processTitle($out['HTML']);


  }

  $out['QRCODE']=$code;


 }

/**
* FrontEnd
*
* Module frontend
*
* @access public
*/
function usual(&$out) {
 $this->admin($out);
}
/**
* qrcodes search
*
* @access public
*/
 function search_qrcodes(&$out) {
  require(DIR_MODULES.$this->name.'/qrcodes_search.inc.php');
 }
/**
* qrcodes edit/add
*
* @access public
*/
 function edit_qrcodes(&$out, $id) {
  require(DIR_MODULES.$this->name.'/qrcodes_edit.inc.php');
 }
/**
* qrcodes delete record
*
* @access public
*/
 function delete_qrcodes($id) {
  $rec=SQLSelectOne("SELECT * FROM qrcodes WHERE ID='$id'");
  // some action for related tables
  SQLExec("DELETE FROM qrcodes WHERE ID='".$rec['ID']."'");
 }
/**
* Install
*
* Module installation routine
*
* @access private
*/
 function install($data='') {
  parent::install();
 }
/**
* Uninstall
*
* Module uninstall routine
*
* @access public
*/
 function uninstall() {
  SQLExec('DROP TABLE IF EXISTS qrcodes');
  parent::uninstall();
 }
/**
* dbInstall
*
* Database installation routine
*
* @access private
*/
 function dbInstall($data) {
/*
qrcodes - App_qrcodes
*/
  $data = <<<EOD
 qrcodes: ID int(10) unsigned NOT NULL auto_increment
 qrcodes: TITLE varchar(255) NOT NULL DEFAULT ''
 qrcodes: QRCODE varchar(255) NOT NULL DEFAULT ''
 qrcodes: HTML text
 qrcodes: CODE text
 qrcodes: EXT_URL char(255) NOT NULL DEFAULT ''
 qrcodes: LINKED_OBJECT varchar(255) NOT NULL DEFAULT ''
 qrcodes: LINKED_METHOD varchar(255) NOT NULL DEFAULT ''
 qrcodes: SCRIPT_ID int(10) NOT NULL DEFAULT '0'
 qrcodes: MENU_ID int(10) NOT NULL DEFAULT '0'
EOD;
  parent::dbInstall($data);
 }
// --------------------------------------------------------------------
}
/*
*
* TW9kdWxlIGNyZWF0ZWQgRmViIDA0LCAyMDE1IHVzaW5nIFNlcmdlIEouIHdpemFyZCAoQWN0aXZlVW5pdCBJbmMgd3d3LmFjdGl2ZXVuaXQuY29tKQ==
*
*/
?>