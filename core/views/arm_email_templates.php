<?php
global $wpdb, $ARMemberLite, $arm_members_class, $arm_member_forms, $arm_global_settings, $arm_email_settings, $arm_social_feature, $arm_slugs, $arm_subscription_plans, $arm_manage_communication;

$arm_all_email_settings = $arm_email_settings->arm_get_all_email_settings();
$template_list          = $arm_email_settings->arm_get_all_email_template();


$form_id   = 'arm_add_message_wrapper_frm';
$mid       = 0;
$edit_mode = false;
$msg_type  = 'on_new_subscription';

$get_page = isset($_GET['page']) ? sanitize_text_field(esc_attr( $_GET['page'] )) : ''; //phpcs:ignore
?>
<style type="text/css" title="currentStyle">
	.paginate_page a{display:none;}
	#poststuff #post-body {margin-top: 32px;}
	.delete_box{float:left;}
	.ColVis_Button{ display: none !important;}
</style>
<script type="text/javascript" charset="utf-8">
// <![CDATA[
jQuery(document).ready(function () {
	var __ARM_Showing = '<?php echo addslashes(esc_html__('Showing','armember-membership')); //phpcs:ignore?>';
    var __ARM_Showing_empty = '<?php echo addslashes(esc_html__('Showing 0 to 0 of 0 entries','armember-membership')); //phpcs:ignore?>';
    var __ARM_to = '<?php echo addslashes(esc_html__('to','armember-membership')); //phpcs:ignore?>';
    var __ARM_of = '<?php echo addslashes(esc_html__('of','armember-membership')); //phpcs:ignore?>';
    var __ARM_RECORDS = '<?php echo addslashes(esc_html__('entries','armember-membership')); //phpcs:ignore?>';
    var __ARM_Show = '<?php echo addslashes(esc_html__('Show','armember-membership')); //phpcs:ignore?>';
    var __ARM_NO_FOUND = '<?php echo addslashes(esc_html__('No email template found.','armember-membership')); //phpcs:ignore?>';
    var __ARM_NO_MATCHING = '<?php echo addslashes(esc_html__('No matching records found.','armember-membership')); //phpcs:ignore?>';
	jQuery('#armember_datatable').dataTable({
		"sDom": '<"H"Cfr>t<"footer"ipl>',
		"sPaginationType": "four_button",
				"oLanguage": {
                    "sInfo": __ARM_Showing + " _START_ " + __ARM_to + " _END_ " + __ARM_of + " _TOTAL_ " + __ARM_RECORDS,
                    "sInfoEmpty": __ARM_Showing_empty,
                    "sLengthMenu": __ARM_Show + "_MENU_" + __ARM_RECORDS,
                    "sEmptyTable": __ARM_NO_FOUND,
                    "sZeroRecords": __ARM_NO_MATCHING
				},
		"bJQueryUI": true,
		"bPaginate": true,
		"bAutoWidth": false,
		"aaSorting": [],
		"aoColumnDefs": [
			{"bVisible": false, "aTargets": []},
			{"bSortable": false, "aTargets": [1]}
		],
		"language":{
			"searchPlaceholder": "<?php esc_html_e( 'Search', 'armember-membership' ); ?>",
			"search":"",
		},
		"oColVis": {
			"aiExclude": [0]
		},
		"iDisplayLength": 50,
	});
		
		arm_load_communication_messages_list_grid();
	   
});

function arm_load_communication_list_filtered_grid(data)
{
	var tbl = jQuery('#armember_datatable_1').dataTable(); 
		
		tbl.fnDeleteRow(data);
	   
		jQuery('#armember_datatable_1').dataTable().fnDestroy();
		arm_load_communication_messages_list_grid();
}

