<?php

add_shortcode('test_form', 'tf_show_test_form');
function tf_show_test_form()
{
	$output = '';
	$output .= '<div class="test-form">
    	<div class="test-form_wrapper">
			<form class="test-form__form" action="" method="post" novalidate>
				<img src="' . plugin_dir_url(dirname(__FILE__)) . 'images/email.svg" alt="form icon" />
				<h2>Subscribe</h2>
				<p>Subscribe now and receive a box filled with hand-picked awesome items</p>
				<div class="test-form__field">
					<div class="test-form__field-content">
						<input class="client_email" name="client_email" type="email" data-validation="required email" placeholder="Your email" />
						<button name="send_form" type="submit">Submit</button>
					</div>
				</div>
			</form>
			<div class="test-form__msg error hidden"></div>
			<div class="test-form__msg success hidden">
				<div class="header">Form Completed</div>
				<p>You are signed up for the newsletter.</p>
			</div>
        </div>
        </div>';
	return $output;
}

add_action( 'wp_ajax_testform_action', 'testform_action_callback' );
add_action( 'wp_ajax_nopriv_testform_action', 'testform_action_callback' );
function testform_action_callback() {
	if(!empty($_POST['client_email'])) { 
		global $wpdb, $table_prefix;
		$tablename =  $table_prefix . 'testform';
		$client_email = $_POST['client_email'];
		$query = "SELECT form_email FROM $tablename WHERE form_email = '$client_email'";
		$results = $wpdb->get_results($query);

		if(!empty($results)){
			echo json_encode(array('success'=> false, 'msg' => 'Such email exist in db. Please try another'));
		}
		else {
			entry_form_db();
		}
	}
	wp_die();

}

function entry_form_db()
{
	global $wpdb, $table_prefix;
	$tablename =  $table_prefix . 'testform';
	$data = array(
		'time' => date('Y-m-d H:i:s'),
		'form_email' => $_POST['client_email'],
		'form_ip' => $_SERVER['REMOTE_ADDR'],
		'form_browser' => $_POST['client_browser']
	);
	// FOR database SQL injection
	$formats = array(
		'%s',
		'%s', 
		'%s', 
		'%s' 
	);
	if ($wpdb->insert($tablename, $data, $formats)) {
		echo json_encode(array('success'=> true, 'msg' => ''));
	} else {
		echo json_encode(array('success'=> false, 'msg' => $wpdb->show_errors()));
	}
}