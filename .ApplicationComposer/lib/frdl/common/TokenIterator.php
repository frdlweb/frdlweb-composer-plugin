<?php
/**
* @component  TokenIterator 
* @author     pradador at me dot com
* http://php.net/manual/en/function.strtok.php#103962
* 
* The TokenIterator class allows you to iterate through string tokens using
* the familiar foreach control structure.
*
* Example:
* <code>
* <?php
* $string = 'This is a test.';
* $delimiters = ' ';
* $ti = new TokenIterator($string, $delimiters);
*
* foreach ($ti as $count => $token) {
*     echo sprintf("%d, %s\n", $count, $token);
* }
*
* // Prints the following output:
* // 0. This
* // 1. is
* // 2. a
* // 3. test.
* </code>
*/
namespace frdl\common;
class TokenIterator implements \Iterator{protected $_string;protected $_delims;protected $_token;protected $_counter=0;public function __construct($string,$delims){$this->_string=$string;$this->_delims=$delims;$this->_token=strtok($string,$delims);}public function current(){return $this->_token;}public function key(){return $this->_counter;}public function next(){$this->_token=strtok($this->_delims);if($this->valid()){++$this->_counter;}}public function rewind(){$this->_counter=0;$this->_token=strtok($this->_string,$this->_delims);}public function valid(){return $this->_token!==FALSE;}}
