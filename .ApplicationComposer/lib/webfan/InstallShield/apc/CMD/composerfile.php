<?php
namespace webfan\InstallShield\apc\CMD;



class composerfile extends \webfan\InstallShield\apc\Command1
{


	
	
  public function run(){
  	
  	$vm = &$this->cli->vm;
  	
		$projectID =  $this->getRequestOption('projectID');
		if(!$projectID){
			\webdof\wResponse::status(400);	
			array_push($this->_result, 'No Project (--projectID) specified!');
			return;
		}
		
		$vendor =  $this->getRequestOption('vendor');
		if(!$vendor){
			\webdof\wResponse::status(400);	
			array_push($this->_result, 'No vendor (--vendor) specified!');
			return;
		}	
		
		$packagename =  $this->getRequestOption('packagename');
		if(!$packagename){
			\webdof\wResponse::status(400);	
			array_push($this->_result, 'No packagename (--packagename) specified!');
			return;
		}	
		
		
		
		$version =  $this->getRequestOption('version');
		if(!$version){
			\webdof\wResponse::status(400);	
			array_push($this->_result, 'No version (--version) specified!');
			return;
		}	
		
		
		
		$dir_project = $vm->context->apc->config->config_source['dir_projects'] . urlencode($projectID) . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project)){
			mkdir($dir_project, 0755, true);
		}
		@chmod($dir_project, 0755);
		
		
		$dir_project_packages = $dir_project . 'project_packages' . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_packages)){
			mkdir($dir_project_packages, 0755, true);
		}
		@chmod($dir_project_packages, 0755);
		
		
		

		$dir_project_package_vendor = $dir_project_packages . $vendor . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_package_vendor)){
			mkdir($dir_project_package_vendor, 0755, true);
		}
		@chmod($dir_project_package_vendor, 0755);		
			
			
			
			
			

		
		$dir_project_package_packagename = $dir_project_package_vendor . $packagename . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_package_packagename)){
			mkdir($dir_project_package_packagename, 0755, true);
		}
		@chmod($dir_project_package_packagename, 0755);		
			
			
						
		$dir_packages = $vm->context->apc->config->config_source['dir_packages'];	
		$dir_package_vendor = $dir_packages . $vendor . DIRECTORY_SEPARATOR;	
		if(!is_dir($dir_package_vendor)){
			mkdir($dir_package_vendor, 0755, true);
		}
		@chmod($dir_package_vendor, 0755);		
					
	
		$dir_package = $dir_package_vendor . $packagename . DIRECTORY_SEPARATOR;	
		if(!is_dir($dir_package)){
			mkdir($dir_package, 0755, true);
		}
		@chmod($dir_package, 0755);		
					
		$dir_project_bootstrap = $dir_project . 'bootstrap' . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_bootstrap)){
			mkdir($dir_project_bootstrap, 0755, true);
		}
		@chmod($dir_project_bootstrap, 0755);
			
		
     //   $zipfile = $dir_package.$version.'.zip';
        $dir_destination = $dir_package.$version. DIRECTORY_SEPARATOR;	
        
        
	 	if(!file_exists($this->argtoks['arguments'][0]['cmd'])){
			\webdof\wResponse::status(409);	
			array_push($this->_result, 'The file '.$this->argtoks['arguments'][0]['cmd'].' does not exist');
			return;			
		}
		
		
		$composerFile = $this->argtoks['arguments'][0]['cmd'];
		
		$composer = json_decode(file_get_contents($composerFile));
		

		
			$file_make_ini = $dir_project. 'make.ini';
			
			$file_boot = $dir_project_bootstrap . 'bootstrap.php';
			
			$conf = new \frdl\common\INI();	
			$conf->read($file_make_ini);
			
			$fp = fopen($file_boot, 'a');	
            register_shutdown_function(function($fp){
            	fclose($fp);
            },$fp);
            
     if(isset($composer->autoload)){
	 	foreach($composer->autoload as $type => $loads){
	 	  foreach($loads as $namespace => $dir){

			$arr = $conf->data(null, 'AUTOLOAD', $type);
			if(null===$arr){
				$arr=array();
			}
						
			$dir2 = dirname($composerFile).DIRECTORY_SEPARATOR.$dir.DIRECTORY_SEPARATOR;
			
			$item = '\\'.$namespace.' '.$dir;
			
			
	     	array_push($arr, $item);
		  //	  $arr[$namespace] = $dir;
		
			$conf->data($arr, 'AUTOLOAD', $type);

              $php = '\frdl\webfan\Autoloading\SourceLoader::top()';

             if('psr-0'===$type){
			 	$php .= '->addPsr0(\''.str_replace('\\', '\\\\', $namespace).'\', \''.$dir2.'\', false)';
			 }elseif('psr-4'===$type){
			 	$php .= '->addPsr4(\''.str_replace('\\', '\\\\', $namespace).'\', \''.$dir2.'\', false)';
			 }elseif('classmap'===$type){
			 	
	$cdir = new \RecursiveDirectoryIterator(dirname($composerFile).DIRECTORY_SEPARATOR);
    $cFiles = new \RecursiveCallbackFilterIterator($cdir, function ($current, $key, $iterator)use($dir) {
    // Allow recursion
    if ($iterator->hasChildren()) {
        return true;
    }

    if ($current->isFile() && $dir === $current->getFilename() ) {
        return true;
    }
    return false;
} );
 
       foreach (new \RecursiveIteratorIterator($cFiles) as $cfile) {
                $Console = new \webfan\InstallShield\apc\cmd1($vm);	
                $Console->verbose = false;
                $Console->exe('inspectfile '.$cfile->getPathname());  
                $r = $Console->result[0];
                $r = preg_split("/\r?\n/", $r);

                $ns = array_shift($r);
                $_ = array();
                foreach($r as $class){
                	$classname = $ns.'\\'.$class;
					if(!isset($_[$classname])){
						$_[$classname]=true;
					 	$php .= PHP_EOL;
					 	$php .= '->class_mapping_add(\''.$classname.'\', \''.$cfile->getPathname().'\',  $success)';	
					}
				}
       }		
        	
			 }	else{
			 		$php .= '/* '.$type.'  '.$namespace.'  '.$dir.'  */';
			 }
			 
			 
			 		
			$php .= ';';
			$php .= PHP_EOL;
			fwrite($fp, $php);
		  }  			
		}  
	 }    	
       	
      
		$conf->write(null, $file_make_ini);		
	
	array_push($this->_result, 'noop '.PHP_EOL); 			
 }	
  
  
}
