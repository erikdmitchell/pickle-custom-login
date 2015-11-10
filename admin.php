<?php
class EMCustomLoginAdmin {

	public function __construct() {
		add_action('admin_menu',array($this,'admin_menu'));
	}

	public function admin_menu() {
		add_options_page('EM Custom Login','EM Custom Login','manage_options','em_custom_login',array($this,'admin_page'));
	}

	public function admin_page() {
		$html=null;

		$html.='<h2>EM Custom Login</h2>';

		$html.="custom emails, add option for activation key";

		echo $html;
	}

}

new EMCustomLoginAdmin();
?>