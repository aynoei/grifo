<?php 

//*****************Classes de transformação de caracteres********************************************************************************//	
	
	
		
class Biblioteca{
	
		//maiuscula e minuscula; 1 = MAIUSCULA, 2 = minuscula
 public function Convertem($term, $tp) {
			if ($tp == "1") $palavra = strtr(strtoupper($term),"àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ","ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß");
			elseif ($tp == "0") $palavra = strtr(strtolower($term),"ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖ×ØÙÜÚÞß","àáâãäåæçèéêëìíîïðñòóôõö÷øùüúþÿ");
			return $palavra;
} 
	
//todas maiusculas	
  public function maius($a){
	$b = self::convertem($a,1);
	return $b;
}
//todas minusculas
  public function minus($a){
	$b = self::convertem($a,0);
	return $b;
}
//limita caracteres fixo
public function limita_char($limita){
	$total_char = strlen($limita);
	$resposta = ($total_char>31)?substr($limita,0,31).'..':$limita;
	return $resposta;
}
//limita caracteres personalizado
public function _limi($limita,$ate){
	$total_char = strlen($limita);
	$resposta = ($total_char>$ate)?substr($limita,0,$ate).'..':$limita;
	return $resposta;
}
	
public function seo_friendly_url($string){
    $string = str_replace(array('[\', \']'), '', $string);
    $string = preg_replace('/\[.*\]/U', '', $string);
    $string = preg_replace('/&(amp;)?#?[a-z0-9]+;/i', '-', $string);
    $string = htmlentities($string, ENT_COMPAT, 'utf-8');
    $string = preg_replace('/&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);/i', '\\1', $string );
    $string = preg_replace(array('/[^a-z0-9]/i', '/[-]+/') , '-', $string);
    return strtolower(trim($string, '-'));
}
//*****************Classes para datas********************************************************************************//	
//tras a data de hoje
public function dataHoje($tipo ='banco') {
    $tz_object = new DateTimeZone('Brazil/East');
    //date_default_timezone_set('Brazil/East');

    $datetime = new DateTime();
    $datetime->setTimezone($tz_object);
    return ($tipo === 'banco')?$datetime->format('Y-m-d'):self::cliente($datetime->format('Y-m-d'));
}
//data_user_para_mysql
  public function banco($y){
	  	
        $data_inverter = explode("/",$y);
        $x = $data_inverter[2].'-'. $data_inverter[1].'-'. $data_inverter[0];
        return $x;
}
//data_mysql_para_user
  public function cliente($y){
        $data_inverter = explode("-",$y);
        $x = $data_inverter[2].'/'. $data_inverter[1].'/'. $data_inverter[0];
        return $x;
}	

public function trasUmData($data,$tipo='Y') {
    $date = DateTime::createFromFormat("Y-m-d", $data);
    return $date->format($tipo);//Y ou m ou d
}


//calcula a data a partir do dia + o prazo em horas que se deseja
public function expira_dia($dia,$prazo){
	list($y,$m,$d) = explode("-",$dia);
	$proxima = @mktime(date('H'), date('i'), date('s'), $m, $d+$prazo, $y);
	  $data_server = date("Y-m-d", $proxima);
	  return $data_server;
}

//calcula a data a partir do mes + o prazo em meses que se deseja
public function expira_mes($mes,$prazo){
	list($y,$m,$d) = explode("-",$mes);
	$proxima = @mktime(date('H'), date('i'), date('s'), $m+$prazo, $d, $y);
	  $data_server = date("Y-m-d", $proxima);
	  return $data_server;
}
//calcula a data a partir do mes - o prazo em meses que se deseja
public function atras_mes($mes,$prazo){
	list($y,$m,$d) = explode("-",$mes);
	$proxima = @mktime(date('H'), date('i'), date('s'), $m-$prazo, $d, $y);
	  $data_server = date("Y-m-d", $proxima);
	  return $data_server;
}
//calcula a hora  + o prazo em horas que se deseja
public function expira_hora($dia,$prazo){
	list($y,$m,$d) = explode("-",$dia);
	$proxima = @mktime(date('H')+$prazo, date('i'), date('s'), $m, $d, $y);
	  $data_server = date("Y-m-d", $proxima);
	  return $data_server;
}

//calcula a data a partir do dia - o prazo em horas que se deseja
public function antecipa($dia,$prazo){
	list($y,$m,$d) = explode("-",$dia);
	$proxima = @mktime(date('H'), date('i'), date('s'), $m, $d-$prazo, $y);
	  $data_server = date("Y-m-d", $proxima);
	  return $data_server;
}
//calcula a data a partir do dia + o prazo em dias que se deseja
public function expira_data($da,$days) {
	 $date = str_replace("-","",$da);
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday + $days, $thisyear );
     $nova = strftime("%Y%m%d", $nextdate);
	 $y = substr ( $nova, 0, 4 );
	 $m = substr ( $nova, 4, 2 );
	 $d = substr ( $nova, 6, 2 );
	 return $y."-".$m."-".$d;
	 
}
//calcula a data a partir do dia - o prazo em dias que se deseja
public function antecipa_data($da,$days) {
	 $date = str_replace("-","",$da);
     $thisyear = substr ( $date, 0, 4 );
     $thismonth = substr ( $date, 4, 2 );
     $thisday =  substr ( $date, 6, 2 );
     $nextdate = mktime ( 0, 0, 0, $thismonth, $thisday - $days, $thisyear );
     $nova = strftime("%Y%m%d", $nextdate);
	 $y = substr ( $nova, 0, 4 );
	 $m = substr ( $nova, 4, 2 );
	 $d = substr ( $nova, 6, 2 );
	 return $y."-".$m."-".$d;
}
//****acrescenta o zero a dias menores que 10
   public function dias_zero($a){
	   switch($a){
		   case 1:$d = "01";break;
		   case 2:$d = "02";break;
		   case 3:$d = "03";break;
		   case 4:$d = "04";break;
		   case 5:$d = "05";break;
		   case 6:$d = "06";break;
		   case 7:$d = "07";break;
		   case 8:$d = "08";break;
		   case 9:$d = "09";break;
		   case 10:$d = "10";break;
		   case 11:$d = "11";break;
		   case 12:$d = "12";break;		   
	   }
	   return $d;
   }
