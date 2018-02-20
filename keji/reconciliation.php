<?php

ini_set('display_errors', 1);
ini_set('auto_detect_line_endings', TRUE);
ini_set('memory_limit', -1);
set_time_limit(0);
error_reporting(E_ERROR);

$response = "";

$download1 = "";
$download2 = "";
$download3 = "";

$ready_for_download=false;
$target_dir = "uploads/";
$download_dir = "downloads/";

$ran = mt_rand();

function RemoveDownloadOldCSV(){

    foreach ( glob('downloads/*.csv') as $csv ){
        unlink($csv);
    }
}

function RemoveUploadOldCSV(){

    foreach ( glob('uploads/*.csv') as $csv ){
        unlink($csv);
    }
}


RemoveDownloadOldCSV();
RemoveUploadOldCSV();

/*Headers for Mobifin files*/
function MobifinCsv($download1, $mainInfo1){
    fputcsv($download1, array('Mobile','Provider Topup Value','Request Time'));
    foreach ($mainInfo1 as $field) {
        fputcsv($download1, $field);
    }

    fclose($download1);
}

/*Headers for Creditswitch files*/
function Csw_Csv($download1, $mainInfo1){
    fputcsv($download1, array('Recipient','FaceValue','Date'));
    foreach ($mainInfo1 as $field) {
        fputcsv($download1, $field);
    }

    fclose($download1);
}
/*Headers for files not found in Connection_error*/
function ConErrorCsv($download2, $mainInfo2){
    fputcsv($download2, array('MSISDN','AMOUNT', 'DATE'));
    foreach ($mainInfo2 as $field) {
        fputcsv($download2, $field);
    }

    fclose($download2);
}


