// JavaScript Document // 19.07.2018
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();//bstp4

  
/********************Apenas número*********************************************************/	
$(".numero").keydown(function (event) {
        if (!(event.keyCode == 8                                // backspace
            || event.keyCode == 9                               // tab
            || event.keyCode == 17                              // ctrl
            || event.keyCode == 46                              // delete
            || (event.keyCode >= 35 && event.keyCode <= 40)     // arrow keys/home/end
            || (event.keyCode >= 48 && event.keyCode <= 57)     // numbers on keyboard
            || (event.keyCode >= 96 && event.keyCode <= 105)    // number on keypad
            || (event.keyCode == 65 && prevKey == 17 && prevControl == event.currentTarget.id))          // ctrl + a, on same control
        ) {
            event.preventDefault(); 
            console.log('Apenas número');    // Prevent character input
        }
        else {
            prevKey = event.keyCode;
            prevControl = event.currentTarget.id;
        }
    });	
});

/************************************Funcoes gerais******************************************************************/
function searchI(table,e){//search a partir do botão
		var table = $(table).DataTable();
		//console.log(e);
		//$('#oficio_filter').find('input').val(e);
			table.search(e).draw();

	}

/*******************************************************Hide slow e Fade******************************************************/		 
function ajaxCustom(tipo, message='', url_inline, dados, successFunction='',beforeSendFunction='',completeFunction='',typeSend='POST'){/*metodo ajax para post*/

switch(tipo){
	case 'confirm':		
		alert_boot('confirm',message,'',function(result){							
		if(result==true){						
		$.ajax({url: url_inline, data: dados,type: typeSend,
			  success: successFunction,
			  beforeSend: beforeSendFunction,
			  complete: completeFunction,
			  error: function (xhr, ajaxOptions, thrownError) {alert("ERROR:" + xhr.responseText+" - "+thrownError);}	 
			});/*ajax*/

			}/*if*/
		});/*alert_boot*/
break;
	case 'submit':
			$.ajax({url: url_inline, data: dados,type: typeSend,
			  success: successFunction,
			  beforeSend: beforeSendFunction,
			  complete: completeFunction,
			  error: function (xhr, ajaxOptions, thrownError) {alert("ERROR:" + xhr.responseText+" - "+thrownError);}	 
			});/*ajax*/
	break;
}
}
function hide_div(target) {//hide a div com efeito blind - 2sec
    setTimeout(function(){
        var selectedEffect = 'blind';
        var options = {};
        $(target).hide(selectedEffect, options, 500)
     }, 2000);
}

function alert_boot(tipo,mensagem='',titulo='',callback='',buttonLabel='',size = 'small', reload = true, estilo=''){
 //fazer foreach para não ter que seguir a ordem
switch(tipo){
	case 'confirm':
	return bootbox.confirm({
		    message: mensagem,
		    buttons: {
		        confirm: {
		            label: 'Sim',
		            className: 'btn-success'

		        },
		        cancel: {
		            label: 'Não',
		            className: 'btn-danger'

		        }
		    },
		    callback: function (result) {
		    	callback(result);
		    	//console.log('confirm_result: '+result);
		    }
		});
		break;
	case 'alert':
	var alert = bootbox.alert({
		    message: mensagem,
		    size: size,
		    title: titulo,
		   	closeButton: false,
		    callback: function () {		        
		        //console.log(mensagem);
				if(reload == true){
					alert_boot('wait','<div class="alert alert-success" role="alert">Recarregando a página</div>');
					window.location.reload();
				}
		    },
		    buttonName : {
			  label: 'Fechar',
			  className: "btn-success"
			}		    
		});
		alert.init(function(){
			bootbox.hideAll();
		});
	return alert;		
	break;
	case 'wait':
		return	bootbox.dialog({ message: '<div class="wait-loading alert '+estilo+' text-center"><i class="fa fa-spin fa-spinner"></i>'+mensagem+'</div>',closeButton: false });
	break;
	}
}

function wait(array){
 return alert_boot('wait',array.msg,array.titulo,array.callback,array.buttonLabel,array.size,array.reload,array.estilo, array.size);
}
function alert_apenas(array){
 return alert_boot('alert',array.msg,array.titulo,array.callback,array.buttonLabel,array.size,array.reload,array.estilo, array.size); 
}

function submit_apenas(array){
 return ajaxCustom('submit', array.msg, array.url, array.dados, 
  function(resposta){ 
    if(resposta !==false){
     array.success;
     console.log('success: '+resposta)
    }
   },
  function(resposta){ 
    array.before;
    console.log('before: '+resposta)
  },
  function(resposta){ 
    array.complete;
    hideAll()
    console.log('complete: '+resposta)    
  },array.typeSend);
}

function hideAll(){
 return bootbox.hideAll();
}

function recarregarPagina(target, message='Para ver as alterações na tabela, clique para atualizar a página!',title='',divClass='alert-success',buttonClass='btn-success'){
/*mostra mensagem de reload*/
var retorno = '<div class="alert '+divClass+' text-center" role="alert"><h4 class="alert-heading msg_retorno">'+title+'</h4><p class=""><button class="btn '+buttonClass+' btn-lg recarregarButton" type="button" onclick="window.location.reload()"><i class="fa fa-refresh fa-4" aria-hidden="true"></i></button></p><p>'+message+'</p></div>';


return $(target).html(retorno);
}

function selecioneDetalhe(target, message='Selecione um item na tabela ao lado para ver detalhes dele.',title='Detalhes dos itens',divClass='alert-success'){
/*mostra mensagem de reload*/
var retorno = '<div class="alert '+divClass+' text-center" role="alert"><h4 class="alert-heading msg_retorno">'+title+'</h4><p>'+message+'</p></div>';

return $(target).html(retorno);
}

function selecioneEditar(target, message='Selecione um item na tabela ao lado para editá-lo.',title='Editar item',divClass='alert-warning'){
/*mostra mensagem de reload*/
var retorno = '<div class="alert '+divClass+' text-center" role="alert"><h4 class="alert-heading msg_retorno">'+title+'</h4><p>'+message+'</p></div>';

return $(target).html(retorno);
}

function selectAuto(fonte,target,url,funcao,label=''){		
//loading para popular o select
	var id = fonte.val();
	var dadosSubmit= {id: '.'+label+'.'+id+'.'+funcao+'', value: 'select'};	
	//console.log(id);
	$(target).html('');
	ajaxCustom('submit','',url, dadosSubmit,
							  function(resposta){
								if (resposta !== false) {
									console.log('submit: '+resposta);
									$(resposta).appendTo(target);
								}
							  },
							  function(){
							    //console.log("script indo");
								formLoading(fonte);
								$(target+" option").remove();
								
							  },
							  function(){	
							    formLoading('remove');	
									
							  });

}
function formLoading(x,estilo='alert-warning',message='Aguarde...'){//insere um loading abaixo do elemento selecionado, geralmente em dropdown select autopopulate
	if(x != 'remove'){
		$('<div class="wait-loading alert '+estilo+' "><i class="fa fa-spin fa-spinner"></i>'+message+'</div>').insertAfter(x);
	}else{
		$('.wait-loading').remove();
	}
}

