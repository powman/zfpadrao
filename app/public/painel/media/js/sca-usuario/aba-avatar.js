

/*
 * Controller Aba Usuario
 */
app.register.controller('sca-usuario_aba-avatar', function Ctrl($scope, Upload, Scopes,$uibModal,$http) {
	Scopes.store('sca-usuario_aba-avatar', $scope);
	
	$scope.clickWebcam = clickWebcam;
	$scope.clickRecortarImagem = clickRecortarImagem;
	$scope.modalInstance = "";
	$scope.imagePerfil = '';
	
	function clickWebcam(){
		$http({
            method: "post",
            url: _baseUrl+_controller+'/webcam',
            data: $.param({returncontrole:'sca-usuario_aba-avatar'})
        }).success(function($data, $status, $headers, $config){
        	$scope.modalInstance = $uibModal.open({
      		  animation: true,
      		  template: $data,
      	      size: 'sm'
      	    });
		}).error(function($data, $status, $headers, $config) {
			
		});
		
	}
	
	function clickRecortarImagem(){
		$http({
            method: "post",
            url: _baseUrl+_controller+'/recortar-imagem',
            data: $.param({returncontrole:'sca-usuario_aba-avatar'})
        }).success(function($data, $status, $headers, $config){
        	$scope.modalInstance = $uibModal.open({
      		  animation: true,
      		  template: $data,
      	      size: 'sm'
      	    });
		}).error(function($data, $status, $headers, $config) {
			
		});
		
	}
	
	
	
	
	// upload on file select or drop
	$scope.upload = function (dataUrl) {
		console.log(dataUrl);
		var FR = new FileReader();
        FR.onload = function(e) {
        	  //$scope.sai = e.target.result;
              $('#sai').attr( "src", e.target.result );
              $scope.imagePerfil = e.target.result;
             //$('#base').text( e.target.result );
        };       
        FR.readAsDataURL( dataUrl );
		//$scope.file = dataUrl;
       /* Upload.upload({
            url: 'https://angular-file-upload-cors-srv.appspot.com/upload',
            data: {
                file: Upload.dataUrltoBlob(dataUrl)
            },
        }).then(function (response) {
        	console.log(response.data);
            $timeout(function () {
                $scope.file = response.data;
            });
        }, function (response) {
            if (response.status > 0) $scope.errorMsg = response.status 
                + ': ' + response.data;
        }, function (evt) {
            $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
        });*/
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

