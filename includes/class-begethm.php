<?php
/**
 * BegetHM setup
 *
 * @package  BegetHM
 * @since    1.0
 */

defined( 'ABSPATH' ) || exit;

final class BegetHM {

    /**
	 * BegetHM version.
	 *
	 * @var string
	 */
    public $version = '1.0.0';    
    
   	/**
	 * The single instance of the class.
	 *
	 * @var BegetHM
	 * @since 1.0
	 */
    protected static $_instance = null;
    
    /**
	 * Main BegetHM Instance.
	 *
	 * Ensures only one instance of BegetHM is loaded or can be loaded.
	 *
	 * @since 1.0
	 * @static
	 * @see beget()
	 * @return BegetHM - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
		return self::$_instance;
    }
    private function __sleep(){}
    private function __wakeup(){}
    
    /**
	 * BegetHM Constructor.
	 */
	public function __construct() {
        $this->constans();		
        $this->includes();
        $this->hooks();
    }

    /**
     * Hooks into actions and filters
     */
    private function hooks() {
        $basename = plugin_basename( BEGET_PLUGIN_FILE );

        add_action( 'plugins_loaded', array( 'BegetHM_settings', 'get_object' ) );
        add_action( 'admin_init', array( $this, 'register_plugin_settings' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'js' ) );
        add_action( 'admin_enqueue_scripts', array( $this, 'css' ) );
        add_action( 'wp_dashboard_setup', array( $this, 'add_dashboard_widgets') );

        // AJAX
        add_action('wp_ajax_check_api', array($this, 'callback_check_api'));

        //add_filter( 'allowed_http_origins', array( $this, 'add_allowed_origins') );
        add_filter( "plugin_action_links_$basename", array( $this, 'plugin_add_settings_link') );
    } 
    
    /**
     * Include Core files
     */    
    public function includes() {
        include_once BEGET_ABSPATH . 'includes/class-bhm-settings.php';
        include_once BEGET_ABSPATH . 'includes/class-bhm-api.php';
    }

    /**
     * Include CSS styles
     *
     */
    public function css() {
        wp_enqueue_style( 'prefix-style',
            plugin_dir_url( BEGET_PLUGIN_FILE ) . 'assets/css/beget.css',
            array(),
            $this->version );
    }

    /**
     * Include JS script only on Beget settings page
     *
     * @param [type] $hook
     * @see $this->hooks()
     */
    public function js( $hook ) {
        if ('settings_page_beget_settings' !== $hook) {
            return;
        }        
        wp_enqueue_script( 'beget_settings_js',
                            plugin_dir_url( BEGET_PLUGIN_FILE ) . 'assets/js/beget.js',
                            array( 'jquery' ),
                            $this->version );
        wp_localize_script( 'beget_settings_js',
                            'beget_loco', 
                            array(
                                'empty_fields' => __( 'login and password can not be empty', BEGET_DOMAIN )

                            )
        );
    }

    /**
     * Define main constans
     */
    private function constans() {
        $this->define( 'BEGET_ABSPATH', dirname( BEGET_PLUGIN_FILE ) . '/' );
    }

    /**
	 * Define constant if not already set.
	 *
	 * @param string      $name  Constant name.
	 * @param string|bool $value Constant value.
	 */
	private function define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
    }

    /**
     * Add settings link in plugin menu
     *
     * @param [array] $links
     * @return array
     */    
    public function plugin_add_settings_link( $links ) {        
        $settings_link = '<a href="options-general.php?page=beget_settings">' . __( 'Settings', BEGET_DOMAIN ) . '</a>';
        array_push( $links, $settings_link );
          return $links;
    }

    /**
     * Register plugin settings fields
     * 
     * @see $this->hooks()
     */
    public function register_plugin_settings() {
        register_setting( 'begethm-settings-group', 'beget_login' );
	    register_setting( 'begethm-settings-group', 'beget_password' );
    }

    /**
     * Check API function
     *
     * @return echo for ajax
     * @see $this->hooks()
     * @todo move to api class
     */    
    public function callback_check_api() {
        $login =  $_POST['login'];
        $pass = $_POST['pass'];

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.beget.com/api/user/getAccountInfo?login=$login&passwd=$pass",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_HTTPHEADER => array(
            "Cache-Control: no-cache"            
          ),
        ));
        
        $response = curl_exec($curl);
        $err = curl_error($curl);
        
        curl_close($curl);
        
        if ($err) {
          echo json_encode(array(
                    'status' => 'error',
                    'message' => __( 'Unknown error, ask for help on the developer`s site', BEGET_DOMAIN )
                ), JSON_UNESCAPED_UNICODE);
        } else {          
          echo $this->parse_check_response($response);
        }

        exit();
    }

    /**
     * Parse response from BegetAPI
     *
     * @param [json string] $response
     * @return json
     * @todo move to api class
     */    
    public function parse_check_response($response) {
        $response = json_decode($response, true);
        if ( $response['status'] == 'success') {
            return json_encode(array(
                'status' => 'success',
                'message' => __( 'The API works, click on the "Save" button', BEGET_DOMAIN )
            ), JSON_UNESCAPED_UNICODE);
        } else {
            if ( $response['error_code'] == 'AUTH_ERROR') {
                return json_encode(array(
                    'status' => 'error',
                    'message' => __( 'Authorization error, check login and password', BEGET_DOMAIN )
                ), JSON_UNESCAPED_UNICODE);
            } else {
                return json_encode(array(
                    'status' => 'error',
                    'message' => __( 'Unknown error, ask for help on the developer`s site', BEGET_DOMAIN )
                ), JSON_UNESCAPED_UNICODE);
            }
        }
    }

    /**
     * Call Dashboard widget 
     *
     * @return $this->dashboard_widget_function()
     * @see $this->hooks()
     */
    public function add_dashboard_widgets() {
        wp_add_dashboard_widget('dashboard_widget', __( 'Beget', BEGET_DOMAIN ), array( $this, 'dashboard_widget_function') );
    }

    /**
     * Dashboard widget
     *
     * @return html
     * @see $this->hooks()
     */
    public function dashboard_widget_function() {
        $settings = BegetHM_settings::get_object();
        $data = $settings->api_factory->getAccountInfo();
        ?>
        <h3><?= __( 'General Information', BEGET_DOMAIN ) ?></h3>
        <ul class="beget_widget">
            <li>
                <span><?= __( 'Hostin plan:', BEGET_DOMAIN ) ?></span>
                <p><?= $data['plan_name'] ?></p>
            </li>
            <li>
                <span><?= __( 'Balance:', BEGET_DOMAIN ) ?></span>
                <p><?= $data['user_balance'] ?> <?= __( '$', BEGET_DOMAIN ) ?></p>
            </li>
            <li>
                <span><?= __( 'Days to block:', BEGET_DOMAIN ) ?></span>
                <p><?= $data['user_days_to_block'] ?></p>                    
            </li>
        </ul>
        <?php
    }
}