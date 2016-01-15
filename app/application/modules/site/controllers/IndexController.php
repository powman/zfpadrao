<?php

class Site_IndexController extends Zend_Controller_Action 
{
    public function init(){
        

    }
	public function indexAction()
	{
	 

	}
	public function loginAction()
	{
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	
	}

}