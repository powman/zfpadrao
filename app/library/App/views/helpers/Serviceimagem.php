<?php 
class Zend_View_Helper_Serviceimagem extends Zend_View_Helper_Abstract
{
    /**
     * Helper para pegar as imagens do webservice
     *
     */
    public function Serviceimagem($id){
        // initialize SOAP client
        $options = array(
            'location' => 'http://painel.local/webservice/index',
            'uri'      => 'http://painel.local/webservice/index'
        );
         
        try {
            $client = new Zend_Soap_Client(null, $options);
            return $client->getImageById($id);
        } catch (SoapFault $s) {
            die('ERROR: [' . $s->faultcode . '] ' . $s->faultstring);
        } catch (Exception $e) {
            die('ERROR: ' . $e->getMessage());
        }
        
    }
}