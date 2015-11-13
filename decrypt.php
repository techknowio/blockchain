#!/usr/bin/php
<?php
$file = file_get_contents("image2");
$json =json_decode($file);
//print_r($json->outputs[0]);
foreach($json->outputs as $output) {
	foreach($output->addresses as $address) {
		//$hex = base58_decode($address);
		//print_r($address);
		//echo $hex ."\n";
		//echo $address ."\n";
		$base58 = decodeBase58($address);
		$base58 = substr($base58,2) ."\n";
		$base58 = substr($base58, 0, -9);
		echo hexStringToByteString($base58);
		//exit;
	}
}



function decodeBase58($base58)
{
	$origbase58=$base58;
	
	$chars="123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz";
	$return="0";
	for($i=0;$i<strlen($base58);$i++)
	{
		$current=(string)strpos($chars,$base58[$i]);
		$return=(string)bcmul($return,"58",0);
		$return=(string)bcadd($return,$current,0);
	}
	
	$return=encodeHex($return);
	
	//leading zeros
	for($i=0;$i<strlen($origbase58)&&$origbase58[$i]=="1";$i++)
	{
		$return="00".$return;
	}
	
	if(strlen($return)%2!=0)
	{
		$return="0".$return;
	}
	
	return $return;
}


function encodeHex($dec)
{
	$chars="0123456789ABCDEF";
	$return="";
	while (bccomp($dec,0)==1)
	{
		$dv=(string)bcdiv($dec,"16",0);
		$rem=(integer)bcmod($dec,"16");
		$dec=$dv;
		$return=$return.$chars[$rem];
	}
	return strrev($return);
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


?>
