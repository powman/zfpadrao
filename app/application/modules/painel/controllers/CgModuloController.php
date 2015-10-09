<?php

class CgModuloController extends Zend_Controller_Action
{
    
    public function init()
    {
        $this->uteis = new App_AbstractController(); 
        $this->view->mostra_head_footer = true;
        $this->model = new Painel_Model_CgModulo();
        $this->modelSubmenu = new Painel_Model_CgModuloSubmenu();
        $this->modelLog = new Painel_Model_Logs();
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
        
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
    }
	
	public function indexAction()
	{
	    
		
	}
	
	public function listarAction()
	{
	
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	
	    $sql = $this->model->getAdapter()->select()->from('modulo');
	
	    // Parametros para busca
	    if($this->getRequest()->getParam('pesquisa'))
	        $sql->orWhere('nome LIKE ?', "%".$this->getRequest()->getParam('pesquisa')."%");
	    
// 	    // Parametros para busca
// 	    if($this->getRequest()->getParam('pesquisa'))
// 	        $sql->orWhere('status = ?', "".$this->getRequest()->getParam('pesquisa')."");
	
	    if($this->getRequest()->getParam('pesquisa'))
	        $sql->orWhere('id = ?', $this->getRequest()->getParam('pesquisa'));
	    // fim Parametros para busca
	
	    // total com a pesquisa
	    $totalTudo = $this->model->getAdapter()->fetchAll($sql);
	
	    // Verifica a ordenacao
	    if($this->getRequest()->getParam('sort') && $this->getRequest()->getParam('order')){
	        $ordernar = $this->getRequest()->getParam('sort')." ".$this->getRequest()->getParam('order');
	        $sql->order($ordernar);
	    }
	
	    // Verifica o limit e o offset
	    if($this->getRequest()->getParam('limit') || $this->getRequest()->getParam('offset'))
	        $sql->limit($this->getRequest()->getParam('limit'),$this->getRequest()->getParam('offset'));
	
	    $logs = $this->model->getAdapter()->fetchAll($sql);
	    $total = count($logs);
	    $values['total'] = count($totalTudo);
	
	    for($i=0;$i<$total;$i++){
	        $values['rows'][$i]['id'] = $logs[$i]['id'];
	        $values['rows'][$i]['nome'] = $logs[$i]['nome'];
	        $values['rows'][$i]['awsome'] = $logs[$i]['awsome'] ? '<i class="fa fa-'.$logs[$i]['awsome'].'"></i>' : '';
	        $values['rows'][$i]['ordem'] = $logs[$i]['ordem'];
	        $values['rows'][$i]['status'] = $logs[$i]['status'] ? "Ativo" : "Inativo";
	    }
	
	    echo json_encode($values);
	}
	public function excluirAction()
	{
	     
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	     
	    $sql = $this->modelSubmenu->getAdapter()->select()->from('modulo_menu_sub');
	     
	    // Parametros para busca
	    if($this->getRequest()->getParam('id')){
	        $sql->where('modulo_id = ?',$this->getRequest()->getParam('id'));
	    }
	    // fim Parametros para busca
	     
	    // total com a pesquisa
	    $data = $this->modelSubmenu->getAdapter()->fetchRow($sql);
	    if($data["id"]){
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Excluir, existem controllers relacionados a este modulo!";
	        echo json_encode($resposta);
	        exit();
	    }
	
	
	    // total com a pesquisa
	    $result = $this->model->getAdapter()->delete("modulo",'id = '.$this->getRequest()->getParam('id'));
	
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Excluido com sucesso!";
	         
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Exclusão do Módulo";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Excluir!";
	    }
	
	    echo json_encode($resposta);
	
	
	}
	public function cadastroRapidoAction()
	{
	
	    $this->_helper->layout()->disableLayout();
	}
	public function cadastroFormAction()
	{
	     
	}
	
	public function cadastrarAcaoAction()
	{
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	
	    $data['modulo'] = $this->getRequest()->getParam('modulo');
	    $data['nome'] = $this->getRequest()->getParam('nome');
	    $data['awsome'] = $this->getRequest()->getParam('awsome');
	    $data['status'] = $this->getRequest()->getParam('status');
	    $data['ordem'] = $this->getRequest()->getParam('ordem') ? $this->getRequest()->getParam('ordem') : 0;
	
	    $result = $this->model->save($data);
	
	    if($result){
	        if($this->getRequest()->getParam('retornoUrl'))
	            $resposta['retornoUrl'] = $this->getRequest()->getParam('retornoUrl').$result;
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Cadastrado com sucesso!";
	         
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Cadastro do Módulo";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Cadastrar!";
	    }
	
	    echo json_encode($resposta);
	}
	
	public function editarRapidoAction()
	{
	     
	    $sql = $this->model->getAdapter()->select()->from('modulo');
	
	    // Parametros para busca
	    if($this->getRequest()->getParam('id')){
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    }
	    // fim Parametros para busca
	
	    // total com a pesquisa
	    $data = $this->model->getAdapter()->fetchRow($sql);
	    $this->view->modulo = $data;
	     
	    $this->_helper->layout()->disableLayout();
	}
	
	public function editarFormAction()
	{
	     
	    $sql = $this->model->getAdapter()->select()->from('modulo');
	     
	    // Parametros para busca
	    if($this->getRequest()->getParam('id')){
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    }
	    //fim Parametros para busca
	     
	    // total com a pesquisa
	    $data = $this->model->getAdapter()->fetchRow($sql);
	    $this->view->modulo = $data;
	     
	     
	     
	}
	public function editarAcaoAction()
	{
	
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	     
	    $data['id'] = $this->getRequest()->getParam('id');
	    $data['modulo'] = $this->getRequest()->getParam('modulo');
	    $data['nome'] = $this->getRequest()->getParam('nome');
	    $data['awsome'] = $this->getRequest()->getParam('awsome');
	    $data['status'] = $this->getRequest()->getParam('status');
	    $data['ordem'] = $this->getRequest()->getParam('ordem') ? $this->getRequest()->getParam('ordem') : 0;
	     
	    $result = $this->model->save($data);
	     
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Editado com sucesso!";
	         
	        // Grava Logo
	        $data = array();
	        $data['modulo'] = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
	        $data['controller'] = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
	        $data['metodo'] = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
	        $data['ip'] = $_SERVER['REMOTE_ADDR'];
	        $data['data'] = time();
	        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
	        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
	        $data['descricao'] = "Edição do módulo";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Editar!";
	    }
	
	    echo json_encode($resposta);
	}


}