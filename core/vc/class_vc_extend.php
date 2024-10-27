<?php
if ( ! defined( 'WPINC' ) ) {
	die;
}

class ARMLITE_VCExtend {

    protected static $instance = null;
    var $is_membership_vdextend = 0;
    var $isWPBakryBuilderRestrictionFeature;

    public function __construct() {
        $is_wpbakery_builder_restriction_feature = get_option('arm_is_wpbakery_page_builder_restriction_feature');
        $this->isWPBakryBuilderRestrictionFeature = ($is_wpbakery_builder_restriction_feature == '1') ? true : false;
        if ($this->isWPBakryBuilderRestrictionFeature) {
            add_action('init', array($this, 'ARM_arm_form'));
            add_action('init', array($this, 'ARM_arm_edit_profile'));
            add_action('init', array($this, 'ARM_arm_logout'));
            add_action('init', array($this, 'ARM_arm_setup'));
            add_action('init', array($this, 'ARM_arm_member_transaction'));
            add_action('init', array($this, 'ARM_arm_account_detail'));
            add_action('init', array($this, 'ARM_arm_close_account'));
            add_action('init', array($this, 'ARM_arm_membership'));
            add_action('init', array($this, 'ARM_arm_username'));
            add_action('init', array($this, 'ARM_arm_user_plan'));
            add_action('init', array($this, 'ARM_arm_displayname'));
            add_action('init', array($this, 'ARM_arm_firstname_lastname'));
            add_action('init', array($this, 'ARM_arm_avatar'));
            add_action('init', array($this, 'ARM_arm_usermeta'));
            add_action('init', array($this, 'ARM_arm_user_planinfo'));
            add_action('vc_before_init', array($this, 'ARM_init_all_shortcode'));
        }
    }

    public function ARM_init_all_shortcode() {           
        add_shortcode('arm_form_vc', array($this, 'arm_form_vc_func'));
        add_shortcode('arm_profile_detail_vc', array($this, 'arm_edit_profile_vc_func'));
        add_shortcode('arm_membership_vc', array($this, 'arm_membership_vc_func'));
        add_shortcode('arm_logout_vc', array($this, 'arm_logout_vc_func'));
        add_shortcode('arm_setup_vc', array($this, 'arm_setup_vc_func'));
        add_shortcode('arm_member_transaction_vc', array($this, 'arm_member_transaction_vc_func'));
        add_shortcode('arm_account_detail_vc', array($this, 'arm_account_detail_vc_func'));
        add_shortcode('arm_close_account_vc', array($this, 'arm_close_account_vc_func'));
        add_shortcode('arm_username_vc', array($this, 'arm_username_vc_func'));
        add_shortcode('arm_user_plan_vc', array($this, 'arm_user_plan_vc_func'));
        add_shortcode('arm_displayname_vc', array($this, 'arm_displayname_vc_func'));
        add_shortcode('arm_firstname_lastname_vc', array($this, 'arm_firstname_lastname_vc_func'));
        add_shortcode('arm_avatar_vc', array($this, 'arm_avatar_vc_func'));
        add_shortcode('arm_usermeta_vc', array($this, 'arm_usermeta_vc_func'));
        add_shortcode('arm_user_planinfo_vc', array($this, 'arm_user_planinfo_vc_func'));
    }

