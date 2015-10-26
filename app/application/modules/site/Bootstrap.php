<?php
/**
 *
 * @author Steve Rhoades
 * @see http://www.stephenrhoades.com
 */
class Site_Bootstrap extends Zend_Application_Module_Bootstrap
{
	
    protected function _initAutoload()
    {
      $autoloader = new Zend_Application_Module_Autoloader(array(
           'namespace' => 'Site_',
           'basePath' => dirname(__FILE__)
      ));
      return $autoloader;
    }
}