<?php

/* MultipartCompress
 * Copyright (C) 2014 Everton
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * This PHP class compact and split files and merge and decompact files.
 */
namespace frdl\Compress\Zip; 
 
class Helper
{
   
   public static function del($filename, $zipfile){
   	  return self::removeFile($filename, $zipfile);
   } 	
  
  
  public static function add($filename, $zipfilename, $str = null){
        $i     = str_replace('\\', '/',  $filename);
        $o = $zipfilename;
        $zp = new \ZipArchive();
        
        if(file_exists($o)){
            $flags = 0;
        }else{
            $flags = \ZipArchive::CREATE;
        }
        
        $zp->open($o, $flags);
        
        $success = $zp->addFromString($i,$str);
        
        $zp->close();
        
        return $success;
        
  }	
    
      
   public static function removeFile($filename, $zipfile){
   	     if(!file_exists($zipfile))return false;
         $za = new \ZipArchive();
         $za->open($zipfile);
         for( $i = 0; $i < $za->numFiles; $i++ ){
              $stat = $za->statIndex( $i );
			  if($stat['name'] !== $filename)continue;
			   $zip->deleteIndex($i);
			  break;
         } 
	   $za->close(); 
	   return true;
   } 
     
  
     
   public static function removeThumbs($filename){
   	     if(!file_exists($filename))return false;
         $za = new \ZipArchive();
         $za->open($filename);
		 $count = 0;
         for( $i = 0; $i < $za->numFiles; $i++ ){
              $stat = $za->statIndex( $i );
			  if(basename($stat['name']) !== 'Thumbs.db')continue;
			  $za->deleteIndex($i);
			  $count++;
         } 
	   $za->close(); 
	   return $count;
  } 
    	
  public static function getFileContents($archivefilename, $filename, &$buf = null){	
   $zip = new \ZipArchive();
   if (!$zip->open($archivefilename)){
  	  return array(
   	     'ok' => false,
         'error' => self::ZipStatusString(\ZipArchive::ER_OPEN),
    );    	
   }
   $fp = $zip->getStream($filename); //file inside archive
   if(!$fp){
   	  return array(
   	     'ok' => false,
         'error' => self::ZipStatusString(\ZipArchive::ER_OPEN),
    ); 
   }
   $stat = $zip->statName($filename);

    $buf = ""; //file buffer
    ob_start(); //to capture CRC error message
    while (!feof($fp)) {
      $buf .= fread($fp, 2048); //reading more than 2156 bytes seems to disable internal CRC32 verification (bug?)
    }
    $s = ob_get_contents();
    ob_end_clean();
  
    if(stripos($s, "CRC error") != FALSE){
      //  echo 'CRC32 mismatch, current ';    //\ZipArchive::ER_CRC
          $buf = null;
          fclose($fp);
          $zip->close();	
		   	  
    return array(
   	     'ok' => false,
         'error' => self::ZipStatusString(\ZipArchive::ER_CRC),
         'crc32' => sprintf("%08X", crc32($buf)),
         'expected' => sprintf("%08X", $stat['crc']),
       );       

    }

     fclose($fp);
     $zip->close();
     //Done, unpacked file is stored in $buf
         return array(
   	     'ok' => true,
         'crc32' => sprintf("%08X", crc32($buf)),
         'expected' => sprintf("%08X", $stat['crc']),
         'byte' => strlen($buf),
         'sha1' => sha1($buf),
       );   
 }
   
   
  
	
     /**
     * Compact a file in multipart zip archive.
     * @param string $i The file to compact.
     * @param string $o The zip archive (*.zip).
     * @param integer $s The mnax size (in byte) for the parts. 0 to no parts.
     * @return boolean Return number of parts created.
     */
     public static function zip($i, $o, $del = false, $afilename = null, $s = 0){
        $i     = str_replace('\\', '/', $i);
        $zp = new \ZipArchive();
        
        if(file_exists($o)){
            $flags = 0;
        }else{
            $flags = \ZipArchive::CREATE;
        }
        
        $zp->open($o, $flags);
        
		$afilename = (is_string($afilename)) ? $afilename : basename($i);
        $success = $zp->addFile($i, $afilename);
        
        $zp->close();
        
        if(true===$del)unlink($afilename);
         
        if($success){
            if($s > 0){
                return self::split($o, $s);
            }
        }else{
            return false;
        }
        
       
    }

	
		
  /**
   * Zip a folder (include itself).
   * Usage:
   *   MultipartCompress::zipDir('/path/to/sourceDir', '/path/to/out.zip');
   *
   * @param string $sourcePath Path of directory to be zip.
   * @param string $outZipPath Path of output zip file.
   */
  public static function zipDir($sourcePath, $outZipPath, $del = false, $delDir = false)
  {
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new \ZipArchive();
    $z->open($outZipPath, \ZipArchive::CREATE);
    $z->addEmptyDir($dirName);
    self::folderToZip($sourcePath, $z, strlen("$parentPath/"), $del);
    if(true===$delDir)rmdir($sourcePath);
    $z->close();
  }  
  
  
  
