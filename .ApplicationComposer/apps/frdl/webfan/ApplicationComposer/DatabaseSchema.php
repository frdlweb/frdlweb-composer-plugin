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
 
abstract class DatabaseSchema 
{
   const VERSION = null;	

   
   public static $s = null;
   
   protected $db = null;	
   protected $tables;
   protected $_tables = array();   
   protected $settings = array();
   
   protected $schema_tables = null;
		
	function __construct($settings = null, \frdl\DB &$db = null){
		if(null === self::$s)self::$s = &$this;
        $this->connect($settings, $db);
	}
	
	/*  abstract public static function _($settings = null, \frdl\DB &$db = null);  */
	abstract public function save_schema($l, $linemax = 128);
	abstract public function load_schema($l);
	abstract public function schema($settings = null, $rootfile = null);
	

     
 	
	public function settings($settings = null){
		 $this->settings = (is_array($settings)) ? array_merge($this->settings, $settings) : $this->settings;
		 return  $this->settings;
	}
	
	public function t($alias, $settings = null){
	  $schema = $this->schema($settings);
		if(isset($schema->tables[$alias])){
			return $schema->tables[$alias]['table'];
		}else{
			return null;
		}
	}
	
	public function c($alias, $settings = null){
	  $schema = $this->schema($settings);
		if(isset($schema->tables[$alias])){
			return $schema->tables[$alias]['ORM_CLASS'];
		}else{
			return null;
		}
	}	
	
	public function i($alias, $settings = null){
	  $schema = $this->schema($settings);
		if(isset($schema->tables[$alias])){
			return new $schema->tables[$alias]['ORM_CLASS']();
		}else{
			return null;
		}
	}		
	
  	
   public function check(&$schema = null, &$tables = null, $version = null,
                           $checkTables = false, $createTables = false, $updateTables = false,
                            \frdl\DB &$db = null, $settings = null, $oldSchema = null){
        // $this->connect($settings, $db);
		$this->version = (null === $version) ? $this->getVersion() : $version;	
     	  
   	   $schema = $this->schema($settings);
      
      
      if(true === $checkTables || true === $createTables || true === $updateTables){
	  	  $this->tables($tables, false);
	  	$this->check_tables($schema, $tables, $oldSchema);
	  }
      
	  
	  $report = '';
	  

	  if(true === $createTables || true === $updateTables ){
	    $report .= $this->create_tables($schema, $oldSchema); 	
	  }

	  
	  
	  return $report;
   }	
   
   
   public function create_tables($schema, $oldSchema = null){
   	 $report = '';
   	 
   	 $this->db ->begin();
   	 
		   foreach($schema->tables as $alias => $t){
		   	/*
	                 $orm_class = $schema->tables[TableAlias::ALIAS]['ORM_CLASS'];
					 $Alias = new $orm_class(array(),  \frdl\xGlobal\webfan::db()->settings(),  $this->db);
					 if(true !== $Alias->find($alias)){
						 $Alias->table_alias = $alias;
					     $Alias->version = $t['version'];
					     $Alias->table = $t['table'];
				         $Alias->comment = $schema->tables[$alias]['ORM_CLASS'].' maps to '.$alias;		
				         $Alias->create();		 	
					 }		   	
		   	*/
  	   	 if(true === $t['exists']  && isset($oldSchema->tables[$alias])
  	   	  && $this->isFresh($oldSchema->tables[$alias]['version'], $schema->tables[$alias]['version']) )continue; 
  	   	  
  	   	  /**
			   * 
			   *  @todo   update table : updatestack.process
			   * 
			   */
  	   	  if(!$this->isFresh($oldSchema->version, '0.0.41') && !$this->isFresh($oldSchema->tables[$alias]['version'], $schema->tables[$alias]['version'])){
		  	 $report.= 'DROP Table: '.$t['table'].' ';
		  	try{
					 $this->db -> query("DROP TABLE `".$t['table']."`");   
				}catch(\Exception $e){
					trigger_error($e->getMessage(), E_USER_ERROR);
					$report.= $e->getMessage();
				}
		  }
		  
			 $report.= 'Create Table: '.$t['table'].' ';
			 $c = $this->i($alias); 
			 $c->install();
	

			 
			 foreach($t['sql'] as $num => $q){
			 	try{
					 $this->db -> query($q);
                   }catch(\Exception $e){
					trigger_error($e->getMessage(), E_USER_WARNING);
					$report.= $e->getMessage();
				}
			 	 
			 }
	   }	  
	   
	   $this->db ->commit();
	   
   	  return $report;
   }
   
   
   
   
   
   
   final public function check_tables(&$schema, $_tables, $oldSchema){
   	   foreach($schema->tables as $title => &$t){
	  	 $t['exists'] = (isset($_tables[$t['table']]) && $this->isFresh($oldSchema->tables[$title]['version'], $schema->tables[$title]['version']));
	  }
   }
   
	final public function tables(&$_tables, $getFields = false){
				
		try{
	        foreach ( $this->db->query("SHOW TABLES") as $row) {
	         $tablename = $row['Tables_in_'.$this->settings['dbname']];	
	        
	         if(true === $getFields){
			 	$this->_tables[$tablename] = $row; 
			 	$this->table_fields($row, $tablename, $this->_tables);
			 }	
             $this->_tables[$tablename] = $row; 
           }	
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_ERROR);
			 
		}
		
		$_tables = $this->_tables;
		return $this;
	}
		



   final public function get_schema_tables($reload = false){
   	  if(true!== $reload && is_array($this->schema_tables) )return $this->schema_tables;
   	    $this->schema_tables = array();
   	    $this->schema_tables[$this->settings['dbname']] = array();
        $this->tables($this->schema_tables[$this->settings['dbname']], true);
         
		return $this->schema_tables;  	  
   }



   final public function table_fields(&$fields = null, $tablename = null, $tables = null) {
    	  $this->connect();
    	  
	    if(null === $tables){
	    	if(!is_arary($this->_tables)){
	    		$this->tables($tables, false);
	    	}else{
				$tables = $this->_tables ;
			}
	    	
	    }
	    
	    if(!isset($tables[$tablename]))return null;
	    
	    $fields = array();
	    $q=	"DESCRIBE `".$tablename."`";
	    
	    foreach($this->db->query($q) as $row){
			 $fields[$row['Field']] = $row;
		}
		
	 return $this;	
   }
	
	
	
	
	
	final public function isFresh($is = null, $should = null){
		if(null===$is)$is = $this->version;
		if(null===$should)$should = self::VERSION;		
		return (version_compare($is, $should) >= 0);
	}
	
	
	
	final public function getVersion(){
		return self::VERSION;
	}
	
	
		
	final public function connect($settings = null, \frdl\DB &$db = null){
	    $this->settings($settings);
        $this->db = (null === $db) ?  \frdl\xGlobal\webfan::db() : $db;	
	}
	
				
}