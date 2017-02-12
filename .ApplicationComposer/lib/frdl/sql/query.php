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
namespace frdl\SQL;


final class Query
{
    const TOK_NAMED = ':';
    const TOK_NUM = '?';
    const TOK = " ";
    
  
    
    protected $errors = array();
    
    protected $sec = array();

    protected $isUnparsed = false;
    protected $isComposed = false;
	
	
    protected $q_schema_tables = array();
	protected $q_statement;
	protected $q_subjects = array();
	protected $q_context;  //  FROM_OR_INTO
	protected $q_tables = array(/* array('table' => '', 'AS' => '', 'alias' => '') */);	
	protected $q_joins = array();
	protected $q_fields = array(/* array( 'conjunction' => 'AND|OR', 'calculus' => '<|>|=|LIKE', 'column' => Spaltenname, [ //'value' => &$value] ) */);
	protected $q_set = array();
	
	
	 // protected $q_start = null;   //   -1    |   0 |  > 0
	 // protected $q_limit = null;   //   n   |   -n(= start=INF-n, INF )  |   INF  INF=>PHP_INT_MAX
	 protected $q_limit = array();  //  [x,n]
	
	protected $q_orderBy = array();
	protected $q_groupBy = array();
	
	protected $query = '';
	
	private $reserved = array('', 'user', 'is', 'file', 'notify', 'restrict', 'password', 'group', 'order', 'key', 'condition', 'option', 
	                              'read', 'ignore', 'index');
	
	
	public function __construct($compose = false, Array $o = null){
		$this->sec = array(
		     'statement' => '',
		     'subjects' => '',
		     'context' => '',   //  FROM_OR_INTO
		     'tables' => '',
		     'joins' => '',
		     'set' => '',
		     'where' => '',
		     'group' => '',
		     'order' => '', 
		     'limit' => '',
		);
		
		
           if(null !== $o)$this->set( $o );	
		  
		   if(true === $compose)$this->compose();
		   
	}
	
	
	public function _compose(Array $o = null){
	    if(null !== $o)$this->set( $o );	

		$this->query = '';		 
		$this->isUnparsed = false;
		$this->isComposed = false;
		

		$this->sec['statement'] = strtoupper($this->command);		
		$this->sec['subjects'] = trim(implode(',', $this->subjects), ' ,');		
		
		$this->sec['context'] = trim(strtoupper($this->sec['context']));
		$this->sec['context'] =  (    'FROM' === $this->sec['context']
		                           || 'INTO' === $this->sec['context']
		                           || '' === $this->sec['context']
		                           ) ? strtoupper($this->context)
		                             : '';
		
		foreach($this->tables as $num => &$t){
		    $t['AS'] = ('AS' === strtoupper($t['AS']) || '' === $t['AS']) ? $t['AS'] : '';
		    $t['table']  = (isset($this->q_schema_tables[$t['table']] ) && !in_array($t['table'], $this->reserved)) ? $t['table'] : '';
			$this->sec['tables'] .=  $t['table'] . self::TOK .  $t['AS'] . self::TOK .  $t['alias'];
			$this->sec['tables'] .= ',';
		}
		 if(count($this->tables) > 0)$this->sec['tables'] = substr_replace($this->sec['tables'] , '', -1);
		
		
		foreach($this->joins as $num => &$join){
			if('JOIN' === get_class($join))$this->sec['joins'] .= (string)$join. self::TOK;
		}
		
		
		
		if(count($this->set) > 0){
		  $this->sec['set'] .=  self::TOK .'SET' . self::TOK ;
		  foreach($this->set as $num => &$field){
		  	 if(!isset($this->q_schema_tables[$this->tables[0]['table']][$field['column']] ) ||  in_array($field['column'], $this->reserved) ){
			 	 $this->error('Column '.$this->tables[0]['table'].'.'.$field['column'].' does not exist '.__METHOD__, E_USER_ERROR);
			 	return $this;
			 }
            $this->sec['set'] .=  $field['column'] . " = :". $field['column'] . ","; 
		  }
			$this->sec['set'] = substr_replace($this->sec['set'] , '', -1);		
		}		
		
		
		
		if(count($this->fields) > 0){
		  $this->sec['where'] .=  self::TOK .'WHERE' . self::TOK ;
		  foreach($this->fields as $num => &$field){
		  	 if(!isset($this->q_schema_tables[$this->tables[0]['table']][$field['column']] ) ||  in_array($field['column'], $this->reserved)){
			 	 $this->error('Column '.$this->tables[0]['table'].'.'.$field['column'].' does not exist '.__METHOD__, E_USER_ERROR);
			 	return $this;
			 }
		  	 if($num > 0) $this->sec['where'] .= $field['conjunction'];
		  	 $this->sec['where'] .= self::TOK;
		  	 if('LIKE' === strtoupper($field['calculus']) )$field['value'] = '%'.$field['value'].'%';
		  	 $this->sec['where'] .= $field['column'];
		  	 $this->sec['where'] .= self::TOK;
		  	 $this->sec['where'] .= $field['calculus'];
		     $this->sec['where'] .= self::TOK;    
		     $this->sec['where'] .= self::TOK_NAMED.$field['column'];
		     $this->sec['where'] .= self::TOK;    
		  }
					
		}


		
		foreach($this->groupBy as $column => $order)
		{
		  	 if(!isset($this->q_schema_tables[$this->tables[0]['table']][$column] ) ||  in_array($field['column'], $this->reserved)){
			 	 $this->error('Column '.$this->tables[0]['table'].'.'.$column.' does not exist. '.__METHOD__, E_USER_ERROR);
			 	return $this;
			 }
			
			$order = strtoupper($order);
			if('ASC' !== $order && 'DESC' !== $order && '' !== $order){
				 unset($this->groupBy[$column]);
		    	 $this->error('Invalid sort oder: '.strip_tags($order).' '.__METHOD__, E_USER_ERROR);
			 	return $this;				
			} 
			$this->sec['group']  .= " ". $column  . "  ". $order . ",";
		}		
  	    if(count($this->groupBy) > 0)$this->sec['group']  = 'GROUP'.self::TOK.'BY' . self::TOK . substr_replace($this->sec['group']  , '', -1);	
		




	
		foreach($this->orderBy as $column => $order)
		{
		  	 if(!isset($this->q_schema_tables[$this->tables[0]['table']][$column] ) ||  in_array($field['column'], $this->reserved)){
			 	 $this->error('Column '.$this->tables[0]['table'].'.'.$column.' does not exist. '.__METHOD__, E_USER_ERROR);
			 	return $this;
			 }
			
			$order = strtoupper($order);
			if('ASC' !== $order && 'DESC' !== $order && '' !== $order){
				 unset($this->orderBy[$column]);
		    	 $this->error('Invalid sort oder: '.strip_tags($order).' '.__METHOD__, E_USER_ERROR);
			 	return $this;				
			} 
			$this->sec['order']  .= " ". $column  . "  ". $order . ",";
		}		
  	    if(count($this->orderBy) > 0)$this->sec['order']  = 'ORDER'.self::TOK.'BY' . self::TOK . substr_replace($this->sec['order']  , '', -1);	
				
				
	    if( count($this->limit) > 0 ){
			$separator = (2  === count($this->limit)) ? ',' : '';
			$start =  (is_int($this->limit[0])) ? (int) $this->limit[0] : ''; 
		    $limit = (is_int($this->limit[1]) || INF === $this->limit[1]) ? $this->limit[1] : '';
		    if(INF === $limit)$limit = PHP_INT_MAX;
			if($limit < 0){
				$limit = PHP_INT_MAX -  ((-1)*$limit);
			}
			$this->sec['limit'] =  'LIMIT' . self::TOK . $start . $separator . $limit;
		}			
				
	 			
  	   $this->isComposed = true;
  	   
  	   $this->unparse();
  	   
      return $this; 
	}
	
	
	
	
	
	
	
	
	protected function _unparse(){
		if (!is_subclass_of($Crud, '\frdl\Crud')){
		 	    $msg = 'Invalid ORM class mu´st be subclass of \'\frdl\Crud\'';
   	           	$trace = debug_backtrace();
	          	trigger_error(
		            $msg.__CLASS__.'_prepare(): ' .
		           ' in ' . $trace[0]['file'] .
		           ' on line ' . $trace[0]['line'],
		           E_USER_ERROR);	
		          return $this; 		
		}
		 	     
	  
	    if(true !== $this->isComposed)$this->compose();
		
		$this->query = trim(implode(self::TOK, $this->sec), ' ,');
		
		
		
		
		$this->isUnparsed = true;
	  return $this;	
	}
	
	
	
