<?php
$text = "John Hass";
#--------------------------------------------
//while (strlen($text) < 35) {
	//$text = $text . "00";
//}
echo strlen($text) ."\n";
$hex = bin2hex($text);
while (strlen($hex) < 40) {
	$hex = $hex . "0";
}



$hash160 = hash('ripemd160', hash('sha256',$hex));
$version = 0x00;
$hashandbytes = hex2bin ($hash160);
$doubleSHA = hash('sha256',hash('sha256',$hashandbytes));
$checksum = substr($doubleSHA,0,8);
//echo $checksum;
echo "D: " .$doubleSHA ."\n";

$unencodedAddress = "00" . $hash160 .$checksum;


echo encodeBase58($unencodedAddress) ."\n";



function hexStringToByteString($hexString){
    $len=strlen($hexString);
 
    $byteString="";
    for ($i=0;$i<$len;$i=$i+2){
        $charnum=hexdec(substr($hexString,$i,2));
        $byteString.=chr($charnum);
    }
 
return $byteString;
}


function bc_hexdec($num) {
    return bc_arb_decode(strtolower($num), '0123456789abcdef');
}
function bc_dechex($num) {
    return bc_arb_encode($num, '0123456789abcdef');
}


function hash160ToAddress($hash160,$addressversion=00) {
      $hash160=$addressversion.$hash160;
      $check=pack('H*' , $hash160);
      $check=hash('sha256',hash('sha256',$check,true));
      $check=substr($check,0,8);

      $hash160=strtoupper($hash160.$check);
      return encodeBase58($hash160);
}


function encodeBase58($hex) {
        $orighex=$hex;
        $chars='123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz';
        $hex=decodeHex($hex);
        $return='';
            while (bccomp($hex,0)==1) {
              $dv=(string)bcdiv($hex,'58',0);
              $rem=(integer)bcmod($hex,'58');
              $hex=$dv;
              $return=$return.$chars[$rem];
            }
        $return=strrev($return);
  
        // Leading zeros
        for($i=0;$i<strlen($orighex)&&substr($orighex,$i,2)=='00';$i+=2) {
          $return='1'.$return;
	}
	return $return;
  }


function decodeHex($hex) {
      $hex=strtoupper($hex);
      $chars='0123456789ABCDEF';
      $return='0';
      for($i=0;$i<strlen($hex);$i++) {
        $current=(string)strpos($chars,$hex[$i]);
            $return=(string)bcmul($return,'16',0);
            $return=(string)bcadd($return,$current,0);
      }
      return $return;
  }

 function encodeHex($dec) {
      $chars='0123456789ABCDEF';
      $return='';
          while (bccomp($dec,0)==1) {
            $dv=(string)bcdiv($dec,'16',0);
            $rem=(integer)bcmod($dec,'16');
            $dec=$dv;
            $return=$return.$chars[$rem];
          }
      return strrev($return);
  }


?>