/*Mobifin file against Networks*/
if(isset($_POST['submit']) && $_POST['source']!=="" && !empty($_FILES['mobifinfile']['name']))
{
    print_r('Got to Mobifin block');
    //upload files and check for validity
    $source = $_POST['source'];

    $download1 = fopen('downloads/File1.csv', 'w');
    $download2= fopen('downloads/File2.csv', 'w');

    $mobifin_file = $target_dir .$ran . basename($_FILES["mobifinfile"]["name"]);
    $cmp_file=  $target_dir .$ran . basename($_FILES["cmpfile"]["name"]);

    $uploadOk = 1;

    $docFileType1 = pathinfo($mobifin_file, PATHINFO_EXTENSION);
    $docFileType2 = pathinfo($cmp_file, PATHINFO_EXTENSION);

    if (strtolower($docFileType1) != "csv" || strtolower($docFileType2) != "csv")
        $uploadOk = 0;

    if ($uploadOk == 0 ) {
        $response = "Files not uploaded. Possible problem: unsupported file format or Compare File Source not selected";
    } else {
        if (move_uploaded_file($_FILES["mobifinfile"]["tmp_name"], $mobifin_file)
            && move_uploaded_file($_FILES["cmpfile"]["tmp_name"], $cmp_file)) {

            $mobifinTarget = fopen( $mobifin_file, "r");
            $otherTarget = fopen( $cmp_file, "r");

            /*put the required fields into an array */
            $mainData1 = [];
            $mainInfo1 = [];

            while (($data1 = fgetcsv($mobifinTarget, 100000)) !== false) {

                /*save column fields to variable*/
                $reqTime =  preg_replace("/[^0-9.]/", "",$data1[1]);//remove any special character
                $dTime1 = (abs(strtotime($reqTime))); // convert date to time stamp
                $phonenumber =  preg_replace("/[^0-9.]/", "",$data1[8]);
                $phonenumber1 = substr($phonenumber,1); //remove the first zero
                $amount1 =  preg_replace("/[^0-9.]/", "",$data1[31]);

                $mainData1[] = [ $dTime1, $phonenumber1, number_format((int)$amount1,0)];
                $mainInfo1[] = [$data1[8],$data1[31],$data1[1]];

            }
            /*remove header from the array*/
            $mainData1 = array_slice($mainData1, 1);
            $mainInfo1 = array_slice($mainInfo1, 1);

            switch ($source) {
                /*--------------------------------------------------Glo--------------------------------------------------------*/
                case "Glo":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget, 100000)) !== false) {

                        $reqTime2 = $data2[1];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phone = $data2[5];
                        $phonenumber2 = substr($phone,3);//remove the first 3 character
                        $amount2 = $data2[8];


                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[5],$data2[8],$data2[1]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1] ;
                            $c = $element2[2] == $element[2] ;
                            if(/*$a &&*/  $b && $c) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;
                                $count++;
                                break;

                            }
                        }

                    }

                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('Receiver MSISDN','Amount','Transaction Date'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;
                /*--------------------------------------------------Airtel----------------------------------------------------------*/
                case "Airtel":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[5];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phonenumber2 =  $data2[3];
                        $amount2 = $data2[4];

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[3],$data2[4],$data2[5]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = $element2[1] == $element[1] ;
                            $b = $element2[2] == $element[2] ;
                            if($a && $b ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }
                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('RECEIVER_MSISDN', 'AMOUNT',' TRANSFER_DATE'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;
                /*--------------------------------------------------MTN----------------------------------------------------------*/
                case "MTN":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {
                        $reqTime2 = $data2[2];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phone =  preg_replace("/[^0-9.]/", "",$data2[4]);
                        $phonenumber2 = substr($phone,1);
                        $amount2 = preg_replace("/[^0-9.]/", "",$data2[5]);

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[4],$data2[5],$data2[2]];

                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1] ;
                            $c = $element2[2] == $element[2] ;
                            if($a && $b && $c ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }

                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('MSISDN','Amount','Date'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;
                /*--------------------------------------------------Capricorn----------------------------------------------------------*/
                case "Capricorn":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $req = $data2[1];
                        $reqTime2 =  date('Y-m-d H:i:s', strtotime(str_replace('/','-',$req)));
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $amount2 = preg_replace("/[^0-9.]/", "",$data2[5]);

                        $mainData2[] = [ $dTime2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2],$data2[3],$data2[4],$data2[5],$data2[6],$data2[7],$data2[8]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[2] ;

                            if($a && $b) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }

                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('Transaction No.', 'Date/Time', 'Status', 'Service', 'Handler', 'Amount', 'Consumer', 'Consumer Commission (%)', 'Consumer Commission (NGN)'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;
                /*----------------------------------------------------Etisalat----------------------------------------------------------*/
                case "Etisalat":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[7];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phonenumber2 =  $data2[6];
                        $amount2 = preg_replace("/[^0-9.]/", "",  $data2[1]);

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[6],$data2[1],$data2[7]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1];
                            $c = $element2[2] == $element[2];
                            if(/* $a &&*/ $b && $c ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;
                                $count++;
                                break;

                            }

                        }

                    }

                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('Subscribers MSISDN (Naira)','Face Value (Naira)','Transmission Date'));

                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;
                /*----------------------------------------------------Interswitch----------------------------------------------------------*/
                case "Interswitch":

                    $mainData1 = [];
                    $mainInfo1 = [];

                    while (($data = fgetcsv($mobifinTarget)) !== false) {

                        $reqTime =  preg_replace("/[^0-9.]/", "",$data[1]);
                        $dTime1 =(abs(strtotime( 'Y-m-d',$reqTime)));
                        $amount1 =  preg_replace("/[^0-9.]/", "",$data[31]);

                        $mainData1[] =[ $dTime1 , number_format((int)$amount1,0)];

                        $mainInfo1[] = [$data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7],$data[8],$data[9],$data[10],$data[11],$data[12],$data[13],$data[14],$data[15],$data[16],$data[17],$data[18],$data[19],$data[20],$data[21],$data[22],$data[23],$data[24],$data[25],$data[26],$data[27],$data[28],$data[29],$data[30],$data[31],$data[32],$data[33],$data[34],$data[35],$data[36]];


                    }

                    $mainData1 = array_slice($mainData1, 1);
                    $mainInfo1 = array_slice($mainInfo1, 1);

                    /*............................File 2............................*/

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[0];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $amount2 = $data2[2];

                        $mainData2[] = [ $dTime2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2],$data2[3],$data2[4],$data2[5],$data2[6]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }
                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('Date','dealer_code','DR(N)','CR(N)','Current Balance','Previous Balance','Description'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;

                /*----------------------------------------------------Creditswitch----------------------------------------------------------*/
                case "Creditswitch":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[6];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phonenumber2 = $data2[1];
                        $amount2 =  preg_replace("/[^0-9.]/", "",$data2[2]);

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[1],$data2[2],$data2[6]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1] ;
                            $c = $element2[2] - $element[2] <= 2 ;
                            if(/*$a && */$b && $c ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;
                                $count++;
                                break;

                            }

                        }

                    }

                    MobifinCsv($download1, $mainInfo1);

                    fputcsv($download2, array('recipient','amount','date2'));

                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;
                    //$ready_for_download1 = true;

                    break;

                default:
                    echo "Your file is none of these formats!";
            }
        }
    }
}

