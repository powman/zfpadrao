// Declare app level module which depends on views, and components
var app = angular.module('painel',[]);
/*app.run(function(editableOptions) {
  editableOptions.theme = 'bs3'; // bootstrap3 theme. Can be also 'bs2', 'default'
});*/

app.factory('validator', function() {
    return {
    	/*
    	 * Exemplo 
    	 * 
    	 * <input type="email" name="email" data-error="Email obrigatório" data-type="email"  required placeholder="E-mail" />
    	 * 
    	 * <input type="text" name="celular" mask="telefone" data-error="Celular obrigatório" data-type="string" placeholder="Celular" required />
    	 * 
    	 * <form validar="true" action="" method="post"></form>
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
