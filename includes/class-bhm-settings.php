<?php

/**
 * Settings class https://gist.github.com/eteubert/1341347
 */

class BegetHM_settings {
	// singleton class variable
	static private $classobj = NULL;
	
	// internationalization textdomain
    public static $textdomain = 'beget-hm';
    
    public $api_factory = null;
	
	private $settings_page_handle = 'beget_settings';
	
	// singleton method
	public static function get_object() {
		if ( NULL === self::$classobj ) {
			self::$classobj = new self;
		}
		return self::$classobj;
	}
				
	private function __construct() {
        add_action( 'admin_menu', array( $this, 'add_menu_entry' ) );
        add_action( 'init', array( $this, 'init' ), 0 );
    }
    
    function init() {
        $this->api_factory = new BegetHM_api();        
    }
	
	public function add_menu_entry() {
		add_submenu_page( 'options-general.php', __( 'Beget', BEGET_DOMAIN), __( 'Beget', BEGET_DOMAIN), 'manage_options', $this->settings_page_handle, array( $this, 'settings_page' ) );
	}
	
	public function settings_page() {
		$tab = ( $_REQUEST[ 'tab' ] == 'tab2' ) ? 'tab2' : 'tab1';
		?>
		<div class="wrap">           
            <?php if ( $this->keys_exists() ): ?>
			<h2 class="nav-tab-wrapper">
				<a href="<?php echo admin_url( 'options-general.php?page=' . $this->settings_page_handle ) ?>" class="nav-tab <?php echo ( $tab == 'tab1' ) ? 'nav-tab-active' : '' ?>">
					<?= __( 'Info', BEGET_DOMAIN ) ?>
				</a>
				<a href="<?php echo admin_url( 'options-general.php?page=' . $this->settings_page_handle . '&tab=tab2' ) ?>" class="nav-tab <?php echo ( $tab == 'tab2' ) ? 'nav-tab-active' : '' ?>">
					<?= __( 'Server', BEGET_DOMAIN ) ?>
				</a>
            </h2>
            <?php endif; ?>

			<div class="metabox-holder has-right-sidebar">
				<?php
                    $this->settings_page_sidebar();
                    if ( $this->keys_exists() ) {
                        if ( $tab == 'tab1' ) {
                            $this->settings_page_tab1();
                        } else {
                            $this->settings_page_tab2();
                        }
                    } else {
                        $this->register_api_keys();
                    }
				?>
			</div> <!-- .metabox-holder -->
		</div> <!-- .wrap -->
		<?php
	}
	
	private function settings_page_sidebar() {
		# see http://www.satoripress.com/2011/10/wordpress/plugin-development/clean-2-column-page-layout-for-plugins-70/
		?>
		<div class="inner-sidebar">

			<div class="postbox">
				<h3><span><?= __( 'Helpful information', BEGET_DOMAIN ) ?></span></h3>
				<div class="inside">
                    <p>
                        <?=
                            sprintf( 
                                __( 'Are you still not a %sBeget%s customer?', BEGET_DOMAIN ), 
                                '<a href="https://beget.com/p420165" target="blank">',
                                '</a>' 
                            );
                        ?>
                    </p>                    
				</div>
			</div>

		</div> <!-- .inner-sidebar -->
		<?php
	}
	
	private function settings_page_tab1() {
        $data = $this->api_factory->getAccountInfo();        
		?>
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
					<h3><span><?= __( 'General Information', BEGET_DOMAIN) ?></span></h3>
					<div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?= __( 'Hostin plan:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['plan_name'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Balance:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['user_balance'] ?> <?= __( '$', BEGET_DOMAIN ) ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Days to block:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['user_days_to_block'] ?></p>
                                </td>
                            </tr>
                        </table>
					</div> <!-- .inside -->
				</div>

			</div> <!-- #post-body-content -->
		</div> <!-- #post-body -->	
		<?php
    }

    private function settings_page_tab2() {
        $data = $this->api_factory->getAccountInfo();        
		?>        
		<div id="post-body">
			<div id="post-body-content">
				<div class="postbox">
					<h3><span><?= __( 'Server Information', BEGET_DOMAIN ) ?></span></h3>
					<div class="inside">
                        <table class="form-table">
                            <tr valign="top">
                                <th scope="row"><?= __( 'Apache version:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_apache_version'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'MySQL version:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_mysql_version'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Nginx version:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_nginx_version'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Perl version:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_perl_version'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Python version:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_python_version'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Server name:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_name'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'CPU name:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_cpu_name'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Server Memory (mb):', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_memory'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Server Current Memory (mb):', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_memorycurrent'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Server Loadaverage:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_loadaverage'] ?></p>
                                </td>
                            </tr>
                            <tr valign="top">
                                <th scope="row"><?= __( 'Server uptime:', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <p><?= $data['server_uptime'] ?></p>
                                </td>
                            </tr>
                        </table>
					</div> <!-- .inside -->
				</div>

			</div> <!-- #post-body-content -->
		</div> <!-- #post-body -->	
		<?php
    }
    
    private function register_api_keys() {
		?>
		<div id="post-body">
			<div id="post-body-content">

				<div class="postbox">					
					<div class="inside">						
                        <h1><?= __( 'Register Beget login and password', BEGET_DOMAIN ) ?></h1>
                        <div id="beget_message"></div>
                        <form method="post" action="options.php">
                            <?php settings_fields( 'begethm-settings-group' ); ?>
                            <?php do_settings_sections( 'begethm-settings-group' ); ?>
                            <table class="form-table">
                                <tr valign="top">
                                <th scope="row"><?= __( 'Login', BEGET_DOMAIN ) ?></th>
                                <td><input id="login"
                                            type="text"
                                            name="beget_login"
                                            value="<?php echo esc_attr( get_option('beget_login') ); ?>"
                                            /></td>
                                </tr>
                                
                                <tr valign="top">
                                <th scope="row"><?= __( 'Password', BEGET_DOMAIN ) ?></th>
                                <td><input id="pass"
                                           type="password"
                                           name="beget_password"
                                           value="<?php echo esc_attr( get_option('beget_password') ); ?>"
                                           /></td>
                                </tr>

                                <tr valign="top">
                                <th scope="row"><?= __( 'Check', BEGET_DOMAIN ) ?></th>
                                <td>
                                    <input type="button" name="check" id="check" class="button" value="Check API">
                                    <p class="beget_attention"><?= __( 'Before saving, check the API availability', BEGET_DOMAIN ) ?></p>                          
                                </td>
                                </tr>
                            </table>                              
                            <p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?= __( 'Save Changes', BEGET_DOMAIN ) ?>" disabled></p>
                        </form>
					</div> <!-- .inside -->
				</div>

			</div> <!-- #post-body-content -->
		</div> <!-- #post-body -->	
		<?php
    }
    
    private function keys_exists() {        
        if( !empty(get_option('beget_login')) and !empty(get_option('beget_password'))) {
            return true;
        } else {
            return false;
        }
    }
}