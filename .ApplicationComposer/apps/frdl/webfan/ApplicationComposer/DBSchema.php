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
 
final class DBSchema extends DatabaseSchema
{
   const VERSION = '0.0.35';	


   protected $db = null;	
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
 	

	
   public function schema($settings = null, $rootfile = null){
   	 $this->settings($settings);
     $this->db = (null === $this->db) ?   \frdl\DB::_($this->settings, true) : $this->db;
   	  
   	    $rootfile = (!is_string($rootfile)) ? 'composer.json' : $rootfile;
  	 
   	 
   	 
   	      $schema = new \frdl\o;
          $schema->version = self::VERSION;

          $repos = 'repositories';
          $packages = 'packages';
          $versions = 'versions';
          $installations = 'installations';
          $oids = 'oids';
          $nodes = 'nodes';
   	      $schema->tables = array( 
   	      
           /*
           * These MUST be the first table definitions !
           */                                 
         TableAlias::ALIAS => array(
             'tablename' => 'table_alias',
             'ORM_CLASS' => '\frdl\ApplicationComposer\TableAlias',
             'exists' => false,
             'version' => TableAlias::VERSION,
            
             'table' => null,
             'sql' => array(
        
                      
             ),
          ),      	  	      
   	      
          'Labels' => array(
             'tablename' => 'labels',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Label',
             'exists' => false,
             'version' => Label::VERSION,
            
             'table' => null,
             'sql' => array(
        
                      
             ),
          ),     
          
          
          
            'Nodes' => array(
             'tablename' => $nodes,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Node',
             'exists' => false,
             'version' => Node::VERSION,
          
             'table' => null,
             'sql' => array(
             
                "INSERT INTO " . $this->settings['pfx'] . $nodes . " 
                     SET 
                     id=1,
                       id_parent=0,
                      `id_root`=0,
                      `table_alias`='".Project::ALIAS."',
                      `file`='".(file_exists($rootfile) ? $rootfile : '')."'
                      
                      ",        
                      
             ),
          ),        
          
         'Edges' => array(
             'tablename' => 'edges',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Edge',
             'exists' => false,
             'version' => Edge::VERSION,
            
             'table' => null,
             'sql' => array(
        
                      
             ),
          ),          
          
          Project::ALIAS => array(
             'tablename' => 'projects',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Project',
             'exists' => false,
             'version' => Project::VERSION,
          
             'table' => null,
             'sql' => array(
                     "INSERT INTO " . $this->settings['pfx'] . 'projects' . " 
                     SET 
                        `node`=1,
                            `node_parent`=0,
				            `node_root`=0,
				            `public`=1,
				            `title` ='Root Project',
				            `description`='Root Project'
                      
                      ",        
                      

             ),
          ),      
          
             
          
          OID::ALIAS => array(
             'tablename' => $oids,
             'ORM_CLASS' => '\frdl\ApplicationComposer\OID',
             'exists' => false,
             'version' => OID::VERSION,
          
             'table' => null,
             'sql' => array(
                   "INSERT INTO " . $this->settings['pfx'] . $oids . " 
                     SET 
                      `weid`='',
                      `oid`='1.3.6.1.4.1.37553',
                      `oid_ra`='1.3.6.1.4.1'
                      ",     
                
                   "INSERT INTO " . $this->settings['pfx'] . $oids . " 
                     SET 
                      `weid`='weid:1-8-8-5-1T-2-9',
                      `oid`='1.3.6.1.4.1.37553.8.1.8.8.5.65.2',
                      `oid_ra`='1.3.6.1.4.1.37553'
                      ",        
                
                   "INSERT INTO " . $this->settings['pfx'] . $oids . " 
                     SET 
                      `weid`='weid:1-8-8-5-1T-59',
                      `oid`='1.3.6.1.4.1.37553.8.1.8.8.5.65',
                      `oid_ra`='1.3.6.1.4.1.37553'
                      ",     
                
                   "INSERT INTO " . $this->settings['pfx'] . $oids . " 
                     SET 
                      `weid`='weid:1-8-3-D-1-6-3',
                      `oid`='1.3.6.1.4.1.37553.8.1.8.3.13.1.6',
                      `oid_ra`='1.3.6.1.4.1.37553'
                      ",         
                
                   "INSERT INTO " . $this->settings['pfx'] . $oids . " 
                     SET 
                      `weid`='weid:1-8-3-D-1-1-4,
                      `oid`='1.3.6.1.4.1.37553.8.1.8.3.13.1.1',
                      `oid_ra`='1.3.6.1.4.1.37553'
                      ",     
                                      
             ),
          ),     
          
              
          /*
          * end of first ones
          */
            	      
           'Repositories' => array(
             'tablename' => $repos,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Repository',
             'exists' => false,
             'version' => Repository::VERSION,
          
             'table' => null,
             'sql' => array(
                "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
                      `name`='Packagist',
                      `host`='packagist.org',
                      `homepage`='https://packagist.org/',
                      `description`='The main Composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\Packagist')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
                      `name`='Composer [Package]',
                      `host`='github.com',
                      `homepage`='https://github.com/composer/composer',
                      `description`='Composer source',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\Composer')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
                      `name`='PragmaMx [Package]',
                      `host`='www.pragmamx.org',
                      `homepage`='http://www.pragmamx.org',
                      `description`='Just another CMS',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\PragmaMx')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
                      `name`='PHPclasses.org',
                      `host`='phpclasses.org',
                      `homepage`='http://webfan.users.phpclasses.org',
                      `description`='The phpclasses composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\phpclasses')."'
                      
                      ",

                 "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
                      `name`='JSclasses.org',
                      `host`='jsclasses.org',
                      `homepage`='http://webfan.users.jsclasses.org',
                      `description`='The jsclasses composer repository',
                      `fetcher_class`='".urlencode('\frdl\ApplicationComposer\Repos\jsclasses')."'
                      
                      ",     
                                          
                   "INSERT INTO " . $this->settings['pfx'] . $repos . " 
                     SET 
                      _use=1,
                      def=1,
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
             
             'table' => null,
             'sql' => array(
                            
             ),
          ),     	    	      
   	      
          Version::ALIAS => array(
             'tablename' => $versions,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Version',
             'exists' => false,
             'version' => Version::VERSION,
             
             'table' => null,
             'sql' => array(
                            
             ),
          ),      	      
    	      
          Feature::ALIAS => array(
             'tablename' => 'features',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Feature',
             'exists' => false,
             'version' => Feature::VERSION,
            
             'table' => null,
             'sql' => array(
                            
             ),
          ),      	      
      	      
          User::ALIAS => array(
             'tablename' => 'users',
             'ORM_CLASS' => '\frdl\ApplicationComposer\User',
             'exists' => false,
             'version' => User::VERSION,
            
             'table' => null,
             'sql' => array(
                  "INSERT INTO " . $this->settings['pfx'] . 'users' . " 
                     SET 
                      `active`='1',
                      `blocked`='0',
                      `wuid`='2',
                      `time`='".time()."',
                      `username`='webfan',
                      `email`='".$_SERVER['SERVER_NAME'].".supportaccount.software.frdl.de'
                      ",                          
             ),
          ),       
          
           SInterface::ALIAS => array(
             'tablename' => 's_interfaces',
             'ORM_CLASS' => '\frdl\ApplicationComposer\SInterface',
             'exists' => false,
             'version' => SInterface::VERSION,
       
             'table' => null,
             'sql' => array(
                            
             ),
          ),       
                                                       
         'Packages' => array(
             'tablename' => $packages,
             'ORM_CLASS' => '\frdl\ApplicationComposer\Package',
             'exists' => false,
             'version' => Package::VERSION,
         
             'table' => null,
             'sql' => array(
                "INSERT INTO " . $this->settings['pfx'] . $packages . " 
                     SET 
                      `vendor`='frdl',
                      `package`='webfan'
                      ",
                "INSERT INTO " . $this->settings['pfx'] . $packages . " 
                     SET 
                      `vendor`='frdl',
                      `package`='package-fetcher'
                      ",

      
                      
             ),
          ),         	     
   	      
   
                       
    	     
    
            Download::ALIAS => array(
             'tablename' => 'downloads',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Download',
             'exists' => false,
             'version' => Download::VERSION,
            
             'table' => null,
             'sql' => array(
        
                      
             ),
          ), 
                                       

                                
         Batch::ALIAS => array(
             'tablename' => 'batches',
             'ORM_CLASS' => '\frdl\ApplicationComposer\Batch',
             'exists' => false,
             'version' => Batch::VERSION,
            
             'table' => null,
             'sql' => array(
        
                      
             ),
          ), 
                                               
         ClosureTreeHelper::ALIAS => array(
             'tablename' => 'ct_tree',
             'ORM_CLASS' => '\frdl\ApplicationComposer\ClosureTreeHelper',
             'exists' => false,
             'version' => ClosureTreeHelper::VERSION,
         
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
                            \frdl\DB &$db = null, $settings = null, $oldSchema = null, $rootfile = null){
     	$this->settings($settings);
	    $this->db = (null === $db) ?   \frdl\DB::_($this->settings, true) : $db;
		$this->version = (null === $version) ? $this->getVersion() : $version;	
     	  
   	   $schema = $this->schema($settings, $rootfile);
      
      
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
  	   	  && $this->isFresh($oldSchema->tables[$alias]['version'], $schema->tables[$alias]['version']) )continue; 
  	   	  
  	   	  /**
			   * 
			   *  @todo   update tabel : copy data to temp.table, drop table, create table
			   * 
			   */
  	   	  if(!$this->isFresh($oldSchema->version, '0.0.35') ){
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
					 $Alias = new $schema->tables['TableAlias']['ORM_CLASS'];
					 $Alias->find($alias);
					 if(!$Alias->find($alias)){
						 $Alias->table_alias = $alias;
					     $Alias->table = $t['table'];
				         $Alias->comment = $schema->tables[$alias]['ORM_CLASS'];		
				         $Alias->create();		 	
					 }

				}catch(\Exception $e){
					trigger_error($e->getMessage(), E_USER_WARNING);
					$report.= $e->getMessage();
				}
			 	 
			 }
	   }	  
   	  return $report;
   }
   
 

			
}