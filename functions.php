<?php
include dirname (__DIR__) . '/vendor/autoload.php';
global $authGrifo;
       // Using Medoo namespace
use Medoo\Medoo;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Helper\Sample;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Shared\StringHelper;
use KubAT\PhpSimple\HtmlDomParser;
use mikehaertl\shellcommand\Command;

class Grifo{
 
 var $database;
 var $biblioteca;

public function __construct(){


     // Initialize
   $this->database = new Medoo([
         'database_type' => 'mysql',
         'database_name' => 'grifo',
         'server' => 'localhost',
         'username' => 'root',
         'password' => ''
     ]);
 
    $this->biblioteca = new Biblioteca\Custom();
 
 

 }
 
public function get_options($option_name, $option_value=''){
 
 switch(true){
  case strlen($option_value) > 0:
     $where = "WHERE option_name = '$option_name' AND option_value ='$option_value'";
     break;
  case strlen($option_name) > 0:
     $where = "WHERE option_name = '$option_name'";
     break;
  default:
     $where = "";
      break;
 }
  $option = $this->database->query("SELECT * FROM options $where")->fetchAll();  
 foreach($option as $opt){
  $tion[] = $opt['option_value'];
 }
 return $tion;
 
}

public function getError( $msg, $level = 'info', $file = 'logs/processo.log' ){
    // variável que vai armazenar o nível do log (INFO, WARNING ou ERROR)
    $levelStr = '';

    // verifica o nível do log
    switch ( $level )
    {
        case 'info':
            // nível de informação
            $levelStr = 'INFO';
            break;

        case 'warning':
            // nível de aviso
            $levelStr = 'WARNING';
            break;

        case 'error':
            // nível de erro
            $levelStr = 'ERROR';
            break;
    }

    // data atual
    $date = date( 'Y-m-d H:i:s' );

    // formata a mensagem do log
    // 1o: data atual
    // 2o: nível da mensagem (INFO, WARNING ou ERROR)
    // 3o: a mensagem propriamente dita
    // 4o: uma quebra de linha
    $msg = sprintf( "[%s] [%s]: %s%s", $date, $levelStr, $msg, PHP_EOL );

    // escreve o log no arquivo
    // é necessário usar FILE_APPEND para que a mensagem seja escrita no final do arquivo, preservando o conteúdo antigo do arquivo
   return file_put_contents( dirname(__FILE__).'/'.$file, $msg, FILE_APPEND );
}
 
public function array_search_id($search_value, $array, $id_path = array('')){ 
      
    if(is_array($array) && count($array) > 0) { 
          
        foreach($array as $key => $value) { 
  
            $temp_path = $id_path; 
              
            // Adding current key to search path 
            array_push($temp_path, $key); 
  
            // Check if this value is an array 
            // with atleast one element 
            if(is_array($value) && count($value) > 0) { 
                $res_path = self::array_search_id( 
                        $search_value, $value, $temp_path); 
  
                if ($res_path != null) { 
                    return $res_path; 
                } 
            } 
            else if($value == $search_value) { 
                return $temp_path[1]; 
                
            } 
        } 
    } 
      
    return null; 
} 

public function dataGrifo($data){
 
   if($data > 0){
     $unixDate = ($data - 25569) * 86400;
     $r = gmdate("Y-m-d", $unixDate);
   }else{
     $r = '00-00-0000';
   }

    return $r;

}

public function phpspreadsheet($file){
  
$inputFileName = $file;

$helper = new Sample();

    try {
      $reader = IOFactory::createReaderForFile($inputFileName);
      $reader->setReadDataOnly(TRUE);
      $spreadsheet = $reader->load($inputFileName);
      return $spreadsheet->getActiveSheet()->toArray('-', true, true, true); 

    } catch(\PhpOffice\PhpSpreadsheet\Reader\Exception $e) {
        die('Error loading file: '.$e->getMessage());
    }
} 
/****************************************************************************************/ 

public function upload($inputFile,$tipo=''){ 
 global $authGrifo;
 
 $pj = $authGrifo['promotoria'];
 $id_user = $authGrifo['user'];
    
    if(isset($inputFile)){
     
    /**
    ****Verifica se a extensão é xlsx
    **/
    $ext = pathinfo($inputFile['name'], PATHINFO_EXTENSION);
    $cool_extensions = array('xlsx');
   
    
    if(in_array($ext, $cool_extensions)){
     
         date_default_timezone_set("Brazil/East"); //Definindo timezone padrão

         $dir = 'uploads/'; //Diretório para uploads
     
         $inputFileReady = strtolower($inputFile['name']);

         move_uploaded_file($inputFile['tmp_name'], $dir.$inputFileReady); //Fazer upload do arquivo


        $sheetData = self::phpspreadsheet($dir.$inputFileReady);//tras os dados da tabela como array

        $value = self::array_search_id('Número do Autos', $sheetData);//verifica se a linha com o cabeçalho existe 

        foreach(range(1,$value) as $arr){//retira as arrays que não importam
             unset($sheetData[$arr]); 
        } 
     
unlink($dir.$inputFileReady);
     
     //isnere o uatualiza******************************/
     
 $this->database->update("procedimentos", ['ultima_atualizacao' => 0],["promotoria" =>$pj]);//atualiza a ultima atualizacao
     
 $tabela = $this->database->query("SELECT *  FROM procedimentos")->fetchAll();  
     
foreach($sheetData as $dado){  
   $autos_tabela = $dado['A'];  
   $key = self::array_search_id($autos_tabela, $tabela);
   $id = $this->database->get("procedimentos","Id",['numero_dos_autos' => $autos_tabela]);  
                if($id > 0) { 
                    $array_update = array(
                   'id_usuario'=> $id_user,
                   'promotoria'=> $pj,
                   'classe'=> $dado['B'],
                   'taxonomia' => $dado['C'],
                   'data_registro' => self::dataGrifo($dado['D']),
                   'data_posse' => self::dataGrifo($dado['E']),
                   'ultimo_movimento' => self::dataGrifo($dado['F']),
                   'numero_movimentos' => $dado['G'],
                   'data_instauracao' => self::dataGrifo($dado['H']),
                   'data_prorrogacao' => self::dataGrifo($dado['I']),
                   'ultima_atualizacao' => 1
                      );  

                  $this->database->update("procedimentos", $array_update,["Id" => $id]);                 
              
                 self::getError(json_encode(array('tipo'=>'update','mensagem'=>json_encode($array_update))),'error','logs/autos.log');

             }else{
               $array_insert = array(
                   'Id'=> '',
                   'id_usuario'=> $id_user,
                   'promotoria'=> $pj,
                   'numero_dos_autos'=> intval($dado['A']),
                   'classe'=> $dado['B'],
                   'taxonomia' => $dado['C'],
                   'data_registro' => self::dataGrifo($dado['D']),
                   'data_posse' => self::dataGrifo($dado['E']),
                   'ultimo_movimento' => self::dataGrifo($dado['F']),
                   'numero_movimentos' => $dado['G'],
                   'data_instauracao' => self::dataGrifo($dado['H']),
                   'data_prorrogacao' => self::dataGrifo($dado['I']),
                   'ultima_atualizacao' => 1
                   );
                $this->database->insert('procedimentos',$array_insert);
                 
                self::getError(json_encode(array('tipo'=>'insert','mensagem'=>json_encode($array_insert))),'error','logs/autos.log');
                 

              }
  
 }

     $tabela_historico = $this->database->query("SELECT *  FROM procedimentos WHERE ultima_atualizacao = '0' AND promotoria = '$pj'")->fetchAll(); 
     
     foreach($tabela_historico as $dado){
          unset($dado['Id']);
          $this->database->insert('procedimentos_historico',$dado);
     }
     
          $this->database->delete('procedimentos',['ultima_atualizacao' => 0, 'promotoria'=>$pj]);
     
      switch($tipo){
       case 'array':
        $return = $sheetData;
        break;
        default:
        $return = '<div class="alert alert-success mt-3">Arquivo enviado com sucesso.</div>';
        break;
       } 
          
     }else{
         $return = '<div class="alert alert-danger mt-3">Siga as instruções acima.</div>';
     }
     
  }else{
     $return = '<div class="alert alert-info mt-3">Envie o arquivo do grifo.</div>';
  }

 return $return;
}
/*****************************************************Atena*******************************************/
public function data($string){
 
    $biblioteca = new Biblioteca\Custom(); 
 
    preg_match('/(0[1-9]|1[0-9]|2[0-9]|3[01])[\/-](0[1-9]|1[0-2])[\/-](19[5-9][0-9]|20[0-9][0-9])/',$string,$data);

    $data_inverter = explode("/",$data[0]);

    return $data_inverter[2].'-'. $data_inverter[1].'-'. $data_inverter[0];
 
}

public function movimento($string){ 
   $m = explode(":",$string);
   return trim(end($m));
}
 
public function atena($usuario=1){

$tabela = $this->database->query("SELECT *  FROM procedimentos")->fetchAll();  
$total = count($tabela);
$x=1;  
$temp = dirname(__FILE__).'/tmp/'.date('Ymd').'_'.$usuario.'.txt';
/*
$teste = array('1','2','3','4','5');
$total_teste = 5;
foreach($teste as $t){
  $num = $x++; 
  file_put_contents($temp, $num.'|'.($num/$total_teste).'|'.$total_teste);
  echo $num;
 sleep(2);
}
*/
foreach($tabela as $atena){
 $num = $x++;
 file_put_contents($temp, $num.'|'.($num/$total).'|'.$total);
 
   $html = HtmlDomParser::file_get_html('http://www.mpgo.mp.br/atena_cidadao/exibir/'.$atena['numero_dos_autos'].'/movimentos/decrescente');
 
  if(!empty($html->find('.movimento'))){

   $envolvidos = @trim($html->find('.envolvidos .valor', 0)->plaintext);
   $status = @trim($html->find('.status .texto', 0)->plaintext);
 

    $movimentos = array();
    foreach($html->find('.movimento') as $valor){
       $movimentos[] = array(
        'tipo'=> trim($valor->find('.tipo', 0)->plaintext),
        'orgao'=> self::movimento(trim($valor->find('.orgao', 0)->plaintext)),
        'data_movimento'=> self::data(trim($valor->find('.data', 0)->plaintext))
        );
      
      
     }
       $movimento = array_merge(array('envolvidos' => $envolvidos,'status' => $status),reset($movimentos));
       
       $dab = $this->database->update("procedimentos", $movimento,["Id" => $atena['Id']]);
       // self::getError(json_encode(array('tipo'=>$atena['numero_dos_autos'],'mensagem'=>json_encode($movimento))),'info','logs/movimentos.log');
       
       //echo $num;//json_encode(@array_merge(array('autos'=>$atena['numero_dos_autos'],'envolvidos'=>$envolvidos,'status'=>$status),$movimento));
}
 
}
/**********************************************************************************************/



  
}
 
