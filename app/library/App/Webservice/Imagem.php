<?php


class App_Webservice_Imagem {

    /**
     * Pega a Image por id
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
        $dados = $db->fetchRow($sql);
        
        $dados["arquivo_base64"] = base64_encode($dados["arquivo"]);
        $dados["arquivo"] = "";
        
        return $dados;
    }
    
    /**
     * Faz o upload da imagem
     *
     * @return array
     */
    public function saveImage($array)
    {
        $db = new Zend_Db_Adapter_Pdo_Mysql(array(
            'host'     => 'localhost',
            'username' => 'root',
            'password' => '',
            'dbname'   => 'painelpadrao_imagem'
        ));
        $form = array(
            "nm_avatar" => $array["file"]["name"],
            "tp_avatar" => $array["file"]["type"],
            "sz_avatar" => $array["file"]["size"],
            "arquivo" =>   file_get_contents($array["file"]["tmp_name"])
        );
        $result = $db->insert("imagem", $form);
        //$sql = "SELECT * FROM imagem WHERE id_avatar = ".$id;
        return $result;
    }
}