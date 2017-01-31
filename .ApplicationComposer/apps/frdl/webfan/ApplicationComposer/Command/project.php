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
 *  @role       example/test
 * 
 *  @cmd "frdl test -b -d"
 * 
 */
namespace frdl\ApplicationComposer\Command;

class project extends CMD
{

    //$this->Console->App->cmd() //webfan.fexe.php
    const SCHEME = 'wpjct://';
    const EXT_WPJCT = 'wpjct';
    protected $dir;
    
    protected $s;
    
    public function process()
    {
       $args = func_get_args();
       $this->result->out = '';
       $this->result->js = '';
       $this->result->args = $this->argtoks;   
 



         //   $this->db = \frdl\xGlobal\webfan::db();
  		if(true!== $this->loadConfigFromFile(true)){
                $this->result->out = 'set config ERROR: cannot readf config file';
        	 return;			
		}          
          
            
         if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'You are not logged in as Admin';
  	
	     	 return;
		  }     
		  
		       
           $this->dir = $this->data['config']['DIRS']['data.storage'].'.projects'.DIRECTORY_SEPARATOR;
           if(!is_dir($this->dir)){
		   	  mkdir($this->dir, 0744, true);
		   }
        
        
        if(!isset($this->aSess['project']))$this->aSess['project']=null;
       
        $this->__RAW__ = function($in){
			return $in;
		};
                
         $this->__COMP__ = function($in){
			return bzcompress($in,9);
		};     
      
        $this->__DECOMP__ = function($out){
			return bzdecompress($out);
		};
                
