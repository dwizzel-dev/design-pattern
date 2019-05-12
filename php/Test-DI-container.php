<?php


// Interface -----------------------------

Interface IParams {
    public function get($k);
    public function set($k, $v);
    public function save();
    public function restore();
}

Interface ITraitParams {
    public function loadParams(IParams $params = null);
    public function getParams($k);
    public function setParams($k, $v);
    public function saveParams();
    public function restoreParams();
}

Interface IContainer {
    public function getModel(IParams $params = null) : IModel;
    public function getView(IParams $params = null) : IView;
}

Interface IRepo{
    public function fetchData() : array;
}

Interface IModel{
    public function getFormatedData() : Iterator;
}

Interface IView{
    public function renderView(array $data) : IView;
}


// Traits -----------------------------

Trait TParams {

    protected $params;

    public function loadParams(IParams $params = null){
        $this->params = ($params !== null) ? $params : new CParams;
    }

    public function getParams($k){
        return $this->params->get($k, $v);                
    }

    public function setParams($k, $v){
       $this->params->set($k, $v);                
       return $this;
    }

    public function saveParams(){
        $this->params->save();
        return $this;
    }

    public function restoreParams(){
        $this->params->restore();
        return $this;
    }

}


// Classes -----------------------------

//Params

Class CParams implements IParams{
    
    private $data;
    private $saved;

    public function __construct($data = null){
        $this->data = ($data !== null) ? $data : [];
    }

    public function set($k, $v){
        $this->data[$k] = $v;
    }

    public function get($k){
        if(isset($this->data[$k])){
            return $this->data[$k];
        }
        return null;
    }

    public function save(){
        $this->saved = $this->data;
    }

    public function restore(){
        if($this->saved !== null){
            $this->data = $this->saved;
            $this->saved = null;
        }
    }

}

//Container

Class CContainer implements IContainer, ITraitParams{

    use TParams;
    
    public function __construct(IParams $params){
        $this->loadParams($params);
    }

    private function getRepo() : IRepo{
        static $inst;
        if(!isset($inst)){
            $inst = new CRepo($this->params->get('repository'));
        }
        return $inst;
    }

    public function getModel(IParams $params = null) : IModel{
        return new CModel(
            $params, 
            $this->getRepo()
        );
    }

    public function getView(IParams $params = null) : IView{
        return new CView($params);
    }
}


//Repo-Model-View

Class CRepo implements IRepo{

    private $fileName;

    public function __construct($fileName){
        $this->fileName = $fileName;
    }
    
    public function fetchData() : array{
        $path = str_replace("\\", "/", $_SERVER['DOCUMENT_ROOT']);
        $path .= "/data/{$this->fileName}";
        if(file_exists($path)){
            $json = file_get_contents($path);
            $arr = json_decode($json, true);
            return $arr;
        }
        exit("file not found: {$path}");
        return null;
    }

}

Class CModel implements IModel, ITraitParams{

    use TParams;

    private $repo;
        
    public function __construct(IParams $params = null, IRepo $repo){
        $this->loadParams($params);
        $this->repo = $repo;
    }

    public function getFormatedData() : Iterator{
        $arr = $this->repo->fetchData();
        foreach($arr as $row){
            yield [
                'fullName' => "{$row['firstName']} {$row['lastName']}",
                'fullAge' => "{$row['age']} ans",
                'fullCountry' => "from {$row['country']}",
            ];
        }
    }

}

Class CView implements IView, ITraitParams{

    use TParams;

    public function __construct(IParams $params = null){
        $this->loadParams($params);
    }
    
    public function renderView(array $data) : IView{
        $str = "{$data['fullName']} ({$data['fullAge']}) {$data['fullCountry']}";
        if($this->params->get('uppercase')){
           $str = mb_strtoupper($str);
        }
        if($this->params->get('reverse')){
            $str = strrev($str);
         }
        echo $str.PHP_EOL;
        return $this;
    }

}


// App and Controller ----------------------


$container = new CContainer(
    new CParams([
        'repository' => 'prod.json'
    ])
);

$model = $container->getModel();
$view = $container->getView(
    new CParams([
        'uppercase' => false
    ])
);

foreach($model->getFormatedData() as $count=>$viewData){
    if(($count + 1)%2 === 0){
        $view->saveParams()->setParams('uppercase', true)->renderView($viewData)->restoreParams();
    }else{
        $view->renderView($viewData);
    }
}


//EOF