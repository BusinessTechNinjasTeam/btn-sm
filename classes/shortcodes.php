<?php
if (! defined('ABSPATH')) {
	header('HTTP/1.0 403 Forbidden');
	die();
}

/**
 * Shortcode Class
 *
 * @since      1.0.0
 * @package    btn-sm
 * @subpackage btn-sm/classes
 * @author     Augustus Villanueva <augustus@businesstechninjas.com>
 */
final class btn_sm_shortcodes {
	static function subscription($atts, $content, $tag) {
		$args = shortcode_atts( [
			'product_type'	=> '',
			'product_id' => ''
		], $atts );

		$product_type = $args['product_type'];
		$product_id = $args['product_id'];
		$user_id = get_current_user_id(); // Assuming you want to get the subscriptions for the currently logged-in user
		global $wpdb;
		$query = "
			SELECT DISTINCT p.ID AS subscription_id
			FROM {$wpdb->prefix}posts AS p
			INNER JOIN {$wpdb->prefix}postmeta AS pm ON p.ID = pm.post_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_items AS woi ON p.ID = woi.order_id
			INNER JOIN {$wpdb->prefix}woocommerce_order_itemmeta AS woim ON woi.order_item_id = woim.order_item_id
			WHERE p.post_type = 'shop_subscription'
			AND p.post_status = 'wc-active'
			AND pm.meta_key = '_customer_user'
			AND pm.meta_value = %d
			AND woim.meta_key = '_product_id'
			AND woim.meta_value = %d
		";
		 $active_subscriptions = $wpdb->get_col($wpdb->prepare($query, $user_id, $product_id));

		$template = btn_sm()->template_part_path("subscription-list.php");
		if($template){
			include $template;
		}
		btn_sm()->frontend()->set_json('btn_sm_shortcode', $args);
		return $html;
	}

	//Use to list the subscription on the funnelkit /checkouts/boost/
	static function subscription_list($atts, $content, $tag) {
		$user_id = get_current_user_id();
		$html = "";
	    // Check if the WC_Subscriptions function exists to ensure the subscriptions plugin is active
	    if (function_exists('wcs_get_subscriptions')) {

	        // Get all active subscriptions for a specific user
	        $subscriptions = wcs_get_subscriptions([
	            'subscriptions_per_page' => -1, // Retrieve all subscriptions
	            'subscription_status' => 'active', // Only get active subscriptions
	            'customer_id' => $user_id, // Filter by user ID
	        ]);

	        // Check if there are any subscriptions found
	        if (!empty($subscriptions)) {
				$html .="<select id =\"boost-wrapper-select\" name=\"boost-wrapper-select\">";
					$html .="<option value=\"\" disabled selected>Select Subscription to boost</option>";
	            foreach ($subscriptions as $subscription) {
	                // Get the items/products for the current subscription
	                $items = $subscription->get_items();

	                foreach ($items as $item) {
	                    // Fetch product details
	                    $product_id = $item->get_product_id();
	                    $product = wc_get_product($product_id);
						$html .="<option value=\"{$subscription->get_id()}\">{$product->get_name()}</option>";
	                }
	            }
				$html .="</select>";
	        } else {
	            $html .= "No active subscriptions found for the user.";
	        }
	    }
		return $html;
	}



function __construct(){}



}

