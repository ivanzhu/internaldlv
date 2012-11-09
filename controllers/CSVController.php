<?php
//ini_set('post_max_size', '500M');
//ini_set('upload_max_filesize', '499M');

require_once '../config/settings.inc.php';
require_once '../config/defines.inc.php';
require_once '../class/ZipFloder.php';

/**
 * 
 * @author Ivan Zhu <wenhua.ivan@gmail.com>
 */
class CSVController {
	/* encoding */

	public $encodingTo;
	public $encodingFrom;

	/* image width */
	public $widthLarge;
	public $widthSmall;

	/* mysql info */
	private $host;
	private $userName;
	private $userPass;
	private $link;

	/* file info */
	public $fileDir;  //like ./2012/12/03/0001
	public $fullFileName; // like 201212030001
	public $fileNameSuffix; //like 0001
	public $uploadFile;  //zip file that uploaded name like csv10291200.csv.zip etc.
	public $filePathBaseDownload; //like ./2012/12/03/0001/201212030001
	public $filePathShowPic;   // like ./2012/12/03/0001/201212030001/0001
	public $filePathDescPic;   // like ./2012/12/03/0001/201212030001/0001_DESC
	public $filePathCsv; //like ./2012/12/03/0001/201212030001/0001.csv

	/* csv array */
	public $csvSource = array();
	public $csvDeal = array();
	public $csvSave = array();

	/* references */
	public $references = array();

	/* images */
	public $showImages = array();  //showImage from file

	/* web url */
	public $imageDescUrl = 'http://192.168.1.218/localpresta/img/desc';

	/* errors */
	public $errors = array();

	public function __construct($host, $userName, $userPass, $widthLarge, $widthSmall, $encodingTo = 'UTF-8', $encodingFrom = 'UTF-16LE') {
		$this->widthLarge = $widthLarge;
		$this->widthSmall = $widthSmall;

		$this->host = $host;
		$this->userName = $userName;
		$this->userPass = $userPass;
		$this->initDb();

		$this->encodingTo = $encodingTo;
		$this->encodingFrom = $encodingFrom;
	}

	private function initDb() {
		if (!$this->link = mysql_connect($this->host, $this->userName, $this->userPass)) {
			$this->errors[] = 'SQL error: ' . mysql_errno() . ' ' . mysql_error();
			exit();
		} else {
			if (!mysql_select_db('internaldlv', $this->link)) {
				$this->errors[] = 'SQL error: ' . mysql_errno() . ' ' . mysql_error();
				exit();
			}
		}
	}

	public function convert() {
		echo '<pre>';
		//upload zip file to destition
		$date = date('Ymd');
		if (!count($_FILES)) {
			$this->errors[] = 'Upload file error: No File uploaded';
			exit();
		}
		$this->fileNameSuffix = $this->getFileNameSuffix();
		$this->fullFileName = $date . $this->fileNameSuffix;

		$this->fileDir = _UPLOAD_CSV_DIR_ . date('Y') . '/' . date('m') . '/' . date('d') . '/' . $this->fileNameSuffix;
		/* create file dir for upload */
		$this->makeDir($this->fileDir);
		/* upload the zip file */
		$this->uploadFile = $this->uploadFile($this->fileDir);

		//unzip zip file
		$zip = new ZipArchive;
		if ($zip->open($this->uploadFile) === TRUE) {
			$zip->extractTo($this->fileDir . '/' . $this->fullFileName);
			$zip->close();
			echo 'ok';
		} else {
			echo 'failed';
		}

		$this->filePathBaseDownload = $this->fileDir . '/' . $this->fullFileName;
		$this->filePathCsv = $this->filePathBaseDownload . '/' . $this->fileNameSuffix . '.csv';
		$this->filePathDescPic = $this->filePathBaseDownload . '/' . $this->fileNameSuffix . '_DESC';
		$this->filePathShowPic = $this->filePathBaseDownload . '/' . $this->fileNameSuffix;
		//rename file name at level 1
		$this->renameFileName($this->filePathBaseDownload, $this->filePathCsv, $this->filePathShowPic, $this->filePathDescPic);

		//deal with csv content
		/* get csv content */
		$this->csvSource = $this->getCsvContent($this->filePathCsv);
		/* format csv array and add reference, delete unuse fields(include prodcut desc pic) */
		$this->csvDeal = $this->csvFormat($this->csvSource);

		/* deal with show pic */
		$this->csvImageShow($this->filePathShowPic);

		/* deal with desc image */
		$this->csvImageDesc($this->filePathDescPic);

		/* reformat new csv  */
		$this->csvSave = $this->csvForInput($this->csvDeal);

		/* put csv to csv file */
		$this->csvOutputToFile($this->csvSave);

		/* zip files */
		$this->zipFiles($this->fileDir . '/' . $this->fullFileName . '.zip', $this->filePathBaseDownload);

		/*  */
		$this->gennerateDownLink($this->fileDir . '/' . $this->fullFileName . '.zip');
//		echo '<pre>';
//		var_dump($this->csvDeal);
//		echo '</pre>';
	}

