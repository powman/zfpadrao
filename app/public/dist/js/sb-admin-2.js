// Declare app level module which depends on views, and components
var app = angular.module('painel',['ngTable','ui.bootstrap','ngFileUpload','ngImgCrop','camera','confirm-click']);
/*app.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});*/

app.config(function($httpProvider,$controllerProvider,$provide,ConfirmClickProvider) {
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
    app.register = {
    		controller: $controllerProvider.register
    };
    // To register the following which will be loaded lazily later for future use
    // * Constant   -   app.regiser.value     -   To register constant
    // * Factory    -   app.regiser.factory   -   To register factory
    // * service    -   app.regiser.service   -   To register service
    app.$register = $provide;
    
    ConfirmClickProvider.ask(function (question) {
        return new Promise(function (resolve) {
          alertify.confirm(question, resolve);
        });
    });
});

app.run(function ($rootScope) {
    $rootScope.$on('scope.stored', function (event, data) {
        console.log("scope.stored", data);
    });
});

app.factory('$validator', function($http,$q) {
    return {
    	/*
    	 * Exemplo 
    	 * 
    	 * <input type="email" name="email" data-error="Email obrigatório" data-type="email"  required placeholder="E-mail" />
    	 * 
    	 * <input type="text" name="celular" mask="telefone" data-error="Celular obrigatório" data-type="string" placeholder="Celular" required />
    	 * 
    	 */
        validar: function($form) {
        	var erros = [];
            var $form = $("#"+$form);
            $form.find("input[required]").each(function(index){
                var $obj = $(this);
                if($obj.attr("data-type") == "string"){
                    if (!$obj.inSizeRangeString(1)){
                        erros.push({
                            msg: $obj.attr("data-error")
                        });
                    }
                }else if($obj.attr("data-type") == "email"){
                    if (!$obj.isValidEMAIL()){
                        erros.push({
                            msg: $obj.attr("data-error")
                        });
                    }
                }else if($obj.attr("data-type") == "radio-check"){
                	var $tipos = $(this).attr("name");
                	if($form.find("input[name="+$tipos+"]").length > 1){
                		if(!$form.find("input[name="+$tipos+"]:checked").length){
        	        		erros.push({
        	                    msg: $("input[name="+$tipos+"]").attr("data-error")
        	                });
                		}
                	}else if(!$passou){
                		if(!$form.find("input[name="+$tipos+"]:checked").length){
        	        		erros.push({
        	                    msg: $("input[name="+$tipos+"]").attr("data-error")
        	                });
                		}
                	}
                }else if($obj.attr("data-type") == "external" && $obj.attr("data-valida-url")){
	          	  var $data = $.param({campo:$($obj).val()});
          		     $http({
                          method: "post",
                          url: $obj.attr("data-valida-url"),
                          data: $data
                      }).success(function($data, $status, $headers, $config){
                      	if($data.status == "error"){
                      		erros.push({
        	                    msg: $("input[name="+$tipos+"]").attr("data-error")
        	                });
                      	}
          			  }).error(function($data, $status, $headers, $config) {
          				
          			  });
                }
                 
            });
            
            var msg = erros.map(function(obj) { return obj.msg; });
            erros = msg.filter(function(v,i) { return msg.indexOf(v) == i; });
            console.log(erros,"erros");
            if(erros.length > 0){
	            var _mensagem = "";
	            $(erros).each(function(idx, item) {
	                _mensagem += item + '<br>';
	            });
	            noty({
	    		    text: _mensagem,
	    		    modal:true,
	    		    killer: true,
	    		    layout:"center",
	    		    type:"information",
	    		    timeout:5000
	    		});
	            return false;
            }else{
            	return true;
            }
        }
    };
});

app.factory('Scopes', function ($rootScope) {
	
    var mem = {};

    return {
        store: function (key, value) {
            $rootScope.$emit('scope.stored', key);
            mem[key] = value;
        },
        get: function (key) {
            return mem[key];
        }
    };
});

