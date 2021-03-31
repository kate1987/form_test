<?php

if ( ! class_exists ( 'WP_List_Table' ) ) {
    require_once ABSPATH . 'wp-admin/includes/class-wp-list-table.php';
}

/**
 * List table class
 */
class testform extends \WP_List_Table {

    function __construct() {
        parent::__construct( array(
            'singular' => 'testform',
            'plural'   => 'testform',
            'ajax'     => false
        ) );
    }

    function get_table_classes() {
        return array( 'widefat', 'fixed', 'striped', $this->_args['plural'] );
    }

    /**
     * Message to show if no designation found
     *
     * @return void
     */
    function no_items() {
        _e( 'No items', '' );
    }

    /**
     * Default column values if no callback found
     *
     * @param  object  $item
     * @param  string  $column_name
     *
     * @return string
     */
    function column_default( $item, $column_name ) {

        switch ( $column_name ) {
            case 'form_email':
                return $item->form_email;

            case 'form_ip':
                return $item->form_ip;

            case 'form_browser':
                return $item->form_browser;

            default:
                return isset( $item->$column_name ) ? $item->$column_name : '';
        }
    }

    /**
     * Get the column names
     *
     * @return array
     */
    function get_columns() {
        $columns = array(
            'cb'           => '<input type="checkbox" />',
            'form_email'      => __( 'Client Email', '' ),
            'form_ip'      => __( 'Client IP', '' ),
            'form_browser'      => __( 'Client browser', '' ),

        );

        return $columns;
    }

    /**
     * Render the designation name column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_form_email( $item ) {

        $actions           = array();
        $actions['delete'] = sprintf( '<a href="%s" class="submitdelete" data-id="%d" title="%s">%s</a>', admin_url( 'admin.php?page=test-form&action=delete&id=' . $item->id ), $item->id, __( 'Delete this item', '' ), __( 'Delete', '' ) );

        return sprintf( '<a href="%1$s"><strong>%2$s</strong></a> %3$s', admin_url( 'admin.php?page=test-form&action=view&id=' . $item->id ), $item->form_email, $this->row_actions( $actions ) );
    }

    /**
     * Get sortable columns
     *
     * @return array
     */
    function get_sortable_columns() {
        $sortable_columns = array(
            'name' => array( 'name', true ),
        );

        return $sortable_columns;
    }

    /**
     * Set the bulk actions
     *
     * @return array
     */
    function get_bulk_actions() {
        $actions = array(
            'trash'  => __( 'Move to Trash', '' ),
        );
        return $actions;
    }

    public function process_bulk_action() {

        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }

        $action = $this->current_action();

        switch ( $action ) {

            case 'delete':
                global $wpdb;
                $table_name = $wpdb->prefix . 'testform';
                $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
                    if (is_array($ids)) $ids = implode(',', $ids);
                    if (!empty($ids)) {
                        $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                }

            echo '<strong style="color: red;">You have deleted this item succesfully</strong>';
            break;

            case 'trash':
                global $wpdb;
                $table_name = $wpdb->prefix . 'testform';
                $ids = isset($_REQUEST['id']) ? $_REQUEST['id'] : array();
                    if (is_array($ids)) $ids = implode(',', $ids);
                    if (!empty($ids)) {
                        $wpdb->query("DELETE FROM $table_name WHERE id IN($ids)");
                }

                echo '<strong style="color: red;">You have deleted this items succesfully</strong>';
            break;

            default:
                // do nothing or something else
                return;
                break;
        }

        return;
    }

    /**
     * Render the checkbox column
     *
     * @param  object  $item
     *
     * @return string
     */
    function column_cb( $item ) {
        return sprintf(
            '<input type="checkbox" name="id[]" value="%d" />', $item->id
        );
    }


    /**
     * Prepare the class items
     *
     * @return void
     */
    function prepare_items() {

        $columns               = $this->get_columns();
        $hidden                = array( );
        $sortable              = $this->get_sortable_columns();
        $this->_column_headers = array( $columns, $hidden, $sortable );

        $per_page              = '20';
        $current_page          = $this->get_pagenum();
        $offset                = ( $current_page -1 ) * $per_page;
        $this->page_status     = isset( $_GET['status'] ) ? sanitize_text_field( $_GET['status'] ) : '2';

        // only ncessary because we have sample data
        $args = array(
            'offset' => $offset,
            'number' => $per_page,
        );

        if ( isset( $_REQUEST['orderby'] ) && isset( $_REQUEST['order'] ) ) {
            $args['orderby'] = $_REQUEST['orderby'];
            $args['order']   = $_REQUEST['order'] ;
        }

        $this->items  = _get_all_testform( $args );

        $this->process_bulk_action();

        $this->set_pagination_args( array(
            'total_items' => _get_testform_count(),
            'per_page'    => $per_page
        ) );
    }
}