<?php


//*****************Classes de transformação de caracteres********************************************************************************//	
	


class Formulario{

private	function biblioteca(){//carrega a classe separado
	include_once(plugin_dir_path( __FILE__ ).'inc/classes/biblioteca.php');
	return new Biblioteca;	 
}
	
public function abrangencia($abr){//popula a coluna abrangencia
global $wpdb;	
$return = $wpdb->get_row("SELECT * FROM promon_orgao_legislacao_orgaos WHERE Id = '".$abr."'");
	return $return->abrangencia;
	
}
	
public function cria_tabs($array,$elem){
$x = 0; 
if($elem ==='li'):	
	foreach($array as $tab):
	$num = $x++;
	  $box[] = '<li class="nav-item"><a class="nav-link '.$tab['ac'].'" id="'.$tab['nome'].'" data-toggle="pill" href="#'.$tab['nome'].'-'.$num.'" role="tab" aria-controls="'.$tab['nome'].'-'.$num.'" aria-expanded="true">'.$tab['label'].'</a></li>';	  
	endforeach;
else:
	foreach($array as $tab):
	$num = $x++;
	  $box[] = '<div class="tab-pane fade show '.$tab['ac'].' " id="'.$tab['nome'].'-'.$num.'" role="tabpanel" aria-labelledby="tab-'.$tab['nome'].'-'.$num.'-tab">'.$tab['div_conteudo'].'</div>';
	endforeach;
endif;
	
	return $box;
}
	
public function editar_form($array){
	$funcao = 'form_'.$array['tipo'];
	$return = self::$funcao($array['id']);	
	return $return;
	
}

public function insertEdit($tab_ela, $dados_inserir, $dados_editar, $acao, $id){
global $wpdb;	
	switch($acao):
		case 'novo': $result = $wpdb->insert($tab_ela, $dados_inserir); $r = 'novo';	break;
		case 'editar': $result = $wpdb->update($tab_ela, $dados_editar, array(Id => $id)); $r = 'editar'; break;	
	endswitch;
	
	if($wpdb->last_error !== '') :
	        $str   = $wpdb->last_result;
	        $query = $wpdb->last_query;
	        $error = $wpdb->last_error;	
	        $res = "WordPress database error:[$str] - $error - $query";
   	 else:
   	 	$res = $result;
   	 endif;
    
	return $res;
	
	}
	
	

	
public function painel_custom($array){ 
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id; 

	
$box_1_li = self::cria_tabs($array['box1']['tabs'],'li');
$box_1_div = self::cria_tabs($array['box1']['tabs'],'div');
$box_2_li = self::cria_tabs($array['box2']['tabs'],'li');
$box_2_div = self::cria_tabs($array['box2']['tabs'],'div');
	
$return = ' 
			<div class="content-wrapper content-wrapper-inner  ">
			<!-- Main content -->
			<div class="geral row">
			  <!-- box start -->
				  <div class="col-md-8">  
					<div class="box box-success">
						<div class="box-header with-border">
								<div class="float-right"><h2 class="box-title">'.$array['box1']['titulo'].'<i class="fa fa-question-circle-o" aria-hidden="true" title=""></i></h2></div>
						 		<div class="float-left">
									<ul class="nav nav-pills" id="tabs_box_1" role="tablist">'.$biblioteca->opcao($box_1_li).'</ul>
						 		</div>
						</div>
						<div class="box-body with-border">						
							  <div class="tab-content" id="box_1">'.$biblioteca->opcao($box_1_div).'</div>	  
						</div>														
						<div class="box-footer"></div> 
					</div>
				  </div>	
			<!----------------------Fim do Box------------------------------>
				  <div class="col-md-4">
				 		 <div class="box box-success ">					
							<div class="box-header with-border">
								<div class="float-right"><h2 class="box-title">'.$array['box2']['titulo'].'<i class="fa fa-question-circle-o" aria-hidden="true" title=""></i></h2></div>
								
								<div class="float-left">
										<ul class="nav nav-pills" id="tabs_box_2" role="tablist">'.$biblioteca->opcao($box_2_li).'</ul>
										
								</div>
							</div>
							<div class="box-body">
									<div hidden class="retorno">
										<div class="alert alert-success text-center" role="alert">
												<h4 class="alert-heading msg_retorno"></h4>
													<p class="">
														<button class="btn btn-success btn-lg" type="button" onClick="window.location.reload()">
															<i class="fa fa-refresh fa-4" aria-hidden="true"></i>
														</button>
													</p>
												<p>Para ver as alterações na tabela, clique para atualizar a página!</p>
										</div>
									</div>
							
									<div class="tab-content" id="box_2">'.$biblioteca->opcao($box_2_div).'</div>						
							</div>											
					</div>
				  </div>
			<!--------------------Fim----------------------------------->
			 </div>
			 
			 <!--*****************************fim main content*********************-->
</div>';
	
	return $return;

}

//****************************************Salvar no BD***************************************//
public function salva_oficio($array){
	global $wpdb, $post, $modulos, $biblioteca, $tabela_orgao, $wp;
	
			$tab_ela = 'promon_orgao_oficio';//get_post_meta( $post->ID, 'tabela_orgao', true );
			$user_id = get_current_user_id();	
			$group_id =	get_user_meta( $user_id, 'grupo_users',true);//pega o grupo do usuário	
			
	$dados_editar = array(
				   oficio_numero => $array['oficio_numero'],
				   procedimento => $array['procedimento'],
				   prazo => $array['prazo'],
				   tipo => $array['tipo'],
				   destinatario => $array['destinatario'],
				   assunto => strtoupper($array['assunto']),
				   texto => $array['observacao'],
				   codigo => $array["rastreamento_editar"],
					 recebido => 	$biblioteca->banco($array["recebido"])
			);
/***************************************Numero auto do Oficio***************************************/
					 $num = $modulos->ultimo($tab_ela,'promotoria',$array['promotoria']);
					 $n = explode("/",$num['numero_oficio']);
					 $ano = date('Y');//gera o ano atual
					 if($ano == $n[1]){//se o ano corrente for igual ao ano do proximo oficio
						 $numero_oficio = ($n[0]+1)."/".$ano;// mesmo ano
					 }else{
						 $numero_oficio = (1)."/".$ano;//ano novo
					 }
/*************************************************************************************************/	
	$dados_inserir = array(
				   Id => '',
				   user_id => $user_id,
				   numero_oficio => $numero_oficio,
				   oficio_numero => $array['oficio_numero'],
				   oficio_numero_option => $array['oficio_numero_option'],
				   comarca => $modulos->promComarca('comarca','Id'),
				   promotoria => $array['promotoria'],
				   procedimento => $array['procedimento'],
				   criado => date("Y-m-d"),
				   recebido => $biblioteca->banco($array["recebido"]),
				   aguardando_resposta => '0',
				   prazo => $array['prazo'],
				   tipo => $array['tipo'],
				   destinatario => $array['destinatario'],
				   assunto => strtoupper($array['assunto']),
				   texto => $array['observacao'],
				   status => '0',//0 => aguardando resposta, 1 => respondido
				   data => date("Y-m-d")		
			);


	$a = $array["rastreamento_editar"];
	
	return self::insertEdit($tab_ela, $dados_inserir, $dados_editar, $array['acao'], $array['id_']);
}

//*****************************************Forms html***************************************//
public function form_oficio($id=''){
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id;
	//pega o grupo do usuário

if($id>0)://editar
	$result = $wpdb->get_row('SELECT * FROM promon_orgao_oficio WHERE Id = '.$id.'');
	$tarefa = 'editar';//informa ao inline.php qual tarefa realizar no salvar_db
	$id_ = $elementos->form(array(type => 'hidden', value => $id, name => "id_", id => "id_"));	
	switch($result->tipo){
		case 'dias':
			$dias= 'checked';
		break;
		case 'uteis':
			$uteis = 'checked'; 
		break;
		case 'horas':
			$horas = 'checked'; 
		break;
	}
	$editavel = 'editavel';
	if($result->oficio_numero_option == 1):
	$input_auto = $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'oficio_numero',label => 'Nº do Ofício',name => 'oficio_numero',place => 'Nº do Ofício', estilo =>'informa_num_oficio campos_alterar', value=> $result->oficio_numero, adicionais=> '', div_class_input => 'col-sm-5', ));endif;
	$recebido = $biblioteca->cliente($result->recebido);
