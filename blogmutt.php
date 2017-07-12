<?php
/*
Plugin Name: BlogMutt
Plugin URI: http://blogmutt.com
Description: BlogMutt plugin prototype.
Version: 1.2
Author: BlogMutt
Author URI: http://blogmutt.com
*/
if ( ! defined( 'ABSPATH' ) )
	die( "Can't load this file directly" );
class MyGallery
{
	function __construct() {
		add_action( 'admin_init', array( $this, 'action_admin_init' ) );
                add_action('admin_menu', array( $this, 'createSettingPage' ));
                add_action( 'wp_ajax_my_action', array( $this,'my_action_callback') );
                add_action('wp_ajax_test_response', array( $this,'text_ajax_process_request'));
                add_action('wp_ajax_check_setting', array( $this,'text_ajax_check_is_setting_defined'));
                
                add_action('admin_head', array($this,'my_add_mce_button'));
                
                //add_action('init', array( $this, 'my_script_css_load'));
                register_deactivation_hook(__FILE__, array($this, 'blogmutt_deactivation'));
	}
	
	function action_admin_init() {
		// only hook up these filters if we're in the admin panel, and the current user has permission
		// to edit posts and pages
		if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
                    
                    
                    //include_once('include/settings-template.php');
			add_filter( 'mce_buttons', array( $this, 'filter_mce_button' ) );
			add_filter( 'mce_external_plugins', array( $this, 'filter_mce_plugin' ) );	
                        
		}
	}

        function blogmutt_deactivation() {    
            // actions to perform once on plugin deactivation go here	
            update_option('blogmutt-udid-options', "");

        }
	
	function filter_mce_button( $buttons ) {
		// add a separation before our button, here our button's id is "mygallery_button"
            include_once('include/blogmutt-template.php');
                array_push( $buttons, '|', 'blogmutt_separator_button' );
                array_push( $buttons, "|", 'blogmutt_suggestedpost_button' );
                //array_push( $buttons, '|', 'blogmutt_separator_button' );
                array_push( $buttons, '|', 'blogmutt_randompost_button' );
                //array_push( $buttons, '|', 'blogmutt_separator_button' );
                array_push( $buttons, '|', 'blogmutt_search_button' );
		return $buttons;
	}
        
	
	function filter_mce_plugin( $plugins ) {
            
            // this plugin file will work the magic of our button
		$plugins['mygallery'] = plugin_dir_url( __FILE__ ) . 'mygallery_plugin.js';
		return $plugins;
	}
        
        /*
         * Setting Page Code Start Here
         */
        
        function createSettingPage(){

            add_options_page('BlogMutt','BlogMutt','manage_options','blogmutt_setting_page', array( $this,'blogmutt_setting_page_create'));
        }

        function blogmutt_setting_page_create(){
           include_once('include/settings-template.php');
           //showSettingsUI();
        }
        
        function my_script_css_load(){
            
            wp_enqueue_style('bootstrap-datepicker',  plugins_url().'/myGallery/assets/css/bootstrap-datepicker.css');
            
            wp_register_script( 'bootstrap-datepicker', plugins_url().'/myGallery/assets/js/bootstrap-datepicker.js');
            
            //<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
            wp_enqueue_script( 'bootstrap-datepicker');
            wp_enqueue_script('jquery','https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js');


        }

        function my_action_callback() {
                    global $wpdb; // this is how you get access to the database

                    // make the ajaxurl var available to the above script
                    wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
                    $whatever = intval( $_POST['whatever'] );

                    $whatever += 10;

                    echo $whatever;

                    die(); // this is required to return a proper result
            }
        //
        /*
         * Setting Page Code End Here
         */
            
            function text_ajax_check_is_setting_defined() {
                // first check if data is being sent and that it is the data we want
                wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
                //if ( isset( $_POST["post_var"] ) ) {
                        // now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
                        //$response = $_POST["post_var"] . " sdsdsd ";
                        // send the response back to the front end
                    
                        $options = !is_array(get_option('blogmutt-udid-options')) ? "" : get_option('blogmutt-udid-options');
                        if(is_array($options)){
                            $blogMuttUDID = $options["blogMuttUDID"];
                            //if(is_null($blogMuttUDID) || $blogMuttUDID=="")
                                //$request = "add";
                        }else{
                            $blogMuttUDID = "";
                            //$request = "add";
                        }
                    
                        $response = $blogMuttUDID;
                        echo $response;
                        die();
                //}
                
                
        }
            
            function text_ajax_process_request() {
                // first check if data is being sent and that it is the data we want
                wp_localize_script( 'ajax-test', 'the_ajax_script', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );
                if ( isset( $_POST["post_var"] ) ) {
                        // now set our response var equal to that of the POST var (this will need to be sanitized based on what you're doing with with it)
                        //$response = $_POST["post_var"] . " sdsdsd ";
                        // send the response back to the front end
                    
                        $options = !is_array(get_option('blogmutt-udid-options')) ? "" : get_option('blogmutt-udid-options');
                        if(is_array($options)){
                            $blogMuttUDID = $options["blogMuttUDID"];
                            //if(is_null($blogMuttUDID) || $blogMuttUDID=="")
                                //$request = "add";
                        }else{
                            $blogMuttUDID = "";
                            //$request = "add";
                        }
                    
                        $response = $blogMuttUDID;
                        
                         $urlRandomGet = "http://big.blogmutt.com/api/v1/idea/$blogMuttUDID";
                        //echo $urlRandomGet;
                             $response_jsonSuggestion = $this->getResponseArrayFromServiceURL1($urlRandomGet);
                             //print_r($response_jsonSuggestion);
                             if(count($response_jsonSuggestion) > 0){
                                 $suggestions = $response_jsonSuggestion["idea"];      
                                 $suggestionsCount = count($suggestions);
                                if($suggestionsCount==0){
                                }else{
                                        $finalContent = "";
                                        $sId = $suggestions["id"];
                                        $suggestionTitle = $suggestions["title"];
                                        $suggestionDescription =  $suggestions["content"]. "</br>" . "</br>" ;
                                        
                                        $news_link = $suggestions["news_link"]. "</br>" . "</br>" ;
                                        $cta = (($suggestions["cta"]!="" && !is_null($suggestions["cta"])) ? $suggestions["cta"]. "</br>" . "</br>" : "");
                                        //"<span style='color:#cccccc;font-size:11px;display: block;padding-top:10px'>" . $landingPost["blogmutt_promo_text"] . "</span>";
                                        $blogmutt_promo_text = "<span style='color:#666666;font-size:11px;display: block;padding-top:11px;font-style:italic !important'>" . $suggestions["blogmutt_promo_text"] . "</span>";
                                        
                                        $finalContent = $suggestionTitle . "[title]" . $suggestionDescription . $news_link . $cta . $blogmutt_promo_text;
                                        //$finalContent = $suggestionTitle . "[title]" . $suggestionDescription . $cta . $blogmutt_promo_text;
                                        
                                        //$response = $finalContent;
                                        $response = json_encode($suggestions);
                                }
                            }
                        echo $response;
                        die();
                }
                
                
        }
        function createRandomPost($postTitle,$postContent){
    
            global $pagesArr;
            global $wpdb;

            $_p = array();
            $_p['post_title']     = $postTitle;
            $_p['post_content']   = $postContent;
            $_p['post_status']    = 'draft';
            $_p['post_type']      = 'page';
            $_p['comment_status'] = 'closed';
            $_p['ping_status']    = 'closed';
            $_p['post_category'] = array(1); // the default 'Uncatrgorised'

            // Insert the post into the database
            $pageId = wp_insert_post($_p);

            $postId = $pageId;
            $metaKey = $metaKey;
            $metaValue = $metaValue;
            add_post_meta($postId, $metaKey, $metaValue);

            return $pageId;

        }
        function createPagePost1($postTitle,$postContent,$metaKey,$metaValue){
    
                    global $pagesArr;
                    global $wpdb;

                   
                    $_p = array();
                    $_p['post_title']     = $postTitle;
                    $_p['post_content']   = $postContent;
                    $_p['post_status']    = 'publish';
                    $_p['post_type']      = 'blogmutt_post';
                    $_p['comment_status'] = 'closed';
                    $_p['ping_status']    = 'closed';
                    $_p['post_category'] = array(1); // the default 'Uncatrgorised'

                    // Insert the post into the database
                    $pageId = wp_insert_post($_p);

                    $postId = $pageId;
                    $metaKey = $metaKey;
                    $metaValue = $metaValue;
                    add_post_meta($postId, $metaKey, $metaValue);

                    return $pageId;

                }
                
                function getResponseArrayFromServiceURL1($url){
                    $ch = curl_init($url);
                    curl_setopt ( $ch, CURLOPT_URL, $url );
                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt ( $ch, CURLOPT_HEADER, false );
                    curl_setopt ( $ch, CURLOPT_HTTPHEADER, array('Accept: application/json') );
                    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

                    $response = curl_exec($ch);

                    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

                    curl_close($ch);

                    $result = json_decode($response,true); 

                    return $result;
               }
               function getResponseArrayFromServiceURL2($url){
                    $ch = curl_init($url);
                    curl_setopt ( $ch, CURLOPT_URL, $url );
                    curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
                    curl_setopt ( $ch, CURLOPT_HEADER, false );
                    curl_setopt ( $ch, CURLOPT_HTTPHEADER, array('Accept: application/json') );
                    curl_setopt($ch,CURLOPT_USERAGENT,'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.13) Gecko/20080311 Firefox/2.0.0.13');

                    $response = curl_exec($ch);

                    $code = curl_getinfo($ch,CURLINFO_HTTP_CODE);

                    curl_close($ch);

                    $result = json_decode($response,true); 

                    return $result;
               }
               
               // Hooks your functions into the correct filters
            function my_add_mce_button() {
                    // check user permissions
                    if ( !current_user_can( 'edit_posts' ) && !current_user_can( 'edit_pages' ) ) {
                            return;
                    }
                    // check if WYSIWYG is enabled
                    if ( 'true' == get_user_option( 'rich_editing' ) ) {
                            add_filter( 'mce_external_plugins', array($this,'my_add_tinymce_plugin' ));
                            add_filter( 'mce_buttons', array($this,'my_register_mce_button') );
                    }
            }
            

            // Declare script for new button
            function my_add_tinymce_plugin( $plugin_array ) {
                    //$plugin_array['my_mce_button'] = get_template_directory_uri() .'/js/mce-button.js';
               $plugin_array['my_mce_button'] = plugin_dir_url( __FILE__ ) . 'mygallery_plugin.js';
                    return $plugin_array;
            }

            // Register new button in the editor
            function my_register_mce_button( $buttons ) {
                    array_push( $buttons, 'my_mce_button' );
                    return $buttons;
            }
            
}
$mygallery = new MyGallery();
