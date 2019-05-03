<?php

declare(strict_types=1);

//static data---------------------------------------------

Class CData{
    private static $arr = [
		'firstName' => 'Olivier',
		'lastName' => 'Renaldin',
	];
    public static function getInfos(){
        return static::$arr;
    }
}


//arr to properties ---------------------------------------

Class CDataObject{
	private $data = [];
    public function __construct($data){
		$this->data = $data;
    }
	public function __set($k, $v){
		$this->data[$k] = $v;
	}
	public function __get($k){
		return isset($this->data[$k]) ? $this->data[$k] : null;
	}
	public function __isset($k){
		return isset($this->data[$k]);
	}
	public function __unset($k){
		unset($this->data[$k]);
	}
}


//conf----------------------------------------------------

Interface IConfInterface{
    public function isMock(): bool;
}

Class CConf implements IConfInterface{
	private $mock;
	public function __construct($bMock = false){
		$this->mock = $bMock;
	}
	public function	isMock(): bool{
		return ($this->mock === true);
	}
}


//request--------------------------------------------------

Interface IRequestInterface{
    public function getAll(): array;
}

Class CRequest implements IRequestInterface{
	private $args = [];
	public function __construct($args){
		$this->args = $args;
	}
	public function __get($key){
	    return isset($this->args[$key]) ? $this->args[$key] : null;
	}
	public function getAll(): array{
	    return $this->args;
	}
}


//repo and a repo adapter--------------------------------------

Interface IRepoInterface{
    public function getData() :array;
}

Class CRepo implements IRepoInterface{
    public function getData() :array{
        return [
			'firstName' => 'John',
			'lastName' => 'Doe',
		];
    }
}

Class CRepoDBAdapter implements IRepoInterface{
	private $repo;
	public function __construct(IRepoDB $repo){
		$this->repo = $repo;
	}	
    public function getData() :array{
        return $this->repo->loadData();
    }
}

Interface IRepoDB{
    public function loadData() :array;
}

Class CRepoDB implements IRepoDB{
    public function loadData() :array{
        return CData::getInfos();
    }
}


//view-------------------------------------------------------

Interface IViewInterface{
    public function show($data): string;
}

Class CView implements IViewInterface{
    public function show($data): string{
        return "<p>{$data->firstName} {$data->lastName}</p>";
    }
}


//view decorator----------------------------------------------

Abstract Class AViewDecorator implements IViewInterface{
    protected $view;
    public function __construct(IViewInterface $view){
        $this->view = $view;
    }
}

Class CViewDecoratorDesktop extends AViewDecorator{
    public function show($data): string{
        return '<desktop>'.($this->view->show($data)).'</desktop>';
    }
}

Class CViewDecoratorMobile extends AViewDecorator{
    public function show($data): string{
        return '<mobile>'.($this->view->show($data)).'</mobile>';
    }
}


//controller--------------------------------------------------

Interface IControllerInterface{
    public function render(): string;
}

Class CController implements IControllerInterface{
    private $repo;
    private $view;
    public function __construct(IRepoInterface $repo, IViewInterface $view){
        $this->repo = $repo;
        $this->view = $view;
    }
    public function render(): string{
        return $this->view->show(
			new CDataObject($this->repo->getData())
		);
    }
}


//container----------------------------------------------------

Class CContainer {
    private $controller;
    public function __construct(IConfInterface $conf, IRequestInterface $request){
        $repo = $conf->isMock() ? new CRepo() : new CRepoDBAdapter(new CRepoDB());
        $view = new CView();
        $view = $request->mobile ? new CViewDecoratorMobile($view) : new CViewDecoratorDesktop($view);
        $this->controller = new CController($repo, $view);
    }
    public function process(){
        echo $this->controller->render();
    }
}


//app-------------------------------------------------------

Class CApp {
    private $container; 
    private $request;
    private $conf;
    public function __construct($req = []){
        $this->request = new CRequest($req);
        $this->conf = new CConf($this->request->mock);
        $this->container = new CContainer($this->conf, $this->request);
    }
    public function run(){
        $this->container->process();
    }
}

//run-------------------------------------------------------


(new CApp([
	'mobile' => true, 
	'mock' => false,
	'lang' => 'en_US'
]))->run();




//EOF