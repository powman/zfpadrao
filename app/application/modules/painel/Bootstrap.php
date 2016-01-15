<?php
/**
 *
 * @author Paulo Henrique
 * @see http://www.pauloph.com.br
 */
class Painel_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
    protected function _initAutoload()
    {
      $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Painel_',
           'basePath' => dirname(__FILE__)
      ));
      return $autoloader;
     }
		
	public function _initPluginBrokers()
	{
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Painel_Plugin_CssHelper());
		$front->registerPlugin(new Painel_Plugin_JavascriptHelper());
		$front->registerPlugin(new App_Plugin_Acl());
	}
}