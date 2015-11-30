// angular calendario

app.controller('ca-usuario_index', ['$scope','NgTableParams','$http','$notify','$loader','$element', function($scope,NgTableParams, $http, $notify,$loader,$element) {

	$scope.dados = [];
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
    			params.total(response.data.total); // recal. page nav controls
    	        return response.data.res;
    		  }, function errorCallback(response) {
    			  $loader.hide();
    		  });
      }
    });
	
	$scope.checkboxes = { 'checked': false, items: {} };
	this.changePageSize = changePageSize;
	
	function changePageSize(newSize){
      this.tableParams.count(newSize);
    }
	
	this.applyGlobalSearch = applyGlobalSearch;
    
    function applyGlobalSearch(){
      var term = this.globalSearchTerm;
      this.tableParams.filter({ valor: term });
    }

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