/*Creditswitch file against Networks*/
if(isset($_POST['submit'])  && !empty($_FILES['cswfile']['name']))//$_FILES['cswfile'] !==""
{
    print_r('Got to Creditswitch block');
    //upload files and check for validity
    $source = $_POST['source'];

    $download1 = fopen('downloads/File1.csv', 'w');
    $download2= fopen('downloads/File2.csv', 'w');

    $csw_file = $target_dir .$ran . basename($_FILES["cswfile"]["name"]);
    $cmp_file=  $target_dir .$ran . basename($_FILES["cmpfile"]["name"]);

    $uploadOk = 1;

    $docFileType1 = pathinfo($csw_file, PATHINFO_EXTENSION);
    $docFileType2 = pathinfo($cmp_file, PATHINFO_EXTENSION);

    if (strtolower($docFileType1) != "csv" && strtolower($docFileType2) != "csv")
        $uploadOk = 0;

    if ($uploadOk == 0 ) {
        $response = "Files not uploaded. Possible problem: unsupported file format or Compare File Source not selected";
    } else {
        if (move_uploaded_file($_FILES["cswfile"]["tmp_name"], $csw_file)
            && move_uploaded_file($_FILES["cmpfile"]["tmp_name"], $cmp_file)) {

            $cswTarget = fopen( $csw_file, "r");
            $otherTarget = fopen( $cmp_file, "r");

            /*put the required fields into an array */
            $mainData1 = [];
            $mainInfo1 = [];

            while (($data1 = fgetcsv($cswTarget, 100000)) !== false) {

                $reqTime1 = $data1[4];
                $dTime1 =(abs(strtotime($reqTime1)));
                $phonenumber1 =  preg_replace("/[^0-9.]/", "",$data1[1]);
                $amount1 =  preg_replace("/[^0-9.]/", "",$data1[3]);

                $mainData1[] =[ $dTime1, $phonenumber1, number_format((int)$amount1,0)];

                $mainInfo1[] = [$data1[1],$data1[3],$data1[4]];

            }
            /*remove header from the array*/
            $mainData1 = array_slice($mainData1, 1);
            $mainInfo1 = array_slice($mainInfo1, 1);


            switch ($source) {
                /*--------------------------------------------------Glo--------------------------------------------------------*/
                case "Glo":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget, 100000)) !== false) {

                        $reqTime2 = $data2[1];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phone = $data2[5];
                        $phonenumber2 = substr($phone,3);//remove the first 3 character
                        $amount2 = $data2[8];


                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[5],$data2[8],$data2[1]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1] ;
                            $c = $element2[2] == $element[2] ;
                            if(/*$a &&*/  $b && $c) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;
                                $count++;
                                break;

                            }
                        }

                    }

                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('Receiver MSISDN','Amount','Transaction Date'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;
                /*--------------------------------------------------Airtel----------------------------------------------------------*/
                case "Airtel":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[4];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phonenumber2 =  $data2[2];
                        $amount2 = $data2[3];

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[2],$data2[3],$data2[4]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = $element2[1] == $element[1] ;
                            $b = $element2[2] == $element[2] ;
                            if($a && $b ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }

                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('RECEIVER_MSISDN', 'AMOUNT',' TRANSFER_DATE'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;
                /*--------------------------------------------------MTN----------------------------------------------------------*/
                case "MTN":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {
                        $reqTime2 = $data2[2];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phone =  preg_replace("/[^0-9.]/", "",$data2[4]);
                        $phonenumber2 = substr($phone,1);
                        $amount2 = preg_replace("/[^0-9.]/", "",$data2[5]);

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[4],$data2[5],$data2[2]];

                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1] ;
                            $c = $element2[2] == $element[2] ;
                            if($a && $b && $c ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }

                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('MSISDN','Amount','Date'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;
                /*--------------------------------------------------Capricorn----------------------------------------------------------*/
                case "Capricorn":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $req = $data2[1];
                        $reqTime2 =  date('Y-m-d H:i:s', strtotime(str_replace('/','-',$req)));
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $amount2 = preg_replace("/[^0-9.]/", "",$data2[5]);

                        $mainData2[] = [ $dTime2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2],$data2[3],$data2[4],$data2[5],$data2[6],$data2[7],$data2[8]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[2] ;

                            if($a && $b) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }

                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('Transaction No.', 'Date/Time', 'Status', 'Service', 'Handler', 'Amount', 'Consumer', 'Consumer Commission (%)', 'Consumer Commission (NGN)'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;
                /*----------------------------------------------------Etisalat----------------------------------------------------------*/
                case "Etisalat":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[7];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $phonenumber2 =  $data2[6];
                        $amount2 = preg_replace("/[^0-9.]/", "",  $data2[1]);

                        $mainData2[] = [ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[6],$data2[1],$data2[7]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = abs($element2[0]- $element[0]) <= 90;
                            $b = $element2[1] == $element[1];
                            $c = $element2[2] == $element[2];
                            if(/* $a &&*/ $b && $c ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;
                                $count++;
                                break;

                            }

                        }

                    }

                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('Subscribers MSISDN (Naira)','Face Value (Naira)','Transmission Date'));

                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;
                /*----------------------------------------------------Interswitch----------------------------------------------------------*/
                case "Interswitch":

                    $mainData1 = [];
                    $mainInfo1 = [];

                    while (($data1 = fgetcsv($cswTarget, 100000)) !== false) {

                        $reqTime1 = $data1[4];
                        $dTime1 =(abs(strtotime($reqTime1)));
                        $phonenumber1 =  preg_replace("/[^0-9.]/", "",$data1[1]);
                        $amount =  preg_replace("/[^0-9.]/", "",$data1[3]);

                        $mainData1[] =[ $dTime1, $phonenumber1, number_format((int)$amount1,0)];

                        $mainInfo1[] = [$data1[0],$data1[1],$data1[2],$data1[3],$data1[4],$data1[5],$data1[6],$data1[7],$data1[8],$data1[9],$data1[10],$data1[11]];

                    }

                    $mainData1 = array_slice($mainData1, 1);
                    $mainInfo1 = array_slice($mainInfo1, 1);

                    /*............................File 2............................*/

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $reqTime2 = $data2[0];
                        $dTime2 =(abs(strtotime($reqTime2)));
                        $amount2 = $data2[2];

                        $mainData2[] = [ $dTime2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2],$data2[3],$data2[4],$data2[5],$data2[6]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";
                                $count++;
                                break;

                            }

                        }

                    }
                    Csw_Csv($download1, $mainInfo1);

                    fputcsv($download2, array('Date','dealer_code','DR(N)','CR(N)','Current Balance','Previous Balance','Description'));
                    foreach ($mainInfo2 as $fields) {
                        fputcsv($download2, $fields);
                    }
                    fclose($download2);

                    $ready_for_download = true;

                    break;

                default:
                    echo "Your file is none of these formats!";
            }
        }
    }
}

