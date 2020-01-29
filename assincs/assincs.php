<?php  

include($_SERVER['DOCUMENT_ROOT'] . 'grifo/parts/includes.php'); 

$biblioteca = new Biblioteca\Custom();

global $database;

$post = $_POST;


switch($post['tipo']){
   case 'oficios_existentes':
     
        $fileList = glob($_SERVER['DOCUMENT_ROOT'] . '/grifo/templates-word/'.$post['value'].'/oficio_*');

        //Loop through the array that glob returned.
        foreach($fileList as $filename){
           //Simply print them out onto the screen.
         $f = explode('_',basename($filename, ".docx"));
           $array[] = $f[1]; 
        }
       
       
  if(count($array) > 0){     
       foreach($array as $dest){
        $inputsgroup[] = '<div class="input-group mr-2 mb-2" style="width:400px;">
               <div class="input-group-prepend">                 
                 <div class="input-group-text">
                   <input type="checkbox" class="oficio_destinatario" aria-label="'.$dest.'" data-target="'.$dest.'">
                 </div>
                 <span class="input-group-text">'.strtoupper($dest).'</span>
               </div>
               <input type="text" class="form-control oficio '.$dest.'" aria-label="'.$dest.'" name="destinatarios['.$dest.']" id="'.$dest.'" placeholder="Número do Ofício" disabled>
                 <div class="input-group-append">
                   <button class="btn btn-dark gerar_oficio '.$dest.'" data-tipo="oficio" data-name="'.$dest.'" disabled>Gerar</button>
                 </div>
             </div>';
         }
       
       $return = '<div class="form-group row">'.$biblioteca->opcao($inputsgroup).'</div>';
  }else{
   $return = '<div class="alert alert-warning">Não há ofício cadastrados para esta portaria</div>';
  }
  break;
}

echo $return;