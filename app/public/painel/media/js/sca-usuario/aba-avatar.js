

/*
 * Controller Aba Usuario
 */
app.register.controller('sca-usuario_aba-avatar', function Ctrl($scope, Upload, Scopes) {
	Scopes.store('sca-usuario_aba-avatar', $scope);
	
	// upload on file select or drop
	$scope.upload = function (dataUrl) {
        Upload.upload({
            url: 'https://angular-file-upload-cors-srv.appspot.com/upload',
            data: {
                file: Upload.dataUrltoBlob(dataUrl)
            },
        }).then(function (response) {
            $timeout(function () {
                $scope.result = response.data;
            });
        }, function (response) {
            if (response.status > 0) $scope.errorMsg = response.status 
                + ': ' + response.data;
        }, function (evt) {
            $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
        });
    }
	
	
	// Antes do Ajax
	$scope.$on('$includeContentRequested', function($obj) {
		$loader.show("Carregando...");
	});
	// Depois que carregou
	$scope.$on('$includeContentLoaded', function() {
		$loader.hide();
	});
    
});

