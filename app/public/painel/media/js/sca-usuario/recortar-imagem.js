app.register.controller('sca-usuario_recortar-imagem', function Ctrl($scope, Scopes) {
	Scopes.store('sca-usuario_recortar-imagem', $scope);
	$scope.picture = Scopes.get($parametros.returncontrole).imagePerfil;
	$scope.croppedDataUrl = "";
	var modalInstance = Scopes.get($parametros.returncontrole).modalInstance;
	modalInstance.result.then(function (selectedItem) {
      
    }, function () {
    	Scopes.get($parametros.returncontrole).imagePerfil = $scope.croppedDataUrl;
    });
});