	protected function _query(&$query = null){
		  $query = trim($this->query);
    	return $this;	
	}
	
	
	protected function _Set(Array $o = null){
		 if(is_array($o)){
		 	 $props = array_keys($o);
		 	 foreach($props as $name => $value){
			 	$this->__set($name, $value);
			 }
		 }
    	return $this;	
	}
	
	protected function _errors(&$errors = null){
		  $errors  = $this->errors;
    	return $this;	
	}
		
    protected function error($msg, $level = E_USER_ERROR){
		array_push($this->errors, array($msg, $level));
	}		
			
	public function __call($name, $args){
		 switch($name){
		 	  case 'unparse' :
		 	        $this->_unparse();
		          return $this; 		 	  
		 	    break;
		 	  case 'compose' :
		 	        $this->_compose($args[0]);
		          return $this; 		 	  
		 	    break;
		 	  case 'set' :
		 	        $this->_Set($args[0]);
		          return $this; 		 	      
		 	    break;
		 	  case 'query' :
		 	       if(true !== $this->isComposed)$this->compose();
		 	        $this->_query($args[0]);
		          return $this;
		 	  case 'errors' :
		 	        $this->_errors($args[0]);
		          return $this; 
		 	    break;
		 	    
		 	  default : 
		 	    $msg = 'Unsupported method call in ';
   	           	$trace = debug_backtrace();
	          	trigger_error(
		            $msg.__CLASS__.'__call(): ' . $name .
		           ' in ' . $trace[0]['file'] .
		           ' on line ' . $trace[0]['line'],
		           E_USER_ERROR);	
		          return $this; 
		 	    break;  
		 }
	}
	
	public function __get($name){
        if('sec' === $name)return $this->sec;
		return (isset($this->{'q_'.$name})) ? $this->{'q_'.$name} : ´((isset($this->{'is'.$name})) ? $this->{'is'.$name} : null);
	}
	
	public function __set($name, $value){
		if(isset($this->{'q_'.$name}))$this->{'q_'.$name} = $value;
		return $this;
	}	
}