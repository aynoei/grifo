<?php


//*****************Classes de transformação de caracteres********************************************************************************//	
	
	
		

class Funcoes{

private	function biblioteca(){//carrega a classe separado
	include_once(plugin_dir_path( __FILE__ ).'inc/classes/biblioteca.php');
	return new Biblioteca;	 
}
//******************************Função apra gerar nova pagina no template ou pluguin***************************//	
public function pagina_nova($page_title,$page_template){//gera uma nova página			
	$new_page_title = $page_title;
        $new_page_template = ($page_template==null)?'':$page_template; //ex. template-custom.php. Leave blank if you don't want a custom page template.
        //don't change the code bellow, unless you know what you're doing
        $page_check = get_page_by_title($new_page_title);
        $new_page = array(
                'post_type' => 'page',
                'post_title' => $new_page_title,
                'post_content' => '',
                'post_status' => 'publish',
                'post_author' => 1,
                'page_template'  => $new_page_template,
        );
        if(!isset($page_check->ID)){
                $new_page_id = wp_insert_post($new_page);
                if(!empty($new_page_template)){
                        update_post_meta($new_page_id, '_wp_page_template', $new_page_template);
                }
        }		
		
	}	
/***********************************Tabelas DataTable e Bootstrap*******************************************************/
public function tabela($tabela=''){
global $biblioteca, $elementos;
	/*
	'tabela_id' => 'apiKey',
	'tabela_class' =>'lista_comum',
	'tabela_style' => '',
	'tabela_cellspacing' => '0',
	'tabela_width' => '100%',
	'thead_conteudo' => ['','produto','quantidade',''],<-----obrigatoria
	'thead_class' => 'center',
	'thead_id' => '',	
	'tbody_conteudo' => [['conteudo'=>['','sabao em pó','10',''],'style'=>'danger'],<------Obrigatoria
		[	
			'conteudo'=>['','detergente','20',''],
			'style'=>''],
		],
	'tbody_class' => 'center',
	'tbody_id' => 'id="produtos" class""',
	'tfooter' => ['','produto','quantidade',''],
	'tfooter_class' => 'center',
	'tfooter_id' => 'id="produtos" class""',	
	'trh_class' => 'text-center',
	'th_class' => 'text-uppercase',
	'td_class' => '',
	'trd_class' => 'text-center',
*/
if(is_array($tabela)):
	$head = $tabela['theadConteudo'];
	$body = $tabela['tbodyConteudo'];
	
	switch($tabela['tipo']):
		case 'thApenas':
					/*********************thead***************************/
					$y = 0;	
					foreach($head as $h): $th[] = '<th id="th_'.$y++.'" class="'.$h['clas'].'" style="'.$h['style'].'" '.$h['attr'].'>'.$h['cont'].'</th>'; endforeach;
	
					$tb_ok = '<thead '.$tabela['theadId'].'><tr class="'.$tabela['hTrClass'].'">'.$biblioteca->opcao($th).'</tr></thead>';
	
	
					$table = '<table name="'.$tabela['name'].'" id="'.$elementos->custom('lista_itens',$tabela['tabelaId']).'" class="'.$elementos->custom('table table-responsive table-hover',$tabela['clas']).'">'.$tb_ok.'<tbody '.$tabela['tbodyId'].'>'.$body.'</tbody></table>';
		break;
		case 'trApenas':
					/*******************tbody***************************/	
					if(count($body)>0):				
						foreach($body as $b):
									$td = '';
									$x = 0;
									$by[] = $body['trCont'];
									foreach($by as $bd):
										$td[] = '<td id="td_'.$x++.'" class="'.$bd['tdClass'].'" '.$bd['tdAttr'].'>'.$bd['tdCont'].'</td>'; 
									endforeach;
								$tr[] =	'<tr id="'.$elementos->custom('tr_'.$y++.'', $b['trId']).'" class="'.$b['trClass'].' '.$b['trStyle'].' '.$b['trAttr'].'">'.$biblioteca->opcao($td).'</tr>';				
						endforeach;
						$tb_ok = '<tbody '.$tabela->tbodyId.'>'.$biblioteca->opcao($tr).'</tbody>';

					else:
						$tb_ok = '<tbody><td colspan="100%"></td></tbody>';
					endif;
	
	
					$table = $td;
		break;
		default:
					/*********************thead***************************/
					$y = 0;	
					foreach($head as $h): $th[] = '<th id="th_'.$y++.'" class="'.$tabela['th_class'].'">'.$h.'</th>'; endforeach;
					/*******************tbody***************************/	
					if(count($body)>0):				
						foreach($body as $b):
									$td = '';
									$x = 0;
									$by = $b['conteudo'];
									foreach($by as $bd):
										$td[] = '<td id="td_'.$x++.'" class="'.$tabela['td_class'].'">'.$bd.'</td>'; 
									endforeach;
										$tr[] =	'<tr id="tr_'.$y++.'" class="'.$tabela['trd_class'].' '.$b['style'].'">'.$biblioteca->opcao($td).'</tr>';				
							endforeach;
						$tb_ok = '<thead '.$tabela['thead_id'].'><tr class="'.$tabela['trd_class'].'">'.$biblioteca->opcao($th).'</tr></thead>
							  <tbody '.$tabela['tbody_id'].'>'.$biblioteca->opcao($tr).'</tbody>';

					else:
						foreach($head as $hh):
							$vz[] = '<td></td>';
						endforeach;

						$tb_ok = '<thead '.$tabela['thead_id'].'><tr class="'.$tabela['trd_class'].'">'.$biblioteca->opcao($th).'</tr></thead>
							  <tbody>'.$biblioteca->opcao($vz).'</tbody>';

					endif;

					$table = '<table id="'.$tabela['id'].'" class="'.$tabela['tabela_class'].' table table-responsive table-hover table-striped" cellspacing="'.$tabela['tabela_cellspacing'].'" width="'.$tabela['tabela_width'].'">'.$tb_ok.'</table>';					
				
		break;
	endswitch;
	
					return $table;

	else:
					$erro = '<div class="well">Não há dados para se mostar. Verifique a Api!</div>';
					return $erro;
	endif;
}
						
	
/***********************************Botoes*************************************************************/

public function botao_icone($nome,$id,$btn="btn-default btn-xs",$class,$icone="fa fa-question",$icone_title='',$extra=''){

		return '<div class="float-left margin-btn-2"><button name="'.$nome.'" class="btn '.$btn.' '.$class.' btn_icone" type="button" id="'.$id.'" '.$extra.' title="'.$icone_title.'" data-toggle="tooltip">
					<i class="'.$icone.'" aria-hidden="true"></i>
				</button></div>';														
	}
//*****************************************Funções para Formulários*************************************************//
public function form_input_text($array){	
	/*
$funcoes->form_input_text(array(
	div_tipo = '',*form-group,incone ou deixe em branco para default
	label=>'true',
	label_text => '',
	label_class => '',
	id => '',
	placeholder => '',
	default => '',
	required => 'true',
	input_class => '',
	div_class => '',
	form_group_class => '',
	
	));
	
	*/
	$required = ($array['required'] === 'true')?'required':'';
			if($array['label'] === 'true')://label foi definida?
				$input_label = '<label for="'.$array['id'].'" class="'.$array['label_class'].' control-label">'.$array['label_text'].'</label>';
			endif;
	
			$input = $input_label.'<div class="'.$array['div_class'].'"><input name="'.$array['id'].'" type="text" class="form-control '.$array['input_class'].'" id="'.$array['id'].'" placeholder="'.$array['placeholder'].'" value="'.$array['default'].'" '.$required.'></div>';
	
		switch($array['div_tipo'])://seleciona se tem ou não form-group
			case 'form-group':
				$output = '<div class="form-group '.$array['form_group_class'].'">'.$input.'</div>';
			break;
			case 'icone':
				$output = '<div class="form-group '.$array['form_group_class'].'" style="padding:15px !important;">'.$input_label.'<div class="input-group '.$array['div_class'].'">
							  <div class="input-group-addon">
								<i class="'.$icone.'"></i>
							  </div>
							  <input name="'.$array['id'].'" type="text" class="form-control '.$array['input_class'].'" id="'.$array['id'].'" placeholder="'.$array['placeholder'].'" value="'.$array['default'].'" data-mask '.$required.'>				
							</div>
						  </div>';
			break;
			default://mostra apenas o input
				$output = $input;
			break;
		endswitch;
	
	return $output;

	
}
public function botao($array){
/*
tipo => '',*opcional
target => '', *opção para submit do form
class_btn => '',
texto => ''
*/
	if($array['tipo'] === 'submit'):
		$form = 'form="'.$array['target'].'"';
		$type = 'submit';
	else:
		$form = '';
		$type = 'button';
	endif;
	$botao = '<button type="'.$type.'" '.$form.' class="btn '.$array['class_btn'].'">'.$array['texto'].'</button>';
	return $botao;
}
/****************************Botao Modal****************************************************/
public function botao_modal($array){
		
	if($array['icone'] === true):$icone = '<i class="'.$array['icone_img'].'" aria-hidden="true"></i>';endif;
		
	return '<button name="'.$array['nome'].'" class="btn '.$array['btn'].' '.$array['style'].' btn_icone" type="button" id="'.$array['id'].'" '.$array['extra'].' title="'.$array['btn_title'].'" data-toggle="modal" data-target="#'.$array['target'].'">'.$icone.$array['btn_text'].'</button>
			<div class="modal fade" id="'.$array['target'].'" tabindex="-1" role="dialog" aria-labelledby="'.$array['label'].'">
			  <div class="modal-dialog modal-lg" role="document">
				<div class="modal-content">
				  <div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Fechar"><span aria-hidden="true">&times;</span></button>
					<h3 class="modal-title" id="myModalLabel">'.$array['titulo'].'</h3>'.$array['cabecalho'].'					
				  </div>
				  <div class="modal-body">'.$array['conteudo'].'</div>
				  <div class="modal-footer">'.$array['footer'].'</div>
				</div>
			  </div>
			</div>';	
		
	}
/**************************************Funções para página**********************************************************/
public function	mensagem($array){
	
//$tipo='warning',$titulo='Alerta!'
	
return '<div class="alert alert-'.$array['tipo'].' alert-dismissible fade in '.$array['div_class'].'">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i>'.$array['titulo'].'</h4>
			'.$array['mensagem'].'
	  	</div>';
}

/*************************************Funções de Banco de Dados******************************************************/

public function doBanco($tabela,$where,$que){
		global $wpdb;
		$result = $wpdb->get_row('SELECT * FROM '.$tabela.' WHERE Id = '.$where.'');
		return $result->$que;
	}
	
/*******************************Funções do Modulo Oficio********************************************************/
	
public function recebidoForma($dataInicial,$prazo,$tipoPrazo,$retorno='-'){
	global $biblioteca;
	if($biblioteca::validaData($dataInicial,false) == false):
		$output = $retorno;	
	else:		
		switch($tipoPrazo):
			case 'dias':
					$output = $biblioteca->cliente(@$biblioteca->expira_dia($dataInicial,$prazo));

			break;
			case 'uteis':
					$output =  $biblioteca->cliente(@$biblioteca->banco($biblioteca::SomaDiasUteis($biblioteca::cliente($dataInicial),$prazo)));

			break;
			case 'horas':
					$output = $biblioteca->cliente(@$biblioteca->expira_hora($dataInicial,$prazo));

			break;													
		endswitch;
	endif;
	
	return $output;
	
}
	
	}//fim da classe funcoes