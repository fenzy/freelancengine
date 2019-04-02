<?php function display_faq_manager_func() {

    global $wpdb;        
    
    $ad_table = MYFAQ_TABLE;
    $authorise = authorizemyplugin();

    if ($authorise == "Authorised!") {

        if (isset($_POST['licence'])) {

            update_option(bml_access, $_POST['bml_access']);

            update_option(bml_transid, $_POST['bml_transid']);
        }

        echo '<h3>Plugin Settings</h3>';

        echo '<form action="" method="post" style="background-color:#DFDFDF; padding:20px; border-radius:5px; width:560px;">';

        echo '<table cellspacing="10px" cellpadding="10px" style="background-color:#DFDFDF; padding:20px; border-radius:5px; width:560px;">';

        $access = get_option('bml_access');

        $trans_id = get_option('bml_transid');

        if ($access == '' && $trans_id == '') {



            echo '<tr>';

            echo '<td style="width:160px;">Access key:</td>';

            echo '<td><input  style="width:330px" type="text" name="bml_access"  /></td>';

            echo '</tr>';

            echo '<tr valign="top">';

            echo '<td scope="row">Transaction ID:</td>';

            echo '<td><input  style="width:330px" type="text" name="bml_transid"  /></tr>';

            echo '<tr valign="top">';

            echo '<td scope="row"></td>';

            echo '<td><input type="submit"  class="button" name="licence" value="Submit" /><td></tr>';
        } else {





            $authorise = authorizemyplugin();

            if ($authorise == "Authorised!") {

                echo '<tr>';

                echo '<td width="160" scope="row">Access key:</td>';

                echo '<td><input type="text" style="width:330px" name="bml_access" value="' . get_option('bml_access') . '" /></td>';

                echo '</tr>';

                echo '<tr valign="top">';

                echo '<td>Transaction ID:</td>';

                echo '<td><input style="width:330px" type="text" name="bml_transid" value="' . get_option('bml_transid') . '" />';









                echo '<br /><b>Authorised!</b></td>';
            } else {



                echo '<tr><td colspan="2"><b>' . $authorise . '</b></td></tr>';

                echo '<tr>';



                echo '<td style="width:160px;">Access key:</td>';

                echo '<td><input style="width:330px" type="text" name="bml_access"  /></td>';

                echo '</tr>';

                echo '<tr valign="top">';

                echo '<td scope="row">Transaction ID:</td>';

                echo '<td><input style="width:330px" type="text" name="bml_transid"  /></tr>';

                echo '<tr valign="top">';

                echo '<td scope="row"></td>';

                echo '<td><input type="submit" class="button" name="licence" value="Submit" /><td></tr>';
            }

            echo '</tr>';



            echo '</table>';

            echo '</form>';

            echo '<br>';
        }
    }

    if ($authorise != "Authorised!") {

        if (isset($_GET['act']) && $_GET['act'] == 'delete' && isset($_GET['id']) && is_numeric($_GET['id'])) {

            $id = $_GET['id'];
            if(trim($id) == '' || !$id){
                $id = '0';
            }
            $sql = "DELETE FROM {$ad_table} WHERE id = {$id}";
            $wpdb->query($sql);
            
              $sql = "DELETE FROM ".AUTOPOST_SPINTXT_TABLE." WHERE project_id = {$id}";
            $wpdb->query($sql);
            
              $sql = "DELETE FROM ".AUTOPOST_SCHEDULING_TABLE." WHERE project_id = {$id}";
            $wpdb->query($sql);
            
              $sql = "DELETE FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = {$id}";
            $wpdb->query($sql); 
            
            $sql = "DELETE FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = {$id}";
            $wpdb->query($sql); 
            
            $results =  $wpdb->get_results("SELECT post_id FROM ".$wpdb->prefix."postmeta WHERE meta_key='autospinproject' AND meta_value='".$id."';");
            foreach($results as $result){
                wp_delete_post($result->post_id,true);
            }
            
            $_GET['m'] = 'd';
            
        }

        if (isset($_GET['m'])) {

            switch ($_GET['m']) {
                case 'a':
                    echo '<div id="message" class="updated fade"><p><strong>Project saved successfully.</strong></p></div>';
                    break;

                case 'e':
                    echo '<div id="message" class="updated fade"><p><strong>Project updated successfully.</strong></p></div>';
                    break;

                case 'd':
                    echo '<div id="message" class="updated fade"><p><strong>Project deleted successfully.</strong></p></div>';
                    break;
                case 's':
                    echo '<div id="message" class="updated fade"><p><strong>Project started successfully.</strong></p></div>';
                    break;
                case 'p':
                    echo '<div id="message" class="updated fade"><p><strong>Project paused successfully.</strong></p></div>';
                    break;
            }
        }
?>

    <div class='wrap autopost-projects'>
            <div class="icon32" id="icon-link-manager"></div>
            <h2>Projects <br />
                <a href='admin.php?page=myfaq-manage&act=new' class='button-primary btn-add-new'>Add New</a>
            </h2>
            <style>
            
.wp-core-ui .button-pause {
    background: #c98c00 none repeat scroll 0 0;
    border-color: #aa8200 #997a00 #996000;
    box-shadow: 0 1px 0 #997f00;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #999800, 1px 0 1px #998900, 0 1px 1px #998900, -1px 0 1px #997f00;
}
.wp-core-ui .button-pause:hover {
   background: #d59d07 none repeat scroll 0 0;
    border-color: #aa8200 #997a00 #996000;
    box-shadow: 0 1px 0 #997f00;
    color: #fff;
    text-decoration: none;
    text-shadow: 0 -1px 1px #999800, 1px 0 1px #998900, 0 1px 1px #998900, -1px 0 1px #997f00;
}
            </style>
            <table class="widefat">
                <thead>
                    <tr>
                        <th>Project</th>
                        <th>Date</th>
                        <th>Total New Pages</th>
                        <th>Total Posted Pages</th>
                        <th>No. post per Hour</th>
                        <th>Status</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                
                <tbody>
                        <?php
                            $sql = "SELECT * FROM {$ad_table} ORDER BY id DESC";
                            $table = $wpdb->get_results($sql);

                            if ($table) {
                                foreach ($table as $row) { 
                                     $row->total_new  = $wpdb->get_var("SELECT count(citystateid) FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE state='0' AND projectid = '" .$row->id  . "'");
                                     $row->total_posted  = $wpdb->get_var("SELECT count(citystateid) FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE state='1' AND projectid = '" .$row->id  . "' "); 
                                     $edit_link = "admin.php?page=myfaq-manage&act=edit&id={$row->id}";
                                    $start_link = "admin.php?page=myfaq-manage&act=start&id={$row->id}";
                                    $pause_link = "admin.php?page=myfaq-manage&act=pause&id={$row->id}";
                                    $delete_link = "admin.php?page=myfaq-manager&act=delete&id={$row->id}";
                                    $addtown_link = "#";
                                    $sql = "SELECT * FROM ".AUTOPOST_SCHEDULING_TABLE." WHERE project_id = ".$row->id;
                                    $schedule = $wpdb->get_row($sql);
                                     if( $row->total_posted > 0  && $row->total_new < 1){
                                        $schedule->status = '8';
                                    }  
                                    $status = 'Pending';
                                    $editbutton = " <a href='".$edit_link."' class='button-primary' style='float: left; margin-right: 3px;'>Edit</a>";
                                    $startbutton = "<a href='{$start_link}' class='button-primary button-green'>Start</a>";

									$savebutton = "<button class='button button-primary save_btn'>Save</button>";

									$updatebutton = "<input type='submit' class='update_btn hidden' value='Update' />";

                                    $exportbutton = "<button class='button button-primary spinexportdata' value='{$row->id}' style='margin-right: 3px;'>Export</button>";



									$spinload = "<button class='no-style'><span class='spincsvload' style='display:none;'></span></button>";

									$addTownbutton = " <a href='".$addtown_link."' class='button-secondary' style='margin-right: 3px;'>Add Towns</a>";
                                    if( $schedule->status == '1'){
                                         $status = 'Started';
                                         $startbutton = "<a href='{$pause_link}' class='button-primary button-pause'>Pause</a>";
                                    }else if( $schedule->status == '5'){
                                          $status = 'Processing';
                                            $startbutton = '';
                                    }else if( $schedule->status == '8'){
                                          $status = 'Completed';
                                            $startbutton = ''; 
                                    }else{
                                         $status = 'Pending'; 
                                    }

									$schedule_form = "<form action='admin.php?page=myfaq-process' method='post'>";
									$schedule_list_act = "<input type='hidden' name='list_act' value='update' />";

									$schedule_act = "<input type='hidden' name='act' value='edit' />";
									$schedule_id = "<input type='hidden' name='id' value='".$row->id."' />";

									$project_title = "<input type='hidden' name='question' value='" .stripslashes($row->question) . "' />";
									$project_answer = "<input type='hidden' name='answer' value='" .stripslashes($row->answer) . "' />";

									$post_to_clone = "<input type='hidden' name='post_to_clone' value='" .stripslashes($row->post_to_clone) . "' />";

									$schedule_days = "<div class='inner_inputbox_wrapper group_wrap toggle_div' id='schedule_days'>";
									
									/* weekday */
									$BWeekDays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');
									$weekdays = explode(',', $schedule->weekdays);
									foreach($BWeekDays as $day) { 
										$day_ = strtolower($day);
										$check = '';
										foreach($weekdays as $weekday){
											if($weekday == $day_) {
												$check =  'checked';
											}
										}

										$day_label = "<div><label>" . substr($day, 0, 3). ":</label><input type='checkbox' " . $check . " name='schedule_". $day_ ."' value='".$day_ ."'/></div>";
										$schedule_days .= $day_label;
									}

									$schedule_days .= "</div>";
									
									/* time_to_post */
									if ($schedule->time_start)
										$time_start = $schedule->time_start;
									else
										$time_start = '12:00 AM';

									if ($schedule->time_end)
										$time_end = $schedule->time_end;
									else
										$time_end = '11:59 PM';
									$time_to_post =
									"<div class='inner_inputbox_wrapper group_wrap toggle_div' id='time_to_post'>".
										"<div id='time-range'>".
											"<label for='time_to_post' style='font-weight: bold;'>Time to post:".
											"<span style=' font-weight: normal; margin-left: 8px;'>".
												"<input style='background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;' type='text' name='schedule_time_start' value='". $time_start . "' id='sc_time_start' class='slider-time sc_time_start'/> - ".
												"<input type='text' style='background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;' name='schedule_time_stop' value='". $time_end ."' id='sc_time_stop' class='slider-time2 sc_time_stop'/><span ></span>EST".
											"</span>".
											"</label>".
										    "<div class='sliders_step1'><div class='slider-range'></div></div>".
										"</div>".
									"</div>";

									/* posts per hour */
									$pp = $schedule->random_post;

									$posts_per_hour =
									"<div class='inner_inputbox_wrapper group_wrap toggle_div' id='div_posts_per_hour'>".
									"<label for='random_posts_per_hour'>Random Posts per Hour:</label>".
									"<input type='number' name='post_min' class='post_min post_val' value='".$schedule->post_min."'>".
									"<input type='number' name='post_max' class='post_max post_val' value='".$schedule->post_max."'>".
									"<input type='hidden' name='schedule_posts_per_hour' class='eposts_per_select' value='".$schedule->random_post."'></div>";

                                    $main_hub = "<div class='inner_inputbox_wrapper group_wrap toggle_div' style='width: 120px;'><label>Go to Hub page</label><a href='".site_url().'/'.$row->main_hub_location . "' class='button-primary' style='float: left; margin-right: 3px;'>Go to Hub page</a></div>";

									$schedule_form .= $schedule_list_act . $schedule_act . $schedule_id . $project_title . $project_answer . $post_to_clone;
									$schedule_form .= $schedule_days;
									$schedule_form .= $time_to_post;
									$schedule_form .= $posts_per_hour;
                                    $schedule_form .= $main_hub;
									$schedule_form .= $updatebutton;
									$schedule_form .= "</form>";
									
                                    echo trim("
                                        <tr>
                                            <td>" . stripslashes($row->question) . "</td>
                                            <td>" . $row->datetime . "</td>
                                            <td>" . $row->total_new . "</td>
                                            <td>" . $row->total_posted . "</td>
                                            <td>" .$schedule->post_min. " - ". $schedule->post_max . "</td>
                                            <td>" . $status . "</td>
                                            <td>".$editbutton."
                                                <a href='{$delete_link}' onclick='return confirm(\"Are you sure to delete?\")' class='button-secondary'>Delete</a>
                                                " .$startbutton. $addTownbutton. $exportbutton. $savebutton. $spinload . "
                                            </td></tr>
											<tr><td colspan='7' style='border-top:1px dashed #000; border-bottom: 1px solid #000;'>".$schedule_form."</td></tr>");
                                }
                            } else {

                                echo "<tr><td colspan='3'>No Project found</td></tr>";
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        <?php
    }
}

add_action("display_faq_manager", "display_faq_manager_func");

function getlinkautodata($value,$type){
   $data = get_post_meta($value, '_fl_builder_'.$type, true);
   if(is_null($data)){
       return false;
   }else{
       return $data;
   }
}


function get_autocontent($data,$index,$options){
    global $autospinline,$autospinrow; 
    $results = array(); 
    $autospinline = $autospinrow = $results;  
     foreach($data as $key => $value){
            if($value->type == 'module' || $value->type == 'column'){
                $item = array(
                    'id' => $value->node, 
                );
                 if(isset($value->settings->type) && ( $value->settings->type == 'rich-text' || $value->settings->type == 'rich-text' || $value->settings->type == 'html' )){
                  $item['parent'] = $value->parent;  
                         $item['position'] = $value->position;  
                          $item['content'] = '';
                     if(isset($value->settings->text)){
                         $item['content'] = stripslashes($value->settings->text); 
                     }  
                     if( $value->settings->type == 'html'){
                           $item['content'] = stripslashes($value->settings->html);  
                     }
                     if(isset($options[$item['id']]) && $index){ 
                           $item['content'] = $options[$item['id']]; 
                     } 
                     if(isset($_POST['spintext_sc'.$item['id']])){
                             $item['content'] = stripcslashes($_POST['spintext_sc'.$item['id']]); 
                     }
                     $results[] = $item;
                }else{
                    if( isset($value->settings->type) && $value->settings->type == 'photo'){
                        $item = array(
                    'id' => $value->node, 
                     'position' => $value->position,
                        'parent' => $value->parent,
                            'content' => '',
                         'src' => $value->settings->photo_src
                );
                          $results[] = $item;
                    }else if($value->type == 'column'){                        
                        $autospinline[$value->node] =array('id' => $value->node,'type' => $value->type,'position' => $value->position,'parent' => $value->parent,'content' => array());
                    }
                }  
            }else if(isset($value->type) && $value->type == 'column-group' || $value->type == 'column'){
                $autospinline[$value->node] = array('id' => $value->node,'type' => $value->type,'position' => $value->position,'parent' => $value->parent,'content' => array());
            }else if(isset($value->type) &&  $value->type == 'row'){
                $autospinrow[$value->node] = array('id' => $value->node,'type' => $value->type,'position' => $value->position,'parent' => $value->parent,'content' => array());
            }
        }  
     return $results;
}

function get_autocontent_($data,$index,$options,$fill){
    foreach($fill as $key => $value){
        if($data[$value['id']]){
            $value_ = $data[$value['id']]; 
            if(isset($value_->type) && ($value_->type == 'module' || $value_->type == 'column')){
                  if($value_->settings->type == 'rich-text' || $value_->settings->type == 'rich-text'){
                       $value_->settings->text = $value['content'];  
                  }else if( $value_->settings->type == 'html'){
                         $value_->settings->html = $value['content'];  
                  }
            }
             $data[$value['id']] = $value_;
        }
    }
    return $data;
}

function get_autospinline($data) {
    global $autospinline,$autospinrow;
    foreach ($data as $data_) {
        if (isset($data_['parent']) && isset($autospinline[$data_['parent']])) {
            $autospinline[$data_['parent']]['content'][] = $data_;
        }
    }
    
    $keys = array();    
    foreach($autospinrow as $key => $autospinrow_){
         if(!isset($keys[$autospinrow_['position']])){
             $keys[$autospinrow_['position']] = array();
         }         
         $keys[$autospinrow_['position']][$key] = $autospinrow_;
    }
    ksort($keys); 
    $autospinrow = array();
    foreach($keys as $key_ => $value_){
        foreach($value_ as $nkey => $nvalue){
            $autospinrow[$nkey] = $nvalue;
        }
    }
    
    foreach ($autospinline as $key => $autospinline_) {
        if ($autospinline_['type'] == 'column') {
            if (isset($autospinline[$autospinline_['parent']])) {
                if ($autospinline_['position'] == '0') {
                    $autospinline[$autospinline_['parent']]['content'][] = $autospinline_;
                } else {
                    $autospinline[$autospinline_['parent']]['content'][$autospinline_['position']] = $autospinline_;
                }
                unset($autospinline[$key]);
            }
        }
    } 
                
    foreach($autospinline as $key => $autospinline_){
       if(isset($autospinrow[$autospinline_['parent']])){
           $autospinrow[$autospinline_['parent']]['content'][$key]= $autospinline_;
       }else{
           if(!isset($autospinrow['none'])){
               $autospinrow['none'] = array('content' => array());
           }
           $autospinrow['content'][$key]= $autospinline_;
       }
    }
    $autospinline = array();
    foreach($autospinrow as $key => $autospinrow_){
        foreach($autospinrow_['content'] as $nkey => $nvalue){
              $autospinline[$nkey] = $nvalue;
        }
    } 
    return $autospinline;
}

function get_autospin($id,$type = false,$index = 0,$mix = false) { 
    global $wpdb;
    if($type){
       $type = 'draft';
    }else{
          $type = 'data';
    }
    $data = getlinkautodata($id,$type);
    if ($data) { 
        $data = maybe_unserialize($data);
        if(!empty($data)){ 
           $options = $wpdb->get_results("SELECT * FROM ".$wpdb->prefix."options  WHERE  option_name like '_".$index."spintext_sc%'" );    
           $options_ = array();
           foreach($options as $option){
               $options_[str_replace("_".$index."spintext_sc", '', $option->option_name)] = stripcslashes($option->option_value);
           } 
           if($mix){
                 return  get_autocontent_($data,$index,$options_,$mix);
           }else{
                return  get_autocontent($data,$index,$options_);
           } 
        }        
    } else {
        return array();
    }
}

function faq_manage_func() {

    extract($_REQUEST);

    if (!isset($act) || $act == '') {

        _redirect("admin.php?page=myfaq-manager");
    }

    global $wpdb;

    switch ($act) {
        case "new": {
            $args = array(
                'posts_per_page'   => 9999,
                'offset'           => 0, 
                'orderby'          => 'post_title',
                'order'            => 'ASC', 
                'post_type'        => array('post','page'), 
                'post_status'      => 'public',
                'exclude' => array(),
                'suppress_filters' => true 
            );
            $ids = $wpdb->get_results("SELECT post_id FROM `".$wpdb->prefix."postmeta` WHERE meta_key='pageType'");  
            foreach($ids as $value){
                $args['exclude'][] = $value->post_id;
            }       
            $postpages = get_posts( $args ); 
            $_post = array();
            if(!isset($_SESSION['spintextnewid'])){
                $_SESSION['spintextnewid'] = strtotime('now').rand(99, 9999);
            }
            if(isset($_POST['post_to_clone'])){
                $_post = get_page($_POST['post_to_clone']);
            }
            $states = $wpdb->get_results("SELECT DISTINCT state FROM ".AUTOPOST_CITYSTATE_TABLE);
            $newid =  $_SESSION['spintextnewid']; 
            $statinfos = $wpdb->get_results("SELECT DISTINCT state FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid IN (SELECT citystateid FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".$newid.")");
            $statinfos_ = array();
            foreach($statinfos as $ikey => $ivalue){
                $statinfos_[$ivalue->state] ='';
            }
            $statinfos = $statinfos_;        
        ?>

        <div class='wrap myfaq-manager'>
            <div class="icon32" id="icon-link-manager"></div>
            <h2>Add New Project
                <a href='admin.php?page=myfaq-manager' class='button-primary'>Back to Projects</a>
            </h2>
            <style>
                .inner_inputbox_wrapper { 
                    width: 99%;
                }
                .autospinlinegroup{
                background: #fefefe none repeat scroll 0 0;
                    border: 1px solid #e4e4e4;
                    float: left;
                    margin-bottom: 8px;
                    padding: 8px;
                    width: 100%;
                } .statecityselectlist  span {
                    float: left;
                    width: 184px;
                }
                form .autospinlinegroup {
                    border: 1px solid #818181;
                }
            </style>
            <form action="admin.php?page=myfaq-process" method="post"  style="width:80%;">
                <input type='hidden' name='act' value='new' />
                <input type="hidden" name="spinstatecity" id="spinstatecity" value="<?php echo $_SESSION['spintextnewid']; ?>"/> 

                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">Main Details</h3>
                </div>
                <div class="inner_inputbox_wrapper">
                    <label for='question'><span style="float:left;">Project Name:</span> <span class="projectload" style="display:none;"></span></label>
                    <div>
                        <input type="text" name='question' id='question' class="newprojectName" style="width: 100%; margin-top: 5px;" value="<?php if(isset($_POST['question'])){ echo $_POST['question']; }  ?>"/>
                    </div> 
                    <span class="projectnameerror"></span>
                </div>
                
                <div class="inner_inputbox_wrapper">
                    <label for='post_to_clone'>Post to Clone:</label>
                    <div>
                        <select name='post_to_clone' id='post_to_clone' class="post_to_clone" style="width: 100%; margin-top: 5px;" >
                            <?php foreach($postpages as $postpage){ ?>
                                    <option <?php if(isset($_POST['post_to_clone']) && $_POST['post_to_clone'] == $postpage->ID){ echo 'selected'; }  ?> value="<?php echo  $postpage->ID; ?>" data-name="<?php echo  $postpage->post_name; ?>"><?php echo  $postpage->post_title; ?></option>
                            <?php } ?>
                        </select>
                        <!--<input type="text" name='post_to_clone' id='post_to_clone' style="width: 100%; margin-top: 5px;" value="" />-->
                        <small>Post id that need to be cloned</small>
                    </div>
                </div>

                <div class="inner_inputbox_wrapper">
                    <label for='answer'>Title:<span class="spinshortcomment">[state] [town] [state-code]</span></label>
                    <div>
                        <input class="spinposttitle" type="text" name='answer' id='answer' style="width: 100%; margin-top: 5px;" value="" />
                        <small>Post title</small>
                    </div>
                </div>

                <div class="inner_inputbox_wrapper" id="main_hub_location" style="background: #7eddf7 none repeat scroll 0 0;padding: 10px;">
                    <label style="font-size: 18px;">Main Hub Location(e.g. stone-wall-locations):</label>
                    <div>
                        <span style="float: left; margin-top: 5px;"><?php echo site_url().'/'; ?></span>
                        <input type="text" name='main_hub_location' id='main_hub_location' style="width: 36%" />
                    </div>

                    <label>Main Hub Location Title and Meta Description</label>
                    <div>
                        <input type="text" class="pull-left hub_title" name="main_title" id="main_title" placeholder="Main Hub Location Title" style="width:25%" maxlength="55" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" name='main_metadesc' id='main_metadesc' placeholder="Main Hub Location Meta Description" style="width: 36%" maxlength="156" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 156 Max Characters</div>
                    </div>

                    <div style="border: 2px solid #888;margin: 40px 0 10px;float: none;clear: both;"></div>
                    
                    <label style="font-size: 18px;">State Hub Location(e.g. [state]-stone-wall-locations):</label>
                    <div>
                        <span style="float: left; margin-top: 5px;"><?php echo site_url().'/'; ?></span>
                        <input type="text" name='state_hub_location' id='state_hub_location' style="width: 36%; float:left;" />
                        <select id="state_google" name="state_google" style="width:150px; margin-top: -2px; height: 27px;">
                            <option  value="0">None</option>
                            <option  value="1">Google Shorten</option>
                        </select>
                        <input type="text" name='state_tag' id='state_tag' placeholder="#Tag" style="width:120px;" />
                        <input type="text" name="state_link_text" id="state_link_text" placeholder="#State Hub Link Text" style="width:170px;" />
                    </div>

                    <label>State Hub Location Title and Meta Description</label>
                    <div>
                        <input type="text" class="pull-left hub_title" name="state_title" id="state_title" placeholder="State Hub Location Title" style="width:25%" maxlength="55" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" name='state_metadesc' id='state_metadesc' placeholder="State Hub Location Meta Description" style="width: 36%" maxlength="156" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 156 Max Characters</div>
                    </div>

                    <div style="border: 2px solid #888;margin: 40px 0 10px;float: none;clear: both;"></div>

                    <label style="font-size: 18px;">City Hub Location(e.g. [state] [town] [state-code]-stone-wall)</label>
                    <div>
                        <div>
                            <span style="float: left; margin-top: 5px; " class="metasitepath"><?php echo site_url().'/'; ?></span> 
                            <input class="metasitename newslugname" type="text"  name='uri' id='uri' style="width: 36%; float:left;" />
                            <select id="city_google" name="city_google" style="width:150px; margin-top: -2px; height: 27px;">
                                <option  value="0">None</option>
                                <option  value="1">Google Shorten</option>
                            </select>
                            <input type="text" name='city_tag' id='city_tag' placeholder="#Tag"  style="width:120px;" />
                            <input type="text" name="city_link_text" id="city_link_text" placeholder="#City Hub Link Text" style="width:170px;" />
                            <span class="nameload" style="display:none;float:right;"></span><span class="nameloaderrormessage" style="float:right;"></span>
                        </div>
                    </div>

                    <label>City Hub Location Title and Meta Description</label>
                    <div>
                        <input type="text" class="pull-left hub_title" id="spintext_metatitle" name="spintext_metatitle" placeholder="City Hub Location Title" style="width:25%" maxlength="55" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" id="spintext_metadesc" name="spintext_metadesc" placeholder="City Hub Location Meta Description" style="width: 36%" maxlength="156" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title">0</span> / 156 Max Characters</div>
                    </div>
                    
                </div>

                <div class="inner_inputbox_wrapper group_wrap" id="changeposttitle">
                    <label for='changeposttitle'>Change Post Title:</label>
                    <div style="margin-right: 7%;">
                        <select class="changeposttitleselect" style="width:100px;float:left;" name="changeposttitleselect" value="" id="changeposttitleselect" >
                            <option  value="">Never</option>
                            <option <?php if(isset($_POST['changeposttitleselect']) && $_POST['changeposttitleselect'] == '24'){ echo 'selected'; } ?> value="24">24H</option> 
                            <option <?php if(isset($_POST['changeposttitleselect']) && $_POST['changeposttitleselect'] == '48'){ echo 'selected'; } ?> value="48">48H</option> 
                            <option <?php if(isset($_POST['changeposttitleselect']) && $_POST['changeposttitleselect'] == '72'){ echo 'selected'; } ?> value="72">72H</option> 
                        </select>
                    </div>
                </div>

                <div class="inner_inputbox_wrapper">
                    <label for='secondtitle'>Second Title:<span class="spinshortcomment">[state] [town] [state-code]</span></label>
                    <div>
                        <input class="secondtitle" type="text" name='secondtitle' id='secondtitle' style="width: 100%; margin-top: 5px;" value="" />
                        <small>Post title</small>
                    </div>
                </div>

                <!--// Scheduling //-->
                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">sub Featured Area</h3>
                </div>
                <div class="inner_inputbox_wrapper wpeditor_wrap">
                    <div>
                        <?php 
                        $desc = '';                       
                         if(isset($_POST['spintext_excerpt'])){
                             $desc = stripslashes($_POST['spintext_excerpt']);
                         }
                          if(!empty($_post)){
                                   $desc = stripslashes($_post->post_excerpt);
                          }                        
                        wp_editor($desc, "spintext_excerpt", array("textarea_rows" => 5)); ?>
                    </div>
                    <span class="spinshortcomment">short code : [state] [town] [state-code]</span>
                </div>
                <div class="inner_heading_wrapper" style="float: left; margin-bottom: 0px; width: 100%; padding: 9px; margin-top: 15px; background: rgb(170, 218, 166) none repeat scroll 0px 0px;">
                    <h3 class="inner_heading"  style="margin:0;padding:0;background: rgb(170, 218, 166) none repeat scroll 0px 0px;">SEO Details</h3>
                </div>
                <div style="float: left; width: 100%; background: rgb(170, 218, 166) none repeat scroll 0px 0px; padding: 9px;">
                    <div style="float: left; margin: 10px 0px 7px;">
                        <label class="switch">
                            <input name="seoswitch" checked class="seoswitch" value="1" type="checkbox">
                            <span class="slider">On</span> 
                        </label>
                        <span style="font-weight: bold; padding-top: 4px; float: left; margin-top: 0px; margin-left: 12px;">Enable PS SEO on this page</span>
                    </div>
                </div>
                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">Text for SPIN</h3>
                </div>
                <?php
                if(!empty($postpages)){
                    $spin_= 0;
                    $postpage = $postpages[0];
                    $id = $postpage->ID;
                    if(isset($_POST['post_to_clone'])){
                        $id = $_POST['post_to_clone'];
                    } 
                    $fcount = false;      
                    $data = get_autospin($id);
                    $data = get_autospinline($data);
                    foreach($data as $value){ ?>
                    <div class="autospinlinegroup">
                        <?php 
                        $counter = count($value['content'] );
                        if($counter != 0){
                            $counter = 100/$counter;
                        }else{
                            $counter = 100;
                        }
                        $sscrd = false;      
                        foreach($value['content'] as $valcontent){
                            foreach($valcontent['content']  as $spin => $val) {
                                if(isset($val['src'])){
                                    ?> <div style="float:left;width:<?php echo $counter; ?>%">  
                                        <div style="margin: 4px 10px;"> <img src="<?php echo $val['src']; ?>" style="width:100%;"/></div>
                                    </div><?php
                                }else{ 
                                    $sscrd = true;     
                             ?>
                        <div style="float:left;width:<?php echo $counter; ?>%">  
                    <div class="inner_inputbox_wrapper wpeditor_wrap">
                        <label for='<?php echo "spintext_sc" . $spin_; ?>'>Spin Text for Shortcode - <?php echo ++$spin_; ?>:</label>
                        <div>
                            <?php wp_editor($val['content'], "spintext_sc" . $val['id'], array("textarea_rows" => 5)); ?>
                        </div> 
                    </div>
                        </div>
                            <?php  } }
                        if(empty($valcontent['content'] )){ ?>
                        <div style="float:left;width:<?php echo $counter; ?>%">  
                            <h3 style=" text-align: center;"><span class="noneeditablearea"> Non Editable Area.</span></h3>
                        </div>
                        <?php  }    
                            
                                } if($sscrd){ ?> 
                         <div style="float:left;width:100%;">
                        <span class="spinshortcomment">short code: [no_toc] [state] [town] [state-code] [autopost_map w="600" h="450"]</span>
                         </div>
                            <?php } ?>
                        <div style="float:left;width:100%;">
                            <hr>
                            <?php $url_ =get_permalink($id);  
                            if(strpos($url_,'?') !== false){
                                 $url_ = $url_.'&';
                            }else{
                                 $url_ = $url_.'?';
                            }
                             
                            ?>
                            <iframe class="spiniframeload spiniframeloading" style="width: 100%; margin-top: 12px;" src="" data-rsrc="<?php echo $url_; ?>autospin=page&spinid=<?php echo $value['id']; ?>"></iframe>
                         </div>
                    </div>
                    <?php }  
                }   
                ?>
                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">States And Cities </h3>
                </div>
                <div style="float: left; width: 100%; margin-left: 10px;">
                    <div style="float: left; width: 100%; margin-bottom: 8px;">
                        <input type="checkbox" <?php if(count($statinfos) > 49){ echo 'checked'; } ?> id="newselectallstate" style="float: left; margin-top: 2px;"/> <label for="newselectallstate" style="font-weight: bold;float: left;">Select All</label>
                        <span class="state_loading" style="display:none;"></span>
                    </div>
                    <div class="statecityselectlist">
                        <?php 
                        foreach($states as $state){
                            $statecode = $wpdb->get_var("SELECT statecode FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".$state->state."'");
                            ?><span><input type="checkbox" <?php if(isset($statinfos[$state->state])){ echo 'checked'; } ?> class="spinstatecitystate" value="<?php echo $state->state; ?>"/> 
                                <span class="spinstatecitystatesingle"><?php echo $state->state; ?> (<?php echo $statecode; ?>)</span>
                                <span class="statecity_loading" style="display:none;"></span>
                            </span><?php
                        } ?>
                    </div>
                </div>
                 <!--// Scheduling //-->
                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">Scheduling</h3>
                </div>
                 <div style="float: left; width: 100%; margin-left: 10px;">

                <!--//schedule_days-->
                <div class="inner_inputbox_wrapper group_wrap" id="schedule_days">
                    <label for='schedule_days'>Posting time until complete:</label>
                    <?php
                    global $autopostWeekDays;
                    foreach ($autopostWeekDays as $day) {
                        $day_ = strtolower($day);
                        ?>
                        <div>
                            <label for='schedule_<?php echo $day_; ?>'><?php echo $day; ?>:</label>
                            <input <?php if(isset($_POST['schedule_'.$day_])){ echo 'checked'; } ?> type="checkbox" value="<?php echo $day_; ?>" id="schedule_<?php echo $day_; ?>" name="schedule_<?php echo $day_; ?>" />
                        </div>
                    <?php } ?>
                </div>
                <!--//time_to_post-->
                <div class="inner_inputbox_wrapper group_wrap" id="time_to_post"> 
                    <div id="time-range">
                        <?php                                 
                        if(isset($_POST['schedule_time_start'])){  $start = $_POST['schedule_time_start'];   }
                        else {  $start = '12:00 AM';  }
                        if(isset($_POST['schedule_time_stop'])){   $end = $_POST['schedule_time_stop']; }else{
                            $end = '11:59 PM';
                        }
                        ?>
                        <label for='time_to_post' style="  font-weight: bold;">Time to post: <span style=" font-weight: normal; margin-left: 8px;"><input style="background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;" type="text" name="schedule_time_start" value="<?php echo $start; ?>" id="sc_time_start"  class="slider-time sc_time_start"/>  - <input type="text" style="background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;" name="schedule_time_stop" value="<?php echo $end; ?>" id="sc_time_stop" class="slider-time2 sc_time_stop"/><span ></span>EST</span></label>
                    <div class="sliders_step1">
                        <div id="slider-range"></div>
                    </div>
                </div>
                         
                </div> 
                <!--//time_to_post-->
                <div class="inner_inputbox_wrapper group_wrap" id="random_posts_per_hour">
                    <label for='random_posts_per_hour'>Random Posts per Hour:</label>
                    <div style="margin-right: 7%;">
						<input type='number' name='post_min' class='post_min post_val' value='<?php echo $row_->post_min; ?>'>
						<input type='number' name='post_max' class='post_max post_val' value='<?php echo $row_->post_max; ?>'>
						<input type='hidden' name='schedule_posts_per_hour' class='eposts_per_select' value='<?php echo $rperhour; ?>'>
                        
                        <?php  $maxcount = $wpdb->get_var("SELECT count(projectid) as count FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".$newid); ?>
                        <input type="hidden" class="spintextdateshow"  value="<?php echo $maxcount; ?>"/>
                        <div  class="spintextdateshowcontent" style="font-size: 11px; color: rgb(115, 115, 115); line-height: 12px; float: left; margin-left: 17px; max-width: 250px; margin-top: 3px;">
                            350 Total pages to post with calculation project with be completed 32 days
                    </div>
                    </div>
                    <div>
                       
                    </div>
                </div>
           
                 </div>
                 <div id="spin_pop_up" style="display:none;">
                    <h3>Cities of <span class="spin_pop_upstatetitle" style="width:auto !important;"></span> State <span class="button  b-close"><span>X</span></span></h3>
                    <div class="spin_pop_upcontent"> 
                        Wait few second to load cities...
                    </div> 
                </div> 
                <div id="spin_pop_discard" style="display:none;">
                    <h3>Discard Project<span class="button  b-close"><span>X</span></span></h3>
                    <div class="spin_pop_discardcontent"> 
                        Are you sure do you want to discard ?
                        <div style="float: left; width: 100%; margin-bottom: 2px; margin-top: 14px;">
                        <a style="padding: 0px 20px;" class="button-primary" href="admin.php?page=myfaq-manager" >Yes</a>
                        <button style="padding: 0px 20px;" class="button-secondary discardclose">No</button>
                        </div>
                    </div> 
                </div> 
                 <p style=" margin-left: 10px;">
                    <input type='submit' class="button-secondary resetsubmit"  style="display:none;"/>
                    <input type='submit' name='btnAddNew' id='btnAddNew' value="Add Project" class="button-primary" />
                    <span id='btndescard' class="button-secondary">Discard</span>
                </p>

                <script type="text/javascript">
                jQuery(document).ready(function($){
                    jQuery("#btnAddNew").on("click", function(){
                        if ( jQuery("#question").val() == '' ){
                            alert("Project Name Cannot be empty"); return false;
                        }
                        if (jQuery("#answer").val() == '' ){
                            alert("Project Title Cannot be empty"); return false;
                        }
                        if (jQuery(".error_project_name").length  > 0 ){
                                jQuery("html, body").animate({ scrollTop: jQuery(".error_project_name").offset().top - 75 }, "slow");
                            return false;
                        }
                            if ( jQuery(".nameloaderror").length  > 0 ){
                                jQuery("html, body").animate({ scrollTop: jQuery(".nameloaderror").offset().top -75 }, "slow");
                            return false;
                        } 
                        var flag = false;
                        jQuery.each(jQuery('.statecityselectlist').find('.spinstatecitystate'),function(){
                            if(jQuery(this).is(':checked')){
                                flag = true;
                            }
                        });                                    
                        if(flag){
                                return true;
                        }else{ 
                            alert("At least one state or city need to select");
                            return false; 
                        }
                    });
                });
                </script>

            </form>
        </div>
                
        <div id="faqs_do_spin" style="display:none;width: 725px;margin-top: 40px;">
            <span class="button b-close"><span>X</span></span> 
            <div class="woodotrash woodocontent">
                <h1 class="faqs_title" >Question Spin</h1>       
                <span  style="float: left; width: 100%; margin-bottom: 6px;">Spin Options: </span>

                <div style="float: left; width: 49%;">
                    <div style="margin-bottom: 4px;">
                    <label>spintax_format </label>
                    <select class="faq-spinselect spintax_format">
                        <option value="{|}">{|}</option>
                        <option value="{~}">{~}</option>
                        <option value="[|]">[|]</option>
                        <option value="[spin]">[spin]</option>
                        <option value="#SPIN">#SPIN</option>
                    </select>
                </div> 
                    <div style="margin-bottom: 4px;">
                    <label>confidence_level </label>
                    <select class="faq-spinselect confidence_level">
                        <option value="low">low</option>
                        <option value="medium">medium</option>
                        <option value="high">high</option> 
                    </select>
                </div>   
                    <div style="margin-bottom: 4px;">
                         <label>protected_terms </label>
                         <textarea class="protected_terms" style="width: 96%; margin-top: 6px;"></textarea>
                          <span style="font-size: 12px; color: rgb(152, 152, 152);">Can be added more than one keyword and separated by ( , )</span>
                    </div>

                </div>
                <div style="float: left; width: 49%;"> 
                    <label><input class="faq-spinoption" type="checkbox" value="auto_protected_terms"/> auto_protected_terms</label>  <br>
                    <label><input class="faq-spinoption" type="checkbox" value="auto_sentences"/>auto_sentences</label> <br>
                    <label><input class="faq-spinoption" type="checkbox" value="auto_paragraphs"/>auto_paragraphs</label>  <br>
                    <label><input class="faq-spinoption" type="checkbox" value="auto_new_paragraphs"/> auto_new_paragraphs</label>    <br>    
                    <label><input class="faq-spinoption" type="checkbox" value="auto_sentence_trees"/> auto_sentence_trees</label>    <br>    
                    <label><input class="faq-spinoption" type="checkbox" value="use_only_synonyms"/> use_only_synonyms</label>        <br>   
                    <label><input class="faq-spinoption" type="checkbox" value="nested_spintax"/> nested_spintax</label>          <br>
                    <label><input class="faq-spinoption" type="checkbox" value="reorder_paragraphs"/> reorder_paragraphs</label>
                </div> 

                <div style="float: left; width: 100%; margin-top: 12px;">
                    <span>Spin Text</span>
                    <textarea class="faq-spincontent" style="float: left; width: 100%; margin-top: 7px; margin-bottom: 10px;"></textarea>
                </div>

                <div>
                    <button class="button button-primary faq_spinbutton"  style="font-size: 16px; float: left; margin-bottom: 10px; margin-top: 2px;">Spin</button>
                    <span id="woo_do_title">
                        <span class="woodoload" style="display:none;"></span>
                        <div class="woo_do_msg"> 
                        </div>  
                    </span> 
                </div> 

                <div> 
                    <textarea readonly class="faq-spinvcontent" placeholder="Variation Content ..." style="float: left; width: 100%; margin-top: 7px;height: 170px;"></textarea>
                </div>
                <div>
                     <button class="button button-primary faq_splitsubmit"  style="font-size: 16px; float: left; margin-top: 12px; margin-right: 12px;">Submit</button>
                     <button class="button button-cancel faq_splitclose"  style="margin-top: 12px; font-size: 16px;float:left;">Cancel</button>
                </div>

            </div> 
        </div>
        <?php
        break;
    }

    case "pause": {  
         $_GET['m'] = 'p'; 
         $sql = "SELECT * FROM ".AUTOPOST_SCHEDULING_TABLE." WHERE project_id = ".$id;
        $schedule = $wpdb->get_row($sql);
        if(!empty($schedule)){
             $wpdb->update(
                            AUTOPOST_SCHEDULING_TABLE, array(  
                        'status' => '0',  
                            ),array(
                                 'project_id' => $id
                            )
                    ); 
        }
        
        display_faq_manager_func();
        break;
    }
    case "start": {       
        $_GET['m'] = 's'; 
        $sql = "SELECT * FROM ".AUTOPOST_SCHEDULING_TABLE." WHERE project_id = ".$id;
        $schedule = $wpdb->get_row($sql);
        if(!empty($schedule)){
        if($schedule->status != '5'){
               $wpdb->update(
                            AUTOPOST_SCHEDULING_TABLE, array(  
                        'status' => '1',  
                            ),array(
                                 'project_id' => $id
                            )
                    ); 
        }
          
        }
        
        display_faq_manager_func();
        break;
    }
    case "edit": {
      
        if (!isset($id) || empty($id)) {
            _redirect("admin.php?page=myfaq-manager");
        }

        $sql = "SELECT * FROM ".MYFAQ_TABLE." m
                LEFT JOIN ".AUTOPOST_SPINTXT_TABLE." s 
                on m.id = s.project_id 
                WHERE m.id = {$id}";
        $spintax = new Spintax();
        $row = $wpdb->get_row($sql); 
        if (!$row) {
            _redirect("admin.php?page=myfaq-manager");
        }
        $post_ = $_POST;
        $sql = "SELECT * FROM ".AUTOPOST_SCHEDULING_TABLE." WHERE project_id = {$id}";
        $row_ = $wpdb->get_row($sql);  
        if(empty($row_))$row_ = array();
      
        $args = array(
            'posts_per_page'   => 9999,
            'offset'           => 0, 
            'orderby'          => 'post_title',
            'order'            => 'ASC', 
            'post_type'        => array('post','page'), 
            'post_status'      => 'public',
                        'exclude' => array(),
            'suppress_filters' => true 
        );
        $ids = $wpdb->get_results("SELECT post_id FROM `".$wpdb->prefix."postmeta` WHERE meta_key='pageType'");  
        foreach($ids as $value){
            $args['exclude'][] = $value->post_id;
        }  
        if(isset($post_['post_to_clone'])){
            $_post = get_page($post_['post_to_clone']);
        }
        $postpages = get_posts( $args ); 

        $states = $wpdb->get_results("SELECT DISTINCT state FROM ".AUTOPOST_CITYSTATE_TABLE);

        $statinfos = $wpdb->get_results("SELECT DISTINCT state FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid IN (SELECT citystateid FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".$id.")");
        $statinfos_ = array();
        foreach($statinfos as $ikey => $ivalue){
            $statinfos_[$ivalue->state] ='';
        }
        $statinfos = $statinfos_;
        $wpdb->query("DELETE FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE projectid = '".$id."'");
    
        $pagelinks = $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."'",'ARRAY_A');     
        foreach($pagelinks as $pagelink){
            unset($pagelink['pageid']);
            $wpdb->insert(
                    AUTOPOST_CITYSTATETEMPPAGE_TABLE,
                    $pagelink
                    );
        }
?>
        <div class='wrap'>
            <div class="icon32" id="icon-link-manager"></div>

            <h2>Edit Autopost
                <a href='admin.php?page=myfaq-manager' class='button-primary'>Back to Projects</a>
            </h2>
                
            <style>
            .inner_inputbox_wrapper {width: 99%;}
            .autospinlinegroup{
            background: #fefefe none repeat scroll 0 0;
                border: 1px solid #e4e4e4;
                float: left;
                margin-bottom: 8px;
                padding: 8px;
                width: 100%;
            }
            .autospinginfo{
                background: #7eddf7 none repeat scroll 0 0;
                color: #11637e;
                padding: 12px; 
            }.statecityselectlist  span {
                float: left;
                width: 184px;
            }
            form .autospinlinegroup {
                border: 1px solid #818181;
            }
            </style>
            <div style="float:left;width:80%;">
                <?php
                if($row_->status == '1'){  ?><div class="autospinginfo"> <b> This project is already started</b>. Updates to this project will erase the previous data and restart the project again.</div><?php  }
                else{  ?><div class="autospinginfo"> <b> The Project not started</b>.</div><?php }
                ?>
            </div>
            <form action="admin.php?page=myfaq-process" method="post"  style="width:80%;">
                <input class="autoeditable" type='hidden' name='act' value='edit' />
                <input class="autoeditableid" type='hidden' name='id' value='<?php echo $id; ?>' />

                <div class="inner_heading_wrapper">
                        <h3 class="inner_heading">Main Details</h3>
                </div>
                <div class="inner_inputbox_wrapper">
                    <label for='question'><span style="float:left;">Project Name:</span> <span style="display:none;" class="projectload"></span></label>
                    <div>
                        <input type="text" name='question' class="editprojectName" id='question' style="width: 100%; margin-top: 5px;" value="<?php
                        if(isset($_POST['question'])){
                                echo $_POST['question'];
                        }else{
                                echo stripslashes($row->question);
                        }
                        
                        ?>" /> 
                        <span class="projectnameerror"></span>
                    </div> 
                </div>
                    
                <div class="inner_inputbox_wrapper">
                    <label for='post_to_clone'>Post to Clone:</label>
                    <div>
                        <select name='post_to_clone' id='post_to_clone' class="post_to_clone" style="width: 100%; margin-top: 5px;" >
                        <?php
                        $linkid = $row->post_to_clone;
                        if(isset($_POST['post_to_clone'])){
                                $linkid =$_POST['post_to_clone'];
                        }
                        foreach($postpages as $postpage){ ?>
                                <option <?php if($linkid == $postpage->ID) { echo 'selected'; }  ?> data-name="<?php echo  $postpage->post_name; ?>" value="<?php echo  $postpage->ID; ?>"><?php echo  $postpage->post_title; ?></option>
                        <?php } ?>
                    </select> 
                        <small>Post id that need to be cloned</small>
                    </div>
                </div>

                <div class="inner_inputbox_wrapper">
                    <label for='answer'>Title:<span class="spinshortcomment">[state] [town] [state-code]</span></label>
                    <div>
                        <input class="spinposttitle"  type="text" name='answer' id='answer' style="width: 100%; margin-top: 5px;" value="<?php
                            if(isset($_POST['answer'])){
                                echo ''; 
                            }else{
                                echo stripslashes($row->answer); 
                            }
                        ?>" />
                        <small>Post title</small>
                    </div>
                </div>

                <div class="inner_inputbox_wrapper" id="main_hub_location" style="background: #7eddf7 none repeat scroll 0 0;padding: 10px;">
                    <label style="font-size: 18px;">Main Hub Location(e.g. stone-wall-locations):</label>
                    <div>
                        <span style="float: left; margin-top: 5px;"><?php echo site_url().'/'; ?></span>
                        <input type="text" name='main_hub_location' id='main_hub_location' style="width: 36%" value="<?php
                            if (isset($_POST['main_hub_location'])) {
                                echo $_POST['main_hub_location'];
                            } else {
                                echo stripslashes($row->main_hub_location); 
                            }
                        ?>" />
                    </div>

                    <label>Main Hub Location Title and Meta Description</label>
                    <div>
                        <input type="text" class="pull-left hub_title" name="main_title" id="main_title" placeholder="Main Hub Location Title" style="width:25%" maxlength="55" value="<?php echo stripslashes($row->main_title); ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($row->main_title); ?></span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" name='main_metadesc' id='main_metadesc' placeholder="Main Hub Location Meta Description" style="width: 36%;" maxlength="156" value="<?php echo stripslashes($row->main_metadesc); ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($row->main_metadesc); ?></span> / 156 Max Characters</div>
                    </div>

                    <div style="border: 2px solid #888;margin: 40px 0 10px;float: none;clear: both;"></div>
                    
                    <label style="font-size: 18px;">State Hub Location(e.g. [state]-stone-wall-locations):</label>
                    <div>
                        <span style="float: left; margin-top: 5px;"><?php echo site_url().'/'; ?></span>
                        <input type="text" name='state_hub_location' id='state_hub_location' style="width: 36%; float:left;" value="<?php
                            if (isset($_POST['state_hub_location'])) {
                                echo $_POST['state_hub_location'];
                            } else {
                                echo stripslashes($row->state_hub_location); 
                            }
                        ?>" />
                        
                        <select id="state_google" name="state_google" style="width:150px; margin-top: -2px; height: 27px;">
                            <option <?php if($row->state_google == 0){ echo 'selected';} ?> value="0">None</option>
                            <option <?php if($row->state_google == 1){ echo 'selected';} ?> value="1">Google Shorten</option>
                        </select>
                        <input type="text" name='state_tag' id='state_tag' placeholder="#Tag" style="width:120px;" value="<?php
                            if (isset($_POST['state_tag'])) {
                                echo $_POST['state_tag'];
                            } else {
                                echo stripslashes($row->state_tag); 
                            }
                        ?>" />
                        <input type="text" name="state_link_text" id="state_link_text" placeholder="#State Hub Link Text" style="width:170px;" value="<?php
                            if (isset($_POST['state_link_text'])) {
                                echo $_POST['state_link_text'];
                            } else {
                                echo stripslashes($row->state_link_text); 
                            }
                        ?>"/>
                    </div>

                    <label>State Hub Location Title and Meta Description</label>
                    <div>
                        <input type="text" class="pull-left hub_title" name="state_title" id="state_title" placeholder="State Hub Location Title" style="width:25%" maxlength="55" value="<?php echo stripslashes($row->state_title); ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($row->state_title); ?></span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" name='state_metadesc' id='state_metadesc' placeholder="State Hub Location Meta Description" style="width: 36%;" maxlength="156" value="<?php echo stripslashes($row->state_metadesc); ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($row->state_metadesc); ?></span> / 156 Max Characters</div>
                    </div>

                    <div style="border: 2px solid #888;margin: 40px 0 10px;float: none;clear: both;"></div>

                    <label style="font-size: 18px;">City Hub Location(e.g. [state] [town] [state-code]-stone-wall)</label>
                    <div>
                        <div>
                            <span style="float: left; margin-top: 5px; " class="metasitepath"><?php echo site_url().'/'; ?></span> 
                            <input class="metasitename editslugname" type="text"  name='uri' id='uri' style="width: 36%; float:left;" value="<?php
                                if(isset($_POST['uri'])){
                                    echo ''; 
                                }else{
                                    echo stripslashes($row->uri); 
                                }
                            ?>" />
                           
                            <select id="city_google" name="city_google" style="width:150px; margin-top: -2px; height: 27px;">
                                <option <?php if($row->city_google == 0){ echo 'selected';} ?> value="0">None</option>
                                <option <?php if($row->city_google == 1){ echo 'selected';} ?> value="1">Google Shorten</option>
                            </select>
                            <input type="text" name='city_tag' id='city_tag' placeholder="#Tag"  style="width:120px;" value="<?php
                                if (isset($_POST['city_tag'])) {
                                    echo $_POST['city_tag'];
                                } else {
                                    echo stripslashes($row->city_tag); 
                                }
                            ?>" />
                            <input type="text" name="city_link_text" id="city_link_text" placeholder="#City Hub Link Text" style="width:170px;" value="<?php
                                if (isset($_POST['city_link_text'])) {
                                    echo $_POST['city_link_text'];
                                } else {
                                    echo stripslashes($row->city_link_text); 
                                }
                            ?>" />
                            <span class="nameload" style="display:none;float:right;"></span><span class="nameloaderrormessage" style="float:right;"></span>
                        </div>
                    </div>

                    <label>City Hub Location Title and Meta Description</label>
                    <div>
                        <?php
                            $desc = stripslashes($row->spintext_metadesc); 
                            if(isset($_POST['spintext_metadesc'])){
                                $desc =  $_POST['spintext_metadesc'];
                            }
                            $title = stripslashes($row->spintext_metatitle); 
                            if(isset($_POST['spintext_metatitle'])){
                                $title =  $_POST['spintext_metatitle'];
                            }
                        ?>

                        <input type="text" class="pull-left hub_title" id="spintext_metatitle" name="spintext_metatitle" placeholder="City Hub Location Title" style="width:25%" maxlength="55" value="<?php echo $title; ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($title); ?></span> / 55 Max Characters</div>
                        <input type="text" class="pull-left hub_title" id="spintext_metadesc" name="spintext_metadesc" placeholder="City Hub Location Meta Description" style="width: 36%" maxlength="156" value="<?php echo $desc; ?>" />
                        <div class="pull-left" style="background: #fff;margin: 5px;padding: 0px 5px;"><span class="len_hub_title"><?php echo strlen($desc); ?></span> / 156 Max Characters</div>
                    </div>

                    
                </div>

                <div class="inner_inputbox_wrapper group_wrap" id="changeposttitle">
                    <label for='changeposttitle'>Change Post Title:</label>
                    <?php
                    if(isset($_POST['changeposttitleselect'])){
                        $changetitle = $_POST['changeposttitleselect'];
                    }else{
                        $changetitle =  $row_->changetitle;
                    } 
                    ?>
                    <div style="margin-right: 7%;">
                        <select class="changeposttitleselect" style="width:100px;float:left;" name="changeposttitleselect" value="" id="changeposttitleselect" >
                            <option value="">Never</option>
                            <option <?php if($changetitle == '24'){ echo 'selected';} ?> value="24">24H</option> 
                            <option <?php if($changetitle == '48'){ echo 'selected';} ?> value="48">48H</option> 
                            <option <?php if($changetitle == '72'){ echo 'selected';} ?> value="72">72H</option> 
                        </select>
                    </div>
                </div>
                    
                <div class="inner_inputbox_wrapper">
                    <label for='secondtitle'>Second Title:<span class="spinshortcomment">[state] [town] [state-code]</span></label>
                    <div>
                        <input class="secondtitle"  type="text" name='secondtitle' id='secondtitle' style="width: 100%; margin-top: 5px;" value="<?php
                            if(isset($_POST['secondtitle'])){
                                echo ''; 
                            }else{
                                echo stripslashes($row->secondtitle); 
                            }
                        ?>" />
                        <small>Post title</small>
                    </div>
                </div>

                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">sub Featured Area</h3>
                </div>
                <div class="inner_inputbox_wrapper wpeditor_wrap">
                    <div>
                        <?php 
                        $desc = '';  
                          $desc = stripslashes($row->excerpt);
                          if(!empty($_post)){
                                   $desc = stripslashes($_post->post_excerpt);
                          }    
                        wp_editor($desc, "spintext_excerpt", array("textarea_rows" => 5)); ?>
                    </div>
                <span class="spinshortcomment">short code : [state] [town] [state-code]</span>
                </div>
                
                <div class="inner_heading_wrapper" style="float: left; margin-bottom: 0px; width: 100%; padding: 9px; margin-top: 15px; background: rgb(170, 218, 166) none repeat scroll 0px 0px;">
                    <h3 class="inner_heading" style="margin:0;padding:0;background: rgb(170, 218, 166) none repeat scroll 0px 0px;">SEO Details</h3>
                </div>

                <div style="float: left; width: 100%; background: rgb(170, 218, 166) none repeat scroll 0px 0px; padding: 9px;">
                    <div style="float: left; margin: 10px 0px 7px;">
                        <label class="switch">
                        <?php 
                            $switch = '';
                            $switchtitle = 'Off';
                            if(isset($_POST['seoswitch']) || $row->seopen){
                                $switch = 'checked';
                                $switchtitle = 'On';
                            } 
                        ?>
                        <input name="seoswitch" <?php echo $switch; ?> class="seoswitch" value="1" type="checkbox">
                        <span class="slider"><?php echo $switchtitle; ?></span> 
                        </label>
                        <span style="font-weight: bold; padding-top: 4px; float: left; margin-top: 0px; margin-left: 12px;">Enable PS SEO on this page</span>
                    </div>
                </div>

                <div class="inner_heading_wrapper">
                    <h3 class="inner_heading">Text for SPIN</h3>
                </div>
                <?php
                $spin_ = 0;
                
                if(isset($_POST['post_to_clone'])){
                        $data = get_autospin($_POST['post_to_clone'],false,$id);
                            $postid = $_POST['post_to_clone'];
                }else{
                        $data = get_autospin($row->post_to_clone,false,$id);
                            $postid = $row->post_to_clone;
                }
                $fcount = false;  
                $sscrd = false;
                $data = get_autospinline($data);
                foreach($data as $value){ ?>
                <div class="autospinlinegroup">
                    <?php 
                    $counter = count($value['content'] );
                    if($counter != 0){
                        $counter = 100/$counter;
                    }else{
                        $counter = 100;
                    }
                    foreach($value['content'] as $valcontent){
                           
                        foreach($valcontent['content']  as $spin => $val) {
                            if(isset($val['src'])){
                                ?> <div style="float:left;width:<?php echo $counter; ?>%">  
                                    <div style="margin: 4px 10px;"> <img src="<?php echo $val['src']; ?>" style="width:100%;"/></div>
                                </div><?php
                            }else{  $sscrd = true; ?>
                        <div style="float:left;width:<?php echo $counter; ?>%">  
                    <div class="inner_inputbox_wrapper wpeditor_wrap">
                        <label for='<?php echo "spintext_sc" . $spin_; ?>'>Spin Text for Shortcode - <?php echo ++$spin_; ?>:</label>
                        <div>
                            <?php wp_editor($val['content'], "spintext_sc" . $val['id'], array("textarea_rows" => 5)); ?>
                        </div> 
                    </div>
                        </div>
                            <?php  } }
                               if(empty($valcontent['content'] )){ ?>
                        <div style="float:left;width:<?php echo $counter; ?>%">  
                            <h3 style=" text-align: center;"><span class="noneeditablearea"> Non Editable Area.</span></h3>
                        </div>
                        <?php  }    
                        }
                    if($sscrd){  ?>        <div style="float:left;width:100%;">
                        <span class="spinshortcomment">short code : [no_toc] [state] [town] [state-code] [autopost_map w="600" h="450"]</span> 
                         </div>
                    <?php } ?>
                        <div style="float:left;width:100%;">
                            <hr>
                            <?php $url_ =get_permalink($postid);  
                             if(strpos($url_, '?') !== false){
                                 $url_ = $url_.'&';
                             }else{
                                 $url_ = $url_.'?';
                             }
                             
                             ?>
                            <iframe class="spiniframeload spiniframeloading" style="width: 100%; margin-top: 12px;" src="" data-rsrc="<?php echo $url_; ?>autospin=page&spinid=<?php echo $value['id']; ?>"></iframe>
                        </div>
                    </div>
                    <?php }  
        
                    ?>
                    <div class="inner_heading_wrapper">
                        <h3 class="inner_heading">States And Cities </h3>
                    </div>
                    <div style="float:left;width:100%;margin-left: 8px;">
                        <div style="float: left; width: 100%; margin-bottom: 8px;">
                            <input type="checkbox" <?php if(count($statinfos) > 49){ echo 'checked'; } ?> id="editselectallstate" style="float: left; margin-top: 2px;"/> <label for="editselectallstate" style="font-weight: bold;float: left;">Select All</label>
                            <span class="state_loading"  style="display:none;"></span>
                        </div>
                        <div class="statecityselectlist">
                        <?php foreach($states as $state){
                            $statecode = $wpdb->get_var("SELECT statecode FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".$state->state."'");
                            ?><span>
                                <input type="checkbox" <?php if(isset($statinfos[$state->state])){ echo 'checked'; } ?> class="editstatecitypoint" value="<?php echo $state->state; ?>"/>
                                <span class="statecity_ctycontent"><?php echo $state->state; ?> (<?php echo $statecode; ?>)</span> 
                                <span class="statecity_loading" style="display:none;"></span>
                                <span class="statecity_delete" style="<?php if(!isset($statinfos[$state->state])){ echo 'display:none;'; } ?>"></span>
                            </span><?php
                        } ?>
                        </div>

                    </div>
                     <!--// Scheduling //-->
                    <div class="inner_heading_wrapper">
                         <h3 class="inner_heading">Scheduling</h3>
                    </div>
                     <div style="float:left;width:100%;margin-left: 8px;">

                        <!--//schedule_days-->
                        <div class="inner_inputbox_wrapper group_wrap" id="schedule_days">
                            <label for='schedule_days'>Posting time until complete:</label>
                            <?php 
                            global $autopostWeekDays;
                            $weekdays = explode(',', $row_->weekdays);
                            $editable = false;
                            foreach($weekdays as $weekday){
                                if(isset($_POST['schedule_'.$weekday])){
                                    $editable = true;
                                }
                            } 
                            foreach($autopostWeekDays as $day) {
                                $day_ = strtolower($day);
                                $check = '';
                                foreach($weekdays as $weekday){
                                    if($weekday == $day_){
                                        $check =  'checked';
                                    }else{
                                        if($editable){
                                            $check =  '';
                                        }
                                    }
                                }
                                if(isset($_POST['schedule_'.$day_])){
                                    $check =  'checked';
                                }
                                ?>
                            <div>
                                <label for='schedule_<?php echo $day_; ?>'><?php echo $day; ?>:</label>
                                <input name="schedule_<?php echo $day_; ?>" <?php echo $check;  ?> type="checkbox" value="<?php echo $day_; ?>" id="schedule_<?php echo $day_; ?>" />
                            </div>
                            <?php } ?>
                        </div>

                        <!--//time_to_post-->
                        <div class="inner_inputbox_wrapper group_wrap" id="time_to_post"> 
                            <div id="time-range">
                                <?php $start = stripslashes($row_->time_start);
                                if(trim($start) == ''){
                                    $start = '12:00 AM';
                                }
                                if(isset($_POST['schedule_time_start'])){
                                        $start = $_POST['schedule_time_start'];
                                }
                                $end = stripslashes($row_->time_end);
                                if(trim($end) == ''){
                                    $end = '11:59 PM';
                                } 
                                if(isset($_POST['schedule_time_stop'])){
                                        $end = $_POST['schedule_time_stop'];
                                } 
                                ?>
                                <label for='time_to_post' style="  font-weight: bold;">Time to post: <span style=" font-weight: normal; margin-left: 8px;"><input style="background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;" type="text" name="schedule_time_start" value="<?php echo $start; ?>" id="sc_time_start"  class="slider-time sc_time_start"/>  - <input type="text" style="background: transparent none repeat scroll 0% 0%; border: medium none; box-shadow: 0px 0px 0px; font-size: 13px; width: 70px;" name="schedule_time_stop" value="<?php echo $end; ?>" id="sc_time_stop" class="slider-time2 sc_time_stop"/><span ></span>EST</span></label>
                                <div class="sliders_step1">
                                    <div id="slider-range"></div>
                                </div>
                            </div>
                         
                        </div>

                        <!--//time_to_post-->
                        <div class="inner_inputbox_wrapper group_wrap" id="random_posts_per_hour">
                            <label for='random_posts_per_hour'>Random Posts per Hour:</label>
                            <?php
                            if(isset($_POST['schedule_posts_per_hour'])){
                                $rperhour = $_POST['schedule_posts_per_hour'];
                            }else{
                            $rperhour =  $row_->random_post;
                            } 
                            ?>
                            <div style="margin-right: 7%;"> 
							    <input type='number' name='post_min' class='post_min post_val' value='<?php echo $row_->post_min; ?>'>
							    <input type='number' name='post_max' class='post_max post_val' value='<?php echo $row_->post_max; ?>'>
							    <input type='hidden' name='schedule_posts_per_hour' class='eposts_per_select' value='<?php echo $rperhour; ?>'>
                                <?php  $maxcount = $wpdb->get_var("SELECT count(projectid) as count FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".$id); ?>
                                <input type="hidden" class="spintextdateshow" value="<?php echo $maxcount;  ?>"/>
                                <div class="spintextdateshowcontent"  style="font-size: 11px; color: rgb(115, 115, 115); line-height: 12px; float: left; margin-left: 17px; max-width: 250px; margin-top: 3px;">
                            350 Total pages to post with calculation project with be completed 32 days
                                </div>
                            </div>
                        <div> 
                        </div>
                        </div>
                        <div id="spin_pop_up" style="display:none;">
                            <h3>Cities of <span class="spin_pop_upstatetitle" style="width:auto !important;"></span> State <span class="button  b-close"><span>X</span></span></h3>
                            <div class="spin_pop_upcontent"> 
                               Wait few second to load cities...
                            </div> 
                        </div>
                        <div id="spin_pop_discard" style="display:none;">
                            <h3>Discard Project<span class="button  b-close"><span>X</span></span></h3>
                            <div class="spin_pop_discardcontent"> 
                              Are you sure do you want to discard ?
                                <div style="float: left; width: 100%; margin-bottom: 2px; margin-top: 14px;">
                                <a style="padding: 0px 20px;" class="button-primary" href="admin.php?page=myfaq-manager" >Yes</a>
                                <button style="padding: 0px 20px;" class="button-secondary discardclose">No</button>
                                </div>
                            </div> 
                        </div> 
                        <p style="margin-bottom: 10px; margin-left: 10px;">
                        <input type='submit' class="button-secondary resetsubmit"  style="display:none;"/>
                        <input type='submit' name='btnAddNew' id='btnAddNew' value="Update" class="button-primary" />
                        <span id='btndescard' class="button-secondary">Discard</span>
                        </p> 
                    </div>

                    <script type="text/javascript">
                        jQuery(document).ready(function($){
                            jQuery("#btnAddNew").on("click", function(){
                                if (jQuery("#question").val() == '' ){
                                        alert("Project Name Cannot be empty"); return false;
                                }
                                if (jQuery("#answer").val() == '' ){
                                        alert("Project Title Cannot be empty"); return false;
                                }
                                
                                if ( jQuery(".error_project_name").length  > 0 ){
                                    jQuery("html, body").animate({ scrollTop: jQuery(".error_project_name").offset().top -75 }, "slow");
                               return false;
                            } 
                                if ( jQuery(".nameloaderror").length  > 0 ){
                                    jQuery("html, body").animate({ scrollTop: jQuery(".nameloaderror").offset().top -75 }, "slow");
                               return false;
                            } 
                                  var flag = false;
                                    jQuery.each(jQuery('.statecityselectlist').find('.editstatecitypoint'),function(){
                                         if(jQuery(this).is(':checked')){
                                        flag = true;
                                    }
                                    });                                    
                                     if(flag){
                                            return true;
                                }else{ 
                                              alert("At least one state or city need to select");
                                   return false; 
                                }
                            });
                        });
                    </script>

                </form>
 
                <div  class="autospinlinegroup" style="width: 80%;margin-top: 35px;">
                    <h4 style="margin-top: 2px; margin-bottom: 10px;">Spined Project posts 
                        <button style="float: right; margin-right: 14px;" class="button button-primary spinexportdata" value="<?php echo $id; ?>">Export</button>
                        <span class="spincsvload" style="display:none;"></span>
                    </h4>
                  <div class="spinedpageslist" style="float: left; width: 98%; padding: 3px 1%;">
                      <table style="width:100%;"> 
                     <?php
                   $rowcount = $wpdb->get_var("SELECT count(pageid) FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."'");
               $results = $wpdb->get_results("SELECT * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' ORDER BY state DESC LIMIT 0,24;");
        
            foreach($results as $result){
                  $postid_ = $wpdb->get_var("SELECT cloneid FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = '".$result->projectid."' AND citystateid = '".$result->citystateid."'");
                  if(empty($postid_) || $result->state != '1'){
                      $cstate = $wpdb->get_row("SELECT * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid='".$result->citystateid."'");
                      ?>
                          <tr>
                              <td><?php echo $spintax->process(str_replace('[state-code]', $cstate->statecode, str_replace('[state]', $cstate->state, str_replace('[town]', $cstate->city,  stripslashes($row->answer)))) ); ?></td>
                              <td> - </td>
                              <td style="text-align: right;    width: 8%;">Pending</td>
                          </tr>
                          <?php
                  }else{
                      $gpost = get_post($postid_);
                if(!empty($gpost)){ 
                        ?>
                        <tr>
                           <td><?php echo $gpost->post_title;  ?></td>
                           <td><a target="_blank"  href="<?php echo get_permalink($gpost->ID); ?>"><?php echo get_permalink($gpost->ID); ?></a></td>
                           <td style="text-align: right;    width: 8%;">Complete</td>
                       </tr>  <?php 
                } 
            }
                
            }
            if(empty($results)){ ?>
                   <tr>
                       <td style="color: rgb(217, 159, 4);" colspan="5">Record Not Found</td> 
                       </tr>
            <?php }
            ?></table>  <?php
               if ($rowcount > 0) {
            $tpage = intval($rowcount / 24);
        if ($tpage == 0)
            $tpage++;
            if (24 < $rowcount)
                if (($tpage * 24) < $rowcount)
                $tpage++;
            $first_button = (1 == 1) ? 'disabled="disabled"' : '';
            $first_class = (1 == 1) ? '' : 'button-primary';
            $first_buttonval = (trim(1) != '1') ? 1 - 1 : 1;
            $cpage = (1 - 1) * 24;      
    }
                  if($rowcount > 24) {   ?>   
                         <div class="spagination" style="float:left;width:100%;">
                            <?php   if ($rowcount > 0) {
            ?><div style="float: left; width: 100%; margin-top: 10px; margin-bottom: 15px;">
                   
                     <div style="float:left;" rel="<?php echo $tpage; ?>">
                         <button id="p_btnsub" class="button  citystatepagepaging <?php echo $first_class; ?>" value="1" rel="first" <?php echo $first_button; ?>>First</button>
                         <button id="p_btnsub" class="prev_btn1 button <?php echo $first_class; ?>  citystatepagepaging" value="<?php echo $first_buttonval; ?>" rel="previous" <?php echo $first_button; ?> >Previous</button><?php
              $sstart = 9;
            $exshow = 0;
            $mstart = intval($tpage/2) - 4;
            if ($value > 7 ) {
                 $sstart = 4; 
                   if( $tpage - 2 > $value){
                $mstart = $value - 3;
                   }
            } 
            $estart = $tpage - 3; 
            $sflag = false;
            $mflag = false;
            $continue =false;
            for ($i = 1; $i <= $tpage; $i++) {
                   if ($sstart > $i) {
                     $continue = false;
                 } else {
                     if (!$sflag) {
                         echo '...';
                         $sflag = true;
                     }
                     if ($mstart < $i && $mstart + 8 > $i) {
                         $continue = false;
                     } else {
                         if ($mstart + 4 < $i) {
                             if (!$mflag) {
                                 echo '...';
                                 $mflag = true;
                             }
                         }
                         if($estart < $i){
                                $continue = false;
                         }else{
                             
                             $continue = true;
                         }
                         
                     }
                 } 
                 if ($continue) {
                     continue;
                 }
                $selected = (1 == $i) ? 'selected' : 'button-primary';
                $desabled = (1 == $i) ? 'disabled="disabled"' : '';
               ?><button style=" margin: 0 2px;" id="p_btnsub_c" class="button <?php echo $selected; ?>  citystatepagepaging" value="<?php echo $i; ?>" rel="<?php echo $i; ?>" <?php echo $desabled; ?>><?php echo $i; ?></button><?php
            }
            $previous_button_val = (1 < $tpage) ? 1 + 1 : $tpage;
            $previous_disabled = (1 == $tpage) ? 'disabled="disabled"' : '';
            $last_disabled = (1 == $tpage) ? 'disabled="disabled"' : '';
            $previous_class = (1 == $tpage) ? '' : 'button-primary';
        ?> <button id="p_btnsub" class="next_btn1 button <?php echo $previous_class; ?>  citystatepagepaging" value="<?php echo $previous_button_val; ?>" rel="next" <?php echo $previous_disabled; ?> >Next</button>
<button id="p_btnsub" class="button <?php echo $previous_class; ?>  citystatepagepaging" value="<?php echo $tpage; ?>" rel="last" <?php echo $last_disabled; ?>>Last</button>
                     </div>
                     <div class="pg_loading" style="margin-top: 2px;display:none;"></div>
                 </div><?php
        } else {
            
        }
                     ?>   
                    </div>
                    
                  <?php } ?>
            </div>
            </div>
            </div>

            <div id="faqs_do_spin" style="display:none;width: 725px;margin-top: 40px;">
                <span class="button b-close"><span>X</span></span> 
                <div class="woodotrash woodocontent">
                    <h1 class="faqs_title" >Question Spin</h1>       
                    <span  style="float: left; width: 100%; margin-bottom: 6px;">Spin Options: </span>

                    <div style="float: left; width: 49%;">
                        <div style="margin-bottom: 4px;">
                        <label>spintax_format </label>
                        <select class="faq-spinselect spintax_format">
                            <option value="{|}">{|}</option>
                            <option value="{~}">{~}</option>
                            <option value="[|]">[|]</option>
                            <option value="[spin]">[spin]</option>
                            <option value="#SPIN">#SPIN</option>
                        </select>
                    </div> 
                        <div style="margin-bottom: 4px;">
                        <label>confidence_level </label>
                        <select class="faq-spinselect confidence_level">
                            <option value="low">low</option>
                            <option value="medium">medium</option>
                            <option value="high">high</option> 
                        </select>
                    </div>   
                        <div style="margin-bottom: 4px;">
                             <label>protected_terms </label>
                             <textarea class="protected_terms" style="width: 96%; margin-top: 6px;"></textarea>
                             <span style="font-size: 12px; color: rgb(152, 152, 152);">Can be added more than one keyword and separated by ( , )</span>
                        </div>

                    </div>
                    <div style="float: left; width: 49%;"> 
                        <label><input class="faq-spinoption" type="checkbox" value="auto_protected_terms"/> auto_protected_terms</label>  <br>
                        <label><input class="faq-spinoption" type="checkbox" value="auto_sentences"/>auto_sentences</label> <br>
                        <label><input class="faq-spinoption" type="checkbox" value="auto_paragraphs"/>auto_paragraphs</label>  <br>
                        <label><input class="faq-spinoption" type="checkbox" value="auto_new_paragraphs"/> auto_new_paragraphs</label>    <br>    
                        <label><input class="faq-spinoption" type="checkbox" value="auto_sentence_trees"/> auto_sentence_trees</label>    <br>    
                        <label><input class="faq-spinoption" type="checkbox" value="use_only_synonyms"/> use_only_synonyms</label>        <br>   
                        <label><input class="faq-spinoption" type="checkbox" value="nested_spintax"/> nested_spintax</label>          <br>
                        <label><input class="faq-spinoption" type="checkbox" value="reorder_paragraphs"/> reorder_paragraphs</label>
                    </div> 

                    <div style="float: left; width: 100%; margin-top: 12px;">
                        <span>Spin Text</span>
                        <textarea class="faq-spincontent" style="float: left; width: 100%; margin-top: 7px; margin-bottom: 10px;"></textarea>
                    </div>

                    <div>
                        <button class="button button-primary faq_spinbutton"  style="font-size: 16px; float: left; margin-bottom: 10px; margin-top: 2px;">Spin</button>
                        <span id="woo_do_title">
                             <span class="woodoload" style="display:none;"></span>
                            <div class="woo_do_msg"> 

                          </div>                  
                        </span> 
                    </div> 

                    <div> 
                        <textarea readonly class="faq-spinvcontent" placeholder="Variation Content ..." style="float: left; width: 100%; margin-top: 7px;height: 170px;"></textarea>
                    </div>
                    <div>
                         <button class="button button-primary faq_splitsubmit"  style="font-size: 16px; float: left; margin-top: 12px; margin-right: 12px;">Submit</button>
                         <button class="button button-cancel faq_splitclose"  style="margin-top: 12px; font-size: 16px;float:left;">Cancel</button>
                    </div>

                </div> 
            </div>
            <?php
            break;
        }
    }
}

add_action("faq_manage", "faq_manage_func");


function randomlist($random){
 $ranvalue = array(
'#19#',
'#30#,#45#',
'#1#,#30#,#55#',
'#2#,#15#,#35#,#55#',
'#1#,#12#,#24#,#36#,#48#',
'#2#,#10#,#20#,#30#,#40#,#55#',
'#1#,#9#,#17#,#26#,#35#,#43#,#52#',
'#1#,#8#,#16#,#23#,#31#,#38#,#46#,#53#',
'#1#,#7#,#14#,#20#,#27#,#34#,#40#,#45#,#52#',
'#1#,#7#,#13#,#19#,#25#,#31#,#37#,#43#,#49#,#55#',
'#1#,#6#,#11#,#17#,#22#,#28#,#33#,#39#,#44#,#50#,#55#',
'#1#,#6#,#11#,#16#,#21#,#26#,#31#,#36#,#41#,#46#,#51#,#56#',
'#1#,#5#,#10#,#14#,#19#,#24#,#28#,#33#,#37#,#42#,#47#,#51#,#56#',
'#1#,#6#,#9#,#13#,#18#,#22#,#26#,#30#,#35#,#39#,#43#,#48#,#52#,#56#',
'#2#,#6#,#9#,#13#,#17#,#21#,#25#,#29#,#33#,#37#,#41#,#45#,#49#,#53#,#57#',
'#1#,#4#,#8#,#12#,#16#,#19#,#23#,#27#,#31#,#34#,#38#,#42#,#46#,#49#,#53#,#57#',
'#2#,#5#,#8#,#11#,#15#,#18#,#22#,#25#,#29#,#32#,#36#,#39#,#43#,#46#,#50#,#53#,#57#',
'#1#,#4#,#7#,#10#,#14#,#17#,#20#,#24#,#27#,#30#,#34#,#37#,#40#,#44#,#47#,#50#,#54#,#57#',
'#1#,#4#,#7#,#10#,#13#,#16#,#19#,#23#,#26#,#29#,#32#,#35#,#38#,#41#,#45#,#48#,#51#,#54#,#57#',
'#1#,#4#,#7#,#10#,#13#,#16#,#19#,#22#,#25#,#28#,#31#,#34#,#37#,#40#,#43#,#46#,#49#,#52#,#55#,#58#',
'#1#,#3#,#6#,#9#,#12#,#15#,#18#,#20#,#23#,#26#,#29#,#32#,#35#,#38#,#40#,#43#,#46#,#49#,#52#,#55#,#58#',
'#1#,#3#,#6#,#9#,#11#,#14#,#17#,#20#,#22#,#25#,#28#,#30#,#33#,#36#,#39#,#41#,#44#,#47#,#49#,#52#,#55#,#58#',
'#1#,#3#,#6#,#8#,#11#,#14#,#16#,#19#,#21#,#24#,#27#,#29#,#32#,#34#,#37#,#40#,#42#,#45#,#47#,#50#,#53#,#55#,#58#',
'#1#,#3#,#6#,#8#,#11#,#13#,#16#,#18#,#21#,#23#,#26#,#28#,#31#,#33#,#36#,#38#,#41#,#43#,#46#,#48#,#51#,#53#,#56#,#58#',
'#1#,#3#,#5#,#8#,#10#,#13#,#15#,#17#,#20#,#22#,#25#,#27#,#29#,#32#,#34#,#37#,#39#,#41#,#44#,#46#,#49#,#51#,#53#,#56#,#58#',
'#1#,#3#,#5#,#7#,#10#,#12#,#14#,#17#,#19#,#21#,#24#,#26#,#28#,#30#,#33#,#35#,#37#,#40#,#42#,#44#,#47#,#49#,#51#,#53#,#56#,#58#',
'#1#,#3#,#5#,#7#,#9#,#12#,#14#,#16#,#18#,#20#,#23#,#25#,#27#,#29#,#32#,#34#,#36#,#38#,#40#,#43#,#45#,#47#,#49#,#52#,#54#,#56#,#58#',
'#1#,#3#,#5#,#7#,#9#,#11#,#13#,#15#,#18#,#20#,#22#,#24#,#26#,#28#,#30#,#33#,#35#,#37#,#39#,#41#,#43#,#45#,#48#,#50#,#52#,#54#,#56#,#58#',
'#1#,#3#,#5#,#7#,#9#,#11#,#13#,#15#,#17#,#19#,#21#,#23#,#25#,#27#,#29#,#31#,#33#,#36#,#38#,#40#,#42#,#44#,#46#,#48#,#50#,#52#,#54#,#56#,#58#',
'#1#,#3#,#5#,#7#,#9#,#11#,#13#,#15#,#17#,#19#,#21#,#23#,#25#,#27#,#29#,#31#,#33#,#35#,#37#,#39#,#41#,#43#,#45#,#47#,#49#,#51#,#53#,#55#,#57#,#59#',
'#1#,#2#,#4#,#6#,#8#,#10#,#12#,#14#,#16#,#18#,#20#,#22#,#24#,#26#,#28#,#29#,#31#,#33#,#35#,#37#,#39#,#41#,#43#,#45#,#47#,#49#,#51#,#53#,#55#,#56#,#58#',
'#1#,#2#,#4#,#6#,#8#,#10#,#12#,#14#,#15#,#17#,#19#,#21#,#23#,#25#,#27#,#29#,#30#,#32#,#34#,#36#,#38#,#40#,#42#,#44#,#45#,#47#,#49#,#51#,#53#,#55#,#57#,#58#',
'#1#,#2#,#4#,#6#,#8#,#10#,#11#,#13#,#15#,#17#,#19#,#20#,#22#,#24#,#26#,#28#,#29#,#31#,#33#,#35#,#37#,#39#,#40#,#42#,#44#,#46#,#48#,#49#,#51#,#53#,#55#,#57#,#58#',
'#1#,#2#,#4#,#6#,#8#,#9#,#11#,#13#,#15#,#16#,#18#,#20#,#22#,#23#,#25#,#27#,#29#,#30#,#32#,#34#,#36#,#37#,#39#,#41#,#43#,#45#,#46#,#48#,#50#,#52#,#53#,#55#,#57#,#59#',
'#1#,#2#,#4#,#6#,#7#,#9#,#11#,#12#,#14#,#16#,#18#,#19#,#21#,#23#,#24#,#26#,#28#,#30#,#31#,#33#,#35#,#36#,#38#,#40#,#42#,#43#,#45#,#47#,#48#,#50#,#52#,#54#,#55#,#57#,#59#',
'#1#,#2#,#4#,#5#,#7#,#9#,#10#,#12#,#14#,#15#,#17#,#19#,#20#,#22#,#24#,#25#,#27#,#29#,#30#,#32#,#34#,#35#,#37#,#39#,#40#,#42#,#44#,#45#,#47#,#49#,#50#,#52#,#54#,#55#,#57#,#59#',
'#1#,#2#,#4#,#5#,#7#,#9#,#10#,#12#,#13#,#15#,#17#,#18#,#20#,#22#,#23#,#25#,#26#,#28#,#30#,#31#,#33#,#35#,#36#,#38#,#39#,#41#,#43#,#44#,#46#,#47#,#49#,#51#,#52#,#54#,#56#,#57#,#59#',
'#1#,#2#,#4#,#5#,#7#,#8#,#10#,#11#,#13#,#15#,#16#,#18#,#19#,#21#,#22#,#24#,#26#,#27#,#29#,#30#,#32#,#33#,#35#,#37#,#38#,#40#,#41#,#43#,#44#,#46#,#48#,#49#,#51#,#52#,#54#,#55#,#57#,#59#',
'#1#,#2#,#4#,#5#,#7#,#8#,#10#,#11#,#13#,#14#,#16#,#17#,#19#,#20#,#22#,#23#,#25#,#27#,#28#,#30#,#31#,#33#,#34#,#36#,#37#,#39#,#40#,#42#,#43#,#45#,#46#,#48#,#49#,#51#,#53#,#54#,#56#,#57#,#59#',
'#1#,#2#,#4#,#5#,#7#,#8#,#10#,#11#,#13#,#14#,#16#,#17#,#19#,#20#,#22#,#23#,#25#,#26#,#28#,#29#,#31#,#32#,#34#,#35#,#37#,#38#,#40#,#41#,#43#,#44#,#46#,#47#,#49#,#50#,#52#,#53#,#55#,#56#,#58#,#59#',
'#1#,#2#,#3#,#5#,#6#,#8#,#9#,#11#,#12#,#14#,#15#,#17#,#18#,#19#,#21#,#22#,#24#,#25#,#27#,#28#,#30#,#31#,#33#,#34#,#36#,#37#,#38#,#40#,#41#,#43#,#44#,#46#,#47#,#49#,#50#,#52#,#53#,#55#,#56#,#57#,#59#',
'#1#,#2#,#3#,#5#,#6#,#8#,#9#,#10#,#12#,#13#,#15#,#16#,#18#,#19#,#20#,#22#,#23#,#25#,#26#,#27#,#29#,#30#,#32#,#33#,#35#,#36#,#37#,#39#,#40#,#42#,#43#,#45#,#46#,#47#,#49#,#50#,#52#,#53#,#54#,#56#,#57#,#59#',
'#1#,#2#,#3#,#5#,#6#,#7#,#9#,#10#,#12#,#13#,#14#,#16#,#17#,#19#,#20#,#21#,#23#,#24#,#26#,#27#,#28#,#30#,#31#,#32#,#34#,#35#,#37#,#38#,#39#,#41#,#42#,#44#,#45#,#46#,#48#,#49#,#51#,#52#,#53#,#55#,#56#,#57#,#59#',
'#1#,#2#,#3#,#5#,#6#,#7#,#9#,#10#,#11#,#13#,#14#,#15#,#17#,#18#,#20#,#21#,#22#,#24#,#25#,#26#,#28#,#29#,#30#,#32#,#33#,#35#,#36#,#37#,#39#,#40#,#41#,#43#,#44#,#45#,#47#,#48#,#49#,#51#,#52#,#54#,#55#,#56#,#58#,#59#',
'#1#,#2#,#3#,#4#,#6#,#7#,#8#,#10#,#11#,#12#,#14#,#15#,#16#,#18#,#19#,#20#,#22#,#23#,#24#,#26#,#27#,#28#,#30#,#31#,#32#,#34#,#35#,#36#,#38#,#39#,#40#,#42#,#43#,#44#,#46#,#47#,#48#,#50#,#51#,#52#,#54#,#55#,#56#,#58#,#59#',
'#1#,#2#,#3#,#4#,#6#,#7#,#8#,#10#,#11#,#12#,#14#,#15#,#16#,#17#,#19#,#20#,#21#,#23#,#24#,#25#,#27#,#28#,#29#,#30#,#32#,#33#,#34#,#36#,#37#,#38#,#40#,#41#,#42#,#43#,#45#,#46#,#47#,#49#,#50#,#51#,#53#,#54#,#55#,#56#,#58#,#59#',
'#1#,#2#,#3#,#4#,#6#,#7#,#8#,#9#,#11#,#12#,#13#,#14#,#16#,#17#,#18#,#20#,#21#,#22#,#23#,#25#,#26#,#27#,#28#,#30#,#31#,#32#,#34#,#35#,#36#,#37#,#39#,#40#,#41#,#42#,#44#,#45#,#46#,#47#,#49#,#50#,#51#,#53#,#54#,#55#,#56#,#58#,#59#',
'#1#,#2#,#3#,#4#,#6#,#7#,#8#,#9#,#11#,#12#,#13#,#14#,#16#,#17#,#18#,#19#,#21#,#22#,#23#,#24#,#26#,#27#,#28#,#29#,#31#,#32#,#33#,#34#,#36#,#37#,#38#,#39#,#41#,#42#,#43#,#44#,#46#,#47#,#48#,#49#,#51#,#52#,#53#,#54#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#7#,#8#,#9#,#10#,#11#,#13#,#14#,#15#,#16#,#18#,#19#,#20#,#21#,#22#,#24#,#25#,#26#,#27#,#29#,#30#,#31#,#32#,#33#,#35#,#36#,#37#,#38#,#40#,#41#,#42#,#43#,#44#,#46#,#47#,#48#,#49#,#51#,#52#,#53#,#54#,#55#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#7#,#8#,#9#,#10#,#11#,#13#,#14#,#15#,#16#,#17#,#19#,#20#,#21#,#22#,#23#,#25#,#26#,#27#,#28#,#29#,#31#,#32#,#33#,#34#,#35#,#37#,#38#,#39#,#40#,#41#,#43#,#44#,#45#,#46#,#47#,#49#,#50#,#51#,#52#,#53#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#8#,#9#,#10#,#11#,#12#,#13#,#15#,#16#,#17#,#18#,#19#,#20#,#22#,#23#,#24#,#25#,#26#,#27#,#29#,#30#,#31#,#32#,#33#,#34#,#36#,#37#,#38#,#39#,#40#,#41#,#43#,#44#,#45#,#46#,#47#,#48#,#50#,#51#,#52#,#53#,#54#,#55#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#17#,#18#,#19#,#20#,#21#,#22#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#40#,#41#,#42#,#43#,#44#,#45#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#54#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',

'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#',
'#1#,#2#,#3#,#4#,#5#,#6#,#7#,#8#,#9#,#10#,#11#,#12#,#13#,#14#,#15#,#16#,#17#,#18#,#19#,#20#,#21#,#22#,#23#,#24#,#25#,#26#,#27#,#28#,#29#,#30#,#31#,#32#,#33#,#34#,#35#,#36#,#37#,#38#,#39#,#40#,#41#,#42#,#43#,#44#,#45#,#46#,#47#,#48#,#49#,#50#,#51#,#52#,#53#,#54#,#55#,#56#,#57#,#58#,#59#,#60#'

);
    return $ranvalue[$random+1];
}

function faq_process_func() {

    extract($_REQUEST);

    if (!isset($act) || $act == '') {

        _redirect("admin.php?page=myfaq-manager");
    }

    global $wpdb;

    switch ($act) {

        case 'new': {
            if (empty($question)) {

                $err['FAQ Question'] = "Project Name Missing";
            }

            if (empty($answer)) {

                $err['FAQ Answer'] = "Project Title Missing";
            } 

            if ($err) {

                echo "<h1>Following error occured:</h1>";

                foreach ($err as $key => $error) {

                    echo "<strong>{$key}</strong> : {$error}<br/>";
                }

                die("<a href='admin.php?page=myfaq-manage&act=new' class='button-primary'>Try Again</a>");
                
            } else {
                if (empty($uri)) {  $uri = ''; } 
                if (empty($post_to_clone)) {  $post_to_clone = ''; } 
                if (empty($spintext_metadesc)) {  $spintext_metadesc = ''; }  
                $post_ = $_POST;
                if (empty($schedule_time_start) ||  $schedule_time_start == '') {  $schedule_time_start = '0'; }  
                if (empty($schedule_time_stop) ||  $schedule_time_stop == '') {  $schedule_time_stop = '0'; }  

                if (empty($post_min) ||  $post_min == '') {  $post_min = '0'; }  
                if (empty($post_max) ||  $post_max == '') {  $post_max = '0'; }  

                if (empty($schedule_posts_per_hour) ||  $schedule_posts_per_hour == '') {  $schedule_posts_per_hour = '0'; }  
                $schedule_dates = array();       
                if (!empty($schedule_sunday) &&   $schedule_sunday == 'sunday') { $schedule_dates[] = $schedule_sunday;  }  
                if (!empty($schedule_monday) &&   $schedule_monday == 'monday') {   $schedule_dates[] = $schedule_monday; }  
                if (!empty($schedule_tuesday) &&   $schedule_tuesday == 'tuesday') {  $schedule_dates[] = $schedule_tuesday; }  
                if (!empty($schedule_wednesday) &&   $schedule_wednesday == 'wednesday') {  $schedule_dates[] = $schedule_wednesday; }  
                if (!empty($schedule_thursday) &&   $schedule_thursday == 'thursday') {  $schedule_dates[] = $schedule_thursday;}  
                if (!empty($schedule_friday) &&   $schedule_friday == 'friday') {  $schedule_dates[] = $schedule_friday; }  
                if (!empty($schedule_saturday) &&   $schedule_saturday == 'saturday') {   $schedule_dates[] = $schedule_saturday; }   
                
                if (empty($spintext_excerpt)) {  $spintext_excerpt = ''; } 
                if (empty($spintext_metatitle)) {  $spintext_metatitle = ''; }  
                if (empty($seoswitch)) {  $seoswitch = '0'; }  
                if (empty($spinstatecity)) {  $spinstatecity = ''; }  
                if (empty($changeposttitleselect)) {  $changeposttitleselect = ''; }  
                if (empty($secondtitle)) {  $secondtitle = ''; }  

                if (empty($main_hub_location)) {  $main_hub_location = ''; }
                if (empty($state_hub_location)) {  $state_hub_location = ''; }
                if (empty($state_google)) {  $state_google = 0; }
                if (empty($state_tag)) {  $state_tag = ''; }
                if (empty($state_link_text)) {  $state_link_text = ''; }
                if (empty($city_google)) {  $city_google = 0; }
                if (empty($city_tag)) {  $city_tag = ''; }
                if (empty($city_link_text)) {  $city_link_text = ''; }

                $id = $wpdb->insert(MYFAQ_TABLE, array(
                    'question' => $question,
                    'answer' => $answer,
                    'uri' => $uri,
                    'post_to_clone' => $post_to_clone, 
                    'main_hub_location' => $main_hub_location, 
                    'state_hub_location' => $state_hub_location, 
                    'state_google' => $state_google, 
                    'state_tag' => $state_tag, 
                    'state_link_text' => $state_link_text, 
                    'city_google' => $city_google, 
                    'city_tag' => $city_tag, 
                    'city_link_text' => $city_link_text, 
                    'main_title' => $main_title, 
                    'main_metadesc' => $main_metadesc, 
                    'state_title' => $state_title, 
                    'state_metadesc' => $state_metadesc, 
                    'hub_page_published' => 0,
                    'datetime' => date('Y-m-d H:i:s'),
                    'excerpt' => stripslashes($spintext_excerpt),
                    'secondtitle' =>     $secondtitle,
                    )
                );
                $spintext =array(); 
                foreach($post_ as $key => $value){
                    if(strpos($key, 'spintext_sc') !== false){
                        $spintext[$key] = $value;
                    }
                } 
                $id = $wpdb->insert_id;
                if($id){
                    $wpdb->update(
                        AUTOPOST_CITYSTATEPAGE_TABLE, array(  
                        'projectid' => $id, 
                        ),array(
                             'projectid' =>$spinstatecity
                        )
                    );  
                    $_SESSION['spintextnewid'] = strtotime('now').rand(99, 9999);
                    $id_ = $wpdb->insert(
                            AUTOPOST_SPINTXT_TABLE, array(
                        'project_id' => $id,
                        'spintext_sc1' => '',
                        'spintext_sc2' => '',
                        'spintext_sc3' => '',
                        'spintext_sc4' => '',
                        'spintext_sc5' => '',
                        'spintext_metadesc' => $spintext_metadesc,
                        'spintext_metatitle' => $spintext_metatitle,
                        'seopen' => $seoswitch,
                            )
                    );             
                    foreach($spintext as $key => $value){
                        $options_ = $wpdb->get_var("SELECT option_id FROM ".$wpdb->prefix."options WHERE option_name = '".'_'.$id.$key."'");
                        if(empty($options_)){
                            $id_ = $wpdb->insert(
                                $wpdb->prefix.'options', array(
                            'option_name' => '_'.$id.$key, 
                            'option_value' => $value, 
                            'autoload' => 'no', 
                                )
                            ); 
                        }else{
                           $wpdb->update(
                             $wpdb->prefix.'options', array(  
                           'option_value' => $value, 
                                ),array( 
                                    'option_id' =>$options_,
                                )
                            );  
                        }
                       
                    }
                    $random = '55';
                    if($schedule_posts_per_hour == '5'){
                         $random = rand(1, 10);
                    }
                    else if($schedule_posts_per_hour == '15'){
                        $random = rand(11, 20);
                    }
                    else if($schedule_posts_per_hour == '25'){
                        $random = rand(21, 30);
                    }
                    else if($schedule_posts_per_hour == '35'){
                        $random = rand(31,40);
                    }
                    else if($schedule_posts_per_hour == '45'){
                        $random = rand(41, 50);
                    }
                    else if($schedule_posts_per_hour == '55'){
                        $random = rand(51,60);
                    }
					else if($schedule_posts_per_hour == '65'){
                        $random = rand(61,70);
                    }
					else if($schedule_posts_per_hour == '75'){
                        $random = rand(71,80);
                    }
					else if($schedule_posts_per_hour == '95'){
                        $random = rand(91,100);
                    }
					else if($schedule_posts_per_hour == '250'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '450'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '800'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '1500'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '3500'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '7500'){
						$random = 60;
					}
                    $id_ = $wpdb->insert(
                        AUTOPOST_SCHEDULING_TABLE, array(
                        'project_id' => $id,
                        'weekdays' => implode(',', $schedule_dates),  
                        'time_start' => $schedule_time_start,  
                        'time_end' => $schedule_time_stop,  
                        'random_post' => $schedule_posts_per_hour,  
                        'post_min' => $post_min,  
                        'post_max' => $post_max,  
                        'changetitle' => $changeposttitleselect,  
                                 'randomtime' => randomlist($random),
                        )
                    ); 
                    }  
                    _redirect("admin.php?page=myfaq-manager&m=a");
                }

                break;
            }

        case 'edit': {

                if (empty($question)) {
                    $err['FAQ Question'] = "Project Name Missing";
                }

                if (empty($answer)) {
                    $err['FAQ Answer'] = "Project Title Missing";
                }
                if (empty($uri)) {
                    $uri = '';
                }
                if (empty($post_to_clone)) {
                    $post_to_clone = '';
                }
                if (empty($spintext_metadesc)) {
                    $spintext_metadesc = '';
                }

                if (empty($schedule_time_start) || $schedule_time_start == '') {
                    $schedule_time_start = '0';
                }
                if (empty($schedule_time_stop) || $schedule_time_stop == '') {
                    $schedule_time_stop = '0';
                }

				if (empty($post_min) ||  $post_min == '') {  $post_min = '0'; }  
				if (empty($post_max) ||  $post_max == '') {  $post_max = '0'; }  

                if (empty($schedule_posts_per_hour) || $schedule_posts_per_hour == '') {
                    $schedule_posts_per_hour = '0';
                }
                $schedule_dates = array();
                $post_ = $_POST;
                if (!empty($schedule_sunday) && $schedule_sunday == 'sunday') {
                    $schedule_dates[] = $schedule_sunday;
                }
                if (!empty($schedule_monday) && $schedule_monday == 'monday') {
                    $schedule_dates[] = $schedule_monday;
                }
                if (!empty($schedule_tuesday) && $schedule_tuesday == 'tuesday') {
                    $schedule_dates[] = $schedule_tuesday;
                }
                if (!empty($schedule_wednesday) && $schedule_wednesday == 'wednesday') {
                    $schedule_dates[] = $schedule_wednesday;
                }
                if (!empty($schedule_thursday) && $schedule_thursday == 'thursday') {
                    $schedule_dates[] = $schedule_thursday;
                }
                if (!empty($schedule_friday) && $schedule_friday == 'friday') {
                    $schedule_dates[] = $schedule_friday;
                }
                if (!empty($schedule_saturday) && $schedule_saturday == 'saturday') {
                    $schedule_dates[] = $schedule_saturday;
                }
                if (empty($spintext_excerpt)) {
                    $spintext_excerpt = '';
                }
                if (empty($secondtitle)) {
                    $secondtitle = '';
                }
                if (empty($changeposttitleselect)) {
                    $changeposttitleselect = '';
                }
                if (empty($spintext_metatitle)) {
                    $spintext_metatitle = '';
                }
                if (empty($seoswitch)) {
                    $seoswitch = '0';
                }

                if ($err) {
                    echo "<h1>Following error occured:</h1>";
                    foreach ($err as $key => $error) {
                        echo "<strong>{$key}</strong> : {$error}<br/>";
                    }
                    die("<a href='admin.php?page=myfaq-manage&act=new' class='button-primary'>Try Again</a>");
                } else {

                    $wpdb->update(
                            MYFAQ_TABLE, array(
                        'question' => $question,
                        'answer' => $answer,
                        'uri' => $uri,
                        'post_to_clone' => $post_to_clone,
                        'main_hub_location' => $main_hub_location, 
                        'state_hub_location' => $state_hub_location, 
                        'state_google' => $state_google, 
                        'state_tag' => $state_tag, 
                        'state_link_text' => $state_link_text, 
                        'city_google' => $city_google, 
                        'city_tag' => $city_tag, 
                        'city_link_text' => $city_link_text, 
                        'hub_page_published' => 0,
                        'main_title' => $main_title, 
                        'main_metadesc' => $main_metadesc, 
                        'state_title' => $state_title, 
                        'state_metadesc' => $state_metadesc, 
                        'excerpt' => stripslashes($spintext_excerpt),
                        'secondtitle' => $secondtitle,
                            ), array(
                        'id' => $id
                            )
                    );


					if (empty($list_act) || $post_min == '') {

                    $wpdb->update(
                            AUTOPOST_SPINTXT_TABLE, array(
                        'spintext_sc1' => '',
                        'spintext_sc2' => '',
                        'spintext_sc3' => '',
                        'spintext_sc4' => '',
                        'spintext_sc5' => '',
                        'spintext_metadesc' => $spintext_metadesc,
                        'spintext_metatitle' => $spintext_metatitle,
                        'seopen' => $seoswitch,
                            ), array(
                        'project_id' => $id
                            )
                    );
                    $spintext = array();
                    foreach ($post_ as $key => $value) {
                        if (strpos($key, 'spintext_sc') !== false) {
                            $spintext[$key] = $value;
                        }
                    }                    
        
                    $cpage =  $wpdb->get_results("SELECT * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid NOT IN (SELECT citystateid FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE  projectid = '".$id."')");
                     foreach ($cpage as $_key => $_value) { 
                        $sql = "DELETE FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$_value->citystateid."'";
                        $wpdb->query($sql); 
                        
                        $results =  $wpdb->get_results("SELECT * FROM ". AUTOPOST_CITYSTATEMETA_TABLE ." WHERE  project_id = '".$id."' AND citystateid = '".$_value->citystateid."'"); 
                        foreach($results as $result){
                            wp_delete_post($result->cloneid,true);
                        }
                        
                        $sql = "DELETE FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE project_id = '".$id."' AND citystateid = '".$_value->citystateid."'";
                        $wpdb->query($sql);
                        
                    }

                    $ppage = $wpdb->get_results("SELECT * FROM " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = '" . $id . "' AND citystateid NOT IN (SELECT citystateid FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE  projectid = '" . $id . "')");
                    foreach ($ppage as $_key => $_value) {
                        $wpdb->insert(
                                AUTOPOST_CITYSTATEPAGE_TABLE, array(
        'projectid' => $_value->projectid,
        'citystateid' => $_value->citystateid,
        'state' => $_value->state,
                                ) 
                        );
                    }

                    $wpdb->query("DELETE FROM  " . $wpdb->prefix . "options  WHERE option_name like '_" . $id . "spintext_sc%'");

                    $wpdb->update(
                            AUTOPOST_CITYSTATEPAGE_TABLE, array(
                        'state' => '0'
                            ), array(
                        'projectid' => $id
                            )
                    );

                    foreach ($spintext as $key => $value) {
                        $id_ = $wpdb->insert(
                                $wpdb->prefix . 'options', array(
                            'option_name' => '_' . $id . $key,
                            'option_value' => $value,
                            'autoload' => 'no',
                                )
                        );
						}

					}
                    $results = $wpdb->get_results("SELECT post_id FROM " . $wpdb->prefix . "postmeta WHERE meta_key='autospinproject' AND meta_value='" . $id . "';");
                    /*
                    foreach ($results as $result) {
                        $wpdb->update($wpdb->prefix . "posts", array(
                            'post_status' => 'pending'
                                ), array(
                            'ID' => $result->post_id
                                )
                        );
                    }*/
                    
                    $random = '55';
                    if($schedule_posts_per_hour == '5'){
                         $random = rand(1, 10);
                    }
                    else if($schedule_posts_per_hour == '15'){
                        $random = rand(11, 20);
                    }
                    else if($schedule_posts_per_hour == '25'){
                        $random = rand(21, 30);
                    }
                    else if($schedule_posts_per_hour == '35'){
                        $random = rand(31,40);
                    }
                    else if($schedule_posts_per_hour == '45'){
                        $random = rand(41, 50);
                    }
                    else if($schedule_posts_per_hour == '55'){
                        $random = rand(51,60);
                    }
					else if($schedule_posts_per_hour == '65'){
                        $random = rand(61,70);
                    }
					else if($schedule_posts_per_hour == '75'){
                        $random = rand(71,80);
                    }
					else if($schedule_posts_per_hour == '95'){
                        $random = rand(91,100);
                    }
					else if($schedule_posts_per_hour == '250'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '450'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '800'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '1500'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '3500'){
						$random = 60;
					}
					else if($schedule_posts_per_hour == '7500'){
						$random = 60;
					}
                    
                    $wpdb->update(
                            AUTOPOST_SCHEDULING_TABLE, array(
                        'weekdays' => implode(',', $schedule_dates),
                        'time_start' => $schedule_time_start,
                        'time_end' => $schedule_time_stop,
                        'changetitle' => $changeposttitleselect,
                        'random_post' => $schedule_posts_per_hour,
						'post_min' => $post_min,
						'post_max' => $post_max,
                        'randomtime' =>  randomlist($random),
                        'ndate' => '0',
                            ), array(
                        'project_id' => $id
                            )
                    ); 
                    
                    _redirect("admin.php?page=myfaq-manager&m=e");
                }    
        
                break;
            }
    }
}

add_action("faq_process", "faq_process_func");

function faq_city_state_func() {
    global $wpdb; 
    $rowcount = $wpdb->get_var( "SELECT count(citystateid) FROM ".AUTOPOST_CITYSTATE_TABLE.";");  
    $rows = $wpdb->get_results( "SELECT * FROM ".AUTOPOST_CITYSTATE_TABLE." ORDER BY citystateid DESC LIMIT 0,12"); 
     if ($rowcount > 0) {
            $tpage = intval($rowcount / 12);
            if ($tpage == 0)
                $tpage++;
            if (12 < $rowcount)
                if (($tpage * 12) < $rowcount)
                    $tpage++;
            $first_button = (1 == 1) ? 'disabled="disabled"' : '';
            $first_class = (1 == 1) ? '' : 'button-primary';
            $first_buttonval = (trim(1) != '1') ? 1 - 1 : 1;
            $cpage = (1 - 1) * 12;      
     }
    ?>
       <div class='wrap faq-settings'>
                    <div class="icon32" id="icon-link-manager"></div>
                    <h2 style="margin-bottom: 10px;">State AND City</h2> 
                    <div class="citystatepagin">
                        <div class="spagination" style="float: left; width: 100%; margin-bottom: 8px;">
                            <?php    if ($rowcount > 0) { ?>
                              <div style="float:left;">Showing  <?php echo ($cpage?$cpage:1); ?> to  <?php echo ( $cpage + 12 ); ?> of <?php echo $rowcount; ?> entries</div>
                            <?php } ?>
                        </div>
                        <table class="citystatecontent widefat" style="float: left; width: 100%;height:auto;">
                            <thead>
                            <th style="text-align:left;">State</th>
                            <th style="text-align:left;">City</th>
                            <th style="text-align:left;width: 70%;">Embed Map</th>
                            <th style="text-align: right; width: 8%;">Action</th>
                            </thead>
                            <tbody>
                                <?php foreach($rows as $row){ ?>
                                <tr class="citystatetr" id="<?php echo $row->citystateid; ?>">
                                    <td><?php echo $row->state; ?></td>
                                    <td><?php echo $row->city; ?></td>
                                    <td><textarea  readonly  style="width: 100%; background: transparent none repeat scroll 0% 0%; scroll-behavior: unset; resize: unset; border: medium none ! important; box-shadow: 0px 0px 0px;"><?php echo $row->embedmap; ?></textarea></td>
                                    <td style="text-align:right;"><a href="#" class="citystateremove_field deletecitystate"></a></td>
                                </tr> 
                                <?php }
                                if(empty($rows)){ ?>
                                <tr>
                                    <td colspan="10"> Records Not Found. </td>
                                </tr>    
                                <?php }  ?>
                            </tbody>
                        </table>
                        <div class="spagination" style="float:left;width:100%;">
                            <?php   if ($rowcount > 0) {
            ?><div style="float: left; width: 100%; margin-top: 10px; margin-bottom: 15px;">
                   
                     <div style="float:left;" rel="<?php echo $tpage; ?>">
                         <button id="p_btnsub" class="button  citystatepaging <?php echo $first_class; ?>" value="1" rel="first" <?php echo $first_button; ?>>First</button>
                         <button id="p_btnsub" class="prev_btn1 button <?php echo $first_class; ?>  citystatepaging" value="<?php echo $first_buttonval; ?>" rel="previous" <?php echo $first_button; ?> >Previous</button><?php
              $sstart = 9;
            $exshow = 0;
            $mstart = intval($tpage/2) - 4;
            if ($value > 7 ) {
                 $sstart = 4; 
                   if( $tpage - 2 > $value){
                $mstart = $value - 3;
                   }
            } 
            $estart = $tpage - 3; 
            $sflag = false;
            $mflag = false;
            $continue =false;
            for ($i = 1; $i <= $tpage; $i++) {
                   if ($sstart > $i) {
                     $continue = false;
                 } else {
                     if (!$sflag) {
                         echo '...';
                         $sflag = true;
                     }
                     if ($mstart < $i && $mstart + 8 > $i) {
                         $continue = false;
                     } else {
                         if ($mstart + 4 < $i) {
                             if (!$mflag) {
                                 echo '...';
                                 $mflag = true;
                             }
                         }
                         if($estart < $i){
                                $continue = false;
                         }else{
            
                             $continue = true;
                         }
                         
                     }
                 } 
                 if ($continue) {
                     continue;
                 }
                $selected = (1 == $i) ? 'selected' : 'button-primary';
                $desabled = (1 == $i) ? 'disabled="disabled"' : '';
               ?><button style=" margin: 0 2px;" id="p_btnsub_c" class="button <?php echo $selected; ?>  citystatepaging" value="<?php echo $i; ?>" rel="<?php echo $i; ?>" <?php echo $desabled; ?>><?php echo $i; ?></button><?php
            }
            $previous_button_val = (1 < $tpage) ? 1 + 1 : $tpage;
            $previous_disabled = (1 == $tpage) ? 'disabled="disabled"' : '';
            $last_disabled = (1 == $tpage) ? 'disabled="disabled"' : '';
            $previous_class = (1 == $tpage) ? '' : 'button-primary';
        ?> <button id="p_btnsub" class="next_btn1 button <?php echo $previous_class; ?>  citystatepaging" value="<?php echo $previous_button_val; ?>" rel="next" <?php echo $previous_disabled; ?> >Next</button>
<button id="p_btnsub" class="button <?php echo $previous_class; ?>  citystatepaging" value="<?php echo $tpage; ?>" rel="last" <?php echo $last_disabled; ?>>Last</button>
                     </div>
                     <div class="pg_loading" style="margin-top: 2px;display:none;"></div>
                 </div><?php
        } else {
            
        }
                            ?>
                        </div> 
                    </div>
                    <div  style="float: left; width: 100%; max-width: 600px; background: white none repeat scroll 0% 0%; margin-top: 14px; box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.04); border: 1px solid rgb(229, 229, 229);">
                        <div style="float: left; padding: 2%; width: 96%;">
                        <div style="float:left;width:100%;">
                            <label>State : <br> <input class="citystate_state" type="text" style="float: left; width: 50%; max-width: 600px;margin-bottom: 10px; margin-top: 6px;"/></label>
                        </div>
                        <div style="float:left;width:100%;">
                            <label>City :<br> <input class="citystate_city" type="text" style="float: left; width: 50%; max-width: 600px;margin-bottom: 10px; margin-top: 6px;"/></label>
                        </div> 
                        <div style="float:left;width:100%;">
                            <label>Embed Map Iframe :<br> 
                                <textarea class="citystate_iframe" type="text" style="float: left; width: 100%; max-width: 600px;margin-bottom: 10px; margin-top: 6px;"></textarea> 
                            </label>
                        </div> 
                        <input style="margin-top:10px;float:left;" type='submit' name='citystate_button' id='citystate_button' value="Submit" class="button-secondary button-primary citystate_button" />
                        <span class="citystateload" style="display:none;"></span>
                        </div>
                    </div> 
       </div>
<?php
}
function faq_settings_func() {

    if (isset($_POST['sobish'])) {

        if (!wp_verify_nonce($_POST['sobish'], 'sobish_faq_nonce')) {

            wp_die(__('Cheatin&#8217; uh?'));
        }

        // save settings here

        $settings['home'] = isset($_REQUEST['settings']['home']) ? TRUE : FALSE;

        $settings['categories'] = isset($_REQUEST['settings']['categories']) ? TRUE : FALSE;

        $settings['posts'] = isset($_REQUEST['settings']['posts']) ? TRUE : FALSE;

        $settings['pages'] = isset($_REQUEST['settings']['pages']) ? TRUE : FALSE;

        $settings['mastersitedata'] = isset($_REQUEST['settings']['mastersitedata']) ? TRUE : FALSE;

        $settings['mastersitedataurl'] = isset($_REQUEST['settings']['mastersitedataurl']) ? $_REQUEST['settings']['mastersitedataurl'] : '';


        $settings['splin_api_key'] = isset($_REQUEST['settings']['splin_api_key']) ? trim($_REQUEST['settings']['splin_api_key']) : '';
        $settings['spin_email'] = isset($_REQUEST['settings']['spin_email']) ? trim($_REQUEST['settings']['spin_email']) : '';


        update_option("faq_settings", $settings);

        echo '<div id="message" class="updated fade"><p><strong>Settings updated successfully.</strong></p></div>';
    }

    $settings = get_option("faq_settings");



    if ($settings) {

        $settings['home'] = isset($settings['home']) && $settings['home'] == TRUE ? "checked='checked'" : NULL;

        $settings['categories'] = isset($settings['categories']) && $settings['categories'] == TRUE ? "checked='checked'" : NULL;

        $settings['posts'] = isset($settings['posts']) && $settings['posts'] == TRUE ? "checked='checked'" : NULL;

        $settings['pages'] = isset($settings['pages']) && $settings['pages'] == TRUE ? "checked='checked'" : NULL;

        $settings['mastersitedata'] = isset($settings['mastersitedata']) && $settings['mastersitedata'] == TRUE ? "checked='checked'" : NULL;

        $settings['mastersitedataurl'] = isset($settings['mastersitedataurl']) ? $settings['mastersitedataurl'] : '';
    }
                ?>

                <div class='wrap faq-settings'>
                    <div class="icon32" id="icon-link-manager"></div>
                    <h2>Settings</h2>
                    
                    <form action="admin.php?page=myfaq-settings" method="post">
                    <?php wp_nonce_field('sobish_faq_nonce', 'sobish', TRUE, TRUE); ?>
                        
                        <div class="sr_settings_block">   
                            <div class="sr_settings_heading">API Details</div>
                            <div class="sr_settings_content">
                                <p> 
                                    <label  id='spin_email' >API Username: </label>
                                    <input style="min-width: 263px;" name="settings[spin_email]" id="spin_email" type="text" value="<?php echo isset($settings['spin_email']) ? $settings['spin_email'] : ''; ?>"/>
                                </p>
                                <p> 
                                    <label  id='splin_api_key'>Api Key: </label>
                                    <input style="min-width: 263px;" name="settings[splin_api_key]" id="splin_api_key" type="text" value="<?php echo isset($settings['splin_api_key']) ? $settings['splin_api_key'] : ''; ?>"/>
                                </p>

                                <p>
                                    <a href="#" class="test_sr_connection btn button button-primary">Test Account</a>
                                    <strong><span>Status: </span><span class="status"></span></strong>
                                    <span class="woodoload" style="float:right;display:none;"></span>
                                </p>
                            </div>

                        </div>
                        <p>
                            <input type='submit' name='btnSaveSettings' id='btnSaveSettings' value="Save Settings" class="button-secondary" />
                        </p>
                    </form>
                </div>



<div style="float: left; width: 100%; margin-top: 40px;">
    <h3>Cron-Job Details</h3>
    <table>
        <thead>
            <tr>
                <th style="text-align:left;">Command</th>
                <th>Time (Minutes)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td style="padding: 8px 5px;">wget <?php echo site_url(); ?>/wp-content/plugins/boston_web/autoposting.php >/dev/null 2>&1</td>
                <td style="text-align: center;padding: 8px 5px;">1</td>
            </tr>
            <tr>
                <td style="padding: 8px 5px;">wget <?php echo site_url(); ?>/wp-content/plugins/boston_web/changetitle.php >/dev/null 2>&1</td>
                <td style="text-align: center;padding: 8px 5px;">5</td>
            </tr>
        </tbody>
    </table>
</div>
    <?php
}

add_action("faq_city_state", "faq_city_state_func");
add_action("faq_settings", "faq_settings_func");


/*** helping function ***/
if (!function_exists("_redirect")) {

    function _redirect($url) {

        if (!headers_sent())
            wp_redirect($url);
        else
            echo "<meta http-equiv='Refresh' content='0; URL={$url}' />";

        exit();
    }

}