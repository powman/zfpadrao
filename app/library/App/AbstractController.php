<?php

class App_AbstractController extends Zend_Controller_Action_Helper_Abstract
{
    
    /*
     * 
     * $aemails = array('paulo@pixelgo.com.br');
		$aAnexos = array('http://mma12.com.br/site/img/logo.png');
		$aMsg = array();
		$aMsg[] = array(
		        'tipo' => 'teste',
		        'msg' => 'teste 2',
		);
		echo $this->enviaEmail($aemails,$aMsg,null,'Assunto',$aAnexos);
     */
    public function enviaEmail($emails=null, $msg=null, $replyTo=null, $assunto=null, $anexos=null){
        //Initialize needed variables
        $config = new Zend_Config_Ini(realpath(APPLICATION_PATH . '/../') . '/application/configs/constants.ini',
                'constants');
        
        //SMTP server configuration
        $smtpConf = array(
                'auth' => 'login',
                'port' => $config->smtp->port,
                'username' => $config->smtp->user,
                'password' => $config->smtp->senha
        );
        
        if($config->smtp->ssl)
            $smtpConf['ssl'] = $config->smtp->ssl;
        
        //$transport = new Zend_Mail_Transport_Smtp($config->smtp->host, $smtpConf);
        $transport = new Zend_Mail_Transport_Smtp('localhost');
        
        // monta Msg
        $messg  = "";
    
        $aMsg = count($msg);
        for($i=0;$i<$aMsg;$i++){
            $messg .= "<b>".$msg[$i]['tipo']." </b> <span>".$msg[$i]['msg']."</span><br/><br/>";
        }
    
        $content = file_get_contents(realpath(APPLICATION_PATH . '/../').'/public/inc/email/padrao.php');
        $content = str_replace('{TEXTO}', $messg, $content);
        $content = str_replace('{LINKSITE}', $config->config->site_cliente, $content);
        $content = str_replace('{URLLOGO}', $config->config->url_logo, $content);
        
        
        $mail = new Zend_Mail('utf-8');
        
        $mail->setFrom($config->smtp->from, $config->smtp->from_name);
        
        if($emails){
            foreach($emails as $each_recipient){
                $mail->addTo($each_recipient);
            }
        }
        $mail->setSubject($assunto);
        $mail->setBodyHtml($content);
        
        if($anexos){
            foreach($anexos as $value){
                $informacao = pathinfo($value);
                $image_mime = image_type_to_mime_type(exif_imagetype($value));
                $attachment = $mail->createAttachment(file_get_contents($value));
                $attachment->type = $image_mime;
                $attachment->disposition = Zend_Mime::DISPOSITION_ATTACHMENT;
                $attachment->filename = $informacao['basename'];
            }
        }
        
        //Enviar
        $sent = true;
        try {
            $mail->send($transport);
        }
        catch (Exception $e) {
            $sent = false;
        }
        
        //Return boolean indicando ok ou nao
        return $sent;
    }
    
    public function byte_format($bytes, $precision = 2)
    {
        $bytes = $bytes * 1000000;
        // human readable format -- powers of 1000
        //
        $unit = array('B','KB','MB','GB','TB','PB','EB');
    
        return @round(
                $bytes / pow(1000, ($i = floor(log($bytes, 1000)))), $precision
        ).' '.$unit[$i];
    }
    
    // Função de porcentagem: N é X% de N
    public function porcentagem_nx ( $parcial, $total ) {
        return round(( $parcial * 100 ) / $total,2);
    }
    
    public function formatar_data_timestamp($str) {
        $date = date('d/m/Y H:i:s', $str);
    
        return $date;
    }
    