//tras a semana atual considerando o dia de hoje	  
 public function diasemana($data) {
			$ano =  substr("$data", 0, 4);
			$mes =  substr("$data", 5, -3);
			$dia =  substr("$data", 8, 9);
		
			$num = date("w", mktime(0,0,0,$mes,$dia,$ano) );
		 
				$inicio =  self::antecipa_data($data,($num));
				$fim =  self::expira_data($data,(6-$num));
				$diasemana = $inicio."_".$fim;
				
			return $diasemana;
}
//diferenca entre Anos, Meses, Dias, Horas e Minutos
public function diffDate($d2, $type='D', $sep='-')
{
 $d1 = self::dataHoje();
 $d1 = explode($sep, $d1);
 $d2 = explode($sep, $d2);
 switch ($type)
 {
 case 'A':
 	$X = 31104000;
 break;
 case 'M':
 	$X = 2592000;
 break;
 case 'D':
 	$X = 86400;
 break;
 case 'H':
 	$X = 3600;
 break;
 case 'MI':
 	$X = 60;
 break;
 default:
 	$X = 1;
 break;
 }
 return @floor(((mktime(0,0,0,$d1[1],$d1[2],$d1[0])-mktime(0,0,0,$d2[1],$d2[2],$d2[0]))/$X));
}	

