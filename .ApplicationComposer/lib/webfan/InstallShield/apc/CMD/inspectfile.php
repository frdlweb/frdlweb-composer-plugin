<?php
namespace webfan\InstallShield\apc\CMD;



class inspectfile extends \webfan\InstallShield\apc\Command1
{

 protected function _inspect($file){
    $fp = fopen($file, 'r');
    $class = $namespace = $buffer = '';
    $classes = array();
    $i = 0;
   //while (!$class) {
   //   if (feof($fp)) break;
  while (!feof($fp)) {
    $buffer .= fread($fp, 512);
    $tokens = token_get_all($buffer);

    if (strpos($buffer, '{') === false) continue;

    for (;$i<count($tokens);$i++) {
        if ($tokens[$i][0] === T_NAMESPACE) {
            for ($j=$i+1;$j<count($tokens); $j++) {
                if ($tokens[$j][0] === T_STRING) {
                     $namespace .= '\\'.$tokens[$j][1];
                } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                     break;
                }
            }
        }

        if ($tokens[$i][0] === T_CLASS) {
            for ($j=$i+1;$j<count($tokens);$j++) {
                if ($tokens[$j] === '{') {
                    $class = $tokens[$i+2][1];
                    array_push($classes, $class);
                }
            }
        }
    }
  }  	

    return array(
        'namespace' => $namespace,
        'classes' => $classes,
    );
 }
	
	
  public function run(){
    
		if(!file_exists($this->argtoks['arguments'][0]['cmd'])){
			\webdof\wResponse::status(409);	
			array_push($this->_result, 'The file '.$this->argtoks['arguments'][0]['cmd'].' does not exist');
			return;			
		}
			
		$info = @	$this->_inspect($this->argtoks['arguments'][0]['cmd']);
		
	//	$ns = array_shift($info);
		array_push($this->_result, $info['namespace']);
		foreach($info['classes'] as $i => $class){
			array_push($this->_result, $class);
		}
			
 }	
  
  
}
