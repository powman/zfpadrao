<?php
class Zend_View_Helper_NenhumArquivoEncontrado extends Zend_View_Helper_Abstract
{
    public function nenhumArquivoEncontrado()
	{
		$html  = '<div class="alert alert-info" role="alert" style="margin-top: 10px;">';
		$html .= '	<strong>Oops!</strong> Nenhum arquivo encontrado';
		$html .= '</div>';
		
		return $html;
		
	}
}