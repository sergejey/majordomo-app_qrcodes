<?php


$dictionary=array(

'KEYWORD'=>'Ключевое слово',
'CONTENT_SOURCE'=>'Источник данных'

/* end module names */


);

foreach ($dictionary as $k=>$v) {
 if (!defined('LANG_'.$k)) {
  define('LANG_'.$k, $v);
 }
}
