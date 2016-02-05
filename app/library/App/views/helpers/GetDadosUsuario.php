<?php 
class Zend_View_Helper_GetDadosUsuario extends Zend_View_Helper_Abstract
{
    /**
     * Helper para pegar as imagens do webservice
     *
     */
    public function GetDadosUsuario(){
        $auth = Zend_Auth::getInstance();
        ;
        $db = Zend_Db_Table::getDefaultAdapter();
		$chAction     = strtolower(Zend_Controller_Front::getInstance()->getRequest()->getActionName());
		$chController = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		
        $sql  = 'select ';
		$sql .= '	u.*, ';
		$sql .= '	a.nm_avatar, ';
		$sql .= '	a.tp_avatar, ';
		$sql .= '	a.sz_avatar, ';
		$sql .= '	a.arquivo ';
		$sql .= 'from ';
		$sql .= '	sca_usuario u ';
		$sql .= '	left join sgg_avatar a on a.id_avatar = u.id_avatar ';
		$sql .= 'where u.st_usuario = 1 ';
		$sql .= 'and u.id_usuario = '.$auth->getIdentity()->id_usuario;
		
		$result = $db->fetchRow($sql);
		if($result){
		    if($result['arquivo'])
		        $result['arquivo'] = "data:".$result['tp_avatar'].";base64,".base64_encode($result['arquivo']);
		}
		
		return $result;
        
    }
}