public function meses($a){//nome dos meses
	switch($a){
		case '1': $mes = 'Janeiro'; break; case '2': $mes = 'Fevereiro'; break; case '3': $mes = 'Março'; break; case '4': $mes = 'Abril'; break; case '5': $mes = 'Maio'; break; case '6': $mes = 'Junho'; break; case '7': $mes = 'Julho'; break; case '8': $mes = 'Agosto'; break; case '9': $mes = 'Setembro'; break; case '10': $mes = 'Outubro'; break; case '11': $mes = 'Novembro'; break; case '12': $mes = 'Dezembro'; break; 
	}
	return $mes;
}

public function caso_vazio($string,$padrao){
	
	if(strlen(trim($string))>0):$op = trim($string);else:$op = $padrao;endif;	
	return $op;	
	
}
//CALCULANDO DIAS NORMAIS---------------------------------------------Dias Uteis
      //LISTA DE FERIADOS NO ANO
public function Feriados($ano,$posicao){
         $dia = 86400;
         $datas = array();
         $datas['pascoa'] = easter_date($ano);
         $datas['sexta_santa'] = $datas['pascoa'] - (2 * $dia);
         $datas['carnaval'] = $datas['pascoa'] - (47 * $dia);
         $datas['corpus_cristi'] = $datas['pascoa'] + (60 * $dia);
         $feriados = array (
            '01/01',
            '02/02', // Navegantes
            date('d/m',$datas['carnaval']),
            date('d/m',$datas['sexta_santa']),
            date('d/m',$datas['pascoa']),
            '21/04',
            '01/05',
            date('d/m',$datas['corpus_cristi']),
            '20/09', // Revolução Farroupilha \m/
            '12/10',
            '02/11',
            '15/11',
            '25/12',
         );
         
      return $feriados[$posicao]."/".$ano;
      }      

      //FORMATA COMO TIMESTAMP
public function dataToTimestamp($data){
         $ano = substr($data, 6,4);
         $mes = substr($data, 3,2);
         $dia = substr($data, 0,2);
      return mktime(0, 0, 0, $mes, $dia, $ano);  
      } 

      //SOMA 01 DIA
public function Soma1dia($data){
         $ano = substr($data, 6,4);
         $mes = substr($data, 3,2);
         $dia = substr($data, 0,2);
      return   date("d/m/Y", mktime(0, 0, 0, $mes, $dia+1, $ano));
      }
      
public function SomaDiasUteis($xDataInicial,$xSomarDias){
         for($ii=1; $ii<=$xSomarDias; $ii++){
            
            $xDataInicial=self::Soma1dia($xDataInicial); //SOMA DIA NORMAL
            
            //VERIFICANDO SE EH DIA DE TRABALHO
            if(date("w", self::dataToTimestamp($xDataInicial))=="0"){
               //SE DIA FOR DOMINGO OU FERIADO, SOMA +1
               $xDataInicial=self::Soma1dia($xDataInicial);
               
            }else if(date("w", self::dataToTimestamp($xDataInicial))=="6"){
               //SE DIA FOR SABADO, SOMA +2
               $xDataInicial=self::Soma1dia($xDataInicial);
               $xDataInicial=self::Soma1dia($xDataInicial);
               
            }else{
               //senaum vemos se este dia eh FERIADO
               for($i=0; $i<=12; $i++){
                  if($xDataInicial==self::Feriados(date("Y"),$i)){
                     $xDataInicial=self::Soma1dia($xDataInicial);
                  }
               }
            }
         }
      return $xDataInicial;
      }
/*********************************************************/
public  function validaData($date, $traco = '-', $format = 'Y-m-d'){
    $d = DateTime::createFromFormat($format, $date);
    $return = $d && $d->format($format) == $date;
    if($return == true):
        return $date;
     else:
        return $traco;
     endif;
	
}
	
//---------------------------------------------Calcular Idade-----------------------------------------//
public function CalcularIdade($nascimento)
{

$birthday = self::banco($nascimento);


    $age = strtotime($birthday);
    
    if($age === false){ 
        return false; 
    } 
    
    list($y1,$m1,$d1) = explode("-",date("Y-m-d",$age)); 
    
    $now = strtotime("now"); 
    
    list($y2,$m2,$d2) = explode("-",date("Y-m-d",$now)); 
    
    $age = $y2 - $y1; 
    
    if((int)($m2.$d2) < (int)($m1.$d1)) 
        $age -= 1; 
        
    return $age; 

 
  }
