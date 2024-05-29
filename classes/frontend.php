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
final class btn_sm_frontend {

    // Wordpress Hooks ( Actions & Filters )
    function add_wp_hooks() {

		    add_action('wp_enqueue_scripts',[$this,'enqueue']);
        	add_action('wp_footer', [$this,'frontend_print_scripts']);
			add_filter( 'woocommerce_thankyou', [$this, 'thankyou'], 10, 1 );

    		// Shortcodes
    		$this->register_shortcodes();
			add_action( "woocommerce_subscription_totals_table", [$this, 'plugin_purchase_subscription_details_customer'], 10, 1 );
    }

	function thankyou($order_id ) {
		$order = wc_get_order($order_id);
		$subscription_boost = get_post_meta($order_id, 'subscription_boost',true);
		if(!empty($subscription_boost)){
			update_post_meta($subscription_boost,"subscription_boost_order",$order_id);
		}
	}
	function plugin_purchase_subscription_details_customer( $subscription ) {
		$html = "";
		// Only display on View Order
		if( is_wc_endpoint_url('view-subscription') || !empty($subscription) ){
			$order_id = $subscription->get_parent_id();
			$subscription_id = $subscription->get_id();
			$order = wc_get_order($order_id);
			if( ! empty($order_id) ){
				 if ($order) {
                   $items = $order->get_items();
                   foreach ($items as $item_id => $item) {
                       $product_id = $item->get_product_id();
                   }

				   // Temporarily Added a constant Product ID 1239 (Your FLFE Object Sanctuary)
					if($product_id == 1239){
						global $wpdb;
						 $query = $wpdb->prepare( "
							SELECT
								woim.meta_value
							FROM {$wpdb->prefix}woocommerce_order_itemmeta woim
							RIGHT JOIN {$wpdb->prefix}woocommerce_order_items woi ON woim.order_item_id = woi.order_item_id
							WHERE woi.order_id = %d AND woi.order_item_name = 'File Upload' AND woim.meta_key = '_wc_checkout_add_on_value'
							",
							$order_id
						);

					  $file_id = $wpdb->get_var( $query );
					  $file_url = wp_get_attachment_url($file_id);
				   	  $product_type = "FLFE Object";
						if(!empty($file_url)){
							$product_image = "<a href=\"{$file_url}\"><img class=\"btn-sm-object popup-image\" style=\"max-width:300px\" src=\"{$file_url}\"><div class=\"popup-container\"></a>";
						}
						else{
							 $product_image = "No Object Uploaded";
						}


						$html .="<div class=\"wrapper-subscription-additional-content\">";
							$html .="<h2>Your FLFE Object</h2>";
							$html .="<div class=\"wrapper-subscription-additional-content-inner\">";
								$html .= $product_image;
							$html .="</div>";
						$html .="</div>";
					  }
					 // Temporarily Added a constant Product ID 1234 (FLFE Mobile Phone Sanctuary)
					 else if($product_id == 1234){
						$html .="<div class=\"wrapper-subscription-additional-content\">";
							$html .="<h2>Your FLFE Phone Number</h2>";
							$html .="<div class=\"wrapper-subscription-additional-content-inner\">";
								$html .= get_post_meta($subscription_id,"flfe_phone",true);
							$html .="</div>";
						$html .="</div>";
					}
					// Temporarily Added a constant Product ID 1223 (FLFE Property Subscription)
					else if($product_id == 1223){
						$shipping_address = $order->get_shipping_address_1();
						$shipping_city = $order->get_shipping_city();
						$shipping_postcode = $order->get_shipping_postcode();
						$shipping_country = $order->get_shipping_country();
						$shipping_state = $order->get_shipping_state();
						$address = "{$shipping_address}, {$shipping_city}, {$shipping_postcode}, {$shipping_country}, {$shipping_state}";
						$html .="<div class=\"wrapper-subscription-additional-content\">";
							$html .="<h2>Your FLFE Property</h2>";
							$html .="<div class=\"wrapper-subscription-additional-content-inner\">";
								$html .= $address;
							$html .="</div>";
						$html .="</div>";

					}
               }
			}
		}
		 echo $html;

	}


	// Register Shortcodes
	function register_shortcodes(){

		$prefix = "btn_sm_";
		$this->shortcode_map = [
			"{$prefix}subscription"	=> "{$prefix}shortcodes",
			"{$prefix}subscription_list"=> "{$prefix}shortcodes",
		];

		foreach ($this->shortcode_map as $tag => $class) {
			add_shortcode($tag, [$this, "shortcode_mapping"]);
		}
	}

	// Shortcode Mapping Function
	// Only includes suporting classes as needed
	function shortcode_mapping( $atts, $content, $tag ){
		$html = '';
		if( isset($this->shortcode_map[$tag]) ){
			$class = $this->shortcode_map[$tag];
			if( class_exists($class) ){
				$prefix = "btn_sm_";
				$func = str_replace($prefix, '', $tag);
				if( method_exists($class, $func) ){
					$html = call_user_func([$class, $func], $atts, $content, $tag);
				}
				else {
					error_log("Function {$class} does not exist");
				}
			}
			else {
				error_log("Class {$class} does not exist");
			}
		}
		return $html;
	}

	// Enqueue Scripts
 function enqueue(){
		$url = BTN_SM_ASSETS_URL;
		$v = BTN_SM_VERSION;

		wp_register_style('btn-sm-frontend-css', "{$url}css/frontend.css", [], $v, 'all');
		wp_enqueue_style('btn-sm-frontend-css');
		wp_register_script('btn-sm-frontend-js', "{$url}js/frontend.js", ['jquery', 'jquery-ui-tabs'], $v, 'all');
		// Enqueue jQuery UI CSS
		wp_enqueue_style('jquery-ui-css', '//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css');
		// Enqueue jQuery UI Timepicker Addon CSS
		wp_enqueue_style('jquery-ui-timepicker-addon-css', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.css');
		// Enqueue jQuery UI
		wp_enqueue_script('jquery-ui-datepicker');
		// Enqueue jQuery UI Timepicker Addon
		wp_enqueue_script('jquery-ui-timepicker-addon', '//cdnjs.cloudflare.com/ajax/libs/jquery-ui-timepicker-addon/1.6.3/jquery-ui-timepicker-addon.min.js', array('jquery', 'jquery-ui-datepicker'));
	}

  	// Footer Scripts
	function frontend_print_scripts(){
	  $to_json = $this->get_json();
	  if ( empty($to_json)){
	        return;
	  } else {
		$to_json['ajax_url'] = admin_url( 'admin-ajax.php' );
		wp_localize_script( 'btn-sm-frontend-js', 'btn_sm_data', $to_json );
		wp_enqueue_script('btn-sm-frontend-js');
	    wp_enqueue_style('btn-sm-frontend-css');
	  }
	}

	// Set JSON Data
    function set_json($key, $value = false) {
		if ($value) {
			$this->to_json[$key] = $value;
		}
		else {
			unset($this->to_json[$key]);
		}
	}

    // Get JSON Data
	function get_json($key = false) {
		if ($key) {
			return (isset($this->to_json[$key])) ? $this->to_json[$key] : null;
		}
		else {
			return $this->to_json;
		}
	}

	function __construct(){}

	// JSON Data for JS
	private $to_json = [];
	// Shortcode Mapping
	private $shortcode_map;
	// Current User Data
	private $user = null;
}