	public function gennerateDownLink($fullPath) {
		// Must be fresh start 
		if (headers_sent())
			die('Headers Sent');

		// Required for some browsers 
		if (ini_get('zlib.output_compression'))
			ini_set('zlib.output_compression', 'Off');

		// File Exists? 
		if (file_exists($fullPath)) {

			// Parse Info / Get Extension 
			$fsize = filesize($fullPath);
			$path_parts = pathinfo($fullPath);
			$ext = strtolower($path_parts["extension"]);

			// Determine Content Type 
			switch ($ext) {
				case "pdf": $ctype = "application/pdf";
					break;
				case "exe": $ctype = "application/octet-stream";
					break;
				case "zip": $ctype = "application/zip";
					break;
				case "doc": $ctype = "application/msword";
					break;
				case "xls": $ctype = "application/vnd.ms-excel";
					break;
				case "ppt": $ctype = "application/vnd.ms-powerpoint";
					break;
				case "gif": $ctype = "image/gif";
					break;
				case "png": $ctype = "image/png";
					break;
				case "jpeg":
				case "jpg": $ctype = "image/jpg";
					break;
				default: $ctype = "application/force-download";
			}

			header("Pragma: public"); // required 
			header("Expires: 0");
			header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
			header("Cache-Control: private", false); // required for certain browsers 
			header("Content-Type: $ctype");
			header("Content-Disposition: attachment; filename=\"" . basename($fullPath) . "\";");
			header("Content-Transfer-Encoding: binary");
			header("Content-Length: " . $fsize);
			ob_clean();
			flush();
			readfile($fullPath);
		} else
			die('File Not Found');
	}

	public function zipFiles($files, $folder) {
		$zip = new ZipFolder($files, $folder);
	}

	public function csvOutputToFile($csv) {
		$fp = fopen($this->filePathCsv, 'w');
		foreach ($csv as $line) {
			fputcsv($fp, $line, "\t", "'");
		}
		fclose($fp);
	}

	public function csvForInput($csv) {
		$template = array('Product Name (ZH)', 'Price', 'Product Images', 'Location', 'Reference', 'Description (FR)');

		$content = array();
		$content[] = $template;
		$i = 1;
		foreach ($csv as $ref => $row) {
			foreach ($row as $key => $value) {
				$content[$i][] = $value;
			}
			$i++;
		}
		return $content;
	}

