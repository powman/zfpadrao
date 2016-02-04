

/*
 * Controller Aba Usuario
 */
app.register.controller('sca-usuario_aba-avatar', function Ctrl($scope, Upload, Scopes,$uibModal,$http) {
	Scopes.store('sca-usuario_aba-avatar', $scope);
	
	$scope.clickWebcam = clickWebcam;
	$scope.clickRecortarImagem = clickRecortarImagem;
	$scope.clickSalvar = clickSalvar;
	$scope.clickRemover = clickRemover;
	$scope.progress = '';
	
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
	
	function clickSalvar(){
	    Upload.upload({
            url: _baseUrl+_controller+"/salvar-avatar",
            data: {
                file: dataURLtoBlob($scope.imagePerfil),
                id_usuario: Scopes.get("sca-usuario_aba-usuario").dados.id_usuario,
                id_avatar: Scopes.get("sca-usuario_aba-usuario").dados.id_avatar
            },
        }).then(function (response) {
        	$scope.progress = '';
        }, function (response) {
            //if (response.status > 0) $scope.errorMsg = response.status 
               // + ': ' + response.data;
        }, function (evt) {
            $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
        });
		
	}
	
	function clickRemover(){
		
		
	}
	
	// upload on file select or drop
	$scope.upload = function (dataUrl) {
		if(dataUrl){
			var FR = new FileReader();
	        FR.onload = function(e) {
	              $('#imagePerfil').attr( "src", e.target.result );
	              $scope.imagePerfil = e.target.result;
	             //$('#base').text( e.target.result );
	        };       
	        FR.readAsDataURL( dataUrl );
		}
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