else:
	$dias = 'checked';
	$tarefa = 'novo';
	$id_ = '';
	 $auto_option = $elementos->form(array(type => 'option', label=>"Nº do Ofício", div_tipo => 'custom', option => array(array(required => 'required',id => 'oficio_numero_option',lab => 'Automático',name => 'oficio_numero_option', checked => $manual , value=>'0', input_class=>'campos_alterar checkbox_valor'),array(required => 'required',id => 'oficio_numero_option',lab => 'Informar número',name => 'oficio_numero_option', value=>'1', checked => 'checked', input_class => 'campos_alterar checkbox_valor'))));
		$input_auto = $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'oficio_numero',label => '',name => 'oficio_numero',place => 'Nº do Ofício', estilo =>'informa_num_oficio campos_alterar', value=> $result->oficio_numero, div_class_input => 'col-sm-5', form_group => 'input_valor' ));
	$recebido = '';
endif;
/********************************************************************************************************/
/*option Nº Oficio*/$forms = $auto_option.$input_auto;
	
/*'N° Atena*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'procedimento',label => 'N° Atena',name => 'procedimento',place => 'Número do Atena', div_class_input=>'col-sm-4', estilo =>'numero campos_alterar', value=> $result->procedimento));
/*Vencimento*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'prazo',label => 'Prazo',name => 'prazo',div_class_input => 'col-sm-2', div_class_input=>'col-sm-2', estilo =>'numero campos_alterar', value=> $result->prazo));
/*'option Tipo*/$forms .= $elementos->form(array(type => 'option', label=>"Tipo de Prazo", div_tipo => 'custom', option => array(array(required => 'required',id => 'tipo',lab => 'dias',name => 'tipo', checked => $dias, value=>'dias', estilo=>'campos_alterar'),array(required => 'required',id => 'tipo',lab => 'dias úteis',name => 'tipo', value=>'uteis', checked => $uteis, estilo => 'campos_alterar'),array(required => 'required',id => 'tipo',lab => 'horas',name => 'tipo', value=>'horas', checked => $horas, estilo => 'campos_alterar'))));
/*Destinatario*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'destinatario.oficio..autocomplete',label => 'Destinatário',name => 'destinatario', estilo =>'autocompletar text-uppercase destinatario campos_alterar',value=> $result->destinatario, place => 'Destinatário do ofício'));
/*Assunto*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'assunto.oficio..autocomplete',label => 'Assunto',name => 'assunto', estilo =>'autocompletar text-uppercase assunto campos_alterar',value=> $result->assunto, place => 'Assunto do ofício'));
/*Interessado*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'interessado.oficio..autocomplete',label => 'Interessado',name => 'interessado', estilo =>'autocompletar text-uppercase interessado campos_alterar',value=> $result->interessado, place => 'Interessado'));

/*$forms .= $elementos->form(array(type => 'checkbox', label=>'Reiteração?', div_tipo => 'custom', option => array(array(id => 'reitera',name => 'reitera', value=>'1', input_class=>'reitera'))));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', id => 'oficio_n_oficio', label => 'Ofício reiterado', name => 'oficio_n_oficio', div_class_input => 'col-sm-4', adicionais=>'disabled'));*/
/*Observacao*/$forms .= $elementos->form(array(type => 'textarea', label => 'Resumo do Conteúdo', rows => '5', name => "observacao", id => "observacao", estilo => 'text-uppercase  campos_alterar',value=> $result->texto));
/*destinatario recebeu em*/	$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => '',id => 'recebido', name => 'recebido', label => 'Destinatário recebeu em', help => 'Deixe em branco se o destinatário ainda não recebeu', div_class_input=>'col-sm-4', estilo =>'data_mask campos_alterar recebido', value=> $recebido));
/*Rastreamento*/$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => '',id => 'rastreamento_editar',label => 'Rastreamento',name => 'rastreamento_editar', div_class_label => 'col-sm-3', div_class_input => 'col-sm-9',estilo =>'text-uppercase destinatario campos_alterar',value=> $result->codigo, place => 'Código de rastreio - Correspondência'));	
/*Hidden tarefa*/$forms .= $elementos->form(array(type => 'hidden', value => $tarefa, name => "tarefa", id => "tarefa", estilo=>'campos_alterar'));
/*Hidden Id*/$forms .= $id_;
return $forms;

}  
//---------------------------------------------------------------DECURSO---------------------------------------------------------------------------/	
public function salva_decurso($array){

global $wpdb, $biblioteca, $modulos, $funcoes, $elementos;

			$tab_ela = 'promon_orgao_decurso';//get_post_meta( $post->ID, 'tabela_orgao', true );
			$user_id = get_current_user_id();	
			$group_id = get_user_meta( $user_id, 'grupo_users',true);//pega o grupo do usuário	
			
	$dados_inserir = array(
				Id => '',
				user_id => $user_id,
				comarca => $modulos->promComarca('comarca','Id'),
				promotoria => $group_id,				
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				prazo => $array['prazo'],
				tipo_prazo => $array['tipo'],
				assunto => strtoupper($array['assunto']),
				obs => $array['obs'],
				status => '0',
				data => date("Y-m-d")		
			);
	$dados_editar = array(
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				prazo => $array['prazo'],
				tipo_prazo => $array['tipo'],
				assunto => strtoupper($array['assunto']),
				obs => $array['obs']
			);

	
	return self::insertEdit($tab_ela, $dados_inserir, $dados_editar, $array['acao'], $array['id_']);

}
	