        $this->__ENC__ = function($in){
			$s = new \frdl\webfan\Serialize\Binary\bin();
			return bzcompress($s->serialize($in),9);
		};
         
        
        $this->__DEC__ = function($out){
			$s = new \frdl\webfan\Serialize\Binary\bin();
			return $s->unserialize(bzdecompress($out));
		};
               
      
         if(null !==  $this->getRequestOption('project') && '' !==  $this->getRequestOption('project')){
               $this->_sub_get($this->getRequestOption('project'));
		}     

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
			$this->_sub_post($_POST);
		}
 
        if($_SERVER['REQUEST_METHOD'] === 'DELETE'){
			$this->_sub_delete();
		}        

         if(isset($this->argtoks['arguments'][0]) && intval($this->argtoks['arguments'][0]['pos']) === 1){
         	$sub = $this->argtoks['arguments'][0]['cmd'];
         	$method = '_sub_'.$sub;
         	if(is_callable(array($this,$method))){
				call_user_func_array(array($this,$method), func_get_args());
			}
         }                
   
       

       
        $this->result->out = 'End of command';
        
        return $this->result;
    }
    
 
   protected function pdir($in, $out = false){
   	  if(true === $out && false!== strpos($in, self::SCHEME) ){
   	  	return str_replace(self::SCHEME, $this->dir, $in);
	  }else{  	
	  	 return str_replace($this->dir, self::SCHEME, $in);	
	  }
   }
 
 
  
  protected function _sub_post($p = null){
  	  if(null===$p)$p = $_POST;
  	  if(!isset($this->aSess['project']->file) && isset( $this->result->project->file)){
	  	$this->aSess['project']->file = $this->result->project->file;
	  }
  	  if(!isset($this->aSess['project']->file) || !file_exists($this->pdir($this->aSess['project']->file, true))){
	  	$this->result->out = 'No existing project file picked!';
	  	return;
	  }
	  
	  /**
	  * 
	  * todo schema validation
	  * 	       	  
	  * */
	  if(isset($p['project']) && false !== json_decode($p['project'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, '.project', json_decode($p['project']), $this->__ENC__);
	  	  $this->result->project = $p['project'];
	  }
	  
	  if(isset($p['PHP']) && false !== json_decode($p['PHP'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'php.required', json_decode($p['PHP']), $this->__ENC__);
	  	  $this->result->project->PHP = $p['PHP'];
	  }
	  
	  if(isset($p['widgets']) && false !== json_decode($p['widgets'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'wgt.required', json_decode($p['widgets']), $this->__ENC__);
	  	  $this->result->project->widgets = $p['widgets'];
	  }
	  
	  if(isset($p['javascript']) && false !== json_decode($p['javascript'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'js.required', json_decode($p['javascript']), $this->__ENC__);
	  	  $this->result->project->javascript = $p['javascript'];
	  }
	  
	  if(isset($p['css']) && false !== json_decode($p['css'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile,  'css.required', json_decode($p['css']), $this->__ENC__);
	  	  $this->result->project->css = $p['css'];
	  }
	  
	  if(isset($p['webapp']) && false !== json_decode($p['webapp'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'manifest.webapp.packed', json_decode($p['webapp']), $this->__ENC__);
	  	  $this->result->project->webapp = $p['webapp'];
	  }
	  
	 
	 


	
	  if(isset($p['todo']) && false !== json_decode($p['todo'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'todo', json_decode($p['todo']), $this->__ENC__);
	  	  $this->result->project->todo = $p['todo'];
	  }
	  	
	  if(isset($p['conflicts']) && false !== json_decode($p['conflicts'])){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'conflicts', json_decode($p['conflicts']), $this->__ENC__);
	  	  $this->result->project->conflicts = $p['conflicts'];
	  }
	  
	  if(isset($p['bat']) ){
	  	  \frdl\Compress\Zip\Helper::set($pfile, 'cmd.bat', $p['bat'], $this->__RAW__);
	  	  $this->result->project->bat = $p['bat'];
	  }
	  
	
	   

	   
	   
	   
	 
	 if($this->isArg('pick')){
	   	    $this->_sub_pick();
	} 
	  
  }
 
 
  protected function _sub_get($pfile = null){
        if(null===$pfile || !is_string($pfile)){
     		if(null !==  $this->getRequestOption('project') && '' !==  $this->getRequestOption('project')){
               $pfile=$this->getRequestOption('project');
			}
		}	
  		
  		if(null===$pfile || !is_string($pfile)){
			if(isset($this->result->project) && isset($this->result->project->file)
			  && file_exists($this->pdir($this->result->project->file, true))){
			  	$pfile=$this->pdir($this->result->project->file, true);
			  }
		}
  
      if(!file_exists($this->pdir($pfile, true)))return ;
	       try{
	       	    $this->result->project = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), '.project', $this->__DEC__);
	            $this->result->project->files = \frdl\Compress\Zip\Helper::getArchiveFilestats( $this->pdir($pfile, true), null);

	   if($this->isArg('php')){
	   	   $this->result->project->PHP = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'php.required', $this->__DEC__);
	   }


	   if($this->isArg('widgets')){
	   	   $this->result->project->widgets = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'wgt.required', $this->__DEC__);
	   }


	   if($this->isArg('javascript')){
	   	   $this->result->project->javascript = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'js.required', $this->__DEC__);
	   }
	   

	   if($this->isArg('css')){
	   	   $this->result->project->css = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'css.required', $this->__DEC__);
	   }
	   	   

	   if($this->isArg('webapp')){
	   	   $this->result->project->webapp = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'manifest.webapp.packed', $this->__DEC__);
	   }
	   	  
	   	  
	   	  

	
	
	   if($this->isArg('todo')){
	   	   $this->result->project->todo = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'todo', $this->__DEC__);
	   }
	   if($this->isArg('conflicts')){
	   	   $this->result->project->conflicts = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'conflicts', $this->__DEC__);
	   }
	   
	   if($this->isArg('bat')){
	   	   $this->result->project->bat = \frdl\Compress\Zip\Helper::get($this->pdir($pfile, true), 'cmd.bat', $this->__RAW__);
	   }
	   	 	   	  
	   	  
	   	  
	   	  
	   	  
	   	  
	   if($this->isArg('pick')){
	   	    $this->aSess['project']= $this->result->project;
	   }
	   	  	   	  
	   	  	   
	   	  	   
	   	  	   
		   }catch(\Exception $e){
	          	$this->result->code=409;
		    	$this->out = 'Error opening project '.$pfile;
		  	    return ;	   	  
		   }        
      return ;	
  } 
  
  
   protected function _sub_delete(){
  
   	
       try{
	   if(!isset($this->aSess['project']) || null ===$this->aSess['project']
	     || !isset($this->aSess['project']->file)
	   ){
	   	   $this->out = 'No project file picked, nothing to delete!';
		  return ;	   	
	   }
	   
	   $f = explode('#', $this->aSess['project']->file, 2);
	   $f = ltrim($f[1], '/ ');
	  
	   
	   if($this->isArg('fromdisk') && file_exists($this->pdir($f, true))){
	   	chmod($this->pdir($f, true), 0744);
	   	unlink($this->pdir($f, true));
	   	rmdir(dirname($this->pdir($f, true)));
	   }
	   $this->aSess['project']=null;
	   $this->result->project = $this->aSess['project'];
	   $this->result->out = 'Deleted';
	   if($this->isArg('fromdisk') && file_exists($this->pdir($f, true))) $this->out = 'Cannot delete project';
	 }catch(\Exception $e){
	          	$this->result->code=409;
		    	$this->out = $e->getMessage();
		  	    return ;	   	  
	 }     
  }
 
 
  protected function _sub_pick($pfile = null, $refresh = false){
  	  if(true ===$refresh)$this->aSess['project']=null;
  	
  		if(null===$pfile || !is_string($pfile)){
     		if(null !==  $this->getRequestOption('project') && '' !==  $this->getRequestOption('project')){
               $pfile=$this->getRequestOption('project');
			}
		}
		if(null===$pfile || !is_string($pfile)){
			if(isset($this->result->project) && isset($this->result->project->file)
			  && file_exists($this->pdir($this->result->project->file, true))){
			  	$pfile=$this->pdir($this->result->project->file, true);
			  }
		}
		

 				if(file_exists($this->pdir($pfile, true))){
					 $this->_sub_get($pfile);
				}  	
				
		if(isset($this->result->project))$this->aSess['project']= $this->result->project;
     	 
  }
 
 
 
    protected function _sub_create(){
    	
    	
      if(!isset($this->aSess['isAdmin']) || true !== $this->aSess['isAdmin'] ){
                $this->result->out = 'You are not logged in as Admin';
  	
	     	 return;
      }  
		  
		   	
    	$pName = function($n){
           	$n = str_replace(' ', '-', $n);
		    $n = preg_replace("/[^A-Za-z0-9_\-]/", "_", $n);
		    return $n;			
		};
    	
    	  if(null !==  $this->getRequestOption('root-project') && '' !==  $this->getRequestOption('root-project')){
          	$this->result->code=400;
		  	$this->result->out = '--root-project not fully supported yet!';
		  	return;		  	
		  }else  {
		  	$rootProject = '';
		  }
		  
		  
	      if(null === $this->getRequestOption('vendor') || '' ===  $pName($this->getRequestOption('vendor'))){
          	$this->result->code=400;
		  	$this->result->out = '--vendor parameter required';
		  	return;		  	
		  }
		  
		  
	      if(null ===  $this->getRequestOption('package-name') || '' === $pName($this->getRequestOption('package-name'))){
          	$this->result->code=400;
		  	$this->result->out = '--package-name parameter required';
		  	return;		  	
		  }
		  
		  
	      if(null ===  $this->getRequestOption('title') || '' ===  $this->getRequestOption('title')){
          	$this->result->code=400;
		  	$this->result->out = '--title parameter required';
		  	return;		  	
		  }
		  
		  	 			  	 		  	  
    	
    	  $id=file_get_contents('http://www.webfan.de/getid.php?pfx=wpjct-&suffix=-us'.$this->data['config']['UID']);
    	  if(false===$id){
		  	$id = uniqid('webfan-project-', false).mt_rand(1,9).'-us'.$this->data['config']['UID'];
		  }
    	   
    	
          $project = new \O;
          $project->type='project';
          $project->id=$id;        
		  $project->rootProject = $rootProject;
          $project->version = '0.0.1.0.0';
          $project->vendor = $pName($this->getRequestOption('vendor'));
          $project->packageName =$pName($this->getRequestOption('package-name'));
          
          $project->name = $project->vendor.'/'.$project->packageName;
          $project->generator = $this->data['config']['PACKAGE'].' '.$this->data['config']['VERSION']
                                .';'.$this->data['config']['URL']
                                .';'.$this->data['config']['URL_API_ORIGINAL']
                                ;                        
          $project->title=$this->getRequestOption('title');
          $dir = $this->dirname($project->title);
          
          
          if(!mkdir($dir, 0744, true)){
          	$this->result->code=409;
		  	$this->result->out = 'Cannot create directory '.$dir;
		  	return $this->result;
		  }
		  
		  $pfile = $dir.$project->id.'.'.self::EXT_WPJCT;
		  $project->file = $this->data['config']['URL'].'#'.$this->pdir($pfile, false);          
          
          
          $project->url=$this->getRequestOption('url');
          $project->dirs =  new \O;
          $project->dirs->www = $this->getRequestOption('www');
          $project->dirs->js = $project->dirs->www. DIRECTORY_SEPARATOR.'js' . DIRECTORY_SEPARATOR ;
          $project->dirs->css = $project->dirs->www. DIRECTORY_SEPARATOR.'css' . DIRECTORY_SEPARATOR ;
          $project->dirs->files = rtrim($this->getRequestOption('files'), '/ ') . DIRECTORY_SEPARATOR.$pName($project->title).DIRECTORY_SEPARATOR;
          $project->dirs->widgets = rtrim($project->dirs->www, '/ ') . DIRECTORY_SEPARATOR . rtrim($this->getRequestOption('widgets'), '/ ') . DIRECTORY_SEPARATOR;
          $project->widgetsUrl = $this->getRequestOption('widgets-url');
          $project->javascriptUrl =rtrim($project->url, '/ ').'/js/';
          $project->cssUrl =rtrim($project->url, '/ ').'/css/';
          $project->online = false;
          $project->prepared = false;
          $project->deployed = false;       
          $project->state = null;    
          $project->created=time();  
          $project->touched=time(); 
          
          

		  
		  $requirePHP =  new \O;
		  $requirePHP->projectID = $project->id;
		  $requirePHP->type = 'required.php';
		  $requirePHP->required = array(
		     'frdl/webfan' => array(
		        'vendor' => 'frdl',
		        'package' => 'webfan',
		        'extern' => false,
		        'version' => 'dev-master',
		        'dir' => null,
		        'installed' => false,
		        'dir' => null,
		        'resolved' => false,
		     ), 
		     'gettext/gettext' => array(
		        'vendor' => 'gettext',
		        'package' => 'gettext',
		        'extern' => false,
		        'version' => 'dev-master',
		        'dir' => null,
		        'installed' => false,
		        'dir' => null,
		        'resolved' => false,
		     ));
		     
	
		  $requireCSS =  new \O;	
		  $requireCSS->projectID = $project->id;
		  $requireCSS->type = 'required.css';
		  $requireCSS->required = array();  
	      
		  
		  
		  $requireJS =  new \O;
		  $requireJS->projectID = $project->id;
		  $requireJS->type = 'required.js';
		  $requireJS->required = array(
		     'µ.flow' => array(
		        'url' => null,
		        'source' => 'http://api.webfan.de/api-d/4/js-api/library.js',
		        'version' => null,
		        'dir' => null,
		        'installed' => false,
		        'cachemanifest' => false,
		     ));
		     
		    

		    
		  $requireWGT =  new \O;	
		  $requireWGT->projectID = $project->id;
		  $requirePHP->type = 'required.wgt';
		  $requireWGT->required = array();  
	       
	       $p = parse_url($project->url);
		  $manifestWebApp =  new \O; 
		  $manifestWebApp->id = strtolower('com.'.$project->vendor.'.'.$project->packageName);
		  $manifestWebApp->name=$project->title;
		  $manifestWebApp->launch_path=$project->url;
		  $manifestWebApp->external=true;
		  $manifestWebApp->permissions= new \O;
		  $manifestWebApp->permissions->systemXHR= new \O;
		  $manifestWebApp->permissions->systemXHR->description='Required to load remote content';
		  $manifestWebApp->origin='http://'.$p['host'];
		  
		  $todo = array();
		  $conflicts= array();
		  $bat = '';
		  
	       try{
	       	    \frdl\Compress\Zip\Helper::set($pfile, '.project', $project, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, '.project', 'Project');
	       	    
	          //  \frdl\Compress\Zip\Helper::add('.project', $pfile, bzcompress($this->s->serialize($project),9));    //bzdecompress 
		        //  \frdl\Compress\Zip\Helper::set($pfile, '.~dev~img~log~.txt', '>'.$_SERVER['REMOTE_ADDR'].'-'.time().':project create', $this->__RAW__); 
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'php.required', $requirePHP, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'php.required', 'PHP');
	       	    
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'todo', $todo, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'todo', 'ToDo List');
	       	    
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'conflicts', $conflicts, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'conflicts', 'Problems and Conflicts');
	       	    

	       	    \frdl\Compress\Zip\Helper::set($pfile, 'cmd.bat', $todo, $this->__RAW__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'cmd.bat', 'Batch file');
	       	    
	       	    	       	           	    		        
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'wgt.required', $requireWGT, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'wgt.required', 'Widgets');	
	       	    	        
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'js.required', $requireJS, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'js.required', 'Javascripts');		
	       	    		        
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'css.required', $requireCSS, $this->__ENC__);
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'css.required', 'Styles');		
	       	    			        
	       	    \frdl\Compress\Zip\Helper::set($pfile, 'manifest.webapp.packed', $manifestWebApp, $this->__ENC__);	
	       	    \frdl\Compress\Zip\Helper::commentFile($pfile, 'manifest.webapp.packed', 'WebApp Application Manifest');			             	 
		   }catch(\Exception $e){
	          	$this->result->code=409;
		    	$this->result->out = 'Error creating project '.$pfile;
		  	    return;	   	  
		   }   
	          

	      if(!file_exists($pfile)){
          	$this->result->code=409;
		  	$this->result->out = 'Cannot create file '.$pfile;
		   try{
	      	   rmdir($dir);
	      	}catch(\Exception $e){
 		     return;	
 		    }  
		  	return;		  	
		  }  
		  
	   chmod($pfile, 0744);
	   
	   $this->result->code=200;	  
	   $this->result->out = 'Project created';
	   $this->result->project = $project;
	  //  $this->result->zip = base64_encode(file_get_contents($pfile) );
	   
	   if($this->isArg('pick')){
	   	  $this->_sub_pick($pfile, true);
	   }
	   return;		    
	 
	}   
   
    protected function _sub_list(){
        if(isset($this->argtoks['arguments'][1]) && intval($this->argtoks['arguments'][1]['pos']) === 2
              && 'root' === $this->argtoks['arguments'][1]['cmd']
          ){
        	$this->listRoot( $this->dir );
            $this->result->code=200;	
        }
        
        
        
         
	} 
    
    
    protected function listRoot($dir = null){
    	try{
        if(null===$dir)$dir=$this->dir;
    	$folders = array();    	
    	$this->result->projects =  &$folders; 
    	$mkdir = function($d, &$folders){
			if(!isset($folders[$d])){
				$folders[$d]=array();
			}			
		};
    	
      	
        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($dir), 
                    \RecursiveIteratorIterator::SELF_FIRST) 
            as $entry) {
            	
            	
            	if ($entry->isDir()){
					// $mkdir($this->pdir($entry->getRealPath(), false), $folders);
				}elseif ($entry->isFile() && self::EXT_WPJCT === pathinfo($entry->getFilename(), PATHINFO_EXTENSION)) {
                   //   echo substr($file->getPathname(), 27) . ": " . $file->getSize() . " B; modified " . date("Y-m-d", $file->getMTime()) . "\n";
                     // $size += $file->getSize();
                    $k = $this->pdir(dirname($entry->getRealPath()), false); 
                    $k2 = $entry->getFilename();
                    $mkdir($k, $folders); 
                    $folders[$k][$k2] = new \stdclass;
                    $folders[$k][$k2]->file = $this->data['config']['URL'].'#'.$this->pdir($entry->getRealPath(), false);
                   
                 }	
      
         }		
         
       $this->result->projects =  $folders; 
       
           }catch(\Exception $e){
 		       $this->out = $e->getMesage(); 
 	        }        
	}
    
    protected function dirname($title){
    	$title = strtolower($title);
    	$title = str_replace(' ', '_', $title);
		$n = preg_replace("/[^a-z0-9_\-]/", "_", $title);
		$n = $this->dir.$n;
		$t=$n;
		$c=1;
		while(is_dir($t.DIRECTORY_SEPARATOR)){
			$c++;
			$t =$n.'_'.$c;
		}
		return $t.DIRECTORY_SEPARATOR;
	} 
    
    public function required()
    {
       $args = func_get_args();
    }
}