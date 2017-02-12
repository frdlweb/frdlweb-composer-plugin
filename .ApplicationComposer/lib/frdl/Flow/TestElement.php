<?php 
namespace frdl\Flow;

log('Starting testCase: '.__FILE__);
function lnbr(){
	echo "\n";
}
function log($str){
  echo microtime().':'.$str;
  lnbr();
  ob_end_flush();	
} 
function highlight_num($file)
{
  $lines = implode(range(1, count(file($file))), '<br />');
  $content = highlight_file($file, true);

 
  $out = '
    <style type="text/css">
        .num {
        float: left;
        color: gray;
        font-size: 13px;   
        font-family: monospace;
        text-align: right;
        margin-right: 6pt;
        padding-right: 6pt;
        border-right: 1px solid gray;}

        body {margin: 0px; margin-left: 5px;}
        td {vertical-align: top;}
        code {white-space: nowrap;}
    </style>';
   
   
   
    $out.= "<table><tr><td class=\"num\">\n$lines\n</td><td>\n$content\n</td></tr></table>";
     
    return $out;
} 
 
log('Creating inherited Element class and Testclasses'.lnbr().'Bind Events on Testclasses and tes-listeners'.lnbr().'Bind a TestDebugger to the "test" Event and trigger it');
class TestElement extends Element{
  protected $reflection;
  	protected $initTime=null;
	public function __construct(){
	    parent::create( func_get_args());
		$this->refelection = ReflectionClass::export('Apple', true);
	}
	function __destuct(){
         register_shutdown_function(function ($className) {
		log('shutdown_function.invocation by destructor of '.$className);              
         }, get_class($this));		
	} 
   
   public function test($event, &$target, &$eventData){
   	  log('Triggering listener of "'.$event.'" Event in listener '.__METHOD__);
   	  log(
   	     '<pre>'
   	     .'Eventdata: '.lnbr()
   	     .print_r($eventData,true)
   	     .lnbr()
   	     .__CLASS__.':'
   	     .lnbr()
   	     .$this->refelection.lnbr()
   	     .'</pre>'.lnbr()
   	     .highlight_num(__FILE__).lnbr()
   	  );
   }
}
class MyElementSubClass extends Element{
	protected function __construct(){
		$args = func_get_args();
		parent::__construct($args);
		$this->name=$args[0];
		$this->data=$args[1];
		log('Creating Instance of '.__CLASS__.' inherrited from '.get_class(parent) );
	}
	public static function create($name, $data){
	   return parent::create($name, $data);
	}	
}

function myEventListenerGlobalFunction($event, &$target, &$data) {
	// return false;  // cancel/ stopPropagation 
  log("Hello from triggered function myEventListenerGlobalFunction() on the $event Event");
}

class Foo {
  public function hello($event, &$target, &$eventData) {
    log("Hello from triggered ".__CLASS__."($event, ".print_r($target,true).", ".print_r($eventData,true).")");
  }
}

class Bar {
   public static function listen($event, &$target, &$eventData) {
    log("Hello from Bar::hello() dispatch ".$event);
  }
}
 $foo = new Foo();
 $Context = new \stdclass;
 
 $myElement = MyElementSubClass::create($Context)
  // bind a global function to the 'test' event
  ->on("test", "myEventListenerGlobalFunction")   

  // bind an anonymous function
  ->on("test", function($event, &$target, &$eventData) { 
     log("Hello from anonymous function triggered by Event:".$event.' with Data:'.print_r($eventData,true));
  })  


   ->on("test", "hello", $foo)  // bind an class function on an instance


  ->on("test", "Bar::listen")  // bind a static class function



 ;
$testData=array(
  'data' => array('someTestData', 1, 2, 3, 5, 8, 13, 21, new \stdclass),
  'Author' => '(c) Till Wehowski, http://frdl.webfan.de',
  '__FILE__' => __FILE__,
);
$myElement()
   ->on("test", "test", new TestElement)  
    
  // dispatch the "test event"  
   ->trigger("test", $testData)
    
   ;