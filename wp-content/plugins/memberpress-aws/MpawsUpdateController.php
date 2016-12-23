<?php
if(!defined('ABSPATH')) {die('You are not allowed to call this page directly.');}

class MpawsUpdateController {
  public $memberpress_active;

  public function __construct() {
    //delete_site_transient('update_plugins');
    $this->load_hooks();
  }

  public function load_hooks() {
    add_filter('pre_set_site_transient_update_plugins', array( $this, 'queue_update' ));
    add_filter('plugins_api', array( $this, 'plugin_info' ), 937418, 3);

    include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    $this->memberpress_active = is_plugin_active('memberpress/memberpress.php');
  }

  public function queue_update($transient) {
    if(empty($transient->checked)) { return $transient; }

    $license = $this->license();
    if(empty($license)) {
      // Just here to query for the current version
      $args = array();
      if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) { $args['edge'] = 'true'; }

      $version_info = $this->send_mothership_request( "/versions/latest/".MPAWS_EDITION, $args );
      $curr_version = $version_info['version'];
      $download_url = '';
    }
    else {
      try {
        $domain = urlencode(MpawsUtils::site_domain());
        $args = compact('domain');

        if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) { $args['edge'] = 'true'; }

        $license_info = $this->send_mothership_request("/versions/info/".MPAWS_EDITION."/{$license}", $args);
        $curr_version = $license_info['version'];
        $download_url = $license_info['url'];
      }
      catch(Exception $e) {
        try {
          // Just here to query for the current version
          $args = array();
          if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) {
            $args['edge'] = 'true';
          }

          $version_info = $this->send_mothership_request("/versions/latest/".MPAWS_EDITION, $args);
          $curr_version = $version_info['version'];
          $download_url = '';
        }
        catch(Exception $e) {
          if(isset($transient->response[MPAWS_PLUGIN_SLUG])) {
            unset($transient->response[MPAWS_PLUGIN_SLUG]);
          }

          return $transient;
        }
      }
    }

    $installed_version = $transient->checked[MPAWS_PLUGIN_SLUG];

    if(isset($curr_version) && version_compare($curr_version, $installed_version, '>')) {
      $transient->response[MPAWS_PLUGIN_SLUG] = (object)array(
        'id'          => $curr_version,
        'slug'        => 'memberpress-aws',
        'new_version' => $curr_version,
        'url'         => 'http://memberpress.com',
        'package'     => $download_url
      );
    }
    else {
      unset( $transient->response[MPAWS_PLUGIN_SLUG] );
    }

    return $transient;
  }

  public function plugin_info($api, $action, $args) {
    global $wp_version;

    if(!isset($action) || $action != 'plugin_information') { return $api; }

    if(isset( $args->slug) && !preg_match("#.*{$args->slug}.*#", MPAWS_PLUGIN_SLUG)) {
      return $api;
    }

    $license = $this->license();
    if(empty($license)) {
      // Just here to query for the current version
      $args = array();
      if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) { $args['edge'] = 'true'; }

      $version_info = $this->send_mothership_request('/versions/latest/'.MPAWS_EDITION, $args);
      $curr_version = $version_info['version'];
      $version_date = $version_info['version_date'];
      $download_url = '';
    }
    else {
      try {
        $domain = urlencode(MpawsUtils::site_domain());
        $args = compact('domain');

        if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) {
          $args['edge'] = 'true';
        }

        $license_info = $this->send_mothership_request("/versions/info/{$license}", $args);
        $curr_version = $license_info['version'];
        $version_date = $license_info['version_date'];
        $download_url = $license_info['url'];
      }
      catch(Exception $e) {
        try {
          $args = array();
          if( defined( "MEMBERPRESS_EDGE" ) && MEMBERPRESS_EDGE ) {
            $args['edge'] = 'true';
          }

          // Just here to query for the current version
          $version_info = $this->send_mothership_request('/versions/latest/'.MPAWS_EDITION, $args);
          $curr_version = $version_info['version'];
          $version_date = $version_info['version_date'];
          $download_url = '';
        }
        catch(Exception $e) {
          if(isset($transient->response[MEPR_PLUGIN_SLUG])) {
            unset($transient->response[MEPR_PLUGIN_SLUG]);
          }

          return $transient;
        }
      }
    }

    $pinfo = (object)array(
      'slug' => MPAWS_PLUGIN_SLUG,
      'name' => __('MemberPress AWS', 'memberpress-aws'),
      'author' => '<a href="http://blairwilliams.com">' . __('Caseproof, LLC', 'memberpress-aws') . '</a>',
      'author_profile' => 'http://blairwilliams.com',
      'contributors' => array('Caseproof' => 'http://caseproof.com'),
      'homepage' => 'http://memberpress.com',
      'version' => $curr_version,
      'new_version' => $curr_version,
      'requires' => $wp_version,
      'tested' => $wp_version,
      'compatibility' => array($wp_version => array($curr_version => array(100, 0, 0))),
      'rating' => '100.00',
      'num_ratings' => '1',
      'downloaded' => '1000',
      'added' => '2014-12-31',
      'last_updated' => $version_date,
      'tags' => array(
        'membership' => __('Membership', 'memberpress-aws'),
        'membership software' => __('Membership Software', 'memberpress-aws'),
        'members' => __('Members', 'memberpress-aws'),
        'payment' => __('Payment', 'memberpress-aws'),
        'protection' => __('Protection', 'memberpress-aws'),
        'rule' => __('Rule', 'memberpress-aws'),
        'lock' => __('Lock', 'memberpress-aws'),
        'access' => __('Access', 'memberpress-aws'),
        'community' => __('Community', 'memberpress-aws'),
        'admin' => __('Admin', 'memberpress-aws'),
        'pages' => __('Pages', 'memberpress-aws'),
        'posts' => __('Posts', 'memberpress-aws'),
        'plugin' => __('Plugin', 'memberpress-aws')
      ),
      'sections' => array(
        'description' => '<p>' . __('MemberPress AWS allows you to protect and embed content hosted on Amazon AWS.', 'memberpress-aws') . '</p>',
        'faq' => '<p>' . sprintf(__('You can access in-depth information about MemberPress at %1$sthe MemberPress User Manual%2$s.', 'memberpress-aws'), '<a href=\'http://memberpress.com/user-manual\'>', '</a>') . '</p>',
        'changelog' => '<p>'.__('No Additional information right now', 'memberpress-aws').'</p>'
      ),
      'download_link' => $download_url
    );

    return $pinfo;
  }

  public function send_mothership_request( $endpoint,
                                           $args=array(),
                                           $method='get',
                                           $domain='http://mothership.caseproof.com',
                                           $blocking=true ) {
    $uri = $domain.$endpoint;

    $arg_array = array(
      'method'    => strtoupper($method),
      'body'      => $args,
      'timeout'   => 15,
      'blocking'  => $blocking,
      'sslverify' => false
    );

    $resp = wp_remote_request($uri, $arg_array);

    // If we're not blocking then the response is irrelevant
    // So we'll just return true.
    if($blocking == false) { return true; }

    if(is_wp_error($resp)) {
      throw new Exception(__('You had an HTTP error connecting to Caseproof\'s Mothership API', 'memberpress-aws'));
    }
    else {
      if(null !== ($json_res = json_decode($resp['body'], true))) {
        if(isset($json_res['error'])) {
          throw new Exception($json_res['error']);
        }
        else {
          return $json_res;
        }
      }
      else {
        throw new Exception(__( 'Your License Key was invalid', 'memberpress-aws'));
      }
    }

    return false;
  }

  public function manually_queue_update() {
    $transient = get_site_transient('update_plugins');
    set_site_transient('update_plugins', $this->queue_update($transient));
  }

  private function license() {
    if( $this->memberpress_active ) {
      $mepr_options = MeprOptions::fetch();
      return $mepr_options->mothership_license;
    }
    else {
      return get_option('mepr_aws_license_key');
    }
  }
} //End class