	public function csvImageDesc($descPath) {
		/* there is a bug about '宝贝名称', you should fixed in the futher */
		foreach ($this->csvDeal as $ref => $row) {
			foreach ($row as $key => $value) {
				$str = str_replace("/", '', $value);
//				echo $str . '</br>';
//				$str = str_replace(array("/",''),array('',), $subject, $count)
				if (!@rename($this->filePathDescPic . '/' . $str, $this->filePathDescPic . '/' . $ref)) {
//					$this->errors[] = 'csvImageDesc error: rename file (' . $this->filePathDescPic . '/' . $value . ')';
				} else {
					$webUrl = $this->imageDescUrl . '/' . substr($this->fullFileName, 0, 4) . '/' . substr($this->fullFileName, 4, 2) . '/' . substr($this->fullFileName, 6, 2) . '/' . substr($this->fullFileName, 8, 4);
					foreach ($this->csvDeal as $ref => $row) {
						$html = '';
						if (is_dir($this->filePathDescPic . '/' . $ref)) {
							if ($dh = opendir($this->filePathDescPic . '/' . $ref)) {
								while (($file = readdir($dh)) !== FALSE) {
									if ($file != '.' and $file != '..') {
										$imageInfo = getimagesize($this->filePathDescPic . '/' . $ref . '/' . $file);
										$imageUrl = $webUrl . '/' . $ref . '/' . $file;

										if ($imageInfo[0] >= $this->widthLarge) {
											$html .= '<img src="' . $imageUrl . '" width="' . $this->widthLarge . '" />';
										} else {
											$html .= '<img src="' . $imageUrl . '" width="' . $this->widthSmall . '" />';
										}
									}
								}
								closedir($dh);
							}
						}
						$this->csvDeal[$ref]['desc'] = $html;
					}
				}
				break;
			}
		}
	}

	public function csvImageShow($showPath) {
		/* rename show image name */
		$this->showImages = $this->ImageShow($showPath);
		/* reformat show image list */
		foreach ($this->csvDeal as $ref => $row) {
			foreach ($row as $key => $value) {
				if ($key == '新图片') {
					$images = array();
					$ex1 = explode('|;', $value);
					foreach ($ex1 as $item) {
						$ex2 = explode(':', $item);
						$imageName = $ex2[0] . '.jpg';
						if (in_array($imageName, $this->showImages))
							$images[] = $imageName;
					}
				}
			}
			$this->csvDeal[$ref]['新图片'] = implode(';', $images);
			unset($images);
		}
//		echo '<pre>';
//		var_dump($this->csvDeal);
//		echo '</pre>';
	}

	public function ImageShow($showPath) {
		if (is_dir($showPath)) {
			if ($dh = opendir($showPath)) {
				$showImagesTbi = array();
				while (($file = readdir($dh)) !== FALSE) {
					if ($file != '.' and $file != '..') {
						//need update in ther futher
						$showImagesTbi[] = $file;
					}
				}
				closedir($dh);
			}
		}

		$showImagesJpg = array();
		foreach ($showImagesTbi as $image) {
			$temp = substr($image, 0, -4) . '.jpg';
			rename($showPath . '/' . $image, $showPath . '/' . $temp);
			$showImagesJpg[] = $temp;
		}

		return $showImagesJpg;
	}

	public function csvFormat($csvArray) {
		$fieldArray = $csvArray[0];
		array_shift($csvArray);
		$productNum = count($csvArray);
		$reference = $this->getReference($productNum, 'B');
		$temp = array();
		for ($i = 0; $i < $productNum; $i++) {
			$temp[$reference[$i]] = array_combine($fieldArray, $csvArray[$i]);
			$temp[$reference[$i]]['reference'] = $reference[$i];
		}

		//need update in the futher
		$csvNew = array();
		$filedNeed = array('宝贝名称', '宝贝价格', '新图片', '宝贝链接', 'reference');


		foreach ($temp as $ref => $row) {
			//have a bug in here : when $key is 宝贝名称, buy not equel with $filedNeed[0](宝贝名称)
//			$i = 0;
			foreach ($row as $key => $value) {
//				if ($i === 0) {
//					$csvNew[$ref][$key] = $value;
//					$i++;
//				}
				if (in_array($key, $filedNeed))
					$csvNew[$ref][$key] = $value;
			}
		}
		return $csvNew;
	}

