<?php
/**
 * EMCustomLoginErrors class.
 *
 * @sonce 0.1.0
 */
class EMCustomLoginErrors {

	/**
	 * __construct function.
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {

	}

	/**
	 * register_errors function.
	 *
	 * @access public
	 * @return void
	 */
	public function register_errors() {
		static $wp_error; // Will hold global variable safely
		return isset($wp_error) ? $wp_error : ($wp_error = new WP_Error(null, null, null));
	}

	/**
	 * show_error_messages function.
	 *
	 * Loop error codes and display errors
	 *
	 * @access public
	 * @return void
	 */
	public function show_error_messages() {
		if ($codes=$this->register_errors()->get_error_codes()) :
			echo '<div class="custom-login-notice">';
			  foreach ($codes as $code) :
			  	$message = $this->register_errors()->get_error_message($code);
			    echo '<span class="error"><span class="title">'.__('Error','emcl').'</span>: '.__($message,'emcl').'</span><br/>';
			  endforeach;
			echo '</div>';
		endif;
	}

	/**
	 * format_error function.
	 *
	 * @access public
	 * @param string $code (default: '')
	 * @param bool $message (default: false)
	 * @param string $type (default: '')
	 * @return void
	 */
	public function format_error($code='',$message=false,$type='') {
		if (!$message)
			return false;

		$html=null;
		$code_display=null;

		if ($code && $code!='')
			$code_display='<span class="title">'.__($code,'emcl').'</span>: ';

		$html.='<div class="custom-login-notice">';
			$html.='<span class="'.$type.'">'.__($code_display.$message,'emcl').'</span>';
		$html.='</div>';

		return $html;
	}

}

$custom_login_errors=new EMCustomLoginErrors();
?>