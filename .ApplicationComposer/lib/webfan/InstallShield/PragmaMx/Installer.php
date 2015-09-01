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
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
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
 *  @version 	2.2   
 */
namespace webfan\InstallShield\PragmaMx;
use frdl;

class Installer implements \frdl\webfan\Config\Install\Installable
{
	
	
	protected $I;
	protected $lid = null;
	
	
	function __construct(\webfan\Install &$I){
		 $this->I = $I;
		 $this->lid = null;
		 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.1.2')$this->lid = 66;
		 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.2.0')$this->lid = 83;		 
		 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.2.1')$this->lid = 84;		
		 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.2.2')$this->lid = 85;	
		 
		 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.2.4')$this->lid = 86;
	//	 if(isset($_REQUEST['version']) && $_REQUEST['version'] === '2.1.2 download from pragmamx.org')$this->lid = 0;	 		 
	}
	
	
	protected function e($str){
		return '<span style="color:red;">'.$str.'</span>'."\n";
	}
	
	public function install($dir){
		$html = '';
		
		
        if(!is_numeric($this->lid)){
        	$html.= $this->e($this->I->lang('__VERSION_UNAVAILABLE__') );
			return $html;
        }   		
		
		 if($this->lid >= 86 && version_compare(PHP_VERSION, '5.4', '>=') !== TRUE){
        	$html.= $this->e($this->I->lang('__REQUIRED_PHP_VERSION_FAILED_1__').'5.4' );
			return $html;		 	
		 }
		
		$dir = strip_tags($dir);
		$dir = rtrim($dir, \frdl\webfan\App::DS.' ');
		if(strlen($dir) > 1)$dir.= \frdl\webfan\App::DS;
		$html.= 'Install or update the PragmaMx CMS to '.$dir."\n";
		
		
        if(!is_writable($dir)){
           @chmod($dir,0755);
           if(!is_writable($dir)){
        	  $html.= $this->e($dir.' '.$this->I->lang('__IS_NOT_WRITABLE__') );
			  return $html;
          }   	
        }   		
		

			 

      $html.= $this->I->webfan_downloads_download($this->lid, $r );
	  if(intval($r['status'])!==200){
	  	$html.= $this->e($this->lang('__DOWNLOAD_FAILED__').' 1#'.__LINE__);
		return $html;	
	  }		
		$zipcont = $r['body'];

	 
	   		 			 
	  chmod($dir,0755);
	  $zipfile = $this->I->dir_install_temp.'PragmaMx.zip';
	  
	  $fp = fopen($zipfile, 'wb+');
	  if(!$fp)
             {
	          	$html.= $this->e($this->I->lang('__DOWNLOAD_FAILED__').' 3#'.__LINE__);
	        	return $html;
             }	  
	  fwrite($fp,$zipcont);
	  fclose($fp);

	   
      if(!file_exists($zipfile)){
	          	$html.= $this->e($this->I->lang('__CANNOT_WRITE_FILE__').' '.$zipfile);
	        	return $html;
      }	  

	
	 
	//  FEHLER:  $Z = new \frdl\webfan\Compress\zip\ZipFile();
	 \frdl\webfan\Compress\zip\ZipFile::removeFile('install.php',$zipfile);
     \frdl\webfan\Compress\zip\ZipFile::removeFile('INSTALL.php',$zipfile);
	  
	  	  
	  $html.= 'Unpack...'."\n";	
	 
	  $success =\frdl\webfan\Compress\zip\ZipFile::unzip($zipfile, $dir);
	  unlink($zipfile);
      if($success !== true){
	          	$html.= $this->e($this->I->lang('__CANNOT_UNZIP_FILE__').' '.$zipfile);
	        	return $html;
      }	  
	  
	
	  
	  $html.= 'Start setup...'."\n";	
	  $url = $_POST['SETUP_URL'].'setup/';
	  if(file_exists($dir.'setup'.\frdl\webfan\App::DS.'.htaccess')){
	  	unlink($dir.'setup'.\frdl\webfan\App::DS.'.htaccess');
	  }
	  
	  $html.='<a name="PMX"></a>';
	  $html.='<iframe src="'.$url.'" style="border:none;width:100%;height:850px;">Ihr Browser kann keine iframes!</iframe>';
	  		 
	  return $html;
	}
	
	
}
