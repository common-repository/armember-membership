<?php
global $wpdb, $ARMemberLite, $arm_slugs, $arm_global_settings, $arm_members_directory, $arm_subscription_plans;
$member_templates  = $arm_members_directory->arm_get_all_member_templates();
$defaultTemplates  = $arm_members_directory->arm_default_member_templates();
$tempColorSchemes  = $arm_members_directory->getTemplateColorSchemes();
$tempColorSchemes1 = $arm_members_directory->getTemplateColorSchemes1();
$subs_data         = $arm_subscription_plans->arm_get_all_subscription_plans( 'arm_subscription_plan_id, arm_subscription_plan_name' );

$fonts_option = array('title_font'=>array('font_family'=>'Poppins','font_size'=>'16','font_bold'=>'1','font_italic'=>'0','font_decoration'=>'',),'subtitle_font'=>array('font_family'=>'Poppins','font_size'=>'13','font_bold'=>'0','font_italic'=>'0','font_decoration'=>'',),'button_font'=>array('font_family'=>'Poppins','font_size'=>'14','font_bold'=>'0','font_italic'=>'0','font_decoration'=>'',),'content_font'=>array('font_family'=>'Poppins','font_size'=>'15','font_bold'=>'1','font_italic'=>'0','font_decoration'=>'',));

