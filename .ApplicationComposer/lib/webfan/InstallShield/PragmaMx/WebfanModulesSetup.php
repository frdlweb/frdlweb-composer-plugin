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
 *  @version 	2.0   
 */
namespace webfan\InstallShield\PragmaMx;
use frdl;


class WebfanModulesSetup implements \frdl\webfan\Config\Install\Installable
{

	
	protected $I;
	protected $lid = 63;
	
		
	function __construct(\webfan\Install &$I){
		 $this->I = $I;
		 $this->lid = 63;
	}
	
	
	
	protected function e($str){
		return '<span style="color:red;">'.$str.'</span>'."\n";
	}
	
		
	public function install($dir){
		$html = '';
		
		$pfx = 'INSTALLED.REDIRECT:';
		$len = strlen($pfx);
		if(substr($dir,0,$len)===$pfx){
		   $dir = substr($dir,$len,strlen($dir));
	       $url = 'http://'.$_SERVER['SERVER_NAME'].rtrim(str_replace($this->I->detect[\webfan\Install::GROUP_SYS]['SERVER_VARS']['DOCUMENT_ROOT'], '',$dir), \frdl\webfan\App::DS.' /\  ').'/modules.php?name=webfan&op=webfan&WEBFAN_SETUP=update&OID=1.3.6.1.4.1.37553.8.1.8.8.5.5.11&step=__reset';
	       $html.='<a name="webdof"></a>';
	       $html.='<iframe src="'.$url.'" style="border:none;width:100%;height:850px;">Ihr Browser kann keine iframes!</iframe>';	   	 
	       return $html;
		}
		
		@set_time_limit(ini_get('max_execution_time'));
		
        if(!is_numeric($this->lid)){
        	$html.= $this->e($this->I->lang('__VERSION_UNAVAILABLE__') );
			return $html;
        }   
				
		if(!isset($this->I->detect[\webfan\Install::GROUP_APPS][\webfan\Install::APP_PMX][$dir]) 
		|| !file_exists($dir.'mainfile.php')
		|| $dir !== $this->I->detect[\webfan\Install::GROUP_APPS][\webfan\Install::APP_PMX][$dir]['dir']){
			$html.= $this->e($this->I->lang('__NO_PRAGMAMX_FOUND_IND_DIR__'));
			return $html;
		}
		
	   $html.= 'Install or update the Webfan PragmaMx Modules Setup to '.$dir."\n";
        if(!is_writable($dir)){
           @chmod($dir,0755);
           if(!is_writable($dir)){
        	  $html.= $this->e($dir.' '.$this->I->lang('__IS_NOT_WRITABLE__') );
          }   	
        }   
		
				
	  $post = array();
	  $send_cookies = array();			 
      $html.= $this->I->webfan_downloads_download($this->lid, $r );
	  if(intval($r['status'])!==200){
	  	$html.= $this->e($this->lang('__DOWNLOAD_FAILED__').' 1#'.__LINE__);
		return $html;	
	  }			 
	  $zipcont = &$r['body'];		
      @set_time_limit(ini_get('max_execution_time'));
			 
			 
	  chmod($dir,0755);
	  $zipfile = $this->I->dir_install_temp.'webfan-pragmamx-modules-setup.zip';
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
	  	 
	 	//   $Z = new \frdl\webfan\Compress\zip\ZipFile();
	   \frdl\webfan\Compress\zip\ZipFile::removeFile('html/install.php',$zipfile);
	   $files = \frdl\webfan\Compress\zip\ZipFile::getArchiveFilenames($zipfile);
	   $pfx = 'html/';
	   $len = strlen($pfx);
	   $count = 0;
	   foreach($files as $file){
	   	   @set_time_limit(ini_get('max_execution_time'));
	   	   if(substr($file,0,$len) !== $pfx ){
	   	   	   if(substr($file,-1,1) !== '/')\frdl\webfan\Compress\zip\ZipFile::removeFile($file,$zipfile);
	   	   }else{
			
	   	   	      $new_file = substr($file,$len,strlen($file));
				  $new_file = $dir . $new_file;
				  if(substr($new_file,-1,1) === '/'){
				  	 $modus = 'dir';
				  }else{
				  	 $modus = 'file';
				  }
				  
				  if($modus === 'dir' ){
				  	 if(!is_dir($new_file))mkdir($new_file,0755,true);
					 continue;
				  }
				  
				  if($modus === 'file' && !is_dir(dirname($new_file))){
				  	 mkdir(dirname($new_file),0755,true);
				  }
				  
				  
				  				  
				  $check = \frdl\webfan\Compress\zip\ZipFile::getFileContents($zipfile, $file, $filecontents);
			      if($check['ok'] === true && $check['crc32'] === $check['expected'] &&  $check['sha1'] === sha1($filecontents)){
	                 $fp = fopen($new_file, 'wb+');
	                 if(!$fp)
                        {
	                       	$html.= $this->e($this->I->lang('__CANNOT_WRITE_FILE__').': '.$new_file);
                        }else{	  
	                       fwrite($fp,$filecontents);
	                       fclose($fp);
                            if(!file_exists($new_file)){
	                        	$html.= $this->e($this->I->lang('__CANNOT_WRITE_FILE__').' '.$new_file);
	        	            }else{
	        	            	$count++;
	        	            }	  						   
						}			      	
			      }else{
			      	$html.= $this->e($this->I->lang('__CHECKSUM_INVALID__').': '.$file);
			      }
			    \frdl\webfan\Compress\zip\ZipFile::removeFile($file,$zipfile);
	   	   }
	   }
	 
	   unlink($zipfile);
	   $html.= $count.' files copied.'."\n";
	   $html.= 'Start setup...'."\n";	
	   $url = 'http://'.$_SERVER['SERVER_NAME'].rtrim(str_replace($this->I->detect[\webfan\Install::GROUP_SYS]['SERVER_VARS']['DOCUMENT_ROOT'], '',$dir), \frdl\webfan\App::DS.' /\  ').'/modules.php?name=webfan&op=webfan&WEBFAN_SETUP=update&OID=1.3.6.1.4.1.37553.8.1.8.8.5.5.11&step=__reset';
	  
	  

	    $html.='<iframe src="'.$url.'" style="border:none;width:100%;height:850px;">Ihr Browser kann keine iframes!</iframe>';	   	 
	   return $html;
	}
		
