<?php
	
function it_exchange_one_click_export_addon_page() {
?>
<div class="wrap help-wrap">
	<?php ITUtility::screen_icon( 'it-exchange' );  ?>
	<h2><?php _e( 'One Click Export', 'LION' ); ?></h2>

	<p class="top-description"><?php printf( __( 'We should say something special here... maybe', 'LION' ) ); ?></p>

	<div class="one-click-export-section-wrap clearfix">
		<form action="" method="POST">
			<input type="hidden" value="0" name="n" />
			<?php submit_button( __( 'Generate Export Files', 'LION' ), 'button', 'one-click-export' ); ?>
		</form>
		<hr />
		<?php
		if ( !empty( $_REQUEST['one-click-export'] ) ) {
			$variants_plugin_enabled = false;
			$date_format = get_option( 'date_format' );
			$limit = 20;
			$page = !empty( $_REQUEST['n'] ) ? $_REQUEST['n'] : 0;
			$args = array(
				'posts_per_page' => $limit,
				'offset'         => $page,
				'post_type'      => 'it_exchange_prod',
				'post_status'    => 'all',
			);
			
			$products = get_posts( $args );
			if ( !empty( $products ) ) {
				if ( 0 === $page ) {
					$f = fopen( 'ithemes-exchanges-products.csv', 'w' );
				} else {
					$f = fopen( 'ithemes-exchanges-products.csv', 'a' );
				}
				$headings = array(
					//Core
					__( 'Title', 'LION' ),
					__( 'Base Price', 'LION' ),
					__( 'Description', 'LION' ),
					__( 'Extended Description', 'LION' ),
					__( 'Type', 'LION' ),
					__( 'Status', 'LION' ),
					__( 'Visibility', 'LION' ),
					__( 'Download Meta', 'LION' ),
					__( 'Availability', 'LION' ),
					__( 'Inventory', 'LION' ),
					__( 'Purchase Message', 'LION' ),
					__( 'Quantity', 'LION' ),
					__( 'Images', 'LION' ),
					__( 'Shipping Weight', 'LION' ),
					__( 'Shipping Dimensions', 'LION' ),
					
					/* Membership */
					__( 'Membership Content Access', 'LION' ),
					__( 'Membership Intended Audience', 'LION' ),
					__( 'Membership Objectives', 'LION' ),
					__( 'Membership Prerequisites', 'LION' ),
					__( 'Membership Parent IDs', 'LION' ),
					__( 'Membership Child IDs', 'LION' ),
					__( 'Membership Welcome Message', 'LION' ),
					
					/* Recurring Payments */
					__( 'Recurring Payment Options', 'LION' ),
					
					/* Variants */
					__( 'Variants', 'LION' ),
					__( 'Variant Inventory', 'LION' ),
					__( 'Variant Pricing', 'LION' ),
					__( 'Variant Images', 'LION' ),
										
				);
				fwrite( $f, implode( ',', $headings ) . "\n" );
				echo '<h3>' . join( ',', $headings ) . "</h3><br />";
				foreach( $products as $product ) {
					$meta = get_post_meta( $product->ID );
					//print_r( $meta );
					$line = array();
					$line[] = it_exchange_one_click_export_escape_csv_value( $product->post_title ); //Product Title
					
					if ( !empty( $meta['_it-exchange-base-price'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-base-price'][0] );
					} else {
						$line[] = '';
					}
					
					if ( !empty( $meta['_it-exchange-product-description'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-description'][0] );
					} else {
						$line[] = '';
					}
					
					$line[] = it_exchange_one_click_export_escape_csv_value( $product->post_content ); //Product Extended Description
					
					if ( !empty( $meta['_it_exchange_product_type'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_product_type'][0] );
					} else {
						$line[] = '';
					}
					
					$line[] = it_exchange_one_click_export_escape_csv_value( $product->post_status ); //Product Status
					
					if ( !empty( $meta['_it-exchange-visibility'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-visibility'][0] );
					} else {
						$line[] = '';
					}
					
					if ( !empty( $meta['_it-exchange-download-meta'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_serialized_arrays_with_keys( $meta['_it-exchange-download-meta'][0] ) );
					} else {
						$line[] = '';
					}
					
					$availability = array();
					if ( !empty( $meta['_it-exchange-enable-product-availability-start'][0] ) 
						&& 'yes' === $meta['_it-exchange-enable-product-availability-start'][0]
						&& !empty( $meta['_it-exchange-product-availability'][0] ) ) {
						$tmp = maybe_unserialize( $meta['_it-exchange-product-availability'][0] );
						$availability['start'] = date_i18n( $date_format, $tmp['start'] );
					} else {
						$availability['start'] = 'disabled';
					}
					
					if ( !empty( $meta['_it-exchange-enable-product-availability-end'][0] ) 
						&& 'yes' === $meta['_it-exchange-enable-product-availability-end'][0]
						&& !empty( $meta['_it-exchange-product-availability'][0] ) ) {
						$tmp = maybe_unserialize( $meta['_it-exchange-product-availability'][0] );
						$availability['end'] = date_i18n( $date_format, $tmp['end'] );
					} else {
						$availability['end'] = 'disabled';
					}
					$availability = maybe_serialize( $availability );
					$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_serialized_arrays_with_keys( $availability ) );

					if ( !empty( $meta['_it-exchange-product-enable-inventory'][0] ) 
						&& 'yes' === $meta['_it-exchange-product-enable-inventory'][0] ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-inventory'][0] );
					} else {
						$line[] = '';
					}
					
					if ( !empty( $meta['_it-exchange-product-purchase-message'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-purchase-message'][0] );
					} else {
						$line[] = '';
					}
					
					if ( !empty( $meta['_it_exchange_product_allow_quantity'][0] ) 
						&& 'yes' === $meta['_it_exchange_product_allow_quantity'][0]
						&& !empty( $meta['_it-exchange-product-quantity'][0] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-quantity'][0] );
					} else {
						$line[] = '';
					}
					
					if ( !empty( $meta['_it-exchange-product-images'][0] ) ) {
						$images = array();
						$tmp = maybe_unserialize( $meta['_it-exchange-product-images'][0] );
						foreach( $tmp as $image_id ) {
							$images[] = wp_get_attachment_url( $image_id );
						}
						$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_serialized_arrays_without_keys( $images ) );
					} else {
						$line[] = '';
					}
					
				    if ( !empty( $meta['_it_exchange_core_weight'][0] ) ) {
					    $tmp = maybe_unserialize( $meta['_it_exchange_core_weight'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp['weight'] );
				    } else {
					    $line[] = '';
				    }
					
				    if ( !empty( $meta['_it_exchange_core_dimensions'][0] ) ) {
					    $tmp = maybe_unserialize( $meta['_it_exchange_core_dimensions'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp['length'] . ' x ' . $tmp['width'] . ' x '. $tmp['height'] );
				    } else {
					    $line[] = '';
				    }
					
					/* Membership */
					if ( !empty( $meta['_it-exchange-membership-addon-content-access-meta'] ) ) {
						$line[] = it_exchange_one_click_export_membership_addon_build_content_rules( $meta['_it-exchange-membership-addon-content-access-meta'][0] ); //Product Download Meta
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-product-membership-intended-audience'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-membership-intended-audience'][0] );
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-product-membership-objectives'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-membership-objectives'][0] );
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-product-membership-prerequisites'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-membership-prerequisites'][0] );
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-membership-child-id'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_serialized_arrays_without_keys( $meta['_it-exchange-membership-child-id'] ) );
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-membership-parent-id'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_serialized_arrays_without_keys( $meta['_it-exchange-membership-parent-id'] ) );
					} else {
						$line[] = '';
					}
					if ( !empty( $meta['_it-exchange-product-membership-welcome-message'] ) ) {
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it-exchange-product-membership-welcome-message'] );
					} else {
						$line[] = '';
					}
					
					/* Recurring Payments */					
					if ( !empty( $meta['_it-exchange-product-recurring-enabled'][0] ) && 'on' === $meta['_it-exchange-product-recurring-enabled'][0] ) {
						$tmp = '';
						if ( !empty( $meta['_it-exchange-product-recurring-trial-enabled'][0] ) && 'on' === $meta['_it-exchange-product-recurring-trial-enabled'][0] ) {
							$tmp = sprintf( __( 'Trial: %s %s; ', 'LION' ), $meta['_it-exchange-product-recurring-trial-interval-count'][0], $meta['_it-exchange-product-recurring-trial-interval'][0] );
						}
						$tmp .= sprintf( __( 'Recurring: %s %s; ', 'LION' ), $meta['_it-exchange-product-recurring-interval-count'][0], $meta['_it-exchange-product-recurring-interval'][0] );
						$tmp .= sprintf( __( 'Auto-Renew: %s; ', 'LION' ), $meta['_it-exchange-product-recurring-auto-renew'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp );
					} else if ( !empty( $meta['_it-exchange-product-recurring-time'][0] ) && $time = $meta['_it-exchange-product-recurring-time'][0] ) { 
						//Old version of Recurring Payments
						if ( 'forever' === $time ) {
							$line[] = '';
						} else {
							$line[] = it_exchange_one_click_export_escape_csv_value( sprintf( __( 'Recurring %s', 'LION' ), $time ) );
						}
					} else {
						$line[] = '';
					}
					
					/* Product Variants */
					$variants = it_exchange_get_variants_for_product( $product->ID );
					if ( !empty( $variants ) ) {
						$tmp = '';
						foreach( $variants as $variant ) {
							$tmp_values = array();
							$tmp .= '[' . $variant->post_title . ':';
							foreach( $variant->values as $value ) {
								$tmp_value = array();
								if ( !empty( $value->title ) ) {
									$tmp_value[] = $value->title;
								}
								if ( $variant->default == $value->ID ) {
									$tmp_value[] = 'default';
								}
								if ( !empty( $value->image ) ) {
									$tmp_value[] = $value->image;
								}
								if ( !empty( $value->color ) ) {
									$tmp_value[] = $value->color;
								}
								$tmp_values[] = implode( ',', $tmp_value );
							}
							$tmp .= implode( '|', $tmp_values );
							$tmp .= "]\n";
						}
						$line[] = $tmp;
					} else {
						$line[] = '';
					}
					/* Variant Inventory */
					if ( !empty( $meta['_it-exchange-product-inventory-variants'][0] ) ) {
						$tmp_inv = array();
						$inventories = maybe_unserialize( $meta['_it-exchange-product-inventory-variants'][0] );
						foreach( $inventories as $inv ) {
							$tmp_inv[] = '[' . $inv['combos_title'] . ':' . $inv['value'] . ']';
						}
						$tmp .= implode( "\n", $tmp_inv );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp );
					} else {
						$line[] = '';
					}
					
					/* Variant Pricing */
					if ( !empty( $meta['_it-exchange-product-pricing-variants'][0] ) ) {
						$tmp_price = array();
						$prices = maybe_unserialize( $meta['_it-exchange-product-pricing-variants'][0] );
						foreach( $prices as $price ) {
							$tmp_price[] = '[' . $price['combos_title'] . ':' . $price['value'] . ']';
						}
						$tmp .= implode( "\n", $tmp_price );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp );
					} else {
						$line[] = '';
					}
					
					/* Variant Images */
					if ( !empty( $meta['_it-exchange-product-variant-images'][0] ) ) {
						$tmp_img = array();
						$images = maybe_unserialize( $meta['_it-exchange-product-variant-images'][0] );
						foreach( $images as $image ) {
							$tmp_images = array();
							foreach( $image['value'] as $image_id ) {
								$tmp_images[] = wp_get_attachment_url( $image_id );
							}
							$tmp_img[] = '[' . $image['combos_title'] . ':' . implode( '|', $tmp_images ) . ']';
						}
						$tmp .= implode( "\n", $tmp_img );
						$line[] = it_exchange_one_click_export_escape_csv_value( $tmp );
					} else {
						$line[] = '';
					}
					
					fwrite( $f, implode( ',', $line ) . "\n"  );
					echo join( ',', $line ) . "<br />";
				}
				fclose( $f );
			}
			
            if ( empty( $products ) || $limit > count( $products ) ) {
	            
                echo '<p>' . __( 'All Done!', 'issuem-leaky-paywall' ) . '</p>';
                return;	
                            
	        } else {
			    
			    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;n=<?php echo ($n + 5) ?>"><?php _e( 'Next Subscribers', 'issuem-leaky-paywall' ); ?></a></p>
			    <script type='text/javascript'>
			    <!--
			    function nextpage() {
			        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&n=<?php echo $page + $limit; ?>";
			    }
			    setTimeout( "nextpage()", 250 );
			    //-->
			    </script><?php
			    
			}
		}		
		?>
	</div>
</div>
<?php
}

function it_exchange_one_click_export_escape_csv_value( $value ) {
	$value = str_replace('"', '""', $value); // First off escape all " and make them ""
	if( preg_match( '/,/', $value ) or preg_match( "/\n/", $value ) or preg_match( '/"/', $value ) ) { // Check if I have any commas or new lines
		return '"' . $value . '"'; // If I have new lines or commas escape them
	} else {
		return $value; // If no new lines or commas just return the value
	}
}

function it_exchange_one_click_export_handle_serialized_arrays_without_keys( $value ) {
	$value = maybe_unserialize( $value );
	return '[' . implode( '|', $value ) . ']';
}

function it_exchange_one_click_export_handle_serialized_arrays_with_keys( $value ) {
	$value = maybe_unserialize( $value );
	return '[' . implode( '|', array_map( function ( $v, $k ) { return $k . '=' . $v; }, $value, array_keys( $value ) ) ) . ']';
}

function it_exchange_one_click_export_membership_addon_build_content_rules( $rules ) {
	$return = array();
	
    if ( !empty( $rules ) ) {
		$rules = maybe_unserialize( $rules );
		foreach( $rules as $rule ) {
			$options = '';
			$current_grouped_id = isset( $rule['grouped_id'] ) ? $rule['grouped_id'] : false;
									
			$selection    = !empty( $rule['selection'] )    ? $rule['selection'] : false; //Content Types (e.g. post_types or taxonomies)
			$selected     = !empty( $rule['selected'] )     ? $rule['selected'] : false;  //Content Type (e.g. posts post_type, or category taxonomy)
			$value        = !empty( $rule['term'] )         ? $rule['term'] : false;      //Content (e.g. specific post or category)
			$group        = isset( $rule['group'] )         ? $rule['group'] : NULL;
			$group_layout = !empty( $rule['group_layout'] ) ? $rule['group_layout'] : 'grid';
			$group_id     = isset( $rule['group_id'] )      ? $rule['group_id'] : NULL;
			$grouped_id   = isset( $rule['grouped_id'] )    ? $rule['grouped_id'] : NULL;
			
			$return[] = '[selection=' . $selection . ','
			          . 'selected=' . $selected . ','
			          . 'value=' . $value . ','
			          . 'group=' . $group . ','
			          . 'group_layout=' . $group_layout . ','
			          . 'group_id=' . $group_id . ','
			          . 'grouped_id=' . $grouped_id . ']';
		}
	}
	
	return implode( '|', $return );
}
