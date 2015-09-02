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


final class JOIN
{
	const WORD = 'JOIN';
	protected $TYPE = '';
	protected $SUBJECT = null;
	protected $ON = null;
	
	
	public function __consrtruct($TYPE = '', $SUBJECT = null, $ON = null){
		$types = array('left', 'right', 'inner', 'outer'  , '');
		foreach(explode($this->tok(), $TYPE) as $TypeTok){
			if(!in_array($TypeTok, $types )){
				trigger_error('Invalid JOIN token: '. $TypeTok .' in '.__METHOD__, E_USER_ERROR);
				return;
			}
		}
		
		$this->TYPE = $TYPE;
		
		$this->SUBJECT = (is_array($SUBJECT) || is_string($SUBJECT) || is_subclass_of($SUBJECT, 'Query') ) ? $SUBJECT : null;
		$this->ON = $ON;
	}
	
	public function tok(){
		return Query::TOK ;
	}
	
	public function __toString(){
		$str = $this->TYPE . $this->tok() . self::WORD . $this->tok() ;
		if(is_array($this->SUBJECT )){
			$str.= $this->SUBJECT ['table']. $this->tok() .  $this->SUBJECT ['alias'];
		}elseif(is_string( $this->SUBJECT )){
			$str .= $this->SUBJECT ;
		}elseif( is_subclass_of($this->SUBJECT , 'Query')){
	     	$this->SUBJECT ->query($q);
			$str .= $q;
		}
		
		$str .= $this->ON;
		
		return $str;
	}
}