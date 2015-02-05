<?php
/*
* @version 0.1 (wizard)
*/
 global $session;
  if ($this->owner->name=='panel') {
   $out['CONTROLPANEL']=1;
  }
  $qry="1";
  // search filters
  // QUERY READY
  global $save_qry;
  if ($save_qry) {
   $qry=$session->data['qrcodes_qry'];
  } else {
   $session->data['qrcodes_qry']=$qry;
  }
  if (!$qry) $qry="1";
  // FIELDS ORDER
  global $sortby_qrcodes;
  if (!$sortby_qrcodes) {
   $sortby_qrcodes=$session->data['qrcodes_sort'];
  } else {
   if ($session->data['qrcodes_sort']==$sortby_qrcodes) {
    if (Is_Integer(strpos($sortby_qrcodes, ' DESC'))) {
     $sortby_qrcodes=str_replace(' DESC', '', $sortby_qrcodes);
    } else {
     $sortby_qrcodes=$sortby_qrcodes." DESC";
    }
   }
   $session->data['qrcodes_sort']=$sortby_qrcodes;
  }
  if (!$sortby_qrcodes) $sortby_qrcodes="TITLE";
  $out['SORTBY']=$sortby_qrcodes;
  // SEARCH RESULTS
  $res=SQLSelect("SELECT * FROM qrcodes WHERE $qry ORDER BY ".$sortby_qrcodes);
  if ($res[0]['ID']) {
   $total=count($res);
   for($i=0;$i<$total;$i++) {
    // some action for every record if required
   }
   $out['RESULT']=$res;
  }
?>