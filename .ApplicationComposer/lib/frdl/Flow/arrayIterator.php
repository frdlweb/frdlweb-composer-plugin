<?php
namespace frdl\Flow;


class arrayIterator implements \Iterator {
  private $a;

  public function __construct( $theArray ) {
    $this->a = $theArray;
  }
  function rewind() {
    return reset($this->a);
  }
  function current() {
    return current($this->a);
  }
  function key() {
    return key($this->a);
  }
  function next() {
    return next($this->a);
  }
  function valid() {
    return key($this->a) !== null;
  }
}