function arm_load_communication_messages_list_grid() {
	jQuery('#armember_datatable_1').dataTable({
		"sDom": '<"H"Cfr>t<"footer"ipl>',
		"sPaginationType": "four_button",
		"oLanguage": {
			"sEmptyTable": "No any automated email message found.",
			"sZeroRecords": "No matching records found."
		},
		"bJQueryUI": true,
		"bPaginate": true,
		"bAutoWidth": false,
		"aaSorting": [],
		"aoColumnDefs": [
			{"bVisible": false, "aTargets": []},
			{"bSortable": false, "aTargets": [0, 2, 5]}
		],
		"language":{
			"searchPlaceholder": "<?php esc_html_e( 'Search', 'armember-membership' ); ?>",
			"search":"",
		},
		"oColVis": {
			"aiExclude": [0, 5]
		},
				"fnDrawCallback": function () {
					jQuery("#cb-select-all-1").prop("checked", false);
				},
	});
		
		 var filter_box = jQuery('#arm_filter_wrapper_after_filter').html();
		  
	jQuery('div#armember_datatable_1_filter').parent().append(filter_box);
	jQuery('#arm_filter_wrapper').remove(); 
	
	}
function ChangeID(id) {
	document.getElementById('delete_id').value = id;
}
// ]]>
</script>
<div class="arm_email_notifications_main_wrapper">
	<div class="page_sub_content">
		<div class="page_sub_title" style="float: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;" ><?php esc_html_e( 'Standard Email Responses', 'armember-membership' ); ?></div>
		<?php if ( empty( $messages ) ) : ?>
		
		<?php endif; ?>
		<div class="armclear"></div>
		<div class="arm_email_templates_list">
		<form method="GET" id="email_templates_list_form" class="data_grid_list arm_email_settings_wrapper">
			<input type="hidden" name="page" value="<?php echo esc_attr($get_page); ?>" />
			<input type="hidden" name="armaction" value="list" />
			<div id="armmainformnewlist">
				<div class="response_messages"></div>
				<div class="armclear"></div>
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="armember_datatable">
					<thead>
						<tr>
							<!--<th class="center"><?php esc_html_e( 'ID', 'armember-membership' ); ?></th>-->
							<th><?php esc_html_e( 'Template Name', 'armember-membership' ); ?></th>
							<th class="arm_text_align_center arm_width_100" ><?php esc_html_e( 'Active', 'armember-membership' ); ?></th>
							<th class="arm_padding_left_10" style="text-align: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;"><?php esc_html_e( 'Subject', 'armember-membership' ); ?></th>
							<th class="armGridActionTD"></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( ! empty( $template_list ) ) : ?>
							<?php foreach ( $template_list as $key => $email_template ) { ?>
								<?php
								if ( $email_template->arm_template_slug == 'follow-notification' || $email_template->arm_template_slug == 'unfollow-notification' ) {
									if ( ! $arm_social_feature->isSocialFeature ) {
										continue;
									}
								}
								if ( $email_template->arm_template_slug == 'email-verify-user' || $email_template->arm_template_slug == 'account-verified-user' ) {
									$user_register_verification = $arm_global_settings->arm_get_single_global_settings( 'user_register_verification' );
									if ( $user_register_verification != 'email' ) {
										continue;
									}
								}
								$tempID    = $email_template->arm_template_id;
								$edit_link = admin_url( 'admin.php?page=' . $arm_slugs->email_notifications . '&action=edit_template&template_id=' . $tempID );
								?>
							<tr class="member_row_<?php echo intval($tempID); ?>">
								<!--<td class="center"><?php echo intval($tempID); ?></td>-->
								<td><a class="arm_edit_template_btn" href="javascript:void(0);" data-temp_id="<?php echo intval($tempID); ?>" data-href="<?php echo esc_url($edit_link); //phpcs:ignore ?>"><?php echo esc_html($email_template->arm_template_name); ?></a></td>
								<td class="center">
								<?php
									$switchChecked = ( $email_template->arm_template_status == 1 ) ? 'checked="checked"' : '';
									echo '<div class="armswitch">
										<input type="checkbox" class="armswitch_input arm_email_status_action" id="arm_email_status_input_' . intval($tempID) . '" value="1" data-item_id="' . intval($tempID) . '" ' . $switchChecked . '><label class="armswitch_label" for="arm_email_status_input_' . intval($tempID) . '"></label> <span class="arm_status_loader_img"></span></div>'; //phpcs:ignore
								?>
								</td>
								<td id="arm_email_template_subject_<?php echo intval($tempID); ?>"><?php echo esc_html( stripslashes( $email_template->arm_template_subject ) ); ?></td>
								<td class="armGridActionTD">
								<?php
									$gridAction  = "<div class='arm_grid_action_btn_container'>";
									$gridAction .= "<a class='arm_edit_template_btn' href='javascript:void(0);' data-temp_id='" . esc_attr($tempID) . "'><img src='" . esc_attr(MEMBERSHIPLITE_IMAGES_URL) . "/grid_edit.png' onmouseover=\"this.src='" . esc_attr(MEMBERSHIPLITE_IMAGES_URL) . "/grid_edit_hover.png';\" class='armhelptip' title='" . esc_html__( 'Edit Message', 'armember-membership' ) . "' onmouseout=\"this.src='" . esc_attr(MEMBERSHIPLITE_IMAGES_URL) . "/grid_edit.png';\" /></a>";
									$gridAction .= '</div>';
									echo '<div class="arm_grid_action_wrapper">' . $gridAction . '</div>'; //phpcs:ignore
								?>
								</td>
							</tr>
						<?php } ?>
						<?php endif; ?>
					</tbody>
				</table>
				<div class="armclear"></div>
				<input type="hidden" name="show_hide_columns" id="show_hide_columns" value="<?php esc_attr_e( 'Show / Hide columns', 'armember-membership' ); ?>"/>
				<input type="hidden" name="search_grid" id="search_grid" value="<?php esc_attr_e( 'Search', 'armember-membership' ); ?>"/>
				<input type="hidden" name="entries_grid" id="entries_grid" value="<?php esc_attr_e( 'messages', 'armember-membership' ); ?>"/>
				<input type="hidden" name="show_grid" id="show_grid" value="<?php esc_attr_e( 'Show', 'armember-membership' ); ?>"/>
				<input type="hidden" name="showing_grid" id="showing_grid" value="<?php esc_attr_e( 'Showing', 'armember-membership' ); ?>"/>
				<input type="hidden" name="to_grid" id="to_grid" value="<?php esc_attr_e( 'to', 'armember-membership' ); ?>"/>
				<input type="hidden" name="of_grid" id="of_grid" value="<?php esc_attr_e( 'of', 'armember-membership' ); ?>"/>
				<input type="hidden" name="no_match_record_grid" id="no_match_record_grid" value="<?php esc_attr_e( 'No matching templates found.', 'armember-membership' ); ?>"/>
				<input type="hidden" name="no_record_grid" id="no_record_grid" value="<?php esc_attr_e( 'No any email template found.', 'armember-membership' ); ?>"/>
				<input type="hidden" name="filter_grid" id="filter_grid" value="<?php esc_attr_e( 'filtered from', 'armember-membership' ); ?>"/>
				<input type="hidden" name="totalwd_grid" id="totalwd_grid" value="<?php esc_attr_e( 'total', 'armember-membership' ); ?>"/>
				<?php $wpnonce = wp_create_nonce( 'arm_wp_nonce' );?>
				<input type="hidden" name="arm_wp_nonce" value="<?php echo esc_attr($wpnonce);?>"/>
			</div>
			<div class="footer_grid"></div>
		</form>
		<div class="armclear"></div>
		</div>
	</div>
