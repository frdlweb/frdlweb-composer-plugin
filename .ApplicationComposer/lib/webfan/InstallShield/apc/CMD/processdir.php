<?php
namespace webfan\InstallShield\apc\CMD;



class processdir extends \webfan\InstallShield\apc\Command1
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
					
		
		
     //   $zipfile = $dir_package.$version.'.zip';
        $dir_destination = $dir_package.$version. DIRECTORY_SEPARATOR;	
	 
	 
	 
$dir = new \RecursiveDirectoryIterator($dir_destination);


$composerFiles = new \RecursiveCallbackFilterIterator($dir, function ($current, $key, $iterator) {
    // Allow recursion
    if ($iterator->hasChildren()) {
        return true;
    }

    if ($current->isFile() && 'composer.json' === $current->getFilename() ) {
        return true;
    }
    return false;
});
 
foreach (new \RecursiveIteratorIterator($composerFiles) as $file) {
  
    	array_push($this->_result, 'composerfile '.$file->getPathname().' -i --vendor="'.$vendor.'" --projectID="'.$projectID.'" --packagename="'.$packagename.'" '
	              .' --version="'.$version.'" '
	           .PHP_EOL); 
}
			
			
 }	
  
  
}