?>
<div class="wrap arm_page arm_profiles_directories_main_wrapper armPageContainer">
	<div class="content_wrapper arm_profiles_directories_container arm_min_height_500" id="content_wrapper">
		<div class="page_title"><?php esc_html_e( 'Profiles & Directories', 'armember-membership' ); ?></div>
		<div class="armclear"></div>
		<div class="arm_profiles_directories_templates_container">
			<div class="arm_profiles_directories_content arm_visible">
				<div id="arm_profile_templates_container" class="page_sub_content arm_profile_templates_container">
					<div class="arm_belt_box">
						<div class="arm_belt_block">
							<div class="page_sub_title"><?php esc_html_e( 'Member Profile Templates', 'armember-membership' ); ?></div>
						</div>
						<div class="arm_belt_block" align="<?php echo is_rtl() ? 'left' : 'right'; ?>">
							<div class="arm_membership_setup_shortcode_box" >
								<span class="arm_font_size_18"><?php esc_html_e( 'Shortcode', 'armember-membership' ); ?></span>
								<?php $shortCode = '[arm_template type="profile" id="1"]'; ?>
								<div class="arm_shortcode_text arm_form_shortcode_box" style="width:auto;">
									<span class="armCopyText"><?php echo esc_attr( $shortCode ); ?></span>
									<span class="arm_click_to_copy_text" data-code="<?php echo esc_attr( $shortCode ); ?>"><?php esc_html_e( 'Click to copy', 'armember-membership' ); ?></span>
									<span class="arm_copied_text"><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/copied_ok.png" alt="ok"/><?php esc_html_e( 'Code Copied', 'armember-membership' ); ?></span>
								</div>
							</div>
						</div>
					</div>
					
					<div id="arm_profile_templates" class="arm_profile_templates arm_pdt_content">
						<?php



						if ( ! empty( $member_templates['profile'] ) ) {
							foreach ( $member_templates['profile'] as $ptemp ) {


								$t_id              = $ptemp['arm_id'];
								$t_title           = $ptemp['arm_title'];
								$t_type            = $ptemp['arm_type'];
								$t_options         = maybe_unserialize( $ptemp['arm_options'] );
								$t_link_attr       = ' data-id="' . esc_attr($t_id) . '" data-type="' . esc_attr($t_type) . '" ';
								$t_container_class = '';
								$t_img_url         = MEMBERSHIPLITE_VIEWS_URL . '/templates/' . $ptemp['arm_slug'] . '.png';

								$default    = $ptemp['arm_default'];
								$plan_names = '';

								if ( $default == 1 ) {
									$plan_names = esc_html__( 'Default Profile Template', 'armember-membership' );
								} else {
									$subscription_plans = $ptemp['arm_subscription_plan'];
									if ( $subscription_plans == '' ) {
										$plan_names = '<strong>' . esc_html__( 'Associated Plans:', 'armember-membership' ) . '</strong><br/>' . esc_html__( 'No plan selected', 'armember-membership' );
									} else {
										$plan_name_array = explode( ',', $subscription_plans );
										$super_admin_placeholders = 'WHERE arm_subscription_plan_id IN (';
										$super_admin_placeholders .= rtrim( str_repeat( '%s,', count( $plan_name_array ) ), ',' );
										$super_admin_placeholders .= ')';
										array_unshift( $plan_name_array, $super_admin_placeholders );
										$sub_where = call_user_func_array(array( $wpdb, 'prepare' ), $plan_name_array );

										$plan_names_db   = $wpdb->get_results( 'SELECT `arm_subscription_plan_name` FROM ' . $ARMemberLite->tbl_arm_subscription_plans . ' '.$sub_where );//phpcs:ignore --Reason: $tbl_arm_subscription_plans is a table name. False Positive Alarm
										$plan_names      = '<strong>' . esc_html__( 'Associated Plans:', 'armember-membership' ) . ' </strong><br/>';
										if ( $plan_names_db != '' ) {
											foreach ( $plan_names_db as $db_plan_name ) {
												$plan_names .= $db_plan_name->arm_subscription_plan_name . ', ';
											}
										} else {
											$plan_names .= ' ' . esc_html__( 'No Plan selected', 'armember-membership' );
										}
										$plan_names = rtrim( $plan_names, ', ' );
									}
								}

								?>
								<div class="arm_template_content_wrapper arm_row_temp_<?php echo $t_id; //phpcs:ignore ?> <?php echo esc_attr($t_container_class); ?> armGridActionTD">
									<div class="arm_template_content_main_box">
										<a href="javascript:void(0)" class="arm_template_preview" <?php echo $t_link_attr; //phpcs:ignore ?>><img alt="<?php echo esc_attr($t_title); ?>" src="<?php echo esc_url($t_img_url); ?>"></a>
										<?php if ( ! empty( $t_title ) ) { ?>
											<div class="arm_template_name_div">
												<?php echo esc_attr($t_title); ?>
											</div>
										<?php } ?>
										<div class="arm_template_content_option_links">
											<a href="javascript:void(0)" class="arm_template_preview armhelptip" title="<?php esc_attr_e( 'Click to preview', 'armember-membership' ); ?>" <?php echo $t_link_attr; //phpcs:ignore ?>><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/dir_preview_icon.png" alt="" /></a>
											<a class="arm_template_edit_link armhelptip" title="<?php esc_attr_e( 'Edit Template Options', 'armember-membership' ); ?>" href="<?php echo esc_url( admin_url( 'admin.php?page=' . $arm_slugs->profiles_directories . '&action=edit_profile&id=' . $t_id ) ); //phpcs:ignore ?>" <?php echo $t_link_attr; //phpcs:ignore ?>><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/dir_edit_icon.png" alt="" /></a>
											
										</div>
									</div>
									
									<div class="armclear"></div>
									<div class="arm_profile_template_associalated_plan"><?php echo esc_attr($plan_names); ?></div>
								</div>
								<?php
							}
						}
						?>
						
					</div>
					<div class="armclear"></div>
					
					<div class="page_sub_title arm_margin_top_10"><?php esc_html_e( 'Member Profile URL', 'armember-membership' ); ?></div>
						<?php
						$permalink_base = ( isset( $arm_global_settings->global_settings['profile_permalink_base'] ) ) ? $arm_global_settings->global_settings['profile_permalink_base'] : 'user_login';
						$profileUrl = '';
						if ( get_option( 'permalink_structure' ) ) {
							if(!empty($arm_global_settings->profile_url))
							{
								$profileUrl = trailingslashit( untrailingslashit( $arm_global_settings->profile_url ) );
							}
							if ( $permalink_base == 'user_login' ) {
								$profileUrl = $profileUrl . '<b>username</b>/';
							} else {
								$profileUrl = $profileUrl . '<b>user_id</b>/';
							}
						} else {
							if(!empty($arm_global_settings->profile_url))
							{
								$profileUrl = $arm_global_settings->add_query_arg( 'arm_user', 'arm_base_slug', $arm_global_settings->profile_url );
							}
							if ( $permalink_base == 'user_login' ) {
								$profileUrl = str_replace( 'arm_base_slug', '<b>username</b>', $profileUrl );
							} else {
								$profileUrl = str_replace( 'arm_base_slug', '<b>user_id</b>', $profileUrl );
							}
						}
						?>
						<span class="arm_info_text">
						<?php
							echo esc_html__( 'Current user profile URL pattern', 'armember-membership' ) . ': ' . $profileUrl; //phpcs:ignore
							echo '&nbsp;&nbsp;<a href="' . esc_url( admin_url( 'admin.php?page=' . $arm_slugs->general_settings . '#profilePermalinkBase' ) ) . '">' . esc_html__( 'Change Pattern', 'armember-membership' ) . '</a>'; //phpcs:ignore
						?>
						</span>





				</div>
				<div class="armclear"></div>
				<div class="arm_solid_divider"></div>
				<div id="arm_directory_templates_container" class="page_sub_content arm_directory_templates_container">
					<div class="arm_belt_box">
						<div class="arm_belt_block">
							<div class="page_sub_title"><?php esc_html_e( 'Members Directory Templates', 'armember-membership' ); ?></div>
						</div>
					</div>
					<div id="arm_directory_templates" class="arm_directory_templates arm_pdt_content">
						<?php
						if ( ! empty( $member_templates['directory'] ) ) {
							foreach ( $member_templates['directory'] as $dtemp ) {
								$t_id              = $dtemp['arm_id'];
								$t_title           = $dtemp['arm_title'];
								$t_type            = $dtemp['arm_type'];
								$t_options         = maybe_unserialize( $dtemp['arm_options'] );
								$t_link_attr       = 'data-id="' . esc_attr($t_id) . '" data-type="' . esc_attr($t_type) . '"';
								$t_container_class = '';
								$t_img_url         = MEMBERSHIPLITE_VIEWS_URL . '/templates/' . $dtemp['arm_slug'] . '.png';
								?>
								<div class="arm_template_content_wrapper arm_row_temp_<?php echo esc_attr($t_id); ?> <?php echo esc_attr($t_container_class); ?> armGridActionTD">
									<div class="arm_template_content_main_box">
										<a href="javascript:void(0)" class="arm_template_preview" <?php echo $t_link_attr; //phpcs:ignore ?>><img alt="<?php echo esc_attr($t_title); ?>" src="<?php echo esc_url($t_img_url); ?>"></a>
										<?php if ( ! empty( $t_title ) ) { ?>
											<div class="arm_template_name_div">
												<?php echo esc_attr($t_title); ?>
											</div>
										<?php } ?>
										<div class="arm_template_content_option_links">
											<a href="javascript:void(0)" class="arm_template_preview armhelptip" title="<?php esc_attr_e( 'Click to preview', 'armember-membership' ); ?>" <?php echo $t_link_attr; //phpcs:ignore ?>><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/dir_preview_icon.png" alt="" /></a>
											<a href="javascript:void(0)" class="arm_template_edit_link armhelptip" title="<?php esc_html_e( 'Edit Template Options', 'armember-membership' ); ?>" <?php echo $t_link_attr; //phpcs:ignore ?>><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/dir_edit_icon.png" alt="" /></a>
											
										</div>
									</div>
									
									<!--<span class="arm_template_title"><?php echo esc_attr($t_title); ?></span>-->
									<div class="arm_short_code_detail">
										<span class="arm_shortcode_title"><?php esc_html_e( 'Short Code', 'armember-membership' ); ?>&nbsp;&nbsp;</span>
										<?php $shortCode = '[arm_template type="' . esc_attr($t_type) . '" id="' . esc_attr($t_id) . '"]'; ?>
										<div class="arm_shortcode_text arm_form_shortcode_box">
											<span class="armCopyText"><?php echo esc_attr( $shortCode ); ?></span>
											<span class="arm_click_to_copy_text" data-code="<?php echo esc_attr( $shortCode ); ?>"><?php esc_html_e( 'Click to copy', 'armember-membership' ); ?></span>
											<span class="arm_copied_text"><img src="<?php echo esc_attr(MEMBERSHIPLITE_IMAGES_URL); //phpcs:ignore ?>/copied_ok.png" alt="ok"/><?php esc_html_e( 'Code Copied', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="armclear"></div>
								</div>
								<?php
							}
						}
						?>
					   
					</div>
				</div>
				<div class="armclear"></div>
			</div>
			<div id="arm_add_profiles_directories_templates" class="arm_add_profiles_directories_templates">
				<?php
				$tempCS            = 'blue';
				$tempType          = 'profile';
				$backToListingIcon = MEMBERSHIPLITE_IMAGES_URL . '/back_to_listing_arrow.png';
				if ( is_rtl() ) {
					$backToListingIcon = MEMBERSHIPLITE_IMAGES_URL . '/back_to_listing_arrow_right.png';
				}
				?>
				<form method="POST" class="arm_admin_form arm_add_template_form" id="arm_add_template_form" onsubmit="return false;">
					<div class="arm_sticky_top_belt" id="arm_sticky_top_belt">
						<div class="arm_belt_box arm_template_action_belt">
							<div class="arm_belt_block">
								<a href="javascript:void(0)" class="arm_temp_back_to_list armemailaddbtn"><img src="<?php echo esc_url($backToListingIcon); ?>"/><?php esc_html_e( 'Back to listing', 'armember-membership' ); ?></a>
							</div>
							<div class="arm_belt_block arm_temp_action_btns" align="<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
								<button type="submit" class="arm_save_btn arm_add_template_submit" data-type="directory"><?php esc_html_e( 'Save', 'armember-membership' ); ?></button>
								<a href="javascript:void(0)" class="arm_add_temp_preview_btn armemailaddbtn" data-type="directory"><?php esc_html_e( 'Preview', 'armember-membership' ); ?></a>
							</div>
							<div class="armclear"></div>
						</div>
					</div>
					<div class="arm_belt_box arm_template_action_belt">
						<div class="arm_belt_block">
							<a href="javascript:void(0)" class="arm_temp_back_to_list armemailaddbtn"><img src="<?php echo esc_url($backToListingIcon); ?>"/><?php esc_html_e( 'Back to listing', 'armember-membership' ); ?></a>
						</div>
						<div class="arm_belt_block arm_temp_action_btns" align="<?php echo ( is_rtl() ) ? 'left' : 'right'; ?>">
							<button type="submit" class="arm_save_btn arm_add_template_submit" data-type="directory"><?php esc_html_e( 'Save', 'armember-membership' ); ?></button>
							<a href="javascript:void(0)" class="arm_add_temp_preview_btn armemailaddbtn" data-type="directory"><?php esc_html_e( 'Preview', 'armember-membership' ); ?></a>
						</div>
						<div class="armclear"></div>
					</div>
					<div class="armclear"></div>
					<div class="arm_add_template_options_wrapper">
						<div class="page_sub_title"><?php esc_html_e( 'Template Options', 'armember-membership' ); ?></div>
						<div class="arm_solid_divider"></div>
						<div class="arm_template_option_block">
							<div class="arm_opt_title"><?php esc_html_e( 'Select Template', 'armember-membership' ); ?></div>
							<div class="arm_opt_content">
								<?php if ( ! empty( $defaultTemplates ) ) : ?>
									<?php
									$templateTypes = array();
									foreach ( $defaultTemplates as $temp ) {
										$templateTypes[ $temp['arm_type'] ][] = $temp;
										if ( is_file( MEMBERSHIPLITE_VIEWS_DIR . '/templates/' . $temp['arm_slug'] . '.css' ) ) {
											wp_enqueue_style( 'arm_template_style_' . $temp['arm_slug'], MEMBERSHIPLITE_VIEWS_URL . '/templates/' . $temp['arm_slug'] . '.css', array(), MEMBERSHIPLITE_VERSION );
										}
									}
									?>
									<?php
									$i = 0;
									foreach ( $templateTypes as $type => $temps ) :
										?>
										<?php foreach ( $temps as $temp ) : ?>

											<label class="arm_tempalte_type_box arm_temp_<?php echo esc_attr($type); ?>_options <?php echo ( $i == 0 ) ? 'arm_active_temp' : ''; ?>" data-type="<?php echo esc_attr($type); ?>" for="arm_temp_type_<?php echo esc_attr($temp['arm_slug']); ?>" style="<?php echo ( $type == $tempType ? '' : 'display:none;' ); ?>">
												<input type="radio" name="template_options[<?php echo esc_attr($type); ?>]" id="arm_temp_type_<?php echo esc_attr($temp['arm_slug']); ?>" class="arm_temp_type_radio arm_temp_type_radio_<?php echo esc_attr($type); ?>" value="<?php echo esc_attr($temp['arm_slug']); ?>" <?php echo ( $i == 0 ) ? 'checked="checked"' : ''; ?> data-type="<?php echo esc_attr($type); ?>">
												<img alt="" src="<?php echo esc_attr(MEMBERSHIPLITE_VIEWS_URL) . '/templates/' . esc_attr($temp['arm_slug']) . '.png'; //phpcs:ignore ?>"/>
												<span class="arm_temp_selected_text"><?php esc_html_e( 'Selected', 'armember-membership' ); ?></span>
											</label>
											<?php
											$i++;
										endforeach;
										?>
										<?php $i = 0; ?>
									<?php endforeach; ?>
								<?php endif; ?>
							</div>
						</div>
						<div class="arm_solid_divider"></div>
						<div class="arm_template_option_block">
							<div class="arm_opt_title"><?php esc_html_e( 'Color Scheme', 'armember-membership' ); ?></div>
							<div class="arm_opt_content">
								<div class="c_schemes arm_padding_left_5" >
									<?php foreach ( $tempColorSchemes as $color => $color_opt ) : ?>
										<label class="arm_temp_color_scheme_block arm_temp_color_scheme_block_<?php echo esc_attr($color); ?> <?php echo ( $color == $tempCS ) ? 'arm_color_box_active' : ''; ?>">
											<span style="background-color:<?php echo esc_attr($color_opt['button_color']); ?>;"></span>
											<span style="background-color:<?php echo esc_attr($color_opt['tab_bg_color']); ?>;"></span>
											<input type="radio" id="arm_temp_color_radio_<?php echo esc_attr($color); ?>" name="template_options[color_scheme]" value="<?php echo esc_attr($color); ?>" class="arm_temp_color_radio" data-type="<?php echo esc_attr($temp['arm_type']); ?>" <?php checked( $tempCS, $color ); ?>/>
										</label>
									<?php endforeach; ?>
									<label class="arm_temp_color_scheme_block arm_temp_color_scheme_block_custom <?php echo ( $color == 'custom' ) ? 'arm_color_box_active' : ''; ?>">
										<input type="radio" id="arm_temp_color_radio_custom" name="template_options[color_scheme]" value="custom" class="arm_temp_color_radio" data-type="<?php echo esc_attr($tempType); ?>" <?php checked( $tempCS, 'custom' ); ?>/>
									</label>
								</div>
								<div class="armclear arm_height_1" ></div>
								<div class="arm_temp_color_options" id="arm_temp_color_options" style="<?php echo ( $color == 'custom' ) ? '' : 'display:none;'; ?>">
									<div class="arm_custom_color_opts">
										<label class="arm_opt_label"><?php esc_html_e( 'Title Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['title_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['title_color']) : '#000000'; ?>">
												<input type="text" name="template_options[title_color]" id="arm_title_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['title_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['title_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Main Title', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['subtitle_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['subtitle_color']) : '#000000'; ?>">
												<input type="text" name="template_options[subtitle_color]" id="arm_subtitle_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['subtitle_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['subtitle_color']) : '#000000'; ?>">
											<span><?php esc_html_e( 'Sub Title', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts">
										<label class="arm_opt_label"><?php esc_html_e( 'Button Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['button_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['button_color']) : '#000000'; ?>">
												<input type="text" name="template_options[button_color]" id="arm_button_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['button_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['button_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Background', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['button_font_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['button_font_color']) : '#000000'; ?>">
												<input type="text" name="template_options[button_font_color]" id="arm_button_font_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['button_font_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['button_font_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Text', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts arm_temp_directory_options">
										<label class="arm_opt_label"><?php esc_html_e( 'Effect Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['border_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['border_color']) : '#000000'; ?>">
												<input type="text" name="template_options[border_color]" id="arm_border_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['border_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['border_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Box Hover', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts arm_temp_directory_options atm_temp_3_opt">
										<label class="arm_opt_label"><?php esc_html_e( 'Background Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['box_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['box_bg_color']) : '#000000'; ?>">
												<input type="text" name="template_options[box_bg_color]" id="arm_box_bg_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['box_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['box_bg_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Top Belt', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts arm_temp_profile_options">
										<label class="arm_opt_label"><?php esc_html_e( 'Tab Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_bg_color']) : '#000000'; ?>">
												<input type="text" name="template_options[tab_bg_color]" id="arm_tab_bg_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_bg_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Background', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_color']) : '#000000'; ?>">
												<input type="text" name="template_options[tab_link_color]" id="arm_tab_link_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Text', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_bg_color']) : '#000000'; ?>">
												<input type="text" name="template_options[tab_link_bg_color]" id="arm_tab_link_bg_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_bg_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Background', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts arm_temp_profile_options">
										<label class="arm_opt_label"><?php esc_html_e( 'Active Tab Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_hover_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_hover_color']) : '#000000'; ?>">
												<input type="text" name="template_options[tab_link_hover_color]" id="arm_tab_link_hover_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_hover_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_hover_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Text', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_hover_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_hover_bg_color']) : '#000000'; ?>">
												<input type="text" name="template_options[tab_link_hover_bg_color]" id="arm_tab_link_hover_bg_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['tab_link_hover_bg_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['tab_link_hover_bg_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Background', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts">
										<label class="arm_opt_label"><?php esc_html_e( 'Other Link Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['link_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['link_color']) : '#000000'; ?>">
												<input type="text" name="template_options[link_color]" id="arm_link_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['link_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['link_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Text', 'armember-membership' ); ?></span>
										</div>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['link_hover_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['link_hover_color']) : '#000000'; ?>">
												<input type="text" name="template_options[link_hover_color]" id="arm_link_hover_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['link_hover_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['link_hover_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Link Hover', 'armember-membership' ); ?></span>
										</div>
									</div>
									<div class="arm_custom_color_opts arm_temp_profile_options">
										<label class="arm_opt_label"><?php esc_html_e( 'Body Content Color', 'armember-membership' ); ?></label>
										<div class="arm_custom_color_picker">
											<label class="arm_colorpicker_label" style="background-color:<?php echo ( isset( $tempColorSchemes[ $tempCS ]['content_font_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['content_font_color']) : '#000000'; ?>">
												<input type="text" name="template_options[content_font_color]" id="arm_content_font_color" class="arm_colorpicker" value="<?php echo ( isset( $tempColorSchemes[ $tempCS ]['content_font_color'] ) ) ? esc_attr($tempColorSchemes[ $tempCS ]['content_font_color']) : '#000000'; ?>">
											</label>
											<span><?php esc_html_e( 'Content Text', 'armember-membership' ); ?></span>
										</div>
									</div>
								</div>
							</div>
						</div>
						<div class="arm_solid_divider"></div>
						<div class="arm_template_option_block">
							<div class="arm_opt_title"><?php esc_html_e( 'Font Settings', 'armember-membership' ); ?></div>
							<div class="arm_opt_content">
								<?php
								$fontOptions = array(
									'title_font'    => esc_html__( 'Title Font', 'armember-membership' ),
									'subtitle_font' => esc_html__( 'Sub Title Font', 'armember-membership' ),
									'button_font'   => esc_html__( 'Button Font', 'armember-membership' ),
									'content_font'  => esc_html__( 'Content Font', 'armember-membership' ),
								);
								?>
								<?php foreach ( $fontOptions as $key => $value ) : ?>
									<div class="arm_temp_font_opts_box">
										<div class="arm_opt_label"><?php echo esc_attr($value); ?></div>
										<div class="arm_temp_font_opts">
											<input type="hidden" id="arm_template_font_family_<?php echo esc_attr($key); ?>" name="template_options[<?php echo esc_attr($key); ?>][font_family]" value="<?php echo esc_attr($fonts_option[ $key ]['font_family']); ?>"/>
											<dl class="arm_selectbox column_level_dd arm_margin_right_10 arm_width_230">
												<dt><span></span><input type="text" style="display:none;" value="" class="arm_autocomplete"  /><i class="armfa armfa-caret-down armfa-lg"></i></dt>
												<dd>
													<ul data-id="arm_template_font_family_<?php echo esc_attr($key); ?>"><?php echo $arm_member_forms->arm_fonts_list(); //phpcs:ignore ?></ul>
												</dd>
											</dl>
											<?php
											$fontSize = '14';
											if ( $key == 'content_font' ) {
												$fontSize = '16';
											}
											?>
											<input type="hidden" id="arm_template_font_size_<?php echo esc_attr($key); ?>" name="template_options[<?php echo esc_attr($key); ?>][font_size]" value="<?php echo esc_attr($fontSize); ?>"/>
											<dl class="arm_selectbox column_level_dd arm_margin_right_10 arm_width_90">
												<dt><span></span><input type="text" style="display:none;" value="" class="arm_autocomplete"  /><i class="armfa armfa-caret-down armfa-lg"></i></dt>
												<dd>
													<ul data-id="arm_template_font_size_<?php echo esc_attr($key); ?>">
														<?php for ( $i = 8; $i < 41; $i++ ) : ?>
															<li data-label="<?php echo esc_attr($i); ?> px" data-value="<?php echo esc_attr($i); ?>"><?php echo esc_html($i); ?> px</li>
														<?php endfor; ?>
													</ul>
												</dd>
											</dl>
											<div class="arm_font_style_options arm_template_font_style_options">
												<label class="arm_font_style_label" data-value="bold" data-field="arm_template_font_bold_<?php echo esc_attr($key); ?>"><i class="armfa armfa-bold"></i></label>
												<input type="hidden" name="template_options[<?php echo esc_attr($key); ?>][font_bold]" id="arm_template_font_bold_<?php echo esc_attr($key); ?>" class="arm_template_font_bold_<?php echo esc_attr($key); ?>" value="" />
												<label class="arm_font_style_label" data-value="italic" data-field="arm_template_font_italic_<?php echo esc_attr($key); ?>"><i class="armfa armfa-italic"></i></label>
												<input type="hidden" name="template_options[<?php echo esc_attr($key); ?>][font_italic]" id="arm_template_font_italic_<?php echo esc_attr($key); ?>" class="arm_template_font_italic_<?php echo esc_attr($key); ?>" value="" />

											<label class="arm_font_style_label arm_decoration_label" data-value="underline" data-field="arm_template_font_decoration_<?php echo esc_attr($key); ?>"><i class="armfa armfa-underline"></i></label>
											<label class="arm_font_style_label arm_decoration_label" data-value="line-through" data-field="arm_template_font_decoration_<?php echo esc_attr($key); ?>"><i class="armfa armfa-strikethrough"></i></label>
											<input type="hidden" name="template_options[<?php echo esc_attr($key); ?>][font_decoration]" id="arm_template_font_decoration_<?php echo esc_attr($key); ?>" class="arm_template_font_decoration_<?php echo esc_attr($key); ?>" value="" />
										</div>
									</div>
								</div>
								<?php endforeach; ?>
							</div>
						</div>
												
						<div class="arm_solid_divider"></div>
						<div class="arm_template_option_block">
							<div class="arm_opt_title"><?php esc_html_e( 'Other Options', 'armember-membership' ); ?></div>
							<div class="arm_opt_content">
																<div class="arm_temp_opt_box">
									<div class="arm_opt_label"><?php esc_html_e( 'Display Administrator Users', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_show_admin_users" value="1" class="armswitch_input" name="template_options[show_admin_users]"/>
												<label for="arm_temp_show_admin_users" class="armswitch_label"></label>
											</div>
										</div>
									</div>
								</div>
								
																<div class="arm_temp_opt_box">
									<div class="arm_opt_label"><?php esc_html_e( 'Display Joining Date', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_show_joining" value="1" class="armswitch_input" name="template_options[show_joining]" checked="checked"/>
												<label for="arm_temp_show_joining" class="armswitch_label"></label>
											</div>
										</div>
									</div>
								</div>
																<div class="arm_temp_opt_box arm_temp_directory_options">
									<div class="arm_opt_label"><?php esc_html_e( 'Redirect To Author Archive Page', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_redirect_to_author" value="1" class="armswitch_input" name="template_options[redirect_to_author]"/>
												<label for="arm_temp_redirect_to_author" class="armswitch_label"></label>
											</div>
											<div class="armclear arm_height_1" ></div>
											<span class="arm_info_text arm_width_450" >(<?php esc_html_e( 'If Author have no any post than user will be redirect to ARMember Profile Page', 'armember-membership' ); ?>)</span>
										</div>
									</div>
								</div>
															   
																<div class="arm_temp_opt_box arm_temp_profile_options">
									<div class="arm_opt_label"><?php esc_html_e( 'Hide empty profile fields', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_hide_empty_profile_fields" value="0" class="armswitch_input" name="template_options[hide_empty_profile_fields]"/>
												<label for="arm_temp_hide_empty_profile_fields" class="armswitch_label"></label>
											</div>
										</div>
									</div>
								</div>
								<div class="arm_temp_opt_box arm_subscription_plans_box">
									<div class="arm_opt_label"><?php esc_html_e( 'Select Membership Plans', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<select id="arm_temp_plans" class="arm_chosen_selectbox arm_template_plans_select" name="template_options[plans][]" data-placeholder="<?php esc_html_e( 'Select Plan(s)..', 'armember-membership' ); ?>" multiple="multiple">
											<?php if ( ! empty( $subs_data ) ) : ?>
												<?php foreach ( $subs_data as $sd ) : ?>
													<option class="arm_message_selectbox_op" value="<?php echo esc_attr($sd['arm_subscription_plan_id']); ?>"><?php echo stripslashes( esc_attr($sd['arm_subscription_plan_name']) ); //phpcs:ignore ?></option>
												<?php endforeach; ?>
											<?php endif; ?>
										</select>
										<div class="armclear arm_height_1" ></div>
										<span class="arm_temp_sub_plan_error arm_color_red" style="display:none;"><?php esc_html_e( 'Please select atleast one plan', 'armember-membership' ); ?></span>
										<span class="arm_info_text arm_temp_directory_options">(<?php esc_html_e( "Leave blank to display all plan's members.", 'armember-membership' ); ?>)</span>
									</div>
								</div>
								<div class="arm_temp_opt_box arm_temp_directory_options">
									<div class="arm_opt_label"><?php esc_html_e( 'Filter Options', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper arm_min_width_550">
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_searchbox" value="1" class="armswitch_input" name="template_options[searchbox]" checked="checked"/>
												<label for="arm_temp_searchbox" class="armswitch_label"></label>
											</div>
											<label for="arm_temp_searchbox"><?php esc_html_e( 'Display Search Box', 'armember-membership' ); ?></label>
										</div>
										<div class="arm_temp_switch_wrapper">
											<div class="armswitch arm_global_setting_switch">
												<input type="checkbox" id="arm_temp_sortbox" value="1" class="armswitch_input" name="template_options[sortbox]" checked="checked"/>
												<label for="arm_temp_sortbox" class="armswitch_label"></label>
											</div>
											<label for="arm_temp_sortbox"><?php esc_html_e( 'Display Sorting Options', 'armember-membership' ); ?></label>
										</div>
									</div>
								</div>
								<div class="arm_temp_opt_box arm_temp_directory_options">
									<div class="arm_opt_label"><?php esc_html_e( 'No. Of Members Per Page', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<input id="arm_temp_per_page_users" type="text" onkeydown="javascript:return checkNumber(event)" value="10" name="template_options[per_page_users]" class="arm_width_70">
									</div>
								</div>
								<div class="arm_temp_opt_box arm_temp_directory_options">
									<div class="arm_opt_label"><?php esc_html_e( 'Pagination Style', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<input type="radio" name="template_options[pagination]" value="numeric" id="arm_temp_pagination_numeric" class="arm_iradio" checked="checked"><label for="arm_temp_pagination_numeric"><span><?php echo esc_html_e( 'Numeric', 'armember-membership' ); ?></span></label>
										<input type="radio" name="template_options[pagination]" value="infinite" id="arm_temp_pagination_infinite" class="arm_iradio" checked="checked"><label for="arm_temp_pagination_infinite"><span><?php echo esc_html_e( 'Load More Link', 'armember-membership' ); ?></span></label>
									</div>
								</div>
								<!-- Socail Profile Fields Start-->
								<div class="arm_temp_opt_box">
									<div class="arm_opt_label"><?php esc_html_e( 'Social Profile Fields', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="social_profile_fields">
											<?php
											$socialProfileFields = $arm_member_forms->arm_social_profile_field_types();
											$activeSPF           = array( 'facebook', 'twitter', 'linkedin' );
											if ( ! empty( $socialFieldsOptions ) ) {
												$activeSPF = isset( $socialFieldsOptions['arm_form_field_option']['options'] ) ? $socialFieldsOptions['arm_form_field_option']['options'] : array();
											}
											$activeSPF = ( ! empty( $activeSPF ) ) ? $activeSPF : array();
											?>
											<div class="arm_social_profile_fields_list_wrapper">
												<?php if ( ! empty( $socialProfileFields ) ) : ?>
													<?php foreach ( $socialProfileFields as $spfKey => $spfLabel ) : ?>
														<div class="arm_social_profile_field_item">
															<input type="checkbox" class="arm_icheckbox arm_spf_active_checkbox" value="<?php echo esc_attr($spfKey); ?>" name="template_options[arm_social_fields][<?php echo esc_attr($spfKey); ?>]" id="arm_spf_<?php echo esc_attr($spfKey); ?>_status" <?php echo ( in_array( $spfKey, $activeSPF ) ) ? 'checked="checked"' : ''; ?>>
														   <label for="arm_spf_<?php echo esc_attr($spfKey); ?>_status"><?php echo esc_attr($spfLabel); ?></label>
														</div>
													<?php endforeach; ?>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>

								<!-- Socail Profile Fields End-->
																
																
																
																
																<!-- Profile Fields Start-->
								<div class="arm_temp_opt_box arm_temp_directory_options">
									<div class="arm_opt_label"><?php esc_html_e( 'Search Members by Profile Fields', 'armember-membership' ); ?></div>
									<div class="arm_opt_content_wrapper">
										<div class="profile_search_fields">
											<?php
																				$dbProfileFields = $arm_members_directory->arm_template_profile_fields();



											$activePF = array( 'first_name', 'last_name' );
											?>
											<div class="arm_profile_search_fields_list_wrapper">
												<?php if ( ! empty( $dbProfileFields ) ) : ?>
													<?php
													foreach ( $dbProfileFields as $pfKey => $pfLabel ) :
														if ( empty( $pfKey ) || $pfKey == 'user_pass' || in_array( $pfLabel['type'], array( 'html', 'section', 'rememberme', 'file', 'avatar', 'password', 'roles' ) ) ) {
															continue;
														}
														?>
														<div class="arm_profile_search_field_item">
															<input type="checkbox" class="arm_icheckbox arm_pf_active_checkbox" value="<?php echo esc_attr($pfKey); ?>" name="template_options[profile_fields][<?php echo esc_attr($pfKey); ?>]" id="arm_pf_<?php echo esc_attr($pfKey); ?>_status" <?php echo ( in_array( $pfKey, $activePF ) ) ? 'checked="checked"' : ''; ?>>
															<label for="arm_pf_<?php echo esc_attr($pfKey); ?>_status"><?php echo stripslashes( esc_attr($pfLabel['label']) ); //phpcs:ignore ?></label>
														</div>
	<?php endforeach; ?>
<?php endif; ?>
											</div>
										</div>
									</div>
								</div>

								<!-- Profile Fields End-->
								
								<div class="armclear"></div>
								<div class="arm_temp_opt_box">
									<div class="arm_opt_label"></div>
									<div class="arm_opt_content_wrapper">
										<button type="submit" class="arm_save_btn arm_add_template_submit" data-type="directory"><?php esc_html_e( 'Save', 'armember-membership' ); ?></button>
									</div>
								</div>
							</div>
						</div>
						<div class="armclear"></div>
					</div>
				</form>
			</div>
					
					
	
				
						
						<?php

						$temp_id  = 1;
						$tempType = 'profile';
						if ( ! empty( $temp_id ) && $temp_id != 0 ) {
							$tempDetails = $arm_members_directory->arm_get_template_by_id( $temp_id );

							if ( ! empty( $tempDetails ) ) {



								$tempType                       = isset( $tempDetails['arm_type'] ) ? $tempDetails['arm_type'] : 'directory';
								$tempOptions                    = $tempDetails['arm_options'];
								$popup                          = '<div class="arm_ptemp_add_popup_wrapper popup_wrapper" >';
								$is_rtl_form                    = is_rtl() ? 'arm_add_form_rtl' : '';
								$popup                         .= '<form action="#" method="post" class="arm_profile_template_add_form arm_admin_form ' . esc_attr($is_rtl_form) . '" onsubmit="return false;" id="arm_profile_template_add_form" data-temp_id="' . esc_attr($temp_id) . '">';
														$popup .= '<table cellspacing="0">';
									$popup                     .= '<tr class="popup_wrapper_inner">';
								$popup                         .= '<td class="popup_header">';
									$popup                     .= '<span class="popup_close_btn arm_popup_close_btn arm_add_profile_template_popup_close_btn"></span>';
									$popup                     .= '<span>' . esc_html__( 'Select Profile Template', 'armember-membership' ) . '</span>';
								$popup                         .= '</td>';
								$popup                         .= '<td class="popup_content_text">';
									$popup                     .= $arm_members_directory->arm_profile_template_options( $tempType );
								$popup                         .= '</td>';
								$popup                         .= '<td class="popup_content_btn popup_footer">';
									$popup                     .= '<input type="hidden" name="id" id="arm_pdtemp_edit_id" value="' . esc_attr($temp_id) . '">';
									$popup                     .= '<div class="popup_content_btn_wrapper arm_temp_option_wrapper">';
								$popup                         .= '<input type="hidden" id="arm_admin_url" value="' . esc_url( admin_url( 'admin.php?page=' . $arm_slugs->profiles_directories . '&action=add_profile' ) ) . '" />';
								$popup                         .= '<button class="arm_save_btn arm_profile_next_submit" data-id="' . esc_attr($temp_id) . '" type="submit" name="arm_add_profile" id="arm_profile_next_submit">' . esc_html__( 'OK', 'armember-membership' ) . '</button>';
									$popup                     .= '<button class="arm_cancel_btn arm_profile_add_close_btn" type="button">' . esc_html__( 'Cancel', 'armember-membership' ) . '</button>';
									$popup                     .= '</div>';
									$popup                     .= '<div class="popup_content_btn_wrapper arm_temp_custom_class_btn hidden_section">';
									$backToListingIcon          = MEMBERSHIPLITE_IMAGES_URL . '/back_to_listing_arrow.png';
									$popup                     .= '<a href="javascript:void(0)" class="arm_section_custom_css_detail_hide_template armemailaddbtn"><img src="' . esc_attr($backToListingIcon) . '"/>' . esc_html__( 'Back to template options', 'armember-membership' ) . '</a>';
									$popup                     .= '</div>';
								$popup                         .= '</td>';
									$popup                     .= '</tr>';
									$popup                     .= '</table>';
								$popup                         .= '</form>';
								echo $popup                    .= '</div>';  //phpcs:ignore




							}
						}
						?>
		</div>
		<div class="armclear"></div>
		<?php $wpnonce = wp_create_nonce( 'arm_wp_nonce' );?>
		<input type="hidden" name="arm_wp_nonce" value="<?php echo esc_attr($wpnonce);?>"/>
		<div id="arm_profile_directory_template_preview" class="arm_profile_directory_template_preview"></div>
		<div id="arm_pdtemp_edit_popup_container" class="arm_pdtemp_edit_popup_container"></div>
	</div>
	<div class="arm_section_custom_css_detail_container"></div>
		
		<?php

		/* **********./Begin Bulk Delete Member Popup/.********** */
		$arm_template_change_message_popup_content  = '<span class="arm_confirm_text">' . esc_html__( 'Plese confirm that while changing Template, all colors will be reset to default.', 'armember-membership' );
		$arm_template_change_message_popup_content .= '<input type="hidden" value="false" id="bulk_delete_flag"/>';
		$arm_template_change_message_popup_arg      = array(
			'id'             => 'arm_template_change_message',
			'class'          => 'arm_template_change_message',
			'title'          => esc_html__( 'Change Directory Template', 'armember-membership' ),
			'content'        => $arm_template_change_message_popup_content,
			'button_id'      => 'arm_template_change_message_ok_btn',
			'button_onclick' => "arm_template_change_message_action('bulk_delete_flag');",
		);
		echo $arm_global_settings->arm_get_bpopup_html( $arm_template_change_message_popup_arg ); //phpcs:ignore
		?>
</div>
<style type="text/css" title="currentStyle">
	#adminmenuback{z-index: 101;}
	#adminmenuwrap{z-index: 9990;}
</style>
<script type="text/javascript">
function armTempColorSchemes() {
	var tempColorSchemes = <?php echo json_encode( $tempColorSchemes ); ?>;
	return tempColorSchemes;
}
function armTempColorSchemes1() {
	var tempColorSchemes = <?php echo json_encode( $tempColorSchemes1 ); ?>;
	return tempColorSchemes;
}
function setAdminStickyTopMenu() {
	var h = jQuery(document).height() - jQuery(window).height();
	var sp = jQuery(window).scrollTop();
	var p = parseInt(sp / h * 100);
	if (p >= 10) {
		if(jQuery('.arm_add_profiles_directories_templates.arm_visible .arm_sticky_top_belt').length > 0){
			jQuery('.arm_add_profiles_directories_templates.arm_visible .arm_sticky_top_belt').slideDown(600);
		} else {
			jQuery('.arm_sticky_top_belt').slideUp(600);
		}
	} else {
		jQuery('.arm_sticky_top_belt').slideUp(600);
	}
}
jQuery(document).ready(function (e) {
	setAdminStickyTopMenu();
});
jQuery(window).scroll(function () {
	setAdminStickyTopMenu();
});
jQuery(window).on("load", function(){
	var popupH = jQuery('.arm_template_preview_popup').height();
	jQuery('.arm_template_preview_popup .popup_content_text').css('height', (popupH - 60)+'px');
	var contentHeight = jQuery('.arm_visible').outerHeight();
	jQuery('.arm_profiles_directories_templates_container').css('height', contentHeight + 20);
});
jQuery(window).resize(function(){
	var popupH = jQuery('.arm_template_preview_popup').height();
	jQuery('.arm_template_preview_popup .popup_content_text').css('height', (popupH - 60)+'px');
	var contentHeight = jQuery('.arm_visible').outerHeight();
	jQuery('.arm_profiles_directories_templates_container').css('height', contentHeight + 20);
});
</script>
<?php
echo $ARMemberLite->arm_get_need_help_html_content('members-profile-directories'); //phpcs:ignore
?>