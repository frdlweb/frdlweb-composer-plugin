<?php
namespace webfan\InstallShield\apc\CMD;



class mkp extends \webfan\InstallShield\apc\Command1
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
		$packagename =  $this->getRequestOption('packagename');

		$title =  $this->getRequestOption('title');
        $organisation =  $this->getRequestOption('org');
  
  
  
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
	
		
		$dir_project_bootstrap = $dir_project . 'bootstrap' . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_bootstrap)){
			mkdir($dir_project_bootstrap, 0755, true);
		}
		@chmod($dir_project_bootstrap, 0755);
	
						
        \frdl\webfan\App::God(false)->copyDir( $vm->context->apc->config->config_source['dir_apc_bootstrap'],$dir_project_bootstrap);
        
        
        $inProject = array(
          'id' => $projectID,
          'title' => $title,
          'vendor' => $vendor,
          'packagename' => $packagename,
          'organisation' => $organisation,
          'dir_project' => $dir_project,
          'dir_project_packages' => $dir_project_packages,
        );
		
		

		
		$file_make_ini = $dir_project. 'make.ini';
		$file_index_php = $dir_project. 'index.php';		

	//n!!!
		$dir_project_package_vendor = $dir_project_packages . $vendor . DIRECTORY_SEPARATOR;
		if(!is_dir($dir_project_package_vendor)){
			mkdir($dir_project_package_vendor, 0755, true);
		}
		@chmod($dir_project_package_vendor, 0755);		
			


$IPHP = "<?php".PHP_EOL."
/**".PHP_EOL."
* just include this file to INVOKE your project !".PHP_EOL."
*/".PHP_EOL."
 require '$dir_project_bootstrap' . 'bootstrap.php'; ".PHP_EOL."

";



    file_put_contents($file_index_php, $IPHP);
    
			
	$def = array(
	    'PROJECT' => $inProject
	);		
		
	$conf = new \frdl\common\INI();	
			
	if(file_exists($file_make_ini)){
		$conf->read($file_make_ini);
	}else{
		$conf->data(array());
	}
			
	$conf->data( array_merge_recursive($conf->data(), $def) );
	
	if(null===$conf->data(null, 'AUTOLOAD', 'BOOTSTRAP')){
		$conf->data($dir_project_bootstrap, 'AUTOLOAD',  'BOOTSTRAP');
	}
		
	if(null===$conf->data(null, 'AUTOLOAD',  'CLASSMAP')){
		$conf->data(array(),  'AUTOLOAD', 'CLASSMAP');
	}
	
	if(null===$conf->data(null,  'AUTOLOAD', 'PSR-4')){
		$conf->data(array(), 'AUTOLOAD',  'CLASSMAP');
	}
	
	if(null===$conf->data(null,  'AUTOLOAD', 'PSR-0')){
		$conf->data(array(), 'AUTOLOAD',  'CLASSMAP');
	}
	
				
	$conf->write($conf->data(), $file_make_ini);			

			array_push($this->_result, 'noop '.PHP_EOL); 	
 }	
  
  
}