public function form_decurso($id=''){
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id;
	
if($id>0)://editar
	$editavel = 'editavel';
	$result = $wpdb->get_row('SELECT * FROM promon_orgao_decurso WHERE Id = '.$id.'');
	$id_ = $elementos->form(array(type => 'hidden', value => $id, name => "id_", id => "id_"));	
	$evento_data = $biblioteca->cliente($result->evento_data);
	switch($result->tipo_prazo){
		case '0':
			$dias= 'checked';
		break;
		case '1':
			$uteis = 'checked'; 
		break;
	}
	$prazo = $result->prazo;
	$option_tipo = '';
else:
	$id_ = '';
	$dias = 'checked';
	$evento_data = '';
	$diario = '';
	$ano = 'checked';
	$prazo = '365';
	$option_tipo = $elementos->form(array(type => 'option', label=>"Prazo em dias", div_tipo => 'custom', option => array(array(required => 'required',id => 'prazo_ano',lab => '1 ano(365 dias)',name => 'prazo_dias', checked => $ano, value=>'0', input_class=>'campos_alterar checkbox_valor'),array(required => 'required',id => 'prazo_custom',lab => 'Informar o prazo em dias', name => 'prazo_dias', value=>'1', checked => $diario, input_class => 'campos_alterar checkbox_valor'))));
endif;
/******************************************Elementos do Formulario Inserir*********************************************************/
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'procedimento', name => 'procedimento', label => 'Número Atena', place => 'Digite o número do protocolo', estilo =>'numero campos_alterar', value=> $result->procedimento));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'evento_data', name => 'evento_data', label => 'Início do Prazo', place => '', estilo =>'data_mask campos_alterar', value=> $evento_data));
	
