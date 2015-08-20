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
 
final class DBSchema 
{
   const VERSION = '0.0.16';	

   protected $version;	
   protected $db;	
   protected $tables;
   protected $_tables;   
   protected $settings = array();
		
	function __construct($settings = null, \frdl\DB &$db = null){
        $this->settings($settings);
        $this->db = (null === $db) ?   \frdl\DB::_($this->settings, true) : $db;
	}
	
    public function save_schema($l, $linemax = 128){
       $bs = new \frdl\bs;	
 	  return $bs->serialize($l);
    } 

    public function load_schema($l){
       $bs = new \frdl\bs;	
 	   return $bs->unserialize($l);
    } 
 	
	public function settings($settings = null){
		 $this->settings = (is_array($settings)) ? $settings : $this->settings;
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
			return new $schema->tables[$alias]['ORM_CLASS'](array(),$this->settings);
		}else{
			return null;
		}
	}		
	
   public function schema($settings = null){
   	 $this->settings($settings);
   	 
   	    $schema = new \frdl\o;
          $schema->version = $this->version;
          $schema->version_should = self::VERSION;
          $repos = 'repositories';
          $packages = 'packages';
          $versions = 'versions';
          $installations = 'installations';
         
         
   	      $schema->tables = array( 
           'Repositories' => array(
             'tablename' => $repos,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Repository',
             'exists' => false,
             'version' => Repository::VERSION,
             'version_should' => '0.0.3',
             'table' => null,
             'sql' => array(
                "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='Packagist',
                      `host`='packagist.org',
                      `homepage`='https://packagist.org/',
                      `description`='The main Composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\Packagist')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='Composer [Package]',
                      `host`='github.com',
                      `homepage`='https://github.com/composer/composer',
                      `description`='Composer source',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\Composer')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='PragmaMx [Package]',
                      `host`='www.pragmamx.org',
                      `homepage`='http://www.pragmamx.org',
                      `description`='Just another CMS',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\PragmaMx')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='PHPclasses.org',
                      `host`='phpclasses.org',
                      `homepage`='http://webfan.users.phpclasses.org',
                      `description`='The phpclasses composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\phpclasses')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='JSclasses.org',
                      `host`='jsclasses.org',
                      `homepage`='http://webfan.users.jsclasses.org',
                      `description`='The jsclasses composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\jsclasses')."'
                      
                      ",     
                                          
                   "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      `name`='Webfan',
                      `host`='api.webfan.de',
                      `homepage`='http://www.webfan.de',
                      `description`='Webfan Software Server',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\webfan')."'
                      
                      ",                     
             ),
          ),  	      
   	        	      
          'Installations' => array(
             'tablename' => $installations,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Installations',
             'exists' => false,
             'version' => Installations::VERSION,
             'version_should' => '0.0.1',
             'table' => null,
             'sql' => array(
                            
             ),
          ),     	    	      
   	      
          'Versions' => array(
             'tablename' => $versions,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Version',
             'exists' => false,
             'version' => Version::VERSION,
             'version_should' => '0.0.1',
             'table' => null,
             'sql' => array(
                            
             ),
          ),      	      
                      
         'Packages' => array(
             'tablename' => $packages,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Package',
             'exists' => false,
             'version' => Package::VERSION,
             'version_should' => '0.0.1',
             'table' => null,
             'sql' => array(
                "INSERT INTO " . $this->settings['pfx'] . $packages . " 
                     SET 
                      `vendor`='frdl',
                      `package`='webfan'
                      ",

      
                      
             ),
          ),         	     
   	      
   
                       
         'Edges' => array(
             'tablename' => 'edges',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Edge',
             'exists' => false,
             'version' => Edge::VERSION,
             'version_should' => '0.0.1',
             'table' => null,
             'sql' => array(
        
                      
             ),
          ),         	     
    
                       
         'Nodes' => array(
             'tablename' => 'nodes',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Node',
             'exists' => false,
             'version' => Node::VERSION,
             'version_should' => '0.0.1',
             'table' => null,
             'sql' => array(
        
                      
             ),
          ),         	                     
      );  
      
     foreach($schema->tables as $title => &$t){
	  	$t['table'] =  $this->settings['pfx'] . $t['tablename'];
	  }    
      
     return $schema; 	
   }	
	
	
   public function check(&$schema = null, &$tables = null, $version = null,
                           $checkTables = false, $createTables = false, $updateTables = false,
                            \frdl\DB &$db = null, $settings = null, $oldSchema = null){
     	$this->settings($settings);
	    $this->db = (null === $db) ?   \frdl\DB::_($this->settings, true) : $db;
		$this->version = (null === $version) ? $this->getVersion() : $version;	
     	  
   	   $schema = $this->schema();
      
      
      if(true === $checkTables || true === $createTables || true === $updateTables){
	  	  $this->tables($tables);
	  }
      
      if(true === $checkTables){
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
		   foreach($schema->tables as $alias => $t){
  	   	 if(true === $t['exists']  && isset($oldSchema->tables[$alias])
  	   	  && $this->isFresh($oldSchema->tables[$alias]['version'], $t['version_should']) )continue; 
  	   	  
  	   	  if(!$this->isFresh($oldSchema->version, '0.0.16') ){
		  	 $report.= 'DROP Table: '.$t['table'].' ';
		  	try{
					 $this->db -> query("DROP TABLE `".$t['table']."`");   
				}catch(\Exception $e){
					trigger_error($e->getMessage(), E_USER_WARNING);
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
   	  return $report;
   }
   
   
   public function check_tables(&$schema, $_tables, $oldSchema){
   	   foreach($schema->tables as $title => &$t){
	  	 $t['exists'] = (isset($_tables[$t['table']]) && $this->isFresh($oldSchema->tables[$title]['version'], $schema->tables[$title]['version']));
	  }
   }
   
	public function tables(&$_tables){
		$_tables = array();
		
		try{
	        foreach ( $this->db->query("SHOW TABLES") as $row) {
             $_tables[$row['Tables_in_'.$this->settings['dbname']]] = $row; 
           }	
		}catch(\Exception $e){
			trigger_error($e->getMessage(), E_USER_ERROR);
			die($e->getMessage());
		}
		
		$this->_tables = $_tables;
		return $this;
	}
		

	
	public function isFresh($is = null, $should = null){
		if(null===$is)$is = $this->version;
		if(null===$should)$should = self::VERSION;		
		return (version_compare($is, $should) >= 0);
	}
	
	public function getVersion(){
		return self::VERSION;
	}
			
}