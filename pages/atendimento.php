<?php
include $_SERVER['DOCUMENT_ROOT'] . '/grifo/classes/formulario-bootstrap.php'; 
$fbootstrap = new FBoot();
$biblioteca = new Biblioteca\Custom();




if($_GET['teste'] == 'on'){
 $url = '?p=atendimento&teste=on';
 echo '<pre>';
 var_dump(array_merge(array('tambem'=>'215'),$_POST));
echo '</pre>';
}else{
 $url = 'http://root-aynoei177396.codeanyapp.com/grifo/assincs/phpoffice.php';
}


/**************************************************************************/

function getDirContents($dir, $filter = '', &$results = array()) {
    $files = scandir($dir);

    foreach($files as $key => $value){
        $path = realpath($dir.DIRECTORY_SEPARATOR.$value); 

        if(!is_dir($path)) {
            if(empty($filter) || preg_match($filter, $path)) $results[] = $path;
        } elseif($value != "." && $value != "..") {
            getDirContents($path, $filter, $results);
        }
    }

    return $results;
} 

$fileList = getDirContents($_SERVER['DOCUMENT_ROOT'] . '/grifo/templates-word/','/portaria/');

        foreach($fileList as $filename){
           //Simply print them out onto the screen.
           $f = explode('_',basename($filename, ".docx"));
           unset($f[0]);
           $array[] = join(' ',$f); 
           //echo join('-',$array);
        }

  if(count($array) > 0){     
       foreach($array as $dest){
            $inputsgroup[] = '<option value="'.$biblioteca->slugando($dest,'_').'">'.strtoupper($dest).'</option>';
         }
  }
?>
<!DOCTYPE html>
<html>    
 <head>
  <title>Atendimento</title>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.js"></script> 
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js" integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js" integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous"></script>
 
<!-- include summernote css/js -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.12/summernote.js"></script>
  
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.11/jquery.mask.js"/></script>
<script type="text/javascript">
$(document).ready(function() {
 $('#declaracoes').summernote({
        placeholder: '',
        tabsize: 2,
        height: 400,
        lang: 'pt-BR'
 });  
 
$( ".data_mask" ).mask("00/00/0000", {placeholder: "__/__/____"});
$( ".hora_mask" ).mask("00:00", {placeholder: "__:__"});

	$( ".data_mask" ).mask("00/00/0000");
  $(".telefone").mask("(00) 0-0000-0000");
	$( ".hora_mask" ).mask("00:00", {placeholder: "__:__"});	

 
 	$(".portaria").mask("0000/0000",{reverse: true});
  $(document).on("keyup",".oficio",function(){
   $(this).mask("0000/0000",{reverse: true});
  })
  $( ".procedimento" ).mask("000000000000");
  $(".cns").mask("000.0000.0000.0000"); 
 
  $('input').keyup(function()
    {
        $(this).val($(this).val().toUpperCase());
    }); 
 
$(document).on('click',".gerar_word",function(){
 var tipo = $(this).data('tipo');
 $('.tipo').val(tipo);  
	var dados = $('#atendimento').serializeArray();
 console.log(dados)
 $('#atendimento').submit();
 
 
});
 
$(document).on('click','.gerar_oficio', function(e) {
 var v = $(this).data('name');
  var tipo = $(this).data('tipo');
 $('.tipo').val(tipo);  
 console.log($('.apenas').val(v));
	var dados = $('#atendimento').serializeArray();
 console.log(dados)
 $('#atendimento').submit();
});
 
$(document).on('change','.oficio_destinatario', function(e) {

  var isDisabled = $(this).is(':checked');

   var target = $(this).data('target');  
    if(isDisabled){
     $('.'+target+'').removeAttr('disabled');

     console.log('remove '+target)
    }else{
     $('.'+target+'').attr('disabled','disabled');
      console.log('disable '+target)
    }
 
})

     $("#portarias").change(function() {
      //loading para popular o select
      $('.btn-portaria').removeAttr('disabled');
      $('.btn-oficio').attr('disabled','disabled');
      var url_inline='assincs/assincs.php';
      var dropdown=$(this).val();
      var dadosSubmit= {tipo: 'oficios_existentes', value: dropdown};
      console.log(dadosSubmit);
      var r = selectAuto(dadosSubmit,'.destinatarios_oficios',url_inline,$(this));

     });
 
});

