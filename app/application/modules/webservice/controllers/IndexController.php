<?php

class Webservice_IndexController extends Zend_Controller_Action 
{
	public function indexAction()
	{
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    
	    // initialize server and set URI
	    $server = new Zend_Soap_Server(null,
	        array('uri' => 'http://painel.local/webservice/index'));
	    
	    // set SOAP service class
	    $server->setClass('App_Webservice_Imagem');
	    
	    // handle request
	    $server->handle();

	}

}