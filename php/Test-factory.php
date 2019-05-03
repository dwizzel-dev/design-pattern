<?php

//Creational Design Pattern: Factory Method
//The Factory Method  is a creational design pattern 
//that provides an interface for creating objects 
//but allows subclasses to alter the type 
//of an object that will be created.

Abstract Class AViewFactoryMethod{
	abstract public function factorize() : IView;
	public function renderView($data){
		$this->view = $this->factorize();
		$this->view->render($data);
	}
}

Class CViewFactoryMethodBig extends AViewFactoryMethod {
	public function factorize() : IView{
		return new CViewBig();	
	}
}

Class CViewFactoryMethodSmall extends AViewFactoryMethod {
	public function factorize() : IView{
		return new CViewSmall();	
	}
}

Interface IView{
	public function render($data);	
}

Class CViewBig implements IView{
    public function render($data){
		$s = strtoupper($data);
		echo "<big>{$s}</big>".PHP_EOL;
	}
}

Class CViewSmall implements IView{
    public function render($data){
		$s = strtolower($data);
		echo "<small>{$s}</small>".PHP_EOL;
	}
}

$cBig = new CViewFactoryMethodBig();
$cSmall = new CViewFactoryMethodSmall();

$cBig->renderView('Hell');
$cSmall->renderView('Heaven');
$cSmall->renderView('Paradis');


//EOF