<?php  
include $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';
$biblioteca = new Biblioteca\Custom();
	//require_once $_SERVER['DOCUMENT_ROOT']."grifo/scripts/htmlToXML/HTMLtoOpenXML.php";

$parser = new HTMLtoOpenXML\Parser();//parser html to xml ins string

function geraWord($post,$template,$file){
 
 if(file_exists($template)){
  
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($template);
    
    $templateProcessor->setValues($post);
    

    header("Content-Description: File Transfer");
    header('Content-Disposition: attachment; filename="' . $file . '"');
    header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
    header('Content-Transfer-Encoding: binary');
    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
    header('Expires: 0');
 return  $templateProcessor->saveAs("php://output");
}else{
 return 'A url não existe - '.$template;
}
 
}


if($_POST['genero'] == 0){
 $genero_artigo = "o";
 $genero_artigo_crase = 'ao'; 
 $sra = 'sr.';
}else{
 $genero_artigo = "a";
 $genero_artigo_crase = 'à'; 
 $sra = 'sra.';
}

$toOpenXML = $parser->fromHTML($_POST['declaracoes']);

/***********************************************************/
$data = date("d").' de '.strtolower($biblioteca->meses(date('m'))).' de '.date("Y");
$post = array(
  "send"=>$_POST['send'],
  "tipo"=> $_POST['tipo'],
  "procedimento"=> $_POST['procedimento'],
  "portaria"=>  $_POST['portaria'],
  "declarante"=> $_POST['declarante'],
  "celular1"=> $_POST['celular1'],
  "celular2"=> $_POST['celular2'],
  "email"=> $_POST['email'],
  "interessado"=> $_POST['interessado'],
  "declaracoes"=> $toOpenXML,
  "signatario"=> $_POST['signatario'],
  "data"=> $data,
  "genero_artigo" => $genero_artigo,
  "genero_artigo_crase" => $genero_artigo_crase,
  "sra" => $sra,
  "servico" => $_POST['servico'],
  "portarias" => $_POST['portarias'],
  "sisreg"=> $_POST['sisreg'],
  "sigtap" => $_POST['sigtap'],
  "cns"=>$_POST['cns'],
  "autor" => $_POST['autor'],
  "apenas" => $_POST['apenas'],
  "diagnostico" => $_POST['diagnostico'],
  "medicamento" => $_POST['medicamento']
 );


if(strlen($post['servico'])>2){
 $servico = $post['servico'];
}else{
 $servico = 'Fornecimento de Medicamento';
}

if($post['send'] == '1'){
 
 switch($post['tipo']){
  case 'termo':
   $template = $_SERVER['DOCUMENT_ROOT'] . '/grifo/templates-word/termo_declaracoes/termo_declaracoes.docx';
   $file = 'Termo '.date('Y').'-'.$post['interessado'].'.docx';
   echo geraWord($post,$template,$file);
   break;
 case 'portaria':
   $p = explode('/',$_POST['portaria']);
   $numero_portaria = $p[0].'-'.$p[1];
   $template = $_SERVER['DOCUMENT_ROOT'] . '/grifo/templates-word/'.$post['portarias'].'/portaria_'.$post['portarias'].'.docx';
   $file = 'Portaria-'.$numero_portaria.'-'.$servico.'-'.$post['interessado'].'-'.$post['procedimento'].'.docx';
   echo geraWord($post,$template,ucfirst($file));
   break;
 case 'oficio':
   $destinatario = $_POST['destinatarios'];
   foreach($destinatario as $dest=>$numero){
    if($dest == $post['apenas']){
       $p = explode('/',$numero);
       $numero_oficio = $p[0].'-'.$p[1];
       $template = $_SERVER['DOCUMENT_ROOT'] . '/grifo/templates-word/'.$post['portarias'].'/oficio_'.$dest.'.docx';
       $file = 'Oficio-'.$numero_oficio.'-'.strtoupper($dest).'-'.$servico.'-'.$post['interessado'].'-'.$post['procedimento'].'.docx';
       $post = array_merge(array('numero_oficio'=>$numero),$post);
       return geraWord($post,$template,ucfirst($file));
    }
   }
   break;
 } 
 



}else{
 echo 'nada enviado';
}
