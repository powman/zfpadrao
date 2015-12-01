<?php

class Painel_Model_CgModulo extends App_Model_Default
{
    
    public $id = null;
    protected $_name = 'modulo';
    protected $primarykey = "id";
    
    public function listarTodos($condicao = array(), $limit = null, $offset = null, &$msg = '')
    {
        // SQL para buscar os registros
        $sql = $this->select()
        ->from(array('m' => $this->_name),
            array(
                'id',
                'nome',
                'icone',
                'ordem',
                'status'
            ));
        if (isset($condicao["nome"]) && $condicao["nome"]) {
            $sql->where('m.nome LIKE ? ', "%{$condicao["nome"]}%");
        }
    
        // SQL para buscar a quantidade de páginas existentes
        $sqlCount = $this->getAdapter()->query($sql);
        
        $sql->order('m.id')->limit($limit, $offset);
    
        $return['res']   = $this->fetchAll($sql)->toArray();
        $return['total'] = $sqlCount->rowCount();
    
        return $return;
    }
}