    public function ARM_arm_form() {
		global $arm_lite_version, $ARMemberLite, $arm_member_forms, $arm_subscription_plans;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $arm_forms = $arm_member_forms->arm_get_all_member_forms('arm_form_id, arm_form_label, arm_form_type');
        $armFormList = array();
        $armFormId = array();
        $armFormList = array(
            __('Select Form', 'armember-membership') => '',
        );

        if (!empty($arm_forms)) {
            foreach ($arm_forms as $_form) {
                if($_form['arm_form_type'] == 'registration') {
                    $armFormId[] = $_form['arm_form_id'];
                }
                $armFormList[ strip_tags(stripslashes($_form['arm_form_label'])) . ' (ID: ' . $_form['arm_form_id'] . ')' ] =  $_form['arm_form_id'];
            }
        }

        $all_plans = $arm_subscription_plans->arm_get_all_subscription_plans('arm_subscription_plan_id, arm_subscription_plan_name');
        $arm_planlist = array();
        $arm_planlist = array(
            __('Select Plan', 'armember-membership') => '',
        );
        if(!empty($all_plans)){
            foreach($all_plans as $plan){
                if(!$arm_subscription_plans->isFreePlanExist($plan['arm_subscription_plan_id'])){ continue; }
                $arm_planlist [ $plan['arm_subscription_plan_name'] ] = $plan['arm_subscription_plan_id'];
            }
        }

        $arm_form_position = array(
            __('Center','armember-membership') => 'center',
            __('Left','armember-membership') => 'left',
            __('Right','armember-membership') => 'right',
        );

        $arm_form_popup = array(
            __('Internal', 'armember-membership') => 'false',
            __('External popup window', 'armember-membership') => 'true',
        );
        
        $arm_form_link_type = array(
            __('Link', 'armember-membership') => 'link',
            __('Button', 'armember-membership') => 'button',
            __('On Load', 'armember-membership') => 'onload',
        );

        $arm_form_overlay = array( '10' => '0.1',  '20' => '0.2',  '30' => '0.3',  '40' => '0.4',  '50' => '0.5',  '60' => '0.6',  '70' => '0.7',  '80' => '0.8',  '90' => '0.9', '100' => '1' );

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Form', 'armember-membership'),
                'description' => '',
                'base' => 'arm_form_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => 'armember-form',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select a form to insert into page', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'id',
                        'value' => $armFormList,
                        'group' => __( 'ARMember From', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Logged in Message','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'logged_in_message',
                        'value' => esc_html__('You are already loggedin!', 'armember-membership'),
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'id',
                            'not_empty' => true,
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Assign Default Plan','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'assign_default_plan',
                        'value' => $arm_planlist,
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'id',
                            'value' => $armFormId,
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('How you want to include this form into page?', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup',
                        'value' => $arm_form_popup,
                        'group' => __( 'ARMember From', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Form Position','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'form_position',
                        'value' => $arm_form_position,
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'false',
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Link Type', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_type',
                        'value' => $arm_form_link_type,
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Link Text', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_title',
                        'value' => 'Click here to open Form',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Background Overlay', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'overlay',
                        'value' => $arm_form_overlay,
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'class' => '',
                        'heading' => __('Background Color', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'modal_bgcolor',
                        'value' => '#000000',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Height', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup_height',
                        'value' => 'auto',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Width', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup_width',
                        'value' => '700',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_hover_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember From', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                )
            ));
        }
    }
    public function arm_form_vc_func( $atts, $content, $tag ){

        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if($hasaccess) {
            $id = isset($atts['id']) && !empty($atts['id']) ? intval( $atts['id'] ) : '' ;
            $logged_in_message = isset($atts['logged_in_message']) && !empty($atts['logged_in_message']) ? esc_attr( $atts['logged_in_message']) : esc_html__('You are already loggedin!', 'armember-membership') ;
            $assign_default_plan = isset($atts['assign_default_plan']) && !empty($atts['assign_default_plan']) ? intval( $atts['assign_default_plan'] ) : 0 ;
            $form_position = isset($atts['form_position']) && !empty($atts['form_position']) ? esc_attr( $atts['form_position'] ) : esc_attr( 'center' ) ;
            $popup = isset($atts['popup']) && !empty($atts['popup']) ? esc_attr( $atts['popup'] ) : 'false' ;
            $popup_height = isset($atts['popup_height']) && !empty($atts['popup_height']) ? esc_attr( $atts['popup_height'] ) : esc_attr( 'auto' ) ;
            $popup_width = isset($atts['popup_width']) && !empty($atts['popup_width']) ? $atts['popup_width'] : '' ;
            $link_type = isset($atts['link_type']) && !empty($atts['link_type']) ? esc_attr( $atts['link_type'] ) : esc_attr( 'link' ) ;
            $link_title = isset($atts['link_title']) && !empty($atts['link_title']) ? esc_attr( $atts['link_title']) : esc_attr__('Click here to open form', 'armember-membership') ;
            $link_css = isset($atts['link_css']) && !empty($atts['link_css']) ? esc_attr( $atts['link_css'] ) : '' ;
            $link_hover_css = isset($atts['link_hover_css']) && !empty($atts['link_hover_css']) ? esc_attr( $atts['link_hover_css'] ) : '' ;
            $overlay = isset($atts['overlay']) && !empty($atts['overlay']) ? esc_attr( $atts['overlay'] ) : '0.6' ;
            $modal_bgcolor = isset($atts['modal_bgcolor']) && !empty($atts['modal_bgcolor']) ? esc_attr( $atts['modal_bgcolor'] ) : '' ;

            return do_shortcode('[arm_form id="'.$id.'" logged_in_message="'.$logged_in_message.'" assign_default_plan="'.$assign_default_plan.'" form_position="'.$form_position.'" popup="'.$popup.'" popup_height="'.$popup_height.'" popup_width="'.$popup_width.'" link_type="'.$link_type.'" link_title="'.$link_title.'"  link_css="'.$link_css.'" link_hover_css="'.$link_hover_css.'" overlay="'.$overlay.'" modal_bgcolor="'.$modal_bgcolor.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_edit_profile() {
		global $arm_lite_version, $ARMemberLite, $arm_member_forms;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $arm_forms = $arm_member_forms->arm_get_all_member_forms('arm_form_id, arm_form_label, arm_form_type');
        $armFormList = array();
        $armFormList = array(
            __('Select Form', 'armember-membership') => '',
        );

        if (!empty($arm_forms)) {
            foreach ($arm_forms as $_form) {
                if ($_form['arm_form_type'] == 'registration') {
                    $armFormList[ strip_tags(stripslashes($_form['arm_form_label'])) . ' (ID: ' . $_form['arm_form_id'] . ')' ] =  $_form['arm_form_id'];
                }
            }
        }

        $arm_form_position = array(
            __('Center','armember-membership') => 'center',
            __('Left','armember-membership') => 'left',
            __('Right','armember-membership') => 'right',
        );

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Edit Profile', 'armember-membership'),
                'description' => '',
                'base' => 'arm_profile_detail_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select Form', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'form_id',
                        'value' => $armFormList, 
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'title',
                        'value' => esc_html__( 'Edit Profile', 'armember-membership' ), 
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Message', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'message',
                        'value' => esc_html__( 'Your profile has been updated successfully.', 'armember-membership' ),
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Form Position', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'form_position',
                        'value' => $arm_form_position,
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('View Profile', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'view_profile',
                        'value' => array(
                            __( 'View Profile', 'armember-membership') => 'view_profile',
                        ),
                        "std" => "view_profile",
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('View Profile Link Label', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'view_profile_link',
                        'value' => esc_html__( 'View Profile', 'armember-membership' ),
                        'group' => __( 'ARMember Edit profile', 'armember-membership' ),
                    )
                )
            ));
        }
    }
    public function arm_edit_profile_vc_func( $atts, $content, $tag ) {
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if($hasaccess) {
            $form_id = isset($atts['form_id']) && !empty($atts['form_id']) ? intval( $atts['form_id'] ) : '101' ;
            $title = isset($atts['title']) && !empty($atts['title']) ? $atts['title'] : esc_html__( 'Edit Profile', 'armember-membership' ) ;
            $form_position = isset($atts['form_position']) && !empty($atts['form_position']) ? esc_attr( $atts['form_position'] ) : 'center' ;
            $message = isset($atts['message']) && !empty($atts['message']) ? $atts['message'] : esc_html__( 'Your profile has been updated successfully.', 'armember-membership' ) ;
            $view_profile = isset($atts['view_profile'])  ? 'false' : 'true' ;
            $view_profile_link = isset($atts['view_profile_link']) && !empty($atts['view_profile_link']) ? $atts['view_profile_link'] : esc_html__( 'View Profile', 'armember-membership' ) ;

            return do_shortcode('[arm_edit_profile title="'. $title .'" form_id="'. $form_id .'" form_position="'. $form_position .'" social_fields="facebook,twitter,linkedin" submit_text="Update Profile" message="'. $message .'" view_profile="'. $view_profile .'" view_profile_link="'. $view_profile_link .'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_logout() {
		global $arm_lite_version, $ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $arm_form_link_type = array(
            __('Link', 'armember-membership') => 'link',
            __('Button', 'armember-membership') => 'button',
        );

        $arm_form_user_info = array(
            __('Yes', 'armember-membership') => 'true',
            __('No', 'armember-membership') => 'false',
        );

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Logout', 'armember-membership'),
                'description' => '',
                'base' => 'arm_logout_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Link Type', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'type',
                        'value' => $arm_form_link_type,
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Link Text', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'label',
                        'value' => 'Logout',
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Display User Info', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'user_info',
                        'value' => $arm_form_user_info,
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Redirect After Logout', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'redirect_to',
                        'value' => ARMLITE_HOME_URL,
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_hover_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember Logout', 'armember-membership' ),
                    ),
                )
            ));
        }
    }
    public function arm_logout_vc_func( $atts, $content, $tag ){

        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if($hasaccess){
            $type = isset($atts['type']) && !empty($atts['type']) ? esc_attr( $atts['type'] ) : esc_html__('link', 'armember-membership') ;
            $label = isset($atts['label']) && !empty($atts['label']) ? $atts['label'] : esc_html__('Logout', 'armember-membership').'?' ;
            $user_info = isset($atts['user_info']) && !empty($atts['user_info']) ? esc_attr( $atts['user_info'] ) : true ;
            $redirect_to = isset($atts['redirect_to']) && !empty($atts['redirect_to']) ? esc_url( $atts['redirect_to'] ) : '' ;
            $link_css = isset($atts['link_css']) && !empty($atts['link_css']) ? esc_attr( $atts['link_css'] ) : '' ;
            $link_hover_css = isset($atts['link_hover_css']) && !empty($atts['link_hover_css']) ? esc_attr( $atts['link_hover_css'] ) : '' ;

            return do_shortcode('[arm_logout type="'.$type.'" label="'.$label.'" user_info="'.$user_info.'" redirect_to="'.$redirect_to.'" link_css="'.$link_css.'" link_hover_css="'.$link_hover_css.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_setup() {
		global $wpdb, $arm_lite_version, $ARMemberLite, $arm_member_forms, $arm_subscription_plans;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $armFormList = array();
        $armFormList = array(
            __('Select Form', 'armember-membership') => '',
        );

        if (!empty($arm_forms)) {
            foreach ($arm_forms as $_form) {
                $armFormList[ strip_tags(stripslashes($_form['arm_form_label'])) . ' (ID: ' . $_form['arm_form_id'] . ')' ] =  $_form['arm_form_id'];
            }
        }

        $arm_hide_title = array(
            __('No', 'armember-membership') => 'false',
            __('Yes', 'armember-membership') => 'true',
        );
        
        $arm_hide_plans = array(
            __('No', 'armember-membership') => 0,
            __('Yes', 'armember-membership') => 1,
        );

        $arm_form_popup = array(
            __('Internal', 'armember-membership') => 'false',
            __('External', 'armember-membership') => 'true',
        );

        $arm_form_link_type = array(
            __('Link', 'armember-membership') => 'link',
            __('Button', 'armember-membership') => 'button',
        );
        
        $arm_form_overlay = array( '10' => '0.1',  '20' => '0.2',  '30' => '0.3',  '40' => '0.4',  '50' => '0.5',  '60' => '0.6',  '70' => '0.7',  '80' => '0.8',  '90' => '0.9', '100' => '1' );

        $setups = $wpdb->get_results("SELECT `arm_setup_id`, `arm_setup_name` FROM `" . $ARMemberLite->tbl_arm_membership_setup . "` "); //phpcs:ignore

        $arm_setuplist = array();
        $arm_setuplist = array(
                __('Select Setup', 'armember-membership') => '',
            );

        if (!empty($setups)){
            foreach ($setups as $ms){
                $arm_setuplist[ $ms->arm_setup_name ] = $ms->arm_setup_id;
            }
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Membership Setup Wizard', 'armember-membership'),
                'description' => '',
                'base' => 'arm_setup_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'group' => __( 'Membership Setup Wizard', 'armember-membership' ),
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select Setup', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'id',
                        'value' => $arm_setuplist,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Hide Setup Title', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'hide_title',
                        'value' => $arm_hide_title,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Default Selected Plan', 'armember-membership'),
                        'description' => __('Please enter plan id', 'armember-membership'),
                        'param_name' => 'subscription_plan',
                        'value' => '',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Hide Plan Selection Area', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'hide_plans',
                        'value' => $arm_hide_plans,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('How you want to include this form into page?', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup',
                        'value' => $arm_form_popup,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Link Type', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_type',
                        'value' => $arm_form_link_type,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Link Text', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_title',
                        'value' => 'Click here to open Form',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Background Overlay', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'overlay',
                        'value' => $arm_form_overlay,
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'colorpicker',
                        'class' => '',
                        'heading' => __('Background Color', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'modal_bgcolor',
                        'value' => '#000000',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Height', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup_height',
                        'value' => 'auto',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Width', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'popup_width',
                        'value' => '800',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'link_hover_css',
                        'value' => 'color: #000000;',
                        'group' => __( 'ARMember Membership Setup Wizard', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'popup',
                            'value' => 'true',
                        ),
                    ),
                )
            ));
        }
    }
    public function arm_setup_vc_func( $atts,$content,$tag ) {

        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if ($hasaccess) {
            $id = isset($atts['id']) && !empty($atts['id']) ? intval( $atts['id'] ) : '' ;
            $hide_title = isset($atts['hide_title']) && !empty($atts['hide_title']) ? esc_attr( $atts['hide_title'] ) : false ;
            $subscription_plan = isset($atts['subscription_plan']) && !empty($atts['subscription_plan']) ? esc_attr( $atts['subscription_plan'] ) : 0 ;
            $hide_plans = isset($atts['hide_plans']) && !empty($atts['hide_plans']) ? esc_attr( $atts['hide_plans'] ) : 0 ;
            $popup = isset($atts['popup']) && !empty($atts['popup']) ? esc_attr( $atts['popup'] ) : false ;
            $link_type = isset($atts['link_type']) && !empty($atts['link_type']) ? esc_attr( $atts['link_type'] ) : false ;
            $link_title = isset($atts['link_title']) && !empty($atts['link_title']) ? esc_attr( $atts['link_title']) : esc_attr__('Click here to open Set up form', 'armember-membership') ;
            $overlay = isset($atts['overlay']) && !empty($atts['overlay']) ? esc_attr( $atts['overlay'] ) : '0.6' ;
            $modal_bgcolor = isset($atts['modal_bgcolor']) && !empty($atts['modal_bgcolor']) ? esc_attr( $atts['modal_bgcolor'] ) : '#000000' ;
            $popup_height = isset($atts['popup_height']) && !empty($atts['popup_height']) ? esc_attr( $atts['popup_height'] ) : '' ;
            $popup_width = isset($atts['popup_width']) && !empty($atts['popup_width']) ? esc_attr( $atts['popup_width'] ) : '' ;
            $link_css = isset($atts['link_css']) && !empty($atts['link_css']) ? esc_attr( $atts['link_css'] ) : '' ;
            $link_hover_css = isset($atts['link_hover_css']) && !empty($atts['link_hover_css']) ? esc_attr( $atts['link_hover_css'] ) : '' ;

            return do_shortcode('[arm_setup id="'.$id.'" hide_title="'.$hide_title.'" subscription_plan="'.$subscription_plan.'" hide_plans="'.$hide_plans.'" popup="'.$popup.'" link_type="'.$link_type.'" link_title="'.$link_title.'" overlay="'.$overlay.'" modal_bgcolor="'.$modal_bgcolor.'" popup_height="'.$popup_height.'" popup_width="'.$popup_width.'" link_css="'.$link_css.'" link_hover_css="'.$link_hover_css.'"]');
        } else {
            return '';
        }

    }

    public function ARM_arm_member_transaction() {
		global $arm_lite_version, $ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $arm_form_display_invoice_button = array(
            __('Yes', 'armember-membership') => 'true',
            __('No', 'armember-membership') => 'false',
        );

        $armFormLabel = array(
            '&nbsp;'.__( 'Transaction ID', 'armember-membership') => 'transaction_id',
            '&nbsp;'.__( 'Invoice ID', 'armember-membership') => 'invoice_id',
            '&nbsp;'.__( 'Plan', 'armember-membership') => 'plan',
            '&nbsp;'.__( 'Payment Gateway', 'armember-membership') => 'payment_gateway',
            '&nbsp;'.__( 'Payment Type', 'armember-membership') => 'payment_type',
            '&nbsp;'.__( 'Transaction Status', 'armember-membership') => 'transaction_status',
            '&nbsp;'.__( 'Amount', 'armember-membership') => 'amount',
            '&nbsp;'.__( 'Used coupon Code', 'armember-membership') => 'used_coupon_code',
            '&nbsp;'.__( 'Used coupon Discount', 'armember-membership') => 'used_coupon_discount',
            '&nbsp;'.__( 'Payment Date', 'armember-membership') => 'payment_date',
        );

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Payment Transaction', 'armember-membership'),
                'description' => '',
                'base' => 'arm_member_transaction_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'transaction_id_label',
                        'value' => array(
                            __( 'Transaction ID', 'armember-membership') => 'transaction_id',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'transaction_id_value',
                        'value' => 'Transaction ID',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'invoice_id_label',
                        'value' => array(
                            __( 'Invoice ID', 'armember-membership') => 'invoice_id',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'invoice_id_value',
                        'value' => 'Invoice ID',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'plan_label',
                        'value' => array(
                            __( 'Plan', 'armember-membership') => 'plan',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'plan_value',
                        'value' => 'Plan',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'payment_gateway_label',
                        'value' => array(
                            __( 'Payment Gateway', 'armember-membership') => 'payment_gateway',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'payment_gateway_value',
                        'value' => 'Payment Gateway',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'transaction_status_label',
                        'value' => array(
                            __( 'Transaction Status', 'armember-membership') => 'transaction_status',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'transaction_status_value',
                        'value' => 'Transaction Status',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'amount_label',
                        'value' => array(
                            __( 'Amount', 'armember-membership') => 'amount',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'amount_value',
                        'value' => 'Amount',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'used_coupon_code_label',
                        'value' => array(
                            __( 'Used coupon Code', 'armember-membership') => 'used_coupon_code',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'used_coupon_code_value',
                        'value' => 'Used coupon Code',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'used_coupon_discount_label',
                        'value' => array(
                            __( 'Used coupon Discount', 'armember-membership') => 'used_coupon_discount',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'used_coupon_discount_value',
                        'value' => 'Used coupon Discount',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Transaction History', 'armember-membership'),
                        'param_name' => 'payment_date_label',
                        'value' => array(
                            __( 'Payment Date', 'armember-membership') => 'payment_date',
                        ),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'param_name' => 'payment_date_value',
                        'value' => 'Payment Date',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Display View Invoice Button', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'display_invoice_button',
                        'value' => $arm_form_display_invoice_button,
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('View Invoice Text', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'view_invoice_text',
                        'value' => __('View Invoice','armember-membership'),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button CSS', 'armember-membership'),
                        'description' => '&nbsp;e.g. color: #ffffff;',
                        'param_name' => 'view_invoice_css',
                        'value' => '',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;e.g. color: #ffffff;',
                        'param_name' => 'view_invoice_hover_css',
                        'value' => '',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'title',
                        'value' => __('Transactions','armember-membership'),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),                   
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Records per Page', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'per_page',
                        'value' => '5',
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('No Records Message', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'message_no_record',
                        'value' => __('There is no any Transactions found', 'armember-membership'),
                        'group' => __( 'ARMember Payment Transaction', 'armember-membership' ),
                    ),
                )
            ));
        }
    }
    public function arm_member_transaction_vc_func( $atts,$content,$tag ) {
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if ($hasaccess) {
            $default_fields = $this->get_shortcode_default_field( $atts, $tag);
    
            $default_field_labels = !empty(implode(",",$default_fields['default_label_field'])) ? implode(",",$default_fields['default_label_field']) : 'transaction_id,invoice_id,plan,payment_gateway,payment_type,transaction_status,amount,used_coupon_code,used_coupon_discount,payment_date' ;
            $default_field_values = !empty(implode(",",$default_fields['default_value_field'])) ? implode(",",$default_fields['default_value_field']) : esc_html__('Transaction ID', 'armember-membership') . ',' . esc_html__('Invoice ID', 'armember-membership') . ',' . esc_html__('Plan', 'armember-membership') . ',' . esc_html__('Payment Gateway', 'armember-membership') . ',' . esc_html__('Payment Type', 'armember-membership') . ',' . esc_html__('Transaction Status', 'armember-membership') . ',' . esc_html__('Amount', 'armember-membership') . ',' . esc_html__('Used Coupon Code', 'armember-membership') . ',' . esc_html__('Used Coupon Discount', 'armember-membership') . ',' . esc_html__('Payment Date', 'armember-membership') . ',' . esc_html__('TAX Percentage', 'armember-membership') . ',' . esc_html__('TAX Amount', 'armember-membership') ;
            $display_invoice_button = isset($atts['display_invoice_button']) && !empty($atts['display_invoice_button']) ? $atts['display_invoice_button'] : 'true' ;
            $view_invoice_text = isset($atts['view_invoice_text']) && !empty($atts['view_invoice_text']) ? $atts['view_invoice_text'] : esc_html__('View Invoice', 'armember-membership') ;
            $view_invoice_css = isset($atts['view_invoice_css']) && !empty($atts['view_invoice_css']) ? $atts['view_invoice_css'] : esc_html__('View Invoice', 'armember-membership') ;
            $view_invoice_hover_css = isset($atts['view_invoice_hover_css']) && !empty($atts['view_invoice_hover_css']) ? $atts['view_invoice_hover_css'] : esc_html__('View Invoice', 'armember-membership') ;
            $title = isset($atts['title']) && !empty($atts['title']) ? $atts['title'] : esc_html__('Transactions', 'armember-membership') ;
            $per_page = isset($atts['per_page']) && !empty($atts['per_page']) ? $atts['per_page'] : 5 ;
            $message_no_record = isset($atts['message_no_record']) && !empty($atts['message_no_record']) ? $atts['message_no_record'] : esc_html__('There is no any Transactions found', 'armember-membership') ;

            return do_shortcode('[arm_member_transaction label="'.$default_field_labels.'" value="'.$default_field_values.'" display_invoice_button="'.$display_invoice_button.'" view_invoice_text="'.$view_invoice_text.'" view_invoice_css="'.$view_invoice_css.'" view_invoice_hover_css="'.$view_invoice_hover_css.'" title="'.$title.'" per_page="'.$per_page.'" message_no_record="'.$message_no_record.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_account_detail() {
        global $arm_lite_version,$ARMemberLite, $arm_member_forms, $arm_social_feature;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $armSocialField = array();
        if( $arm_social_feature->isSocialFeature ) {
            $socialProfileFields = $arm_member_forms->arm_social_profile_field_types();
            if (!empty($socialProfileFields)) {
                foreach ($socialProfileFields as $spfKey => $spfLabel) {
                    $armSocialField[ esc_attr($spfLabel) ] = esc_attr($spfKey);
                }
            }
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember My Profile', 'armember-membership'),
                'description' => '',
                'base' => 'arm_account_detail_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'first_name_label',
                        'value' => array(
                            '&nbsp;'.__( 'First Name', 'armember-membership') => 'first_name',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'first_name_value',
                        'value' => 'First Name',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'last_name_label',
                        'value' => array(
                            '&nbsp;'.__( 'Last Name', 'armember-membership') => 'last_name',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'last_name_value',
                        'value' => 'Last Name',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'display_name_label',
                        'value' => array(
                            '&nbsp;'.__( 'Display Name', 'armember-membership') => 'display_name',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'display_name_value',
                        'value' => 'Display Name',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'user_login_label',
                        'value' => array(
                            '&nbsp;'.__( 'Username', 'armember-membership') => 'user_login',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'user_login_value',
                        'value' => 'Username',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'user_email_label',
                        'value' => array(
                            '&nbsp;'.__( 'Email Address', 'armember-membership') => 'user_email',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'user_email_value',
                        'value' => 'Email Address',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'gender_label',
                        'value' => array(
                            '&nbsp;'.__( 'Gender', 'armember-membership') => 'gender',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'gender_value',
                        'value' => 'Gender',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'user_url_label',
                        'value' => array(
                            '&nbsp;'.__( 'Website (URL)', 'armember-membership') => 'user_url',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'user_url_value',
                        'value' => 'Website (URL)',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'country_label',
                        'value' => array(
                            '&nbsp;'.__( 'Country/Region', 'armember-membership') => 'country',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'country_value',
                        'value' => 'Country/Region',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Profile Fields', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'description_label',
                        'value' => array(
                            '&nbsp;'.__( 'Biography', 'armember-membership') => 'description',
                        ),
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'description_value',
                        'value' => 'Biography',
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'hidden',
                        'class' => '',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'is_social_fields',
                        'value' => $arm_social_feature->isSocialFeature,
                        'group' => __( 'ARMember My Profile', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => '',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'social_fields',
                        'value' => $armSocialField,
                        'group' => __( 'ARMember My Profile', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'is_social_fields',
                            'value' => 'true',
                        ),                        
                    ),
                )
            ));
        }
    }
    public function arm_account_detail_vc_func( $atts, $content, $tag ) {
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if ($hasaccess) {
            $default_fields = $this->get_shortcode_default_field( $atts, $tag);
    
            $default_field_labels = !empty(implode(",",$default_fields['default_label_field'])) ? implode(",",$default_fields['default_label_field']) : 'first_name,last_name,user_login,user_email' ;
            $default_field_values = !empty(implode(",",$default_fields['default_value_field'])) ? implode(",",$default_fields['default_value_field']) : 'First Name,Last Name,Username,Email' ;
            $social_fields = isset($atts['social_fields']) && !empty($atts['social_fields']) ? esc_attr( $atts['social_fields'] ) : '' ;

            return do_shortcode('[arm_account_detail label="'.$default_field_labels.'" value="'.$default_field_values.'" social_fields="'.$social_fields.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_close_account() {
		global $wpdb, $arm_lite_version, $ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $setups = $wpdb->get_results("SELECT `arm_setup_id`, `arm_setup_name` FROM `" . $ARMemberLite->tbl_arm_membership_setup . "` "); //phpcs:ignore

        $arm_setuplist = array();
        $arm_setuplist = array(
                __('Select Setup', 'armember-membership') => '',
            );

        if (!empty($setups)){
            foreach ($setups as $ms){
                $arm_setuplist[ $ms->arm_setup_name ] = $ms->arm_setup_id;
            }
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Close Account', 'armember-membership'),
                'description' => '',
                'base' => 'arm_close_account_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select set of login form','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'set_id',
                        'value' => $arm_setuplist,
                        'group' => __( 'ARMember Close Account', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Link Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'css',
                        'value' => '',
                        'group' => __( 'ARMember Close Account', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'set_id',
                            'not_empty' => true,
                        ),
                    ),
                )
            ));
        }
    }
    public function arm_close_account_vc_func( $atts, $content, $tag ) {
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if($hasaccess){
            $set_id = isset($atts['sat_id']) && !empty($atts['sat_id']) ? esc_attr( $atts['sat_id'] ) : '' ;
            $css = isset($atts['css']) && !empty($atts['css']) ? esc_attr( $atts['css'] ) : '' ;

            return do_shortcode('[arm_close_account set_id="'.$set_id.'" css="'.$css.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_membership(){
		global $wpdb, $arm_lite_version, $ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $setups = $wpdb->get_results("SELECT `arm_setup_id`, `arm_setup_name` FROM `" . $ARMemberLite->tbl_arm_membership_setup . "` "); //phpcs:ignore

        $arm_setuplist = array();
        $arm_setuplist = array(
                __('Select Setup', 'armember-membership') => '',
            );

        if (!empty($setups)){
            foreach ($setups as $ms){
                $arm_setuplist[ $ms->arm_setup_name ] = $ms->arm_setup_id;
            }
        }

        $arm_display_yes_no_option = array(
            __('No', 'armember-membership') => 'false',
            __('Yes', 'armember-membership') => 'true',
        );

        $armFormMembershipLabel = array(
            '&nbsp;'.__( 'No.', 'armember-membership') => 'current_membership_no',
            '&nbsp;'.__( 'Membership Plan', 'armember-membership') => 'current_membership_is',
            '&nbsp;'.__( 'Plan Type', 'armember-membership') => 'current_membership_recurring_profile',
            '&nbsp;'.__( 'Starts On', 'armember-membership') => 'current_membership_started_on',
            '&nbsp;'.__( 'Expires On', 'armember-membership') => 'current_membership_expired_on',
            '&nbsp;'.__( 'Cycle Date', 'armember-membership') => 'current_membership_next_billing_date',
            '&nbsp;'.__( 'Action', 'armember-membership') => 'action_button',
        );

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Current Membership', 'armember-membership'),
                'description' => '',
                'base' => 'arm_membership_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Title','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'title',
                        'value' => 'Current Membership',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select Setup', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'setup_id',
                        'value' => $arm_setuplist,
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_no_label',
                        'value' => array(
                            __( 'No.', 'armember-membership') => 'current_membership_no',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => false,
                        'param_name' => 'current_membership_no_value',
                        'value' => 'No.',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_is_label',
                        'value' => array(
                            __( 'Membership Plan', 'armember-membership') => 'current_membership_is',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'current_membership_is_value',
                        'value' => 'Membership Plan',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_recurring_profile_label',
                        'value' => array(
                            __( 'Plan Type', 'armember-membership') => 'current_membership_recurring_profile',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'current_membership_recurring_profile_value',
                        'value' => 'Plan Type',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_started_on_label',
                        'value' => array(
                            __( 'Starts On', 'armember-membership') => 'current_membership_started_on',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'current_membership_started_on_value',
                        'value' => 'Starts On',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_expired_on_label',
                        'value' => array(
                            __( 'Expires On', 'armember-membership') => 'current_membership_expired_on',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'current_membership_expired_on_value',
                        'value' => 'Expires On',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'current_membership_next_billing_date_label',
                        'value' => array(
                            __( 'Cycle Date', 'armember-membership') => 'current_membership_next_billing_date',
                        ),
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'current_membership_next_billing_date_value',
                        'value' => 'Cycle Date',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'checkbox',
                        'class' => 'arm_element_checkbox',
                        'heading' => __('Current Membership', 'armember-membership'),
                        'description' => false,
                        'param_name' => 'action_button_label',
                        'value' => array(
                            __( 'Action', 'armember-membership') => 'action_button',
                        ),
                        'group' =>__( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => 'arm_element_textfield',
                        'holder' => 'div',
                        'heading' => false,
                        'description' => '&nbsp;',
                        'param_name' => 'action_button_value',
                        'value' => 'Action',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Display Renew Subscription Button', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'display_renew_button',
                        'value' => $arm_display_yes_no_option,
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Button Text','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'renew_text',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_renew_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Make Payment Text','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'make_payment_text',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_renew_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'renew_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_renew_button',
                            'value' => 'true',
                        ),
                    ), 
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'renew_hover_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_renew_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Display Cancel Subscription Button', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'display_cancel_button',
                        'value' => $arm_display_yes_no_option,
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Button Text','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'cancel_text',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_cancel_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'cancel_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_cancel_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'cancel_hover_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_cancel_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Subscription Cancelled Message','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'cancel_message',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_cancel_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Display Update Card Subscription Button?', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'display_update_card_button',
                        'value' => $arm_display_yes_no_option,
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Button Text','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'update_card_text',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_update_card_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'update_card_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_update_card_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textarea',
                        'class' => '',
                        'heading' => __('Button Hover CSS', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'update_card_hover_css',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                        'dependency' => array(
                            'element' => 'display_update_card_button',
                            'value' => 'true',
                        ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Trial Active Label','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'trial_active',
                        'value' => '',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('No Records Message','armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'message_no_record',
                        'value' => 'There is no membership found.',
                        'group' => __( 'ARMember Current Membership', 'armember-membership' ),
                    ),
                )
            ));
        }
    }
    public function arm_membership_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if ($hasaccess) {
            $default_fields = $this->get_shortcode_default_field( $atts, $tag);
    
            $default_field_labels = !empty(implode(",",$default_fields['default_label_field'])) ? implode(",",$default_fields['default_label_field']) : 'current_membership_no,current_membership_is,current_membership_recurring_profile,current_membership_started_on,current_membership_expired_on,current_membership_next_billing_date,action_button' ;
            $default_field_values = !empty(implode(",",$default_fields['default_value_field'])) ? implode(",",$default_fields['default_value_field']) : 'No.,Membership Plan,Plan Type,Starts On,Expires On,Cycle Date,Action' ;
            $title = isset($atts['title']) && !empty($atts['title'])? esc_attr( $atts['title']) : esc_attr__('Current Membership', 'armember-membership') ;
            $setup_id =  isset($atts['setup_id']) && !empty($atts['setup_id']) ? esc_attr( $atts['setup_id'] ) : '' ;
            $display_renew_button = isset($atts['display_renew_button']) && !empty($atts['display_renwe_button']) ? esc_attr( $atts['display_renew_button'] ) : 'true' ;
            $renew_text = isset($atts['renew_text']) && !empty($atts['renew_text']) ? esc_attr( $atts['renew_text']) : esc_attr__('Renew', 'armember-membership') ; 
            $make_payment_text = isset($atts['make_payment_text']) && !empty($atts['make_payment_text']) ? esc_attr( $atts['make_payment_text']) : esc_attr__('Make Payment', 'armember-membership') ;
            $renew_css = isset($atts['renew_css']) && !empty($atts['renew_css']) ? esc_attr( $atts['renew_css'] ) : '' ;
            $renew_hover_css = isset($atts['renew_hover_css']) && !empty($atts['renew_hover_css']) ? esc_attr( $atts['renew_hover_css'] ) : '' ;
            $display_cancel_button = isset($atts['display_cancel_button']) && !empty($atts['display_cancel_button']) ? esc_attr( $atts['display_cancel_button'] ) : 'true' ;
            $cancel_text = isset($atts['cancel_text']) && !empty($atts['cancel_text']) ? esc_attr( $atts['cancel_text']) : esc_attr__('Cancel', 'armember-membership') ;
            $cancel_css = isset($atts['cancel_css']) && !empty($atts['cancel_css']) ? esc_attr( $atts['cancel_css'] ) : '' ;
            $cancel_hover_css = isset($atts['cancel_hover_css']) && !empty($atts['cancel_hover_css']) ? esc_attr( $atts['cancel_hover_css'] ) : '' ;
            $cancel_message = isset($atts['cancel_message']) && !empty($atts['cancel_message']) ? esc_attr( $atts['cancel_message']) : esc_attr__('Your Subscription has been cancelled.', 'armember-membership') ; 
            $display_update_card_button = isset($atts['display_update_card_button']) && !empty($atts['display_update_card_button']) ? esc_attr( $atts['display_update_card_button'] ) : 'true' ;  
            $update_card_text = isset($atts['update_card_text']) && !empty($atts['update_card_text']) ? esc_attr( $atts['update_card_text']) : esc_attr__('Update Card', 'armember-membership') ;
            $update_card_css = isset($atts['update_card_css']) && !empty($atts['update_card_css']) ? esc_attr( $atts['update_card_css'] ) : '' ;
            $update_card_hover_css = isset($atts['update_card_hover_css']) && !empty($atts['update_card_hover_css']) ? esc_attr( $atts['update_card_hover_css'] ) : '' ;
            $trial_active = isset($atts['trial_active']) && !empty($atts['trial_active']) ? esc_attr( $atts['trial_active'] ) : esc_attr__('trial active', 'armember-membership') ;
            $message_no_record = isset($atts['message_no_record']) && !empty($atts['message_no_record']) ? esc_attr( $atts['message_no_record'], 'armember-membership' ) : esc_attr__('There is no membership found.', 'armember-membership') ;

            return do_shortcode('[arm_membership title="'.$title.'" setup_id="'.$setup_id.'" membership_label="'.$default_field_labels.'" membership_value="'.$default_field_values.'" display_renew_button="'.$display_renew_button.'" renew_text="'.$renew_text.'" renew_css="'.$renew_css.'" renew_hover_css="'.$renew_hover_css.'" make_payment_text="'.$make_payment_text.'" display_cancel_button="'.$display_cancel_button.'" cancel_text="'.$cancel_text.'" cancel_css="'.$cancel_css.'" cancel_hover_css="'.$cancel_hover_css.'" cancel_message="'.$cancel_message.'" display_update_card_button="'.$display_update_card_button.'" update_card_text="'.$update_card_text.'" update_card_css="'.$update_card_css.'" update_card_hover_css="'.$update_card_hover_css.'" trial_active="'.$trial_active.'" message_no_record="'.$message_no_record.'"]');
        } else {
            return '';
        }

    }

    public function ARM_arm_username(){
		global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $user_name = '';
        if (is_user_logged_in()) {
            $user_id = get_current_user_id();
            $user_data = wp_get_current_user($user_id);
            $user_name = $user_data->data->user_login;
        }
        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember Username', 'armember-membership'),
                'description' => '',
                'base' => 'arm_username_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'group' => __( 'ARMember Username', 'armember-membership' ),
                'params' => array()
            ));
        }
    }
    public function arm_username_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if ($hasaccess) {
            return do_shortcode('[arm_username]');
        } else {
            return '';
        }
    }

    public function ARM_arm_user_plan(){
		global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Plan', 'armember-membership'),
                'description' => '',
                'base' => 'arm_user_plan_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'group' => __( 'ARMember User Plan', 'armember-membership' ),
                'params' => array()
            ));
        }
    }
    public function arm_user_plan_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if ($hasaccess) {
            return do_shortcode('[arm_user_plan]');
        } else {
            return '';
        }
    }

    public function ARM_arm_displayname(){
		global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Displayname', 'armember-membership'),
                'description' => '',
                'base' => 'arm_displayname_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'group' => __( 'ARMember User Displayname', 'armember-membership' ),
                'params' => array()
            ));
        }
    }
    public function arm_displayname_vc_func( $atts, $content, $tag){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if ($hasaccess) {
            return do_shortcode('[arm_displayname]');
        } else {
            return '';
        }
    }

    public function ARM_arm_firstname_lastname(){
		global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Firstname Lastname', 'armember-membership'),
                'description' => '',
                'base' => 'arm_firstname_lastname_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array()
            ));
        }
    }
    public function arm_firstname_lastname_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if ($hasaccess) {
            return do_shortcode('[arm_firstname_lastname]');
        } else {
            return '';
        }
    }

    public function ARM_arm_avatar(){
		global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Avatar', 'armember-membership'),
                'description' => '',
                'base' => 'arm_avatar_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array()
            ));
        }
    }
    public function arm_avatar_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        if ($hasaccess) {
            return do_shortcode('[arm_avatar]');
        } else {
            return '';
        }
        
    }

    public function ARM_arm_usermeta(){
        global $arm_lite_version,$ARMemberLite;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Custom Meta', 'armember-membership'),
                'description' => '',
                'base' => 'arm_usermeta_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'textfield',
                        'class' => '',
                        'heading' => __('Enter Usermeta Name', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'meta',
                        'value' => '',
                        'group' => __( 'ARMember User Custom Meta', 'armember-membership' )
                    ),
                )
            ));
        }
    }
    public function arm_usermeta_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );

        $meta = isset($atts['meta']) && !empty($atts['meta']) ? esc_attr( $atts['meta'] ) : '' ;

        if ($hasaccess) {
            return do_shortcode('[arm_usermeta meta="'.$meta.'"]');
        } else {
            return '';
        }
    }

    public function ARM_arm_user_planinfo(){
		global $arm_lite_version, $ARMemberLite, $arm_subscription_plans;

        if (!$this->isWPBakryBuilderRestrictionFeature) {
            return;
        }

        $arm_planlist = array();
        $arm_planlist = array(
            __('Select Plan', 'armember-membership') => '',
        );
        $all_plans = $arm_subscription_plans->arm_get_all_subscription_plans('arm_subscription_plan_id, arm_subscription_plan_name');
        if(!empty($all_plans)){
            foreach($all_plans as $plan){
                $arm_planlist[ stripslashes($plan['arm_subscription_plan_name']) ] = $plan['arm_subscription_plan_id'];
            }
        }

        $armPlanInfo = array();
        $armPlanInfo = array(
            __('Start Date', 'armember-membership') => 'arm_start_plan',
            __('End Date', 'armember-membership') => 'arm_expire_plan',
            __('Plan Amount', 'armember-membership') => 'arm_amount_plan',
            __('Trial Start Date', 'armember-membership') => 'arm_trial_start',
            __('Trial End Date', 'armember-membership') => 'arm_trial_end',
            __('Grace End Date', 'armember-membership') => 'arm_grace_period_end',
            __('Paid By', 'armember-membership') => 'arm_user_gateway',
            __('Completed Recurrence', 'armember-membership') => 'arm_completed_recurring',
            __('Next Due Date', 'armember-membership') => 'arm_next_due_payment',
            __('Payment Mode', 'armember-membership') => 'arm_payment_mode',
            __('Payment Cycle', 'armember-membership') => 'arm_payment_cycle',
        );


        if (function_exists('vc_map')) {
            vc_map(array(
                'name' => esc_html__('ARMember User Plan Information', 'armember-membership'),
                'description' => '',
                'base' => 'arm_user_planinfo_vc',
                'category' => esc_html__('armember-membership', 'armember-membership'),
                'class' => '',
                'controls' => 'full',
                'icon' => 'arm_vc_icon',
                'admin_enqueue_css' => array(MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css'),
                'front_enqueue_css' => MEMBERSHIPLITE_URL . '/core/vc/arm_vc.css',
                'params' => array(
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select Plan', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'plan_id',
                        'value' => $arm_planlist,
                        'group' => __( 'ARMember User Plan Information', 'armember-membership' )
                    ),
                    array(
                        'type' => 'dropdown',
                        'class' => 'arm_element_dropdown',
                        'heading' => __('Select Plan Information', 'armember-membership'),
                        'description' => '&nbsp;',
                        'param_name' => 'plan_info',
                        'value' => $armPlanInfo,
                        'group' => __( 'ARMember User Plan Information', 'armember-membership' )
                    ),
                )
            ));
        }
    }
    public function arm_user_planinfo_vc_func( $atts, $content, $tag ){
        $hasaccess = $this->chack_shortcode_hasaccess( $atts );
        
        if ($hasaccess) {
            $plan_id = isset($atts['plan_id']) && !empty($atts['plan_id']) ? intval( $atts['plan_id'] ) : '' ;
            $plan_info = isset($atts['plan_info']) && !empty($atts['plan_info']) ? esc_attr( $atts['plan_info'] ) : '' ;

            return do_shortcode('[arm_user_planinfo plan_id="'.$plan_id.'" plan_info="'.$plan_info.'"]');
        } else {
            return '';
        }
    }
    
    public function chack_shortcode_hasaccess( $atts) {
        if (current_user_can('administrator')) {
            return true;
        }

        if(isset($armember_restriction_access) && !$armember_restriction_access) {
            return true;
        }

        if(isset($atts['armember_restriction_access']) && $atts['armember_restriction_access'] == 'no') {
            return true;
        }

        $arm_membership_plans = isset($atts['armember_membership_plans']) && !empty($atts['armember_membership_plans']) ? explode(",", $atts['armember_membership_plans']) : array();
        $arm_restriction_type = isset($atts['armember_access_type']) && !empty($atts['armember_access_type']) ? $atts['armember_access_type'] : '';

        global $arm_restriction;
        $hasaccess = $arm_restriction->arm_check_content_hasaccess( $arm_membership_plans, $arm_restriction_type );

        return $hasaccess;
    }

    public function get_shortcode_default_field( $atts, $tag) {
        $default_arm_member_transaction_vc_fields = array(
            'transaction_id' => 'Transaction ID', 
            'invoice_id' => 'Invoice ID', 
            'plan' => 'Plan', 
            'payment_gateway' => 'Payment Gateway', 
            'payment_type' => 'Payment Type', 
            'transaction_status' => 'Transaction Status', 
            'amount' => 'Amount', 
            'used_coupon_code' => 'Used Coupon Code', 
            'used_coupon_discount' => 'Used Coupon Discount', 
            'payment_date' => 'Payment Date', 
        );

        $default_arm_account_detail_vc_fields = array(
            'first_name' => 'First Name', 
            'last_name' => 'Last Name', 
            'display_name' => 'Display Name', 
            'user_login' => 'Username', 
            'user_email' => 'Email Address', 
            'gender' => 'Gender', 
            'user_url' => 'Website (URL)', 
            'country' => 'Country/Region', 
            'description' => 'Biography', 
        );

        $default_arm_membership_vc_fields = array(
            'current_membership_no' => __('No.', 'armember-membership'),
            'current_membership_is' => __('Membership Plan', 'armember-membership'),
            'current_membership_recurring_profile' => __('Plan Type', 'armember-membership'),
            'current_membership_started_on' => __( 'Starts On', 'armember-membership'),
            'current_membership_expired_on' => __( 'Expires On', 'armember-membership'),
            'current_membership_next_billing_date' => __( 'Cycle Date', 'armember-membership'),
            'action_button' => __( 'Action', 'armember-membership'),
        );

        $return_shortcode_fields = array();
        $return_shortcode_fields['default_label_field'] = array();
        $return_shortcode_fields['default_value_field'] = array();
        foreach (${'default_'.$tag.'_fields'} as $f_key => $f_value) {
            if (isset($atts[$f_key.'_label']) && !empty($atts[$f_key.'_label'])) {
                $return_shortcode_fields['default_label_field'][] = isset($atts[$f_key.'_label']) && !empty($atts[$f_key.'_label']) ? $atts[$f_key.'_label'] : $f_key;
                $field_value = isset($atts[$f_key.'_value']) && !empty($atts[$f_key.'_value']) ? $atts[$f_key.'_value'] : $f_value ;
                $return_shortcode_fields['default_value_field'][] = $field_value;
            }
        }

        return $return_shortcode_fields;
    }
}?>
