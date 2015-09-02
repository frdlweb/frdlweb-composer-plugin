<?php
/**
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
 */
namespace frdl\ApplicationComposer;
 
class Version  extends \frdl\Crud {
		
		   const VERSION = '0.0.8';
		   const ALIAS = 'Versions';
		   
		   
			# Your Table name 
			protected $table = 'versions';
			
			# Primary Key of the Table
			protected $pk	 = 'id';
			

	
				
			public function shema(){
				return array(
				  'version' => self::VERSION,
				  'schema' => "(
				      `id` BIGINT(255) NOT NULL AUTO_INCREMENT,
				      `type` varchar(64) NOT NULL DEFAULT 'package',
				      `supported` TINYINT(1) NOT NULL DEFAULT '0',
				      `ad` TINYINT(1) NOT NULL DEFAULT '0',
				      `frdl` TINYINT(1) NOT NULL DEFAULT '0',
				      `webfan` TINYINT(1) NOT NULL DEFAULT '0',
				      `installable` TINYINT(1) NOT NULL DEFAULT '0',
				      `installed` TINYINT(1) NOT NULL DEFAULT '0',
				      `redistribute` TINYINT(1) NOT NULL DEFAULT '0',
				      `composer_json` TINYINT(1) NOT NULL DEFAULT '0',
				      `autoloading` TINYINT(1) NOT NULL DEFAULT '0',
				      `state` enum('test','alpha','beta','dev','stable','unknown','','pre-release') NOT NULL DEFAULT 'unknown',
				      `repository` varchar(32) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
				      `version` varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `versionNormalized`  varchar(64) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `vendor` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '' ,
				      `package` varchar(128) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `description` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `keywords` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `url` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `dir` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `file_autoload` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
				      `time` INT(11) NOT NULL DEFAULT '0',
				      `time_last_fetch_info` INT(11) NOT NULL DEFAULT '0',
				      `time_last_download_zip` INT(11) NOT NULL DEFAULT '0',
				      UNIQUE KEY `package_version` (`repository`,`version`,`vendor`,`package`),
				      PRIMARY KEY (`id`)
				     )ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ",
				);
			}
			

			
	        public function field($label = null){
				$l = array(
				 'id' => 'package #ID',
				 'type' => 'Packagetype',
				 'supported' => 'Is the package supported by frdl/Webfan?',
				 'installed' => 'Is the package installed?',
				 'ad' => 'Is the package special promoted?',
				 'composer_json' => 'Does this package provide a composer.json file?',
				 'repository' => 'Repository',
				 'version' => 'Version',
				 'versionNormalized' => 'Normalized Version Format',
				 'vendor' => 'Vendor', 
				 'package' => 'Packagename', 
				 'description' => 'Description', 
				 'dir' => 'Directory', 
				 'file_autoload' => 'Autoloader Include', 
				 'time' => 'Time package created or published', 
				 'time_last_fetch_info' => 'Last Time we fetched the version info', 
				 'time_last_download_zip' => 'Last Time we downloaded the zip Archive', 
				);
				if(null === $label){
					return $l;
				}
				
				return (isset($l[$label])) ? $l[$label] : null;
			}
			
	
			
	}