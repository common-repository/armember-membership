<?php
global $wp, $arm_access_rules, $arm_global_settings, $arm_crons, $wpdb, $wp_roles, $ARMemberLite, $arm_slugs, $arm_members_class, $arm_global_settings, $arm_email_settings, $arm_member_forms, $arm_subscription_plans, $arm_payment_gateways, $arm_social_feature, $arm_transaction, $arm_lite_members_activity;

$allRoles     = $arm_global_settings->arm_get_all_roles();
$dbFormFields = $arm_member_forms->arm_get_db_form_fields( true );
$user_id      = 0;
if ( isset( $_REQUEST['id'] ) && $_REQUEST['id'] != '' ) {
	$user_id = intval(abs( $_REQUEST['id'] )); //phpcs:ignore
}
$view_type = 'page';
if ( ! empty( $_REQUEST['view_type'] ) && 'popup' == $_REQUEST['view_type'] ) {
	$view_type = 'popup';
}


$user = get_user_by( 'id', $user_id );
if ( empty( $user ) ) {
	wp_redirect( admin_url( 'admin.php?page=' . $arm_slugs->manage_members ) );
}

$user_metas  = get_user_meta( $user_id );
$edit_link   = admin_url( 'admin.php?page=' . $arm_slugs->manage_members . '&action=edit_member&id=' . $user->ID );
$userRegForm = array();
$armform     = new ARM_Form_Lite();
$user_arm_form_id = !empty($user->arm_form_id) ? $user->arm_form_id : 0;
if(empty($user_arm_form_id))
{
	$user_arm_form_entry_id = !empty($user->arm_entry_id) ? $user->arm_entry_id : 0;
	$user_arm_form_entry_id = (empty($user_arm_form_entry_id) && !empty($user->arm_wooc_gateway_entry_id)) ? $user->arm_wooc_gateway_entry_id : 0;
	if(!empty($user_arm_form_entry_id))
	{
		$user_arm_form_id = $wpdb->get_var( $wpdb->prepare("SELECT arm_form_id FROM ".$ARMemberLite->tbl_arm_entries." WHERE arm_entry_id = %d ",$user_arm_form_entry_id ) ); //phpcs:ignore --Reason $usermeta_table is a table name
	}
}
if ( !empty($user_arm_form_id) ) {
	$userRegForm = $arm_member_forms->arm_get_single_member_forms($user_arm_form_id);
    $arm_exists_form = $armform->arm_is_form_exists($user_arm_form_id);
    if( $arm_exists_form ){
        $armform->init((object) $userRegForm);
    }
}
$date_format         = $arm_global_settings->arm_get_wp_date_format();
$global_currency     = $arm_payment_gateways->arm_get_global_currency();
$all_currencies      = $arm_payment_gateways->arm_get_all_currencies();
$global_currency_sym = $all_currencies[ strtoupper( $global_currency ) ];
$backToListingIcon   = MEMBERSHIPLITE_IMAGES_URL . '/back_to_listing_arrow.png';
if ( is_rtl() ) {
	$backToListingIcon = MEMBERSHIPLITE_IMAGES_URL . '/back_to_listing_arrow_right.png';
}
?>
<div class="wrap arm_page arm_view_member_main_wrapper arm_view_member_popup">
	<div class="content_wrapper" id="content_wrapper">
		<div class="arm_view_member_wrapper arm_member_detail_box">
			<div class="arm_belt_box arm_view_memeber_top_belt">
				<div class="arm_belt_block">
					<div class="page_title"><?php echo $user->first_name . ' ' . $user->last_name; //phpcs:ignore ?> (<?php echo $user->user_login; //phpcs:ignore ?>)</div>
				</div>
				<?php
				if ( $view_type != 'popup' ) {
					?>
				<div class="arm_belt_block" align="<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
					<a href="<?php echo esc_url( admin_url( 'admin.php?page=' . $arm_slugs->manage_members ) ); //phpcs:ignore ?>" class="armemailaddbtn"><img src="<?php echo esc_attr($backToListingIcon); //phpcs:ignore ?>" style="<?php echo ( is_rtl() ) ? 'margin-left: 5px;' : 'margin-right: 5px;'; ?>"/><?php esc_html_e( 'Back to listing', 'armember-membership' ); ?></a>
				</div>
					<?php
				}
				?>
				<div class="armclear"></div>
			</div>
			<div class="armclear"></div>
			<form class="arm_member_detail_wrapper_frm arm_admin_form">
				<div class="armclear"></div>
				<div class="page_sub_content arm_member_details_container">
					<div class="arm_view_member_left_box">
						<table class="form-table">
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Username', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content"><?php echo esc_html($user->user_login); ?></td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Email', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content"><a class="" href="mailto:<?php echo esc_attr($user->user_email); ?>"><?php echo esc_html($user->user_email); ?></a></td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'First Name', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content"><?php echo esc_html($user->first_name); ?></td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Last Name', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content"><?php echo esc_html($user->last_name); ?></td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Role', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content">
								<?php
								$u_roles = '-';
								if ( ! empty( $user->roles ) ) {
									$u_roles = '';
									foreach ( $user->roles as $urole ) {
										if ( isset( $allRoles[ $urole ] ) ) {
											$u_roles .= $allRoles[ $urole ] . ', ';
										}
									}
									$u_roles = trim( $u_roles, ', ' );
								} else {
									$u_roles = get_option( 'default_role' );
								}
								echo $u_roles; //phpcs:ignore
								?>
								</td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Member Status', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content">
								<?php
								echo $arm_members_class->armGetMemberStatusText( $user_id ); //phpcs:ignore
								?>
								</td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Member Since', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content">
								<?php
									echo date_i18n( $date_format, strtotime( $user->user_registered ) ); //phpcs:ignore
								?>
								</td>
							</tr>
							<tr class="form-field">
								<th class="arm-form-table-label"><?php esc_html_e( 'Registered/Edited Profile From', 'armember-membership' ); ?>:</th>
								<td class="arm-form-table-content">
								<?php
								if ( ! empty( $user->arm_form_id ) && $user->arm_form_id != 0 ) {
									if ( ! empty( $userRegForm ) ) {
										echo strip_tags( stripslashes( $userRegForm['arm_form_label'] ) ) . "<em> (Form ID: <b>$user->arm_form_id</b>)</em>"; //phpcs:ignore
									} else {
										echo '--';
									}
								} else {
									$arm_is_user_import = get_user_meta( $user->ID, 'arm_user_import' );
									if ( $arm_is_user_import ) {
										esc_html_e( 'ARMember Admin (Import)', 'armember-membership' );
									} else {
										$usermeta_table  = $wpdb->usermeta;
										$result_arm_meta = $wpdb->get_results( $wpdb->prepare('SELECT count(*) as arm_meta FROM ' . $usermeta_table . " WHERE user_id = %d and meta_key like '%arm_%' ",$user->ID), ARRAY_A );//phpcs:ignore --Reason $usermeta_table is a table name, False Positivity alarm
										if ( isset( $result_arm_meta[0]['arm_meta'] ) && $result_arm_meta[0]['arm_meta'] > 0 ) {
											esc_html_e( 'ARMember Admin', 'armember-membership' );
										} else {
											esc_html_e( 'WordPress default', 'armember-membership' );
										}
									}
								}
								?>
								</td>
							</tr>
							<?php
							$exclude_keys = array(
								'first_name',
								'last_name',
								'user_login',
								'user_email',
								'user_pass',
								'repeat_pass',
								'arm_user_plan',
								'arm_last_login_ip',
								'arm_last_login_date',
								'roles',
								'section',
								'repeat_pass',
								'repeat_email',
								'social_fields',
								'avatar',
								'profile_cover',
							);
							if ( ! empty( $dbFormFields ) ) {
								foreach ( $dbFormFields as $meta_key => $field ) {
									$field_options = maybe_unserialize( $field );
									$field_options = apply_filters( 'arm_change_field_options', $field_options );
									$meta_key      = isset( $field_options['meta_key'] ) ? $field_options['meta_key'] : $field_options['id'];
									$field_id      = $meta_key . arm_generate_random_code();
									if ( ! in_array( $meta_key, $exclude_keys ) && ! in_array( $field_options['type'], array( 'section', 'roles', 'html', 'hidden', 'submit', 'repeat_pass', 'repeat_email' ) ) ) {
										?>
										<tr class="form-field">
											<th class="arm-form-table-label"><?php echo esc_html($field_options['label']); ?>:</th>
											<td class="arm-form-table-content">
											<?php
											if ( ! empty( $user->$meta_key ) ) {
												if ( $field_options['type'] == 'file' ) {
													$file_name = basename( $user->$meta_key );
													if ( $user->$meta_key != '' ) {
														$exp_val        = explode( '/', $user->$meta_key );
														$filename       = $exp_val[ count( $exp_val ) - 1 ];
														$file_extension = explode( '.', $filename );
														$file_ext       = $file_extension[ count( $file_extension ) - 1 ];
														$thumbUrl       = '';
														if ( in_array( $file_ext, array( 'jpg', 'jpeg', 'jpe', 'gif', 'png', 'bmp', 'tif', 'tiff' ) ) ) {
															$thumbUrl = $user->$meta_key;
														} elseif ( in_array( $file_ext, array( 'pdf', 'exe' ) ) ) {
															$thumbUrl = MEMBERSHIPLITE_IMAGES_URL . '/document.png';
														} elseif ( in_array( $file_ext, array( 'zip' ) ) ) {
															$thumbUrl = MEMBERSHIPLITE_IMAGES_URL . '/archive.png';
														} else {
															$thumbUrl = MEMBERSHIPLITE_IMAGES_URL . '/text.png';
														}
														?>
														<a href="<?php echo esc_url($user->$meta_key); //phpcs:ignore ?>" target="__blank"> <img src="<?php echo esc_attr($thumbUrl); //phpcs:ignore ?>" class="arm_max_width_100" style="height: auto;"></a>
																			<?php
													}
												} elseif ( in_array( $field_options['type'], array( 'radio', 'checkbox', 'select' ) ) ) {
													$user_meta_detail = $user->$meta_key;
													$main_array       = array();
													$options          = $field_options['options'];
													$value_array      = array();
													foreach ( $options as $arm_key => $arm_val ) {
														if ( strpos( $arm_val, ':' ) != false ) {
															$exp_val                    = explode( ':', $arm_val );
															$exp_val1                   = $exp_val[1];
															$value_array[ $exp_val[0] ] = $exp_val[1];
														} else {
															$value_array[ $arm_val ] = $arm_val;
														}
													}
													$user_meta_detail = $ARMemberLite->arm_array_trim( $user_meta_detail );
													if ( ! empty( $value_array ) ) {
														if ( is_array( $user_meta_detail ) ) {
															foreach ( $user_meta_detail as $u ) {
																foreach ( $value_array as $arm_key => $arm_val ) {
																	if ( $u == $arm_val ) {
																		array_push( $main_array, $arm_key );
																	}
																}
															}
															$user_meta_detail = @implode( ', ', $main_array );
															echo $user_meta_detail; //phpcs:ignore
														} else {
															$exp_val = array();
															if ( strpos( $user_meta_detail, ',' ) != false ) {
																$exp_val = explode( ',', $user_meta_detail );
															}
															if ( ! empty( $exp_val ) ) {
																foreach ( $exp_val as $u ) {
																	if ( in_array( $u, $value_array ) ) {
																		array_push( $main_array, array_search( $u, $value_array ) );
																	}
																}
																$user_meta_detail = @implode( ', ', $main_array );
																echo $user_meta_detail; //phpcs:ignore
															} else {
																if ( in_array( $user_meta_detail, $value_array ) ) {
																	echo array_search( $user_meta_detail, $value_array ); //phpcs:ignore
																} else {
																	echo $user_meta_detail; //phpcs:ignore
																}
															}
														}
													} else {
														if ( is_array( $user_meta_detail ) ) {
															$user_meta_detail = $ARMemberLite->arm_array_trim( $user_meta_detail );
															$user_meta_detail = @implode( ', ', $user_meta_detail );
															echo $user_meta_detail; //phpcs:ignore
														} else {
															echo $user_meta_detail; //phpcs:ignore
														}
													}
												} else {
													$user_meta_detail = $user->$meta_key;


													$pattern = '/^(date\_(.*))/';

													if ( preg_match( $pattern, $meta_key ) ) {
															$user_meta_detail = date_i18n( $date_format, strtotime( $user_meta_detail ) );
													}



													if ( is_array( $user_meta_detail ) ) {
														$user_meta_detail = $ARMemberLite->arm_array_trim( $user_meta_detail );
														$user_meta_detail = @implode( ', ', $user_meta_detail );
														echo $user_meta_detail; //phpcs:ignore
													} else {
														echo $user_meta_detail; //phpcs:ignore
													}
												}
											} else {
												echo '--';
											}
											?>
											</td>
										</tr>
										<?php
									}
								}
							}
							$form_settings = ( isset( $armform->settings ) ) ? maybe_unserialize( $armform->settings ) : array();
							if ( $armform->exists() && isset( $form_settings['is_hidden_fields'] ) && $form_settings['is_hidden_fields'] == '1' ) {
								if ( isset( $form_settings['hidden_fields'] ) && ! empty( $form_settings['hidden_fields'] ) ) {
									foreach ( $form_settings['hidden_fields'] as $hiddenF ) {
										$hiddenMetaKey = ( isset( $hiddenF['meta_key'] ) && ! empty( $hiddenF['meta_key'] ) ) ? $hiddenF['meta_key'] : sanitize_title( 'arm_hidden_' . $hiddenF['title'] );
										$hiddenValue   = get_user_meta( $user_id, $hiddenMetaKey, true );
										?>
										<tr class="form-field">
											<th class="arm-form-table-label"><?php echo esc_html($hiddenF['title']); ?>:</th>
											<td class="arm-form-table-content"><?php echo esc_html($hiddenValue); ?></td>
										</tr>
										<?php
									}
								}
							}
							if ( $arm_social_feature->isSocialFeature ) {
								$socialProfileFields = $arm_member_forms->arm_social_profile_field_types();
								if ( ! empty( $socialProfileFields ) ) {
									foreach ( $socialProfileFields as $spfKey => $spfLabel ) {
										$spfMetaKey   = 'arm_social_field_' . $spfKey;
										$spfMetaValue = get_user_meta( $user_id, $spfMetaKey, true );
										?>
										<tr class="form-field">
											<th class="arm-form-table-label"><?php echo esc_html($spfLabel); ?>:</th>
											<td class="arm-form-table-content">
											<?php
											echo ( ! empty( $spfMetaValue ) ) ? $spfMetaValue : '--'; //phpcs:ignore
											?>
											</td>
										</tr>
										<?php
									}
								}
							}
							?>
						</table>
					</div>
					<div class="arm_view_member_right_box">
						<div class="arm_member_detail_avtar_section">
							<div class="arm_member_detail_avtar">
								<?php echo $user_avatar = get_avatar( $user_id, 150 ); //phpcs:ignore ?>
							</div>
							<a href="<?php echo esc_url($edit_link); //phpcs:ignore ?>" class="arm_open_edit_profile_popup_admin armemailaddbtn arm_edit_member_link"><?php esc_html_e( 'Edit Profile', 'armember-membership' ); ?></a>
						</div>
						<?php

						$last_login_date = get_user_meta( $user_id, 'arm_last_login_date', true );
						if ( ! empty( $last_login_date ) ) {
							$last_login_ip = get_user_meta( $user_id, 'arm_last_login_ip', true );
							?>
							<div class="arm_member_detail_login_section">
								<div class="arm_member_detail_login_date">
									<span>
									<?php
									echo esc_html__( 'Last loggedin on', 'armember-membership' ) . ' ' . date_i18n( $date_format, strtotime( $last_login_date ) ) . ''; //phpcs:ignore
									if ( ! empty( $last_login_ip ) ) {
										echo ' ' . esc_html__( 'from IP', 'armember-membership' ) . ' ' . $last_login_ip; //phpcs:ignore
									}
									?>
									</span>
								</div>
							</div>
						<?php } ?>
					</div>
					<div class="armclear"></div>
					<?php



										$plan_id_name_array = $arm_subscription_plans->arm_get_plan_name_by_id_from_array();


					$membership_history = $arm_subscription_plans->arm_get_user_membership_history( $user_id, 1, 5, $plan_id_name_array );
					?>
					<?php if ( ! empty( $membership_history ) ) : ?>
						<div class="arm_view_member_sub_title"><?php esc_html_e( 'Membership History', 'armember-membership' ); ?></div>
						<div class="arm_view_member_sub_content arm_membership_history_list armPageContainer">
							<?php echo $membership_history; //phpcs:ignore ?> 
						</div>
						<div class="armclear"></div>
					<?php endif; ?>
												
										<?php
										$user_logs = $arm_transaction->arm_get_user_transactions_with_pagging( $user_id, 1, 5, $plan_id_name_array );
										?>
					<?php if ( ! empty( $user_logs ) ) : ?>
						<div class="arm_view_member_sub_title"><?php esc_html_e( 'Payment History', 'armember-membership' ); ?></div>
												<div class="arm_view_member_sub_content arm_membership_history_list armPageContainer">
							<?php echo $user_logs; //phpcs:ignore ?>
						</div>
						<div class="armclear"></div>
					<?php endif; ?>
					<div class="armclear"></div>
					<?php $wpnonce = wp_create_nonce( 'arm_wp_nonce' );?>
					<input type="hidden" name="arm_wp_nonce" value="<?php echo esc_attr($wpnonce);?>"/>
					
				</div>
			</form>
		</div>
		<div class="armclear"></div>
		<div class="arm_members_activities_detail_container"></div>
	</div>

</div>
