

//angular calendario
app.$register.service(
    "$model",
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
                url: _baseUrl+"sca-grupo/index",
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


app.register.controller('sca-grupo_modal', function Ctrl($scope,Scopes,NgTableParams, $http, $notify,$loader,$element,$sessao,$model) {
	Scopes.store('sca-grupo_modal', $scope);
	$scope.dados = [];
	var $_this = this;
	$scope.pesquisar = pesquisar;
	$scope.addSelecao = addSelecao;
	$scope.islocation = $parametros.islocation;

	loadDadosRemoto();

	
	/*
	 * Pega os dados remoto
	 */
	function loadDadosRemoto(){
		
		$scope.tableParams = new NgTableParams({}, {
	      getData: function(params) {
	    	console.log(params,"params");
	    	var $parametrosUrl = $.extend(params.url(),$parametros);
	        // ajax request to api
	    	var $data = $parametrosUrl;
	    	return $model.getAll($data)
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
			loadDadosRemoto();
		}
	}
	
	/*
	 * Aplica os dados remoto a view
	 */
    function aplicarDadosRemoto( dados ) {
        $scope.dados = dados;
    }
    
    function addSelecao($aGrupo){
    	if(!$scope.islocation){
	    	$(".modal").click();
	    	Scopes.get($parametros.returncontrole).dados.id_grupo = $aGrupo.id_grupo;
	    	Scopes.get($parametros.returncontrole).dados.nm_grupo = $aGrupo.nm_grupo;
    	}else{
    		window.location.href= _baseUrl+"sca-grupo/form/id/"+$idgrupo;
    	}
    }
    
});



