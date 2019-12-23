<?php
error_reporting(0);
$pina = "555456"; // PIN AKUN
$merchant = "5414151d-b055-4bf3-8b38-0a69ac305034"; //MERCHANT ID SELLER
$amount = "1"; //Nominal TF
$i = 0;
$listcode = $argv[1];
$codelistlist = file_get_contents($listcode);
$code_list_array = file($listcode);
$code = explode(PHP_EOL, $codelistlist);
$count = count($code);
echo "Total Ada : $count Akun Sob, Gass \n";
while($i < $count) {
  $ptoken = $code[$i];
echo "$ptoken \n";
$header_token = array();
$header_token[] = 'D1: EA:41:DE:EA:37:90:B2:DA:B5:E1:A0:BA:95:DF:F6:15:7F:6C:10:AF:CD:6A:84:CF:76:6F:H2:49:21:OP:ME:DD';
$header_token[] = 'X-AppVersion: 3.36.1';
$header_token[] = 'X-AppId: com.gojek.app';
$header_token[] = 'X-Platform: Android';
$header_login[] = 'X-UniqueId: memek'.rand(79460,79461).'9y91be';
$header_token[] = 'Accept: application/json';
$header_token[] = 'X-Session-ID: 2904f856-25d4-4f2c-a4dd-'.rand(100,999).'c23b'.rand(500,600).'c1';
$header_token[] = 'X-PhoneModel: memek,rog';
$header_token[] = 'X-PushTokenType: FCM';
$header_token[] = 'X-DeviceOS: Android,5.1.1';
$header_token[] = 'User-uuid: ';
$header_token[] = 'X-DeviceToken: ';
$header_token[] = 'Authorization: Bearer '.$ptoken.'';
$header_token[] = 'Accept-Language: en-ID';
$header_token[] = 'X-User-Locale: en_ID';
$header_token[] = 'X-M1: 1:__b512d9b2626145'.rand(9000,1000).'ec86d756bbfb22,2:48437154,3:1567925007701-7041370982918632882,4:7935,5:|2400|2,6:UNKNOWN,7:"bsiq820417",8:1280x720,9:passive\,gps,10:1,11:UNKNOWN';
$header_token[] = 'Content-Type: application/json; charset=UTF-8';
$header_token[] = 'User-Agent: okhttp/3.12.1';



   $detectmerch =curl('https://api.gojekapi.com/v1/explore', '{"data":"{\"activity\":\"GP:MT\",\"data\":{\"receiverid\":\"'.$merchant.'\"}}","type":"QR_CODE"}', $header_token);
   $jsdetectmerch = json_decode($detectmerch[0]);

   $namatoko = $jsdetectmerch->data->activity_data->receiver->name;
   if($namatoko == null){
    echo $jsdetectmerch->errors[0]->message;
    echo "\n"; echo "\n";
   } else {
   echo "Nama Toko = ";
   echo $namatoko;
   echo "\n";
   echo "[+] Processing... \n";
   $bodymerch = '{"amount":'.$amount.',"metadata":{"tags":"{ \"service_type\": \"GOPAY_OFFLINE\" }","channel_type":"STATIC_QR","merchant_cross_reference_id":"'.$merchant.'","external_merchant_name":":'.$namatoko.'"},"payment_request_type":"STATIC_QR","receiver_payment_handle":"'.$merchant.'"}';
   $tfmerch = curl('https://api.gojekapi.com/v1/payment', $bodymerch, $header_token);
   $jstfmerch = json_decode($tfmerch[0]);
   $idbayar = $jstfmerch->data->reference_id;
   echo "[+] Reference_id : ";
   echo $idbayar;
   echo "\n";
   $gettoken = curl('https://api.gojekapi.com/payment-switch/payment-options?intent=STATIC_QR', null, $header_token);
   $jsgettoken = json_decode($gettoken[0]);
   $tokenbayar = $jsgettoken->data->payment_options->token;
   $bodycekout ='{"promotion_ids":[],"reference_id":"'.$idbayar.'","token":"'.$tokenbayar.'"}';
   $header_token[] = 'pin:'.$pina.'';
   $ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://api.gojekapi.com/v1/payment?action=fulfill');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "{\"promotion_ids\":[],\"reference_id\":\"".$idbayar."\",\"token\":\"".$tokenbayar."\"}");
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
curl_setopt($ch, CURLOPT_HTTPHEADER, $header_token);
$result = curl_exec($ch);
$jsam = json_decode($result,true);
if($jsam['success'] == null){
  echo "GAGAL = ".$jsam['errors'][0]['message']." \n";
  echo "\n";
} else {
echo $result;
echo "\n";
echo "\n";
 $livee = "transaksi_id.txt";
    $fopen = fopen($livee, "a+");
    $fwrite = fwrite($fopen, " ".$result." \n");
    fclose($fopen);
   
  } 
} $i++;
}
   function curl($url, $fields = null, $headers = null)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        if ($fields !== null) {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        }
        if ($headers !== null) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        $result   = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        return array(
            $result,
            $httpcode
        );
	}