/*Mobifin file against Creditswitch*/
elseif(isset($_POST['submit']))
{

    $download1 = fopen('downloads/File1.csv', 'w');
    $download2= fopen('downloads/File2.csv', 'w');

    $mobfile = $target_dir .$ran . basename($_FILES["mobifinfile"]["name"]);
    $cswfile=  $target_dir .$ran . basename($_FILES["cswfile"]["name"]);

    $uploadOk = 1;

    $docFileType1 = pathinfo($mobfile, PATHINFO_EXTENSION);
    $docFileType2 = pathinfo($cswfile, PATHINFO_EXTENSION);

    if (strtolower($docFileType1) != "csv" || strtolower($docFileType2) != "csv")
        $uploadOk = 0;

    if ($uploadOk == 0 ) {
        $response = "Files not uploaded. Possible problem: unsupported file format or Compare File Source not selected";
    } else {
        if (move_uploaded_file($_FILES["mobifinfile"]["tmp_name"], $mobfile)
            && move_uploaded_file($_FILES["cswfile"]["tmp_name"], $cswfile)) {

            $mobTarget = fopen( $mobfile, "r");
            $cswTarget = fopen( $cswfile, "r");

            /*put the required fields into an array */
            $mainData1 = [];
            $mainInfo1 = [];

            while (($data1 = fgetcsv($mobTarget, 100000)) !== false) {

                /*save column fields to variable*/
                $reqTime =  preg_replace("/[^0-9.]/", "",$data1[1]);
                $dTime1 = (abs(strtotime($reqTime)));
                $phonenumber =  preg_replace("/[^0-9.]/", "",$data1[8]);
                $phonenumber1 = substr($phonenumber,1);
                $amount1 =  preg_replace("/[^0-9.]/", "",$data1[31]);

                $mainData1[] = [ $dTime1, $phonenumber1, number_format((int)$amount1,0)];
                $mainInfo1[] = [$data1[8],$data1[31],$data1[1]];

            }
            //remove header from the array
            $mainData1 = array_slice($mainData1, 1);
            $mainInfo1 = array_slice($mainInfo1, 1);


            /*----------------------------------------------Creditswitch-------------------------------------------------*/

            $mainData2 = [];
            $mainInfo2 = [];

            while (($data2 = fgetcsv($cswTarget, 100000)) !== false) {

                $reqTime2 = $data2[4];
                $dTime2 =(abs(strtotime($reqTime2)));
                $phonenumber2 =  preg_replace("/[^0-9.]/", "",$data2[1]);
                $amount2 =  preg_replace("/[^0-9.]/", "",$data2[3]);

                $mainData2[] =[ $dTime2, $phonenumber2, number_format((int)$amount2,0)];

                $mainInfo2[] = [$data2[1],$data2[3],$data2[4]];

            }

            $mainData2 = array_slice($mainData2, 1);
            $mainInfo2 = array_slice($mainInfo2, 1);

            for($i=0; $i < count($mainData1) ; $i++) {
                $element = $mainData1[$i];
                for ($j=0; $j < count($mainData2) ; $j++) {
                    $element2 = $mainData2[$j];
                    $a = abs($element2[0]- $element[0]) <= 90;
                    $b = $element2[1] == $element[1] ;
                    $c = $element2[2] == $element[2] ;
                    if(/*$a &&*/  $b && $c) {

                        $mainData1[$i] = "" ;
                        $mainData2[$j] = "" ;
                        $mainInfo1[$i] = "" ;
                        $mainInfo2[$j] = "" ;
                        $count++;
                        break;

                    }
                }

            }

            MobifinCsv($download1, $mainInfo1);

            fputcsv($download2, array('Recipient','FaceValue','Date'));
            foreach ($mainInfo2 as $fields) {
                fputcsv($download2, $fields);
            }
            fclose($download2);

            $ready_for_download = true;

        }
    }
}

