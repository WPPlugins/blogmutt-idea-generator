<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
/*
echo "<pre>";
    print_r($_POST);
echo "</pre>";   
 * 
 */
if((!is_null($_POST) && $_POST!="") && (count($_POST) > 0)){
    $selectedPostId = $_POST["selectedPostId"];
    //echo $selectedPostId . "</br>";
   if($selectedPostId!=""){
        $title = stripslashes($_POST["posts"][$selectedPostId]["title"]);
        $content = stripslashes($_POST["posts"][$selectedPostId]["description"]);
        
       
      //  echo $title . "</br>";
       // echo $content . "</br>";
       
    //     echo $pageID;
       //header();
       //echo site_url();
        $pageID =  createPagePost1($title,$content);
       $url = site_url()."/wp-admin/post.php?post=".$pageID."&action=edit";
       
       wp_redirect($url);
    }
}

function createPagePost1($postTitle,$postContent){
    
            global $pagesArr;
            global $wpdb;

            /*
            $args = array (

                'post_type' => 'post',
                'nopaging' => true
              );

            // The Query
                $the_query = new WP_Query( $args );

                // The Loop
                if ( $the_query->have_posts() ) {
                        echo '<ul>';
                        while ( $the_query->have_posts() ) {
                                $the_query->the_post();
                                echo '<li>' . get_the_title() . '</li>';
                        }
                        echo '</ul>';
                } else {
                        // no posts found
                }
                */
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

   function ilc_admin_tabs_blogmutt( $current = 'homepage' ) {
        $tabs = array( 'general' => 'Suggested Posts', 'homepage' => 'Settings');
        echo '<div id="icon-themes" class="icon32"><br></div>';
        echo '<h2 class="nav-tab-wrapper">';
        foreach( $tabs as $tab => $name ){
            $class = ( $tab == $current ) ? ' nav-tab-active' : '';
            echo "<a class='nav-tab$class' href='?page=blogmutt_setting_page&tab=$tab'>$name</a>";

        }
        echo '</h2>';    
    }
    
    showBlogmuttUI();
    
function showBlogmuttUI(){ 
    
    $suggestions = array();
    $keywords = array();
    $keyword = "";
    $homeSettingDisplayVal = "none";
    $generalSettingDisplayVal = "";
    $settingURL = site_url() . "/wp-admin/options-general.php?page=blogmutt_setting_page";
    $options = !is_array(get_option('blogmutt-udid-options')) ? "" : get_option('blogmutt-udid-options');
       if(is_array($options)){
           $blogMuttUDID = $options["blogMuttUDID"];
           if(is_null($blogMuttUDID) || $blogMuttUDID==""){
               //wp_redirect($settingURL);
           }
               
       }else{
           $blogMuttUDID = "";
           $request = "add";
           //wp_redirect($settingURL);
       }
       $mygallery = new MyGallery();
       $urlSuggestedGet = "http://big.blogmutt.com/api/v1/posts/$blogMuttUDID";
       //echo $urlSuggestedGet;
            $response_jsonSuggestion = $mygallery->getResponseArrayFromServiceURL1($urlSuggestedGet);
            
            /*
            echo "<pre>";
                print_r($response_jsonSuggestion);
            echo "</pre>";
            */
            
            
            if(count($response_jsonSuggestion) > 0){
                $suggestions = $response_jsonSuggestion["suggestion"];
                $keywords = $response_jsonSuggestion["keywords"];
                
                $suggestionsDetail =  json_encode($suggestions) ;
                $keywordDetail =  json_encode($keywords) ;
                
                
                //echo $suggestionsDetail;
           }
           
      ?>
<script type="text/javascript">

if(typeof $ == 'undefined'){
        document.write('<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></'+'script>');
  }

</script>

<div id="mygallery-form" style="display:none">
    <table id="mygallery-table" class="form-table"> 
    
                <div id="icon-themes" class="icon32"><br></div>
                <h2 class="nav-tab-wrapper">
                    <a id="suggestedPostTab" class="nav-tab nav-tab-active" href="#" onclick="//changeTab('suggestedPostTab')">Suggested Posts</a>
<!--                    <a id="settingsTab" class="nav-tab " href="<?php echo $settingURL?>" onclick="//changeTab('settingsTab')">Settings</a>-->
                    
                    <div style="float:right">
                        <a href="<?php echo $settingURL?>">Go to Settings</a>
                        
                    </div>
                </h2>
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
                            <form id="useForm" name="useForm" method="post">
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
                                $suggestionsCount = count($suggestions);
                                if($suggestionsCount==0){
                                }else{
                                    //echo "else";
                                    for($j=0;$j<$suggestionsCount;$j++){
                                        $sId = $suggestions[$j]["id"];
                                        $suggestionTitle = $suggestions[$j]["title"];
                                        $suggestionDescription =  $suggestions[$j]["content"];
                                        $suggestedDateRes = $suggestions[$j]["suggested_date"];
                                        $suggestedDate =   explode("T", $suggestedDateRes);
                                        if(count($suggestedDate) > 0)
                                            $suggestedDate = $suggestedDate[0];
                                        
                            ?>
                            
                                <tr id="post-2" class="post-2 type-page status-publish hentry alternate iedit author-other level-0" valign="top">
<!--                                   <th scope="row" class="check-column">
                                       <label class="screen-reader-text" for="cb-select-2">Select Sample Page</label>
                                       <input id="cb-select-2" type="checkbox" name="post[]" value="2">
                                       <div class="locked-indicator"></div>
                                   </th>-->
                                   <td class="post-title page-title column-title">
                                       <strong>
                                           <?php echo $suggestionTitle?>
                                        <input type="hidden" name="posts[<?php echo $sId;?>][title]" value="<?php echo addslashes($suggestionTitle)?>"/>
                                        <input type="hidden" name="posts[<?php echo $sId;?>][description]" value="<?php echo htmlspecialchars(addslashes($suggestionDescription))?>"/>
                                       </strong>
                                       <div class="locked-info">
                                           <span class="locked-avatar"></span> <span class="locked-text"></span>
                                       </div>
                                       <div>
                                           <span><a href="#" title="Edit this item" onclick="userPost('<?php echo $sId?>');" >Use Post</a>  </span>
                                           
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
                                 } 
                                }
                            } ?>
                               <input id="selectedPostId" type="hidden" name="selectedPostId"/>
                            </form>
                        </tbody>
                    </table>
                </div>                
 
   
<div id="TB_overlay111" disabled="disabled" style="background: #000;
opacity: 0.7;
filter: alpha(opacity=70);
position: fixed;
top: 0;
right: 0;
bottom: 0;
left: 0;
z-index: 100050;
display:none"></div>
<div id="TB_load111" style="padding-top: 13px;position: fixed;
display: none;
z-index: 103;
top: 50%;
left: 50%;
background-color: #E8E8E8;
border: 1px solid #555;
margin: -45px 0 0 -125px;
padding: 40px 15px 15px;"><div id="pleaseWait" style="padding-top:0px">Please wait</div><img src="<?php echo site_url()?>/wp-includes/js/thickbox/loadingAnimation.gif" width="208"></div>

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
 </style>
 <script>
     var isDirty = false;
     $(':input').not('input[name="sid"], input[name="token"], input[name="tw_phone_number"]').change(function(){
         if(!isDirty){
            isDirty = true;
         }
    });
    
     var blogmutt = {};

        blogmutt.plugin = {
            suggestedDescriptionLengt : 0,
            randomDescriptionLengt : 0,
            keyword : "",
            siteUrl : ""
        }
        
        blogmutt.plugin.siteUrl  = '<?php echo site_url()?>';
        
        //var tOut = setTimeout(function(){methodsForSelectionKeyWordOnLoad();clearTimeout(tOut)}, 2000);
        
        //blogmutt.plugin.keyword = '<?php echo $keyword?>';
        methodsForSelectionKeyWordOnLoad();
        //alert('<?php echo $suggestedDate?>');
     /*
     alert($('#basicExample').timepicker);
     $('#basicExample').timepicker();
     alert($('#basicExample').timepicker);
     */
    function changeTab(id){
        //
        //alert(id);
        var suggestedPostTab = $("#suggestedPostTab");
        var settingsTab = $("#settingsTab");
        var suggestedPostTabClass = $("#suggestedPostTab").attr("class");
        var settingsTabClass = $("#settingsTab").attr("class")
        
        
        var settingDiv = $("#settingDiv");
        var suggestedPostDiv = $("#suggestedPostDiv");
        
        if(id=="suggestedPostTab"){
            suggestedPostTabClass =  suggestedPostTabClass + " nav-tab-active";
            suggestedPostTab.attr("class",suggestedPostTabClass);
            //suggestedPostTab.css("display","block");
            //settingsTab.removeClass("nav-tab-active");
            settingsTab.attr("class" ,"nav-tab");
            
            suggestedPostDiv.css("display","block");
            settingDiv.css("display","none");
            //nav-tab nav-tab-active
        }else{
            //alert(settingsTabClass);
            settingsTabClass =  settingsTabClass + "nav-tab-active";
            settingsTab.attr("class",settingsTabClass);
            //settingsTab.css("display","block");
            suggestedPostTab.attr("class" ,"nav-tab");
            
            settingDiv.css("display","block");
            suggestedPostDiv.css("display","none");
        }
    }
    
    function userPost(id){
        //alert($("#useForm"));
        
        var suggestedPostObj = <?php echo $suggestionsDetail?>;
        //alert(suggestedPostObj.length);
        
        //$("#title").val(title);
        
        var val = true;
        //if(($("#content_ifr").contents().find("#tinymce").html().length > blogmutt.plugin.suggestedDescriptionLengt) && blogmutt.plugin.suggestedDescriptionLengt > 0){
        if(isDirty){
            //alert("content changed");
            var r=confirm("Are you sure you want to change the content of post composition form?");
            if (r==true)
            {
                val = true;
            }else{
                val = false;
            }
        }
        isDirty = false;
        if(val){
            if(suggestedPostObj!= null && suggestedPostObj.length > 0){
                for(var i=0;i<suggestedPostObj.length;i++){
                    var suggestedPost = suggestedPostObj[i];
                    var title = "";
                    var description = "";
                    var news_link = "";
                    var cta = "";
                    var blogmutt_promo_text = "";
                    var finalContent = "";
                    if(suggestedPost.id==id){
                        
                        console.log(suggestedPost);
                        $("#content_ifr").contents().find("#tinymce").html("");
                        title = suggestedPost.title;
                        description = suggestedPost.content + "</br>" + "</br>" ;
                        blogmutt.plugin.keyword = suggestedPost.post_key_word;
                        blogmutt.plugin.keyword = suggestedPost.news_link;
                        //alert(blogmutt.plugin.keyword);
                        //alert(description);
                        news_link = "<span style='padding-top:10px;padding-bottom:10px;display:block'>" + suggestedPost.news_link + "</br>" + "</br>" + "</span>";
                        
                        //alert(news_link);
                        
                        if(suggestedPost.cta!="")
                            cta = suggestedPost.cta + "</br></br>";
                        
                        //alert(cta)
                        blogmutt_promo_text = suggestedPost.blogmutt_promo_text;
                        //alert(blogmutt_promo_text);
                        //finalContent = description + news_link + cta + "<span style='color:#666666;font-size:11px;display: block;padding-top:11px;font-style:italic !important'>" +  blogmutt_promo_text + "</span>";
                        finalContent = description + cta + "<span style='color:#666666;font-size:11px;display: block;padding-top:11px;font-style:italic !important'>" +  blogmutt_promo_text + "</span>";

                        //alert(finalContent);
                        $("#title").val(title);
                        $("#content_ifr").contents().find("#tinymce").html(finalContent);
                        $("#title").focus();

                        pluginData.news_link = suggestedPost.news_link;
                        blogmutt.plugin.suggestedDescriptionLengt = $("#content_ifr").contents().find("#tinymce").html().length;
                        //alert(blogmutt.plugin.suggestedDescriptionLengt);
                        //alert(blogmutt.plugin.suggestedDescriptionLengt);
                        tb_remove();
                        $("#TB_window").hide();
                        $("#TB_overlay"). css("opacity","0");
                        $("#TB_overlay").removeAttr("class");
                        $("#TB_overlay").hide();
                        //console.log(suggestedPost.cta);
                    }

                }
            }
        }
        
       
        //$("#content_ifr").contents().find("#tinymce p").html("");
        //$("#content_ifr").contents().find("#tinymce p").html(description);
        
    }
   
   function methodsForSelectionKeyWordOnLoad(){
   
   //alert("methodsForSelectionKeyWordOnLoad");
   /*
    var suggestedKeywordObj = <?php echo $keywordDetail?>;
       //alert(suggestedKeywordObj.length);
            if(suggestedKeywordObj.length > 0){
                for(var i=0;i<suggestedKeywordObj.length;i++){
                    var suggestedKeyword = suggestedKeywordObj[i];
                    //alert(suggestedKeyword.keyword);
                    //alert($("#title").val().indexOf(suggestedKeyword.keyword.toString()));
                    if($("#title").val().indexOf(suggestedKeyword.keyword.toString()) != -1)
                        blogmutt.plugin.keyword = suggestedKeyword.keyword;
                    

                }
            }
        */
       var suggestedPostObj = <?php echo $suggestionsDetail?>;
       //alert(suggestedKeywordObj.length);
            if(suggestedPostObj!= null && suggestedPostObj.length > 0){
                for(var i=0;i<suggestedPostObj.length;i++){
                    var suggestedKeyword = suggestedPostObj[i];
                    //alert(suggestedKeyword.keyword);
                    //alert($("#title").val().indexOf(suggestedKeyword.keyword.toString()));
                    
                    //blogmutt.plugin.keyword = suggestedKeyword.news_link;
                    //alert(blogmutt.plugin.keyword);
                    

                }
            }
   }
   
 </script>
 <style>
     .mce_mygallery_button1{
         width: 20px !important;
     }
     .mce_mygallery_button{
         margin-left:10px !important;
         width: 30px !important;
     }
 </style>    
<?php 
    //return "hello";

}
?>