var autopoint = 0;
var autovalue = 1440;
jQuery(document).ready(function(){
    var quetion_view = true;
    var spinindex = 0;
    var spinindex_ = 0;
     function spinstateupdate_(index){
          if (jQuery('.statecityselectlist').find('.spinstatecitystate').length > index) {
               var item = jQuery('.statecityselectlist').find('.spinstatecitystate').eq(index); 
                  item.attr('checked',false);
                  var loading = item.parent().find('.statecity_loading');
               var check = 'add';   
                 var id =  jQuery('#spinstatecity').val();
         var _val = jQuery.trim( item.val()); 
         loading.show();
          jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'newstate_feilds',
               value: id, 
               name:_val, 
               check:check
            },  
            success: function (responce) {
                 loading.hide();
                    item.attr('checked',true);
                     spinindex_ = spinindex_ + 1;
                   spinstateupdate_(spinindex_);
                    if(spinindex_ != 3 && spinindex_ != 8 && spinindex_ != 11 && spinindex_ != 12 && spinindex_ != 15 && spinindex_ != 21 && spinindex_ != 25 && spinindex_ != 34 && spinindex_ != 36 && spinindex_ != 42 && spinindex_ != 47){
                        spinindex_ = spinindex_ + 1;
                   spinstateupdate_(spinindex_);
                   } 
                    spinstatemax();
            }
        });
               
          }else{
              var t = 0;
              jQuery.each(jQuery('.statecityselectlist').find('.spinstatecitystate'),function(){
                  if(jQuery(this).is(':checked')){
                      t = t +1;
                  }
              });
              if(t > 49){
                     jQuery('.state_loading').hide();
                      spinstatemax();
              } 
          } 
    }
    function spinstateupdate(index) {
        if (jQuery('.statecityselectlist').find('.editstatecitypoint').length > index) {
            var item = jQuery('.statecityselectlist').find('.editstatecitypoint').eq(index);
              item.attr('checked',false);
            var  check = 'add';
            var name = item.val();
            var id = jQuery('.autoeditableid').val();
            var loading = item.parent().find('.statecity_loading');
            loading.show();
            jQuery.ajax({
                type: 'POST',
                url: faqspn.ajaxurl,
                data: {
                    PGNonce: faqspn.PGNonce,
                    action: 'shedule_feilds',
                    value: check,
                    name: name,
                    id: id,
                },
                success: function (responce) {
                    loading.hide();
                    item.attr('checked',true);
                   spinindex = spinindex + 1;
                   spinstateupdate(spinindex);
                   if(spinindex != 3 && spinindex != 7 && spinindex != 11 && spinindex != 13 && spinindex != 17 && spinindex != 24 && spinindex != 27 && spinindex != 34 && spinindex != 37 && spinindex != 42 && spinindex != 47){
                    spinindex = spinindex + 1;
                   spinstateupdate(spinindex);
                    spinstatemax();
                }
                }
            }); 
        } else {
             var t = 0;
              jQuery.each(jQuery('.statecityselectlist').find('.editstatecitypoint'),function(){
                  if(jQuery(this).is(':checked')){
                      t = t +1;
                  }
              });
              if(t > 49){
            jQuery('.state_loading').hide();
                      spinstatemax();
        }
    }
    }
    
    
    function spinstatemax(){
        var id = '0';
        if(jQuery('#spinstatecity').length > 0){
            id = jQuery('#spinstatecity').val();
        }
        if(jQuery('.autoeditableid').length > 0){
            id = jQuery('.autoeditableid').val();
        }
        
        jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'max_feilds',                
                value: id,
            },
            success: function (responce) {
                
                var val = parseInt(responce);
                jQuery('.spintextdateshow').val(val);
        var index = parseInt(jQuery('.eposts_per_select').val());  
        if(val < 1){
            jQuery('.spintextdateshowcontent').hide();
        }else{
            index = index * 24;
            if(val <= index){
                 index = '1';
            }else{
                 index = parseInt(val/index) + 1;
            } 
              jQuery('.spintextdateshowcontent').text(val + ' Total pages to post with calculation project with be completed '+ index +' days');
             jQuery('.spintextdateshowcontent').show();
        }  
            }
        });
         
    } 
    
    if(jQuery('.spintextdateshow').length > 0){
        var val = parseInt(jQuery('.spintextdateshow').val());
        var index = parseInt(jQuery('.eposts_per_select').val());  
        if(val < 1){
            jQuery('.spintextdateshowcontent').hide();
        }else{
            index = index * 24;
            if(val <= index){
                 index = '1';
            }else{
                 index = parseInt(val/index) + 1;
            } 
              jQuery('.spintextdateshowcontent').text(val + ' Total pages to post with calculation project with be completed '+ index +' days');
             jQuery('.spintextdateshowcontent').show();
        }
      
    }
    
    jQuery('.editprojectName').keyup(function () {
         jQuery('.projectnameerror').text('');
           jQuery('.editprojectName').removeClass('error_project_name');
    });
    jQuery('.editprojectName').focusout(function () {
        jQuery('.projectload').show();
        var value = jQuery(this).val();
        var id = jQuery('.autoeditableid').val();
        jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'check_project_feilds',
                value: value,
                id:id
            },
            success: function (responce) {
                   jQuery('.projectload').hide();
if('project_not_exist' == responce){
    jQuery('.projectnameerror').text('');
     jQuery('.editprojectName').removeClass('error_project_name');
}else{
     jQuery('.projectnameerror').text('Project Name already exist.');
       jQuery('.editprojectName').addClass('error_project_name');
}
            }
        });
    });
    
    
    jQuery('.newprojectName').keyup(function () {
         jQuery('.projectnameerror').text('');
           jQuery('.newprojectName').removeClass('error_project_name');
    });
    jQuery('.newprojectName').focusout(function () {
        jQuery('.projectload').show();
        var value = jQuery(this).val(); 
        jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'new_project_feilds',
                value: value, 
            },
            success: function (responce) {
                         jQuery('.projectload').hide();
if('project_not_exist' == responce){
    jQuery('.projectnameerror').text('');
     jQuery('.newprojectName').removeClass('error_project_name');
}else{
     jQuery('.projectnameerror').text('Project Name already exist.');
     jQuery('.newprojectName').addClass('error_project_name');
}
            }
        });
    });
    
    /*
      jQuery('.editslugname').keyup(function () {
           jQuery('.nameloaderrormessage').text('');
           jQuery('.editslugname').removeClass('nameloaderror');
      });
      jQuery('.editslugname').focusout(function () {
               jQuery('.nameload').show();
                 var id = jQuery('.autoeditableid').val();
                     var value = jQuery(this).val();
          jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'edit_slug_feilds',
                value: value, 
                  id:id
            },
            success: function (responce) {
                jQuery('.nameload').hide();
                if('url_not_exist' == responce){
                      jQuery('.nameloaderrormessage').text('');
           jQuery('.editslugname').removeClass('nameloaderror');
                }else{
                     jQuery('.nameloaderrormessage').text('Url already exist.');
       jQuery('.editslugname').addClass('nameloaderror');
                    
                }
            }
        });
          
      });
      jQuery('.newslugname').keyup(function () {
             jQuery('.nameloaderrormessage').text('');
           jQuery('.newslugname').removeClass('nameloaderror');
      });
      jQuery('.newslugname').focusout(function () {
               jQuery('.nameload').show();
                   var value = jQuery(this).val();
           jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'new_slug_feilds',
                value: value, 
            },
            success: function (responce) {
                     jQuery('.nameload').hide();
                      if('url_not_exist' == responce){
                         jQuery('.nameloaderrormessage').text('');
           jQuery('.newslugname').removeClass('nameloaderror');
                }else{
                      jQuery('.nameloaderrormessage').text('Url already exist.');
       jQuery('.newslugname').addClass('nameloaderror');
                }
                     
            }
        });
          
      });
    */
    
    jQuery('.spinexportdata').click(function(){
        var id = jQuery(this).val();
        jQuery(this).parent().find(".spincsvload").show();
             jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'save_file_feilds', 
                            value: id,
                        },
                        success: function (responce) {
                                jQuery('.spincsvload').hide();
                           if(responce.indexOf('file-name(-)') !=-1){
                               responce = responce.split('(-)');
                               window.location.href= responce[1]; 
                           }else{
                               alert('Cannot export Spined Project posts.');
                           }
                        }
                    });
        
    });
    jQuery('.eposts_per_select').change(function(){
        var val = parseInt(jQuery('.spintextdateshow').val());
        var index = parseInt(jQuery('.eposts_per_select').val());  
        if(val < 1){
            jQuery('.spintextdateshowcontent').hide();
        }else{
            index = index * 24;
            if(val <= index){
                 index = '1';
            }else{
                 index = parseInt(val/index) + 1;
            } 
              jQuery('.spintextdateshowcontent').text(val + ' Total pages to post with calculation project with be completed '+ index +' days');
             jQuery('.spintextdateshowcontent').show();
        }
    });
    
    jQuery('.spinstatecitystatesingle').click(function(){
         var load = jQuery(this).parent().find('.statecity_loading');
       load.show();
       var state =  jQuery(this).parent().find('.spinstatecitystate').val();     
       var id = jQuery('#spinstatecity').val();
         singleitemspan = jQuery(this);
          jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'get_new_sate_feilds',
               value: state, 
              id:id
            }, 
            success: function (responce) {
                  jQuery('.spin_pop_upstatetitle').text(state); 
                    jQuery('.spin_pop_upcontent').text('');   
                  jQuery('.spin_pop_upcontent').append(responce);      
                     jQuery('.spin_pop_upcontent').find('#statecityallselect').click(function(){
                          if (jQuery(this).is(':checked')) { 
                              jQuery('.spin_pop_upcontent').find('.statecityselectsingle').attr('checked',true);
                          }else{
                              jQuery('.spin_pop_upcontent').find('.statecityselectsingle').attr('checked',false);
            }
        }); 
                       jQuery('.spin_pop_upcontent').find('.citystatesinglestatesave').click(function(){
                        var checked = [];
                        var notchecked = [];
                        jQuery.each(jQuery('.statecityselectsingle'),function(){
                            if (jQuery(this).is(':checked')) {
                            checked.push(jQuery(this).val());
                        } else {
                            notchecked.push(jQuery(this).val());
                        }
                    });                  
                    var id = jQuery('#spinstatecity').val();
                    var name = jQuery('.citystatesinglestate').val();
                    var loading = jQuery('.bpopupload');
                    loading.show();
                    jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'single_state_feilds',
                            value: checked,
                            novalue: notchecked,
                            name: name,
                            id: id,
                        },
                        success: function (responce) {
                            jQuery('.bpopupmessage').text('Save changes successfully.');
                            loading.hide();
                            jQuery('#spin_pop_up').bPopup().close();
                            spinstatemax();
                             if(checked.length > 0){
                                singleitemspan.parent().find('input[type="checkbox"]').attr('checked',true);
                            }else{
                                  singleitemspan.parent().find('input[type="checkbox"]').attr('checked',false);
                            }
                        }
                    });
                });
                jQuery('#spin_pop_up').bPopup();
                load.hide();
       
            }
    });
       
    });
    var singleitemspan = null;
    jQuery('#btndescard').click(function(){
       jQuery('#spin_pop_discard').bPopup();
    });
    jQuery('.discardclose').click(function(){
       jQuery('#spin_pop_discard').bPopup().close();
    });
    jQuery('.statecity_ctycontent').click(function(){
       var load = jQuery(this).parent().find('.statecity_loading');
       load.show();
       var state =  jQuery(this).parent().find('.editstatecitypoint').val();     
       var id = jQuery('.autoeditableid').val();
       singleitemspan = jQuery(this);
           jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'get_sate_feilds',
               value: state, 
              id:id
            }, 
            success: function (responce) {
                  jQuery('.spin_pop_upstatetitle').text(state); 
                    jQuery('.spin_pop_upcontent').text('');   
                  jQuery('.spin_pop_upcontent').append(responce);                   
                   jQuery('.spin_pop_upcontent').find('#statecityallselect').click(function(){
                          if (jQuery(this).is(':checked')) { 
                              jQuery('.spin_pop_upcontent').find('.statecityselectsingle').attr('checked',true);
                          }else{
                              jQuery('.spin_pop_upcontent').find('.statecityselectsingle').attr('checked',false);
                          }
                   });
                    jQuery('.spin_pop_upcontent').find('.citystatesinglestatesave').click(function(){
                        var checked = [];
                        var notchecked = [];
                        jQuery.each(jQuery('.statecityselectsingle'),function(){
                            if (jQuery(this).is(':checked')) {
                            checked.push(jQuery(this).val());
                        } else {
                            notchecked.push(jQuery(this).val());
                        }
                    });
                    var id = jQuery('.autoeditableid').val();
                    var name = jQuery('.citystatesinglestate').val();
                    var loading = jQuery('.bpopupload');
                    loading.show();
                    jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'single_state_feilds',
                            value: checked,
                            novalue: notchecked,
                            name: name,
                            id: id,
                        },
                        success: function (responce) {
                            jQuery('.bpopupmessage').text('Save changes successfully.');
                            loading.hide();
                            jQuery('#spin_pop_up').bPopup().close();
                            spinstatemax();
                            if(checked.length > 0){
                                singleitemspan.parent().find('input[type="checkbox"]').attr('checked',true);
                            }else{
                                  singleitemspan.parent().find('input[type="checkbox"]').attr('checked',false);
                            }
                        }
                    });
                });
                  jQuery('#spin_pop_up').bPopup(); 
                 load.hide();
            }
        });
    });     
       

    jQuery(".hub_title").on("keyup", function(){
      jQuery(this).next().find(".len_hub_title").text(jQuery(this).val().length);
    });

    if(jQuery('.spiniframeload').length > 0){
        setInterval(function(){ 
            if(jQuery('.spiniframeloading').length > 0){
                jQuery('.spiniframeloading').eq(0).attr('src',jQuery('.spiniframeloading').eq(0).attr('data-rsrc'));
                jQuery('.spiniframeloading').eq(0).removeClass('spiniframeloading');
            } 
        }, 5800);
       
    }   
       
    jQuery('.statecity_delete').click(function () {
        var check = 'clear';
        if (confirm('Are yo sure do you want to delete?')) {
            var checkbox = jQuery(this).parent().find('.editstatecitypoint');
            var name = checkbox.val();
            var id = jQuery('.autoeditableid').val();
            var loading = jQuery(this).parent().find('.statecity_loading'); 
            loading.show();
            jQuery.ajax({
                type: 'POST',
                url: faqspn.ajaxurl,
                data: {
                    PGNonce: faqspn.PGNonce,
                    action: 'shedule_feilds',
                    value: check,
                    name: name,
                    id: id,
                },
                success: function (responce) {
                   checkbox.attr('checked',false);
                   checkbox.parent().find('.statecity_delete').hide();
                    loading.hide();
                    spinstatemax();
                }
            });
        }
    });   
    jQuery('.statecityselectsingle').live('click',function(){
        if( jQuery(this).parent().find('.statecitysingle_delete').length > 0){
             if(jQuery(this).is(':checked')){
                 jQuery(this).parent().find('.statecitysingle_delete').show();
             }else{
                 jQuery(this).parent().find('.statecitysingle_delete').hide();
             }
        }
    });
    jQuery('.statecitysingle_delete').live('click',function(){
        if (confirm('Are yo sure do you want to delete?')) {
            jQuery(this).hide();
            jQuery(this).parent().find('.statecityselectsingle').attr('checked',false);
        }
    });
    jQuery('.editstatecitypoint').click(function(){
         var check = 'clear';
           if(jQuery(this).is(':checked')){
                check = 'add';
           }else{
                check = 'clear';
           }
           var name = jQuery(this).val();
           var id = jQuery('.autoeditableid').val();
           var loading = jQuery(this).parent().find('.statecity_loading');
           var checkbox = jQuery(this);
           loading.show();
            jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'shedule_feilds',
               value: check, 
               name:name,
               id:id,
            }, 
            success: function (responce) {
                 loading.hide();
                 if( check == 'add'){
                     checkbox.parent().find('.statecity_delete').show();
                 }else{
                     checkbox.parent().find('.statecity_delete').hide();
                 }
                 spinstatemax();
            }
        });
    });
    jQuery('.spinstatecitystate').click(function(){
        var id =  jQuery('#spinstatecity').val();
         var _val = jQuery.trim( jQuery(this).val());
               var loading = jQuery(this).parent().find('.statecity_loading');
                 loading.show();
                 var check = 'clear';
         if(jQuery(this).is(':checked')){
                        check = 'add';
         }else{
                     check = 'clear';
                }
          jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'newstate_feilds',
               value: id, 
               name:_val, 
               check:check
            }, 
            success: function (responce) {
                 loading.hide();
                 spinstatemax();
         }
          });
    });
    jQuery('.seoswitch').click(function(){
         if(jQuery(this).is(':checked')){
             jQuery(this).parent().find('.slider').text('On');
         }else{
              jQuery(this).parent().find('.slider').text('Off');
         }
    });
    jQuery('.spinposttitle').keyup(function(){
       jQuery('.spinseoheading').text(jQuery('.spinposttitle').val());
    });
    jQuery('.post_to_clone').change(function(){
        if(jQuery('.error_project_name').length == 0){
            var val = jQuery(this).val();
        var parent = jQuery(this).parents('form');
        jQuery.each(jQuery(this).find('option'),function(){
            if(val == jQuery(this).val()){                
                if(parent.find('#answer').val() == ''){
                    parent.find('#answer').val(jQuery(this).text());
                   jQuery('.spinseoheading').text(jQuery(this).text());
                }
                if( parent.find('#uri').val() == ''){
                    parent.find('#uri').val(jQuery(this).attr('data-name'));
                }
                if(jQuery('.autoeditable').length!=0){
                     parent.attr('action','admin.php?page=myfaq-manage&act=edit&id='+jQuery('.autoeditableid').val());
                }else{
                     parent.attr('action','admin.php?page=myfaq-manage&act=new');
                }
               
                parent.find('.resetsubmit').trigger('click');
            }
        });
        }        
    });
     if(jQuery('.lenmetatitle').length > 0) {
         jQuery('.lenmetatitle').text(jQuery('#spintext_metatitle').val().length+'/');
         jQuery('#spintext_metatitle').keyup(function(){
              jQuery('.lenmetatitle').text(jQuery('#spintext_metatitle').val().length+'/');
         });
     }
     if(jQuery('.metapathset').length > 0) {
         jQuery('.metapathset').text(jQuery('.metasitepath').text()+jQuery('.metasitename').val()); 
         jQuery('.metasitename').keyup(function(){
               jQuery('.metapathset').text(jQuery('.metasitepath').text()+jQuery('.metasitename').val());
         });
     }
      jQuery('#editselectallstate').click(function(){
            var id = jQuery('.autoeditableid').val();
              var check = 'clear';
               jQuery('.state_loading').show();
        if (jQuery(this).is(':checked')) { 
            spinindex = 0;
            var point = 1;
            var cpoint = 0;
            check = 'add'; 
            spinstateupdate(spinindex);
               spinindex = spinindex + 1;
                   spinstateupdate(spinindex);
            
         }else{
              check = 'clear';
              jQuery('.editstatecitypoint').attr('checked',false);
                   jQuery('.state_loading').show();
                 jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'allshedule_feilds',
               value: check, 
               name:'all',
               id:id,
            }, 
            success: function (responce) {                  
                     jQuery('.state_loading').hide();
                     spinstatemax();
            }
        });
         } 
    });
     if(jQuery('#spintext_metadesc').length > 0) {
           jQuery('.lenmetadesc').text( jQuery('#spintext_metadesc').val().length+'/');
             jQuery('#spintext_metadesc').keyup(function(){
              jQuery('.lenmetadesc').text(jQuery('#spintext_metadesc').val().length+'/');
         });
     }
     if(jQuery('.post_to_clone').length > 0) {
        var val = jQuery('.post_to_clone').val();
        var parent = jQuery('.post_to_clone').parents('form');
        jQuery.each(jQuery(this).find('option'), function () {
             if(val == jQuery(this).val()){                
                if(parent.find('#answer').val() == ''){
                    parent.find('#answer').val(jQuery(this).text());
                       jQuery('.spinseoheading').text(jQuery(this).text());
                }
                if( parent.find('#uri').val() == ''){
                    parent.find('#uri').val(jQuery(this).attr('data-name'));
                }
                
            }
        });
    }
    if(jQuery('.sc_time_stop').length > 0){
        var time = jQuery('.sc_time_stop').val();
        time = time.split(' ');
        if(time.length > 1){
            if(time[1].trim() == 'PM'){
                time = jQuery.trim(time[0]).split(':');
                  time[0] = parseInt(time[0]);
                if(time[0]<12)time[0] = time[0] + 12;
            }else{
                 time = jQuery.trim(time[0]).split(':');
                  time[0] = parseInt(time[0]);
            }
        }else{
              time = jQuery.trim(time[0]).split(':');
                  time[0] = parseInt(time[0]);
        }
         if(time.length > 1){
           time= ( time[0] * 60) +  parseInt(time[1])
        }else{
          time =  time[0] * 60;
        }
        if(time < 1){
            autovalue = 1440;
        }else{
            autovalue = time;
        }
        time = jQuery('.sc_time_start').val();
        time = time.split(' ');
        if(time.length > 1){
            if(time[1].trim() == 'PM'){
                time = jQuery.trim(time[0]).split(':');
                time[0] = parseInt(time[0]);
                if(time[0]<12)time[0] = time[0] + 12;
            }else{
                 time = jQuery.trim(time[0]).split(':');
                  time[0] = parseInt(time[0]);
                  if(time[0] > 11){
                      time[0] = 0;
            }
            }
        }else{
               time = jQuery.trim(time[0]).split(':');
               time[0] = parseInt(time[0]);
        } 
        if(time.length > 1){ 
          time =  (time[0] * 60) +  parseInt(time[1])
        }else{
         time =   time[0] * 60;
        }
        autopoint = time;
            } 
    var citystateline = 1; 
    var wrapper         = jQuery(".input_fields_wrap"); 
    jQuery('.citystate_button').click(function (e) {
        citystateline++;
        var item = jQuery(this).parent().find('.citystateload');
        item.show();
        jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'save_feilds',
                'city': jQuery('.citystate_city').val(),
                'state': jQuery('.citystate_state').val(),
                'map': jQuery('.citystate_iframe').val(),
            },
            beforeSend: function () {
            },
            success: function (responce) {
                item.hide();
                if (responce == 'addcitystatesuccess') {
                    jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'list_feilds',
                        },
                        beforeSend: function () {
                        },
                        success: function (responce) {
                            jQuery('.citystatepagin').children().remove();
                                jQuery('.citystatepagin').append(responce); 
                        }
                    });
                } else {
                }
            }
        });
    });
    
    jQuery('.citystatepaging').live('click',function(e){
        var val = jQuery(this).val();
        jQuery('.pg_loading').show();
           jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'list_feilds', 
                            value:val
                        },
                        success: function (responce) {
                             jQuery('.pg_loading').hide();
                            jQuery('.citystatepagin').children().remove();
                                jQuery('.citystatepagin').append(responce);                         
                        }
                    });
    });
    jQuery('.citystatepagepaging').live('click',function(e){
        var val = jQuery(this).val();
          jQuery('.pg_loading').show();
          var id = jQuery('.autoeditableid').val();
           jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'page_feilds', 
                            value:val,
                            id:id
                        },
                        success: function (responce) {
                                jQuery('.pg_loading').hide();
                            jQuery('.spinedpageslist').children().remove();
                                jQuery('.spinedpageslist').append(responce);                         
                        }
                    });
    });
    
    jQuery('.citystateremove_field').live('click',function(e){
        e.preventDefault();
        var parent = jQuery(this).parents('.citystatetr');
          jQuery.ajax({
            type: 'POST',
            url: faqspn.ajaxurl,
            data: {
                PGNonce: faqspn.PGNonce,
                action: 'delete_feilds',
                'value': parent.attr('id'),
            },
            beforeSend: function () {
            },
            success: function (responce) {
                if (responce == 'deletecitystatesuccess') { 
                    jQuery.ajax({
                        type: 'POST',
                        url: faqspn.ajaxurl,
                        data: {
                            PGNonce: faqspn.PGNonce,
                            action: 'list_feilds', 
                        },
                        beforeSend: function () {
                        },
                        success: function (responce) {
                            jQuery('.citystatepagin').children().remove();
                                jQuery('.citystatepagin').append(responce);                         
                        }
                    });
                } else {
                        
                    }
                }
            });
        
    
    })
    
    if(jQuery('#newselectallstate').length > 0) {
        jQuery('#newselectallstate').click(function () {
            var loading = jQuery('.state_loading');
            loading.show();
            if (jQuery(this).is(':checked')) {
                spinindex_ = 0;
                spinstateupdate_(spinindex_);
                spinindex_ = spinindex_ + 1;
                spinstateupdate_(spinindex_);
            } else {
                var id = jQuery('#spinstatecity').val();
                jQuery('.spinstatecitystate').attr('checked', false);
                jQuery.ajax({
                    type: 'POST',
                    url: faqspn.ajaxurl,
                    data: {
                        PGNonce: faqspn.PGNonce,
                        action: 'new_delete_feilds',
                        value: id,
                    },
                    success: function (responce) {
                        loading.hide();
                        spinstatemax();
                    }
                });
            }
        });
    }
        
    if(jQuery('#btnSpiningq').length != 0){
        jQuery('#btnSpiningq').click(function(){
            quetion_view = true;
            jQuery('.confidence_level').val('medium');
            jQuery('.spintax_format').val('{|}');
            jQuery('.faqs_title').text('Question Spin Convertor');
             jQuery('.faq-spincontent').val(jQuery('#question').val());
             jQuery('.faq-spinvcontent').val('');
             jQuery('.faq-spinoption').attr('checked',false);
             jQuery('#faqs_do_spin').bPopup();             
        });
        jQuery('#btnSpininga').click(function(){
            quetion_view = false;
             jQuery('.confidence_level').val('medium');
            jQuery('.spintax_format').val('{|}');
             jQuery('.faqs_title').text('Answer Spin Convertor');
             jQuery('.faq-spincontent').val(jQuery('#answer').val());
             jQuery('.faq-spinvcontent').val('');
             jQuery('.faq-spinoption').attr('checked',false);
             jQuery('#faqs_do_spin').bPopup();             
        });
        
        jQuery('.faq_splitclose').click(function(){             
            jQuery("#faqs_do_spin").bPopup().close();
        });
        
        jQuery('.faq_splitsubmit').click(function(){
            jQuery("#faqs_do_spin").bPopup().close();
            if(quetion_view){
                 jQuery('#question').val(jQuery('.faq-spinvcontent').val()); 
            }else{
                 jQuery('#answer').val(jQuery('.faq-spinvcontent').val()); 
            }
           
        });
        
        jQuery('.faq_spinbutton').click(function () {
            var option = [];
            var select = [];
            jQuery.each(jQuery('#faqs_do_spin').find('.faq-spinoption:checked'), function () {
                option.push(jQuery(this).val());
            }); 
             jQuery('.woo_do_msg').text('');
            jQuery.ajax({
                type: 'POST',
                url: faqspn.ajaxurl,
                data: {
                    PGNonce: faqspn.PGNonce,
                    action: 'get_feilds',
                    'option': option,
                    'value': jQuery('.faq-spincontent').val(),
                    'confidence_level':jQuery('.confidence_level').val(),
                    'spintax_format':jQuery('.spintax_format').val(),
                    'protected_terms':jQuery('.protected_terms').val(),
                },
                beforeSend: function () {
                    jQuery('.woodoload').show();
                },
                success: function (responce) {
                    jQuery('.woodoload').hide(); 
                    responce = JSON.parse(responce); 
                    if (responce[0]) {
                          jQuery('.faq-spinvcontent').val('');
                          jQuery('.faq-spinvcontent').val(decodeURIComponent(responce[1]));
                           
                    } else {//                        
                        jQuery('.woo_do_msg').append('<div class="errorval">' + responce[1] + '</div>');
                    }
                }
            });
        });
        
    }
    if(jQuery('.test_sr_connection').length > 0) {
        jQuery('.test_sr_connection').click(function (e) {
            e.preventDefault();
            jQuery.ajax({
                type: 'POST',
                url: faqspn.ajaxurl,
                data: {
                    PGNonce: faqspn.PGNonce,
                    action: 'test_sr_connection',
                    email_address: jQuery("input#spin_email").val(),
                    api_key: jQuery("input#splin_api_key").val()
                },
                beforeSend: function () {
                    jQuery('.sr_settings_block .sr_settings_content span.status').empty().hide(); 
                    jQuery('.woodoload').show();
                },
                success: function (responce) {
                    jQuery('.sr_settings_block .sr_settings_content span.status').show(); 
                    jQuery('.woodoload').hide(); 
                    
                    responce = JSON.parse(responce);

                    if (responce[0]) {
                        jQuery('.sr_settings_block .sr_settings_content span.status').append('<div class="errorval">' + responce[1] + '</div>');
                    } else {                    
                        jQuery('.sr_settings_block .sr_settings_content span.status').append('<div class="errorval">' + responce[1] + '</div>');
                    }
                }
            });
        });
    }
});