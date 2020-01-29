<?php
//error_reporting(0);
include $_SERVER['DOCUMENT_ROOT'] . '/grifo/classes/elementos_form.php'; $elementos = new Elementos();
include $_SERVER['DOCUMENT_ROOT'] . '/grifo/classes/formulario.php'; $formulario = new Formulario();


$biblioteca = new Biblioteca\Custom();


$form_inserir = '<div class="card m-2 p-3 mx-auto" style="max-width: 700px;">
             <div class="detalhes_inserir"></div><form action="" method="post" name="forms_todos" id="salva_oficio" class="forms_todos " enctype="multipart/form-data" autocomplete="off">
										  <div class="box box-success">
										  		<div class="box-header with-border text-center"><h5>Inserir Novo</h5></div>
												<div class="box-body with-border">'.$formulario->form_oficio().'
												<input name="acao" type="hidden" value="novo"/><input name="modulo" type="hidden" value="oficio"/></div>						
												<div class="box-footer text-center"><button type="submit" class="btn btn-success" name="salvar_novo">Salvar</button></div> 
										  </div>
										</form>
          </div>';


 ?>

<script src="https://code.jquery.com/jquery-3.4.1.min.js" ></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-validator/0.5.3/js/bootstrapValidator.min.js"/></script>
     <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"/></script>


  <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
  <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootbox.js/4.4.0/bootbox.min.js"/></script>
<script src="https://root-aynoei177396.codeanyapp.com/grifo/scripts/default_scripts.js"/></script>

 <script type="text/javascript">
/******Oficio******************/
$(document).ready(function() {
 $( ".data_mask" ).mask("00/00/0000", {placeholder: "__/__/____"});
$( ".hora_mask" ).mask("00:00", {placeholder: "__:__"});
$(document).on('focus','.campos_alterar',function(){	
	$( ".data_mask" ).mask("00/00/0000");
 $( "#procedimento" ).mask("000000000000");
  $( "#prazo" ).mask("000");
	$( ".hora_mask" ).mask("00:00", {placeholder: "__:__"});	
});
 	$('.recarregarButton').on('click',function(){
		console.log('recarregar');
		alert_boot('wait','Recarregando página...'); 
	})
 $( ".recebido" ).datepicker({
	dateFormat: 'dd/mm/yy',
	dayNames: ['Domingo','Segunda','Terça','Quarta','Quinta','Sexta','Sábado'],
	dayNamesMin: ['D','S','T','Q','Q','S','S','D'],
	dayNamesShort: ['Dom','Seg','Ter','Qua','Qui','Sex','Sáb','Dom'],
	monthNames: ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'],
	monthNamesShort: ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'],
	nextText: 'Próximo',
	prevText: 'Anterior',
  maxDate: 0
});
/*********************************************Apenas Numeros******************************************/  
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
/*********************************************Checkbox reitera******************************************/
  $('.reitera').change(function(){
  if ($(this).is(':checked')) {
 	$("#oficio_n_oficio").removeAttr('disabled');
  }else{
  	$("#oficio_n_oficio").attr('disabled','disabled').val('');
  	
  	}
  });
/***********************************************CHECKBOX PRAZO*******************************************************/	
 $(document).on('focus','#oficio_numero',function(){
    $(this).attr('placeholder','205/2017');
 });
	$(document).on('change','.checkbox_valor',function(){
		var muda = $(this).val();
		if(muda == '1'){
			$('.input_valor').show('slow').val('');
			$('#oficio_numero').removeAttr('disabled');
			console.log(muda)
		}else{
			$('.input_valor').hide('slow').val('');
			$('#oficio_numero').attr('disabled','disabled').val(' ').attr('placeholder','205/2017');
		}
	});
	
	$(document).on('focus','.informa_num_oficio',function(){
	
	$(this).mask("0000/0000",{reverse: true});
		
		});
/*********************************************Autocomplete*****************************************/
	 $(document).on('keyup',".assunto",function(){
			  $(".assunto").autocomplete({
		       		source: <?php  echo $grifo->jsonAutocomplete('assunto');  ?>
			 })
		});
	 $(document).on('keyup',"#oficio_numero",function(){
			 $( "#oficio_numero" ).autocomplete({
			  source: <?php  echo $grifo->jsonAutocomplete('numero');  ?>
			 });
		});
$(document).on('keyup',".destinatario",function(){
			 $( ".destinatario" ).autocomplete({
			  source: <?php  echo $grifo->jsonAutocomplete('destinatario');  ?>
			 });
		});
$(document).on('keyup',".interessado",function(){
			  $(".interessado").autocomplete({
		       		source: <?php  echo $grifo->jsonAutocomplete('interessado');  ?>
			 })
		});   
	/********************************Reseta as divs de detalhe e editar ao alternar tabs**********************/	 
	$('a.tabAberto').click(function(){
		selecioneDetalhe('.show_detalhes');
		selecioneEditar('.detalhes_editar');
	})
	
	$('a.tabFechado').click(function(){
		selecioneDetalhe('.show_detalhes');
		selecioneEditar('.detalhes_editar');
	})
//***********************************************Forms*******************************************************/
	$(document).on('submit','.forms_todos',function(event){//salva os forms inserir e editar	
	var id_form = $(this).attr('id');
	var url_inline = 'assincs/expedientes.php';
	var dados = $(this).serializeArray();
	var id = $(this).find('input[name="modulo"]').val();
	var dadosSubmit= {tipo: id, value: dados};
	var retorno = $(".retorno").html();
	console.log(dados);
 var ultimo_inserido = $(".informa_num_oficio").val();
$('.ultimo_inserido').detach();
		ajaxCustom('submit','',url_inline, dadosSubmit,
							  function(resposta){
								if (resposta != false) {
									console.log('submit: '+resposta);									
								}
							  },
							  function(){
							    alert_boot('wait','Salvando...'); 
							    console.log("indo");
							  },
							  function(){				
								hide_div('.alert_salvo');
								$('.campos_alterar').each(function(){
									$(this).val('');
								});								
								$('.box-header').append('<div class="alert alert-success text-center mx-auto ultimo_inserido w-25 mt-2">Último inserido: '+ultimo_inserido+'</div>');
							    alert_boot('alert','Registro salvo com sucesso!','','','','',false);    
							   
							  });
         
		
	  event.preventDefault();
	});	 
	



//*******************************************************************************************************************************//	

		
	});
</script>
<div class="container"><?php echo $form_inserir; ?></div>
