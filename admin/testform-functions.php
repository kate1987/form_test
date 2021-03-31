<?php

/**
 * Get all testform
 *
 * @param $args array
 *
 * @return array
 */
function _get_all_testform( $args = array() ) {
    global $wpdb;

    $defaults = array(
        'number'     => 20,
        'offset'     => 0,
        'orderby'    => 'id',
        'order'      => 'ASC',
    );

    $args      = wp_parse_args( $args, $defaults );
    $cache_key = 'testform-all';
    $items     = wp_cache_get( $cache_key, '' );

    if ( false === $items ) {
        $items = $wpdb->get_results( 'SELECT * FROM ' . $wpdb->prefix . 'testform ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number'] );

        wp_cache_set( $cache_key, $items, '' );
    }

    return $items;
}

/**
 * Fetch all testform from database
 *
 * @return array
 */
function _get_testform_count() {
    global $wpdb;

    return (int) $wpdb->get_var( 'SELECT COUNT(*) FROM ' . $wpdb->prefix . 'testform' );
}


add_action('admin_menu', 'tf_add_admin_pages');
function tf_add_admin_pages()
{
	add_menu_page('Test form', 'Test form', 'create_users', 'test-form', 'tf_form_page', 'data:image/svg+xml;base64,' . base64_encode('<svg width="50" height="50" viewBox="0 0 512 512" xmlns="http://www.w3.org/2000/svg"><path fill="#9ea3a8" xmlns="http://www.w3.org/2000/svg" d="M467,61H45C20.218,61,0,81.196,0,106v300c0,24.72,20.128,45,45,45h422c24.72,0,45-20.128,45-45V106 C512,81.28,491.872,61,467,61z M460.786,91L256.954,294.833L51.359,91H460.786z M30,399.788V112.069l144.479,143.24L30,399.788z M51.213,421l144.57-144.57l50.657,50.222c5.864,5.814,15.327,5.795,21.167-0.046L317,277.213L460.787,421H51.213z M482,399.787 L338.213,256L482,112.212V399.787z"/></svg>'), 6); //$page_title, $menu_title, $capability, $menu_slug, $function, $icon_url, $position
}

function tf_form_page()
{
	if (!current_user_can('create_users')) {
		wp_die(__('You do not have sufficient permissions to access this page.', 'test-form'));
	}
?>
	<div class="wrap">
		<h2><?php _e('Clients Subscriber form submissions', ''); ?></h2>
		<p style="color: green; font-weight: bold;"><?php _e('To add form to frontend please use shortcode [test_form]', ''); ?></p>
		<form method="post">
			<input type="hidden" name="page" value="ttest_list_table">
			<?php
			$list_table = new testform();
			$list_table->prepare_items();
			$list_table->display();
			?>
		</form>
	</div>
<?php
}