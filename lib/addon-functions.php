<?php
	
function it_exchange_one_click_export_addon_page() {
	$date_format = get_option( 'date_format' );
    $uploads_dirs = wp_upload_dir();
    $baseurl = $uploads_dirs['baseurl'];
    $basedir = $uploads_dirs['basedir'];
    $destination = $basedir . '/exchange_export';
	$path = $baseurl . '/exchange_export';
	if ( is_writable( $basedir ) ) {
		if ( !file_exists( $destination ) ) {
			if ( false === mkdir( $destination ) ) {
				echo "NOT MAKEABLE DUFFUS!";
			}
		} else if ( !is_writeable( $destination ) ) {
			echo "NOT WRITEABLE DUFFUS!";
		}
	} else {
		echo "NOT WRITEABLE FOOL!";
	}
?>
<div class="wrap help-wrap">
	<?php ITUtility::screen_icon( 'it-exchange' );  ?>
	<h2><?php _e( 'One Click Export', 'LION' ); ?></h2>

	<p class="top-description"><?php printf( __( 'We should say something special here... maybe', 'LION' ) ); ?></p>

	<div class="one-click-export-section-wrap clearfix">
		<form action="" method="POST">
			<input type="hidden" value="0" name="n" />
			<input type="hidden" value="1" name="product-export" />
			<?php submit_button( __( 'Generate Export Files', 'LION' ), 'button', 'one-click-export' ); ?>
		</form>
		<?php
		if ( !empty( $_REQUEST['one-click-export'] ) ) {
			$membership_plugin_enabled         = is_plugin_active( 'exchange-addon-membership' );
			$recurring_payments_plugin_enabled = is_plugin_active( 'exchange-addon-recurring-payments' );
			$variants_plugin_enabled           = is_plugin_active( 'exchange-addon-variants' );
			$customer_pricing_plugin_enabled   = is_plugin_active( 'exchange-addon-customer-pricing' );
			$can_tax_plugin_enabled            = is_plugin_active( 'exchange-addon-easy-canadian-sales-taxes' );
			$eu_tax_plugin_enabled             = is_plugin_active( 'exchange-addon-easy-eu-value-added-taxes' );
			$us_tax_plugin_enabled             = is_plugin_active( 'exchange-addon-easy-us-sales-taxes' );
			$limit = 20;
			
			echo '<p>';
			_e( 'Exporting products...', 'LION' );
			
			if ( !empty( $_REQUEST['product-export'] ) ) {
				
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
						$f = fopen( $destination . '/ithemes-exchanges-products.csv', 'w' );
					} else {
						$f = fopen( $destination . '/ithemes-exchanges-products.csv', 'a' );
					}
					$headings = array(
						//Core
						__( 'Title', 'LION' ),
						__( 'ID', 'LION' ),
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
					);
					
					if ( $membership_plugin_enabled ) {
						$headings[] = __( 'Membership Content Access', 'LION' );
						$headings[] = __( 'Membership Intended Audience', 'LION' );
						$headings[] = __( 'Membership Objectives', 'LION' );
						$headings[] = __( 'Membership Prerequisites', 'LION' );
						$headings[] = __( 'Membership Parent IDs', 'LION' );
						$headings[] = __( 'Membership Child IDs', 'LION' );
						$headings[] = __( 'Membership Welcome Message', 'LION' );
					}
					
					if ( $recurring_payments_plugin_enabled ) {
						$headings[] = __( 'Recurring Payment Options', 'LION' );
					}
					
					if ( $variants_plugin_enabled ) {
						$headings[] = __( 'Variants', 'LION' );
						$headings[] = __( 'Variant Inventory', 'LION' );
						$headings[] = __( 'Variant Pricing', 'LION' );
						$headings[] = __( 'Variant Images', 'LION' );
					}
					
					if ( $customer_pricing_plugin_enabled ) {
						$headings[] = __( 'Customer Pricing Options', 'LION' );
						$headings[] = __( 'Customer Name Your Own Prices Options', 'LION' );
					}
					
					if ( $can_tax_plugin_enabled ) {
						$headings[] = __( 'Canadian Tax Exempt?', 'LION' );
					}

					if ( $eu_tax_plugin_enabled ) {
						$headings[] = __( 'EU VAT Settings', 'LION' );
						$headings[] = __( 'EU VATMOSS Settings', 'LION' );
					}
					
					if ( $us_tax_plugin_enabled ) {
						$headings[] = __( 'U.S. TaxCloud Tax Code', 'LION' );
					}
											
					fwrite( $f, implode( ',', $headings ) . "\n" );
					foreach( $products as $product ) {
						$meta = get_post_meta( $product->ID );
	
						$line = array();
						$line[] = it_exchange_one_click_export_escape_csv_value( $product->post_title ); //Product Title
						$line[] = it_exchange_one_click_export_escape_csv_value( $product->ID ); //Product ID
						
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
						
						if ( $membership_plugin_enabled ) {
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
						}
						
						if ( $recurring_payments_plugin_enabled ) {
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
						}
						
						if ( $variants_plugin_enabled ) {
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
						}
						
						if ( $customer_pricing_plugin_enabled ) {
							if ( !empty( $meta['_it-exchange-customer-pricing-enabled'][0] ) && 'no' != $meta['_it-exchange-customer-pricing-enabled'][0] ) {
								$options = maybe_unserialize( $meta['_it-exchange-customer-pricing-options'][0] );
								$tmp_options = array();
								foreach( $options as $value ) {
									$tmp_options[] = '[Price:' . $value['price'] . ';Label:' . $value['label'] . ';Default:' . $value['default'] . ']';
								}
								$tmp = implode( "\n", $tmp_options );
								$line[] = it_exchange_one_click_export_escape_csv_value( $tmp );
		
								if ( !empty( $meta['_it-exchange-customer-pricing-nyop-enabled'][0] ) && 'no' != $meta['_it-exchange-customer-pricing-nyop-enabled'][0] ) {
									$line[] = it_exchange_one_click_export_escape_csv_value( sprintf( 'Min: %s; Max: %s', $meta['_it-exchange-customer-pricing-nyop-min'][0], $meta['_it-exchange-customer-pricing-nyop-max'][0] ) );
								}
							}
						}
						
						if ( $can_tax_plugin_enabled ) {
							if ( !empty( $meta['_it-exchange-add-on-easy-us-sales-taxes-canadian-tax-exempt-status'][0] ) ) {
								$line[] = 'Exempt';
							} else {
								$line[] = '';
							}
						}
	
						if ( $eu_tax_plugin_enabled ) {
							if ( empty( $meta['_it-exchange-easy-eu-value-added-taxes-exempt'][0] ) ) {
								$settings = it_exchange_get_option( 'addon_easy_eu_value_added_taxes' );
								$tax_type = !isset( $meta['_it-exchange-easy-eu-value-added-taxes-type'][0] ) ? 'default' : $meta['_it-exchange-easy-eu-value-added-taxes-type'][0];
								if ( $tax_type !== 0 && 'default' === $tax_type ) {
									$line[] = 'Default';
								} else {
									$line[] = sprintf( __( '%s (%s%%)', 'LION' ), $settings['tax-rates'][$tax_type]['label'], $settings['tax-rates'][$tax_type]['rate'] );
								}
							} else {
								$line[] = 'Exempt';
							}
							if ( !empty( $meta['_it-exchange-easy-eu-value-added-taxes-vat-moss'][0] ) && 'on' === $meta['_it-exchange-easy-eu-value-added-taxes-vat-moss'][0] ) {
								$settings = it_exchange_get_option( 'addon_easy_eu_value_added_taxes' );
								$vat_moss_tax_types = empty( $meta['_it-exchange-easy-eu-value-added-taxes-vat-moss-tax-types'][0] ) ? array() : maybe_unserialize( $meta['_it-exchange-easy-eu-value-added-taxes-vat-moss-tax-types'][0] );
		
								foreach( $settings['vat-moss-tax-rates'] as $memberstate_abbrev => $tax_rates ) {
									foreach( $tax_rates as $tax_rate ) {
										if ( 'checked' === $tax_rate['default'] ) {
											$default_tax_rate = $tax_rate;
											break;
										}
									}
									if ( empty( $vat_moss_tax_types[$memberstate_abbrev] ) ) {
										$vat_moss_tax_types[$memberstate_abbrev] = 'default';
									}
									if ( 'default' === $vat_moss_tax_types[$memberstate_abbrev] ) {
										$line[] = sprintf( __( 'Default (%s - %s%%)', 'LION' ), $default_tax_rate['label'], $default_tax_rate['rate'] );;
									} else {
										$line[] = sprintf( __( '%s (%s%%)', 'LION' ), $tax_rates[$vat_moss_tax_types[$memberstate_abbrev]]['label'], $tax_rates[$vat_moss_tax_types[$memberstate_abbrev]]['rate'] );
									}
								}
							} else {
								$line[] = 'Disabled';
							}
						}
						
						if ( $us_tax_plugin_enabled ) {
							if ( !empty( $meta['_it-exchange-add-on-easy-us-sales-taxes-us-tic'][0] ) ) {
								$line[] = $meta['_it-exchange-add-on-easy-us-sales-taxes-us-tic'][0];
							} else {
								$settings = it_exchange_get_option( 'addon_easy_us_sales_taxes' );
								$line[] = $settings['us-tic'];
							}
						}
						
						fwrite( $f, implode( ',', $line ) . "\n"  );
					}
					fclose( $f );
				}
				
	            if ( empty( $products ) || $limit > count( $products ) ) {
			            
					_e( 'all done!', 'LION' );
					
				    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;transaction-export&amp;n=0"><?php _e( 'Start Transactions', 'LION' ); ?></a></p>
				    <script type='text/javascript'>
				    <!--
				    function nextpage() {
				        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&transaction-export=1&n=0";
				    }
				    setTimeout( "nextpage()", 250 );
				    //-->
				    </script><?php
					return;
	                            
		        } else {
				    
				    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;product-export&amp;n=<?php echo ($n + 5) ?>"><?php _e( 'Next Products', 'LION' ); ?></a></p>
				    <script type='text/javascript'>
				    <!--
				    function nextpage() {
				        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&product-export=1&n=<?php echo $page + $limit; ?>";
				    }
				    setTimeout( "nextpage()", 250 );
				    //-->
				    </script><?php
				    
				}
			} else {
				_e( 'all done!', 'LION' );
			}
			echo '</p>';
			
			echo '<p>';
			_e( 'Exporting transactions...', 'LION' );
			if ( !empty( $_REQUEST['transaction-export'] ) ) {
				
				$page = !empty( $_REQUEST['n'] ) ? $_REQUEST['n'] : 0;
				$args = array(
					'posts_per_page' => $limit,
					'offset'         => $page,
					'post_type'      => 'it_exchange_tran',
					'post_status'    => 'all',
				);
				$transactions = get_posts( $args );
				
				if ( !empty( $transactions ) ) {
					if ( 0 === $page ) {
						$f = fopen( $destination . '/ithemes-exchanges-transactions.csv', 'w' );
					} else {
						$f = fopen( $destination . '/ithemes-exchanges-transactions.csv', 'a' );
					}
					$headings = array(
						//Core
						__( 'Title', 'LION' ),
						__( 'ID', 'LION' ),
						__( 'Method', 'LION' ),
						__( 'Method ID', 'LION' ),
						__( 'Status', 'LION' ),
						__( 'Customer ID', 'LION' ),
						__( 'Customer IP Address', 'LION' ),
						__( 'Parent Transaction ID', 'LION' ),
						__( 'Cart Object', 'LION' ),
					);
					
					/*
					if ( $membership_plugin_enabled ) {
						$headings[] = __( 'Membership Content Access', 'LION' );
						$headings[] = __( 'Membership Intended Audience', 'LION' );
						$headings[] = __( 'Membership Objectives', 'LION' );
						$headings[] = __( 'Membership Prerequisites', 'LION' );
						$headings[] = __( 'Membership Parent IDs', 'LION' );
						$headings[] = __( 'Membership Child IDs', 'LION' );
						$headings[] = __( 'Membership Welcome Message', 'LION' );
					}
					
					if ( $recurring_payments_plugin_enabled ) {
						$headings[] = __( 'Recurring Payment Options', 'LION' );
					}
					
					if ( $variants_plugin_enabled ) {
						$headings[] = __( 'Variants', 'LION' );
						$headings[] = __( 'Variant Inventory', 'LION' );
						$headings[] = __( 'Variant Pricing', 'LION' );
						$headings[] = __( 'Variant Images', 'LION' );
					}
					
					if ( $customer_pricing_plugin_enabled ) {
						$headings[] = __( 'Customer Pricing Options', 'LION' );
						$headings[] = __( 'Customer Name Your Own Prices Options', 'LION' );
					}
					
					if ( $can_tax_plugin_enabled ) {
						$headings[] = __( 'Canadian Tax Exempt?', 'LION' );
					}

					if ( $eu_tax_plugin_enabled ) {
						$headings[] = __( 'EU VAT Settings', 'LION' );
						$headings[] = __( 'EU VATMOSS Settings', 'LION' );
					}
					
					if ( $us_tax_plugin_enabled ) {
						$headings[] = __( 'U.S. TaxCloud Tax Code', 'LION' );
					}
					/**/
					fwrite( $f, implode( ',', $headings ) . "\n" );
					foreach( $transactions as $transaction ) {
						$meta = get_post_meta( $transaction->ID );

						$line = array();
						$line[] = it_exchange_one_click_export_escape_csv_value( $transaction->post_title ); //Transaction Title
						$line[] = it_exchange_one_click_export_escape_csv_value( $transaction->ID ); //Transaction ID
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_transaction_method'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_transaction_method_id'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_transaction_status'][0] );
						$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_customer_id'][0] );
						if ( !empty( $meta['_it_exchange_customer_ip'][0] ) ) {
							$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_customer_ip'][0] );
						} else {
							$line[] = '';
						}
						if ( !empty( $meta['_it_exchange_parent_tx_id'][0] ) ) {
							$line[] = it_exchange_one_click_export_escape_csv_value( $meta['_it_exchange_parent_tx_id'][0] );
						} else {
							$line[] = '';
						}
						$line[] = it_exchange_one_click_export_escape_csv_value( it_exchange_one_click_export_handle_cart_objects( $meta['_it_exchange_cart_object'][0] ) );

						fwrite( $f, implode( ',', $line ) . "\n"  );
					}
					fclose( $f );
				}

				if ( empty( $transactions ) || $limit > count( $transactions ) ) {
			            
					_e( 'all done!', 'LION' );
					
					if ( $membership_plugin_enabled ) {
					    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;membership-export&amp;n=0"><?php _e( 'Start Members', 'LION' ); ?></a></p>
					    <script type='text/javascript'>
					    <!--
					    function nextpage() {
					        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&membership-export=1&n=0";
					    }
					    setTimeout( "nextpage()", 250 );
					    //-->
					    </script><?php
						return;
					}
	                            
		        } else {
				    
				    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;transaction-export&amp;n=<?php echo ($n + 5) ?>"><?php _e( 'Next Transactions', 'LION' ); ?></a></p>
				    <script type='text/javascript'>
				    <!--
				    function nextpage() {
				        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&transaction-export=1&n=<?php echo $page + $limit; ?>";
				    }
				    setTimeout( "nextpage()", 250 );
				    //-->
				    </script><?php
				    
				}
			} else {
				_e( 'all done!', 'LION' );
			}
			echo '</p>';
			
			if ( $membership_plugin_enabled ) {
				echo '<p>';
				_e( 'Exporting membership data...', 'LION' );
				if ( !empty( $_REQUEST['membership-export'] ) ) {
					
					$membership_data = false;
					
					if ( empty( $membership_data ) || $limit > count( $membership_data ) ) {
				            
						_e( 'all done!', 'LION' );
						
			        } else {
					    
					    ?><p><?php _e( 'If your browser doesn&#8217;t start loading the next page automatically, click this link:' ); ?> <a class="button" href="admin.php?page=leaky-paywall-update&amp;transaction-export&amp;n=<?php echo ($n + 5) ?>"><?php _e( 'Next Members', 'LION' ); ?></a></p>
					    <script type='text/javascript'>
					    <!--
					    function nextpage() {
					        location.href = "admin.php?page=it-exchange-one-click-export&one-click-export=1&membership-export=1&n=<?php echo $page + $limit; ?>";
					    }
					    setTimeout( "nextpage()", 250 );
					    //-->
					    </script><?php
					    
					}
				} else {
					_e( 'all done!', 'LION' );
				}
				echo '</p>';			
			}
			
		}		
		?>
	</div>
	
	<hr />
	
	<div class="one-click-export-section-wrap clearfix">
		<h3><?Php _e( 'Current Export Files', 'LION' ); ?></h3>
		<ul>
		<?php
		$gmt_offset = get_option( 'gmt_offset' );
		foreach( scandir( $destination ) as $file ) {
			if ( '.' === $file || '..' === $file ) {
				continue;
			}
			$mtime = filemtime( $destination . '/' . $file ) + ( $gmt_offset * 3600 );
			$mtime = date_i18n( $date_format . ' h:i:s A', $mtime );
			echo '<li><a href="' . $path . '/' . $file . '">' . $file . '</a> (' . $mtime . ')</li>';
		}
		?>
		</ul>
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
	return '[' . implode( '|', array_map( function ( $v, $k ) { return $k . '=' . $v; }, (array)$value, array_keys( (array)$value ) ) ) . ']';
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

function it_exchange_one_click_export_handle_cart_objects( $cart ) {
	$return = array();
	
    if ( !empty( $cart ) ) {
		$cart = maybe_unserialize( $cart );
		$return[] = 'Total:' . $cart->total;
		if ( !empty( $cart->currency ) ) {
			$return[] = 'Currency:' . $cart->currency;
		}
		if ( !empty( $cart->description ) ) {
			$return[] = 'Description:' . $cart->description;
		}
		if ( !empty( $cart->currency ) ) {
			$return[] = 'Currency:' . $cart->currency;
		}
		if ( !empty( $cart->products ) ) {
			$tmp_product = array();
			foreach ( $cart->products as $product ) {
				$tmp_product[] = '(Base Price: ' . $product['product_base_price'] . '; Subtotal: ' . $product['product_subtotal'] . '; Name: ' . $product['product_name'] . '; ID: ' . $product['product_id'] . '; Count: ' . $product['count'] . ';)';
			}
			$return[] = 'Products:' . join( "\n", $tmp_product );
		}
	}
	
	return implode( '|', $return );
}
