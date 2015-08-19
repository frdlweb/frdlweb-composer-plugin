<?php
/**
 * 
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
 * 3. All advertising materials mentioning features or use of this software
 *    must display the following acknowledgement:
 *    This product includes software developed by the frdl/webfan.
 * 4. Neither the name of frdl/webfan nor the
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
 * */
 namespace webfan\InstallShield\bridge;
 /**
 * ToDo..
 */ 
 
 class ComposerBridge
 {
    public $success = false;
    public $type;
    public $_event;
    
    /*
     see https://getcomposer.org/doc/articles/scripts.md
    */
    protected $events = array(
    
    /* Command Events# */
           'post-root-package-install' => null,
           'pre-install-cmd' => null,
           'post-create-project-cmd' => null,
           
           /* cmd */
           'pre-file-download' => null,
           'command' => null,
           
           /*Package Events#*/
           'pre-package-install' => null,
           'post-package-install' => null,
           'post-package-uninstall' => null,
           'pre-package-uninstall' => null,
          
         /* Installer Events# */  
           'pre-dependencies-solving' => null,
           'post-dependencies-solving' => null,
    );
    
    
    protected function __construct($type, $event){
       $this->_event = $event;
       $this->type = $type;
       $this->success = true;
       
       $this->setEvent('command', array($this, 'command'));
    }
    
    
    public function command(\Composer\Plugin\CommandEvent $event){
		/**
		* ToDo :  delegate to 
		*            \frdl\ApplicationComposer\Console
		*  ...
		*/
		
		
	}
    
    
    public function setEvent($type, $callback){
		$this->Events[$type] = $callback;
	}
	
    public static function dispatch($event){
           try{
              $Event = new self($event->getName(), $event);
           }catch(Exception $e){
              $Event = new self('error', null);
              trigger_error('Error using brige Composer->frdl/webfan in '.$e->getMessage().'<br />'.$e->getFile().' '.$e->getLine(), E_USER_WARNING);
           }
        
         if(isset($Event->events[$Event->type]) && null !== $Event->events[$Event->type]){
           try{
               return call_user_func_array($Event->events[$Event->type], func_get_args());
           }catch(Exception $e){
              trigger_error('Error dispatching event in '.$e->getMessage().'<br />'.$e->getFile().' '.$e->getLine(), E_USER_WARNING);
           }        
         }
      
           
       return $Event;     
    }
  
  
  public function __get($name){
      if(isset($this->{$name}))return $this->{$name};
      return null;
  }
  
  
  public function __set($name, $value){
      if('_' !== substr($name,0,1) && isset($this->{$name}))return $this->{$name} = $value;
        trigger_error('Accessing protected property in '.__METHOD__.' '.__LINE__, E_USER_WARNING);
      return null;
  } 
 
 public function onPreFileDownload(\Composer\Plugin\PreFileDownloadEvent $event){
 /**
 * ToDo..
 */ 
 }
 
 
 }
