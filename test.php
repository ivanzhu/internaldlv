<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

//if ($handle = opendir('/home/ivan/www/internaldlv/config/../upload/csv/2012/11/06/0036/201211060036/0036_DESC')) {
//    echo "Directory handle: $handle\n";
//    echo "Files:\n";
//	echo '<pre>';
//    /* 这是正确地遍历目录方法 */
//    while (false !== ($file = readdir($handle))) {
//        var_dump($file);
//		echo '<br/>';
//    }
//
////    /* 这是错误地遍历目录的方法 */
////    while ($file = readdir($handle)) {
////        echo "$file\n";
////    }
//echo '</pre>';
//    closedir($handle);
//}
//
//echo $str = '这是正确 地遍历目录方法/';
//echo '<br/>';
//echo str_replace(" ", '\ ', $str);
//echo str_replace("/", '', $str);
//$str = '201211030002';
//echo substr($str,0,4);
//echo '|';
//echo substr($str,4,2);
//echo '|';
//echo substr($str,6,2);
//echo '|';
//echo substr($str,8,4);
//echo '|';
//$list = array(
//	'aaa,bbb,ccc,dddd',
//	'123,456,789',
//	'"aaa","bbb"'
//);
//
//$fp = fopen('file.csv', 'w');
//
//foreach ($list as $line) {
//	fputcsv($fp, split(',', $line));
//}
//
//fclose($fp);
//class ZipFolder {
//
//	protected $zip;
//	protected $root;
//	protected $ignored_names;
//
//	function __construct($file, $folder, $ignored = null) {
//		$this->zip = new ZipArchive();
//		$this->ignored_names = is_array($ignored) ? $ignored : $ignored ? array($ignored) : array();
//		if ($this->zip->open($file, ZIPARCHIVE::CREATE) !== TRUE) {
//			throw new Exception("cannot open <$file>\n");
//		}
//		$folder = substr($folder, -1) == '/' ? substr($folder, 0, strlen($folder) - 1) : $folder;
//		if (strstr($folder, '/')) {
//			$this->root = substr($folder, 0, strrpos($folder, '/') + 1);
//			$folder = substr($folder, strrpos($folder, '/') + 1);
//		}
//		$this->zip($folder);
//		$this->zip->close();
//	}
//
//	function zip($folder, $parent = null) {
//		$full_path = $this->root . $parent . $folder;
//		$zip_path = $parent . $folder;
//		$this->zip->addEmptyDir($zip_path);
//		$dir = new DirectoryIterator($full_path);
//		foreach ($dir as $file) {
//			if (!$file->isDot()) {
//				$filename = $file->getFilename();
//				if (!in_array($filename, $this->ignored_names)) {
//					if ($file->isDir()) {
//						$this->zip($filename, $zip_path . '/');
//					} else {
//						$this->zip->addFile($full_path . '/' . $filename, $zip_path . '/' . $filename);
//					}
//				}
//			}
//		}
//	}
//
//}
//
//$zip = new ZipFolder('./test2.zip','/home/ivan/www/internaldlv/upload/csv/2012/11/06/0059/');



//function downloadFile($fullPath) {
//
//	// Must be fresh start 
//	if (headers_sent())
//		die('Headers Sent');
//
//	// Required for some browsers 
//	if (ini_get('zlib.output_compression'))
//		ini_set('zlib.output_compression', 'Off');
//
//	// File Exists? 
//	if (file_exists($fullPath)) {
//
//		// Parse Info / Get Extension 
//		$fsize = filesize($fullPath);
//		$path_parts = pathinfo($fullPath);
//		$ext = strtolower($path_parts["extension"]);
//
//		// Determine Content Type 
//		switch ($ext) {
//			case "pdf": $ctype = "application/pdf";
//				break;
//			case "exe": $ctype = "application/octet-stream";
//				break;
//			case "zip": $ctype = "application/zip";
//				break;
//			case "doc": $ctype = "application/msword";
//				break;
//			case "xls": $ctype = "application/vnd.ms-excel";
//				break;
//			case "ppt": $ctype = "application/vnd.ms-powerpoint";
//				break;
//			case "gif": $ctype = "image/gif";
//				break;
//			case "png": $ctype = "image/png";
//				break;
//			case "jpeg":
//			case "jpg": $ctype = "image/jpg";
//				break;
//			default: $ctype = "application/force-download";
//		}
//
//		header("Pragma: public"); // required 
//		header("Expires: 0");
//		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
//		header("Cache-Control: private", false); // required for certain browsers 
//		header("Content-Type: $ctype");
//		header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
//		header("Content-Transfer-Encoding: binary");
//		header("Content-Length: " . $fsize);
//		ob_clean();
//		flush();
//		readfile($fullPath);
//	} else
//		die('File Not Found');
//}
//
////downloadFile('/home/ivan/www/internaldlv/upload/csv/2012/11/06/0062/201211060062.zip');
//
//
//echo is_dir('/home/ivan/www/internaldlv/upload/csv/2012/11/07/0003/201211070003/0003_DESC/南极人\专柜正品中等加厚加绒保暖内衣套装\男士女士羊毛黄金绒');
//$dir = "/home/ivan/www/internaldlv/upload/csv/2012/11/07/0003/201211070003/0003_DESC/南极人 专柜正品中等加厚加绒保暖内衣套装 男士女士羊毛黄金绒";
//
//// Open a known directory, and proceed to read its contents
//if (is_dir($dir)) {
//    if ($dh = opendir($dir)) {
//        while (($file = readdir($dh)) !== false) {
//            echo "filename: $file : filetype: " . filetype($dir . $file) . "\n";
//        }
//        closedir($dh);
//    }
//

//echo '<pre>';
//$handle = fopen('./csvutf8.csv','r');
//$row = 1;
////$handle = fopen("test.csv","r");
//while ($data = fgetcsv($handle, 1000, "\t")) {
//    $num = count($data);
//    echo "<p> $num fields in line $row: <br>\n";
//    $row++;
//    for ($c=0; $c < $num; $c++) {
//		echo mb_strlen($data[$c],'utf-8');
//		for($i=0; $i<mb_strlen($data[$c],'utf-8');$i++){
//			echo ord($data[$c][$i]);
//		}
//		if(trim($data[$c],'﻿')=='宝贝名称')
//			echo 'aaaaaaaaaaaaaaaaaaaaaaaaaa';
//        echo $data[$c] . "<br>\n";
//    }
////	﻿		improtant
//}
//fclose($handle);
//echo ord('﻿');
//echo ord('宝');
//echo ord('贝');
//echo chr(229);
//echo '</pre>';



