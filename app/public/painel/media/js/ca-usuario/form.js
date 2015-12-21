// angular calendario

app.service(
    "$model",
    function( $http, $q, $loader ) {
        // Return
        return({
            add: add,
            getAll: getAll,
            getTabs: getTabs,
            remove: remove
        });
        // ---
        // PUBLIC METHODS.
        // ---
        // Adciona os dados remoto
        function add( obj ) {
        	$loader.show("Carregando...");
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/salvar",
                data: obj
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        // Pega os dados todos remoto
        function getAll(obj) {
            var request = $http({
                method: "post",
                url: _baseUrl+_controller+"/index",
                data: obj
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        // Pega os dados todos remoto
        function getTabs(obj) {
        	$loader.show("Carregando...");
            var request = $http({
                method: "get",
                url: _baseUrl+_controller+"/get-abas"
            });
            return( request.then( handleSuccess, handleError ) );
        }
        
        // Remove um dado remoto
        function remove( ids ) {
            var request = $http({
                method: "delete",
                url: _baseUrl+_controller+"/remover",
                params: ids
            });
            return( request.then( handleSuccess, handleError ) );
        }
        // ---
        // PRIVATE METHODS.
        // ---
        // I transform the error response, unwrapping the application dta from
        // the API response payload.
        function handleError( response ) {
        	$loader.hide();
            // The API response from the server should be returned in a
            // nomralized format. However, if the request was not handled by the
            // server (or what not handles properly - ex. server error), then we
            // may have to normalize it on our end, as best we can.
            if (
                ! angular.isObject( response.data ) ||
                ! response.data.message
                ) {
                return( $q.reject( "An unknown error occurred." ) );
            }
            // Otherwise, use expected error message.
            return( $q.reject( response.data.message ) );
        }
        // I transform the successful response, unwrapping the application data
        // from the API response payload.
        function handleSuccess( response ) {
        	$loader.hide();
            return( response.data );
        }
    }
);

app.controller('ca-usuario_form', function Ctrl($scope,$loader,$model) {
	
	$scope.tabs = [];
	
	loadTabs();
	/*
	 * Pega os dados das abas
	 */
	function loadTabs(){
	    	return $model.getTabs()
		    .then(
		        function( data ) {
		        	if(data.status == "sucesso" && data.dados){
		        		setTabs( data.dados );
		    	       // return $scope.dados;
		        	}else{
		        		$notify.open(data.msg,3000,"error");
		        	}
		        }
		    );
	}
	
	function setTabs( dados ) {
		
        $scope.tabs = dados;
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

/*
 * Controller Aba Usuario
 */
app.controller('ca-usuario_aba-usuario', function Ctrl($scope) {
	$scope.find = function(){
		console.log("fds");
	}
	
    
});

