// angular calendario
app.controller('index_login', function index_login($scope,$http,validator) {
	// Http Login
		
	    $scope.logar = function(idform){
	    if(validator.validar(idform)){
			$http({
			  method: 'POST',
			  url: _baseUrl+'/index/logar',
			  data: { email: $scope.email, senha: $scope.senha }
			}).then(function successCallback(response) {
				//$scope.loading = false;
			    // this callback will be called asynchronously
			    // when the response is available
			  }, function errorCallback(response) {
				  //$scope.loading = false;
			    // called asynchronously if an error occurs
			    // or server returns response with an error status.
			});
	    }
	}
	
	
});