	public function getReference($productNum, $prefix) {
		$dateTimeStamp = strtotime(date("F j, Y"));
		$sqlSelect = 'select reference from `csv_reference` where created = ' . $dateTimeStamp;
		if (($result = mysql_query($sqlSelect, $this->link))) {
			if (mysql_num_rows($result)) {
				$startNumber = '';
				while ($row = mysql_fetch_assoc($result)) {
					$startNumber = $row['reference'] + 1;
				}
				mysql_free_result($result);

				$newReference = $startNumber + $productNum - 1;
				$sqlUpdate = 'update `csv_reference` set reference =' . $newReference . ', modified=' . time() . ' where created = ' . $dateTimeStamp;
				if (!mysql_query($sqlUpdate, $this->link)) {
					$this->errors[] = 'SQL error : ' . mysql_errno() . ' ' . mysql_error();
					exit();
				}
			} else {
				$sqlInsert = 'insert into `csv_reference` (`reference`,`modified`,`created`) values(1,' . time() . ',' . $dateTimeStamp . ');';
				if (mysql_query($sqlInsert, $this->link)) {
					$startNumber = 1;
				} else {
					$this->errors[] = 'SQL error : ' . mysql_errno() . ' ' . mysql_error();
					exit();
				}
			}
		} else {
			$this->errors[] = 'SQL error: ' . mysql_errno() . ' ' . mysql_error();
			exit();
		}

		$reference = array();
		for ($i = 0; $i < $productNum; $i++) {
			$reference[] = 'D' . date("ymd") . $prefix . str_pad($startNumber + $i, 4, '0', STR_PAD_LEFT);
		}

		return $reference;
	}

	public function getCsvContent($csvFile) {
		if (!$csv = file_get_contents($csvFile)) {
			$this->errors[] = 'Get CSV contents error: can not read csv contents';
			exit();
		}
		
		$csv = trim($csv, "\xFF,\xFE");
//		if (!$csvNew = iconv($this->encodingFrom, $this->encodingTo, $csv)) {
		if (!$csvNew = mb_convert_encoding($csv, $this->encodingTo, "$this->encodingFrom")) {
			$this->errors[] = 'Get csv contents error: can not convert csv encoding';
			exit();
		}

//		$csvNew = str_replace('﻿', '', $csvNew);
//		echo strlen($csvNew);
//		$csvNew = trim()
		
		if (!file_put_contents($csvFile, $csvNew)) {
			$this->errors[] = 'Get csv contents error: can not pub csv to file';
			exit();
		}
		if (!$fh = fopen($csvFile, 'rb')) {
			$this->errors[] = 'Get csv contents error: can not read new csv contents';
			exit();
		}
		$data = array();
		while ($row = fgetcsv($fh, 40000, "\t", "\n")) {
			$data[] = $row;
		}
//		var_dump($this->csvSource);
		return $data;
	}

	/**
	 * rename file name at level 1
	 */
	public function renameFileName($dir, $csv, $show, $descs) {
		//conver filenames encoding from GBK to UTF-8
		system('convmv -f GBK -t UTF-8 -r --notest ' . $dir . ' > /dev/null');
		$dir .= '/';
		if (is_dir($dir)) {
			if (($dh = opendir($dir))) {
				$files = array();
				while (($file = readdir($dh)) !== false) {
					if ($file != '.' && $file != '..') {
						$files[$file]['type'] = filetype($dir . $file);
					}
				}
				closedir($dh);

				if (count($files) != 3) {
					$this->errors[] = 'Rename File Name error: not enough file';
					exit();
				}

				$csvName = '';
				$i = 0;
				foreach ($files as $key => $value) {
					if ($value['type'] == 'file') {
						$csvName = $key;
						if (substr($csvName, -4) != '.csv') {
							$this->errors[] = 'Rename FileName error: No csv File';
							exit();
						}
						$i++;
					}
					if ($value['type'] == 'dir') {
						$i++;
					}
				}

				if ($i != 3) {
					$this->errors[] = 'Rename FileName error: wrong file counts';
					exit();
				}
				$mainName = substr($csvName, 0, -4);

				foreach ($files as $key => $value) {
					if ($value['type'] == 'dir') {
						$tag = true;
						if ($mainName == $key) {
							$tag = FALSE;
							continue;
						}
						if ($mainName . '_描述图片' == $key) {
							$tag = FALSE;
							continue;
						}
						if ($tag) {
							$this->errors[] = 'Rename FileName error: please Check you zip archive files';
							exit();
						}
					}
				}
				if (!rename($dir . $mainName, $show) or !rename($dir . $mainName . '_描述图片', $descs) or !rename($dir . $mainName . '.csv', $csv)) {
					$this->errors[] = 'Rename FileName error: Can\'t rename file';
					exit();
				}
			} else {
				$this->errors[] = 'Rename FileName error: Could\'t open ' . $dir;
				exit();
			}
		} else {
			$this->errors[] = 'Rename FileName error: ' . $dir . ' is not a dir';
			exit();
		}
	}

