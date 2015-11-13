<?php
$text = "John Hass";
#--------------------------------------------
while (strlen($text) < 20) {
        $text = $text . " ";
}
echo $text ."\n";
echo strlen($text) ."\n";

$hex = bin2hex($text);
while (strlen($hex) < 130) {
	$hex = $hex . "0";
	
}

//$publickey='0450863AD64A87AE8A2FE83C1AF1A8403CB53F53E486D8511DAD8A04887E5B23522CD470243453A299FA9E77237716103ABC11A1DF38855ED6F2EE187E9C582BA6';

$publickey = strtoupper($hex);

echo strlen($publickey) ."\n";

$step1=hexStringToByteString($publickey);

echo "step1 ".$publickey."\n";

// step 2

$step2=hash("sha256",$step1);
echo "step2 ".$step2."\n";

// step 3

$step3=hash('ripemd160',hexStringToByteString($step2));
echo "step3 ".$step3."\n";

// step 4

$step4="00".$step3;
echo "step4 ".$step4."\n";

// step 5

$step5=hash("sha256",hexStringToByteString($step4));
echo "step5 ".$step5."\n";

// step 6

$step6=hash("sha256",hexStringToByteString($step5));
echo "step6 ".$step6."\n";

// step 7

$checksum=substr($step6,0,8);
echo "step7 ".$checksum."\n";

// step 8

$step8=$step4.$checksum;
echo "step8 ".$step8."\n";

// step 9
// base conversion is from hex to base58 via decimal. 
// Leading hex zero converts to 1 in base58 but it is dropped
// in the intermediate decimal stage.  Simply added back manually.

$step9="1".bc_base58_encode(bc_hexdec($step8));
echo "step9 ".$step9."\n\n";


function hexStringToByteString($hexString){
    $len=strlen($hexString);

    $byteString="";
    for ($i=0;$i<$len;$i=$i+2){
        $charnum=hexdec(substr($hexString,$i,2));
        $byteString.=chr($charnum);
    }

return $byteString;
}

// BCmath version for huge numbers
function bc_arb_encode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $rep = '';

    while( true ){
        if( strlen($num) < 2 ) {
            if( intval($num) <= 0 ) {
                break;
            }
        }
        $rem = bcmod($num, $base);
        $rep = $basestr[intval($rem)] . $rep;
        $num = bcdiv(bcsub($num, $rem), $base);
    }
    return $rep;
}

function bc_arb_decode($num, $basestr) {
    if( ! function_exists('bcadd') ) {
        Throw new Exception('You need the BCmath extension.');
    }

    $base = strlen($basestr);
    $dec = '0';

    $num_arr = str_split((string)$num);
    $cnt = strlen($num);
    for($i=0; $i < $cnt; $i++) {
        $pos = strpos($basestr, $num_arr[$i]);
        if( $pos === false ) {
            Throw new Exception(sprintf('Unknown character %s at offset %d', $num_arr[$i], $i));
        }
        $dec = bcadd(bcmul($dec, $base), $pos);
    }
    return $dec;
}


// base 58 alias
function bc_base58_encode($num) {   
    return bc_arb_encode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
}
function bc_base58_decode($num) {
    return bc_arb_decode($num, '123456789ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnopqrstuvwxyz');
}

//hexdec with BCmath
function bc_hexdec($num) {
    return bc_arb_decode(strtolower($num), '0123456789abcdef');
}
function bc_dechex($num) {
    return bc_arb_encode($num, '0123456789abcdef');
}
?>