app.factory('$notify', function() {
    return {
        open: function($msg, $time, $type, $onComplete) {
        	$type ? $type : "information";
        	noty({
    		    text: $msg,
    		    modal:true,
    		    killer: true,
    		    layout:"center",
    		    type:$type,
    		    maxVisible: 3,
    		    force:true,
    		    timeout:$time ? $time : 100000,
	    		callback: {
	    	        onShow: function() {},
	    	        afterShow: function() {},
	    	        onClose: function() {
	    	        	$.noty.closeAll();
	    		    	$.noty.clearQueue();
	    		    	if($onComplete)
	    		    		$onComplete();
	    	        },
	    	        afterClose: function() {},
	    	        onCloseClick: function() {
	    	        	if($onComplete)
	    		    		$onComplete();
	    	        },
	    	    },
    		});
        
        },
    
	    close: function() {
	    	setTimeout(function(){ 
	    		$.noty.closeAll();
		    	$.noty.clearQueue();
		    	if($onComplete)
		    		$onComplete();
	    	}, 1000);
	    	
	    }	
    };
});

app.factory('$loader', function() {
	// Cria a vid
	var $dialog = $(
		'<div id="loadingPadrao" style="background-color:#fff1a8; width:200px; border:1px solid #ccc; -webkit-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75); box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75); -moz-box-shadow: 0px 0px 5px 0px rgba(50, 50, 50, 0.75); padding: 5px; z-index:9999999; position:fixed; top:-100px; left:50%; margin-left:-100px; text-align:center; -webkit-border-radius: 2px; -moz-border-radius: 2px; border-radius: 2px;">' +
			'<p></p>'+
		'</div>' );

	return {
		/*
		 * Abre o Dialog
		 */
		show: function (message) {
			$('body').append($dialog);
			$('#loadingPadrao').stop().animate({
				top: '0px'
			},200);
			$dialog.find('p').text(message);
		},
		/**
		 * Fecha o Dialog
		 */
		hide: function () {
			$('#loadingPadrao').stop().animate({
				top: '-100px'
			},200);
			$('#loadingPadrao').remove();
		}
	}
});

app.factory('$sessao', function($http,$q) {
	  var factory = {};
	  factory.getSessions = function () {
		  return $http.post( _baseUrl+_modulo+'/index/sessao').success(function($data, $status, $headers, $config){
			  	if($data.situacao == "error"){
					
				}else if($data.situacao == "success"){
					return $data;
					
				}else{
					return false;
				}
				
			}).error(function($data, $status, $headers, $config) {
				return false;
			});
		   
	   }
  	   return factory;
});

app.directive('botaoAcao', function($http,$q,$location) {
  var directive = {};
  directive.restrict = 'E'; /* restrict this directive to elements */
  var $completaurl = "";
  var $id = "";
  if($location.$$absUrl.indexOf('id') > -1){
	  $id = $location.$$absUrl.match(/id\/[0-9]*/).toString().replace("id/",""); 
  }
  if($id){ 
	  $completaurl = "/id/"+$id ;
  }
  directive.templateUrl =  _baseUrl+_modulo+'/'+_controller+'/get-botao'+$completaurl;
  return directive;
});

app.directive('lookup', function($http,$q,$location,$uibModal,$loader) {
  var modalInstance;
  var directive = {};
  directive.restrict = 'E'; /* restrict this directive to elements */
  var $html = "";
  $html  = '<span class="pull-right">';
  $html += '	<button class="btn btn-default btn-sm-default" type="button">';
  $html += '		<span class="glyphicon glyphicon glyphicon-search" aria-hidden="true"></span>';
  $html += '	</button>';
  $html += '</span>';
  
  directive.template =  function(elem, attr){
	  return $html;
  }
  
  directive.link = function(scope, elem, attrs) {
	  elem.bind('click', function() {
	  	  $loader.show("Carregando...");
	  	  var $data = $.param({islocation:attrs.islocation,returncontrole:attrs.returncontrole});
		  $http({
                method: "post",
                url: attrs.url,
                data: $data
            }).success(function($data, $status, $headers, $config){
            	$loader.hide();
            	var modalInstance = $uibModal.open({
      		      animation: true,
      		      template: $data
      		    });
			}).error(function($data, $status, $headers, $config) {
				$loader.hide();
			});
      });
	  
	  if(attrs.search){
		  $('input[name='+attrs.search+']').click(function(){
			  $(elem).click();
		  });
		  
	  }
    
  }
  return directive;
});

