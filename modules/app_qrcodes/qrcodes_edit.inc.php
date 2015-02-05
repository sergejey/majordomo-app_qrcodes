<?php
/*
* @version 0.1 (wizard)
*/
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $table_name='qrcodes';
  $rec=SQLSelectOne("SELECT * FROM $table_name WHERE ID='$id'");
  if ($this->mode=='update') {
   $ok=1;
  //updating 'TITLE' (varchar)
   global $title;
   $rec['TITLE']=$title;

   if (!$rec['TITLE']) {
    $ok=0;
    $out['ERR_TITLE']=1;
   }

  //updating 'QRCODE' (varchar)
   global $qrcode;
   $rec['QRCODE']=$qrcode;

   if (!$rec['QRCODE']) {
    $ok=0;
    $out['ERR_QRCODE']=1;
   }


   global $show_on_click;

  //updating 'HTML' (text)
  if ($show_on_click=='html') {
   global $html;
   $rec['HTML']=$html;
  } else {
   $rec['HTML']='';
  }

  //updating 'EXT_URL' (url)
  if ($show_on_click=='url') {
   global $ext_url;
   $rec['EXT_URL']=$ext_url;
  } else {
   $rec['EXT_URL']='';
  }

  if ($show_on_click=='menu') {
   global $menu_id;
   $rec['MENU_ID']=(int)$menu_id;
  } else {
   $rec['MENU_ID']=0;
  }


   global $do_on_click;

  //updating 'CODE' (text)
   global $code;
   if ($do_on_click=='run_code') {
    $rec['CODE']=$code;
   } else {
    $rec['CODE']='';
   }


   if ($do_on_click=='run_method') {
  //updating 'LINKED_OBJECT' (varchar)
   global $linked_object;
   $rec['LINKED_OBJECT']=$linked_object;
  //updating 'LINKED_METHOD' (varchar)
   global $linked_method;
   $rec['LINKED_METHOD']=$linked_method;
  } else {
   $rec['LINKED_OBJECT']='';
   $rec['LINKED_METHOD']='';
  }
  //updating 'SCRIPT_ID' (int)

   if ($do_on_click=='run_script') {
    global $script_id;
    $rec['SCRIPT_ID']=(int)$script_id;
   } else {
    $rec['SCRIPT_ID']=0;
   }


  //UPDATING RECORD
   if ($ok) {
    if ($rec['ID']) {
     SQLUpdate($table_name, $rec); // update
    } else {
     $new_rec=1;
     $rec['ID']=SQLInsert($table_name, $rec); // adding new record
    }
    $out['OK']=1;
   } else {
    $out['ERR']=1;
   }
  }
  if (is_array($rec)) {
   foreach($rec as $k=>$v) {
    if (!is_array($v)) {
     $rec[$k]=htmlspecialchars($v);
    }
   }
  }
  outHash($rec, $out);


  if (!$out['QRCODE']) {
   $out['QRCODE']=substr(md5(rand(0, 50000)), 0, 5);
  }

$out['SCRIPTS']=SQLSelect("SELECT ID, TITLE FROM scripts ORDER BY TITLE");

   $menu_items=SQLSelect("SELECT ID, TITLE, PARENT_ID FROM commands WHERE EXT_ID=0 ORDER BY PARENT_ID, TITLE");
   $titles=array();
   foreach($menu_items as $k=>$v) {
    $titles[$v['ID']]=$v['TITLE'];
   }
   $total=count($menu_items);
   for($i=0;$i<$total;$i++) {
    if ($titles[$menu_items[$i]['PARENT_ID']]) {
     $menu_items[$i]['TITLE']=$titles[$menu_items[$i]['PARENT_ID']].' &gt; '.$menu_items[$i]['TITLE'];
    }
   }
   $out['MENU_ITEMS']=$menu_items;
