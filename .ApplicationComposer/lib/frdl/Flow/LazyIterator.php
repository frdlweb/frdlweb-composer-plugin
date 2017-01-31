<?php
namespace frdl\Flow;
/**  LazyIterator
* from http://php.net/manual/de/language.generators.syntax.php
* CachedGenerator => LazyIerator
* (c)  info at boukeversteegh dot nl
* 
* 
* 
You can use generators to do lazy loading of lists. You only compute the items that are actually used. However, when you want to load more items, how to cache the ones already loaded?

Here is how to do cached lazy loading with a generator:
* 
* 
* 
* class Foobar {
    protected $loader = null;

    protected function loadItems() {
        foreach(range(0,10) as $i) {
            usleep(200000);
            yield $i;
        }
    }

    public function getItems() {
        $this->loader = $this->loader ?: new CachedGenerator($this->loadItems());
        return $this->loader->generator();
    }
}

$f = new Foobar;

# First
print "First\n";
foreach($f->getItems() as $i) {
    print $i . "\n";
    if( $i == 5 ) {
        break;
    }
}

# Second (items 1-5 are cached, 6-10 are loaded)
print "Second\n";
foreach($f->getItems() as $i) {
    print $i . "\n";
}

# Third (all items are cached and returned instantly)
print "Third\n";
foreach($f->getItems() as $i) {
    print $i . "\n";
}
*/

/*
function fibonacci($item) {
    $a = 0;
    $b = 1;
    for ($i = 0; $i < $item; $i++) {
        yield $a;
        $a = $b - $a;
        $b = $a + $b;
    }
}
$fibo = fibonacci(10);

$list= $fibo;

function loadItems($list) {
            foreach ($list as $value) {
               yield $value;
            }
}
$iterator = new LazyIterator(loadItems($list))->generator();
foreach($iterator as $i) {
    print $i . "\n";
    if( $i == 5 ) {
        break;
    }
}

foreach($iterator as $i) {
    print $i . "\n";
}


*/

class LazyIterator {
    protected $cache = [];
    protected $generator = null;

    public function __construct($generator) {
        $this->generator = $generator;
    }

    public function generator() {
        foreach($this->cache as $item) yield ($item);

        while( $this->generator->valid() ) {
            $this->cache[] = $current = $this->generator->current();
            $this->generator->next();
            yield ($current);
        }
    }
}