//should be like load-ctrl="myNoteCtrl" caminho-js="controller.js"
app.directive('loadCtrl', ['$compile', function($compile) {
  return {
    restrict: 'A',
    terminal: true,
    priority: 100000,
    link: function(scope, element, attrs, ctrl) {
      
      var controllerName = attrs.loadCtrl;
      var path = attrs.caminhoJs;
      if(path && element.attr('ng-controller') == undefined || element.attr('ng-controller') == "") {
    	  //if(!$('script[src="'+path+'"]').length){
	          // append script tag to head
	          var script = document.createElement( 'script' );
	          script.type = 'application/javascript';
	          script.src = path;
	          $('div[load-ctrl]').append(script);
	
	        // add ng-controller
	        element.attr('ng-controller', controllerName);
	        element.removeAttr('load-ctrl');
	        $compile(element)(scope);
    	 // }
      }
      else {
        console.log('Controller path for '+controllerName+' not found!');
      }
    }
  };
}]);

function enableCadastrar(){
	$("#incluir").removeAttr("disabled");
	$("#incluir").css("opacity",1);
	$("#alterar").attr("disabled",true);
	$("#alterar").css("opacity",0.1);
	$("#remover").attr("disabled",true);
	$("#remover").css("opacity",0.1);
}

function enableAlterar(){
	$("#incluir").attr("disabled",true);
	$("#incluir").css("opacity",0.1);
	$("#alterar").removeAttr("disabled");
	$("#alterar").css("opacity",1);
	$("#remover").removeAttr("disabled");
	$("#remover").css("opacity",1);
}

function preencheCampos($array){
	angular.forEach($array, function(value, key) {
		$('input[name='+key+']').val(value);
		$('select[name='+key+']').find("option").each(function(index){
			$(this).val() == value ? $(this).attr("selected",true) : "";
		});
	});
}

$(function() {

    $('#side-menu').metisMenu();
    
    //Filtro de busca
	$("#txtBusca").keyup(function(){ 
		var texto = $(this).val(); 
		$("ul#side-menu li").css("display", "block"); 
		
		$("ul#side-menu li").each(function(){ 
			if($(this).find("a").text().toUpperCase().indexOf(texto.toUpperCase()) < 0){ 
				$(this).css("display", "none"); 
			}
		});		
	});

});





$.fn.inSizeRangeString = function(min, max) {
    var len = this.val().length;
    if (this.val() == this.attr('placeholder')) return false;
    if (min != null && max != null) {
        if (len < min || len > max)return false;
    } else if (min != null && max == null) {
        if (len < min)return false;
    } else if (min == null && max != null) {
        if (len > max)return false;
    }
    return true;
};
$.fn.isValidEMAIL = function() {
    var email = this.val();
    var emailPattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    return emailPattern.test(email);
};

//**dataURL to blob**
function dataURLtoBlob(dataurl) {
    var arr = dataurl.split(','), mime = arr[0].match(/:(.*?);/)[1],
        bstr = atob(arr[1]), n = bstr.length, u8arr = new Uint8Array(n);
    while(n--){
        u8arr[n] = bstr.charCodeAt(n);
    }
    return new Blob([u8arr], {type:mime});
}

//**blob to dataURL**
function blobToDataURL(blob, callback) {
    var a = new FileReader();
    a.onload = function(e) {callback(e.target.result);}
    a.readAsDataURL(blob);
}
