<?php

class IndexController extends App_Controller_BaseController 
{
    public $models = array();
    public $modelAtual = '';
    public $msg = null;

	public function indexAction()
	{
	 $tblModulo = new Painel_Model_CgModulo();
	 //$params = array("nome" => "teste");
	 $tblModulo->listarTodos();

	}
	
	public function loginAction()
	{
	      $this->view->Serviceimagem(1);
	}
	
	public function logarAction()
	{
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    $resposta = array();
	    
	    $dbAdapter = Zend_Db_Table_Abstract::getDefaultAdapter();
	    $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
	    
	    $authAdapter->setTableName('sca_usuario')
	    ->setIdentityColumn('login_usuario')
	    ->setCredentialColumn('password_usuario')
	    ->getDbSelect()
	    ->join( array('g' => 'sca_grupo'), 'g.id_grupo = sca_usuario.id_grupo', array('nm_grupo','is_root') );
	    
	    $authAdapter->setIdentity($this->getRequest()->getParam('login_usuario'))
	    ->setCredential($this->getRequest()->getParam('password_usuario'))
	    ->setCredentialTreatment('MD5(?) and st_usuario = 1');
	    
	    //Realiza autenticacao
	    $result = $authAdapter->authenticate();
	    //Verifica se a autenticacao foi validada
	    if($result->isValid()){
	        //obtem os dados do usuario
	        $usuario = $authAdapter->getResultRowObject();
	        //Armazena seus dados na sessao
	        $storage = Zend_Auth::getInstance()->getStorage();
	        $storage->write($usuario);
	        // se não for para lembrar os dados expira a sessao em 30 minutos
	        if(!$this->getRequest()->getParam('lembrar')){
    	        $session = new Zend_Session_Namespace( 'Zend_Auth' );
    	        $session->setExpirationSeconds( 1800 );
	        }
	        //Redireciona para o Index
	        $resposta['situacao'] = "success";
	        $resposta['msg'] = "Logando aguarde...";
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Usuário inativo ou senha incorreta.";
	    }
	    
	    echo json_encode($resposta);
	}
	
	public function sessaoAction()
	{
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    $resposta = array();
	    
	    $resposta['situacao'] = "success";
	    $resposta['dados'] = $this->view->sessao;
	    
	    echo json_encode($resposta);

	}

}