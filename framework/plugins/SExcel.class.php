<?php
/*{{{LICENSE
+-----------------------------------------------------------------------+
| SlightPHP Framework                                                   |
+-----------------------------------------------------------------------+
| This program is free software; you can redistribute it and/or modify  |
| it under the terms of the GNU General Public License as published by  |
| the Free Software Foundation. You should have received a copy of the  |
| GNU General Public License along with this program.  If not, see      |
| http://www.gnu.org/licenses/.                                         |
| Copyright (C) 2008-2009. All Rights Reserved.                         |
+-----------------------------------------------------------------------+
| Supports: http://www.slightphp.com                                    |
+-----------------------------------------------------------------------+
}}}*/
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."phpexcel/PHPExcel.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."phpexcel/PHPExcel/IOFactory.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."phpexcel/PHPExcel/Reader/Excel2007.php");
require_once(dirname(__FILE__).DIRECTORY_SEPARATOR."phpexcel/PHPExcel/Reader/Excel5.php");
/**
 * @package SlightPHP
 */
class SExcel extends PHPExcel{
	function __constructor($filePath){
		$PHPReader = new PHPExcel_Reader_Excel5();
		if(!$PHPReader->canRead($filePath)){					
			$PHPReader = new PHPExcel_Reader_Excel2007();
			if(!$PHPReader->canRead($filePath)){						
				throw new Exception();
				return false;
			}else{
				return $PHPReader;
			}
		}else{
			return $PHPReader;
		}
	}
}
?>