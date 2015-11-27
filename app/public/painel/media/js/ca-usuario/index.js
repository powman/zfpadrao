// angular calendario

app.controller('ca-usuario_index', ['$scope','NgTableParams', function($scope,NgTableParams) {
	var data = [{ name: 'christian', age: 21 }, { name: 'anthony', age: 88 }];
	this.tableParams = new NgTableParams({
      page: 1, // show first page
      count: 10 // count per page
    }, {
      filterDelay: 0,
      dataset: data
    });
}]);