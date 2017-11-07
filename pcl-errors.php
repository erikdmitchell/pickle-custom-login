<?php

class Pickle_Custom_Login_Errors {

	public function __construct() {

	}

	public function register_errors() {
		static $wp_error; // Will hold global variable safely
		
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}

	public function show_error_messages() {
		if ($codes=$this->register_errors()->get_error_codes()) :
			echo '<div class="custom-login-notice">';
			  foreach ($codes as $code) :
			  	$message = $this->register_errors()->get_error_message($code);
			    echo '<span class="error"><span class="title">'.__('Error','pcl').'</span>: '.__($message,'pcl').'</span><br/>';
			  endforeach;
			echo '</div>';
		endif;
	}

	public function format_error($code='', $message=false, $type='') {
		if (!$message)
			return false;

		$html=null;
		$code_display=null;

		if ($code && $code!='')
			$code_display='<span class="title">'.__($code, 'pcl').'</span>: ';

		$html.='<div class="custom-login-notice">';
			$html.='<span class="'.$type.'">'.__($code_display.$message, 'pcl').'</span>';
		$html.='</div>';

		return $html;
	}

}
?>