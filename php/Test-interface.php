<?php

Final Class CData{
    private static $str = 'From Heaven!';
    public static function getProps(){
        return static::$str;
    }
}

Interface IConfInterface{
    public function getConf():Array;
}

Class CConf implements IConfInterface{
	private $mock = false;
    private $liveConf = [
		'cRepo' => 'CRepoExtern',
		'cView' => 'CViewUpper'
	];
	private $mockConf = [
		'cRepo' => 'CRepo',
		'cView' => 'CView'
	];
	public function __construct($bMock = false){
		$this->mock = $bMock;
	}
	public function	getConf():Array{
		return ($this->mock) ? $this->mockConf : $this->liveConf;
	}	
}


Interface IRepoInterface{
    public function getData();
}

Class CRepo implements IRepoInterface{
    public function getData(){
        return "To Hell!!";
    }
}

Class CRepoExtern implements IRepoInterface{
    public function getData(){
        return $this->loadData();
    }
    private function loadData(){
        return CData::getProps();
    }
}

Interface IViewInterface{
    public function show($data):String;
}

Class CView implements IViewInterface{
    public function show($data):String{
        return '(DESKTOP) '.strtolower($data);
    }
}

Class CViewUpper implements IViewInterface{
    public function show($data):String{
        return '(DESKTOP) '.strtoupper($data);
    }
}

Interface IControllerInterface{
    public function process():String;
}

Class CController implements IControllerInterface{
    private $repo;
    private $view;
    public function __construct(IRepoInterface $repo, IViewInterface $view){
        $this->repo = $repo;
        $this->view = $view;
    }
    public function process():String{
        return $this->view->show($this->repo->getData());
    }
}

Class CApp {
    private $controller;
    public function __construct(IConfInterface $Iconf){
        $conf = $Iconf->getConf();
        $this->controller = new CController(
			new $conf['cRepo'](), 
			new $conf['cView']()
		);
    }
    public function run(){
        echo $this->controller->process();
    }
}

(new CApp(new CConf(true)))->run();




//EOF