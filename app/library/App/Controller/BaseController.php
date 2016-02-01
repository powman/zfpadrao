<?php

class App_Controller_BaseController extends Zend_Controller_Action
{
	public $models = array();
	public $modelAtual = '';
	public $msg = null;
	
    public function init(){
    	// pega os dados da sessao
    	$identity = Zend_Auth::getInstance()->getIdentity();
    	$this->view->sessao = $identity;
    	// importar o css de acordo com o controler
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
		// importar o js de acordo com o controler
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
		// classes uteis para usar
        $this->uteis = new App_AbstractController();
		// nome do modulo
		$this->modulo = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		$this->view->modulo = Zend_Controller_Front::getInstance()->getRequest()->getModuleName();
		// nome do controller
		$this->controle = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$this->view->controle = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		// nome da acao
		$this->acao = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		$this->view->acao = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		// pega o id do usuario logado
		if(isset($identity->id))
		  $this->idUsuario = $identity->id;
		if(count($this->models)){
			// loader dos models $models 
			foreach ($this->models as $value) {
				$modelLoader = 'Painel_Model_'.$value.'';
				$modelName = 'model'.$value.'';
				$this->{$modelName} = new $modelLoader();
			}
		}
		// inclue o model de logs
		$this->modelLog = new Painel_Model_Logs();
		if($this->modelAtual){
			// inclue o model do controle atual
			$modelAtualLoader = 'Painel_Model_'.$this->modelAtual.'';
			$this->model = new $modelAtualLoader();
		}
		// Desabilita o layout sempre que uma requisição ajax ocorrer.
		if($this->getRequest()->isXmlHttpRequest()) {
		    
		}
    }
    
    public function getBotaoAction()
    {
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender(true);
        $id        		= $this->getRequest()->getParam('id');
        $dados = array();
        $podeIncluir = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "incluir");
        $podeAlterar = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "alterar");
        $podeRemover = Zend_Registry::get('acl')->isAllowed($this->view->sessao->id_grupo, $this->controle, "remover");
        if(!$id){
            
            $dados[] = array('text' => "Incluir",'classe' => 'btn btn-success','model' => "btn.incluir",'btn' => "incluir","disabled" => $podeIncluir ? "" : "disabled");
            $dados[] = array('text' => "Alterar",'classe' => 'btn btn-info','model' => "btn.alterar",'btn' => "alterar","disabled" => 'disabled');
            $dados[] = array('text' => "Remover",'classe' => 'btn btn-danger','model' => "btn.remover",'btn' => "remover","disabled" => 'disabled');
            $dados[] = array('text' => "Limpar",'classe' => 'btn btn-default','model' => "btn.limpar",'btn' => "limpar","disabled" => "");
            
        }else if($id){
            $dados[] = array('text' => "Incluir",'classe' => 'btn btn-success','model' => "btn.incluir",'btn' => "incluir","disabled" => "disabled");
            $dados[] = array('text' => "Alterar",'classe' => 'btn btn-info','model' => "btn.alterar",'btn' => "alterar","disabled" => $podeAlterar ? "" : "disabled");
            $dados[] = array('text' => "Remover",'classe' => 'btn btn-danger','model' => "btn.remover",'btn' => "remover","disabled" => $podeRemover ? "" : "disabled");
            $dados[] = array('text' => "Limpar",'classe' => 'btn btn-default','model' => "btn.limpar",'btn' => "limpar","disabled" => "");
        }
        
        $html = "";
        foreach ($dados as $key => $value){
            $html .= '<button '.$value['disabled'].' style="'.($value['disabled'] ? "opacity:0.1;" : "").'" type="button" id="'.$value['btn'].'" ng-model="'.$value['model'].'" ng-click="btnAcao(\''.$value['btn'].'\')" class="'.$value['classe'].'">'.$value['text'].'</button>';
        }
    
        echo $html;
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