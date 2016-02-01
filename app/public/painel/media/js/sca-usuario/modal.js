

//angular calendario
app.$register.service(
    "$modelusuario",
    function( $http, $q, $loader ) {
        // Return
        return({
            getAll: getAll
        });
        // ---
        // PUBLIC METHODS.
        
        // Pega os dados todos remoto
        function getAll(obj) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/index",
                data: $.param(obj)
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


app.register.controller('sca-usuario_modal', function Ctrl($scope,NgTableParams, $http, $notify,$loader,$element,$sessao,$modelusuario) {

	$scope.dados = [];
	$scope.pesquisar = pesquisar;
	$scope.addSelecao = addSelecao;
	$scope.islocation = $parametros.islocation;
	
	loadDadosRemotoUsuario();
	/*
	 * Pega os dados remoto
	 */
	function loadDadosRemotoUsuario(){
		
		$scope.tableParams = new NgTableParams({}, {
			
	      getData: function(params) {
	    	  console.log(params,"params");
	    	var $parametrosUrl = $.extend(params.url(),$parametros);
	        // ajax request to api
	    	var $data = $parametrosUrl;
	    	return $modelusuario.getAll($data)
		    .then(
		        function( data ) {
		        	
		        	if(data.status == "sucesso" && data.dados.res){
		        		aplicarDadosRemoto( data.dados.res );
		        		params.total(data.dados.total); // recal. page nav controls
		    	        return $scope.dados;
		        	}else{
		        		$notify.open(data.msg,3000,"error");
		        	}
		        }
		    );
	      }
	    });
		
	}

	
	/*
	 * Pesquisa no banco de dados
	 */
	function pesquisar(){
		if($scope.pesquisa){
			$scope.tableParams.filter({ valor: $scope.pesquisa });
		}else{
			loadDadosRemotoUsuario();
		}
	}
	
	/*
	 * Aplica os dados remoto a view
	 */
    function aplicarDadosRemoto( dados ) {
        $scope.dados = dados;
    }
    
    function addSelecao($dados,$islocation){
    	if(!$scope.islocation){
	    	var $chaves = Object.keys($dados);
	    	for(var i = 0 in $chaves){
	    		$('input[name='+$chaves[i]+']').val($dados[$chaves[i]]);
	    	}
	    	$(".modal").click();
    	}else{
    		window.location.href= _baseUrl+_controller+"/form/id/"+$islocation;
    	}
    }
    
});

