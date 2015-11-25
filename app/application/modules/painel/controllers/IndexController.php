<?php

class IndexController extends Zend_Controller_Action 
{
    public function init(){

//         if ( !Zend_Auth::getInstance()->hasIdentity() ) {
//             return $this->_helper->redirector->goToRoute( array('controller' => 'ca-auth'), null, true);
//         }
    }
	public function indexAction()
	{
	 $tblModulo = new Painel_Model_CgModulo();
	 //$params = array("nome" => "teste");
	 $tblModulo->listarTodos();

	}
	
	public function loginAction()
	{
	
	}

}