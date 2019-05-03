<?php
/**
 * Created by PhpStorm.
 * User: Dwizzel
 * Date: 18/02/2018
 * Time: 1:52 PM
 */
trait Macroable
{
    protected static $macros = [];
    protected static $self = null;
    
    public static function macro($name, $macro)
    {
        static::$macros[$name] = $macro;
    }
    private static function getInst()
    {
        if(static::$self === null){
            $class = __NAMESPACE__.'\\'.__CLASS__;
            static::$self = new $class;
        }
        return static::$self;
    }
    private static function hasMacro($name)
    {
        return isset(static::$macros[$name]);
    }
    public function __set($key, $value)
    {
        $self = static::getInst();
        $self->{$key} = $value;
    }
    public function __get($key)
    {
        $self = static::getInst();
        if(isset($self->{$key})){
            return $self->{$key};
        }
        return null;
    }
    public function __call($method, $parameters)
    {
        $self = static::getInst();
        if (! static::hasMacro($method)) {
            throw new BadMethodCallException("Method {$method} does not exist.");
        }
        $macro = static::$macros[$method];
        if ($macro instanceof Closure) {
            call_user_func_array($macro->bindTo($self), $parameters);
        }else{
            call_user_func_array($macro, $parameters);    
        }
        return $self;
    }
}


//------------------------

class Router{
    
    use Macroable;
    
    protected $name = "helll!";
    
    
}


//-----------------------



Router::macro('blabla', function($args){
    echo $this->name.PHP_EOL;
    echo $this->header.PHP_EOL;
    print_r($args);
});

//(new Router)->blabla([1,2,3])->blabla([5,6,7]);

$router = new Router;
$router->header = 'test string';
$router->blabla([1,2])->blabla([3,4]);
$router->name = 'heaven!';
$router->blabla([5,6])->blabla([7,8]);


/*

$reflection = new ReflectionClass($router);
$methods = $reflection->getMethods();
$properties = $reflection->getProperties();
var_dump($methods);
var_dump($properties);

*/


//-----------------------