//**********************Funcao para gerar lista de checkbox banco****************************************//	

public function opcao($opcao,$separador=''){
$campos = "";
// esse campo tem o valor que será adicionado para separar cada item enviado
$adiciona = $separador;
// esse é um valor que vai ser incrementado, para adicionar ou não o $adiciona ( vírgula para separar os itens )
$mais = 0;
if(isset($opcao)) {
// separo cada e armazeno em $valor
foreach($opcao AS $valor) {
// incremento $mais, 0, 1, 2, ...
$mais++;
// aqui eu faço a adição ou não da vírgula para separar cada item
// além de mandar os valores para $campos
if($mais < count($opcao)) {
$campos.=$valor.$adiciona;

} else {
$campos.=$valor;

} // fim do if

} // fim do foreach

}else{
	$campos = " ";
}// fim do if isset
return $campos;
}


//*******************************************Numeros*********************************************************//
public function ordinal($x){
	switch($x){
		case 1:$num = "primeiro";break;
		case 2:$num = "segundo";break;
		case 3:$num = "terceiro";break;
		case 4:$num = "quarto";break;
		case 5:$num = "quinto";break;
		case 6:$num = "sexto";break;
		case 7:$num = "sétimo";break;
		case 8:$num = "oitavo";break;
		case 9:$num = "nono";break;
		case 10:$num = "décimo";break;
	}
	return $num;
}

