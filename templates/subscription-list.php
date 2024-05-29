<?php
$ns = "sm";
$counts = 1;

if(!empty($active_subscriptions)){
    // Access subscription data using $subscription object
    // Output or process subscription data as needed
    $html .= "<div class=\"{$ns}-inner-wrapper\">";
        $html .="<table class=\"wp-list-table widefat fixed striped table-view-list posts\">";
        $html .="<thead>";
            if($product_type == "object"){
                $html .="<th>Subscription ID</th>";
                $html .="<th>FLFE Object</th>";
                $html .="<th>600/650 Boost</th>";
				$html .="<th>850 Boost</th>";
                $html .="<th>On/Off</th>";
            }
            if($product_type == "phone"){
                $html .="<th>Subscription ID</th>";
                $html .="<th>FLFE Phone Number</th>";
				$html .="<th>600/650 Boost</th>";
				$html .="<th>850 Boost</th>";
				$html .="<th>On/Off</th>";
            }
            if($product_type == "property"){
                $html .="<th>Subscription ID</th>";
                $html .="<th>FLFE Address</th>";
				$html .="<th>600/650 Boost</th>";
				$html .="<th>850 Boost</th>";
                $html .="<th>On/Off</th>";
            }

        $html .="</thead>";
        $html .="<tbody>";
    foreach ( $active_subscriptions as $subscription_id ) {
        $subscription = wcs_get_subscription($subscription_id);
        $product_id = '';
        $phone_number = 'N/A';
        $product_image = 'N/A';
        $address = 'N/A';
        if($subscription){

			 $status = $subscription->get_status();
   			 if ($status === 'active') {
             $user_id = $subscription->get_user_id();
             $order_id = $subscription->get_parent_id();
             $subscription_edit_link = "/my-account/view-subscription/{$subscription_id}";
             $subscription_id_html = "<a target=\"_blank\" href=\"{$subscription_edit_link}\">#{$subscription_id}</a>";
             $order = wc_get_order($order_id);
			 $get_subscription_status = get_post_meta($subscription_id,"subscription_on_off",true);

			 if($get_subscription_status != 0){
				$on_off = '<label class="switch"><input data-id="'.$subscription_id.'" type="checkbox" checked class="trigger-on-off"><span class="slider round"></span></label>';
			 }
			 else{
				 $on_off = '<label class="switch"><input data-id="'.$subscription_id.'" type="checkbox" class="trigger-on-off"><span class="slider round"></span></label>';
			 }


             if ($order) {
                   $items = $order->get_items();
                   foreach ($items as $item_id => $item) {
                       $product_id = $item->get_product_id();

                   }
             }

             $subscription_boost_650_status = get_post_meta($subscription_id,'boost_650_status',true);
             $boost_650_schedule_status = get_post_meta($subscription_id,"boost_650_schedule_status",true);
             if(!empty($boost_650_schedule_status)){
                 $get_650_time = get_post_meta($subscription_id,'boost_650_time',true);
                 $time_duration = get_post_meta($product_id,'_boost_600_650',true);

                 if($time_duration){
                    $time_650_end = date('H:i',strtotime("{$get_650_time} + {$time_duration} minutes"));
                    $time_650_start = date('H:i',strtotime("{$get_650_time}"));
                 }
             }
             if($boost_650_schedule_status == 'active'){
                 if($subscription_boost_650_status == 1){
                     $active_650 = "Active till {$time_650_end}";
                 }
             }
             else if($boost_650_schedule_status == 'schedule'){
                    $active_650 = "Inactive. <br>Will Become Active at {$time_650_start}.";
             }
             else{
                   $active_650 = "<label>Inactive</label><input type=\"text\" readonly data-id=\"{$subscription_id}\" placeholder =\"Choose Time\" class=\"timepicker\" name=\"boost_650\">";
             }




             $subscription_boost_850_status = get_post_meta($subscription_id,'boost_850_status',true);
             $boost_850_schedule_status = get_post_meta($subscription_id,"boost_850_schedule_status",true);

             if(!empty($boost_850_schedule_status)){
                 $get_850_time = get_post_meta($subscription_id,'boost_850_time',true);
                 $time_duration_850 = get_post_meta($product_id,'_boost_850',true);

                 if($time_duration_850){
                    $time_850_end = date('H:i',strtotime("{$get_850_time} + {$time_duration_850} minutes"));
                    $time_850_start = date('H:i',strtotime("{$get_850_time}"));
                 }
             }
             if($boost_850_schedule_status == 'active'){
                 if($subscription_boost_850_status == 1){
                     $active_850 = "Active till {$time_850_end}";
                 }
             }
             else if($boost_850_schedule_status == 'schedule'){
                    $active_850 = "Inactive. <br>Will Become Active at {$time_850_start}.";
             }
             else{
                   $active_850 = "<label>Inactive</label><input type=\"text\" readonly data-id=\"{$subscription_id}\" placeholder =\"Choose Time\"  class=\"timepicker\" name=\"boost_850\">";
             }


             $subscription_boost = get_post_meta($subscription_id,"subscription_boost_order",true);
             $boost = (!empty($subscription_boost))? $subscription_boost : "N/A";

             $object = get_post_meta($order_id,"_alg_checkout_files_upload_1",true);
             $file_num = 1;

             $subscription_boost = get_post_meta($subscription_id,"subscription_boost_order",true);

             // Temporarily Added a constant Product ID 1239 (Your FLFE Object Sanctuary)
             if($product_id == 1239 && $product_type == "object"){
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
                    $product_subscription = "FLFE Object";

                    if(!empty($file_url)){
                        $product_image = "<a href=\"{$file_url}\"><img class=\"btn-sm-object popup-image\" style=\"max-width:200px\" src=\"{$file_url}\"><div class=\"popup-container\"></a>";
                    }
                    else{
                        $product_image = "No Object Uploaded";
                    }

                     $html .="<tr>";
                         $html .="<td>{$subscription_id_html}<br><div class=\"btn-wrapper\"><a href=\"{$subscription_edit_link}\" data-id=\"{$order_id}\">View</a></div></td>";
                         $html .="<td>{$product_image}</td>";
                          $html .="<td class=\"boost-list\">{$active_650}</td>";
    					 $html .="<td class=\"boost-list\">{$active_850}</td>";
    				 	$html .="<td>{$on_off}</td>";
                     $html .="</tr>";
             }
             // Temporarily Added a constant Product ID 1234 (FLFE Mobile Phone Sanctuary)
             else if($product_id == 1234 && $product_type == "phone"){
                 $product_subscription = "FLFE Mobile Phone";
                 $phone_number = get_post_meta($subscription_id,"flfe_phone",true);
                 $html .="<tr>";
                     $html .="<td>{$subscription_id_html}<br><div class=\"btn-wrapper\"><a href=\"{$subscription_edit_link}\" data-id=\"{$order_id}\">View</a></div></td>";
                      $html .="<td>{$phone_number}</td>";

                        // Format and output the datetime in 'Y-m-d H:i:s' format
                     $html .="<td class=\"boost-list\">{$active_650}</td>";

					 $html .="<td class=\"boost-list\">{$active_850}</td>";
				  $html .="<td>{$on_off}</td>";
                 $html .="</tr>";
             }
             // Temporarily Added a constant Product ID 1223 (FLFE Property Subscription)
             else if($product_id == 1223 && $product_type == "property"){
                 $shipping_address = $order->get_shipping_address_1();
                  $shipping_city = $order->get_shipping_city();
                  $shipping_postcode = $order->get_shipping_postcode();
                  $shipping_country = $order->get_shipping_country();
                  $shipping_state = $order->get_shipping_state();
                  $product_subscription = "FLFE Property";
                  $address = "{$shipping_address}, {$shipping_city}, {$shipping_postcode}, {$shipping_country}, {$shipping_state}";
                  $html .="<tr>";
                      $html .="<td>{$subscription_id_html}<br><div class=\"btn-wrapper\"><a href=\"{$subscription_edit_link}\" data-id=\"{$order_id}\">View</a></div></td>";
                      $html .="<td>{$address}</td>";
                      $html .="<td class=\"boost-list\">{$active_650}</td>";
					  $html .="<td class=\"boost-list\">{$active_850}</td>";
				   $html .="<td>{$on_off}</td>";
                  $html .="</tr>";
             }

             if(!empty($subscription_boost)){
                 $order_boost = wc_get_order($order_id);
                 if ($order_boost) {
                       $items = $order_boost->get_items();
                       foreach ($items as $item_id => $item) {
                           $product_id = $item->get_product_id();
                       }

                       $subscription_boost_850_status_add_on = get_post_meta($subscription_id,'boost_850_status_add_on',true);
                       $boost_850_schedule_status_add_on = get_post_meta($subscription_id,"boost_850_schedule_status_add_on",true);

                       if(!empty($boost_850_schedule_status_add_on)){
                           $get_850_time_add_on = get_post_meta($subscription_id,'boost_850_time_add_on',true);
                           $time_duration_850_add_on = get_post_meta($product_id,'_boost_850',true);

                           if($time_duration_850_add_on){
                              $time_850_end_add_on = date('H:i',strtotime("{$get_850_time_add_on} + {$time_duration_850_add_on} minutes"));
                              $time_850_start_add_on = date('H:i',strtotime("{$get_850_time_add_on}"));
                           }
                       }
                       if($boost_850_schedule_status_add_on == 'active'){
                           if($subscription_boost_850_status_add_on == 1){
                               $active_850_add_on = "Active till {$time_850_end_add_on}";
                           }
                       }
                       else if($boost_850_schedule_status_add_on == 'schedule'){
                              $active_850_add_on = "Inactive. <br>Will Become Active at {$time_850_start_add_on}.";
                       }
                       else{
                             $active_850_add_on = "<label>Inactive</label><input type=\"text\" readonly data-id=\"{$subscription_id}\" placeholder =\"Choose Time\"  class=\"timepicker\" name=\"boost_850_add_on\">";
                       }


                       $subscription_boost_650_status_add_on = get_post_meta($subscription_id,'boost_650_status_add_on',true);
                       $boost_650_schedule_status_add_on = get_post_meta($subscription_id,"boost_650_schedule_status_add_on",true);
                       if(!empty($boost_650_schedule_status_add_on)){
                           $get_650_time_add_on = get_post_meta($subscription_id,'boost_650_time_add_on',true);
                           $time_duration_add_on = get_post_meta($product_id,'_boost_600_650',true);

                           if($time_duration_add_on){
                              $time_650_end_add_on = date('H:i',strtotime("{$get_650_time_add_on} + {$time_duration_add_on} minutes"));
                              $time_650_start_add_on = date('H:i',strtotime("{$get_650_time_add_on}"));
                           }
                       }
                       if($boost_650_schedule_status_add_on == 'active'){
                           if($subscription_boost_650_status_add_on == 1){
                               $active_650_add_on = "Active till {$time_650_end_add_on}";
                           }
                       }
                       else if($boost_650_schedule_status_add_on == 'schedule'){
                              $active_650_add_on = "Inactive. <br>Will Become Active at {$time_650_start_add_on}.";
                       }
                       else{
                             $active_650_add_on = "<label>Inactive</label><input type=\"text\" readonly data-id=\"{$subscription_id}\" placeholder =\"Choose Time\" class=\"timepicker\" name=\"boost_650_add_on\">";
                       }

                       $html .="<tr class=\"add-on-wrapper\">";
                           $html .="<td class=\"wrapper-addon-boost\" colspan=2>Add-on Boost for {$subscription_id_html}</td>";
                           $html .="<td class=\"boost-list\">{$active_650_add_on}</td>";
     					  $html .="<td class=\"boost-list\">{$active_850_add_on}</td>";
     				   $html .="<td></td>";
                       $html .="</tr>";
                 }
             }

		  }
        }
    }
    $html .="</tbody>";
    $html .="</table>";

    $html .="</div>";
}
else{
    if($product_type == "property"){
           $html .="<div class=\"btn-no-subscription\">You don't have any property subscriptions, go <a href='/checkouts/property-checkout'>here</a> to buy now</div>";
    }
    else  if($product_type == "phone"){
             $html .="<div class=\"btn-no-subscription\">You don't have any phone subscriptions, go <a href='/checkouts/flfe-phone-checkout/'>here</a> to buy now</div>";
    }
    else  if($product_type == "object"){
           $html .="<div class=\"btn-no-subscription\">You don't have any object subscriptions, go <a href='/checkouts/flfe-object-checkout/'>here</a> to buy now</div>";
    }
}


?>