/*Networks against Mobifin Connection_error*/
if(isset($_POST['submit2']) && $_POST['source2']!=="")
{

    $source2 = $_POST['source2'];

    $download3 = fopen('downloads/File.csv', 'w');

    $mobifin_file = $target_dir .$ran . basename($_FILES["mobifin_file"]["name"]);
    $downloaded_file=  $target_dir .$ran . basename($_FILES["dwnddfile"]["name"]);

    $uploadOk = 1;

    $docFileType1 = pathinfo($mobifin_file, PATHINFO_EXTENSION);
    $docFileType2 = pathinfo($downloaded_file, PATHINFO_EXTENSION);

    if (strtolower($docFileType1) != "csv" || strtolower($docFileType2) != "csv")
        $uploadOk = 0;

    if ($uploadOk == 0 ) {
        $response = "Files not uploaded. Possible problem: unsupported file format or Compare File Source not selected";
    } else {
        if (move_uploaded_file($_FILES["mobifin_file"]["tmp_name"], $mobifin_file)
            && move_uploaded_file($_FILES["dwnddfile"]["tmp_name"], $downloaded_file)) {

            $mobifinTarget = fopen( $mobifin_file, "r");
            $otherTarget = fopen( $downloaded_file, "r");

            $mainData1 = [];
            $mainInfo1 = [];

            while (($data1 = fgetcsv($mobifinTarget, 100000)) !== false) {

                $phonenumber =  preg_replace("/[^0-9.]/", "",$data1[8]);
                $phonenumber1 = substr($phonenumber,1);
                $amount1 =  preg_replace("/[^0-9.]/", "",$data1[31]);

                $mainData1[] = [$phonenumber1, number_format((int)$amount1,0)];
                $mainInfo1[] = [$data1[8],$data1[31]];

            }
            /*remove header from the array*/
            $mainData1 = array_slice($mainData1, 1);
            $mainInfo1 = array_slice($mainInfo1, 1);



            switch ($source2) {
                /*--------------------------------------------------Glo----------------------------------------------------------*/
                case "Glo":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget, 100000)) !== false) {

                        $phone = $data2[0];
                        $phonenumber2 = substr($phone,3);
                        $amount2 = $data2[1];

                        $mainData2[] = [$phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];

                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;

                                break;

                            }

                        }

                    }

                    ConErrorCsv($download3, $mainInfo2);

                    $ready_for_download = true;

                    break;
                /*--------------------------------------------------Airtel----------------------------------------------------------*/
                case "Airtel":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $phonenumber2 =  $data2[1];
                        $amount2 = $data2[2];

                        $mainData2[] = [$phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];
                            $a = $element2[1] == $element[1] ;
                            $b = $element2[2] == $element[2] ;
                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;

                                break;

                            }

                        }

                    }

                    ConErrorCsv($download3, $mainInfo2);

                    $ready_for_download = true;

                    break;
                /*--------------------------------------------------MTN----------------------------------------------------------*/
                case "MTN":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $phonenumber =  preg_replace("/[^0-9.]/", "",$data2[0]);
                        $phonenumber2 = substr($phonenumber,1);
                        $amount2 = preg_replace("/[^0-9.]/", "", $data2[1]);

                        $mainData2[] = [$phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];

                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "";
                                $mainInfo2[$j] = "";

                                break;

                            }

                        }

                    }

                    ConErrorCsv($download3, $mainInfo2);

                    $ready_for_download = true;

                    break;

                /*----------------------------------------------------Etisalat----------------------------------------------------------*/
                case "Etisalat":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $phonenumber2 =  $data2[1];
                        $amount2 = preg_replace("/[^0-9.]/", "",  $data2[2]);

                        $mainData2[] = [$phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];

                            if($element2 == $element ) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;

                                break;

                            }

                        }

                    }

                    ConErrorCsv($download3, $mainInfo2);

                    $ready_for_download = true;

                    break;

                /*----------------------------------------------------Creditswitch----------------------------------------------------------*/
                case "Creditswitch":

                    $mainData2 = [];
                    $mainInfo2 = [];

                    while (($data2 = fgetcsv($otherTarget)) !== false) {

                        $phonenumber2 = $data2[1];
                        $amount2 =  preg_replace("/[^0-9.]/", "",$data2[2]);

                        $mainData2[] = [$phonenumber2, number_format((int)$amount2,0)];

                        $mainInfo2[] = [$data2[0],$data2[1],$data2[2]];
                    }

                    $mainData2 = array_slice($mainData2, 1);
                    $mainInfo2 = array_slice($mainInfo2, 1);

                    for($i=0; $i < count($mainData1) ; $i++) {
                        $element = $mainData1[$i];
                        for ($j=0; $j < count($mainData2) ; $j++) {
                            $element2 = $mainData2[$j];

                            if($element2 == $element) {

                                $mainData1[$i] = "" ;
                                $mainData2[$j] = "" ;
                                $mainInfo1[$i] = "" ;
                                $mainInfo2[$j] = "" ;

                                break;

                            }

                        }

                    }

                    ConErrorCsv($download3, $mainInfo2);

                    $ready_for_download = true;

                    break;

                default:
                    echo "Your file is not any of these formats!";
            }
        }
    }
}


