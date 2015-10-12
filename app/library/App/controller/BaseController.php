<?php

class App_Controller_BaseController extends Zend_Controller_Action
{
	public $models = array();
	public $modelAtual = '';
	
    public function init(){
    	// pega os dados da sessao
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	// importar o css de acordo com o controler
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
		// importar o js de acordo com o controler
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
		// classes uteis para usar
        $this->uteis = new App_AbstractController();
		// nome do modulo
		$this->modulo = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		// nome do controller
		$this->controle = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		// nome da acao
		$this->acao = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		// pega o id do usuario logado
		$this->idUsuario = $identity->id;
		// loader dos models $models 
		foreach ($this->models as $value) {
			$modelLoader = 'Painel_Model_'.$value.'';
			$modelName = 'model'.$value.'';
			$this->{$modelName} = new $modelLoader();
		}
		// inclue o model de logs
		$this->modelLog = new Painel_Model_Logs();
		// inclue o model do controle atual
		$modelAtualLoader = 'Painel_Model_'.$this->modelAtual.'';
		$this->model = new $modelAtualLoader();
    }

	public function excluirAction()
	{
	    $resposta = array();
		// desabilita o layout
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
    
	    // chama a funcao excluir
	    $result = $this->model->excluir($this->getRequest()->getParam('id'));
	     
	    if($result){
	        $resposta['situacao'] = "sucess";
	        $resposta['msg'] = "Excluido com sucesso!";
	        
	        // Gravar o log
	        $this->gravarLog('Excluiu um dado');
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Excluir!";
	    }
		// se for ajax retorna o json, senao retorna o array
	    if ($this->getRequest()->isXmlHttpRequest()) { 
	    	echo json_encode($resposta); 
		}else{
			return $resposta;
		}
	}
	
	public function gravarLog($descricao){
		 // Grava o Log
        $data = array();
        $data['modulo'] = $this->modulo;
        $data['controller'] = $this->controle;
        $data['metodo'] = $this->acao;
        $data['ip'] = $_SERVER['REMOTE_ADDR'];
        $data['data'] = time();
        $data['usuario_id'] = Zend_Auth::getInstance()->getIdentity()->id;
        $data['usuario_nome'] = Zend_Auth::getInstance()->getIdentity()->nome;
        $data['descricao'] = $descricao;
        return $this->modelLog->save($data);
	}

}