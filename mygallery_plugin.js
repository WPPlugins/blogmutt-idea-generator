
(function(){
    pluginData = {
        news_link: (typeof getUrlVars()["news_link"] == 'undefined' )?"":getUrlVars()["news_link"]
    }
    tinymce.PluginManager.add('my_mce_button', function( editor, url ) {
		editor.on('keyup', function(e) {
                    if(!isDirty){
                            isDirty = true;
                        }
                });
                
                editor.addButton( 'blogmutt_suggestedpost_button', {
                    title : 'BlogMutt idea generator settings', // title of the button
                    //image : '../wp-includes/images/smilies/icon_mrgreen.gif',  // path to the button's image
                    image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/BlogMutt.png',  // path to the button's image
                    onclick : function() {
                                                
                                                var data = {
                                                        action: 'check_setting',
                                                        post_var: 'this will be echoed back'
                                                };
                                                 jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                     $("#TB_window").show();
                                                     $("#TB_overlay").show();
                                                     $("#TB_overlay").css("opacity","0.7");
                                                     //alert(response);
                                                     if(response!=""){
                                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                                            W = W - 80;
                                                            H = H - 84;
                                                            tb_show( 'BlogMutt Suggested Posts', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=mygallery-form' );
                                                     }else{
                                                            var r=confirm("Please define settings for BlogMutt plugin first. Do you want to define settings now?");
                                                            if (r==true)
                                                            {
                                                               //alert(blogmutt.plugin.siteUrl);
                                                               window.location.href = blogmutt.plugin.siteUrl + "/wp-admin/options-general.php?page=blogmutt_setting_page";
                                                            }
                                                     }
                                                 });
                                                    
                                                
                                                
					}
				
                });
                editor.addButton( 'blogmutt_randompost_button', {
                                        title : 'Generate new blog idea', // title of the button
					//image : '../wp-includes/images/smilies/bulb.png',  // path to the button's image
                                        image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/bulb.png',  // path to the button's image
                                        
					onclick : function() {
						// triggers the thickbox
                                                
                                                //
                                                //$("#TB_window").hide();
                                                //$("#TB_overlay").css("opacity","0");
                                                $("#TB_overlay111").show();
                                                $("#TB_load111").show();
                                                //tb_show("we are working");
                                                //$("#TB_overlay").show();
                                                
                                                //$("#TB_window").show();
                                                $("#TB_overlay111").show();
                                                $("#TB_overlay111").css("opacity","0.4");
                                                $("#TB_load111").css("padding-top","13px");
                                                
                                                var data = {
                                                        action: 'check_setting',
                                                        post_var: 'this will be echoed back'
                                                };
                                            
                                                 jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                     //alert(response);
                                                            if(response!=""){
                                                                       var data = {
                                                                   action: 'test_response',
                                                                   post_var: 'this will be echoed back'
                                                           };
                                                           tinyMCE.execCommand("mceRepaint");
                                                           $.ajax
                                                            ({
                                                                type: "POST",
                                                                //the url where you want to sent the userName and password to
                                                                url: "../wp-admin/admin-ajax.php",
                                                                dataType: 'json',
                                                                async: false,
                                                                //json object to sent to the authentication url
                                                                data: data,
                                                                success: function (response) {
                                                                    if(typeof response.title == "undefined"){
                                                                         $("#TB_load111").hide();
                                                                    }else{
                                                                        //$("#TB_window").hide();
                                                                        $("#TB_load111").hide();

                                                                        $("#TB_overlay111"). css("opacity","0");
                                                                        $("#TB_overlay111").removeAttr("class");
                                                                        $("#TB_overlay111").hide();
                                                                        var val = true;
                                                                        if(($("#content_ifr").contents().find("#tinymce").html().length > blogmutt.plugin.suggestedDescriptionLengt) && blogmutt.plugin.suggestedDescriptionLengt > 0){
                                                                            var r=confirm("Are you sure you want to change the content of post composition form?");
                                                                            if (r==true)
                                                                            {
                                                                                val = true;
                                                                            }else{
                                                                                val = false;
                                                                            }
                                                                        }
                                                                        if(val){
                                                                            //var randomPostArr = randomPost.split('[title]');
                                                                            var title = response.title;
                                                                            cta = ((response.cta !="" && response.cta != null) ? response.cta+ "</br>" + "</br>" : "");
                                                                            blogmutt_promo_text = "<span style='color:#666666;font-size:11px;display: block;padding-top:11px;font-style:italic !important'>" + response.blogmutt_promo_text +"</span>";
                                                                            var finalContent = response.content+"<br>  <br>"+cta+ blogmutt_promo_text;
                                                                            
                                                                            $("#title").val(title);
                                                                            $("#content_ifr").contents().find("#tinymce").html(finalContent);
                                                                            $("#title").focus();
                                                                            pluginData.news_link = response.news_link;
                                                                            //$("#content_ifr").contents().find("#tinymce").focus();
                                                                            
                                                                            blogmutt.plugin.suggestedDescriptionLengt = $("#content_ifr").contents().find("#tinymce").html().length;
                                                                            //methodsForSelectionKeyWordOnLoad();
                                                                        }
                                                                        tinymce.execCommand('mceRemoveEditor', false, "my_mce_button");
                                                                        tinymce.execCommand('mceAddEditor', false, "my_mce_button");
                                                                    }
                                                                }
                                                            })
                                                          /* jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                                var randomPost = response;
                                                                if(randomPost.length > 0){
                                                                   
                                                                   //$("#TB_window").hide();
                                                                    $("#TB_load111").hide();
                                                                    
                                                                    $("#TB_overlay111"). css("opacity","0");
                                                                    $("#TB_overlay111").removeAttr("class");
                                                                    $("#TB_overlay111").hide();
                                                                    var val = true;
                                                                   if(($("#content_ifr").contents().find("#tinymce").html().length > blogmutt.plugin.suggestedDescriptionLengt) && blogmutt.plugin.suggestedDescriptionLengt > 0){
                                                                       var r=confirm("Are you sure you want to change the content of post composition form?");
                                                                       if (r==true)
                                                                       {
                                                                           val = true;
                                                                       }else{
                                                                           val = false;
                                                                       }
                                                                   }
                                                                   if(val){
                                                                       var randomPostArr = randomPost.split('[title]');
                                                                       var title = randomPostArr[0];
                                                                       var finalContent = randomPostArr[1];
                                                                       $("#title").val(title);
                                                                       $("#content_ifr").contents().find("#tinymce").html(finalContent);
                                                                       $("#title").focus();
                                                                       //$("#content_ifr").contents().find("#tinymce").focus();
                                                                       
                                                                       blogmutt.plugin.suggestedDescriptionLengt = $("#content_ifr").contents().find("#tinymce").html().length;
                                                                       methodsForSelectionKeyWordOnLoad();
                                                                   }
                                                                   tinyMCE.execCommand("mceRepaint");
                                                                }
                                                           });*/
                                                     }else{
                                                         $("#TB_load111").hide();
                                                                    
                                                        $("#TB_overlay111"). css("opacity","0");
                                                        $("#TB_overlay111").removeAttr("class");
                                                        $("#TB_overlay111").hide();
                                                            var r=confirm("Please define settings for BlogMutt plugin first. Do you want to define settings now?");
                                                            if (r==true)
                                                            {
                                                               //alert(blogmutt.plugin.siteUrl);
                                                               window.location.href = blogmutt.plugin.siteUrl + "/wp-admin/options-general.php?page=blogmutt_setting_page";
                                                            }
                                                     }
                                                 });
                                                
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						//tb_show( 'BlogMutt Random Posts', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=mygallery-random-form' );
                                                
                                                
					}
                });
                
                editor.addButton( 'blogmutt_search_button', {
                                        //title : (pluginData.news_link !="" && $("#title").val()!="") ? 'Search Google News about this keyword.' : "To use this Google News search tool, first select an idea using the Light Bulb icon.", // title of the button
                                        title : 'Search Google News about this keyword.', // title of the button
					//image : '../wp-includes/images/smilies/bulb.png',  // path to the button's image
                                        image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/search_icon.png',  // path to the button's image
					onclick : function() {
                                                // triggers the thickbox
                                                //alert(blogmutt.plugin.keyword);
                                                //alert($("#mce-widget").attr("aria-label"));
                                           if(pluginData.news_link !="" && $("#title").val()!=""){
                                               var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/
                                               if(regexp.test(decodeURIComponent(pluginData.news_link)))
                                                window.open(decodeURIComponent(pluginData.news_link),"_blank");
                                               else
                                                   alert("Something went wrong please try again later.");
                                           }    
                                            else
                                                alert("To use this Google News search tool, first select an idea using the Light Bulb icon.");
                                                
					}
                                        
                                       
                });
                
                 editor.addButton( 'blogmutt_separator_button', {
                                    
                                        title : "",
					//image : '../wp-includes/images/smilies/bulb.png',  // path to the button's image
                                        image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/sep-icon.png',  // path to the button's image
                                        style: 'color:red',
					onclick : function() {
                                             
                                                
					}
                                        
                                       
                });
               
                
	});
        
    /*
	// creates the plugin
	tinymce.create('tinymce.plugins.mygallery', {
		// creates control instances based on the control's id.
		// our button's id is "mygallery_button"
		createControl : function(id, controlManager) {
                    alert("createControl");
			if (id == 'mygallery_button') {
				// creates the button
				var button = controlManager.createButton('mygallery_button', {
					title : 'BlogMutt idea generator settings', // title of the button
					//image : '../wp-includes/images/smilies/icon_mrgreen.gif',  // path to the button's image
                                        image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/BlogMutt.png',  // path to the button's image
                                        
					onclick : function() {
                                            
                                                //jQuery("#suggestedPostDiv").css("display","block");
						// triggers the thickbox
                                                //alert("contentFromService Length" + blogmutt.plugin.descriptionLength);
                                                //alert("tinymce Length" +  $("#content_ifr").contents().find("#tinymce").html().length);
                                                //alert(($("#content_ifr").contents().find("#tinymce").html().length > blogmutt.plugin.descriptionLength) && blogmutt.plugin.descriptionLength > 0);
                                                
                                                var data = {
                                                        action: 'check_setting',
                                                        post_var: 'this will be echoed back'
                                                };
                                                 jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                     //alert(response);
                                                     if(response!=""){
                                                            var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
                                                            W = W - 80;
                                                            H = H - 84;
                                                            tb_show( 'BlogMutt Suggested Posts', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=mygallery-form' );
                                                     }else{
                                                            var r=confirm("Please define settings for BlogMutt plugin first. Do you want to define settings now?");
                                                            if (r==true)
                                                            {
                                                               //alert(blogmutt.plugin.siteUrl);
                                                               window.location.href = blogmutt.plugin.siteUrl + "/wp-admin/options-general.php?page=blogmutt_setting_page";
                                                            }
                                                     }
                                                 });
                                                    
                                                
                                                
					}
				});
				return button;
			}
                        if (id == 'mygallery_button1') {
				// creates the button
				var button = controlManager.createButton('mygallery_button1', {
					title : 'Generate new blog idea', // title of the button
					//image : '../wp-includes/images/smilies/bulb.png',  // path to the button's image
                                        image : '../wp-content/plugins/blogmutt-idea-generator/assets/img/bulb.png',  // path to the button's image
                                        
					onclick : function() {
						// triggers the thickbox
                                                
                                                
                                                var data = {
                                                        action: 'check_setting',
                                                        post_var: 'this will be echoed back'
                                                };
                                                 jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                     //alert(response);
                                                            if(response!=""){
                                                                       var data = {
                                                                   action: 'test_response',
                                                                   post_var: 'this will be echoed back'
                                                           };
                                                           jQuery.post("../wp-admin/admin-ajax.php", data, function(response) {
                                                                var randomPost = response;
                                                                if(randomPost.length > 0){
                                                                    var val = true;
                                                                   if(($("#content_ifr").contents().find("#tinymce").html().length > blogmutt.plugin.suggestedDescriptionLengt) && blogmutt.plugin.suggestedDescriptionLengt > 0){
                                                                       var r=confirm("Are you sure you want to change the content of post composition form?");
                                                                       if (r==true)
                                                                       {
                                                                           val = true;
                                                                       }else{
                                                                           val = false;
                                                                       }
                                                                   }
                                                                   if(val){
                                                                       var randomPostArr = randomPost.split('[title]');
                                                                       var title = randomPostArr[0];
                                                                       var finalContent = randomPostArr[1];
                                                                       $("#title").val(title);
                                                                       $("#content_ifr").contents().find("#tinymce").html(finalContent);

                                                                       blogmutt.plugin.suggestedDescriptionLengt = $("#content_ifr").contents().find("#tinymce").html().length;
                                                                   }
                                                                }
                                                           });
                                                     }else{
                                                            var r=confirm("Please define settings for BlogMutt plugin first. Do you want to define settings now?");
                                                            if (r==true)
                                                            {
                                                               //alert(blogmutt.plugin.siteUrl);
                                                               window.location.href = blogmutt.plugin.siteUrl + "/wp-admin/options-general.php?page=blogmutt_setting_page";
                                                            }
                                                     }
                                                 });
                                                
						var width = jQuery(window).width(), H = jQuery(window).height(), W = ( 720 < width ) ? 720 : width;
						W = W - 80;
						H = H - 84;
						//tb_show( 'BlogMutt Random Posts', '#TB_inline?width=' + W + '&height=' + H + '&inlineId=mygallery-random-form' );
                                                
                                                
					}
				});
				return button;
			}
			return null;
		}
	});
        tinymce.PluginManager.add('mygallery', tinymce.plugins.mygallery);
        
        
        */
})()

function getUrlVars()
{
    var vars = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++)
    {
        hash = hashes[i].split('=');
        vars.push(hash[0]);
        vars[hash[0]] = hash[1];
    }
    return vars;
}