    public function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node )
            $out[$index] = ( is_object ( $node ) ) ? $this->xml2array ( $node ) : $node;
    
        return $out;
    }
    
    public function verificaImagem($arquivo){
        $formatoImg = $this->formatoFile($arquivo);
        switch ($formatoImg){
            case'PNG':
                return true;
                break;
            case'png':
                return true;
                break;
            case'jpg':
                return true;
                break;
            case'JPG':
                return true;
                break;
            case'JPEG':
                return true;
                break;
            case'jpeg':
                return true;
                break;
            case'gif':
                return true;
                break;
            case'GIF':
                return true;
                break;
            default:
                return false;
                break;
        }
    }
    
    public function retornaMesExtensoArray(){
        $meses = array();
        $meses[] = "Janeiro";
        $meses[] = "Fevereiro";
        $meses[] = "Março";
        $meses[] = "Abril";
        $meses[] = "Maio";
        $meses[] = "Junho";
        $meses[] = "Julho";
        $meses[] = "Agosto";
        $meses[] = "Setembro";
        $meses[] = "Outubro";
        $meses[] = "Novembro";
        $meses[] = "Dezembro";
    
        return $meses;
    }
    
    public function lerRss(){
        // caminho do feed do meu blog
        $feed = 'http://g1.globo.com/dynamo/economia/rss2.xml';
        // leitura do feed
        $rss = simplexml_load_file($feed);
        // limite de itens
        $limit = 5;
        // contador
        $count = 0;
    
        // imprime os itens do feed
        if($rss)
        {
            foreach ( $rss->channel->item as $item )
            {
                // formata e imprime uma string
                printf('<li style="height: 50px;"><a class="NoticiasIndex" href="%s" target="_blank" title="%s" >%s</a><br /></li>', $item->link, $item->title, $item->title);
                // incrementamos a variÃ¡vel $count
                $count++;
                // caso nosso contador seja igual ao limite paramos a iteraÃ§Ã£o
                if($count == $limit) break;
            }
        }
        else
        {
            echo 'Não foi possivel ler.';
        }
    
    }
    
    /********************
     *@file - path to file
    */
    public function force_download($file)
    {
        if ((isset($file))&&(file_exists($file))) {
            header("Content-length: ".filesize($file));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $file . '"');
            readfile("$file");
        } else {
            echo "No file selected";
        }
    }
    
    public function gerarUrl($atributos){
        $a_parametros = explode("/", $atributos);
    
        $aResult = array();
    
        for ($i = 0;  $i <= count($a_parametros); $i = $i + 2) {
            if (($i % 2) == 0) {
                $aResult[$a_parametros[$i]] = $a_parametros[$i+1];
            }
        }
    
        array_pop($aResult);
    
        return $aResult;
    }
    
    /**
     * Coloca uma mascara no CEP
     * ex '74023045' => '74.023-045'
     *
     * @param string $cep
     * @return string
     */
    public function KM_formatCEP($cep) {
        $cep = preg_replace('/[^0-9]/', '', $cep);
        if ($cep)
            $cep = substr($cep, 0, 2) . '.' . substr($cep, 2, 3) . '-' . substr($cep, 5);
        return $cep;
    }
    
    
    /**
     * Função que formata um cpf/cnpj
     *
     * @param string $cpfcnpj
     * @param bool $addSep se é para adicionar separadores para os campos
     * @return string
     */
    public function KM_formatCpfCnpj($cpfcnpj = null, $addSep = true) {
        $cpfcnpj = preg_replace('/[^0-9]/', '', $cpfcnpj);
        if (!$cpfcnpj) {
            return '';
        }
    
        if (KM_checkCpf($cpfcnpj)) {
            /*se a string for um cpf*/
            $isCpf = true;
        } else if (KM_checkCnpj($cpfcnpj)) {
            /*se a string for um cpnj*/
            $isCpf = false;
        } else {
            $aux = substr($cpfcnpj, strlen($cpfcnpj) - 11);
            if (strlen($cpfcnpj) == 11 || KM_checkCpf($aux)) {
                /*se os ultimos 11 caracteres forem um cpf*/
                $isCpf = true;
                $cpfcnpj = $aux;
            } else {
                return $cpfcnpj;
            }
        }
    
        if (!$addSep) {
            /*retorna só os numeros caso não seja para adicionar os separadores*/
            return $cpfcnpj;
        }
    
        /*adiciona os separadores de acordo com o tipo*/
        if ($isCpf) {
            return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpfcnpj);
        } else {
            return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/', '$1.$2.$3/$4-$5', $cpfcnpj);
        }
    }
    
    
    /**
     * Escreve um numero por extenso
     *
     * @param integer $iNumero
     * @return strinc
     */
    public function KM_extensoNumero($iNumero) {
        $aUnidade = array(
                '',
                'um',
                'dois',
                'três',
                'quatro',
                'cinco',
                'seis',
                'sete',
                'oito',
                'nove',
                'dez',
                'onze',
                'doze',
                'treze',
                'quatorze',
                'quinze',
                'dezesseis',
                'dezessete',
                'dezoito',
        'dezenove');
    	$aDezena = array('', '', 'vinte', 'trinta', 'quarenta', 'cinqüenta', 'sessenta', 'setenta', 'oitenta', 'noventa');
    	
    	$aCentena = array(
    					'cem', 
    					'cento', 
    					'duzentos', 
    					'trezentos', 
    					'quatrocentos', 
    					'quinhentos', 
    					'seiscentos', 
    					'setecentos', 
    					'oitocentos', 
    					'novecentos');
    	$singular = array('', 'mil', 'milhão', 'bilhão', 'trilhão', 'quatrilhão');
    	$plural = array('', 'mil', 'milhões', 'bilhões', 'trilhões', 'quatrilhões');
    	
    	$iMilhar = intval($iNumero / 1000);
    	$iCentena = intval($iNumero % 1000 / 100);
    	$iDezena = intval($iNumero % 1000 % 100 / 10);
    	$iUnidade = $iNumero % 1000 % 100 % 10;
    	$result = '';
    	
    	if ($iMilhar) {
    		$aCentenaMilhar = array();
    		
    		/*pega o nome da função atual para chamar recursivamente*/
    		$func = __FUNCTION__;
    		
    		/*inverte a string. Ex: 1234 => 4321*/
    		$iMilhar = strrev($iMilhar);
    		
    		/*separa a str em pedaços. Ex: array('432', '1')*/
    		$aCentenaMilhar = str_split($iMilhar, 3);
    		
    		for ($i = count($aCentenaMilhar) - 1; $i >= 0; $i--) {
    			$j = $i + 1;
    			$valor = strrev($aCentenaMilhar[$i]) * 1;
    			/*ignora valores como 000*/
    			if ($valor) {
    				$result .= ($result ? ' ' : '');
    				$result .= $func($valor);
    				$result .= ' ' . ($valor > 1 ? $plural[$j] : $singular[$j]);
    			}
    		}
    	}
    	
    	if ($iCentena) {
    		if ($iCentena == 1 && $iDezena == 0 && $iUnidade == 0) {
    			if ($iMilhar)
    				$result .= ' e ';
    			$result .= 'cem';
    		} else {
    			if ($iMilhar)
    				$result .= ' ';
    			$result .= $aCentena[$iCentena];
    		}
    	}
    	
    	if ($iDezena) {
    		if ($iCentena || $iMilhar)
    			$result .= ' e ';
    		if ($iDezena < 2) {
    			$result .= $aUnidade[$iDezena * 10 + $iUnidade];
    			return $result;
    		} else
    			$result .= $aDezena[$iDezena];
    	}
    	
    	if ($iUnidade) {
    		if ($iCentena || $iMilhar || $iDezena)
    			$result .= ' e ';
    		$result .= $aUnidade[$iUnidade];
    	}
    	return $result;
    }
    
    
    /**
     * Função que faz abreviação de nomes de pessoas
     * Ex:
     * <code>
     * echo KM_abreviarNome('joão pedro da silva dos santos');
     * //Resultado: "João P. S. Santos"
     * </code>
     *
     * @param string $sNome
     * @return string
     */
    public function KM_abreviarNome($sNome) {
        $aNome = explode(' ', $sNome);
        $j = count($aNome);
        for ($i = 1; $i < $j - 1; $i++) {
            if (strlen($aNome[$i]) <= 3) {
                unset($aNome[$i]);
            } else {
                $aNome[$i] = substr($aNome[$i], 0, 1) . '.';
            }
        }
        return ucwords(strtolower(join(' ', $aNome)));
    }
    
    /**
     * Converte data no formato unix timestamp para o formato intelegivel d/m/Y
     *
     * @param timestamp $date
     * @return string
     */
    public function datetostr($date = 'mktime') {
        if ($date == 'mktime') {
            $date = time();
        }
        if (!$date)
            return '';
        if (!is_numeric($date)) {
            $msg = 'Erro na função: ' . __FUNCTION__ . "\n";
            $msg .= 'O parâmetro $date enviado(' . $date . ') não corresponde a um timestamp válido.';
            throw new Exception($msg);
        }
        return date('d/m/Y', $date);
    }
    
    public function arredonda($numero,$numCasasDecimais=2) {
        return (round($numero*pow(10,$numCasasDecimais)))/pow(10,$numCasasDecimais);
    }
    
    public function encodeObj(&$obj) {
        foreach ($obj as &$attr) {
            if (is_object($attr)) {
                $this->encodeObj($attr);
            } elseif (is_array($attr)) {
                $this->encodeArray($attr);
            } else {
                $attr = utf8_encode($attr);
            }
        }
        return $obj;
    }
    
    public function encodeArray(&$array) {
    
        foreach ($array as &$elem) {
            if (is_array($elem)) {
                $this->encodeArray($elem);
            } elseif (is_object($elem)) {
                $this->encodeObj($elem);
            } else {
                $elem = utf8_encode($elem);
            }
        }
        return $array;
    }
    
    public function encode(&$var) {
        if (is_array($var)) {
            $this->encodeArray($var);
        } else if (is_object($var)) {
            $this->encodeObj($var);
        } else {
            $var = utf8_encode($var);
        }
        return $var;
    }
    
    //Decode
    
    public function decodeObj(&$obj) {
        foreach ($obj as &$attr) {
            if (is_object($attr)) {
                $this->decodeObj($attr);
            } elseif (is_array($attr)) {
                $this->decodeArray($attr);
            } else {
                $attr = utf8_decode($attr);
            }
        }
        return $obj;
    }
    
    public function decodeArray(&$array) {
    
        foreach ($array as &$elem) {
            if (is_array($elem)) {
                $this->decodeArray($elem);
            } elseif (is_object($elem)) {
                $this->decodeObj($elem);
            } else {
                $elem = utf8_decode($elem);
            }
        }
        return $array;
    }
    
    public function decode(&$var) {
        if (is_array($var)) {
            $this->decodeArray($var);
        } else if (is_object($var)) {
            $this->decodeObj($var);
        } else {
            $var = utf8_decode($var);
        }
        return $var;
    }
    
    public function regraDeTres($num, $total) {
    
        $resposta = ($num * 100) / $total;
        return number_format($resposta, 1);
    }
    
    ##################################
    ## RETORNA O FORMATO DO ARQUIVO ##
    ##################################
    
    public function formatoFile($file) {
    
        //list($lixo,$formato) = explode(".",$file);
        $lista = explode(".", $file);
        $formato = $lista[count($lista) - 1];
    
    
        return strtolower($formato);
    }
    
    ##################################
    ## RETORNA O FORMATO DO ARQUIVO ##
    ##################################
    
    public function nameFile($file) {
    
    list($lixo, $formato) = explode(".", $file);
    
    
    
    return $lixo;
    }
    
    public function moeda($valor) {
    
        if (($valor == 0) or ($valor == "")) {
    
            return "";
        } else {
    
            $valor = number_format($valor, "2", ",", ".");
    
            return "R$ " . $valor;
        }
    }
    
    public function urlName($str) {
    
        $str = strtolower(utf8_decode($str)); $i=1;
        $str = strtr($str, utf8_decode('àáâãäåæçèéêëìíîïñòóôõöøùúûýýÿ'), 'aaaaaaaceeeeiiiinoooooouuuyyy');
        $str = preg_replace("/([^a-z0-9])/",'-',utf8_encode($str));
        while($i>0) $str = str_replace('--','-',$str,$i);
        if (substr($str, -1) == '-') $str = substr($str, 0, -1);
        return $str;
    }
    
    public function uploadArq($img_tmp, $img_name) {
    
        if (move_uploaded_file($img_tmp, $img_name)) {
    
            return true;
        } else {
    
            return false;
        }
    }
    
    ######################################
    ## RETORNA A URL DA IMAGEM DO VIDEO ##
    ######################################
    //http://www.youtube.com/v/qYAqtth4cNE&hl=en
    //http://i.ytimg.com/vi/qYAqtth4cNE/default.jpg
    
    public function urlImgVideo($string) {
    
        //se tiver cadastrando o <object
    
        if (substr($string, 0, 7) == "<object") {
    
            $arrayString = explode('"', $string);
    
            $string = substr($arrayString[7], 0, -1);
        } elseif (stristr($string, 'watch?v=')) {
    
            $arrayString = explode("watch?v=", $string);
    
            $string = "http://www.youtube.com/v/" . $arrayString[count($arrayString) - 1] . "&hl=en";
        }
    
    
    
        $aux = explode("/", $string);
    
        $aux2 = $aux[count($aux) - 1];
    
        $aux3 = explode("&", $aux2);
    
    
    
        $string = "http://i.ytimg.com/vi/" . $aux3[0] . "/default.jpg";
    
        return $string;
    }
    
    #######################
    ## LIMITA CARACTERES ##
    #######################
    
    public function limitaCarac($string, $maximo) {
    
        if (strlen($string) > $maximo) {
    
            $texto = substr($string, 0, $maximo) . "...";
        } else {
    
            $texto = $string;
        }
    
        return $texto;
    }
    
    public function dataHora($dataHora) {
    
        list($data, $hora) = explode(" ", $dataHora);
    
    
    
        $A = explode("-", $data);
    
        $V_data = $A[2] . "/" . $A[1] . "/" . $A[0];
    
    
    
        $dt["data"] = $V_data;
    
        $dt["hora"] = $hora;
    
    
    
        return $dt;
    }
    
    ###################
    ## CONVERTE DATA ##
    ###################
    
    public function converteData($data) {
    
        if (strstr($data, "/")) {
    
            $A = explode("/", $data);
    
            $V_data = $A[2] . "-" . $A[1] . "-" . $A[0];
        } else {
    
            $A = explode("-", $data);
    
            $V_data = $A[2] . "/" . $A[1] . "/" . $A[0];
        }
    
        return $V_data;
    }
    
    #######################
    ## DELETA UM ARQUIVO ##
    #######################
    
    public function delFile($file) {
    
        if (file_exists($file)) {
    
            $result = unlink($file);
        } else {
    
            $result = true;
        }
    
        return $result;
    }
    
    ##################
    ## DELETA PASTA ##
    ##################
    
    public function delDir($diretorio) {
    
        @chmod($diretorio, 0777);
    
        $dh = opendir(($dir = $diretorio));
    
        while (false !== ($filename = readdir($dh))) {
    
            if (is_file("$dir$filename") && !($filename == '.' || $filename == '..')) {
    
                @chmod($filename, 0777);
    
                unlink("$dir$filename");
            }
        }
    
        if (!@rmdir("$diretorio")) {
    
            print "FALHA AO DELETAR!";
        }
    }
    
    
    #######################
    ## CRIA UM DIRETÓRIO ##
    #######################
    
    public function criaDir($dir) {
    
        //se o diretorio não existir
    
        if (!file_exists($dir)) {
    
            $result = mkdir($dir, 0777);
    
            chmod($dir, 0777);
        } else {
    
            $result = true;
        }
    
    
    
        return $result;
    }
    
    public function cadUsuarioNewsletterGeral($form) {
    
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, "http://www.pixelgo.com.br/ctrl.php?acao=emailNaLista");
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_POST, true);
    
    
        foreach ($form as $param => $value) {
            $parametros .= "&$param=$value";
        }
    
        $parametros = substr($parametros, 1);
    
        if ($form->ip != "") {
            $parametros . "&remoteAddr=" . $form->ip;
        }
    
        curl_setopt($curl, CURLOPT_POSTFIELDS, $parametros);
        //echo $form->ip;
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_TIMEOUT, 15);
    
        $retorno = curl_exec($curl);
    
        curl_close($curl);
        if ($retorno == "sucess")
            return true;
        else if ($retorno == "error" || $retorno == "")
            return false;
    }
    
    
}