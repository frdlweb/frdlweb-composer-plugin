<?php
//http://php.net/manual/en/class.recursivecallbackfilteriterator.php#110974
class RecursiveCallbackFilterIterator extends RecursiveFilterIterator {
   
    public function __construct ( RecursiveIterator $iterator, $callback ) {
       
        $this->callback = $callback;
       
        parent::__construct($iterator);
       
    }
   
    public function accept () {
       
        $callback = $this->callback;
       
        return $callback(parent::current(), parent::key(), parent::getInnerIterator());
       
    }
   
    public function getChildren () {
       
        return new self($this->getInnerIterator()->getChildren(), $this->callback);
       
    }
   
}