   /**
   * Add files and sub-directories in a folder to zip file.
   * @param string $folder
   * @param \ZipArchive $zipFile
   * @param int $exclusiveLength Number of text to be exclusived from the file path.
   */
  private static function folderToZip($folder, &$zipFile, $exclusiveLength, $del = false) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";
        // Remove prefix from file path before add to zip.
        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
          if(true===$del)unlink($filePath);
        } elseif (is_dir($filePath)) {
          // Add sub-directory.
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength, $del);
        }
      }
    }
    closedir($handle);
  }

  
  public static function ZipStatusString( $status )
    {
       switch( (int) $status )
        {
        case \ZipArchive::ER_OK           : return 'N No error';
        case \ZipArchive::ER_MULTIDISK    : return 'N Multi-disk zip archives not supported';
        case \ZipArchive::ER_RENAME       : return 'S Renaming temporary file failed';
        case \ZipArchive::ER_CLOSE        : return 'S Closing zip archive failed';
        case \ZipArchive::ER_SEEK         : return 'S Seek error';
        case \ZipArchive::ER_READ         : return 'S Read error';
        case \ZipArchive::ER_WRITE        : return 'S Write error';
        case \ZipArchive::ER_CRC          : return 'N CRC error';
        case \ZipArchive::ER_ZIPCLOSED    : return 'N Containing zip archive was closed';
        case \ZipArchive::ER_NOENT        : return 'N No such file';
        case \ZipArchive::ER_EXISTS       : return 'N File already exists';
        case \ZipArchive::ER_OPEN         : return 'S Can\'t open file';
        case \ZipArchive::ER_TMPOPEN      : return 'S Failure to create temporary file';
        case \ZipArchive::ER_ZLIB         : return 'Z Zlib error';
        case \ZipArchive::ER_MEMORY       : return 'N Malloc failure';
        case \ZipArchive::ER_CHANGED      : return 'N Entry has been changed';
        case \ZipArchive::ER_COMPNOTSUPP  : return 'N Compression method not supported';
        case \ZipArchive::ER_EOF          : return 'N Premature EOF';
        case \ZipArchive::ER_INVAL        : return 'N Invalid argument';
        case \ZipArchive::ER_NOZIP        : return 'N Not a zip archive';
        case \ZipArchive::ER_INTERNAL     : return 'N Internal error';
        case \ZipArchive::ER_INCONS       : return 'N Zip archive inconsistent';
        case \ZipArchive::ER_REMOVE       : return 'S Can\'t remove file';
        case \ZipArchive::ER_DELETED      : return 'N Entry has been deleted';
       
        default: return sprintf('Unknown status %s', $status );
      }
    }

  

    
    /**
     * Split the zip archive.
     * @param string $i The zip archive.
     * @param integer $s The max size for the parts.
     * @return integer Return the number of parts created.
     */
    protected static function split($i, $s){
        $fs = filesize($i);
        //$bn = basename($i);
        //$dn = dirname($i).DIRECTORY_SEPARATOR;
        $p = 1;
        
        for($c = 0; $c < $fs; $c = $c + $s){
            $data = file_get_contents($i, FILE_BINARY, null, $c, $s);
            //$fn = "$dn$bn.$p";
            $fn = "$i.$p";
            file_put_contents($fn, $data);
            $p++;
            unset($data);
        }
        unlink($i);
        return $p - 1;
    }
    
	
	
	
    /**
     * Decompact the zip archive.
     * @param string $i The zip archive (*.zip).
     * @param string $o The directory name for extract.
     * @param integer $p Number of parts of the zip archive.
     * @return boolean Return TRUE for success or FALSE for fail.
     */
    public static function unzip($i, $o, $p = 0){
        $success = true;
        if($p > 0){
            $success = self::merge($i, $p);
        }
        if($success == false){
            return false;
        }
        
        $zp = new \ZipArchive();
        $zp->open($i);
        if($zp->extractTo($o)){
            $zp->close();
            unset($zp);
           // unlink($i);
            return true;
        }else{
            return false;
        }
        
    }
    
    /**
     * Merge the parts of zip archive.
     * @param string $i The zip archive (*.zip).
     * @param integer $p Number of parts of the zip archive.
     * @return boolean Return TRUE for success or FALSE for fail.
     */
    protected static function merge($i, $p){
        for($c = 1; $c <= $p; $c++){
            $data = file_get_contents("$i.$c");
            file_put_contents($i, $data, FILE_APPEND);
            unset($data);
        }
        return true;
    }
	
	
	
	
	

  
  
  
  
 /** 
     The following code can be used to get a list of all the file names in a zip file.
 */
   public static function getArchiveFilenames($filename){
   	    return self::getArchiveFilestats($filename, 'name');
    }
   
   public static function getArchiveFilestats($filename, $field = null){
   	     if(!file_exists($filename))return false;
		 $names = array();
         $za = new \ZipArchive();
         $za->open($filename);

         for( $i = 0; $i < $za->numFiles; $i++ ){
              $stat = $za->statIndex( $i );
			 // $e = explode('/',$stat['name']);
			 // $k = (count($e) < 2) ? 0 : 1;
			 // $k = '_'.str_replace('/','_',$stat['name']);
              $names[$stat['name']] = ($field !== null && isset($stat[$field])) 
                           ? $stat[$field] 
						   : $stat;
         } 
	   ksort($names);	
	   $za->close(); 
       return $names;
    } 
 
 /**
* Check if the file is encrypted
*
* Notice: if file doesn't exists or cannot be opened, function
* also return false.
*
* @param string $pathToArchive
* @return boolean return true if file is encrypted
*/
   public static function isEncryptedZip( $pathToArchive ) {
    $fp = @fopen( $pathToArchive, 'r' );
    $encrypted = false;
    if ( $fp && fseek( $fp, 6 ) == 0 ) {
        $string = fread( $fp, 2 );
        if ( false !== $string ) {
            $data = unpack("vgeneral", $string);
            $encrypted = $data[ 'general' ] & 0x01 ? true : false;
        }
        fclose( $fp );
    }
    return $encrypted;
   }  
   
   
   
}