<?php

class IndexController extends Zend_Controller_Action 
{
    public function init(){
        $this->view->cssHelper = Site_Plugin_CssHelper::CssHelper();
        $this->view->jsHelper = Site_Plugin_JavascriptHelper::JsHelper();
        $this->view->mostra_head_footer = true;
        
//         if ( !Zend_Auth::getInstance()->hasIdentity() ) {
//             return $this->_helper->redirector->goToRoute( array('controller' => 'ca-auth'), null, true);
//         }
    }
	public function indexAction()
	{
	 

	}

}