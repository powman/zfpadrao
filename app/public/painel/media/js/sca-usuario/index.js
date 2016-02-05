// angular usuario

app.service(
    "$modelusuario",
    function( $http, $q, $loader ) {
        // Return
        return({
            getAll: getAll,
            remove: remove,
            ativar: ativar,
            desativar: desativar
        });
        // ---
        // PUBLIC METHODS.
        
        /**
         * Pega os dados todos remoto
         */
        function getAll(obj) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/index",
                data: $.param(obj)
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        /**
         * Remove um dado remoto
         */
        function remove( ids ) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/remover",
                data: $.param(ids)
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        /**
         * Ativa um dado remoto
         */
        function ativar( ids ) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/ativar",
                data: $.param(ids)
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        /**
         * Desativa um dado remoto
         */
        function desativar( ids ) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/desativar",
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
// controller usuario index
app.controller('sca-usuario_index', function Ctrl($scope,NgTableParams, $http, $notify,$loader,$element,$sessao,$modelusuario) {

	$scope.dados = [];
	var $_this = this;
	$_this.changePageSize = mudarQtdeDeListagem;
	$scope.selecionar = selecionar;
	$scope.checarTodos = checarTodos;
	$scope.pesquisar = pesquisar;
	$scope.removeFiltro = removeFiltro;
	$_this.del = deletar;
	
	loadDadosRemoto();
	
	/**
	 * Pega os dados remoto
	 */
	function loadDadosRemoto(){
		$_this.tableParams = new NgTableParams({}, {
	      getData: function(params) {
	        // ajax request
	    	var $data = params.url();
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
	
	/**
	 * Pesquisa no banco de dados
	 */
	function pesquisar(){
		if($scope.pesquisa){
			$_this.tableParams.filter({ valor: $scope.pesquisa });
		}else{
			loadDadosRemoto();
		}
	}
	/**
	 * funcao para remover o texto na pesquisa
	 */
	function removeFiltro(){
		$scope.pesquisa = '';
		loadDadosRemoto();
	}
	
	
	/**
	 * Aplica os dados remoto a view
	 */
    function aplicarDadosRemoto( dados ) {
        $scope.dados = dados;
    }
	
    /**
     * Muda a qtde de listagem por pagina
     */
	function mudarQtdeDeListagem(valor){
		this.tableParams.count(valor);
	}
	
	/**
     * Deletar um usuario
     */
	function deletar($event,$index){
		alertify.confirm("Deseja realmente excluir este usuário? Nome: "+$scope.dados[$index].nm_usuario+" - ID: "+$scope.dados[$index].id_usuario+"",function(status){
			if(status){
				$modelusuario.remove({id:$scope.dados[$index].id_usuario})
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
		});
	}
	/**
	 * com os selecionados
	 */
	function selecionar($param){
		var $aRemover = [];
		var $aAtivar = [];
		var $aDesativar = [];
		if($param == "ex"){
			alertify.confirm("Deseja realmente excluir os selecionados?",function(status){
				if(status){
					$filtro = $scope.dados.filter(function(obj) {
					  if(obj.selected === true || obj.selected === 'true')
						  $aRemover.push(obj.id_usuario);

					});
					
					$modelusuario.remove({id:$aRemover})
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
			});
		}else if($param == "at"){
			alertify.confirm("Deseja realmente ativar os selecionados?",function(status){
				if(status){
					$filtro = $scope.dados.filter(function(obj) {
					  if(obj.selected === true || obj.selected === 'true')
						  $aAtivar.push(obj.id_usuario);

					});
					
					$modelusuario.ativar({id:$aAtivar})
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
			});
	    }else if($param == "de"){
	    	alertify.confirm("Deseja realmente desativar os selecionados?",function(status){
	    		if(status){
	    			$filtro = $scope.dados.filter(function(obj) {
	  				  if(obj.selected === true || obj.selected === 'true')
	  					  $aDesativar.push(obj.id_usuario);

	  				});
	  				
	  				$modelusuario.desativar({id:$aDesativar})
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
	    	});
	    }else{
	    	$notify.open("Não foi selecionado nenhum item",2000,"error");
			var element = angular.element('[ng-model="selecionados"]');
			element.find("option")[0].selected = true;
			$scope.selecionados = "";
	    }
	}
	/**
	 * Funcao de checar todos da tabela
	 */
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