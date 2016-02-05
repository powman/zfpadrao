

/*
 * Controller Aba Avatar
 */
app.register.controller('sca-usuario_aba-avatar', function Ctrl($scope, Upload, Scopes,$uibModal,$http,$notify) {
	Scopes.store('sca-usuario_aba-avatar', $scope);
	
	$scope.clickWebcam = clickWebcam;
	$scope.clickRecortarImagem = clickRecortarImagem;
	$scope.clickSalvar = clickSalvar;
	$scope.clickRemover = clickRemover;
	$scope.upload = upload;
	$scope.progress = '';
	// pega os dados da imagem na aba do usuario
	setTimeout(function(){ 
		if(Scopes.get("sca-usuario_aba-usuario").dados){
			if(Scopes.get("sca-usuario_aba-usuario").dados.id_usuario){
				//habilita a aba de avatar
				Scopes.get("sca-usuario_form").tabs[1].disabled = false; 
				$scope.imagePerfil = Scopes.get("sca-usuario_aba-usuario").dados.arquivo; 
			}
		}
	}, 500);
	
	/**
	 * função para abrir o modal da webcam
	 */
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
			$notify.open($status,3000,"error");
		});
		
	}
	/**
	 * funcao para recortar a imagem
	 */
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
			$notify.open($status,3000,"error");
		});
		
	}
	/**
	 * Função para salvar o avatar
	 */
	function clickSalvar(){
	    Upload.upload({
            url: _baseUrl+_controller+"/salvar-avatar",
            data: {
                file: dataURLtoBlob($scope.imagePerfil),
                id_usuario: Scopes.get("sca-usuario_aba-usuario").dados.id_usuario,
                id_avatar: Scopes.get("sca-usuario_aba-usuario").dados.id_avatar
            },
        }).then(function (response) {
        	Scopes.get("sca-usuario_aba-usuario").dados.id_avatar = response.data.dados.id_avatar;
        	$scope.progress = '';
        	$notify.open(response.data.msg,3000,"success");
        }, function (response) {
        	$notify.open(response.status + ': ' + response.data,3000,"error");
        }, function (evt) {
            $scope.progress = parseInt(100.0 * evt.loaded / evt.total);
        });
		
	}
	/**
	 * função para remover o avatar
	 */
	function clickRemover(){
		if(Scopes.get("sca-usuario_aba-usuario").dados.id_avatar != null){
			alertify.confirm("Deseja realmente excluir esta imagem?",function(status){
				if(status){
					$http({
			            method: "post",
			            url: _baseUrl+_controller+'/remover-avatar',
			            data: $.param({id_avatar:Scopes.get("sca-usuario_aba-usuario").dados.id_avatar,id_usuario: Scopes.get("sca-usuario_aba-usuario").dados.id_usuario})
			        }).success(function($data, $status, $headers, $config){
			        	Scopes.get("sca-usuario_aba-usuario").dados.id_avatar = null;
			        	$scope.imagePerfil = '';
			        	$('#imagePerfil').attr( "src", _baseUrl+'assets/images/person.png' );
			        	$notify.open($data.msg,3000,"success");
					}).error(function($data, $status, $headers, $config) {
						$notify.open($status,3000,"error");
					});
				}
				
			});
		}else{
			$scope.imagePerfil = '';
			$('#imagePerfil').attr( "src", _baseUrl+'assets/images/person.png' );
		}
		
	}
	/**
	 * função para pegar o dados da imagem
	 */
	function upload(dataUrl){
		if(dataUrl){
			var FR = new FileReader();
	        FR.onload = function(e) {
	              $('#imagePerfil').attr( "src", e.target.result );
	              $scope.imagePerfil = e.target.result;
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
		alert("carregou");
	});
    
});