<?php if ( ! empty( $messages ) ) : ?>
	<div class="arm_solid_divider"></div>
		<div class="arm_filter_wrapper" id="arm_filter_wrapper_after_filter" style="display:none;">
			<div class="arm_datatable_filters_options">
				<div class='sltstandard'>
					<input type="hidden" id="arm_communication_bulk_action1" name="action1" value="-1" />
					<dl class="arm_selectbox column_level_dd arm_width_250">
						<dt><span></span><input type="text" style="display:none;" value="" class="arm_autocomplete"  /><i class="armfa armfa-caret-down armfa-lg"></i></dt>
						<dd>
							<ul data-id="arm_communication_bulk_action1">
								<li data-label="<?php esc_attr_e( 'Bulk Actions', 'armember-membership' ); ?>" data-value="-1"><?php esc_html_e( 'Bulk Actions', 'armember-membership' ); ?></li>
								<li data-label="<?php esc_attr_e( 'Delete', 'armember-membership' ); ?>" data-value="delete_communication"><?php esc_html_e( 'Delete', 'armember-membership' ); ?></li>
							</ul>
						</dd>
					</dl>
				</div>
				<input type="submit" id="doaction1" class="armbulkbtn armemailaddbtn" value="<?php esc_attr_e( 'Go', 'armember-membership' ); ?>"/>
			</div>
		</div>
	<div class="page_sub_content">
		<div class="page_sub_title" style="float: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;" ><?php esc_html_e( 'Automated Email Messages', 'armember-membership' ); ?></div>
		<div class="arm_add_new_item_box" style="margin: 0 0 20px 0;">			
			<a class="greensavebtn arm_add_new_message_btn arm_margin_right_40" href="javascript:void(0);" ><img align="absmiddle" src="<?php echo MEMBERSHIPLITE_IMAGES_URL; //phpcs:ignore ?>/add_new_icon.png"><span><?php esc_html_e( 'Add New Response', 'armember-membership' ); ?></span></a>
		</div>
		<div class="armclear"></div>
		<div class="arm_filter_wrapper" id="arm_filter_wrapper" style="display:none;">
			<div class="arm_datatable_filters_options">
				<div class='sltstandard'>
					<input type="hidden" id="arm_communication_bulk_action1" name="action1" value="-1" />
					<dl class="arm_selectbox column_level_dd arm_width_120">
						<dt><span></span><input type="text" style="display:none;" value="" class="arm_autocomplete"  /><i class="armfa armfa-caret-down armfa-lg"></i></dt>
						<dd>
							<ul data-id="arm_communication_bulk_action1">
								<li data-label="<?php esc_attr_e( 'Bulk Actions', 'armember-membership' ); ?>" data-value="-1"><?php esc_html_e( 'Bulk Actions', 'armember-membership' ); ?></li>
								<li data-label="<?php esc_attr_e( 'Delete', 'armember-membership' ); ?>" data-value="delete_communication"><?php esc_html_e( 'Delete', 'armember-membership' ); ?></li>
							</ul>
						</dd>
					</dl>
				</div>
				<input type="submit" id="doaction1" class="armbulkbtn armemailaddbtn" value="<?php esc_attr_e( 'Go', 'armember-membership' ); ?>"/>
			</div>
		</div>
		<?php
		/*
		<form method="GET" id="communication_list_form" class="data_grid_list arm_email_settings_wrapper" onsubmit="return apply_bulk_action_communication_list();return false;">
			<input type="hidden" name="page" value="<?php echo esc_attr( $get_page ); ?>" />
			<input type="hidden" name="armaction" value="list" />
			<div id="armmainformnewlist">
				<table cellpadding="0" cellspacing="0" border="0" class="display" id="armember_datatable_1">
					<thead>
						<tr>
							<th class="center cb-select-all-th arm_max_width_60" ><input id="cb-select-all-1" type="checkbox" class="chkstanard"></th>
							<th style="text-align: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;"><?php esc_html_e( 'Message Subject', 'armember-membership' ); ?></th>
							<th class="arm_width_100 arm_text_align_center"><?php esc_html_e( 'Active', 'armember-membership' ); ?></th>
							<th style="text-align: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;"><?php esc_html_e( 'Subscription', 'armember-membership' ); ?></th>
							<th style="text-align: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;"><?php esc_html_e( 'Type', 'armember-membership' ); ?></th>
							<th class="armGridActionTD"></th>
						</tr>
					</thead>
					<tbody id="">
					<?php if ( ! empty( $messages ) ) : ?>
						<?php
						foreach ( $messages as $key => $rc ) {
							$messageID = $rc->arm_message_id;
							$edit_link = admin_url( 'admin.php?page=' . $arm_slugs->email_notifications . '&action=edit_communication&message_id=' . $messageID );
							?>
						<tr class="arm_message_tr_<?php echo $messageID; ?> row_<?php echo $messageID; ?>">
							<td class="arm_padding_left_17">
								<input class="chkstanard arm_bulk_select_single" type="checkbox" value="<?php echo $messageID; ?>" name="item-action[]">
							</td>
							<td>
								<a class="arm_edit_message_btn" href="javascript:void(0);" data-message_id="<?php echo $messageID; ?>"><?php echo esc_html( stripslashes( $rc->arm_message_subject ) ); ?></a>
							</td>
							<td class="center">
							<?php
								$switchChecked = ( $rc->arm_message_status == '1' ) ? 'checked="checked"' : '';
								echo '<div class="armswitch">
									<input type="checkbox" class="armswitch_input arm_communication_status_action" id="arm_communication_status_input_' . $messageID . '" value="1" data-item_id="' . $messageID . '" ' . $switchChecked . '>
									<label class="armswitch_label" for="arm_communication_status_input_' . $messageID . '"></label>
									<span class="arm_status_loader_img"></span>
								</div>';
							?>
							</td>
							<?php
							$subs_plan_title = '';
							if ( ! empty( $rc->arm_message_subscription ) ) {
								$plans_id        = @explode( ',', $rc->arm_message_subscription );
								$subs_plan_title = $arm_subscription_plans->arm_get_comma_plan_names_by_ids( $plans_id );
								$subs_plan_title = ( ! empty( $subs_plan_title ) ) ? $subs_plan_title : '--';
							} else {
								$subs_plan_title = esc_html__( 'All Membership Plans', 'armember-membership' );
							}
							?>
							<td class=""><?php echo $subs_plan_title; ?></td>
							<td>
							<?php
							$msge_type = '';
							switch ( $rc->arm_message_type ) {
								case 'on_new_subscription':
									$msge_type = esc_html__( 'On New Subscription', 'armember-membership' );
									break;
								case 'on_menual_activation':
									$msge_type = esc_html__( 'On Manual User Activation', 'armember-membership' );
									break;
								case 'on_change_subscription':
									$msge_type = esc_html__( 'On Change Subscription', 'armember-membership' );
									break;
								case 'on_renew_subscription':
									$msge_type = esc_html__( 'On Renew Subscription', 'armember-membership' );
									break;
								case 'on_failed':
									$msge_type = esc_html__( 'On Failed Payment', 'armember-membership' );
									break;
								case 'on_next_payment_failed':
									$msge_type = esc_html__( 'On Semi Automatic Subscription Failed Payment', 'armember-membership' );
									break;
								case 'trial_finished':
									$msge_type = esc_html__( 'Trial Finished', 'armember-membership' );
									break;
								case 'on_expire':
									$msge_type = esc_html__( 'On Membership Expired', 'armember-membership' );
									break;
								case 'before_expire':
									$msge_per_unit = $rc->arm_message_period_unit;
									$msge_per_type = $rc->arm_message_period_type;
									$msge_type     = $msge_per_unit . ' ' . $msge_per_type . '(s) ' . esc_html__( 'Before Membership Expired', 'armember-membership' );
									break;
								case 'manual_subscription_reminder':
										$msge_per_unit = $rc->arm_message_period_unit;
									$msge_per_type     = $rc->arm_message_period_type;
									$msge_type         = esc_html__( 'Semi Automatic Subscription Payment Reminder', 'armember-membership' );
										$msge_type    .= '(BeFore ' . $msge_per_unit . ' ' . $msge_per_type . '(s))';
									break;
								case 'on_change_subscription_by_admin':
										 $msge_type = esc_html__( 'On Change Subscription By Admin', 'armember-membership' );
									break;
								case 'before_dripped_content_available':
										$msge_per_unit = $rc->arm_message_period_unit;
									$msge_per_type     = $rc->arm_message_period_type;
									$msge_type         = $msge_per_unit . ' ' . $msge_per_type . '(s) ' . esc_html__( 'Before Dripped Content Available', 'armember-membership' );
									break;
								case 'on_cancel_subscription':
									$msge_type = esc_html__( 'On Cancel Subscription', 'armember-membership' );
									break;
								default:
																		$msge_type = apply_filters( 'arm_notification_get_list_msg_type', $rc->arm_message_type );
									break;
							}
							echo $msge_type;
							?>
							</td>
							<td class="armGridActionTD">
							<?php

								$gridAction  = "<div class='arm_grid_action_btn_container'>";
								$gridAction .= "<a class='arm_edit_message_btn' href='javascript:void(0);' data-message_id='" . $messageID . "'><img src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_edit.png' onmouseover=\"this.src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_edit_hover.png';\" class='armhelptip' title='" . esc_html__( 'Edit Message', 'armember-membership' ) . "' onmouseout=\"this.src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_edit.png';\" /></a>";
								$gridAction .= "<a href='javascript:void(0)' onclick='showConfirmBoxCallback({$messageID});'><img src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_delete.png' class='armhelptip' title='" . esc_html__( 'Delete', 'armember-membership' ) . "' onmouseover=\"this.src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_delete_hover.png';\" onmouseout=\"this.src='" . MEMBERSHIPLITE_IMAGES_URL . "/grid_delete.png';\" /></a>";
								$gridAction .= $arm_global_settings->arm_get_confirm_box( $messageID, esc_html__( 'Are you sure you want to delete this message?', 'armember-membership' ), 'arm_communication_delete_btn' );
								$gridAction .= '</div>';
								echo '<div class="arm_grid_action_wrapper">' . $gridAction . '</div>';
							?>
							</td>
						</tr>
							<?php } ?>  
					<?php endif; ?>
					</tbody>
				</table>
				<div class="armclear"></div>
				<input type="hidden" name="search_grid" id="search_grid" value="<?php esc_html_e( 'Search', 'armember-membership' ); ?>"/>
				<input type="hidden" name="entries_grid" id="entries_grid" value="<?php esc_html_e( 'messages', 'armember-membership' ); ?>"/>
				<input type="hidden" name="show_grid" id="show_grid" value="<?php esc_html_e( 'Show', 'armember-membership' ); ?>"/>
				<input type="hidden" name="showing_grid" id="showing_grid" value="<?php esc_html_e( 'Showing', 'armember-membership' ); ?>"/>
				<input type="hidden" name="to_grid" id="to_grid" value="<?php esc_html_e( 'to', 'armember-membership' ); ?>"/>
				<input type="hidden" name="of_grid" id="of_grid" value="<?php esc_html_e( 'of', 'armember-membership' ); ?>"/>
				<input type="hidden" name="no_match_record_grid" id="no_match_record_grid" value="<?php esc_html_e( 'No matching messages found', 'armember-membership' ); ?>"/>
				<input type="hidden" name="no_record_grid" id="no_record_grid" value="<?php esc_html_e( 'There is no any communication message found.', 'armember-membership' ); ?>"/>
				<input type="hidden" name="filter_grid" id="filter_grid" value="<?php esc_html_e( 'filtered from', 'armember-membership' ); ?>"/>
				<input type="hidden" name="totalwd_grid" id="totalwd_grid" value="<?php esc_html_e( 'total', 'armember-membership' ); ?>"/>
				<?php $wpnonce = wp_create_nonce( 'arm_wp_nonce' );?>
				<input type="hidden" name="arm_wp_nonce" value="<?php echo esc_attr($wpnonce);?>"/>
			</div>
			<div class="footer_grid"></div>
		</form>
		*/
		?>
		<div class="armclear"></div>
		<?php
		/* **********./Begin Bulk Delete Communication Popup/.********** */
		$bulk_delete_message_popup_content  = '<span class="arm_confirm_text">' . esc_html__( 'Are you sure you want to delete this message(s)?', 'armember-membership' ) . '</span>';
		$bulk_delete_message_popup_content .= '<input type="hidden" value="false" id="bulk_delete_flag"/>';
		$bulk_delete_message_popup_arg      = array(
			'id'             => 'delete_bulk_communication_message',
			'class'          => 'delete_bulk_communication_message',
			'title'          => 'Delete Communication Message(s)',
			'content'        => $bulk_delete_message_popup_content,
			'button_id'      => 'arm_bulk_delete_message_ok_btn',
			'button_onclick' => "arm_delete_bulk_communication('true');",
		);
		echo $arm_global_settings->arm_get_bpopup_html( $bulk_delete_message_popup_arg ); //phpcs:ignore
		/* **********./End Bulk Delete Communication Popup/.********** */
		?>
		<div class="armclear"></div>
	</div>
