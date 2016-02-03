app.register.controller('sca-usuario_webcam', function Ctrl($scope, Scopes) {
	Scopes.store('sca-usuario_webcam', $scope);
	
	$scope.picture = "";

	var modalInstance = Scopes.get($parametros.returncontrole).modalInstance;
	
	modalInstance.result.then(function (selectedItem) {
      
    }, function () {
    	Scopes.get($parametros.returncontrole).imagePerfil = $scope.picture;
    });
});