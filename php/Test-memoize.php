<?php

$maxLoop = 100000;
$maxPossibilities = 1000;

$randArgs = [];
for($i=0;$i!==$maxLoop;$i++){
    $randArgs[] = rand(1, $maxPossibilities);
}

// COMMON INTERFACE --------------------------------------

interface IRepositoryInterface
{

    public function get($id);
    public function getCounter();

}


//WITH DECORATOR -----------------------------------------

class CRepository implements IRepositoryInterface
{
    private $counter = 0;

    public function get($id)    {
        return $this->fetch($id);
    }
    public function getCounter(){
        return $this->counter;
    }
    private function fetch($id) { 
        usleep(5);
        $this->counter++;
        return "{$id}";
    }
}
 
class CRepositoryCacheDecorator implements IRepositoryInterface
{
    private $results = [];
    private $repository;
    
    public function __construct(IRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function get($id){
        $key = md5(__METHOD__.\serialize($id));
        if(!isset($this->results[$key])) {
            $this->results[$key] = $this->repository->get($id);
        }
        return $this->results[$key];
    }
    public function getCounter(){
        return $this->repository->getCounter();
    }
}


//WITH TRAIT --------------------------------------------

trait TCache
{
    private $results = [];
 
    protected function cacheCall($methodName, $args){
        $key = md5($methodName.\serialize($args));
        if (!isset($this->results[$key])) {
            $this->results[$key] = $this->repository->{$methodName}(...$args);
        }
        return $this->results[$key];
    }
}

class CRepositoryCacheTrait implements IRepositoryInterface
{
    use TCache;
    private $repository;
    public function __construct(IRepositoryInterface $repository){
        $this->repository = $repository;
    }
    public function get($id){
        return $this->cacheCall(__FUNCTION__, \func_get_args());
    }
    public function getCounter(){
        return $this->repository->getCounter();
    }
}


// -------------------------------------


$repositoryDecorator = new CRepositoryCacheDecorator(new CRepository());
$repositoryTrait = new CRepositoryCacheTrait(new CRepository());

$start = microtime(true);
for($i=0;$i!==$maxLoop;$i++){
    $repositoryDecorator->get($randArgs[$i]);
}
$timer = microtime(true) - $start;
echo "DECORATOR[{$repositoryDecorator->getCounter()}] TOOK {$timer}".PHP_EOL;

$start = microtime(true);
for($i=0;$i!==$maxLoop;$i++){
    $repositoryTrait->get($randArgs[$i]);
}
$timer = microtime(true) - $start;
echo "TRAIT[{$repositoryTrait->getCounter()}] TOOK {$timer}".PHP_EOL;





//EOF