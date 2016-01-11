// angular calendario
app.controller('index_login', function index_login($scope,$http,$validator,$notify ) {
	// Http Login
	$scope.logar = function(idform){
	    if($validator.validar(idform)){
	    	$notify.open("Carregando...");
	    	var data = $.param({ login_usuario: $scope.login_usuario, password_usuario: $scope.password_usuario, lembrar: $scope.lembrar });
    	    $http.post( _baseUrl+_modulo+'/index/logar',data).success(function($data){
    	    	if($data.situacao == "error"){
    	    		$notify.open($data.msg,2000,"error");
    	    	}else if($data.situacao == "success"){
    	    		$notify.open($data.msg,2000,"success");
    	    		window.location.href= _baseUrl+_modulo+'/index/index';
    	    	}else{
    	    		$notify.close();
    	    	}
    	    	
    	    }).error(function() {
    	    	$notify.open("Um erro inesperado aconteceu.",2000,"error");
    	    });
	    }
	}
	
	
});