$forms .= $option_tipo;	
	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'prazo', name => 'prazo', label => '', place => '365', form_group => 'input_valor',estilo =>'numero campos_alterar', value=> $prazo, help => 'Informe o prazo em dias.', div_class_input => 'col-sm-5'));	
	
$forms .= $elementos->form(array(type => 'option', label=>"Tipo de Prazo", div_tipo => 'custom', option => array(array(required => 'required',id => 'tipo',lab => 'dias corridos',name => 'tipo', checked => $dias, value=>'0', estilo=>'campos_alterar'),array(required => 'required',id => 'tipo',lab => 'dias uteis',name => 'tipo', value=>'1', checked => $uteis, estilo => 'campos_alterar'))));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'assunto', name => 'assunto', label => 'Assunto', place => '', estilo =>'campos_alterar text-uppercase assunto', value=> $result->assunto));	
$forms .= $elementos->form(array(type => 'textarea', label => 'Observações', rows => '3', id => "obs", name => "obs", textarea_class => 'text-uppercase campos_alterar',value=> $result->obs));

	

	return $forms.$id_;
}
//---------------------------------------------------------------LEGISLACAO---------------------------------------------------------------------------/	
public function salva_legislacao($array){

global $wpdb, $biblioteca, $modulos, $funcoes, $elementos;

			$tab_ela = 'promon_orgao_legislacao';//get_post_meta( $post->ID, 'tabela_orgao', true );
			$user_id = get_current_user_id();	
			$group_id = get_user_meta( $user_id, 'grupo_users',true);//pega o grupo do usuário	
			
	$dados_inserir = array(
				   Id => '',
				   user_id => $user_id,
				   tipo => $array['tipo'],
				   numero => $array['numero'],
				   data_criacao => $biblioteca->banco($array['data_criacao']),
				   ementa=> $array['ementa'],
				   edicao_numero=> $elementos->custom(NULL,$array['edicao_numero']),
				   publicacao => $elementos->custom(NULL,$biblioteca->banco($array['publicacao'])),
				   orgao_criador=> $array['orgao_criador'],
				   status => '1',
				   alteracoes => $array['alteracoes'],
				   link => $array['link'],
				   abrangencia => self::abrangencia($array['orgao_criador']),	
				   tags => $array['tags'],			   
				   data => date("Y-m-d")		
			);
	$dados_editar = array(
				   tipo => $array['tipo'],
				   numero => $array['numero'],
				   data_criacao => $biblioteca->banco($array['data_criacao']),
				   ementa=> $array['ementa'],
				   edicao_numero=> $elementos->custom(NULL,$array['edicao_numero']),
				   publicacao => $elementos->custom(NULL,$biblioteca->banco($array['publicacao'])),
				   orgao_criador=> $array['orgao_criador'],
				   alteracoes => $array['alteracoes'],
				   link => $array['link'],
				   abrangencia => self::abrangencia($array['orgao_criador']),
				   tags => $array['tags'],
			);

	
	return self::insertEdit($tab_ela, $dados_inserir, $dados_editar, $array['acao'], $array['id_']);

}

