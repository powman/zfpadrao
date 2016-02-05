// angular form
app.service(
    "$modelform",
    function( $http, $q, $loader ) {
        // Return
        return({
            getTabs: getTabs,
        });
        // ---
        // PUBLIC METHODS.
        // ---
        // Pega os dados todos remoto
        function getTabs(obj) {
        	$loader.show("Carregando...");
            var request = $http({
                method: "get",
                url: _baseUrl+_controller+"/get-abas"
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

app.controller('sca-grupo_form', function Ctrl($scope,$loader,$modelform,$notify,Scopes) {
	Scopes.store('sca-grupo_form', $scope);
	
	$scope.tabs = [];
	
	loadTabs();
	/**
	 * Pega as abas ativas
	 */
	function loadTabs(){
    	return $modelform.getTabs()
	    .then(
	        function( data ) {
	        	if(data.status == "sucesso" && data.dados){
	        		setTabs( data.dados );
	        	}else{
	        		$notify.open(data.msg,3000,"error");
	        	}
	        }
	    );
	}
	/**
	 * Seta as abas para o scope
	 */
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

