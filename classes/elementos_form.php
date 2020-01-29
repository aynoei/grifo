<?php

class Elementos{
	
public function custom($default,$custom){
if(strlen($custom) > 0){
$r = $custom;
}else{
$r = $default;
}
	
	return $r;
}	

public function input_help($array){
		
	}

public function form($array){
	global $biblioteca;

switch($array['type']):
	case 'colunas':				
			foreach($array['colunas_content'] as $col):
				$colunas[] = $col['item'];
			endforeach;	
			$fieldset = '<fieldset class="form-group '.$array['fieldset_class'].'"> <div class="row"> <label class=" '.self::custom('col-sm-2',$array['colunas_div_class_label']).' ">'.$array['colunas_label'].'</label><div class="'.self::custom('col-sm-3',$array['colunas_class']).'">'.$biblioteca->opcao($colunas).'</div></fieldset>';
			$output = $fieldset;
		break;
/************************************Text******************************************************************/
case 'text':
//div_tipo: row ou icone ou null	
$help =  '<small id="help" class="form-text text-muted">'.$array['help'].'</small>';
			if(isset($array['label']))://label foi definida?
				$input_label = '<label for="'.$array['id'].'" class="'.self::custom('col-sm-2',$array['div_class_label']).' col-form-label">'.$array['label'].'</label>';
			endif;	
			$input = $input_label.'<div class="'.self::custom('col-sm-10',$array['div_class_input']).'">
			
			<input name="'.$array['name'].'" type="text" class="form-control '.$array['estilo'].'" id="'.$array['id'].'" placeholder="'.$array['place'].'" value="'.$array['value'].'" '.$array['required'].' '.$array['adicionais'].'>'.$help.'</div>';
		switch($array['div_tipo'])://seleciona se tem ou não form-group
			case 'row':
				$output = '<div class="form-group row '.$array['form_group'].'" '.$array['hidden'].' >'.$input.'</div>';
			break;
			case 'icone':
				$output = '<div class="form-group '.$form_group.'" style="padding:15px !important;" row>'.$array['input_label'].'<div class="input-group '.$array['div_class'].'" '.$array['hidden'].'>
										<div class="input-group-addon">
											<i class="'.$array['icone'].'"></i>
											</div>
											<input name="'.$array['id'].'" type="text" class="form-control '.$array['estilo'].'" id="'.$array['id'].'" placeholder="'.$array['placeholder'].'" value="'.$array['value'].'" data-mask '.$array['adicionais'].'>
										</div>
									</div>';
			break;
			default://mostra apenas o input
				$output = $input;
			break;
		endswitch;
	
		$output;
	break;
/************************************Select******************************************************************/	
	case 'select':
	
	if(strlen($array['selected_key']) > 0):
		$option = '';
		$selectedDefault = '';
			foreach($array['option'] as $key => $value)://para form editar- busca a opção selecionada - apenas trata com key=>value comparando o key
				if($array['selected_key'] == $key):
					$option .= '<option value="'.$key.'" selected="selected">'.$value.'</option>';
				else:
					$option .= '<option value="'.$key.'" name="'.$array['selected_key'].'">'.$value.'</option>';
				endif;
			endforeach;
	else:
		$selectedDefault = '<option value="" selected="selected">Selecione</option>';
		foreach($array['option'] as $key => $value):
					$option .= '<option value="'.$key.'" >'.$value.'</option>';
		endforeach;
	endif;
	
	
		$select = '<div class="form-group '.self::custom('row',$array['form_group_class']).'">
                <label class="'.self::custom('col-sm-2',$array['div_class_label']).' col-form-label">'.$array['label'].'</label>
				<div class="'.self::custom('col-sm-10',$array['div_class_select']).'">
                <select class="form-control select2 '.$array['estilo'].'" style="width: 100%;" name="'.$array['name'].'" id="'.$array['id'].'" '.$array['adicionais'].'>';                  
		$select .= $selectedDefault.$option;
		$select .= 	'</select></div></div>';
	
		$output = $select;
	break;
/************************************Option******************************************************************/
	case 'option':
	$help =  '<small id="help" class="form-text text-muted">'.$array['help'].'</small>';
		global $biblioteca;
		$tipo = $array['div_tipo'];
		$ordem = 1;
		switch($tipo){			
		case 'fieldset':
			$fieldset = '<fieldset class="form-group '.$array['fieldset_class'].'"><div class="row"><label class=" '.self::custom('col-sm-2',$array['div_class_label']).'">'.$array['label'].'</label><div class="'.self::custom('col-sm-10',$array['div_class']).'">';
			$x = 0;
			foreach($array['option'] as $op):
					$select[] = '<div class="form-check '.self::custom('',$op['inline']).'"><label class="form-check-label"><input class="form-check-input '.$op['input_class'].'" type="radio" name="'.$op['name'].'" id="'.$op['id'].'" value="'.$op['value'].'" '.self::custom('',$op['checked']).'>'.$op['lab'].'</label></div>';
			endforeach;	
			$fieldset .= $biblioteca->opcao($select).'</div></div></fieldset>';
			$output = $fieldset;
		break;
			
		case 'single':
		
			foreach($array['option'] as $op):
					$select[] =  '<div class="custom-control custom-radio '.self::custom('',$op['inline']).'">
                   <input type="radio" name="'.$op['name'].'" id="'.$op['id'].'_'.$ord.'" class="custom-control-input '.$op['input_class'].'" value="'.$op['value'].'" '.self::custom('',$op['checked']).'>
                   <label class="custom-control-label" for="'.$op['id'].'_'.$ord.'">'.$op['lab'].'</label>
                 </div>';
			endforeach;
			$output = $biblioteca->opcao($select);
		break;
			
		case 'custom':
			$fieldset = '<fieldset class="form-group '.$array['fieldset_class'].'">'.$help.'
     <div class="row">
       <label class=" '.self::custom('col-sm-2',$array['div_class_label']).'">'.$array['label'].'</label>
       <div class="'.self::custom('col-sm-10',$array['div_class']).'">';
			$x = 0;
			foreach($array['option'] as $op):
    $ord = $ordem++;
					$select[] = '<div class="custom-control custom-radio '.self::custom('',$op['inline']).'">
                   <input type="radio" name="'.$op['name'].'" id="'.$op['id'].'_'.$ord.'" class="custom-control-input '.$op['input_class'].'" value="'.$op['value'].'" '.self::custom('',$op['checked']).'>
                   <label class="custom-control-label" for="'.$op['id'].'_'.$ord.'">'.$op['lab'].'</label>
                 </div>';

			endforeach;	
			$fieldset .= $biblioteca->opcao($select).'</div></div></fieldset>';
			$output = $fieldset;
		break;
		case 'input_group':
				$help =  '<small id="help" class="form-text text-muted">'.$array['help'].'</small>';
			$input = '<input name="'.$array['name'].'" type="text" class="form-control '.$array['estilo'].'" id="'.$array['id'].'" placeholder="'.$array['place'].'" value="'.$array['value'].'" '.$array['required'].' '.$array['adicionais'].'>';
				
				
			$fieldset = '<fieldset class="form-group '.$array['fieldset_class'].'">
						<div class="row">
							<label class=" '.self::custom('col-sm-2',$array['div_class_label']).'">'.$array['label'].'</label>
								<div class="'.self::custom('col-sm-10',$array['div_class']).'">
									<div class="input-group">
									<span class="input-group-addon">
										<input class="'.$op['radio_input_class'].'" type="radio" name="'.$op['name'].'_radio" id="'.$op['id'].'_radio" value="'.$op['radio_value'].'" '.self::custom('',$op['checked']).'>
									</span>'.$input.'
								</div>
							</div>
						</div>
					</fieldset>';

				$output = $fieldset;
				
		break;
				
				
				
		}
	
			
	
		$output;	
	break;
/************************************Checkbox******************************************************************/	
	case 'checkbox':
		global $biblioteca;
		$help =  '<small id="help" class="form-text text-muted">'.$array['help'].'</small>';
		switch($array['div_tipo']){
		default:		
			foreach($array['option'] as $op):
			$select[] = '<div class="form-check '.self::custom('',$op['inline']).'"><label class="form-check-label"><input class="form-check-input '.$op['input_class'].'" type="checkbox" name="'.$op['name'].'" id="'.$op['id'].'" value="'.$op['value'].'" '.self::custom('',$op['checked']).'>'.$op['lab'].'</label></div>';
			endforeach;					
			$output = $biblioteca->opcao($select);
		break;
		case 'custom':
			$fieldset = '<fieldset class="form-group '.$array['fieldset_class'].'">'.$help.'<div class="row"><label class=" '.self::custom('col-sm-2',$array['div_class_label']).'">'.$array['label'].'</label><div class="'.self::custom('col-sm-10',$array['div_class']).'">';
			$x = 0;
			foreach($array['option'] as $op):
					$select[] = '<div class="custom-controls-stacked '.self::custom('',$op['inline']).'"><label class="custom-control custom-checkbox"><input class="custom-control-input '.$op['input_class'].'" type="checkbox" name="'.$op['name'].'" id="'.$op['id'].'" value="'.$op['value'].'" '.self::custom('',$op['checked']).'><span class="custom-control-indicator"></span><span class="custom-control-description">'.$op['lab'].'</span></label></div>';
			endforeach;	
			$fieldset .= $biblioteca->opcao($select).'</div></div></fieldset>';
			$output = $fieldset;
		break;
		}
	break;
/************************************Hidden******************************************************************/
	case 'hidden':
		$output = '<input name="'.$array['name'].'" class="'.$array['estilo'].'" type="hidden" id="'.$array['id'].'" value="'.$array['value'].'"/>';	
	break;
/************************************TextArea******************************************************************/	
	case 'textarea':
		$output = '<div class="form-group '.$array['div_class'].'">
		    <label for="'.$array['id'].'">'.$array['label'].'</label>
		    <textarea class="form-control '.$array['estilo'].'" name="'.$array['name'].'" id="'.$array['id'].'" rows="'.self::custom('3',$array['rows']).'" '.$array['adicionais'].'>'.$array['value'].'</textarea>
		  </div>';
	 break;

/*******************************************Date*******************************************************/
	
	case 'date':
	if(isset($array['label']))://label foi definida?
				$input_label = '<label for="'.$array['id'].'" class="col-sm-2 col-form-label">'.$array['label'].'</label>';
			endif;	
			$input = $input_label.'<div class="'.$array['div_class'].'"><input name="'.$array['name'].'" type="date" class="form-control '.$array['estilo'].'" id="'.$array['id'].'"" '.$array['adicionais'].'></div>';
		switch($div_tipo)://seleciona se tem ou não form-group
			case 'form-group':
				$output = '<div class="form-group">'.$input.'</div>';
			break;
			case 'icone':
				$output = '<div class="form-group">'.$input_label.'<div class="input-group '.$array['div_class'].'">
							  <div class="input-group-addon">
								<i class="fa fa-calendar"></i>
							  </div>
							  <input name="'.$array['name'].'" type="date" class="form-control '.$estilo.'" id="'.$array['id'].'"" data-mask '.$array['adicionais'].'/>				
							</div>
							<!-- /.input group -->
						  </div>';
			break;
			default://mostra apenas o input
				$output = $input;
			break;
		endswitch;
	break;
/******************************************************************************************************/	
	endswitch;
return $output;
}
//*****************************************************FIM DO GERADOR DE ITENS DE FORMULÁRIO*****************************************************************//
public function nova_pagina($page_title,$page_template){//gera uma nova página			
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
	
public function formsGerais($array){
global $biblioteca, $modulos, $elementos;
	switch($array['tipo']):
		case 'inserir':
			$forms = '<div class="'.self::custom('detalhes_inserir',$array['divClass']).'"></div>
				<form action="" method="post" name="forms_todos" id="'.$array['formId'].'" class="forms_todos " enctype="multipart/form-data" autocomplete="off">
				  <div class="box '.self::custom('box-success',$array['boxClass']).'">
						<div class="box-header with-border text-center"><h5>'.self::custom('Inserir Novo',$array['hText']).'</h5></div>
						<div class="box-body with-border">'.$array['conteudoForm'].'<input name="acao" type="hidden" value="novo"/><input name="tarefa" type="hidden" value="novo"/><input name="modulo" type="hidden" value="'.$array['modulo'].'"/></div></div>						
						<div class="box-footer text-center"><button type="submit" class="btn '.self::custom('btn-success',$array['btnClass']).'" name="salvar_novo">'.self::custom('Salvar',$array['hText']).'</button></div> 
				  </div>
				</form>';
		break;
		case 'editar':
			$forms = '<div class="'.self::custom('detalhes_editar',$array['divClass']).'" >
						<div class="alert alert-warning text-center" role="alert">
							<h4 class="alert-heading msg_retorno">'.self::custom('Editar item',$array['hText']).'</h4>
							<p>'.self::custom('Selecione um item na tabela ao lado para editá-lo.',$array['pText']).'</p>
						</div>
					</div>
					<div class="alterna" hidden >
						<div class="alert alert-warning text-center" role="alert">
							<h4 class="alert-heading msg_retorno">'.self::custom('Editar item',$array['hText']).'</h4>
							<p>'.self::custom('Selecione um item na tabela ao lado para editá-lo.',$array['pText']).'</p>
						</div>
					</div>';
		break;
	endswitch;
	
	return $forms;
}	

public function botao_salvar_form($formulario,$label,$class_btn='btn-success',$class_box='box-danger'){
	$botao = '<div class="box '.$class_box.'">
			  			 <div class="box-footer">
							<button type="submit" form="'.$formulario.'" class="btn '.$class_btn.'">'.$label.'</button>
						 </div>
				</div>';
	return $botao;
}
	
public function botao_form($type='button',$label,$class_btn='btn-success',$formulario=''){
	if($type === 'submit'):
		$form = 'form="'.$formulario.'"';
	endif;
	$botao = '<button type="'.$type.'" '.$form.' class="btn '.$class_btn.'">'.$label.'</button>';
	return $botao;
}
	
public function botao_link($label,$url='#',$class_btn='btn-success',$class_box='box-danger',$box='',$icon='',$function=''){
	$btn = '<a href="'.$url.'" class="btn '.$class_btn.'" role="button" '.$function.'><i class="'.$icon.'" aria-hidden="true"></i>'.$label.'</a>';
	switch($box){
		case '1':
			//opção para inserir como box no cabeçalho basta inserir o número 1
			$botao = '<div class="box '.$class_box.'"><div class="box-footer">'.$btn.'</div></div>';
			break;
		default:
			$botao = $btn;
			break;
	}
	
	return $botao;
}	
	
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
	
public function modal_alert($array){

	switch($array['footer']):
		case 'confirm':		
			$footer = '<button type="button" class="btn btn-primary alert_confirmar">OK</button><button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>';
		break;
		case 'alert':
			$footer = '<button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>';
		break;
	endswitch;
	
return '<div class="modal"><div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header"><h5 class="modal-title">'.$array['title'].'</h5></div><div class="modal-body">'.$array['conteudo'].'</div><div class="modal-footer">'.$foooter.'</div></div></div></div>';
			  
}
	
public function	mensagem($tipo='warning',$titulo='Alerta!',$mensagem='',$estilo=''){
	
return '<div class="alert alert-'.$tipo.' alert-dismissible fade in '.$estilo.'">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<h4><i class="icon fa fa-check"></i>'.$titulo.'</h4>
			'.$mensagem.'
	  	</div>';
}
	
public function botao_array($array){
	
		

	}
	
}//fim da classe