public function form_legislacao($id=''){
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id;

$normas =  $modulos->busca_basica_array_opcao('SELECT * FROM promon_orgao_legislacao_normas ORDER BY nome ASC');
$orgaos =  $modulos->busca_basica_array_opcao('SELECT * FROM promon_orgao_legislacao_orgaos WHERE pai > 0 ORDER BY nome ASC');
//$result = $wpdb->get_row('SELECT * FROM promon_orgao_legislacao_normas WHERE Id = '.$id.'');

	$options_tipo = $biblioteca->string_array($normas,'keyvalue','nome','Id');
	$options_orgao = $biblioteca->string_array($orgaos,'keyvalue','nome','Id');

if($id>0)://editar
	$editavel = 'editavel';
	$result = $wpdb->get_row('SELECT * FROM promon_orgao_legislacao WHERE Id = '.$id.'');
	$orgao_selectedId = $result ->orgao_criador;	
	$tipo_selectedId = $result->tipo;
	$abangencia_selectedId = $result->abrangencia;
	$id_ = $elementos->form(array(type => 'hidden', value => $id, name => "id_", id => "id_"));	
else:
	$orgao_selectedId = '';
	$tipo_selectedId = '';
	$abangencia_selectedId = '';
	$id_ = '';
endif;
/******************************************Elementos do Formulario Inserir*********************************************************/
$forms = $elementos->form(array(type => 'select',label=>'Órgão',name=>'orgao_criador',id=>'leg_orgao_criador', option=>$options_orgao, selected_key=> $orgao_selectedId));
$forms .= $elementos->form(array(type => 'select',label=>'Tipo de Norma',name=>'tipo',id=>'leg_tipo', option=> $options_tipo, selected_key=> $tipo_selectedId ));
/*$forms .= $elementos->form(array(type => 'select',label=>'Abrangência', name=>'abrangencia',id=>'leg_abrangencia', option=>array('ESTADUAL'=>'ESTADUAL','FEDERAL'=>'FEDERAL','MUNICIPAL'=>'MUNICIPAL'), selected_key=> $abangencia_selectedId));*/
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'leg_numero', name => 'numero', label => 'Número da Norma', place => '33', estilo =>'numero campos_alterar', value=> $result->numero));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'leg_data_criacao', name => 'data_criacao',label => 'Criada em', place => '25/12/2012', estilo =>'data_mask campos_alterar', value=> $biblioteca->cliente($result->data_criacao)));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row',id => 'leg_edicao_numero', name => 'edicao_numero',label => 'Edição', place => '1345', estilo =>'numero campos_alterar', value=> $elementos->custom('',$result->edicao_numero)));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row',id => 'leg_publicacao', name => 'publicacao',label => 'Publicada em', place => '25/12/2012', estilo =>'data_mask campos_alterar', value=> $elementos->custom('',$biblioteca->cliente($result->publicacao))));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'leg_link', name => 'link',label => 'Link online', place => 'http://www.linkdanorma.com.br', estilo =>'campos_alterar links', value=> $result->link));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => '',id => 'leg_tags', name => 'tags',label => 'Palavras-Chave', estilo =>'tag-input campos_alterar text-uppercase', value=> $result->tags, adicionais => ''));
$forms .= $elementos->form(array(type => 'textarea', label => 'Ementa/Resumo', rows => '3', id => "leg_ementa", name => "ementa", textarea_class => 'text-uppercase campos_alterar',value=> $result->ementa));

	return $forms.$id_;
}
//---------------------------------------------------------------AUDIENCIA---------------------------------------------------------------------------/	
public function salva_audiencia($array){

global $wpdb, $biblioteca, $modulos, $funcoes, $elementos;

			$tab_ela = 'promon_orgao_audiencia';//get_post_meta( $post->ID, 'tabela_orgao', true );
			$user_id = get_current_user_id();	
			$group_id = get_user_meta( $user_id, 'grupo_users',true);//pega o grupo do usuário	

	
	$dados_inserir = array(
				Id => '',
				user_id => $user_id,
				comarca => $modulos->promComarca('comarca','Id'),
				promotoria => $group_id,
				projud => $array['projud'],
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				evento_hora => $array['evento_hora'],
				local => strtoupper($array['local']),
				artigo => strtoupper($array['artigo']),
				assunto => strtoupper($array['assunto']),
				obs => $array['obs'],
				testemunha => $array['testemunha'],
				reu_preso => $array['reu_preso'],
				data => date("Y-m-d")		
			);
	$dados_editar = array(
				projud => $array['projud'],
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				evento_hora => $array['evento_hora'],
				local => strtoupper($array['local']),
				artigo => strtoupper($array['artigo']),
				assunto => strtoupper($array['assunto']),
				obs => $array['obs'],
				testemunha => $array['testemunha'],
				reu_preso => $array['reu_preso'],
			);

	
	return self::insertEdit($tab_ela, $dados_inserir, $dados_editar, $array['acao'], $array['id_']);

}	
	
