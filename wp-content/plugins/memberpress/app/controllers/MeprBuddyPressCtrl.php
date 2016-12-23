<?php if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MeprBuddyPressCtrl extends MeprBaseCtrl {
  public $enabled_str             = 'mepr_buddypress_enabled';
  public $default_membership_str  = 'mepr_buddypress_default_membership';
  public $default_groups_str      = 'mepr_buddypress_default_groups';

  public function load_hooks() {
/*     include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    if(is_plugin_active('buddypress/bp-loader.php')) {
      //MP Options tab
      add_action('mepr_display_options_tabs',         array($this, 'display_option_tab'));
      add_action('mepr_display_options',              array($this, 'display_option_fields'));
      add_action('mepr-process-options',              array($this, 'store_option_fields'));
      add_action('mepr-options-admin-enqueue-script', array($this, 'enqueue_options_page_scripts'));

      //BP Nav Items & Account page
      add_action('bp_setup_nav',                      array($this, 'setup_bp_nav'));
      add_filter('mepr-account-page-permalink',       array($this, 'change_account_page_url'));
      add_action('template_redirect',                 array($this, 'catch_account_page_and_redirect'));

      //BP's signup form hook
      add_action('bp_core_signup_user',               array($this, 'capture_bp_signups'), 11, 5);

      //Sync BP Groups w/MP
        //Need to capture event hooks
        //Need to capture txn transition status != complete to == complete
        //Need to show per Membership Groups selector
        //Need to save per Membership Groups selector
        //Need to enqueue the JS for per Membership Groups selector checkbox toggle shnizzle (see MailChimp addon for how-to)

      //Auto activate BP users when they signup via MemberPress
      add_action('mepr-signup',                       array($this, 'activate_bp_profile'));
    } */
  }

//MP OPTIONS PAGE
  public function display_option_tab() {
    ?>
      <a class="nav-tab" id="buddypress" href="#"><?php _e('BuddyPress', 'memberpress'); ?></a>
    <?php
  }

  public function display_option_fields() {
    $enabled            = get_option($this->enabled_str, 0);
    $default_membership = get_option($this->default_membership_str, 0);
    $default_groups     = maybe_unserialize(get_option($this->default_groups_str, array()));

    $memberships        = get_posts(array('numberposts' => -1, 'post_type' => MeprProduct::$cpt, 'post_status' => 'publish'));
    $groups             = (bp_is_active('groups'))?BP_Groups_Group::get(array('type'=>'alphabetical', 'per_page' => 9999)):false;
    ?>
      <div id="buddypress" class="mepr-options-hidden-pane">
        <h3><?php _e('BuddyPress Integration', 'memberpress'); ?></h3>

        <input type="checkbox" id="mepr_bp_enabled" name="mepr_bp_enabled" <?php checked($enabled); ?> />
        <label for="mepr_bp_enabled" style="vertical-align:top;"><?php _e('Enable BuddyPress Integration', 'memberpress'); ?></label>

        <div id="mepr_bp_options_area" class="mepr-hidden mepr-sub-box-white" style="margin-top:20px;">
          <div class="mepr-arrow mepr-white mepr-up mepr-sub-box-arrow"> </div>

          <?php if(!get_option('users_can_register') && !bp_is_active('groups')): ?>
            <p><?php _e('BuddyPress Integration is activated. For further integration options - enable BuddyPress Groups.', 'memberpress'); ?></p>
          <?php endif; ?>

          <?php if(get_option('users_can_register')): ?>
            <label for="mepr_bp_default_free_membership"><?php _e('Default Free Membership', 'memberpress'); ?>:</label>
            <br/>
            <select id="mepr_bp_default_free_membership" name="mepr_bp_default_free_membership">
              <option value="none"><?php _e('None', 'memberpress'); ?></option>
              <?php foreach($memberships as $m): ?>
                <option value="<?php echo $m->ID; ?>" <?php selected($default_membership, $m->ID); ?>><?php echo $m->post_title; ?></option>
              <?php endforeach; ?>
            </select>
            <br/>
            <small><?php _e("If the user signs up via BuddyPress's signup page, then no payment can be collected. Therefore the member will get lifetime free access to the default Membership you choose here. If you need to charge your users, then we recommend that you disable signups via BuddyPress and instead force the users to signup via MemberPress instead.", 'memberpress'); ?></small>
          <?php endif; //Users can register ?>
          <?php if(bp_is_active('groups') && $groups['total']): ?>
            <?php if(get_option('users_can_register')): //Show a spacer ?>
              <br/>
              <br/>
            <?php endif; ?>
            <label for="mepr_bp_default_groups"><?php _e('Default Group(s) for ALL Members', 'memberpress'); ?>:</label>
            <br/>
            <select id="mepr_bp_default_groups" name="mepr_bp_default_groups[]" multiple="multiple" style="width:98%;height:150px;">
              <?php foreach($groups['groups'] as $g): ?>
                <option value="<?php echo $g->id; ?>" <?php selected(in_array($g->id, $default_groups, false)); ?>><?php echo $g->name; ?></option>
              <?php endforeach; ?>
            </select>
            <br/>
            <small><?php _e("Hold the Control Key (Command Key on the Mac) in order to select or deselect multiple groups.", 'memberpress'); ?><br/><?php _e("Select a default BuddyPress Group(s) that every member should be added to when signing up. Please note, ALL members are added to this group whether they're active and paid or not. Please see the per-Membership Groups if you want to add/remove members to/from Groups automatically based on their status.", 'memberpress'); ?></small>
          <?php endif; //!empty groups ?>
        </div>
      </div>
    <?php
  }

  public function store_option_fields() {
    update_option($this->enabled_str, (isset($_POST['mepr_bp_enabled'])));
    update_option($this->default_membership_str, (int)$_POST['mepr_bp_default_free_membership']);
    update_option($this->default_groups_str, (!empty($_POST['mepr_bp_default_groups']))?(array)$_POST['mepr_bp_default_groups']:array());
  }

  public function enqueue_options_page_scripts($hook) {
    wp_enqueue_script('mepr-buddypress-options-js', MEPR_JS_URL.'/admin_buddypress_options.js', array('jquery'), MEPR_VERSION);
  }

//BP's Signup Form Capture
  public function capture_bp_signups($user_id, $user_login, $user_password, $user_email, $usermeta) {
    $enabled            = get_option($this->enabled_str, 0);
    $default_membership = get_option($this->default_membership_str, 0);
    $default_groups     = maybe_unserialize(get_option($this->default_groups_str, array()));

    if(!$enabled || (empty($default_membership) && empty($default_groups))) { return; }

    //Default Membership Handling
    if($default_membership) {
      $user = new MeprUser($user_id);
      $active_subs = $user->active_product_subscriptions('ids');
      $active_subs = (empty($active_subs))?array():$active_subs;

      if(!in_array($default_membership, $active_subs)) {
        $txn = new MeprTransaction();
        $txn->trans_num   = 'bp-'.uniqid();
        $txn->product_id  = $default_membership;
        $txn->status      = MeprTransaction::$complete_str;
        $txn->txn_type    = MeprTransaction::$payment_str;
        $txn->amount      = 0.00;
        $txn->created_at  = gmdate('c');
        $txn->expires_at  = MeprUtils::mysql_lifetime();
        $txn->user_id     = $user_id;
        $txn->gateway     = 'free';
        $txn->store();
      }
    }

    //Default Groups handling
    if(bp_is_active('groups') && !empty($default_groups)) {
      foreach($default_groups as $g_id) {
        groups_join_group($g_id, $user_id);
      }
    }
  }

//Sync BP Groups w/MP
  public function update_groups_txn_wrapper($old_status, $new_status, $txn) {
    if($new_status == MeprTransaction::$complete_str && $old_status != MeprTransaction::$complete_str) {
      $this->update_groups($txn, true);
    }
  }

  public function update_groups($event_or_obj, $is_obj = false) {
    $enabled = get_option($this->enabled_str, 0);

    if(!$enabled || !bp_is_active('groups')) { return; }

    //$is_obj - should we decide to use other hooks here like mepr-txn-store
    if(!$is_obj) {
      $obj = $event_or_obj->get_data(); //$event_or_obj is of type MeprEvent
    }
    else {
      $obj = $event_or_obj; //$event_or_obj is most likely a MeprTransaction or MeprSubscription
    }

    if(!($obj instanceof MeprTransaction) && !($obj instanceof MeprSubscription)) {
      return; // nothing here to do if we're not dealing with a txn or sub
    }

    $member = $obj->user(true);

    if($member->is_active_on_membership($obj)) {
      // $this->add_group($obj);
    }
    else {
      // $this->remove_group($obj);
    }
  }

  // public function add_group($obj) {
    // $enabled = get_option($this->enabled_str, 0);

    // if(!$enabled || !bp_is_active('groups')) { return; }

    // $membership_groups = get_post_meta();
    // groups_join_group($g_id, $obj->user_id);
  // }

  // public function remove_group() {

  // }

//BP NAV AND ACCOUNT
  //Override Mepr Account page URL
  public function change_account_page_url($url) {
    global $bp;

    $current_user = MeprUtils::get_currentuserinfo();
    $enabled      = get_option($this->enabled_str, 0);
    $main_slug    = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'membership');

    if($current_user !== false && $enabled) {
      $url = $bp->loggedin_user->domain . $main_slug . '/';
    }

    return $url;
  }

  public function catch_account_page_and_redirect() {
    global $bp;

    $mepr_options = MeprOptions::fetch();
    $current_post = MeprUtils::get_current_post();
    $current_user = MeprUtils::get_currentuserinfo();
    $enabled      = get_option($this->enabled_str, 0);
    $main_slug    = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'membership');

    if($enabled && isset($current_post->ID) && $current_post->ID == $mepr_options->account_page_id) {
      if($current_user !== false) {
        MeprUtils::wp_redirect($bp->loggedin_user->domain . $main_slug . '/');
      }
      else {
        MeprUtils::wp_redirect($mepr_options->login_page_url('?redirect_to=' . urlencode($mepr_options->account_page_url())));
      }
    }
  }

  public function setup_bp_nav() {
    global $bp;

    $main_slug = MeprHooks::apply_filters('mepr-bp-info-main-nav-slug', 'membership');

    //Parent
    bp_core_new_nav_item(
      array(
        'name' => MeprHooks::apply_filters('mepr-bp-info-main-nav-name', _x('Membership', 'ui', 'memberpress')),
        'slug' => $main_slug,
        'position' => MeprHooks::apply_filters('mepr-bp-info-main-nav-position', 25),
        'show_for_displayed_user' => false,
        // 'screen_function' => array($this, 'membership_info'), //Not needed with subnav?
        'default_subnav_slug' => 'info',
        'item_css_id' => 'mepr-bp-info'
      )
    );

    //Info Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Info', 'ui', 'memberpress'),
        'slug' => 'info',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_info'),
        'position' => 0,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-info'
      )
    );

    //Subscriptions Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Subscriptions', 'ui', 'memberpress'),
        'slug' => 'subscriptions',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_subscriptions'),
        'position' => 10,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-subscriptions'
      )
    );

    //Payments Sub Menu
    bp_core_new_subnav_item(
      array(
        'name' => _x('Payments', 'ui', 'memberpress'),
        'slug' => 'payments',
        'parent_url' => $bp->loggedin_user->domain . $main_slug . '/',
        'parent_slug' => $main_slug,
        'screen_function' => array($this, 'membership_payments'),
        'position' => 20,
        'user_has_access' => bp_is_my_profile(),
        'site_admin_only' => false,
        'item_css_id' => 'mepr-bp-payments'
      )
    );
  }

  /* INFO TAB */
  public function membership_info() {
    add_action('bp_template_title', array($this, 'membership_info_title'));
    add_action('bp_template_content', array($this, 'membership_info_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_info_title() {
    echo _x('Membership Info', 'ux', 'memberpress');
  }

  public function membership_info_content() {
    ?>
      <style>
        span.mepr-account-change-password {
          display:none !important;
        }
      </style>
    <?php

    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->home();
  }

  /* SUBSCRIPTIONS TAB */
  public function membership_subscriptions() {
    add_action('bp_template_title', array($this, 'membership_subscriptions_title'));
    add_action('bp_template_content', array($this, 'membership_subscriptions_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_subscriptions_title() {
    echo _x('Membership Subscriptions', 'ux', 'memberpress');
  }

  public function membership_subscriptions_content() {
    $acct_ctrl = new MeprAccountCtrl();

    $action = (isset($_REQUEST['action']))?$_REQUEST['action']:false;

    switch($action) {
      case 'cancel':
        $acct_ctrl->cancel();
        break;
      case 'suspend':
        $acct_ctrl->suspend();
        break;
      case 'resume':
        $acct_ctrl->resume();
        break;
      case 'update':
        $acct_ctrl->update();
        break;
      case 'upgrade':
        $acct_ctrl->upgrade();
        break;
      default:
        $acct_ctrl->subscriptions();
    }
  }

  /* PAYMENTS TAB */
  public function membership_payments() {
    add_action('bp_template_title', array($this, 'membership_payments_title'));
    add_action('bp_template_content', array($this, 'membership_payments_content'));

    //Enqueue the account page scripts here yo
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->enqueue_scripts(true);

    bp_core_load_template(apply_filters('bp_core_template_plugin', 'members/single/plugins'));
  }

  public function membership_payments_title() {
    echo _x('Membership Payments', 'ux', 'memberpress');
  }

  public function membership_payments_content() {
    $acct_ctrl = new MeprAccountCtrl();
    $acct_ctrl->payments();
  }

  public function activate_bp_profile($txn) {
    $enabled = get_option($this->enabled_str, 0);

    if($enabled && function_exists('bp_update_user_last_activity')) {
      bp_update_user_last_activity($txn->user_id);
    }
  }
} //End Class
