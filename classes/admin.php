<?php
if (! defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

/**
 * Frontend Class
 *
 * @since      1.0.0
 * @package    btn-sm
 * @subpackage btn-sm/classes
 * @author    Augustus Villanueva <augustus@businesstechninjas.com>
 */
final class btn_sm_admin {

    // Wordpress Hooks ( Actions & Filters )
    function add_wp_hooks() {
			add_filter( 'woocommerce_product_data_tabs',  [$this,'add_boost_product_data_tab' ] );
			add_action( 'woocommerce_product_data_panels',  [$this,'add_boost_options_product_data'] );
			add_action( 'woocommerce_admin_process_product_object',  [$this,'save_custom_field_with_subscription_product'], 10, 1 );
			add_filter( 'manage_edit-shop_subscription_columns',  [$this, 'add_custom_subscription_column'], 10, 1 );
 		   	add_action( 'manage_shop_subscription_posts_custom_column',   [$this, 'custom_subscription_column_content'], 10, 2 );
    }

	/* Add Boost Tab for Product Editor */
	function add_boost_product_data_tab( $tabs ) {
	    // Adds a custom "Boost" tab
	    $tabs['boost_tab'] = array(
	        'label'  => __( 'Boost', 'btn-sm' ),
	        'target' => 'boost_options_product_data',
	        'class'  => array( 'show_if_simple', 'show_if_variable' ),
	    );

	    return $tabs;
	}

	/* Add Boost Field for Product Editor */
	function add_boost_options_product_data() {
	    echo '<div id="boost_options_product_data" class="panel woocommerce_options_panel hidden">';
	    woocommerce_wp_select( array(
	        'id'          => '_boost_600_650',
	        'label'       => __( '600/650 Boost ', 'btn-sm' ),
	        'description' => __( 'Choose an option from the 600/650 Boost .', 'btn-sm' ),
	        'options'     => array(
	            ''        => __( 'Select an option', 'btn-sm' ),
	            '30' => __( '1/2 hr - 600 LOC', 'btn-sm' ),
	            '10' => __( '10 min - 650 LOC', 'btn-sm' ),
				'2' => __( '2 min', 'btn-sm' ),
	        ),
	        'desc_tip'    => true,
	    ));


		woocommerce_wp_select( array(
	        'id'          => '_boost_850',
	        'label'       => __( '850 Boost ', 'btn-sm' ),
	        'description' => __( 'Choose an option from the 600/650 Boost .', 'btn-sm' ),
	        'options'     => array(
	            ''        => __( 'Select an option', 'btn-sm' ),
	            '30' => __( '1/2 hr - 600 LOC', 'btn-sm' ),
	            '10' => __( '10 min - 650 LOC', 'btn-sm' ),
				'2' => __( '2 min', 'btn-sm' ),
	        ),
	        'desc_tip'    => true,
	    ));
	    echo '</div>';
	}

	/* Save Boost field */
	function save_custom_field_with_subscription_product( $product ) {
	    if ( isset( $_POST['_boost_600_650'] ) ) {
	        $product->update_meta_data( '_boost_600_650', sanitize_text_field( $_POST['_boost_600_650'] ) );
	    }
		if ( isset( $_POST['_boost_850'] ) ) {
		   $product->update_meta_data( '_boost_850', sanitize_text_field( $_POST['_boost_850'] ) );
	   }
	}

	//Added Boosted Status column in subscription backend
	function add_custom_subscription_column( $columns ) {
	    // Add a new column with 'custom_column' as the key and 'Custom Column' as the column name
	    $columns['boosted'] = __( 'Boosted', 'woocommerce' );
		$columns['turn_on_off'] = __( 'Turn On/Off', 'woocommerce' );
	    return $columns;
	}

	//Added Boosted Status Value in subscription backend
	function custom_subscription_column_content( $column, $subscription_id ) {
		switch ( $column ) {
			case 'boosted':
				// Retrieve data based on the subscription ID ($post_id)
				// For demonstration, we're simply echoing "Example content"
				$subscription_boost = get_post_meta($subscription_id,"subscription_boost_order",true);
				$boost = (!empty($subscription_boost))? "<a target=\"_blank\" href=\"/wp-admin/post.php?post={$subscription_boost}&action=edit\">Yes</a>" : "No";
				echo $boost;
				break;
			case 'turn_on_off':

				$get_subscription_status = get_post_meta($subscription_id,"subscription_on_off",true);

				 if($get_subscription_status != 0){
					$on_off = "On";
				 }
				 else{
					$on_off = "Off";
				 }
				// Retrieve data based on the subscription ID ($post_id)
				// For demonstration, we're simply echoing "Example content"

				echo $on_off;
				break;
		}
	}


	function __construct(){}

	// JSON Data for JS
	private $to_json = [];
	private $enqueue_css = false;
	// Shortcode Mapping
	private $shortcode_map;
	// Current User Data
	private $user = null;
}