function selectAuto(fonte,target,url,origin){		
//console.log(fonte+'-'+target+'-'+url+'-'+funcao+'-'+label);
	$(target).html('');
 console.log(target);
   $.ajax({url: url, data: fonte, type: 'POST',
			  success: function(resposta){
								if (resposta !== false) {
									console.log(resposta);
									$(resposta).appendTo(target);
								}
							  },
			  beforeSend: function(){
							    //console.log("script indo");
								formLoading(origin);
								$(target+" option").remove();
								
							  },
			  complete: function(){	
							    formLoading('remove');	
									
							  },
			  error: function (xhr, ajaxOptions, thrownError) {alert("ERROR:" + xhr.responseText+" - "+thrownError);}	 
			});/*ajax*/

}
function formLoading(x,estilo='alert-warning',message='Aguarde...'){//insere um loading abaixo do elemento selecionado, geralmente em dropdown select autopopulate
	if(x != 'remove'){
		$('<div class="wait-loading alert '+estilo+' "><i class="fa fa-spin fa-spinner"></i>'+message+'</div>').insertAfter(x);
	}else{
		$('.wait-loading').remove();
	}
}
</script>
 </head>
 <body>
 <div class="container mx-auto">
  <h4 class="mx-auto text-center">Registrar Atendimento - Padrões</h4>
 <form action="<?php echo $url; ?>" method="post" class="atendimento" id="atendimento">
  
 
  <div class="card interessado border-bottom-0 rounded-0 text-center">
   <div class="card-header"></div>
   <div class="card-body">
    <?php  
         echo $fbootstrap->input_cols(
                      array(
                          array('label'=>'Número do Atena','id'=>'procedimento','name'=>'procedimento','place'=> date('Y').'00000000','classes_input'=>'procedimento','classes_div_input'=>'col-sm-4','html_input'=>'required'),
                          
                       ));
    ?>
   <div class="form-group row">
      <label for="signatario" class="col-sm-2  col-form-label">Gênero do Interessado</label>
      <div class="col-sm-10 text-left">
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="masculino" name="genero" class="custom-control-input" value="0" required>
          <label class="custom-control-label" for="masculino">Masculino</label>
        </div>
        <div class="custom-control custom-radio custom-control-inline">
          <input type="radio" id="feminino" name="genero" class="custom-control-input" value="1" required>
          <label class="custom-control-label" for="feminino">Feminino</label>
        </div>
      </div>
    </div>
        <?php 

   
    echo $fbootstrap->input(array('style'=>'row','label'=>'Interessado','id'=>'interessado','name'=>'interessado','maxlenght'=>'200','html_input'=>'required'));
    echo $fbootstrap->input_cols(
                      array(
                          array('style'=>'row','label'=>'CNS','id'=>'cns','name'=>'cns','classes_input'=>'cns','place'=>'','html_input'=>'required')
                       ));
    ?>
   </div>
  </div>
  <div class="card termo rounded-0">
   <div class="card-header"></div>
   <div class="card-body">
    <?php 
     echo $fbootstrap->input(array('style'=>'row','label'=>'Declarante','id'=>'declarante','name'=>'declarante','classes_input'=>'declarante','maxlenght'=>'200','html_input'=>'required'));
     echo $fbootstrap->input_cols(
                      array(
                          array('label'=>'Celular 1','id'=>'celular1','name'=>'celular1','place'=> '62 9-9999-9999','classes_input'=>'telefone','classes_div_input'=>'col-sm-4','html_input'=>'required'),
                          array('label'=>'Celular 2','id'=>'celular2','name'=>'celular2','place'=> '62 9-9999-9999','classes_input'=>'telefone','classes_div_input'=>'col-sm-4','html_input'=>'')
                       )); 
    echo $fbootstrap->input(array('style'=>'row','label'=>'Email','type'=>'email','id'=>'email','name'=>'email','classes_input'=>'email','place'=>'declarante@email.com','html_input'=>''));
    ?>
   <?php  
    $d = date('d');
    $extenso = ($d<11)?'Ao  '.$biblioteca->ordinal($d).' dia':'Aos  '.$biblioteca->porExtenso($d);
    $pretexto = $extenso.' do mês de '.strtolower($biblioteca->meses(date('m'))).' do ano de '.date('Y').', compareceu a 9ª Promotoria de Justiça de Anápolis/GO, o(a) DECLARANTE e expôs o seguinte:
    NADA mais havendo a declarar, vai, depois de lido e achado conforme, devidamente assinado por mim ______________(Daniel Felix da Silva), que o digitei e pelo(a) declarante.';
    echo $fbootstrap->textarea(array('label'=>'Termo de Declarações','id'=>'declaracoes','name'=>'declaracoes','rows'=>'10','value'=>$pretexto));
    ?>
    
   </div>
   <div class="text-center"><button class="btn btn-dark gerar_word btn-termo" data-tipo="termo">Gerar Termo de Declarações</button></div>
  </div>
  <div class="card signatario rounded-0">
   <div class="card-header"></div>
   <div class="card-body">
    <?php       echo $fbootstrap->input_cols(
                      array(
                          array('style'=>'row','label'=>'Signatario','id'=>'signatario','name'=>'signatario','classes_input'=>'signatario','place'=>'','html_input'=>'required', "value"=>"LUIS FERNANDO FERREIRA DE ABREU"),
                          array('style'=>'row','label'=>'Sigla do Servidor','id'=>'autor','name'=>'autor','classes_div_input'=>'col-sm-2', 'help'=>'Máximo 3 letras', 'maxlength'=>'3','html_input'=>'required')
                       ));
    ?>
   </div>
  </div>
  <div class="card portarias rounded-0">
   <div class="card-header"></div>
   <div class="card-body">
    <?php  
         
          echo $fbootstrap->input(array('style'=>'row','label'=>'Número da Portaria','id'=>'portaria','name'=>'portaria','classes_input'=>'portaria','place'=>'251/2019','html_input'=>'required')); 
    ?>
    <div class="form-group row">
      <label for="signatario" class="col-sm-2  col-form-label">Portarias</label>
      <div class="col-sm mr-0 pr-0">
       <select class="custom-select mb-3" name="portarias" id="portarias"><option >Selecione uma Portaria</option><?php echo $biblioteca->opcao($inputsgroup); ?></select>
     </div>
     <div class="col-sm-3 ml-0 pl-0">
        <button class="btn btn-dark gerar_word btn-portaria" data-tipo="portaria" disabled>Gerar Portaria</button>
     </div>
    </div>
    <?php
       echo $fbootstrap->input_cols(
                      array(
                          array('style'=>'row','label'=>'Diagnosticado(a) com','id'=>'diagnostico','name'=>'diagnostico','classes_input'=>'diagnostico','place'=>'','html_input'=>'', "value"=>"", 'classes_div_input'=>''),
                          array('style'=>'row','label'=>'Necessita do medicamento','id'=>'medicamento','name'=>'medicamento', 'help'=>'', 'html_input'=>'')
                       ));
        echo $fbootstrap->input_cols(
                      array(
                          array('style'=>'row','label'=>'Procedimento/Especialidade','id'=>'servico','name'=>'servico', 'help'=>'', 'html_input'=>'','classes_div_input'=>'col-sm-4','classes_label'=>''),
                          array('style'=>'row','label'=>'Codigo SIGTAP','id'=>'sigtap','name'=>'sigtap','classes_input'=>'sigtap numero','place'=>'','html_input'=>'', "value"=>"", 'classes_div_input'=>'col-sm-2','classes_label'=>'col-sm-1'),                          
                          array('style'=>'row','label'=>'SISREG','id'=>'sisreg','name'=>'sisreg','classes_input'=>'sisreg', 'place'=>'','html_input'=>'required', 'classes_div_input'=>'col-sm-2','classes_label'=>'col-sm-1')
                       ));
    ?>
      <div class="form-group row">
      <label for="signatario" class="col-sm-2  col-form-label">Destinatários</label>
      <div class="col-sm-10 destinatarios_oficios">
     </div>
    </div>
   </div>
  </div>

  <div class="card footer border-bottom-0 rounded-0 text-center">
   <div class="card-footer">
   <input type="hidden" name="apenas" class="apenas" value="">
    <input type="hidden" value="1" name="send">
    <input type="hidden" value="" name="tipo" class="tipo">

   </div>
  </div>
 </form>
 </div>
 
 </body>
</html>