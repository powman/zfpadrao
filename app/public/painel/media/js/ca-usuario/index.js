// angular calendario

app.service(
    "$model",
    function( $http, $q, $loader ) {
        // Return
        return({
            add: add,
            getAll: getAll,
            remove: remove
        });
        // ---
        // PUBLIC METHODS.
        // ---
        // Adciona os dados remoto
        function add( obj ) {
        	$loader.show("Carregando...");
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/salvar",
                data: obj
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        // Pega os dados todos remoto
        function getAll(obj) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/index",
                data: obj
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        // Remove um dado remoto
        function remove( ids ) {
            var request = $http({
                method: "delete",
                url: _baseUrl+_controller+"/remover",
                params: ids
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
	this.changePageSize = mudarQtdeDeListagem;
	this.del = deletar;
	
	/*
	 * Pega os dados remoto
	 */
	this.tableParams = new NgTableParams({}, {
      getData: function(params) {
        // ajax request to api
    	var $data = $.param(params.url());
    	return $model.getAll($data)
	    .then(
	        function( data ) {
	        	if(data.status == "sucesso" && data.dados.res){
	        		aplicarDadosRemoto( data.dados.res );
	        		params.total(data.dados.total); // recal. page nav controls
	    	        return $scope.dados;
	        	}
	        }
	    );
      }
    });
	
	/*
	 * Pega os dados remoto
	 */
	function loadDadosRemoto() {
		$model.getAll()
		    .then(
		        function( data ) {
		        	if(data.status == "sucesso" && data.dados.res){
		        		aplicarDadosRemoto( data.dados.res );
		    	        return $scope.dados;
		        	}
		        }
		    )
		;
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
		if(confirm("Deseja realmente excluir? \n\n"+$scope.dados[$index].nome+" - ID: "+$scope.dados[$index].id+"")){
	    	
			$model.remove({id:$scope.dados[$index].id})
		    .then(
		        function( data ) {
		        	if(data.status == "sucesso"){
		        		loadDadosRemoto();
		        	}
		        }
		    );
	     }
	}
    
    $scope.removerSelecionados = function ($param){
	  var $aExcluir = [];
	  var $aKey = [];
	  
      if(confirm("Deseja realmente excluir os selecionados?")){
    	  angular.forEach($scope.dados, function($value, $key) {
    		  console.log($key);
    		  if($scope.dados[$key].selected === true){
    			  $aExcluir.push($scope.dados[$key].id);
    			  $aKey.push($key);
    		  }
          });
    	  if($aExcluir.length){
	    	  var data = $.param({ id: $aExcluir});
	    	  $loader.show("Carregando...");
	    	  $http.post( _baseUrl+_modulo+'/'+_controller+'/remover',data).success(function($data){
	  	    	if($data.situacao == "error"){
	  	    		$loader.hide();
	  	    		$notify.open($data.msg,2000,"error");
	  	    	}else if($data.situacao == "success"){
	  	    		$loader.hide();
	  	    		$notify.open($data.msg,2000,"success");
					//$scope.dados.splice($scope.flumps.indexOf($aExcluir),1);//remove a linha da view
					this.selecionados = "";
	  	    	}else{
	  	    		$loader.hide();
	  	    		$notify.open("Um erro inesperado aconteceu.",2000,"error");
	  	    	}
	  	    	
	  	      }).error(function() {
	  	    	$loader.hide();
	  	    	$notify.open("Um erro inesperado aconteceu.",2000,"error");
	  	      });
    	  }else{
    		  $notify.open("Não foi selecionado nenhum item para exclusão",2000,"error");
    		  var element = angular.element('[ng-model="selecionados"]');
    		  element.find("option")[0].selected = true;
    		  this.selecionados = "";
    	  }
    	 /* var $retorno = remover({id:[$scope.dados[$index].id]});
    	  console.log($retorno);*/
    	  /*if(){
    		  $scope.dados.splice($index,1);
    	  }*/
    	  
      }else{
    	  var element = angular.element('[ng-model="selecionados"]');
		  element.find("option")[0].selected = true;
		  this.selecionados = "";
      }
    };

 // check todos os produtos do carrinho
	$scope.checarAll = function($check){
		if($check){
			angular.forEach($scope.dados, function(value, key) {
				if(value.del === true)
					$scope.dados[key].selected = true;
			});
		}else{
			angular.forEach($scope.dados, function(value, key) {
				$scope.dados[key].selected = false;
			});
		}
	};
    
});



function remover(objeto){
	return $.ajax({
        url: _baseUrl+_controller+"/excluir",
        data: {
        	id: objeto.id
        },
        type: "post",
        dataType: "json",
        beforeSend: function() {
            
        },
        error:function(data){
            return false;
        },
        complete: function(data) {
            if(data.responseJSON.status == "sucesso"){
            	return true;
           } else {

           		return false;
           }
        }
    });
}