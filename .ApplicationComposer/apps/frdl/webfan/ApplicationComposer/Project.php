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
 
class Project  extends \frdl\Crud {
		
		   const VERSION = '0.0.2';
		   const ALIAS = 'Projects';
		
			# Your Table name 
			protected $table = 'projects';
			
			# Primary Key of the Table
			protected $pk	 = 'node';
			

	public function create() { 
		   if(!isset($this->node_parent))$this->node_parent = 0;
		   if(!isset($this->node_root))$this->node_root = 0; 
	      $N = new Node();
	      $N->id_parent = $this->node_parent;
	      $N->id_root = $this->node_root;
	      $N->table_alias = self::ALIAS;
	      $N->create();
	      $this->variables[$this->pk] = $N->db()->lastInsertId();
		return parent::create();
	}


	  
				      	
	
			public function shema(){
				return array(
				  'version' => self::VERSION,
				  'schema' => "(
                            `node`  BIGINT(255) NOT NULL,
                            `node_parent`  BIGINT(255) NOT NULL DEFAULT '0',
				            `node_root`  BIGINT(255) NOT NULL DEFAULT '0',
				            `public` tinyint(1) NOT NULL DEFAULT '1',
				            `title` VARCHAR(128) NOT NULL DEFAULT 'UNTITLED',
				            `dir` VARCHAR(1024) NOT NULL,
				            `description` VARCHAR(1024) NOT NULL,
				            
                        PRIMARY KEY (`node`),
                        UNIQUE KEY `dir` (`node_parent`, `dir`)
				     )ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ",
				);
			}
			

			
	        public function field($label = null){
				$l = array(
			
				
				);
				if(null === $label){
					return $l;
				}
				
				return (isset($l[$label])) ? $l[$label] : null;
			}
			
	
			
	}