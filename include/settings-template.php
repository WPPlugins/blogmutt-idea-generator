<?php
    function getResponsePOSTArrayFromServiceURL($url,$data,$requestType){
        
       $ch = curl_init($url);

        
        curl_setopt ( $ch, CURLOPT_URL, $url );
        
        if($requestType=="update")
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
        else
            curl_setopt($ch, CURLOPT_POST, count($data));
        
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
        curl_setopt ( $ch, CURLOPT_HEADER, false );
        //curl_setopt ( $ch, CURLOPT_HTTPHEADER, array('Accept: application/json') );
        
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
   function ilc_admin_tabs( $current = 'homepage' ) {
        $tabs = array( 'homepage' => 'Settings', 'general' => 'Suggested Posts' );
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=blogmutt_setting_page&tab=$tab'>$name</a>";

        }
        echo '</h2>';    
    }
    function createPagePostSettingTemplate($postTitle,$postContent){

        global $pagesArr;
        global $wpdb;

        
        $_p = array();
        $_p['post_title']     = $postTitle;
        $_p['post_content']   = $postContent;
        $_p['post_status']    = 'draft';
        $_p['post_type']      = 'post';
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
    
    //echo site_url();
    $request = "update";
    
    $user = array();
    $keywords = array();
    $keywordsCount = 0;
    $userCount = 0;
    $businessName = "";
    $businessURL = "";
    $blogURL = "";
    $contactPageURL = "";
    $emailAddress = "";
    $dayOfWeek = "";
    $timeOfDay = "";
    $timeZone = "";
    $suggestionsCount = 0;
    $redictToDraftPage = "";
    $pagesStatus = "";
    $emailError = "";
    $isWeeklyUpdateCheck = "checked";
    //update_option('blogmutt-udid-options', "");
    $options = !is_array(get_option('blogmutt-udid-options')) ? "" : get_option('blogmutt-udid-options');
    if(is_array($options)){
        $blogMuttUDID = $options["blogMuttUDID"];
        if(is_null($blogMuttUDID) || $blogMuttUDID=="")
            $request = "add";
    }else{
        $blogMuttUDID = "";
        $request = "add";
    }
    
     if((!is_null($_POST) && $_POST!="") && (count($_POST) > 0)){
        $redictToDraftPage = "";
        if($_GET['tab']=="general"){
            $selectedPostId = $_POST["selectedPostId1"];
           if($selectedPostId!=""){
               
               //$cPageDraftId = createDraft($selectedPostId);
               $cPageData = createDraft($selectedPostId);
               $cPageDraftId = $cPageData["draft_id"];
               $cPageDraftNewsLink = urlencode($cPageData["news_link"]);
               if($cPageDraftId!=""){
                   $redictToDraftPage = "yes";
                   $url = site_url()."/wp-admin/post.php?post=".$cPageDraftId."&action=edit&news_link=$cPageDraftNewsLink";   
               }
               
            }
        }
         if(($_GET['tab']=="homepage") || is_null($_GET['tab'])){
             
             
             /*echo "<pre>";
                print_r($_POST);
             echo "</pre>";
             die;*/
             $str_time = "";
             $time_window_start_time = $_POST["customer"]["time_window_start_time"];
             
             $time_window_weekly = $_POST["customer"]["time_window_weekly"];
             if(empty($time_window_weekly))
                 $_POST["customer"]["time_window_weekly"] = false;
             if (strpos($time_window_start_time,'am') !== false) {
                 $splitTimeWithAmorPm = explode("am", $time_window_start_time);
                 $str_time = $splitTimeWithAmorPm[0];
                 
                 sscanf($str_time, "%d:%d:%d", $hours, $minutes, $seconds);

                $time_seconds = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;
            
                $time_seconds = $time_seconds * 60;
             }else if(strpos($time_window_start_time,'pm') !== false) {
                 $splitTimeWithAmorPm = explode("pm", $time_window_start_time);
                 $splitTimeWithAmorPmAndWithColon = explode(":", $splitTimeWithAmorPm[0]);
                 
                 //echo $splitTimeWithAmorPmAndWithColon[0] . "</br>";
                 $str_timeInPm = ($splitTimeWithAmorPmAndWithColon[0] + 12) * 3600;
                 $time_seconds = $str_timeInPm + ($splitTimeWithAmorPmAndWithColon[1] * 60);
                // echo $str_timeInPm . "</br>";
                /*
                 switch($splitTimeWithAmorPmAndWithColon[0]){
                     case ""
                 }
                 * 
                 */
                 //$str_time = $str_timeInPm . ":" . $splitTimeWithAmorPmAndWithColon[1];
             }
             
             
             //$str_time = "18:30";
             $_POST["customer"]["time_window_start_time"] = $time_seconds;
             $_POST["customer"]["blog_admin_url"] = site_url() . "/" . "wp-admin/" . "options-general.php?page=blogmutt_setting_page&tab=homepage";
            //echo $time_seconds;
             //return;
             $requestParam = http_build_query($_POST, NULL, '&');
                if($request=="update")
                    $urlPost = "http://big.blogmutt.com/api/v1/customers/$blogMuttUDID";
                else    
                    $urlPost = "http://big.blogmutt.com/api/v1/customers/";
               
                if(isset($_POST["customer"]["keywords_attributes"])){
                    foreach($_POST["customer"]["keywords_attributes"] as $i=>$val){
                        if(!$val["keyword"]){
                           $_POST["customer"]["keywords_attributes"][$i]["_destroy"] = TRUE;
                        }
                    }
                    $requestParam = http_build_query($_POST, NULL, '&');
                    
                }
                //echo $urlPost . "request " . $request;
                $results = getResponsePOSTArrayFromServiceURL($urlPost,$requestParam,$request);
                
                /*
                echo "<pre>";
                    print_r($results);
                 echo "</pre>";
                 */
                if(count($results) > 0){
                    $blogMuttUDIDFromService = $results["identifier"];
                    $errorArr =  $results["email"];
                    
                    if(count($blogMuttUDIDFromService) > 0){
                        if($blogMuttUDIDFromService!=""){
                            $variable = array('blogMuttUDID' => $blogMuttUDIDFromService);
                            update_option('blogmutt-udid-options', $variable);
                            $request = "update";
                            $pagesStatus="UpdateDone";
                            $blogMuttUDID = $blogMuttUDIDFromService;
                        }
                    }else{
                        if(count($errorArr) > 0){
                            $pagesStatus="EmailProblem";
                            $emailError = "email " . $errorArr[0];

                            //echo $emailError;
                        }
                    }
                    
                }       
         }   
         
    }
    
   // echo "</br>" . $request;
        if($request=="update"){
            $urlGet = "http://big.blogmutt.com/api/v1/customers/$blogMuttUDID";
            $response_json = getResponseArrayFromServiceURL2($urlGet);
           
            /*
            echo $urlGet;
            
            echo "<pre>";
                print_r($response_json);
             echo "</pre>";
            */
            //http://big.blogmutt.com/api/v1/customers/022fb1acb4394c37778a5f19acb08dce
            if(count($response_json) > 0){
                $user = $response_json;
                
                $keywords = $response_json["keywords"];
                $keywordsCount = count($keywords);
                $userCount = count($user);
                //echo "count " . $userCount;
                if($userCount > 0){
                    
                    $businessName = $user["business_name"];
                    //echo "businessName $businessName";
                    $businessURL = $user["business_url"];
                    $blogURL = $user["blog_url"];
                    $contactPageURL = $user["contact_page_url"];
                    $emailAddress = $user["email"];
                    $dayOfWeek = $user["time_window_start_day_date"];
                    $timeOfDay = $user["time_window_start_time"];
                    $updateTime = $user["update_time"];
                    $timeZone = $user["time_zone"];
                    $timeWindowWeekly = $user["time_window_weekly"];
                    $isWeeklyUpdateCheck = "";
                    if($timeWindowWeekly==true)
                        $isWeeklyUpdateCheck = "checked";
                    
                    if((!is_null($_POST) && $_POST!="") && (count($_POST) > 0)){
                        
                        if($pagesStatus=="")
                            $pagesStatus="UpdateDone";
                        if($emailError!="" && $emailError=="email has already been taken")
                            $emailAddress = $_POST["customer"]["email"];
                    }
                    
                        
                    //$request = "update";
                    //$pagesStatus="UpdateDone";
                        
                }
            }
         
            $urlSuggestedGet = "http://big.blogmutt.com/api/v1/posts/$blogMuttUDID";
            //echo $urlSuggestedGet;
            $response_jsonSuggestion = getResponseArrayFromServiceURL2($urlSuggestedGet);
            if(count($response_jsonSuggestion) > 0){
                $suggestions = $response_jsonSuggestion["suggestion"];      
           }
    }
    $url_parameters = isset($_GET['tab'])? 'updated=true&tab='.$_GET['tab'] : 'updated=true';
    $homeSettingDisplayVal = "block";
    
    if($_GET['tab']=="general"){
        $homeSettingDisplayVal = "none";
        $generalSettingDisplayVal = "block";
        ilc_admin_tabs("general");
        
        //$pagesStatus="";
        //$emailError = "";
    }else{
        $homeSettingDisplayVal = "block";
        $generalSettingDisplayVal = "none";
        ilc_admin_tabs();
    }
    
    
    function formatOffset($offset) {
        $hours = $offset / 3600;
        $remainder = $offset % 3600;
        $sign = $hours > 0 ? '+' : '-';
        $hour = (int) abs($hours);
        $minutes = (int) abs($remainder / 60);

        if ($hour == 0 AND $minutes == 0) {
            $sign = ' ';
        }
        return $sign . str_pad($hour, 2, '0', STR_PAD_LEFT) .':'. str_pad($minutes,2, '0');

}

function createPagePostSetting($postTitle,$postContent){
    
    global $pagesArr;
    global $wpdb;

    $_p = array();
    $_p['post_title']     = $postTitle;
    $_p['post_content']   = $postContent;
    $_p['post_status']    = 'draft';
    $_p['post_type']      = 'post';
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

if((!is_null($_GET) && $_GET!="") && (count($_GET) > 0)){
    if((!is_null($_GET["post_id"]) && $_GET["post_id"]!="") && (count($_GET["post_id"]) > 0)){
        $postId = $_GET["post_id"];
        $sPageData = createDraft($postId);
        $sPageDraftId = $sPageData["draft_id"];
        $sPageDraftNewsLink = urlencode($sPageData["news_link"]);
        if($sPageDraftId!=""){
            $redictToDraftPage = "yes";
            $url = site_url()."/wp-admin/post.php?post=".$sPageDraftId."&action=edit&news_link=$sPageDraftNewsLink";
        }
    }
}

function createDraft($pId){
    $urlGetPost = "http://big.blogmutt.com/api/v1/posts/get_post/$pId";
        //echo $urlSuggestedGet;
        $response_jsonLandingPost = getResponseArrayFromServiceURL2($urlGetPost);
        /*
        echo "<pre>";
            print_r($response_jsonLandingPost);
        echo "</pre>";
         * 
         */
        $settingPageDraftId = "";
        if(count($response_jsonLandingPost) > 0){
            $landingPost = $response_jsonLandingPost["post"];  
            
            $finalContent = "";
            $sId = $landingPost["id"];
            $suggestionTitle = $landingPost["title"];
            $suggestionDescription =  $landingPost["content"]. "</br>" . "</br>" ;

            //$news_link = "<span style='padding-top:10px;padding-bottom:10px;display:block'>" . $landingPost["news_link"]. "</br>" . "</br>" . "</span>" ;
            $cta = (($landingPost["cta"]!="" && !is_null($landingPost["cta"])) ? $landingPost["cta"]. "</br>" . "</br>" : "");
            $blogmutt_promo_text = "<span style='color:#666666;font-size:11px;display: block;padding-top:11px;font-style:italic !important'>" . $landingPost["blogmutt_promo_text"] . "</span>";

            //$finalContent = $suggestionDescription . $news_link . $cta . $blogmutt_promo_text;
            $finalContent = $suggestionDescription . $cta . $blogmutt_promo_text;
            
            $settingPageDraftId = createPagePostSetting($suggestionTitle, $finalContent);
            $news_link  = $landingPost["news_link"];
            
            
       }
       $data = array("draft_id"=>$settingPageDraftId, "news_link"=>$news_link);
       return $data;
}
    
$arrTimeZone = array();
?>

<script type="text/javascript">

if(typeof $ == 'undefined'){
        document.write('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></'+'script>');
  }

</script>


<link type="text/css" href="<?php echo plugins_url(); ?>/blogmutt-idea-generator/assets/css/jquery.timepicker.css" rel="stylesheet"/>
<link type="text/css" href="<?php echo plugins_url(); ?>/blogmutt-idea-generator/assets/css/style.css" rel="stylesheet"/>
<script type="text/javascript" src="<?php echo plugins_url(); ?>/blogmutt-idea-generator/assets/js/jquery.timepicker.js"></script>
<script type="text/javascript" src="<?php echo plugins_url(); ?>/blogmutt-idea-generator/assets/js/jstz-1.0.4.min.js"></script>
<script type="text/javascript" src="http://ajax.aspnetcdn.com/ajax/jquery.validate/1.11.1/jquery.validate.min.js"></script>


    <div style="display:<?php echo $homeSettingDisplayVal?>">
        <?php if(isset($pagesStatus) && $pagesStatus=='UpdateDone'){ ?>
        <!--        <div class="updated settings-error"><p><strong>Site Pages already created for you.</strong></p></div>-->
        <div id="message" class="updated below-h2">
            <p>Settings updated successfully.         
            </p>
        </div>
        <?php }else if(isset($pagesStatus) && $pagesStatus=='EmailProblem'){ ?>
            <div id="message" class="updated below-h2" style="color: red;">
                <p>Email Address has already been taken. Please choose another one.         
                </p>
            </div>
        <?php } ?>
        <form id="testForm" name="testForm" method="post">
            <table>
                <tr>
                    <td>
                        <h3 class="title">Keywords</h3>
                            
                    </td>
                </tr>
                <tr>
                    <td colspan="2">
                        <p>
                            Fill in the fields with keywords (preferably nouns) that you'd like to write about, and each week we'll send you an email with relevant blog posts!
                        </p>

                    </td>
                </tr>
                <tr>
                    <td style="vertical-align: top;padding-top:20px;float:right;padding-right:10px">
                        <div class="circle-icon" onmousemove="$('.tooltip-content').css('display','block')" onmouseout="$('.tooltip-content').css('display','none')"> ? </div> 
                        
                    </td>
                    <td style="padding-top:20px">
                        <div id="keywordsTextInputDiv">
                            <?php
                                if($request=="update"){
                                    for($i=0;$i<$keywordsCount;$i++){
                                      $val = $keywords[$i]["keyword"];
                                      $kId = $keywords[$i]["id"];
                                      $req = "";
                                      if($i == 0)
                                          $req = "required";
                             ?>
                                <input type="text" name="customer[keywords_attributes][][keyword]" placeholder="Keyword 1" class="keyword" style="height: 40px;width: 300px;margin-bottom: 15px;" value="<?php echo $val?>" <?php echo $req?>><br>
                                <input type="hidden" name="customer[keywords_attributes][<?echo $i?>][id]" value="<?php echo $kId?>"/>
                            <?php
                                    }
                                }else{  
                                ?>
                                    <input type="text" name="customer[keywords_attributes][][keyword]" placeholder="Keyword 1" class="keyword" style="height: 40px;width: 300px;margin-bottom: 5px;" required><br>
                                    <input type="text" name="customer[keywords_attributes][][keyword]" placeholder="Keyword 2" class="keyword" style="height: 40px;width: 300px;margin-bottom: 5px;"><br>
                                    <input type="text" name="customer[keywords_attributes][][keyword]" placeholder="Keyword 3" class="keyword" style="height: 40px;width: 300px;margin-bottom: 5px;"><br>
                            <?php
                                }
                                ?>
                            
                        </div>
                        <a href="#" onclick="addMoreKeywords();">Add more keywords</a>
                    </td>
                </tr>
            </table>
            <table>
                <tr>
                    <td colspan="2">
                        <h3 class="title">Contact Information & Preferences</h3>
                            
                    </td>
                </tr>
                <tr>
                    <td style="width: 173px;"> * Business Name </td>
                    <td style="padding-bottom: 10px;"> <input type="text" name="customer[business_name]" class="keyword" style="height: 40px;width: 300px;"  value="<?php echo $businessName?>" required> 
                    
                    </td>
                    
                </tr>
                
                <tr>
                    <td> * Business URL  </td>
                    <td> <input id="businessUrlTxt" type="url" name="customer[business_url]" class="keyword" style="height: 40px;width: 300px;" value="<?php echo $businessURL?>" required> 
                    <p class="description" style="padding-bottom: 10px;">e.g. http://www.yourwebsite.com</p>
                    </td>
                </tr>
                <tr>
                    <td> * Blog URL </td>
                    <td> <input id="blogUrlTxt" type="url" name="customer[blog_url]" class="keyword" style="height: 40px;width: 300px;" value="<?php echo $blogURL?>" required> 
                    <p class="description" style="padding-bottom: 10px;">e.g. http://www.yourwebsite.com/blog</p>
                    </td>
                </tr>
                <tr>
                    <td> Contact page URL </td>
                    <td> <input id="contactPageUrlTxt" type="url" name="customer[contact_page_url]" class="keyword" style="height: 40px;width: 300px;" value="<?php echo $contactPageURL?>"> 
                    <p class="description" style="padding-bottom: 10px;">e.g. http://www.yourwebsite.com/contact</p>
                    </td>
                </tr>
                <tr>
                    <td> * Email address </td>
                    <td> <input id="emailAddressTxt" name="customer[email]" type="email"  class="keyword" style="height: 40px;width: 300px;" value="<?php echo $emailAddress?>" required> </td>
                </tr>
                <tr>
                    <td style="width: 173px;padding-top:20px;padding-bottom: 10px;"> Send me weekly emails </td>
                    <td style="padding-top:20px;padding-bottom: 10px;">
                        <input value="1" type="checkbox" name="customer[time_window_weekly]" <?php echo $isWeeklyUpdateCheck?> />
                    </td>
                </tr>
                <tr>
                    <td colspan="2" style="padding-top:10px;padding-bottom:15px"> 
                        <b>BlogMutt weekly email time preferences</b>
                    </td>
                    
                </tr>
                
                <tr>
                    <td style="width: 173px;padding-bottom: 10px;"> Day of week </td>
                    <td style="padding-bottom: 10px;">
                        <select class="input-sm form-control valid" id="day-of-week" name="customer[time_window_start_day_date]" tabindex="22" style="width: 100%;float: left;height:40px;">
                                    <option value="1" style="padding: 0;">Monday</option>
                                    <option value="2">Tuesday</option>
                                    <option value="3">Wednesday</option>
                                    <option value="4">Thursday</option>
                                    <option value="5">Friday</option>
                                    <option value="6">Saturday</option>
                                    <option value="0">Sunday</option>
                                    
                            </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 173px;padding-bottom: 10px;"> Time of day </td>
                    <td style="padding-bottom: 10px;">
<!--                        <input id="basicExample2" type="text" name="customer[update_time]" class="time ui-timepicker-input" autocomplete="off" onclick="$('#basicExample2').timepicker();" style="height: 40px;width: 100%;" value="<?php echo $updateTime?>">-->
                        <select id="timeOfDayDDL" name="customer[update_time]" style="height:40px;width: 150px;">
                                <option value="12:00am">12:00am</option>
                                <option value="01:00am">1:00am</option>
                                <option value="02:00am">2:00am</option>
                                <option value="03:00am">3:00am</option>
                                <option value="04:00am">4:00am</option>
                                <option value="05:00am">5:00am</option>
                                <option value="06:00am">6:00am</option>
                                <option value="07:00am">7:00am</option>
                                <option value="08:00am">8:00am</option>
                                <option value="09:00am">9:00am</option>
                                <option value="10:00am">10:00am</option>
                                <option value="11:00am">11:00am</option>
                                <option value="12:00pm">12:00pm</option>
                                <option value="01:00pm">1:00pm</option>
                                <option value="02:00pm">2:00pm</option>
                                <option value="03:00pm">3:00pm</option>
                                <option value="04:00pm">4:00pm</option>
                                <option value="05:00pm">5:00pm</option>
                                <option value="06:00pm">6:00pm</option>
                                <option value="07:00pm">7:00pm</option>
                                <option value="08:00pm">8:00pm</option>
                                <option value="09:00pm">9:00pm</option>
                                <option value="10:00pm">10:00pm</option>
                                <option value="11:00pm">11:00pm</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td style="width: 173px;padding-bottom: 10px;"> Select time zone </td>
                    <td style="padding-bottom: 10px">
                        <?php
                        $utc = new DateTimeZone('UTC');
                        $dt = new DateTime('now', $utc);

                        echo '<select name="customer[time_zone]" id="time-zone" style="height:40px">';
                        foreach(DateTimeZone::listIdentifiers() as $tz) {
                            $current_tz = new DateTimeZone($tz);
                            $offset =  $current_tz->getOffset($dt);
                            $transition =  $current_tz->getTransitions($dt->getTimestamp(), $dt->getTimestamp());
                            $abbr = $transition[0]['abbr'];

                            echo '<option value="' .$tz. '">' .$tz. ' [' .$abbr. ' '. formatOffset($offset). ']</option>';
                        }
                        echo '</select>';
                        ?>
                    </td>
                </tr>
                
                <tr>
                    <td>  </td>
                    <td style="padding-top:20px">
                        <input type="submit" class="button-primary" value="Save" onclick="Save()" />
                    </td>
                </tr>

                
            </table>
            
            <table>
                
                
            </table>
                
            
        </form>
    </div>
    <div class="tooltip-content" style="display: none;" onmousemove="$('.tooltip-content').css('display','block')" onmouseout="$('.tooltip-content').css('display','none')">
        <p>Not sure which terms to use? Answer questions like these: How do people describe your products or services? What problems do your products or services solve? What industry keywords do you want to rank for on Google? </p>
        
    </div>
    <div id="suggestedPostDiv" style="display:<?php echo $generalSettingDisplayVal?>">
        
                <table class="wp-list-table widefat fixed pages" cellspacing="0">
                        <thead>
                            <tr>
<!--                                 <th scope="col" id="cb" class="manage-column column-cb check-column" style="">
                                     <label class="screen-reader-text" for="cb-select-all-1">Select All</label>
                                     <input id="cb-select-all-1" type="checkbox">
                                 </th>-->
                                 <th scope="col" id="title" class="manage-column column-title sortable desc" style="">
                                     <a href="#">
                                         <span>Title</span>
<!--                                        <span class="sorting-indicator"></span>-->
                                     </a>
                                 </th>
<!--                                 <th scope="col" id="author" class="manage-column column-author" style="width: 30%;">Author</th>-->
                                 
                                 <th scope="col" id="date" class="manage-column column-date sortable asc" style="width: 30%;">
                                     <a href="#">
                                         <span>Suggested Date</span>
<!--                                         <span class="sorting-indicator"></span>-->
                                     </a>
                                 </th>	
                            </tr>
                        </thead>

                        <tfoot>
                            <tr>
<!--                                    <th scope="col" class="manage-column column-cb check-column" style="">
                                        <label class="screen-reader-text" for="cb-select-all-2">Select All</label>
                                        <input id="cb-select-all-2" type="checkbox">
                                    </th>-->
                                    <th scope="col" class="manage-column column-title sortable desc" style="">
                                        <a href="#">
                                            <span>Title</span>
<!--                                            <span class="sorting-indicator"></span>-->
                                        </a>
                                    </th>
<!--                                    <th scope="col" class="manage-column column-author" style="">Author</th>-->
                                    
                                    <th scope="col" class="manage-column column-date sortable asc" style="">
                                        <a href="#">
                                            <span>Suggested  Date</span>
<!--                                            <span class="sorting-indicator"></span>-->
                                        </a>
                                    </th>	
                            </tr>
                        </tfoot>
                        
                        <tbody id="the-list">
                            
                            <?php
                                 $suggestionsCount = count($suggestions);
                                if($suggestionsCount==0){
                             ?>
                                <tr id="post-2" class="post-2 type-page status-publish hentry alternate iedit author-other level-0" valign="top">
                                   
                                   <td class="post-title page-title column-title" colspan="3">
                                       <strong>
                                           No suggested posts available.

                                       </strong>
                                       <div class="locked-info">
                                           <span class="locked-avatar"></span> <span class="locked-text"></span>
                                       </div>
                                       
                                   </td>			
                                   		
                               </tr>
                             <?
                                }else{
                                    /*
                                    echo "<pre>";
                                        print_r($suggestions);
                                    echo "</pre>";
                                     * 
                                     */
                                    for($j=0;$j<$suggestionsCount;$j++){
                                        $sId = $suggestions[$j]["id"];
                                        $suggestionTitle = $suggestions[$j]["title"];
                                        $suggestedDateRes = $suggestions[$j]["suggested_date"];
                                        $suggestedDate =   explode("T", $suggestedDateRes);
                                        if(count($suggestedDate) > 0)
                                            $suggestedDate = $suggestedDate[0];
                                        
                            ?>
                            <form id="useForm1" name="useForm1" method="post">
                                <tr id="post-2" class="post-2 type-page status-publish hentry alternate iedit author-other level-0" valign="top">
<!--                                   <th scope="row" class="check-column">
                                       <label class="screen-reader-text" for="cb-select-2">Select Sample Page</label>
                                       <input id="cb-select-2" type="checkbox" name="post[]" value="2">
                                       <div class="locked-indicator"></div>
                                   </th>-->
                                   <td class="post-title page-title column-title">
                                       <strong>
                                           <?php echo $suggestionTitle?>

                                       </strong>
                                       <div class="locked-info">
                                           <span class="locked-avatar"></span> <span class="locked-text"></span>
                                       </div>
                                       <div>
                                           <span ><a href="#" title="Edit this item" onclick="userPost1('<?php echo $sId?>');" >Use Post</a>  </span>
                                           
                                       </div>
                                       <div class="hidden" id="inline_2">
                                           <div class="post_title">Sample Page</div>
                                           <div class="post_name">sample-page</div>
                                           <div class="post_author">9</div>
                                           <div class="comment_status">open</div>
                                           <div class="ping_status">open</div>
                                           <div class="_status">publish</div>
                                           <div class="jj">14</div>
                                           <div class="mm">03</div>
                                           <div class="aa">2014</div>
                                           <div class="hh">16</div>
                                           <div class="mn">37</div>
                                           <div class="ss">09</div>
                                           <div class="post_password"></div>
                                           <div class="post_parent">0</div>
                                           <div class="page_template">default</div>
                                           <div class="menu_order">0</div>

                                       </div>
                                   </td>			
<!--                                   <td class="author column-author">
                                       <a href="edit.php?post_type=page&amp;author=9">BlogMutt Plugin</a>
                                   </td>-->

                                   <td class="date column-date">
                                       <abbr title="2014/03/14 4:37:09 PM"><?php echo $suggestedDate?></abbr>
                                   </td>		
                               </tr>       
                               
                            <?php
                                 } ?>
                                <input id="selectedPostId1" type="hidden" name="selectedPostId1"/>
                                </form>
                          <?php  } ?>
                              
                            
                        </tbody>
                    </table>
                    
                </div> 
 <style>
     .circle-icon {
        display: table-cell;
        vertical-align: middle;
        border: 3px solid #d9d9d9;
        -webkit-border-radius: 50%;
        -moz-border-radius: 50%;
        -ms-border-radius: 50%;
        -o-border-radius: 50%;
        border-radius: 50%;
        width: 1.2em;
        height: 1.2em;
        font-weight: 700;
        text-align: center;
        color: #d9d9d9;
        font-size: 28.3px;
        background: #fff;
        }
        .ui-timepicker-wrapper{
            width:26%;
        }
        
        .tooltip-content {
/*position: relative;
left: 0;
top: -1.3em;*/
position: absolute;
left: 180px !important;
top: 20.7em;
z-index: 2;
max-width: 26em;
background: #f6f6f6;
-webkit-box-shadow: 2px 3.464px 2px 0px rgba(1,2,2,0.2);
-moz-box-shadow: 2px 3.464px 2px 0px rgba(1,2,2,0.2);
box-shadow: 2px 3.464px 2px 0px rgba(1,2,2,0.2);
padding: 1em 2em 1em 2em;
}
 </style>
 <script>
     
     
     //$('#basicExample2').timepicker();
     //alert($('#basicExample2').timepicker);
     /*
     alert($('#basicExample').timepicker);
     $('#basicExample').timepicker();
     alert($('#basicExample').timepicker);
     */
    var redictToDraftPage = '<?php echo $redictToDraftPage?>'
    if(redictToDraftPage=="yes")
        window.location.href='<?php echo $url?>';
    var keywordCount = 4;
   
    function addMoreKeywords(){
        if(keywordCount < 22){
            var keywordsTextInputDiv = $("#keywordsTextInputDiv");
            var keywordsTextInputDivHTML = $("#keywordsTextInputDiv").html();
            keywordsTextInputDivHTML = keywordsTextInputDivHTML + "<input type='text' name='customer[keywords_attributes][][keyword]' placeholder='"+'Keyword ' + (keywordCount++) + "' class='keyword' style='height: 40px;width: 300px;margin-bottom: 5px;'><br>" ;
            keywordsTextInputDivHTML = keywordsTextInputDivHTML + "<input type='text' name='customer[keywords_attributes][][keyword]' placeholder='"+'Keyword ' + (keywordCount++) + "' class='keyword' style='height: 40px;width: 300px;margin-bottom: 5px;'><br>" ;
            keywordsTextInputDivHTML = keywordsTextInputDivHTML + "<input type='text' name='customer[keywords_attributes][][keyword]' placeholder='"+'Keyword ' + (keywordCount++) + "' class='keyword' style='height: 40px;width: 300px;margin-bottom: 5px;'><br>" ;
            keywordsTextInputDiv.html(keywordsTextInputDivHTML);
        }else{
            alert("You can't enter more than 21 Keywords");
        }
        //keywordCount = keywordCount + 3;
    }
    
    function Save(){
    
        
        //alert(isUrl($("#businessUrlTxt").val()));
        
        /*
        var allFieldsOk = true;
        
        if(!isUrl($("#businessUrlTxt").val())){
            //$("#testForm").submit(false);
            allFieldsOk = false;
        }
        if(!isUrl($("#blogUrlTxt").val())){
            //$("#testForm").submit(false);
            allFieldsOk = false;
        }
        if(!isUrl($("#contactPageUrlTxt").val())){
            //$("#testForm").submit(false);
            allFieldsOk = false;
        }
        if(!validateEmail($("#emailAddressTxt").val())){
           // $("#testForm").submit(false);
            allFieldsOk = false;
        }
        if(allFieldsOk){
            $("#testForm").submit(true);
        }else{
            $("#testForm").submit(false);
        }
        
        */
        
    }
    function userPost1(id){
        $("#selectedPostId1").val(id);
        //console.log($("#useForm1").serialize());
        $("#useForm1").submit();
    }
    
    //if(typeof isUrl != 'function'){
        function isUrl(s) {
            var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
            return regexp.test(s);
        }
    //}
    //if(typeof validateEmail != 'function'){
        function validateEmail(email) {
           var reg = /^([A-Za-z0-9_\-\.])+\@([A-Za-z0-9_\-\.])+\.([A-Za-z]{2,4})$/;
           return reg.test(email);
        }
    //}
    
    $(document).ready(function () {
        var tz = jstz.determine();
        
        response_text = 'No timezone found';
        
        if (typeof (tz) === 'undefined') {
            response_text = 'No timezone found';
            $("#time-zone").val("America/Denver");
        }
        else {
            response_text = tz.name(); 
            
        }
        
        var weekday=new Array(7);
        weekday[0]="Sunday";
        weekday[1]="Monday";
        weekday[2]="Tuesday";
        weekday[3]="Wednesday";
        weekday[4]="Thursday";
        weekday[5]="Friday";
        weekday[6]="Saturday";
        
        var d = new Date();
        //alert(d.getHours());
        
        var dayOfWeek = '<?php echo $dayOfWeek?>';

                 
        if(dayOfWeek!=""){
            $("#day-of-week").val(dayOfWeek);
        }
        else{
            var d = new Date();
            var currentDay = d.getDay();        
            $("#day-of-week").val(currentDay);
        }
            
        var timeZone = '<?php echo $timeZone?>';
        
        if(timeZone!=""){
            $("#time-zone").val(timeZone);
        }
        else{
            $("#time-zone").val(response_text);
            //$("#time-zone").val("America/Denver");
        }
        
        
        var updateTime = '<?php echo $updateTime?>';
        //alert(updateTime);
        //updateTime = "";
        if(updateTime==""){
            //$("#basicExample2").val("1:30pm");
            //$("#basicExample2").val(formatTime(new Date()));
            //alert(formatTime(new Date()));
            $("#timeOfDayDDL").val(formatTime(new Date()));
        }else{
            $("#timeOfDayDDL").val(updateTime);
        }
        
    //alert(formatTime(new Date()));  

        $('#testForm').validate({errorElement: 'div'});

    });
    
    var formatTime = (function () {
    function addZero(num) {
        return (num >= 0 && num < 10) ? "0" + num : num + "";
    }

    return function (dt) {
        var formatted = '';

        if (dt) {
            var hours24 = dt.getHours();
            var hours = ((hours24 + 11) % 12) + 1;
            var min = dt.getMinutes();
            var min = 00;
            //formatted = [formatted, [addZero(hours), addZero(dt.getMinutes())].join(":"), hours24 > 11 ? "pm" : "am"].join("");            
            formatted = [formatted, [addZero(hours), addZero(min)].join(":"), hours24 > 11 ? "pm" : "am"].join("");            
        }
        return formatted;
    }
})();

 </script>
     
<?php 
    //return "hello";

//}
?>