public function form_audiencia($id=''){
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id;


if($id>0)://editar
	$editavel = 'editavel';
	$result = $wpdb->get_row('SELECT * FROM promon_orgao_audiencia WHERE Id = '.$id.'');
	$id_ = $elementos->form(array(type => 'hidden', value => $id, name => "id_", id => "id_"));	
	$reu_preso = ($result->reu_preso == 1)?'checked':'';
	$testemunha = ($result->testemunha == 1)?'checked':'';
	$hora = date('H:i', strtotime($result->evento_hora));
else:
	$id_ = '';
	$hora = '';
endif;
	
/******************************************Elementos do Formulario Inserir*********************************************************/
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'procedimento', name => 'procedimento', label => 'Número Atena', place => 'Digite o número do protocolo', estilo =>'numero campos_alterar', value=> $result->procedimento));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'projud', name => 'projud', label => 'Processo Judicial', place => 'Digite o número do protocolo', estilo =>'numero campos_alterar', value=> $result->projud));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'evento_data', name => 'evento_data',label => 'Data da Audiência', place => '25/12/2012', estilo =>'data_mask campos_alterar', value=> $biblioteca->cliente($result->evento_data)));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'evento_hora', name => 'evento_hora',label => 'Hora da Audiência', place => '14:30', estilo =>'hora_mask campos_alterar', value=> $hora));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'assunto', name => 'assunto', label => 'Assunto', place => 'Instrução e julgamento', estilo =>'campos_alterar text-uppercase assunto', value=> $result->assunto));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'local', name => 'local', label => 'Local de Realização', place => '', estilo =>'campos_alterar text-uppercase text-uppercase local', value=> $result->local));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => '',id => 'artigo', name => 'artigo', label => 'Artigo do Crime/Infração', place => 'Art. 157 - CPP', estilo =>'campos_alterar text-uppercase artigo', value=> $result->artigo));
$forms .= $elementos->form(array(type => 'checkbox', label=>"Arrolar Testemunhas?", div_tipo => 'custom', help => 'Marque caso tenha que arrolar testemunhas para audiência e se possível, insira o nome delas na observação.', option => array(array(required => 'required',id => 'testemunha',lab => 'Sim',name => 'testemunha', value=>'1', estilo => 'campos_alterar', checked => $testemunha))));	
$forms .= $elementos->form(array(type => 'checkbox', label=>"Reu Preso?", div_tipo => 'custom', help => 'Marque caso exista algum réu preso.', option => array(array(required => 'required',id => 'reu_preso',lab => 'Sim',name => 'reu_preso', value=>'1', estilo => 'campos_alterar', checked => $reu_preso))));
$forms .= $elementos->form(array(type => 'textarea', label => 'Observações', rows => '3', id => "obs", name => "obs", textarea_class => 'text-uppercase campos_alterar',value=> $result->obs));

	

	return $forms.$id_;
}	
	
