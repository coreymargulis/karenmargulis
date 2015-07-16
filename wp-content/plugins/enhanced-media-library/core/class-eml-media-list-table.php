<?php

/**
 *  EML custom media table
 *
 *  Extends wp-admin/includes/class-wp-media-list-table.php
 * 
 *  Features added:
 *  All Uncategorized
 *
 *  @since    2.0.4
 *  @created  21/02/15
 */
 
class WPUXSS_EML_Media_List_Table extends WP_Media_List_Table {
    
    protected $detached;
    
    protected $uncategorized;
    
    protected $is_trash;
    

	public function __construct( $args = array() ) 
    {         
        $this->detached = ( isset( $_REQUEST['attachment-filter'] ) && 'detached' === $_REQUEST['attachment-filter'] );
        
		$this->uncategorized = ( isset( $_REQUEST['attachment-filter'] ) && 'uncategorized' === $_REQUEST['attachment-filter'] );
        
        $this->is_trash = isset( $_REQUEST['attachment-filter'] ) && 'trash' == $_REQUEST['attachment-filter'];

		parent::__construct();
	}
    
    
    protected function get_views() { 
     
		global $wpdb, $post_mime_types, $avail_post_mime_types;

		$type_links = array();
		$_num_posts = (array) wp_count_attachments();
		$_total_posts = array_sum($_num_posts) - $_num_posts['trash'];
		$total_orphans = $wpdb->get_var( "SELECT COUNT( * ) FROM $wpdb->posts WHERE post_type = 'attachment' AND post_status != 'trash' AND post_parent < 1" );
		$matches = wp_match_mime_types(array_keys($post_mime_types), array_keys($_num_posts));
		foreach ( $matches as $type => $reals )
			foreach ( $reals as $real )
				$num_posts[$type] = ( isset( $num_posts[$type] ) ) ? $num_posts[$type] + $_num_posts[$real] : $_num_posts[$real];

		$selected = empty( $_GET['attachment-filter'] ) ? ' selected="selected"' : '';
		$type_links['all'] = "<option value=''$selected>" . sprintf( _nx( 'All (%s)', 'All (%s)', $_total_posts, 'uploaded files' ), number_format_i18n( $_total_posts ) ) . '</option>';
		foreach ( $post_mime_types as $mime_type => $label ) {
			if ( !wp_match_mime_types($mime_type, $avail_post_mime_types) )
				continue;

			$selected = '';
			if ( !empty( $_GET['attachment-filter'] ) && strpos( $_GET['attachment-filter'], 'post_mime_type:' ) === 0 && wp_match_mime_types( $mime_type, str_replace( 'post_mime_type:', '', $_GET['attachment-filter'] ) ) )
				$selected = ' selected="selected"';
			if ( !empty( $num_posts[$mime_type] ) )
				$type_links[$mime_type] = '<option value="post_mime_type:' . esc_attr( $mime_type ) . '"' . $selected . '>' . sprintf( translate_nooped_plural( $label[2], $num_posts[$mime_type] ), number_format_i18n( $num_posts[$mime_type] )) . '</option>';
		}
		$type_links['detached'] = '<option value="detached"' . ( $this->detached ? ' selected="selected"' : '' ) . '>' . sprintf( _nx( 'Unattached (%s)', 'Unattached (%s)', $total_orphans, 'detached files' ), number_format_i18n( $total_orphans ) ) . '</option>';
        
        $type_links['uncategorized'] = '<option value="uncategorized"' . ( $this->uncategorized ? ' selected="selected"' : '' ) . '>' . __( 'All Uncategorized', 'eml' ) . '</option>';

		if ( !empty($_num_posts['trash']) )
			$type_links['trash'] = '<option value="trash"' . ( (isset($_GET['attachment-filter']) && $_GET['attachment-filter'] == 'trash' ) ? ' selected="selected"' : '') . '>' . sprintf( _nx( 'Trash (%s)', 'Trash (%s)', $_num_posts['trash'], 'uploaded files' ), number_format_i18n( $_num_posts['trash'] ) ) . '</option>';

		return $type_links;
	}
    
    protected function extra_tablenav( $which ) 
    {
		if ( 'bar' !== $which ) {
			return;
		}
?>
		<div class="actions">
<?php
		if ( ! is_singular() ) {
			if ( ! $this->is_trash ) {
				$this->months_dropdown( 'attachment' );
			}

			/** This action is documented in wp-admin/includes/class-wp-posts-list-table.php */
			do_action( 'restrict_manage_posts' );
			submit_button( __( 'Filter' ), 'button', 'filter_action', false, array( 'id' => 'post-query-submit' ) );
            
            submit_button( __( 'Reset All Filters' ), 'button', 'filter_action', false, array( 'id' => 'eml-reset-filters-query-submit', 'disabled' => 'disabled' ) );
		}

		if ( $this->is_trash && current_user_can( 'edit_others_posts' ) ) {
			submit_button( __( 'Empty Trash' ), 'apply', 'delete_all', false );
		} 
?>
		</div>
<?php
	}
    
}

?>