//extenso com valor monetario
public function extenso($valor=0, $maiusculas=false, $moeda) {
        // verifica se tem virgula decimal
        if (strpos($valor, ",") > 0) {
                // retira o ponto de milhar, se tiver
                $valor = str_replace(".", "", $valor);

                // troca a virgula decimal por ponto decimal
                $valor = str_replace(",", ".", $valor);
        }
		
		if($moeda==1){
			$singular = array("centavo", "real", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
			$plural = array("centavos", "reais", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
		}else{
			$singular = array("", "", "mil", "milhão", "bilhão", "trilhão", "quatrilhão");
			$plural = array("", "", "mil", "milhões", "bilhões", "trilhões","quatrilhões");
		}

        $c = array("", "cem", "duzentos", "trezentos", "quatrocentos",
                "quinhentos", "seiscentos", "setecentos", "oitocentos", "novecentos");
        $d = array("", "dez", "vinte", "trinta", "quarenta", "cinquenta",
                "sessenta", "setenta", "oitenta", "noventa");
        $d10 = array("dez", "onze", "doze", "treze", "quatorze", "quinze",
                "dezesseis", "dezessete", "dezoito", "dezenove");
        $u = array("", "um", "dois", "três", "quatro", "cinco", "seis",
                "sete", "oito", "nove");

        $z = 0;

        $valor = @number_format($valor, 2, ".", ".");
        $inteiro = explode(".", $valor);
        $cont = count($inteiro);
        for ($i = 0; $i < $cont; $i++)
                for ($ii = strlen($inteiro[$i]); $ii < 3; $ii++)
                $inteiro[$i] = "0" . $inteiro[$i];

        $fim = $cont - ($inteiro[$cont - 1] > 0 ? 1 : 2);
        $rt = '';
        for ($i = 0; $i < $cont; $i++) {
                $valor = $inteiro[$i];
                $rc = (($valor > 100) && ($valor < 200)) ? "cento" : $c[$valor[0]];
                $rd = ($valor[1] < 2) ? "" : $d[$valor[1]];
                $ru = ($valor > 0) ? (($valor[1] == 1) ? $d10[$valor[2]] : $u[$valor[2]]) : "";

                $r = $rc . (($rc && ($rd || $ru)) ? " e " : "") . $rd . (($rd &&
                        $ru) ? " e " : "") . $ru;
                $t = $cont - 1 - $i;
                $r .= $r ? " " . ($valor > 1 ? $plural[$t] : $singular[$t]) : "";
                if ($valor == "000"

                )$z++; elseif ($z > 0)
                $z--;
                if (($t == 1) && ($z > 0) && ($inteiro[0] > 0))
                $r .= ( ($z > 1) ? " de " : "") . $plural[$t];
                if ($r)
                $rt = $rt . ((($i > 0) && ($i <= $fim) &&
                        ($inteiro[0] > 0) && ($z < 1)) ? ( ($i < $fim) ? ", " : " e ") : " ") . $r;
        }

        if (!$maiusculas) {
                return($rt ? $rt : "zero");
        } elseif ($maiusculas == "2") {
                return (strtoupper($rt) ? strtoupper($rt) : "Zero");//maiuscula
        } else {
                return (ucwords($rt) ? ucwords($rt) : "Zero");//minuscula
        }
        }
//*****************************************MONETARIOS****************************************************//

//converte o valor numerico em moeda

public function _precos($p){
			@$_valor = number_format($p ,2,',','.');
			return $_valor;
		}
public function _moeda($p){
			$a = str_replace(".","",$p);
			$b = str_replace(",",".",$a);
			return $b;
		}

//***************************************Rotinas Basicas*****************************************//
function string_array($array,$tipo='',$valor='',$chave=''){
	
	switch($tipo):
	default:
		foreach($array as $ar):
			$return[] = $ar;
		endforeach;
	break;
	case 'coluna':
		foreach($array as $ar):
			$return[] = $ar->$valor;
		endforeach;
	break;
	case 'keyvalue':
		foreach($array as $ar):
			$re[$ar->$chave] = $ar->$valor;
		endforeach;
		$return = self::multi_uma($re);
	break;
	case 'jsonJquery':			
			foreach ($array as $value) {
				$d[] = array($value["name"] => $value["value"]);
			}
			$return = self::multi_uma($d);
	break;
	endswitch;
	
	return $return;
}
	
public function vardump($x){

$vardump = "<pre>".var_dump($x)."</pre>";

return $vardump;

}

public function virgula($termo,$opcao){

	return strlen(trim($termo))>1?$opcao.$termo.",":"";

}

//***************************************Operando com Arrays*****************************************//

public function apenas($busca,$onde){		//retira de uma array valores de outras, retornando o resultado sem elas
		
		foreach($busca as $gr):
			
				$key = array_search($gr,$onde);
				if($key!==false){
					unset($onde[$key]);
				}
		endforeach;
		
		
		return $onde;
	}
	

public function multi_uma($array){//transforma uma array multi em array single
				 if (!is_array($array)) { 
			return false; 
		  } 
		  $result = array(); 
		  foreach ($array as $key => $value) { 
			if (is_array($value)) { 
			  $result = array_merge($result, self::multi_uma($value)); 
			} else { 
			  $result[$key] = $value; 
			} 
		  } 
		  return $result; 
    }	
 
public function vpost($post){//verifica se uma array possui valor vazio e retorna apenas as com valor valido
	foreach($post as $key => $value):
		if(strlen(trim($value))>0):$op[] = trim($value);endif;
	endforeach;	
	return $op;
}
	
public function jsonAutocomplete($campo){//cria o json para o autocomplete
	global $post, $wpdb, $biblioteca, $tabela_orgao;
	$tab_ela = get_post_meta($post->ID, 'tabela_orgao', true );
	$arrays = self::string_array($wpdb->get_results( 'SELECT '.$campo.' FROM '.$tab_ela .' '),'coluna',$campo);
	$unique = @array_unique($arrays);//gerava um Fatal Error, 'campo vazio'
	
	foreach($unique as $un):
		$retorno[] = $biblioteca->maius($un);
	endforeach;
	
	return '["'.self::opcao($retorno,'","').'"]';

}


		                   
}