//---------------------------------------------------------------JURI---------------------------------------------------------------------------/	
public function salva_juri($array){

global $wpdb, $biblioteca, $modulos, $funcoes, $elementos;

			$tab_ela = 'promon_orgao_juri';//get_post_meta( $post->ID, 'tabela_orgao', true );
			$user_id = get_current_user_id();	
			$group_id = get_user_meta( $user_id, 'grupo_users',true);//pega o grupo do usuário	
	
	$dados_inserir = array(
				Id => '',
				user_id => $user_id,
				comarca => $modulos->promComarca('comarca','Id'),
				promotoria => $group_id,
				projud => $array['projud'],
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				evento_hora => $array['evento_hora'],
				local => strtoupper($array['local']),
				reu => strtoupper($array['reu']),
				estado => $array['estado'],
				artigo => strtoupper($array['artigo']),
				complemento => strtoupper($array["complemento"]),
				assunto => strtoupper($array['assunto']),
				obs => $array['obs'],
				data => date("Y-m-d")		
			);
	$dados_editar = array(
				projud => $array['projud'],
				procedimento => $array['procedimento'],
				evento_data => $biblioteca->banco($array['evento_data']),
				evento_hora => $array['evento_hora'],
				local => strtoupper($array['local']),
				reu => strtoupper($array['reu']),
				estado => $array['estado'],
				artigo => strtoupper($array['artigo']),
				complemento => strtoupper($array["complemento"]),
				assunto => strtoupper($array['assunto']),
				obs => $array['obs'],
			);

	
	return self::insertEdit($tab_ela, $dados_inserir, $dados_editar, $array['acao'], $array['id_']);

}	
	
