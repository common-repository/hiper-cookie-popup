<?php

/**
 * @author Hiper Criativo
 * @url http://www.hipercriativo.pt
 * @copyright 2018
 */

/**
 * Plugin Name: Hiper Cookie Popup Plugin
 * Description: Have your own Privacy Policy popup on your website.
 * Version: 1.0
 */
if ( ! defined( 'WPINC' ) )die('security by preventing any direct access to your plugin file');
     if (!class_exists('hiper_Cookie_Consent')) {
         class hiper_Cookie_Consent
             {
                 public function __construct()
                 {
                     global $wpdb;
                     $this->id = 'hipercritaivo';
                     $this->method_title = __('Hiper Cookie Consent Plugin', 'hipercritaivo');
                     $this->method_description = __('Have your own Privacy Policy popup on your website.', 'hipercritaivo');
                     $this->enabled = isset($this->settings['enabled']) ? $this->settings['enabled'] : 'yes';
                     $this->title = isset($this->settings['title']) ? $this->settings['title'] : $this->method_title;
                     $this->isOn=get_option('hiper_cookie_consent_on',false);
                     $this->hiper_cookie_consent_init();
                        //add_action( 'admin_notices', array($this, 'hipercriativo_admin_notice__error') );
                     
                 }
                 function hiper_cookie_consent_init()
                 {
                    if ( ! is_admin() ) {
                        add_action('wp_head', array($this, 'hiper_cookie_consent_enqueue_front_scripts'));
                        add_action('wp_footer', array($this,'hiper_cookie_consent_print'));
                    }
                    add_action('admin_menu', array($this,'register_hiper_cookie_consent_page'),99);
                 }
                 function hiper_cookie_consent_enqueue_front_scripts()
                 {
                    if(!$this->isOn||$this->isOn!='on')return;
                    wp_enqueue_style('hiper_cookie_css',plugins_url('css/hiper_cookie.css', __FILE__));
                    wp_enqueue_script('hiper_cookie_js',plugins_url('js/hiper_cookie.js', __FILE__));
                 }
                 function register_hiper_cookie_consent_page() {
                    add_menu_page( 'Cookie Policy Popup', 'Cookie Policy Popup', 'manage_options', 'hiper_cookie', array($this,'hiper_cookie_consent_page_callback'),'dashicons-index-card' );
                 }
                 function getLanguageOptions()
                 {
                    global $wpdb;
                    $lang='';
                    
                    /*IF WPML EXISTS AND IS ACTIVE*/
                    if ( function_exists('icl_object_id') ) {
                        $lang=( defined('ICL_LANGUAGE_CODE')?'_'.ICL_LANGUAGE_CODE:'');
                    }
                    /*IF POLYLANG EXISTS AND IS ACTIVE*/
                    if ( function_exists('pll_current_language') ) {
                        if( pll_current_language() )$lang='_'.pll_current_language();
                    }
                    
                    return $lang;
                 }
                 function hiper_cookie_consent_page_callback() {
                    global $wpdb;
                    if ( is_user_logged_in()&&!current_user_can( 'manage_options' ) ) {
                        wp_die( __( 'Restricted access' ) );
                    }
                    $message=$lang='';
                    
                    $lang=$this->getLanguageOptions();
                    
                    if(isset($_POST['hiper_submit'])){
                        if( !isset($_POST['hiper_nonce_popup']) || !wp_verify_nonce( sanitize_key($_POST['hiper_nonce_popup']),-1 ) ){
                            $message='<div class="alert alert-warning">'.__( 'An error occurred.', 'hipercritaivo' ).'</div>';
                        }
                        //$cookieMsg=esc_html($_POST['hiper_cookie_consent_message']);
                        $cookieMsg=htmlentities($_POST['hiper_cookie_consent_message']);
                        $cookieBtn=esc_html($_POST['hiper_cookie_consent_button_text']);
                        
                        if($cookieMsg==(get_option('hiper_cookie_consent_message'.$lang,false)) ){
                            $message='<div class="alert alert-warning">'.__( 'Content was already saved previously.', 'hipercritaivo' ).'</div>';
                        }else{
                            if(update_option('hiper_cookie_consent_button_text'.$lang,$cookieBtn))
                            {
                                $message='<div class="alert alert-success">'.__( 'Updated successfully!', 'hipercritaivo' ).'</div>';
                            }
                            if(update_option('hiper_cookie_consent_message'.$lang,$cookieMsg))
                            {
                                $message='<div class="alert alert-success">'.__( 'Updated successfully!', 'hipercritaivo' ).'</div>';
                            }
                            else $message='<div class="alert alert-danger">'.__( 'An error occurred!', 'hipercritaivo' ).'</div>';
                        }
                    }
                    $cookieMsg=get_option('hiper_cookie_consent_message'.$lang,false);
                    $cookieBtn=get_option('hiper_cookie_consent_button_text'.$lang,false);
                    echo '<div class="wrap">';
                    echo '<h1 class="hero">Cookie Policy Popup</h1>';
                    echo '<small class="madeby">by: <a href="http://hipercriativo.pt" target="_blank">Hiper Criativo</a></small>';
                    echo ($message)?$message:'';
                    echo '<form action="'.esc_url( admin_url( 'admin.php?page=hiper_cookie' ) ).'" method="post" enctype="multipart/form-data">';
                    echo '<div class="form-group"><label>'.__( 'Cookie box content', 'hipercritaivo' ).'</label>';
                    echo '<textarea name="hiper_cookie_consent_message" class="form-control trumbowyg" id="hiper_cookie_consent_message">'.(($cookieMsg)?html_entity_decode(stripslashes($cookieMsg)):'').'</textarea></div>';
                    echo '<hr class="clearfix mt-2 mb-2">';
                    echo '<div class="form-group"><label>'.__( 'Cookie box button text', 'hipercritaivo' ).'</label>';
                    echo '<input type="text" name="hiper_cookie_consent_button_text" class="form-control" id="hiper_cookie_consent_button_text" value="'.(($cookieBtn)?stripslashes($cookieBtn):'').'"></div>';
                    wp_nonce_field(-1,'hiper_nonce_popup');
                    echo '<button type="submit" name="hiper_submit" class="btn btn-primary">'.__( 'Save', 'hipercritaivo' ).'</button>';
                    echo '</form>';
                    echo '</div>';
                    wp_enqueue_style('hipertrumbowygcss',plugins_url('css/trumbowyg.min.css', __FILE__));
                    wp_enqueue_style('hiperbootstrapcss',plugins_url('css/bootstrap.min.css', __FILE__));
                    wp_enqueue_style('hiperadmincss',plugins_url('css/admin.css', __FILE__));
                    //wp_enqueue_style('hiperfontawesomecss','https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
                    wp_enqueue_script('hipertrumbowygjs',plugins_url('js/trumbowyg.min.js', __FILE__));
                    wp_enqueue_script('hiperadminjs',plugins_url('js/hiperadmin.js', __FILE__));
                   
                 }
                 function hiper_cookie_consent_print()
                 {
                    global $wpdb;
                    $lang=$this->getLanguageOptions();
                    $cookieMsg=get_option('hiper_cookie_consent_message'.$lang,false);
                    $cookieBtn=get_option('hiper_cookie_consent_button_text'.$lang,false);
                    if($cookieMsg):
                        echo '<div class="cookie-container">';
                        echo '<div class="cookie-holder">';
                        echo html_entity_decode(stripslashes($cookieMsg));
                        echo '<button id="btn-hiper-cookie-consent" class="btn-hiper btn-blue">'.( ($cookieBtn)?stripslashes($cookieBtn):__('I Agree','hipercritaivo') ).'</button>';
                        echo '</div>';
                        echo '</div>';
                     endif;
                 }
                 function hiper_admin_notice__error()
                 {
                    $class = 'notice notice-error';
                    $msg = __( '', 'hipercritaivo' );
                    printf( '<div class="%1$s"><p>%2$s</p></div>', esc_attr( $class ), esc_html( $msg ) );
                 }
            
             }
     }
 
function hiper_cookie_consent_on_activation()
{
    global $wpdb;
    if ( ! current_user_can( 'activate_plugins' ) )return;
    $plugin=plugin_basename( __FILE__ );
    check_admin_referer( "activate-plugin_{$plugin}" );
    update_option('hiper_cookie_consent_on','on');
}
function hiper_cookie_consent_on_deactivation()
{
    if ( ! current_user_can( 'activate_plugins' ) )return;
    global $wpdb;
    $plugin=plugin_basename( __FILE__ );
    check_admin_referer( "deactivate-plugin_{$plugin}" );
    delete_option('hiper_cookie_consent_on');
}

add_action('plugins_loaded', function(){
    $hiper_Cookie_Consent=new hiper_Cookie_Consent();
});
register_activation_hook( __FILE__, 'hiper_cookie_consent_on_activation' );
register_deactivation_hook( __FILE__, 'hiper_cookie_consent_on_deactivation' );
?>