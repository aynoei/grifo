<?php  
//bootstrap 4.
include $_SERVER['DOCUMENT_ROOT'] . 'vendor/autoload.php';

class FBoot{

var $biblioteca;
 
public function __construct(){


    $this->biblioteca = new Biblioteca\Custom();
 
 

}
 
public function custom($default,$custom){
   if(strlen($custom) > 0){
   $r = $custom;
   }else{
   $r = $default;
   }

    return $r;
}

/********************************************Input************************************************/ 
 public function input($array){

$label = '<label for="'.$array['name'].'" class="'.self::custom('',$array['classes_label']).'" '.$array['html_label'].'>'.$array['label'].'</label>'; 
$input = '<input type="'.self::custom('text',$array['type']).'" class="form-control '.self::custom('',$array['classes_input']).'" id="'.$array['id'].'" name="'.$array['name'].'" value="'.$array['value'].'" aria-describedby="'.$array['aria'].'" maxlength="'.$array['maxlength'].'" placeholder="'.$array['place'].'" '.$array['html_input'].'>';
$help = '<small id="'.$array['id'].'_help" class="form-text text-muted '.self::custom('',$array['classes_help']).'" '.$array['html_help'].'>'.$array['help'].'</small>';
  
switch($array['style']){
 case 'row':
    $return = '<div class="form-group row '.self::custom('',$array['classes_div']).'" '.$array['html_group'].'>
                <label for="'.$array['name'].'" class="'.self::custom('col-sm-2',$array['classes_label']).'  col-form-label" '.$array['html_label'].'>'.$array['label'].'</label>
                <div class="'.self::custom('col-sm-10',$array['classes_div_input']).'" '.$array['html_div_input'].'>'.$input.''.$help.'</div>
              </div>';
  break;
 case 'inline':
     $return = '<div class="form-group '.self::custom('',$array['classes_div']).'">'.$label.''.$input.''.$help.'</div>';
  break;
 default:
    $return = $label.$input.$help;
  break;
}  
  return $return;
  
 }
/********************************************Input Cols************************************************/
public function input_cols($arrays){
 
 
 foreach($arrays as $array){
   $label = '<label for="'.$array['name'].'" class="'.self::custom('col-sm-2',$array['classes_label']).'  col-form-label" '.$array['html_label'].'>'.$array['label'].'</label>';
   $input = '<input type="'.self::custom('text',$array['type']).'" class="form-control '.self::custom('',$array['classes_input']).'" id="'.$array['id'].'" name="'.$array['name'].'" value="'.$array['value'].'" aria-describedby="'.$array['aria'].'" maxlength="'.$array['maxlength'].'" placeholder="'.$array['place'].'" '.$array['html_input'].'>';
   $help = '<small id="'.$array['id'].'_help" class="form-text text-muted '.self::custom('',$array['classes_help']).'" '.$array['html_help'].'>'.$array['help'].'</small>';   
  
   $inputs[] = $label.'<div class="'.self::custom('col-sm',$array['classes_div_input']).'" '.$array['html_div_input'].'>'.$input.''.$help.'</div>';
                
 }
 
 return '<div class="form-group row '.self::custom('',$array['classes_div']).'">'.$this->biblioteca->opcao($inputs).'</div>';
 
} 
/********************************************Text Area************************************************/
public function textarea($array){
 
return '<div class="form-group '.self::custom('',$array['classes_div']).'" '.$array['html_group'].'>
         <label for="'.$array['name'].'" '.$array['html_label'].'>'.$array['label'].'</label>
         <textarea name="'.$array['name'].'" class="form-control '.self::custom('',$array['classes_textarea']).'" id="'.$array['id'].'" rows="'.self::custom('3',$array['rows']).'" '.$array['html_textarea'].'>'.$array['value'].'</textarea>
       </div>';
     
  }
}//fim da classe