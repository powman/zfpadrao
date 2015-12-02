// angular calendario

app.controller('ca-usuario_index', ['$scope','NgTableParams','$http','$notify','$loader','$element','$sessao', function($scope,NgTableParams, $http, $notify,$loader,$element,$sessao) {

	$scope.dados = [];
	// pega as sessao
	$sessao.getSessions().success(function(data, status){
        $scope.sessao = data.dados;
    });
	this.tableParams = new NgTableParams({}, {
      getData: function(params) {
        // ajax request to api
    	var $data = $.param(params.url());
    	$loader.show("Carregando...");
    	return $http({
    		  method: 'POST',
    		  url: _baseUrl+'/painel/ca-usuario',
    		  data: $data,
    		}).then(function successCallback(response) {
    			$loader.hide();
    			$scope.dados = response.data.res;
    			angular.forEach($scope.dados, function(value, key) {
    				$scope.dados[key].del = true;
    				if($scope.dados[key].id == $scope.sessao.id){
    					$scope.dados[key].del = false;
    					$scope.dados[key].selected = false;
    				}
		        });
    			params.total(response.data.total); // recal. page nav controls
    	        return $scope.dados;
    		  }, function errorCallback(response) {
    			  $loader.hide();
    		  });
      }
    });
	
	
	this.changePageSize = function (newSize){
      this.tableParams.count(newSize);
    };
	
	this.del = function ($event,$index){
		console.log($scope.variableName);
      if(confirm("Deseja realmente excluir? \n\n"+$scope.dados[$index].nome+" - ID: "+$scope.dados[$index].id+"")){
    	  var $retorno = remover({id:[$scope.dados[$index].id]});
    	  console.log($retorno);
    	  /*if(){
    		  $scope.dados.splice($index,1);
    	  }*/
    	  
      }
    };
    
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
    
}]);

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