	/**
	 * create the file dir ./2012/12/02/0073
	 * @param type $fileDir
	 */
	public function makeDir($fileDir) {
		if (!mkdir($fileDir, 0775, true)) {
			$this->errors[] = 'create file dir error: ' . $fileDir . 'can\'t be created';
			exit();
		}
		if (!chmod($fileDir, 0775)) {
			$this->errors[] = 'create file dir error: ' . $fileDir . 'can\'t be chmod';
			exit();
		}
	}

	public function uploadFile($uploadDir) {
//		var_dump($_FILES);
		foreach ($_FILES as $file) {
			switch ($file['error']) {
				case '0' :
					if ($file['type'] == 'application/zip') {
						$uploadFile = $uploadDir . '/' . basename($file['name']);
						if (move_uploaded_file($file['tmp_name'], $uploadFile)) {
							return $uploadFile;
						} else {
							$this->errors[] = 'Upload file error: Possible file upload attack!';
							exit();
						}
					} else {
						$this->errors[] = 'Upload file error: please upload zip file';
						exit();
					}
					break;
				case '1' :
					$this->errors[] = 'Upload file error: 上传的文件超过了 php.ini 中 upload_max_filesize 选项限制的值。';
					exit();
					break;
				case '2' :
					$this->errors[] = 'Upload file error:上传文件的大小超过了 HTML 表单中 MAX_FILE_SIZE 选项指定的值。';
					exit();
					break;
				case '3' :
					$this->errors[] = 'Upload file error:文件只有部分被上传。';
					exit();
					break;
				case '4' :
					$this->errors[] = 'Upload file error:没有文件被上传。';
					exit();
					break;
				case '6':
					$this->errors[] = 'Upload file error:，找不到临时文件夹。';
					exit();
					break;
				default :
					$this->errors[] = 'Upload file error:undefined error';
					exit();
					break;
			}
		}
	}

	public function getFileNameSuffix() {
		$dateTimeStamp = strtotime(date("F j, Y"));
		$sqlSelect = 'select fileNumber from `csv_fileName` where created = ' . $dateTimeStamp;
		if (($result = mysql_query($sqlSelect, $this->link))) {
			if (mysql_num_rows($result)) {
				$fileNumber = '';
				while ($row = mysql_fetch_assoc($result)) {
					$fileNumber = $row['fileNumber'];
				}
				mysql_free_result($result);

				$newFileNumber = $fileNumber + 1;
				$sqlUpdate = 'update `csv_fileName` set fileNumber =' . $newFileNumber . ', modified=' . time() . ' where created = ' . $dateTimeStamp;
				if (mysql_query($sqlUpdate, $this->link)) {
					$fileNameSuffix = str_pad($newFileNumber, 4, '0', STR_PAD_LEFT);
				} else {
					$this->errors[] = 'SQL error : ' . mysql_errno() . ' ' . mysql_error();
					exit();
				}
			} else {
				$sqlInsert = 'insert into `csv_fileName` (`fileNumber`,`modified`,`created`) values(1,' . time() . ',' . $dateTimeStamp . ');';
				if (mysql_query($sqlInsert, $this->link)) {
					$fileNameSuffix = '0001';
				} else {
					$this->errors[] = 'SQL error : ' . mysql_errno() . ' ' . mysql_error();
					exit();
				}
			}
		} else {
			$this->errors[] = 'SQL error: ' . mysql_errno() . ' ' . mysql_error();
			exit();
		}

		return $fileNameSuffix;
	}

	private function dispalyErrors() {
		if (count($this->errors)) {
			echo '<pre>';
			foreach ($this->errors as $error) {
				echo $error . '<br/>';
			}
			echo '</pre>';
		}
	}

	public function __destruct() {
		$this->dispalyErrors();

		if ($this->link)
			mysql_close($this->link);
	}

}

$csv = new CSVController('localhost', 'root', 'zhuwenhua', 700, 350);
echo $csv->convert();