 public function regularidade($classe,$posse,$inst,$pror){
 global $biblioteca;
   switch(true){
      case ($posse < $inst)://posse mais antiga - novo procedimento
              if($inst<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
                  $inicio = 'prorrogacao';
              }else{
                  $r = $inst;
                  $inicio = 'instauracao';
              }
          break;
      case ($posse > $inst)://posse mais recente - veio encaminhado
             if($posse<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
                  $inicio = 'prorrogacao';
              }else{
                if(isset($pror)){
                 $ja = 1;//se ja houve prorrogacao, mas a posse é mais recente, renova o prazo da prorrogacao com a nova data da posse
                }
                  $r = $posse;
                  $inicio = 'posse';
              }
          break;
      case ($posse == $inst)://instaurado na pj
              if($inst<$pror){
                  $r = $pror;
                  $ja = 1;//ja prorrogado
               $inicio = 'prorrogacao';
              }else{
                  $r = $inst;
               $inicio = 'instauracao';
              }
          break;

  }
 
 $dias = $biblioteca->diffDate($r);

 switch($biblioteca->slugando($classe)){
   case 'noticiadefato':
   if($ja == 1){
       $prazo = 90;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'text-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
   }else{
       $prazo = 30;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'text-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
   }
  break;
   case 'procedimentopreparatorio':
       $prazo = 90;
       $return = ($prazo-$biblioteca->diffDate($r) > 0)?'':'text-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
  break;
    default:
       $prazo = 365;
       $return = (($prazo-$biblioteca->diffDate($r)) > 0)?'':'text-danger';
       $tempo = ($prazo-$biblioteca->diffDate($r));
  break;
 }
 
 return array('css'=>$return,'dias'=>$dias, 'tempo'=>$tempo,'contagem'=>$inicio,'classe'=>$biblioteca->slugando($classe));
 //return array('css'=>$return,'dias'=>$dias);
}
 /**********************************************************Oficio**********************************************/
public function jsonAutocomplete($campo){//cria o json para o autocomplete
 
 $options = $this->database->query("SELECT *  FROM oficios")->fetchAll();   

	foreach($options as $un):
		$retorno[] = $this->biblioteca->maius($un[$campo]);
	endforeach;	
 
 $return = array_unique($retorno);

		return '["'.$this->biblioteca->opcao($return,'","').'"]';

}
}