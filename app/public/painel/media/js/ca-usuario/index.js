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
    				}
		        });
    			params.total(response.data.total); // recal. page nav controls
    	        return $scope.dados;
    		  }, function errorCallback(response) {
    			  $loader.hide();
    		  });
      }
    });
	
	$scope.checkboxes = { 'checked': false, items: {} };
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

	// watch for check all checkbox
    $scope.$watch(function() {
      return $scope.checkboxes.checked;
    }, function(value) {
      angular.forEach($scope.dados, function(item) {
    	  $scope.checkboxes.items[item.id] = value;
      });
    });
    
    // watch for data checkboxes
   $scope.$watch(function() {
      return $scope.checkboxes.items;
    }, function(values) {
      var checked = 0, unchecked = 0,
          total = $scope.dados.length;
      angular.forEach($scope.dados, function(item) {
        checked   +=  ($scope.checkboxes.items[item.id]) || 0;
        unchecked += (!$scope.checkboxes.items[item.id]) || 0;
      });
      // grayed checkbox
      angular.element($element[0].getElementsByClassName("select-all")).prop("indeterminate", (checked != 0 && unchecked != 0));
    }, true);
    
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