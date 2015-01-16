<?php
/**
 * 
 * Copyright  (c) 2015, Till Wehowski
 * All rights reserved.
 * 
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 * 1. Redistributions of source code must retain the above copyright
 *    notice, this list of conditions and the following disclaimer.
 * 2. Redistributions in binary form must reproduce the above copyright
 *    notice, this list of conditions and the following disclaimer in the
 *    documentation and/or other materials provided with the distribution.
 * 3. Neither the name of frdl/webfan nor the
 *    names of its contributors may be used to endorse or promote products
 *    derived from this software without specific prior written permission.
 * 
 * THIS SOFTWARE IS PROVIDED BY frdl/webfan ''AS IS'' AND ANY
 * EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
 * WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE
 * DISCLAIMED. IN NO EVENT SHALL frdl/webfan BE LIABLE FOR ANY
 * DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES;
 * LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND
 * ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS
 * SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 * 
 * 
 *  @author 	Till Wehowski <software@frdl.de>
 *  @copyright 	2014 Copyright (c) Till Wehowski
 *  @version 	1.2  
 */
namespace frdl\webfan\Compress\zip;
 
class ZipFile
{
	
   
   public static function removeFile($fileToRemove, $filename /* ZipFile */){
   	     if(!file_exists($filename))return false;
         $za = new \ZipArchive();
         $za->open($filename);
         for( $i = 0; $i < $za->numFiles; $i++ ){
              $stat = $za->statIndex( $i );
			  if($stat['name'] !== $fileToRemove)continue;
			   $za->deleteIndex($i);
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
    	
	
  public static function readfile($archivefilename, $filename){
   $zip = new \ZipArchive();
   if (!$zip->open($archivefilename)){
  	  return false;    	
   }
   
   $contents = $zip->getFromName($filename);
   $zip->close();
   return $contents; 	
  }		
		
  public static function getFileContents($archivefilename, $filename, &$buf = null){	
   $zip = new \ZipArchive();
   if (!$zip->open($archivefilename)){
  	  return array(
   	     'ok' => false,
         'error' => self::ZipStatusString(\ZipArchive::ER_OPEN),
    );    	
   }
   $fp = $zip->getStream($filename); 
   if(!$fp){
   	  return array(
   	     'ok' => false,
         'error' => self::ZipStatusString(\ZipArchive::ER_OPEN),
    ); 
   }
   $stat = $zip->statName($filename);

    $buf = "";
    ob_start(); 
    while (!feof($fp)) {
      $buf .= fread($fp, 2048); 
    }
    $s = ob_get_contents();
    ob_end_clean();
  
    if(stripos($s, "CRC error") != FALSE){
    
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
    
         return array(
   	     'ok' => true,
         'crc32' => sprintf("%08X", crc32($buf)),
         'expected' => sprintf("%08X", $stat['crc']),
         'byte' => strlen($buf),
         'sha1' => sha1($buf),
       ); 
     
 }
   
   
  
	

     public static function zip($i, $o, $s = 0, $afilename = null){
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
        
        if($success){
            if($s > 0){
                return self::split($o, $s);
            }
        }else{
            return false;
        }
        
       
    }
	
	
		

  public static function zipDir($sourcePath, $outZipPath)
  {
    $pathInfo = pathInfo($sourcePath);
    $parentPath = $pathInfo['dirname'];
    $dirName = $pathInfo['basename'];

    $z = new \ZipArchive();
    $z->open($outZipPath, \ZipArchive::CREATE);
    $z->addEmptyDir($dirName);
    self::folderToZip($sourcePath, $z, strlen("$parentPath/"));
    $z->close();
  }  
  
  
  

  private static function folderToZip($folder, &$zipFile, $exclusiveLength) {
    $handle = opendir($folder);
    while (false !== $f = readdir($handle)) {
      if ($f != '.' && $f != '..') {
        $filePath = "$folder/$f";

        $localPath = substr($filePath, $exclusiveLength);
        if (is_file($filePath)) {
          $zipFile->addFile($filePath, $localPath);
        } elseif (is_dir($filePath)) {
     
          $zipFile->addEmptyDir($localPath);
          self::folderToZip($filePath, $zipFile, $exclusiveLength);
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

  

    

    protected static function split($i, $s){
        $fs = filesize($i);

        $p = 1;
        
        for($c = 0; $c < $fs; $c = $c + $s){
            $data = file_get_contents($i, FILE_BINARY, null, $c, $s);
   
            $fn = "$i.$p";
            file_put_contents($fn, $data);
            $p++;
            unset($data);
        }
        unlink($i);
        return $p - 1;
    }
    
	
	

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
      
            return true;
        }else{
            return false;
        }
        
    }
    

    protected static function merge($i, $p){
        for($c = 1; $c <= $p; $c++){
            $data = file_get_contents("$i.$c");
            file_put_contents($i, $data, FILE_APPEND);
            unset($data);
        }
        return true;
    }
	
	
	
	
	


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
              $names[$stat['name']] = ($field !== null && isset($stat[$field])) 
                           ? $stat[$field] 
						   : $stat;
         } 
	   ksort($names);	
	   $za->close(); 
       return $names;
    } 
 

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
