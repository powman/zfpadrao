// angular calendario

app.controller('ca-usuario_index', ['$scope','NgTableParams','$http', function($scope,NgTableParams, $http) {


	this.tableParams = new NgTableParams({}, {
      getData: function(params) {
        // ajax request to api
    	var $data = $.param(params.url());
    	return $http({
    		  method: 'POST',
    		  url: _baseUrl+'/painel/ca-usuario',
    		  data: $data,
    		}).then(function successCallback(response) {
    			params.total(response.data.total); // recal. page nav controls
    	        return response.data.res;
    		  }, function errorCallback(response) {
    		    // called asynchronously if an error occurs
    		    // or server returns response with an error status.
    		  });
        /*return Api.get(params.url()).$promise.then(function(data) {
          params.total(data.inlineCount); // recal. page nav controls
          return data.results;
        });*/
      },
      counts: [2, 5, 10, 20]
    });
	
	$scope.checkboxes = { 'checked': false, items: {} };

    // watch for check all checkbox
    $scope.$watch('checkboxes.checked', function(value) {
        angular.forEach($scope.users, function(item) {
            if (angular.isDefined(item.id)) {
                $scope.checkboxes.items[item.id] = value;
            }
        });
    });
    
}]);