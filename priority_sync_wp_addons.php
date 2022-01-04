<?php

//Add ERP number to user profile
add_action('show_user_profile', 'prs_extra_user_profile_fields');
add_action('edit_user_profile', 'prs_extra_user_profile_fields');

function prs_extra_user_profile_fields($user)
{ ?>
	<h3><?php _e("מספר לקוח ERP", "blank"); ?></h3>

	<table class="form-table">
		<tr>
			<th><label for="erp_num"><?php _e("מספר לקוח"); ?></label></th>
			<td>
				<input type="text" name="erp_num" id="erp_num" value="<?php echo esc_attr(get_the_author_meta('erp_num', $user->ID)); ?>" class="regular-text" /><br />
			</td>
		</tr>
	</table>
<?php }

add_action('personal_options_update', 'prs_save_extra_user_profile_fields');
add_action('edit_user_profile_update', 'prs_save_extra_user_profile_fields');

function prs_save_extra_user_profile_fields($user_id)
{
	if (!current_user_can('edit_user', $user_id)) {
		return false;
	}
	update_user_meta($user_id, 'erp_num', $_POST['erp_num']);
}

add_filter('manage_users_columns', 'prs_add_new_user_column');

function prs_add_new_user_column($columns)
{
	$columns['erp_num'] = 'מספר ERP';
	return $columns;
}

add_filter('manage_users_custom_column', 'prs_add_new_user_column_content', 10, 3);

function prs_add_new_user_column_content($content, $column, $user_id)
{
	if ('erp_num' === $column) {
		$content = get_the_author_meta('erp_num', $user_id);
	}
	return $content;
}

/**
 * Set a minimum order amount for checkout
 */

add_action('woocommerce_after_checkout_form', 'prs_minimum_order_amount');
add_action('woocommerce_checkout_process', 'prs_minimum_order_amount');
add_action('woocommerce_after_cart', 'prs_minimum_order_amount');

function prs_minimum_order_amount()
{
	if (WC()->cart->total < PRS_MIN_ORDER) {

		if (is_rtl()) {
			$msg = 'הזמנה שלך היא %s מינימום הזמנה %s';
		} else {
			$msg = 'Your current order total is %s — you must have an order with a minimum of %s to place your order ';
		}
		if (is_cart()) {
			wc_print_notice(sprintf($msg, wc_price(WC()->cart->total), wc_price(PRS_MIN_ORDER)), 'error');
		} else {
			wc_add_notice(sprintf($msg . " !", wc_price(WC()->cart->total), wc_price(PRS_MIN_ORDER)), 'error');
		}
		echo "<script>
		(function ($) {
		$( document.body ).on( 'updated_cart_totals', function(){
            toggle_cart();
        });
        $( document.body ).on( 'updated_checkout', function(){
            toggle_cart();
        });
		toggle_cart();
    	
    	function toggle_cart(){
    	    let error = $(document.body).find('.woocommerce-error');
    	    if (error.length > 0) {
                $('.wc-proceed-to-checkout').hide();
                $('#order_review_heading').hide();
                $('#order_review').hide();
                $('#customer_details').hide();
                $('#payment').hide();
    	    }else{
        	    $('.wc-proceed-to-checkout').show();
        	    $('#order_review_heading').show();
                $('#order_review').show();
        	    $('#customer_details').hide();
        	    $('#payment').show();
    	    }
    	}
        })(jQuery);</script>";
	}
}

//to remove zero price items uncomment
//add_action('woocommerce_product_query', 'prs_product_query');
function prs_product_query($q)
{
	$meta_query = $q->get('meta_query');
	$meta_query[] = array(
		'key'       => '_price',
		'value'     => 0,
		'compare'   => '>'
	);
	$q->set('meta_query', $meta_query);
}

//remove add to cart button if price is 0
function prs_remove_add_to_cart_on_0($purchasable, $product)
{
	if ($product->get_price() == 0)
		$purchasable = false;
	return $purchasable;
}
add_filter('woocommerce_is_purchasable', 'prs_remove_add_to_cart_on_0', 10, 2);

//change 0.00 price to text
add_filter('woocommerce_get_price_html', 'prs_maybe_hide_price', 10, 2);
function prs_maybe_hide_price($price_html, $product)
{
	if ($product->get_price() > 0) {
		return $price_html;
	}
	return 'להזמנות נא להתקשר: 074-7155000';
}

//disable add to cart
//add_filter( 'woocommerce_is_purchasable', '__return_false');

//round up prices
//add_filter( 'woocommerce_get_price_excluding_tax', 'prs_round_price_product', 10, 1 );
//add_filter( 'woocommerce_get_price_including_tax', 'prs_round_price_product', 10, 1 );
//add_filter( 'woocommerce_tax_round', 'prs_round_price_product', 10, 1);

function prs_round_price_product($price)
{
	// Return rounded price
	return ceil($price);
}