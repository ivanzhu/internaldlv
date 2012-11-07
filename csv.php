<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
//include predefined files

require_once './config/defines.inc.php';
require_once './config/settings.inc.php';

echo '<pre>';

$baseUrl = _BASE_URL_;

if(isset($_GET['action']))
    $action = $_GET['action'];

switch($action){
    case 'upload':
		if(isset($_POST['submit'])){
			$uploadDir = _UPLOAD_CSV_DIR_;
			$uploadFile = $uploadDir . basename($_FILES['uploadFile']['name']);
			
			if(move_uploaded_file($_FILES['uploadFile']['tmp_name'], $uploadFile)){
				echo 'File is valid, and was successfully uploaded.' . chr(13);
				
				// unzip upload file
				$zipFile = new ZipArchive();
				echo $zipFile->open($uploadFile);
				print_r($zipFile);
				
				
			}else{
				echo 'Possible file upload attack!' . chr(13);
			}
			
			var_dump($_FILES);
		}else{
		echo <<<HTML
		<form action="{$baseUrl}controllers/CSVController.php" method="post" enctype="multipart/form-data">
			<p>
				<lable>File Upload</lable>
				<input type="file" name="uploadFile" />
				<span>Only accept *.zip file</span>
			</p>
			<p>
				<label>Submit</label>
				<input type="submit" Value="Upload" name="submit" />
			</p>
		</form>
HTML;
		}

        break;
    
    default :
        echo <<<HTML
        <div>
            Error Here, Please Try Again.
        </div>
   
HTML;
        break;
}

echo '</pre>';



//require_once './classes/PHPExcel/PHPExcel.php';
//$csv = fopen('./csvsource.csv','rb');
//
//echo '<pre>';
//
//$data = array();
//
//while($row = fgetcsv($csv,100000,"\t","\r")){
//    $data[] = $row;
//}
//var_export($data);
////var_dump(mb_detect_encoding($a));
////$a = eval('return '.iconv('UNICODE','UTF-8',var_export($data,true)).';');
////$data = eval('return '.iconv('gbk','utf-8',var_export($data,true)).';');
////$a = var_export($str);
////print_r($a);
//
//echo '</pre>';
//
//fclose($csv);
//
//
////$str = file_get_contents('./csvsource.csv');
////$str2 = iconv('UTF-16LE', 'UTF-8', $str);
//
////echo $str2;






//$fileName = 'csvsource.csv';
//$filePath = getcwd() . "/" . $fileName;
//echo $filePath;
////echo $csv = file_get_contents('./csvutf8.csv');
//if(($csv = file_get_contents('./csvsource.csv')) !== false){
//    echo '-----------';
////    echo '<br/>'.$csv;
////    if(($csvNew = mb_convert_encoding($csv,"UTF-8","UTF-16LE")) !== FALSE){
//    if(($csvNew = iconv("UTF-16LE//IGNORE","UTF-8",$csv)) !== FALSE){
//        echo '+++++++++++';
//        $csvNew = ltrim($csvNew);
//        var_dump($csvNew);
//        if(file_put_contents('./temp.csv',$csvNew) !== false){
//            echo '================';
//            $file = fopen('./temp.csv','rb');
//            $data[] = array();
//            while($row = fgetcsv($file,100000,"\t","\n")){
//                $data[] = $row;
//            }
//			
//            echo '<pre>';
//            print_r($data);
//            echo '</pre>';
//
//            fclose($file);
//        }
//    }
//}





//$csv = file_get_contents('$filePath');
//$csvNew = iconv('UTF-16LE','UTF-8',$csv);
//file_put_contents('$filePath',$csvNew);


//require_once './classes/CSVConvert.php';
//
//echo '<pre>';
//echo $filePath = getcwd().'/'.'csvsource.csv';
//echo '<br/>';
//$csv = new CSVConvert($filePath);
//$csv->getConvertedArray();
//
//print_r($csv->arrayConvered);
//
//echo '</pre>';

