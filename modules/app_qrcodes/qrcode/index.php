<?php    
    
   
include "qrlib.php";    
    
$errorCorrectionLevel="M";
if ($_REQUEST['size']) {
 $matrixPointSize=(int)$_REQUEST['size'];
} else {
 $matrixPointSize=3;
}
QRcode::png($_REQUEST['data'], false, $errorCorrectionLevel, $matrixPointSize, 2);

    