?>

<html>
<head><meta http-equiv="Content-Type" content="text/html; charset=gb18030">
    <link href="bootstrap.min.css" rel="stylesheet" />
    <title> Portal</title>
    <style>
        body{padding:25px;background:#B0C4DE}
        h3{width:430px;color:#14285f;text-align:center;border-bottom:3px solid #cf0000;padding-bottom:6px;}
        label{color:#889;}
        input[type=submit]{background:#eee;color:#888;-webkit-transition:0.7s;-moz-transition:0.7s;transition:0.7s;}
        input[type=submit]:hover{background:#667;color:#eee;}
    </style>
</head>
<body>

<div class="container">
    <center><h3> RECONCILIATION PORTAL</h3></center><br>
    <form action="#" method="POST" enctype="multipart/form-data">
        <div class="row">

            <div class="col-lg-6" style="padding:25px;">
                <div class="row" style="background:#fff;box-shadow:2px 3px 15px #aaa;border:1px solid #eef;border-radius:7px;">
                    <label class="col-md-12" style="background:#f5f5f5;padding:5px;">
                        Select Files to Upload:
                    </label>
                    <div class="col-md-7" style="padding:15px;">
                        <br>
                        Upload Mobifin Report <span style="color:#b00;">(Mobifin .csv files only)</span> :
                        <input type="file" class="form-control" name="mobifinfile"/>
                        <br><br>
                        Upload Creditswitch Report <span style="color:#b00;">(Creditswitch .csv files only)</span> :
                        <input type="file" class="form-control" name="cswfile"/>
                        <br><br>
                        Upload Compare File <span style="color:#b00;">(.csv files only)</span> :
                        <input type="file" class="form-control" name="cmpfile"/>
                        <br><br>
                        Compare File Source:
                        <select name="source" class="form-control">
                            <option value="">--SELECT--</option>
                            <option value="Etisalat">9Mobile</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Glo">Glo</option>
                            <option value="MTN">MTN</option>
                            <option value="Capricorn">Capricorn</option>
                            <option value="Interswitch">Inter-switch</option>
                            <option value="Interswitch">Wema</option>
                        </select>
                        <br><br>
                        <input type="submit" value="Compare Files" name="submit"> <br><br>

                        <input type="submit" class="form-control" name="download" value= "Download File1"/>
                        <?php if($ready_for_download && $download1!=="") echo"<a href='downloads/File1.csv' download>Download</a>"?>
                        <br><br>
                        <input type="submit" class="form-control" name="download_two" value= "Download File2"/>
                        <?php if($ready_for_download && $download2!=="") echo"<a href='downloads/File2.csv'  download>Download</a>"?>
                        <br>
                    </div>
                </div>
            </div>
            <!-- This part handles connection error comparison -->
            <div class="col-lg-6" style="padding:25px;">
                <div class="row" style="background:#fff;box-shadow:2px 3px 15px #aaa;border:1px solid #eef;border-radius:7px;">
                    <label class="col-md-12" style="background:#f5f5f5;padding:5px;">
                        Compare against Connection_error:
                    </label>
                    <div class="col-md-7" style="padding:15px;">
                        <br>
                        Upload Mobifin Connection_error Report <span style="color:#b00;">(Mobifin .csv files only)</span> :
                        <input type="file" class="form-control" name="mobifin_file"/>
                        <br><br>
                        Upload Downloaded File <span style="color:#b00;">(.csv files only)</span> :
                        <input type="file" class="form-control" name="dwnddfile"/>
                        <br><br>
                        Compare File Source:
                        <select name="source2" class="form-control">
                            <option value="">--SELECT--</option>
                            <option value="Etisalat">9Mobile</option>
                            <option value="Airtel">Airtel</option>
                            <option value="Glo">Glo</option>
                            <option value="MTN">MTN</option>
                            <option value="Creditswitch">Creditswitch</option>
                        </select>
                        <br><br>
                        <input type="submit" value="Compare Files" name="submit2"> <br><br>

                        <input type="submit" class="form-control" name="download2" value= "Download File"/>
                        <?php if($ready_for_download && $download3!=="") echo"<a href='downloads/File.csv' download>Download</a>"?>
                        <br>
                    </div>
                </div>
            </div>

        </div>
    </form>
    <br><br>
    <?php if(isset($response))echo "<span style='color:#b00;'>".$response."</span>"; ?>
</div>

</body>
</html>