<?php endif; ?>
</div>
<!--./******************** Add New Member Form ********************/.-->

<div class="add_edit_message_wrapper_container"></div>
<div class="edit_email_template_wrapper popup_wrapper" >
	<form method="post" id="arm_edit_email_temp_frm" class="arm_admin_form arm_responses_message_wrapper_frm" action="#" onsubmit="return false;">
		<input type='hidden' name="arm_template_id" id="arm_template_id" value="0"/>
		<table cellspacing="0">
			<tr class="popup_wrapper_inner">	
				<td class="edit_template_close_btn arm_popup_close_btn"></td>
				<td class="popup_header"><?php esc_html_e( 'Edit Email Template', 'armember-membership' ); ?></td>
				<td class="popup_content_text">
					<table class="arm_table_label_on_top">	
						<tr class="">
							<th><?php esc_html_e( 'Subject', 'armember-membership' ); ?></th>
							<td>
								<input class="arm_input_tab arm_width_510" type="text" name="arm_template_subject" id="arm_template_subject" value="" data-msg-required="<?php esc_attr_e( 'Email Subject Required.', 'armember-membership' ); ?>"/>
							</td>
						</tr>
						<tr class="form-field">
							<th><?php esc_html_e( 'Message', 'armember-membership' ); ?></th>
							<td>
								<div class="arm_email_content_area_left">
								<?php
								$email_setting_editor = array(
									'textarea_name'  => 'arm_template_content',
									'editor_class'   => 'arm_message_content',
									'media_buttons'  => false,
									'textarea_rows'  => 5,
									'default_editor' => 'html',
									'editor_css'     => '<style type="text/css"> body#tinymce{margin:0px !important;} </style>',
								);
								wp_editor( '', 'arm_template_content', $email_setting_editor );
								?>
									<span id="arm_responses_wp_validate_msg" class="error" style="display:none;"><?php esc_html_e( 'Content Cannot Be Empty.', 'armember-membership' ); ?></span>
								</div>
								<div class="arm_email_content_area_right">
									<span class="arm_sec_head"><?php esc_html_e( 'Template Tags', 'armember-membership' ); ?></span>
									<div class="arm_constant_variables_wrapper arm_shortcode_wrapper" id="arm_shortcode_wrapper">
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_ADMIN_EMAIL}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Admin Email', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the admin email that users can contact you at. You can configure it under Mail settings.', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_BLOGNAME}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Blog Name', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays blog name', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_BLOG_URL}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Blog URL', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays blog URL', 'armember-membership' ); ?>"></i>
										</div>
										<!--									<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_BLOG_ADMIN}" title="<?php esc_html_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Blog Admin', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon fa fa-question-circle" title="<?php esc_html_e( 'Displays blog WP-admin URL', 'armember-membership' ); ?>"></i>
										</div>-->
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_LOGIN_URL}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Login URL', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the ARM login page', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_USERNAME}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Username', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the Username of user', 'armember-membership' ); ?>"></i>
										</div>
															<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_USER_ID}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'User ID', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the User ID of user', 'armember-membership' ); ?>"></i>
										</div>
															<div class="arm_shortcode_row arm_email_code_reset_password">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_RESET_PASSWORD_LINK}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Reset Password Link', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the Reset Password Link for user', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_FIRST_NAME}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'First Name', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the user first name', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_LAST_NAME}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Last Name', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the user last name', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_NAME}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Display Name', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the user display name or public name', 'armember-membership' ); ?>"></i>
										</div>                                        
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_EMAIL}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Email', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the E-mail address of user', 'armember-membership' ); ?>"></i>
										</div>                                        
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PROFILE_LINK}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'User Profile Link', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the User Profile address', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_VALIDATE_URL}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Validation URL', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'The account validation URL that user receives after signing up (If you enable e-mail validation feature)', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_USERMETA_meta_key}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'User Meta Key', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php echo esc_attr_e( "To Display User's meta field value.", 'armember-membership' ) . ' (' . esc_attr__( 'Where', 'armember-membership' ) . ' `meta_key` ' . esc_attr__( 'is meta field name.', 'armember-membership' ) . ')'; ?>"></i>
										</div>
										
										<div class="arm_shortcode_row arm_email_code_plan_name">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PLAN}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Plan Name', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the plan name of user', 'armember-membership' ); ?>"></i>
										</div>										
										<div class="arm_shortcode_row arm_email_code_plan_desc">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PLAN_DESCRIPTION}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Plan Description', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the plan description of user', 'armember-membership' ); ?>"></i>
										</div>	
										<div class="arm_shortcode_row arm_email_code_plan_amount">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PLAN_AMOUNT}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Plan Amount', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the plan amount of user', 'armember-membership' ); ?>"></i>
										</div>
										
										<div class="arm_shortcode_row arm_email_code_trial_amount">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_TRIAL_AMOUNT}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Trial Amount', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the trial amount of plan', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row arm_email_code_payable_amount">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PAYABLE_AMOUNT}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Payable Amount', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the Final Payable Amount of user', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row arm_email_code_payment_type">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PAYMENT_TYPE}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Payment Type', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the payment type of user', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row arm_email_code_payment_gateway">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_PAYMENT_GATEWAY}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Payment Gateway', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the payment gateway of user', 'armember-membership' ); ?>"></i>
										</div>
										<div class="arm_shortcode_row arm_email_code_transaction_id">
											<span class="arm_variable_code arm_standard_email_code" data-code="{ARM_TRANSACTION_ID}" title="<?php esc_attr_e( 'Click to add shortcode in textarea', 'armember-membership' ); ?>"><?php esc_html_e( 'Transaction Id', 'armember-membership' ); ?></span><i class="arm_email_helptip_icon armfa armfa-question-circle" title="<?php esc_attr_e( 'Displays the payment transaction Id of user', 'armember-membership' ); ?>"></i>
										</div>
																				<?php do_action( 'arm_email_notification_template_shortcode' ); ?>
									</div>
								</div>
							</td>
						</tr>
						<tr>
							<th></th>
							<td>	
								<span class="arm-note-message --warning"><?php printf( esc_html__( 'NOTE : Please add %1$sbr%2$s to use line break in plain text.', 'armember-membership' ), '&lt;', '&gt;' ); //phpcs:ignore ?></span>
							</td>
						</tr>
					</table>
					<input type=hidden name="arm_template_status" id="arm_template_status" value=""/>
					<div class="armclear"></div>
				</td>
				<td class="popup_content_btn popup_footer">
					<div class="popup_content_btn_wrapper">
						<img src="<?php echo MEMBERSHIPLITE_IMAGES_URL . '/arm_loader.gif'; //phpcs:ignore ?>" id="arm_loader_img_temp" class="arm_loader_img arm_submit_btn_loader" style="top: 15px;display: none;float: <?php echo ( is_rtl() ) ? 'right' : 'left'; ?>;" width="20" height="20" />
						<button class="arm_save_btn" id="arm_email_template_submit" type="submit"><?php esc_html_e( 'Save', 'armember-membership' ); ?></button>
						<button class="arm_cancel_btn edit_template_close_btn" type="button"><?php esc_html_e( 'Cancel', 'armember-membership' ); ?></button>
					</div>
				</td>
			</tr>
		</table>
		<div class="armclear"></div>
	</form>
</div>
<script type="text/javascript">
	__ARM_ADDNEWRESPONSE = '<?php esc_html_e( 'Add New Response', 'armember-membership' ); ?>';
	__ARM_VALUE = '<?php esc_html_e( 'Value', 'armember-membership' ); ?>';
</script>
