// Declare app level module which depends on views, and components
var app = angular.module('painel',['ngTable','ui.bootstrap']);
/*app.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});*/

app.config(function($httpProvider) {
    $httpProvider.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
    $httpProvider.defaults.headers.common["X-Requested-With"] = 'XMLHttpRequest';
});

app.factory('$validator', function() {
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
                }
                 
            });
            
            var msg = erros.map(function(obj) { return obj.msg; });
            erros = msg.filter(function(v,i) { return msg.indexOf(v) == i; });
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

app.factory('$notify', function() {
    return {
        open: function($msg, $time, $type) {
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
	    	        },
	    	        afterClose: function() {},
	    	        onCloseClick: function() {},
	    	    },
    		});
        
        },
    
	    close: function() {
	    	setTimeout(function(){ 
	    		$.noty.closeAll();
		    	$.noty.clearQueue();
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
	  	  var $data = $.param({search:$('input[name='+attrs.search+']').val()});
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
    
  }
  return directive;
});

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

//Loads the correct sidebar on window load,
//collapses the sidebar on window resize.
// Sets the min-height of #page-wrapper to window size
$(function() {
    $(window).bind("load resize", function() {
        topOffset = 50;
        width = (this.window.innerWidth > 0) ? this.window.innerWidth : this.screen.width;
        if (width < 768) {
            $('div.navbar-collapse').addClass('collapse');
            topOffset = 100; // 2-row-menu
        } else {
            $('div.navbar-collapse').removeClass('collapse');
        }

        height = ((this.window.innerHeight > 0) ? this.window.innerHeight : this.screen.height) - 1;
        height = height - topOffset;
        if (height < 1) height = 1;
        if (height > topOffset) {
            $("#page-wrapper").css("min-height", (height) + "px");
        }
    });

    var url = window.location;
    var element = $('ul.nav a').filter(function() {
        return this.href == url || url.href.indexOf(this.href) == 0;
    }).addClass('active').parent().parent().addClass('in').parent();
    if (element.is('li')) {
        element.addClass('active');
    }
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
