// angular calendario

app.service(
    "$model",
    function( $http, $q, $loader ) {
        // Return
        return({
            getAll: getAll,
            remove: remove
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
        
        // Remove um dado remoto
        function remove( ids ) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/remover",
                data: $.param(ids)
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

app.controller('ca-usuario_index', function Ctrl($scope,NgTableParams, $http, $notify,$loader,$element,$sessao,$model) {

	$scope.dados = [];
	var $_this = this;
	$_this.changePageSize = mudarQtdeDeListagem;
	$scope.removerSelecionados = removerSelecionados;
	$scope.checarTodos = checarTodos;
	$scope.pesquisar = pesquisar;
	$_this.del = deletar;
	
	loadDadosRemoto();
	
	/*
	 * Pega os dados remoto
	 */
	function loadDadosRemoto(){
		$_this.tableParams = new NgTableParams({}, {
	      getData: function(params) {
	        // ajax request to api
	    	var $data = params.url();
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
			$_this.tableParams.filter({ valor: $scope.pesquisa });
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
	
    /*
     * Muda a qtde de listagem por pagina
     */
	function mudarQtdeDeListagem(valor){
		this.tableParams.count(valor);
	}
	
	/*
     * Muda a qtde de listagem por pagina
     */
	function deletar($event,$index){
		if(confirm("Deseja realmente excluir? \n\n"+$scope.dados[$index].nm_usuario+" - ID: "+$scope.dados[$index].id_usuario+"")){
	    	
			$model.remove({id:$scope.dados[$index].id_usuario})
		    .then(
		        function( data ) {
		        	if(data.status == "sucesso"){
		        		loadDadosRemoto();
		        	}else{
		        		$notify.open(data.msg,3000,"error");
		        	}
		        }
		    );
	     }
	}
	
	function removerSelecionados($param){
		var $aRemover = [];
		if($param == "ex"){
			if(confirm("Deseja realmente excluir os selecionados?")){
				$filtro = $scope.dados.filter(function(obj) {
				  if(obj.selected === true || obj.selected === 'true')
					  $aRemover.push(obj.id_usuario);

				});
				
				$model.remove({id:$aRemover})
			    .then(
			        function( data ) {
			        	if(data.status == "sucesso"){
			        		loadDadosRemoto();
			        		var element = angular.element('[ng-model="selecionados"]');
			      		    element.find("option")[0].selected = true;
			      		    $scope.selecionados = "";
			        	}else{
			        		var element = angular.element('[ng-model="selecionados"]');
			      		    element.find("option")[0].selected = true;
			      		    $scope.selecionados = "";
			        		$notify.open(data.msg,3000,"error");
			        	}
			        }
			    );
			}else{
				var element = angular.element('[ng-model="selecionados"]');
				element.find("option")[0].selected = true;
				$scope.selecionados = "";
			}
		}else{
  		  $notify.open("Não foi selecionado nenhum item para exclusão",2000,"error");
		  var element = angular.element('[ng-model="selecionados"]');
		  element.find("option")[0].selected = true;
		  $scope.selecionados = "";
	    }
	}
	
	function checarTodos($check){
		if($check){
			angular.forEach($scope.dados, function(value, key) {
				if(value.del === 'true'){
					$scope.dados[key].selected = "true";
				}
			});
		}else{
			angular.forEach($scope.dados, function(value, key) {
				$scope.dados[key].selected = false;
			});
		}
	}
    
});