	public function render_form_install(){
		 $html = '';
		 
	     $html.= '<form action="'.$_SERVER['REQUEST_URI'].'#webdof" method="post">';
		 $html.= '<table style="border:none;width:100%;">';
		 $html.='<tr>';
		 $html.='<td>';
		// $html.= 'PragmaMx Installation w&auml;hlen:<br />';
		 $html.='</td>';
		 $html.='<td>';
		 $html.= '<select name="dir_pmx_installation" style="width:400px;">';
		 $ID = 0;
		 foreach($this->I->detect[\webfan\Install::GROUP_APPS][\webfan\Install::APP_PMX] as $dir => $app_pmx){
		 	 $ID++;
			 $selected = ($dir === realpath(__DIR__).\webfan\App::DS) ? ' selected ' : '';
			 
			 if(isset($this->I->detect[\webfan\Install::GROUP_APPS][\webfan\Install::APP_WEBDOF][$dir.'modules'.\frdl\webfan\App::DS.'webfan'.\frdl\webfan\App::DS.'webdof'.\frdl\webfan\App::DS])){
                $html.= '<option value="INSTALLED.REDIRECT:'.$dir.'" '.$selected.'>+ Update Webfan Modules of PragmaMx #'.$ID.' (v.'.$app_pmx['version'].') at '.$dir.'</option>';
			 }else{
			 	$html.= '<option value="'.$dir.'" '.$selected.'>++ Upgrade PragmaMx Installation #'.$ID.' (v.'.$app_pmx['version'].') at '.$dir.'</option>';
			 }
			 
		 	 
		 } 
		 $html.='</select>';
		 
		  
	        $html.= '&nbsp;&nbsp;<input type="checkbox" name="accept_license" />';
		    $html.= ' <a href="http://domainundhomepagespeicher.webfan.de/software-center/api/license/1.3.6.1.4.1.37553.8.1.8.4.5/licensetext/" target="_blank">'.$this->I->lang('__ACCEPT_LICENSE__').' (webdof)</a>';


		 $html.= '&nbsp;&nbsp;<input type="checkbox" name="sure_backup" /> ';
		  $html.= $this->I->lang('__SURE_BACKUP__');
		  $html.='?';
		  
	      $html.= '&nbsp;&nbsp;<input type="submit" name="BTN_install" value="'.$this->I->lang('__INSTALL_NOW__').'..." style="font-weight:bold;" />';
		  			 
		 $html.='</td>';
		 
		 $html.'</tr>';
		 $html.='</table>';
		 
		  $html.= '<input type="hidden" name="OID" value="'.\webfan\Install::APP_WEBDOF.'" />';
	 $html.= '</from>';	 
		 return $html;
	}
	
}
