<?php
class App_Webservice_Imagem {

    /**
     * Returns list of all products in database
     *
     * @return array
     */
    public function getImageById($id)
    {
        $db = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'painelpadrao_imagem'
        ));
        $sql = "SELECT * FROM imagem WHERE id_avatar = ".$id;
        return $db->fetchRow($sql);
    }
}