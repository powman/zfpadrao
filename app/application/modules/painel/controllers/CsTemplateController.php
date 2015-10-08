<?php

class Painel_CsTemplateController extends Zend_Controller_Action
{
    
    public function init()
    {
        $this->uteis = new App_AbstractController(); 
        $this->view->mostra_head_footer = true;
        $this->model = new Painel_Model_CsTemplate();
        $this->model_opcao = new Painel_Model_CsTemplateOpcao();
        $this->modelLog = new Painel_Model_Logs();
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
        
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'auth'), null, true);
        }
    }
	
	public function indexAction()
	{
	    $this->_helper->layout()->disableLayout();
	    $this->view->id = $this->getRequest()->getParam('id');
	    
	    $sql = $this->model_opcao->getAdapter()->select()->from('template_opcao');
	    
	    // Parametros para busca
	    if($this->getRequest()->getParam('id')){
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    }
	    // fim Parametros para busca
	    
	    // total com a pesquisa
	    $data = $this->model_opcao->getAdapter()->fetchRow($sql);
	    $this->view->template_opcao = $data;
		
	}
	
	public function cssAction()
	{

	    // Define que o arquivo terá a codificação de saída no formato CSS
	    header("Content-type: text/css");
	    
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    
	    $sql = $this->model_opcao->getAdapter()->select()->from('template_opcao');
	     
	    // Parametros para busca
	    if($this->getRequest()->getParam('id')){
	        $sql->where('id = ?',$this->getRequest()->getParam('id'));
	    }
	    // fim Parametros para busca
	     
	    // total com a pesquisa
	    $data = $this->model_opcao->getAdapter()->fetchRow($sql);
	     
	    $css = '
    	    .cTopo {
        	    background-color: '.$data['topo_fatia'].';
    	    }
        	.cBgTopo {
        	    background-color: '.$data['topo_bg'].';
    	    }
        	.cMenu1 li a {
        	    color: '.$data['topo_fonte_menu1'].';
    	    }
        	.cMenu1 li a:hover {
        	    color: '.$data['topo_fonte_menu1_hover'].';
    	    }
        	.cTelefoneTopo {
        	    color: '.$data['topo_telefone'].';
    	    }
        	.cMenu2 {
        	    color: '.$data['topo_fonte_menu2'].';
    	    }
        	.cMenu2:hover {
        	    color: '.$data['topo_fonte_menu2_hover'].';
    	    }
        	.cFatiaBanner {
        	    background-color: '.$data['topo_bg_banner'].';
    	    }
        	.cbgPesquisa {
        	    background-color: '.$data['pesquisa_bg'].';
    	    }
        	.cbgPesquisa1Titulo {
        	    color: '.$data['pesquisa_titulo'].';
    	    }
        	.cBgBotaoPesquisa1 {
        	    background-color: '.$data['pesquisa_bt'].';
    	    }
        	.cBgBotaoPesquisa1Texto {
        	    color: '.$data['pesquisa_bt_texto'].';
    	    }
        	.cBgBotaoPesquisa1Texto:focus {
        	    color: '.$data['pesquisa_bt_texto'].';
        	    text-decoration:none;
    	    }
        	.cBgBotaoPesquisa1Texto:hover {
        	    color: '.$data['pesquisa_bt_texto'].';
        	    text-decoration:none;
    	    }
        	.cCorpoTextoImovel {
        	    color: '.$data['corpo_texto_imovel'].';
    	    }
        	.cCorpoTextoImovel:hover {
        	    color: '.$data['corpo_texto_imovel_hover'].';
        	    text-decoration:none;
    	    }
        	            
        	            
	    ';
	    
	    echo $css;
	    	    
	
	}
	
	public function cadastrarAcaoAction()
	{
	    $resposta = array();
	    $this->_helper->viewRenderer->setNoRender(true);
	    $this->_helper->layout()->disableLayout();
	    
	    
	    $params = $this->getRequest()->getPost();
	
	    $result = $this->model_opcao->save($params);
	
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
	        $data['descricao'] = "Cadastro de opção de layout";
	        $this->modelLog->save($data);
	    }else{
	        $resposta['situacao'] = "error";
	        $resposta['msg'] = "Erro ao Cadastrar!";
	    }
	
	    echo json_encode($resposta);
	}

}