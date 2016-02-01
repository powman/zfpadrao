
//angular aba usuário
app.$register.service(
    "$modelabausuario",
    function( $http, $q, $loader ) {
        // Return
        return({
        	getById: getById,
        });
        // ---
        // PUBLIC METHODS.
        // ---
        // Pega os dados remoto por id
        function getById(obj) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/get-usuario",
                data: obj
            });
            return( request.then( handleSuccess, handleError ) );
        }

        // ---
        // PRIVATE METHODS.
        // ---
        // I transform the error response, unwrapping the application dta from
        // the API response payload.
        function handleError( response ) {
        	$loader.hide();
            // The API response from the server should be returned in a
            // nomralized format. However, if the request was not handled by the
            // server (or what not handles properly - ex. server error), then we
            // may have to normalize it on our end, as best we can.
            if (
                ! angular.isObject( response.data ) ||
                ! response.data.message
                ) {
                return( $q.reject( "An unknown error occurred." ) );
            }
            // Otherwise, use expected error message.
            return( $q.reject( response.data.message ) );
        }
        // I transform the successful response, unwrapping the application data
        // from the API response payload.
        function handleSuccess( response ) {
        	$loader.hide();
            return( response.data );
        }
    }
);

/*
 * Controller Aba Usuario
 */
app.register.controller('sca-usuario_aba-usuario', function Ctrl($scope,Scopes,$rootScope,Scopes,$notify,$location,$element,$uibModalStack,$modelabausuario) {
	Scopes.store('sca-usuario_aba-usuario', $scope);
	console.log("sai");
	$scope.btn = [];
	$scope.botaoAcao = [];
	$scope.btnAcao = btnAcao;
	$scope.setUsuario = setUsuario;
	$scope.modalSelect = modalSelect;
	$scope.loadById = loadById;
	$id = "";
	$scope.usuario = [];

	$scope.opa = function(){
		alert("opa");
	}
	// verifica se tem o id na url
	if($location.$$absUrl.indexOf('id') > -1){
		$id = $location.$$absUrl.match(/id\/[0-9]*/).toString().replace("id/",""); 
	}
	
	if($id)
		// pega os dados por id
		loadById({id: $id});
	
	/**
	 * Recebe os dados remoto por id
	 */
	function loadById($obj){
		var $data = $.param($obj);
    	return $modelabausuario.getById($data)
	    .then(
	        function( data ) {
	        	if(data.status == "sucesso" && data.dados){
	        		setUsuario(data.dados);
	        		// se o id passado na url for direfente do id do scope atualiza a pagina.
	        		if($id != data.dados.id_usuario){
	        			window.location.href= _baseUrl+_controller+"/form/id/"+data.dados.id_usuario;
	        		}
	        	}else{
	        		$notify.open(data.msg,3000,"error");
	        		$scope.usuario.id_usuario = $id;
	        		preencheCampos($scope.usuario);
	        	}
	        }
	    );
	}
	
	/**
	 * Grava os dados do usuario no scope
	 */
	function setUsuario( dados ) {
        $scope.usuario = dados;
        // coloca os valores nos input
        preencheCampos($scope.usuario);
    }
	
	/**
	 * Verfica o tipo de acao dos botoes
	 */
	function btnAcao($tipo){
		if($tipo == "limpar"){
			var $obj = $("#limpar");
			$('#form').each (function(){
				  this.reset();
			});
			window.location.href= _baseUrl+_controller+"/form";
		}else if($tipo == "alterar"){
			alert(JSON.stringify($("#form").serialize()));
		}
	}
	/**
	 * Recebe o clique do modal passando o atributo
	 */
	function modalSelect($obj){
		window.location.href= _baseUrl+_controller+"/form/id/"+$obj.id_usuario;
	}
	
	$scope.sai = function(){
		console.log("sai");
	}
	
	// Antes do Ajax
	$scope.$on('$includeContentRequested', function($obj) {
		$loader.show("Carregando...");
	});
	// Depois que carregou
	$scope.$on('$includeContentLoaded', function() {
		$loader.hide();
	});
    
});