public function form_juri($id=''){
global $wpdb, $biblioteca, $modulos, $funcoes, $elementos, $wp, $group_id;


if($id>0)://editar
	$editavel = 'editavel';
	$result = $wpdb->get_row('SELECT * FROM promon_orgao_juri WHERE Id = '.$id.'');
	$id_ = $elementos->form(array(type => 'hidden', value => $id, name => "id_", id => "id_"));	
	$preso = ($result->estado == '1')?'checked':'';
	$solto = ($result->estado == '0')?'checked':'';
	$hora = date('H:i', strtotime($result->evento_hora));
else:
	$id_ = '';
	$hora = '';
	$preso = 'checked';
endif;
/******************************************Elementos do Formulario Inserir*********************************************************/
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'procedimento', name => 'procedimento', label => 'Número Atena', place => 'Digite o número do protocolo', estilo =>'numero campos_alterar', value=> $result->procedimento));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'projud', name => 'projud', label => 'Processo Judicial', place => 'Digite o número do protocolo', estilo =>'numero campos_alterar', value=> $result->projud));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'evento_data', name => 'evento_data',label => 'Data do Juri', place => '25/12/2012', estilo =>'data_mask campos_alterar', value=> $biblioteca->cliente($result->evento_data)));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'evento_hora', name => 'evento_hora',label => 'Hora do Juri', place => '14:30', estilo =>'hora_mask campos_alterar', value=> $hora));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'reu', name => 'reu', label => 'Nome do Réu', place => '', estilo =>'campos_alterar text-uppercase reu', value=> $result->reu));	
$forms .= $elementos->form(array(type => 'option', label=>"Situação do Réu", div_tipo => 'custom', option => array(array(required => 'required',id => 'estado',lab => 'Preso',name => 'estado', checked => $preso, value=>'1', input_class=>'campos_alterar'),array(required => 'required',id => 'estado',lab => 'Solto', name => 'estado', value=>'0', checked => $solto, input_class => 'campos_alterar'))));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'artigo', name => 'artigo', label => 'Artigo do Crime/Infração', place => 'Art. 157 - CPP', estilo =>'campos_alterar text-uppercase artigo', value=> $result->artigo));
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => '',id => 'complemento', name => 'complemento', label => 'Complemento do Artigo', place => '§2º, Incisos I, Art. 61, Inciso I Alinea F', estilo =>'campos_alterar text-uppercase complemento', value=> $result->complemento));
	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'assunto', name => 'assunto', label => 'Assunto', place => 'HOMICÍDIO QUALIFICADO', estilo =>'campos_alterar text-uppercase assunto', value=> $result->assunto));	
$forms .= $elementos->form(array(type => 'text', div_tipo => 'row', required => 'required',id => 'local', name => 'local', label => 'Local de Realização', place => '', estilo =>'campos_alterar text-uppercase text-uppercase local', value=> $result->local));

$forms .= $elementos->form(array(type => 'textarea', label => 'Observações', rows => '3', id => "obs", name => "obs", textarea_class => 'text-uppercase campos_alterar',value=> $result->obs));

	

	return $forms.$id_;
}	

	
}//fim da classe Formulario