#!/usr/bin/php
<?php
$file = $argv[1];

if ($file == "") {
	echo "You Must pass a file\n";
	exit;
}

echo "{\n";
  echo "\"outputs\":[\n";



//lets start reading the file 20 Bytes at a time
$fp = fopen($file,'r');
while (!feof($fp)) {
	$bytes = fread($fp, 19);

	$hex = bin2hex($bytes);
	if (strlen($hex) >= 40) {
		echo "You tried to do something more then 20 Bytes!!!!\n";
		exit;
	}

	while (strlen($hex) < 40) {
		//add some null
		$hex = $hex . "0";
	}


	$hex = "80".$hex;
	$hexbyte = hexStringToByteString($hex);

	$hash1 = hash('sha256',$hexbyte);
	$hash1 =  hexStringToByteString($hash1);
	//do our crc
	$hash2 = hash('sha256',$hash1);

	//need to grab the first 4 bytes of the hex
	$checksum = substr($hash2,0,8);

	//take our hex plus 0x80 and add our checksum
	$hex = $hex . $checksum;


	echo "{\n";
        echo "\"addresses\":[\n";
	echo encodeBase58($hex) ."\n";
        echo "]\n";
        echo "},\n";

}
fclose($fp);
echo "]";
echo "}";

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


function hexStringToByteString($hexString){
    $len=strlen($hexString);

    $byteString="";
    for ($i=0;$i<$len;$i=$i+2){
        $charnum=hexdec(substr($hexString,$i,2));
        $byteString.=chr($charnum);
    }

return $byteString;
}
