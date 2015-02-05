<?php


$dictionary=array(

'KEYWORD'=>'Keyword',
'CONTENT_SOURCE'=>'Content source'

/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
