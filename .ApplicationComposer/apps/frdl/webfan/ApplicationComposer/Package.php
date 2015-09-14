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
 
class Package extends \frdl\Crud {
		
		   const VERSION = '0.0.4';
		
			# Your Table name 
			protected $table = 'packages';
			
			# Primary Key of the Table
			protected $pk1	 = 'vendor';
			protected $pk2	 = 'package';
			

    public function __set($name,$value){			
		if(strtolower($name) === $this->pk1) {
			$this->variables[$this->pk1] = $value;
		}elseif(strtolower($name) === $this->pk2) {
			$this->variables[$this->pk2] = $value;
		}
		else {
			$this->variables[$name] = $value;
		}
	}	
	public function save($vendor = "vendor", $package = 'package') {
		$this->variables[$this->pk1] = (empty($this->variables[$this->pk1])) ? $vendor : $this->variables[$this->pk1];
		$this->variables[$this->pk2] = (empty($this->variables[$this->pk2])) ? $package : $this->variables[$this->pk2];
		$fieldsvals = '';
		$columns = array_keys($this->variables);
		foreach($columns as $column)
		{
			if($column !== $this->pk1 && $column !== $this->pk2)
			  $fieldsvals .= $column . " = :". $column . ",";
		}
		$fieldsvals = substr_replace($fieldsvals , '', -1);
		if(count($columns) > 1 ) {
			$sql = "UPDATE " . $this->table .  " SET " . $fieldsvals . " WHERE " . $this->pk1 . "= :" . $this->pk1." AND " . $this->pk2 . "= :" . $this->pk2 . " LIMIT 1";
			return $this->db->query($sql,$this->variables);
		}
	}
				
	public function delete($vendor = "vendor", $package = 'package') {
		$vendor = (empty($this->variables[$this->pk1])) ? $vendor : $this->variables[$this->pk1];
		$package = (empty($this->variables[$this->pk2])) ? $package : $this->variables[$this->pk2];
		if(!empty($vendor) && !empty($package)) {
			$sql = "DELETE FROM " . $this->table . " WHERE " . $this->pk1 . "= :" . $this->pk1. " AND " . $this->pk2 . "= :" . $this->pk2 . " LIMIT 1" ;
			return $this->db->query($sql,array($this->pk1=>$vendor, $this->pk2=>$package));
		}
	}
	public function find($vendor =null, $package =null) {
		$this->variables[$this->pk1] = (is_string($vendor)) ? $vendor : $this->variables[$this->pk1];
		$this->variables[$this->pk2] = (is_string($package)) ? $package : $this->variables[$this->pk2];

			$sql = "SELECT * FROM " . $this->table ." WHERE " . $this->pk1 . "= :" . $this->pk1 . " AND " . $this->pk2 . "= :" . $this->pk2 . " LIMIT 1";	
			$this->variables = $this->db->row($sql,array($this->pk1=>$vendor, $this->pk2=>$package));
		
	   return (false !== $this->variables) ? true : false;	
	}	
		
		
		   

		
			public function shema(\mixed $args = null){
				return array(
				  'version' => self::VERSION,
				  'schema' => "(
				          
				         
                          `vendor` varchar(128) NOT NULL,
                          `package` varchar(128) NOT NULL,
                          
                          `time_last_fetch_info` INT(11) NOT NULL DEFAULT '0',
                          `url` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                          `description` varchar(1024) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
                            
                           PRIMARY KEY ( `vendor`,`package`)
                         
				     )ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ",
				);
			}
			
			
	        public function field($label = null){
				$l = array(
				 'vendor' => 'Vendor',
				 'package' => 'Packagename',
				 'time_last_fetch_info' => 'Last time the package-info was updated in the Application Composer',
				);
				if(null === $label){
					return $l;
				}
				
				return (isset($l[$label])) ? $l[$label] : null;
			}
			

			
}