<?php

class LogsController extends Zend_Controller_Action
{
    
    public function init(){
        $this->model = new Painel_Model_Logs();
        $this->view->cssHelper = Painel_Plugin_CssHelper::CssHelper();
        $this->view->jsHelper = Painel_Plugin_JavascriptHelper::JsHelper();
        $this->view->mostra_head_footer = true;
        $this->uteis = new App_AbstractController();
        
        if ( !Zend_Auth::getInstance()->hasIdentity() ) {
            return $this->_helper->redirector->goToRoute( array('controller' => 'ca-auth'), null, true);
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
        
        $sql = $this->model->getAdapter()->select()->from('logs');
        
        // Parametros para busca
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario_id = ?',$this->getRequest()->getParam('pesquisa'));
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('modulo LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('controller LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('metodo LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('ip LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('id = ?',$this->getRequest()->getParam('pesquisa'));
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('usuario_nome LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
        
        if($this->getRequest()->getParam('pesquisa'))
            $sql->orWhere('descricao LIKE ?',"%".$this->getRequest()->getParam('pesquisa')."%");
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
            $values['rows'][$i]['modulo'] = $logs[$i]['modulo'];
            $values['rows'][$i]['ctrl'] = $logs[$i]['controller'];
            $values['rows'][$i]['metodo'] = $logs[$i]['metodo'];
            $values['rows'][$i]['ip'] = $logs[$i]['ip'];
            $values['rows'][$i]['data'] = $this->uteis->formatar_data_timestamp($logs[$i]['data']);
            $values['rows'][$i]['usuario_id'] = $logs[$i]['usuario_id'];
            $values['rows'][$i]['usuario_nome'] = $logs[$i]['usuario_nome'];
            $values['rows'][$i]['descricao'] = $logs[$i]['descricao'];
        }
        
        echo json_encode($values);
	}

}