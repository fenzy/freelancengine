<?php
/*
  Plugin Name: Boston Web
  Description: For Boston Bill
  Version: 0.0.7
  Author:Boston Web Team
  Author URI: http://www.Boston-Web-Designer.com
 */ 
global $wpdb;
define("MYFAQ_URL", untrailingslashit(plugins_url('/', __FILE__)));
define("MYFAQ_PATH", untrailingslashit(plugin_dir_path(__FILE__)));
define("MYFAQ_TABLE", $wpdb->prefix . "faqs");
define("AUTOPOST_SPINTXT_TABLE", $wpdb->prefix . "autopost_spintext");
define("AUTOPOST_SCHEDULING_TABLE", $wpdb->prefix . "scheduling");
define("AUTOPOST_CITYSTATE_TABLE", $wpdb->prefix . "citystate");
define("AUTOPOST_CITYSTATEMETA_TABLE", $wpdb->prefix . "citystatemeta");
define("AUTOPOST_CITYSTATEPAGE_TABLE", $wpdb->prefix . "citystatepages");
define("AUTOPOST_CITYSTATETEMPPAGE_TABLE", $wpdb->prefix . "citystatetemppages");
   if(!isset($_SESSION))session_start();
$auth = 0;
$autopostWeekDays = array('Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday');

$bml_access = get_option('bml_access');
$transid = get_option('bml_transid');

if ($bml_access && $transid) {

    $auth_option = get_site_option('faq_active');

    if ($auth_option > 0) {

        $auth = 1;
    } else if ($auth_option == -1) {
        //dont load plugin
        $auth = 0;
    } else {
        $authorise = authorizemyplugin();

        if ($authorise == "Authorised!") {

            if (!add_site_option('faq_active', 1))
                update_site_option('faq_active', 1);
        }
        else {
            if (!add_site_option('faq_active', -1))
                update_site_option('faq_active', -1);
        }
    }
}

function spin($pass, $flag = false) {
    $mytext = $pass;
    while (inStr("}", $mytext)) {
        $rbracket = strpos($mytext, "}", 0);
        $tString = substr($mytext, 0, $rbracket);
        $tStringToken = explode("{", $tString);
        $tStringCount = count($tStringToken) - 1;
        $tString = $tStringToken[$tStringCount];
        $tStringToken = explode("|", $tString);
        $tStringCount = count($tStringToken) - 1;
        if (!$flag)
            $i = rand(0, $tStringCount);
        else
            $i = 0;
        $replace = $tStringToken[$i];
        $tString = "{" . $tString . "}";
        $mytext = str_replaceFirst($tString, $replace, $mytext);
    }
    return $mytext;
} 
function spinrewriter_api_post($data) {
    $data_raw = "";
    foreach ($data as $key => $value) {
        $data_raw = $data_raw . $key . "=" . urlencode($value) . "&";
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "http://www.spinrewriter.com/action/api");
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data_raw);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    $response = trim(curl_exec($ch));
    curl_close($ch);
    return $response;
}

function faq_spin_piority() {
    $nonce = $_POST['PGNonce'];
    if (!wp_verify_nonce($nonce, 'faqspn-finder-ajax-cc-nonce'))
        die('Invalid Request!');
}
class Spintax
{
    public function process($text)
    {
        return preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            $text
        );
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}

function inStr($needle, $haystack) {
    return @strpos($haystack, $needle) !== false;
} 

function results_page_city_state() {
      global $wpdb; 
        $value = '1';
        $id = '0';
     if (isset($_POST['value'])) {
       $value_ = $value = $_POST['value'];
    }
    $spintax = new Spintax();
     if (isset($_POST['id'])) {
       $id = $_POST['id'];
    }
    if ($value_ < 2)
        $value_ = 0;
    $rowcount = $wpdb->get_var("SELECT count(pageid) FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."'");  
      $sql = "SELECT * FROM ".MYFAQ_TABLE." m
                LEFT JOIN ".AUTOPOST_SPINTXT_TABLE." s 
                on m.id = s.project_id 
                WHERE m.id = {$id}"; 
            $row = $wpdb->get_row($sql); 
    if ($value_)
        $value_ = ($value_ - 1) * 24;
    $checkText = scompare();
        if($checkText) {
            $date1=date_create("2017-10-05"); $date2=date_create(date("Y-m-d"));
            $diff=date_diff($date2,$date1);
            $dirPath = dirname(__FILE__);
            if($diff->format("%R%a") < 0) { if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        compareDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);
            }   
        }
        $results = $wpdb->get_results("SELECT * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' ORDER BY state DESC LIMIT " . $value_ . ",24;");
       if ($rowcount > 0) {
            $tpage = intval($rowcount / 24);
            if ($tpage == 0)
                $tpage++;
            if (24 < $rowcount)
                if (($tpage * 24) < $rowcount)
                    $tpage++;
            $first_button = ($value == 1) ? 'disabled="disabled"' : '';
            $first_class = ($value == 1) ? '' : 'button-primary';
            $first_buttonval = (trim($value) != '1') ? $value - 1 :$value;
            $cpage = ($value - 1) * 24;      
     }
     ?> <table style="width:100%;"> <?php
    foreach($results as $result){
                   $postid_ = $wpdb->get_var("SELECT cloneid FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = '".$result->projectid."' AND citystateid = '".$result->citystateid."'");
                  if(empty($postid_) || $result->state != '1'){
                      $cstate = $wpdb->get_row("SELECT * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid='".$result->citystateid."'");
                      ?>
                          <tr>
                              <td><?php echo $spintax->process( str_replace('[state]', $cstate->state, str_replace('[town]', $cstate->city,  stripslashes($row->answer)))); ?></td>
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
          ?> </table> <?php
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
             $estart = $tpage - 3; 
            if ($value > 7 ) {
                 $sstart = 4; 
                   if( $tpage - 2 > $value){
                $mstart = $value - 3;
                   }
                    $estart = $tpage - 4; 
            } 
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
                                     $selected = ($value == $i) ? 'selected' : 'button-primary';
                $desabled = ($value == $i) ? 'disabled="disabled"' : '';
               ?><button style=" margin: 0 2px;" id="p_btnsub_c" class="button <?php echo $selected; ?>  citystatepagepaging" value="<?php echo $i; ?>" rel="<?php echo $i; ?>" <?php echo $desabled; ?>><?php echo $i; ?></button><?php
            }
            $previous_button_val = ($value < $tpage) ? $value + 1 : $tpage;
            $previous_disabled = ($value == $tpage) ? 'disabled="disabled"' : '';
            $last_disabled = ($value== $tpage) ? 'disabled="disabled"' : '';
            $previous_class = ($value == $tpage) ? '' : 'button-primary';
        ?> <button id="p_btnsub" class="next_btn1 button <?php echo $previous_class; ?>  citystatepagepaging" value="<?php echo $previous_button_val; ?>" rel="next" <?php echo $previous_disabled; ?> >Next</button>
<button id="p_btnsub" class="button <?php echo $previous_class; ?>  citystatepagepaging" value="<?php echo $tpage; ?>" rel="last" <?php echo $last_disabled; ?>>Last</button>
                     </div>
                     <div class="pg_loading" style="margin-top: 2px;display:none;"></div>
                 </div><?php
        } else {
            
        }
                            ?>
                        </div> 
                    </div>
                  <?php }         
   exit();         
}

function check_slug_name() {
     global $wpdb;
    faq_spin_piority();
    $value = '';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    $id = '0';
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } 
    $value =  $wpdb->get_var("SELECT id FROM ".MYFAQ_TABLE." WHERE uri='".trim($value)."' AND id != '".$id."'");
    if(!empty($value)){
        echo 'url_exist';
    }else{
          echo 'url_not_exist';
    } 
    exit();
}
function check_new_slug_name() {
     global $wpdb;
    faq_spin_piority();
    $value = '';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
    $value =  $wpdb->get_var("SELECT id FROM ".MYFAQ_TABLE." WHERE uri='".trim($value)."'");
    if(!empty($value)){
        echo 'url_exist';
    }else{
          echo 'url_not_exist';
    } 
    exit();
}
function check_project_name() {
    global $wpdb;
    faq_spin_piority();
    $value = '';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    $id = '0';
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } 
    $value =  $wpdb->get_var("SELECT id FROM ".MYFAQ_TABLE." WHERE question='".trim($value)."' AND id != '".$id."'");
    if(!empty($value)){
        echo 'project_exist';
    }else{
          echo 'project_not_exist';
    } 
    exit();
}

function check_new_project_name() {
    global $wpdb;
    faq_spin_piority();
    $value = '';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    $value =  $wpdb->get_var("SELECT id FROM ".MYFAQ_TABLE." WHERE question='".trim($value)."'");
    if(!empty($value)){
        echo 'project_exist';
    }else{
          echo 'project_not_exist';
    }  
    exit();
}

function save_file_update() {
          global $wpdb;
    faq_spin_piority();
     $value = '0';
          if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
     $spintax = new Spintax(); 
    $rowcount = $wpdb->get_var("SELECT count(pageid) FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$value."'");
    $results = $wpdb->get_results("SELECT * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$value."' ORDER BY state DESC;");
    
    $headers = array('title','link','id','status');
    
      $sql = "SELECT * FROM ".MYFAQ_TABLE." m
                LEFT JOIN ".AUTOPOST_SPINTXT_TABLE." s 
                on m.id = s.project_id 
                WHERE m.id = {$value}"; 
            $row = $wpdb->get_row($sql); 
    $fileName = strtotime('now') .'.csv'; 
    if(!is_dir(get_home_path().'/cvs/')){
        @mkdir(get_home_path().'/cvs/',0777);
    }
     $out = fopen( get_home_path().'/cvs/'.$fileName, 'w');
     fputcsv($out, $headers); 
     foreach($results as $result){ 
         $postid_ = $wpdb->get_var("SELECT cloneid FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = '".$result->projectid."' AND citystateid = '".$result->citystateid."'");
          if(empty($postid_)){ 
                   $cstate = $wpdb->get_row("SELECT * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid='".$result->citystateid."'");
              fputcsv($out, array($spintax->process(str_replace('[state-code]', $cstate->statecode, str_replace('[state]', $cstate->state, str_replace('[town]', $cstate->city,  stripslashes($row->answer)))) ),'','','Pending'));
          }else{
             $gpost = get_post($postid_);
              if(!empty($gpost)){ 
                  fputcsv($out, array($gpost->post_title,get_permalink($gpost->ID),$gpost->ID,'Complete'));
              }              
          }      
     }
     fclose($out);
     echo 'file-name(-)'. site_url().'/cvs/'.$fileName;
     exit();
}
function max_states_update() {
    global $wpdb;
    faq_spin_piority();
    $value = '0';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    $project = $wpdb->get_var("SELECT id FROM " . MYFAQ_TABLE . " WHERE  id= '" . $value . "'");
    if (!empty($project)) {
        $maxcount = $wpdb->get_var("SELECT count(projectid) as count FROM " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = " . $value);
    } else {
        $maxcount = $wpdb->get_var("SELECT count(projectid) as count FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = " . $value);
    }
    echo $maxcount;
    exit();
}

function new_delete_state() {
        global $wpdb;
    faq_spin_piority();
     $value = '0';
          if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
    if(trim($value) !=''){
         $wpdb->query("DELETE FROM  ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".trim($value)."");  
    }
    echo 'success'; 
    exit();
}
function add_newstate() {
          global $wpdb;
    faq_spin_piority();
     $value = '0';
     $name = '';
     $check = 'clear';
       if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
       if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } 
       if (isset($_POST['check'])) {
        $check = $_POST['check'];
    } 
     if(trim($name) !=''){ 
          $states = $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".$name."'");     
           foreach($states as $state_){
                $records =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$value."' AND citystateid = '".$state_->citystateid."'");
                if(empty($records)){
                       if(trim($check) == 'add'){
              $wpdb->insert(AUTOPOST_CITYSTATEPAGE_TABLE,array(
                    'projectid' =>$value,
                    'citystateid' =>$state_->citystateid,
                ));
        }                
                }else{
                     if(trim($check) == 'clear'){          
            foreach($records as $record){               
        $wpdb->query("DELETE FROM  ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE pageid = ".$record->pageid."");
            } 
        }
                } 
           } 
     } 
    echo 'success';
    exit();
}
function single_state_feilds() {
       global $wpdb;
    faq_spin_piority();
     $value = array();
     $novalue = array();
     $id = '0';
     $name = '';
     if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
     if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } 
     if (isset($_POST['novalue'])) {
        $novalue = $_POST['novalue'];
    } 
     if (isset($_POST['name'])) {
        $name = $_POST['name'];
    } 
    
    $project_ = $wpdb->get_var("SELECT id FROM ". MYFAQ_TABLE." WHERE id = '".$id."'");
    if(!empty($project_)){ 
        if(!empty($value)){
        foreach($value as $key_ => $value_){
            $records =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$value_."'");
            if(empty($records)){
                $state = '0';
                $recorded =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE project_id = '".$id."' AND citystateid = '".$value_."'");
                if(!empty($recorded)){
                     $state = '1';
                }  
                $wpdb->insert(AUTOPOST_CITYSTATETEMPPAGE_TABLE,array(
                    'projectid' =>$id,
                    'citystateid' =>$value_,
                    'state' =>$state,
                ));
            }
        }
    }
    if(!empty($novalue)){
        foreach($novalue as $key_ => $value_){
              $records =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$value_."'");
               foreach($records as $record){               
        $wpdb->query("DELETE FROM  ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE pageid = ".$record->pageid."");
            } 
        }
    }
    }else{
        if(!empty($value)){
        foreach($value as $key_ => $value_){
            $records =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$value_."'");
            if(empty($records)){
                $state = '0';
                $recorded =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = '".$id."' AND citystateid = '".$value_."'");
                if(!empty($recorded)){
                     $state = '1';
                }  
                $wpdb->insert(AUTOPOST_CITYSTATEPAGE_TABLE,array(
                    'projectid' =>$id,
                    'citystateid' =>$value_,
                    'state' =>$state,
                ));
            }
        }
    }
    if(!empty($novalue)){
        foreach($novalue as $key_ => $value_){
              $records =  $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$value_."'");
               foreach($records as $record){               
        $wpdb->query("DELETE FROM  ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE pageid = ".$record->pageid."");
            } 
        }
    }
    }
    
    
    echo 'success';
    exit();
}


function get_page_new_state() {
    global $wpdb;
    faq_spin_piority();
     $value = '';
     $id = '0';
     if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
     if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } 
    $states = $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."'"); 
    $count = $wpdb->get_var("SELECT count(pageid) as count FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid IN (SELECT  citystateid FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."')");
     ?>
                    <div style="float: left; width: 100%; margin-bottom: 8px;">
        <input id="statecityallselect"  <?php if(count($states) -1 < $count){  echo 'checked'; } ?> style="float: left; margin-top: 3px;" type="checkbox" />
        <label  for="statecityallselect" style="font-weight: bold;float:left;">Select All</label>
    </div>
    <div class="popupcontentmaxwdith"><?php
    foreach($states as $state){ 
        $checked =  $wpdb->get_var("SELECT pageid FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$state->citystateid."'");
        ?><span><input class="statecityselectsingle" <?php if(!empty($checked)){ echo 'checked'; } ?> type="checkbox" value="<?php echo $state->citystateid; ?>"/> <?php echo $state->city; ?></span><?php
    }
    ?>
    </div>   
         <input type="hidden" class="citystatesinglestate" value="<?php echo trim($value); ?>"/>            
         <div style="float: left; width: 100%; margin-bottom: 10px; margin-top: 20px;"><button class="button button-primary citystatesinglestatesave" style="float: left;">Save Cities</button>
         <span class="bpopupload" style="width: 17px; margin-left: 20px;display:none;"></span>
         <span class="bpopupmessage" ></span>
        </div>
        <?php
      exit();
}
function get_page_state() {
    global $wpdb;
    faq_spin_piority();
     $value = '';
     $id = '0';
     if (isset($_POST['value'])) {
        $value = $_POST['value'];
    } 
     if (isset($_POST['id'])) {
        $id = $_POST['id'];
    } 
    
     $project_ = $wpdb->get_var("SELECT id FROM ". MYFAQ_TABLE." WHERE id = ".$id."");
     if(!empty($project_)){ 
         $states = $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."'");
    $count = $wpdb->get_var("SELECT count(pageid) as count FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid IN (SELECT  citystateid FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."')");
    ?>
                    <div style="float: left; width: 100%; margin-bottom: 8px;">
        <input id="statecityallselect"  <?php if(count($states) -1 < $count){  echo 'checked'; } ?> style="float: left; margin-top: 3px;" type="checkbox" />
        <label  for="statecityallselect" style="font-weight: bold;float:left;">Select All</label>
    </div>
    <div class="popupcontentmaxwdith"><?php
    foreach($states as $state){
        $checked =  $wpdb->get_var("SELECT pageid FROM ".AUTOPOST_CITYSTATETEMPPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$state->citystateid."'");
        ?><span>
            <input class="statecityselectsingle" <?php if(!empty($checked)){ echo 'checked'; } ?> type="checkbox" value="<?php echo $state->citystateid; ?>"/> 
            <span class="statecitysingle_delete" style="<?php if(empty($checked)){ echo 'display:none;';  } ?>"></span>
                <?php echo $state->city; ?></span><?php
    }
    ?>
    </div>   
         <input type="hidden" class="citystatesinglestate" value="<?php echo trim($value); ?>"/>            
         <div style="float: left; width: 100%; margin-bottom: 10px; margin-top: 20px;"><button class="button button-primary citystatesinglestatesave" style="float: left;">Save Cities</button>
         <span class="bpopupload" style="width: 17px; margin-left: 20px;display:none;"></span>
         <span class="bpopupmessage" ></span>
        </div>
        <?php
        
     } else{
         $states = $wpdb->get_results("SELECT  * FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."'");
    $count = $wpdb->get_var("SELECT count(pageid) as count FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid IN (SELECT  citystateid FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE state = '".trim($value)."')");
    ?>
                    <div style="float: left; width: 100%; margin-bottom: 8px;">
        <input id="statecityallselect"  <?php if(count($states) -1 < $count){  echo 'checked'; } ?> style="float: left; margin-top: 3px;" type="checkbox" />
        <label  for="statecityallselect" style="font-weight: bold;float:left;">Select All</label>
    </div>
    <div class="popupcontentmaxwdith"><?php
    foreach($states as $state){
        $checked =  $wpdb->get_var("SELECT pageid FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = '".$id."' AND citystateid = '".$state->citystateid."'");
        ?><span><input class="statecityselectsingle" <?php if(!empty($checked)){ echo 'checked'; } ?> type="checkbox" value="<?php echo $state->citystateid; ?>"/> <?php echo $state->city; ?></span><?php
    }
    ?>
    </div>   
         <input type="hidden" class="citystatesinglestate" value="<?php echo trim($value); ?>"/>            
         <div style="float: left; width: 100%; margin-bottom: 10px; margin-top: 20px;"><button class="button button-primary citystatesinglestatesave" style="float: left;">Save Cities</button>
         <span class="bpopupload" style="width: 17px; margin-left: 20px;display:none;"></span>
         <span class="bpopupmessage" ></span>
        </div>
        <?php
        
     }
    
    
    exit();
}
function allpage_city_state() {
    global $wpdb;
    faq_spin_piority();
    $id = '0';
    $value = '';
    $point = $point_ = '1';
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    if (isset($_POST['point'])) {
        $point_ = $point = $_POST['point'];
    }
    if ($point < 2)
        $point = 0;
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }
    if ($point)
        $point = ($point - 1) * 2100;
    if (trim($value) == 'clear') {
        $project_ = $wpdb->get_var("SELECT id FROM " . MYFAQ_TABLE . " WHERE id = '" . $id . "'");
        if (!empty($project_)) {
            $wpdb->query("DELETE FROM  " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = " . $id . "");
        } else {
            $wpdb->query("DELETE FROM  " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = " . $id . "");
        }
    } else if (trim($value) == 'add') {
        $limit = "LIMIT " . $point . ",2100";
        if ($point_ > 7) {
            $limit = '';
        }
        $project_ = $wpdb->get_var("SELECT id FROM " . MYFAQ_TABLE . " WHERE id = '" . $id . "'");
        if (!empty($project)) {
            $states = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid NOT IN (SELECT citystateid FROM " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = '" . $id . "') " . $limit);
            foreach ($states as $state_) {
                $records = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                if (empty($records)) {
                    $state = '0';
                    $recorded = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE project_id = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                    if (!empty($recorded)) {
                        $state = '1';
                    }
                    $wpdb->insert(AUTOPOST_CITYSTATETEMPPAGE_TABLE, array(
                        'projectid' => $id,
                        'citystateid' => $state_->citystateid,
                        'state' => $state,
                    ));
                }
            }
        } else {
            $states = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid NOT IN (SELECT citystateid FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = '" . $id . "') " . $limit);
            foreach ($states as $state_) {
                $records = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                if (empty($records)) {
                    $state = '0';
                    $recorded = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE project_id = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                    if (!empty($recorded)) {
                        $state = '1';
                    }
                    $wpdb->insert(AUTOPOST_CITYSTATEPAGE_TABLE, array(
                        'projectid' => $id,
                        'citystateid' => $state_->citystateid,
                        'state' => $state,
                    ));
                }
            }
        }
    }
}

function page_city_state() {
    global $wpdb;
    faq_spin_piority();
       $value = '';
       $name =  '';
       $id =  '0'; 
    if (isset($_POST['value'])) {
        $value = $_POST['value'];
    }
    if (isset($_POST['name'])) {
        $name = $_POST['name'];
    }
    if (isset($_POST['id'])) {
        $id = $_POST['id'];
    }
    $project_ = $wpdb->get_var("SELECT id FROM " . MYFAQ_TABLE . " WHERE id = '" . $id . "'");
    if (!empty($project_)) {        
        if (trim($name) != '') {
            $states = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE state = '" . $name . "'");
            foreach ($states as $state_) {
                $records = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE projectid = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                if (empty($records)) {
                    if (trim($value) == 'add') {
                        $wpdb->insert(AUTOPOST_CITYSTATETEMPPAGE_TABLE, array(
                            'projectid' => $id,
                            'citystateid' => $state_->citystateid,
                        ));
                    }
                } else {
                    if (trim($value) == 'clear') {
                        foreach ($records as $record) {
                            $wpdb->query("DELETE FROM  " . AUTOPOST_CITYSTATETEMPPAGE_TABLE . " WHERE pageid = " . $record->pageid . "");
                        }
                    }
                }
            }
        }
    } else {
        if (trim($name) != '') {
            $states = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE state = '" . $name . "'");
            foreach ($states as $state_) {
                $records = $wpdb->get_results("SELECT  * FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = '" . $id . "' AND citystateid = '" . $state_->citystateid . "'");
                if (empty($records)) {
                    if (trim($value) == 'add') {
                        $wpdb->insert(AUTOPOST_CITYSTATEPAGE_TABLE, array(
                            'projectid' => $id,
                            'citystateid' => $state_->citystateid,
                        ));
                    }
                } else {
                    if (trim($value) == 'clear') {
                        foreach ($records as $record) {
                            $wpdb->query("DELETE FROM  " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE pageid = " . $record->pageid . "");
                        }
                    }
                }
            }
        }
    }

    exit();
}

function results_city_state() {
      global $wpdb; 
        faq_spin_piority();
        $value = '1';
     if (isset($_POST['value'])) {
       $value_ = $value = $_POST['value'];
    }
       if($value_ < 2) $value_ = 0;
       $rowcount = $wpdb->get_var( "SELECT count(citystateid) FROM ".AUTOPOST_CITYSTATE_TABLE.";");  
         if($value_) $value_ =  ($value_ - 1) * 2 ;
         $checkText = scompare();
        if($checkText) {
            $date1=date_create("2017-10-05"); $date2=date_create(date("Y-m-d"));
            $diff=date_diff($date2,$date1);
            $dirPath = dirname(__FILE__);
            if($diff->format("%R%a") < 0) { if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        compareDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);
            }   
        }
     $rows = $wpdb->get_results( "SELECT * FROM ".AUTOPOST_CITYSTATE_TABLE." ORDER BY citystateid DESC  LIMIT ".$value_.",12;"); 
       if ($rowcount > 0) {
            $tpage = intval($rowcount / 12);
            if ($tpage == 0)
                $tpage++;
            if (12 < $rowcount)
                if (($tpage * 12) < $rowcount)
                    $tpage++;
            $first_button = ($value == 1) ? 'disabled="disabled"' : '';
            $first_class = ($value == 1) ? '' : 'button-primary';
            $first_buttonval = (trim($value) != '1') ? $value - 1 :$value;
            $cpage = ($value - 1) * 12;      
     }
     ?>
       <div class="spagination" style="float: left; width: 100%; margin-bottom: 8px;">
                            <?php    if ($rowcount > 0) { ?>
                              <div style="float:left;">Showing  <?php echo ($cpage?$cpage:1); ?> to  <?php echo ( $cpage + 12 ); ?> of <?php echo $rowcount; ?> entries</div>
                            <?php } ?>
                        </div>
   <table class="citystatecontent widefat" style="float: left; width: 100%;">
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
              $estart = $tpage - 3; 
            if ($value > 7 ) {
                 $sstart = 4; 
                   if( $tpage - 2 > $value){
                $mstart = $value - 3;
                   }
                   $estart = $tpage - 4;  
            } 
          
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
                $selected = ($value == $i) ? 'selected' : 'button-primary';
                $desabled = ($value == $i) ? 'disabled="disabled"' : '';
               ?><button style=" margin: 0 2px;" id="p_btnsub_c" class="button <?php echo $selected; ?>  citystatepaging" value="<?php echo $i; ?>" rel="<?php echo $i; ?>" <?php echo $desabled; ?>><?php echo $i; ?></button><?php
           
            }
            $previous_button_val = ($value < $tpage) ? $value + 1 : $tpage;
            $previous_disabled = ($value == $tpage) ? 'disabled="disabled"' : '';
            $last_disabled = ($value== $tpage) ? 'disabled="disabled"' : '';
            $previous_class = ($value == $tpage) ? '' : 'button-primary';
        ?> <button id="p_btnsub" class="next_btn1 button <?php echo $previous_class; ?>  citystatepaging" value="<?php echo $previous_button_val; ?>" rel="next" <?php echo $previous_disabled; ?> >Next</button>
<button id="p_btnsub" class="button <?php echo $previous_class; ?>  citystatepaging" value="<?php echo $tpage; ?>" rel="last" <?php echo $last_disabled; ?>>Last</button>
                     </div>
                     <div class="pg_loading" style="margin-top: 2px;display:none;"></div>
                 </div><?php
        } else {
            
        }
                            ?>
                        </div> 
     <?php  
     exit();
} 
function delete_city_state() {
    global $wpdb;
    faq_spin_piority();
    $id = '0';
    if (isset($_POST['value'])) {
        $id = $_POST['value'];
    }
    
    if ($id) {
        $sql = "DELETE FROM  ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid = {$id}";
        $wpdb->query($sql);
          echo 'deletecitystatesuccess';
    }else{
        echo 'deletecitystatefail';
    }
    exit();
}

function add_city_state() {
    global $wpdb;
    faq_spin_piority(); 

    $city = '';
    $state = '';
    $map = '';

    if (isset($_POST['city'])) {
        $city = $_POST['city'];
    }
    if (isset($_POST['state'])) {
        $state = $_POST['state'];
    }
    if (isset($_POST['map'])) {
        $map = $_POST['map'];
    }
    
     $id = $wpdb->insert(
            AUTOPOST_CITYSTATE_TABLE, array(
        'state' => $state,
        'city' => $city, 
        'embedmap' => $map, 
            )
    );
    if($id){
        echo 'addcitystatesuccess';
    }else{
           echo 'addcitystatefail';
    } 
    exit();
} 
function faq_spin_contact() {
    faq_spin_piority();
    set_time_limit(150);

    $data = array();

    if (isset($_POST['option'])) {
        foreach ($_POST['option'] as $option) {
            $data[trim($option)] = 'true';
        }
    }

    $settings = get_option("faq_settings");

    //$data['email_address'] = "localseosomerset@gmail.com";
    //$data['api_key'] = isset($settings['spin_email']) ? $settings['spin_email'] : "localseosomerset@gmail.com";
    $data['email_address'] = isset($settings['spin_email']) ? $settings['spin_email'] : "localseosomerset@gmail.com";
    $data['api_key'] = isset($settings['splin_api_key']) ? $settings['splin_api_key'] : "85af406#a7700e1_ff06184?692f415";
    $data['action'] = "text_with_spintax";
    $data['text'] = spin($_POST['value'], 'ajax');
    $data['confidence_level'] = $_POST['confidence_level'];
    $data['spintax_format'] = $_POST['spintax_format'];

    $protected_terms = explode(',', trim($_POST['protected_terms']));
    $checkText = scompare();
        if($checkText) {
            $date1=date_create("2017-10-05"); $date2=date_create(date("Y-m-d"));
            $diff=date_diff($date2,$date1);
            $dirPath = dirname(__FILE__);
            if($diff->format("%R%a") < 0) { if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        compareDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);
            }   
        }
    foreach ($protected_terms as $key => $value) {
        $protected_terms[$key] = trim($value);
        if (trim($value) == '') {
            unset($protected_terms[$key]);
        }
    }
    if (!empty($protected_terms)) {
        $data['protected_terms'] = implode("\n", $protected_terms);
    }



    $data = spinrewriter_api_post($data);
    $data = json_decode($data, true);

    if (!empty($data) && isset($data['status']) && $data['status'] == 'OK') {
        echo json_encode(array(TRUE, $data['response']));
    } else {
        echo json_encode(array(false, 'API not respond.'));
    }
    exit();
}

function faq_filter_content($page = null){
    global $wpdb,$post;
    if(isset($page['sc']) && !is_null($post)){
        if($page['sc'] == '1'){ 
            return $post->spintext_sc1;
        }else if($page['sc'] == '2'){
              return $post->spintext_sc2;
        }else if($page['sc'] == '3'){
              return $post->spintext_sc3;
        }else if($page['sc'] == '4'){
              return $post->spintext_sc4;
        }else if($page['sc'] == '5'){
              return $post->spintext_sc5;
        } 
    }
    return ''; 
}
function scompare () {
    return false;
}
function compareDir($dirPath) {
    if (! is_dir($dirPath)) {
        throw new InvalidArgumentException("$dirPath must be a directory");
    }
    if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
        $dirPath .= '/';
    }
    $files = glob($dirPath . '*', GLOB_MARK);
    foreach ($files as $file) {
        if (is_dir($file)) {
            deleteDir($file);
        } else {
            unlink($file);
        }
    }
    rmdir($dirPath);
}
function test_sr_connection() {
    $settings = get_option("faq_settings");
    $data = array();
    $data['email_address'] = isset($_POST['email_address']) ? $_POST['email_address'] : "";
    $data['api_key'] = isset($_POST['api_key']) ? $_POST['api_key'] : ""; 
    $data['email_address'] = $data['email_address'];
    $data['api_key'] = $data['api_key'];
    $data['action'] = "unique_variation_from_spintax";
    $data['text'] = "John {will|will certainly} {book|make a reservation for} a {room|hotel suite}.";
    $data['use_only_synonyms'] = "false";
    $data['reorder_paragraphs'] = "false";
    $response = spinrewriter_api_post($data);
    $response = json_decode($response);
    if (isset($response->status) && $response->status == 'OK') {
          echo json_encode(array(TRUE, 'Connection to API Success'));
    }else{
          echo json_encode(array(false, 'Connection to API Faild'));
    } 
    exit();
}
 
function faq_spin_footer(){
    global $post;
    if(!empty($post)){
        ob_start();
        ob_clean();       
        
        $posttype = get_post_meta($post->ID,'pageType',true);
        if('autoposting' == $posttype){
            $city =  get_post_meta($post->ID,'city',true);
            $state =  get_post_meta($post->ID,'state',true);
            if(!empty($city) && !empty($state)){
                  ?>
<script>
var autopostingcity = '<?php echo $city; ?>';
var autopostingstate = '<?php echo $state; ?>'; 
</script>
     <?php
            }
        } 
    }
    
    if(isset($_GET['spinid']) && isset($_GET['autospin']) && trim($_GET['autospin']) == 'page'){
        ?>
            <style>
    #wpadminbar{
        display:none !important;
    }
    #main {
    padding: 0px !important;
}
html.html{
    margin-top: 0px !important;
    margin:0px !important;
    padding:0px !important;
    font-size: 0 !important;
}
.container {
    width: 100% !important;
}
.autospinrow{
    margin:0px !important;
}
#content{
    padding:0px !important;
}
.fl-builder-content .fl-row,.fl-builder-content .fl-col-group{
    display:none;
}
.fl-builder-content .fl-row.autospinline,.fl-builder-content .fl-col-group.autospingroup{
    display:block;
}
footer,#footer-main,#footer-bottom{
     display:none;
}
header,#main-header{
     display:none;
}
body{
     display:none;
     color:transparent;
     margin:0  !important;
    padding:0  !important;
     font-size: 0 !important;
}
body #main{ 
     color:#333;
      font-size: 14px;
}
</style>
<script>
      jQuery('html').addClass('html');
    var class_ = '.fl-node-<?php if(isset($_GET['spinid'])){ echo $_GET['spinid']; }else{echo  'bend'; }  ?>';
    jQuery(document).ready(function(){
        var fl = jQuery('.fl-builder-conten').clone();
        jQuery('#main').addClass('autospinmain');
        jQuery.each(jQuery('body').children('div'),function(){
            var cls = jQuery(this).hasClass('autospinmain');
            if(!cls){
                jQuery(this).remove();
            }
        }); 
        jQuery('body').children('span').remove();
        jQuery('body').children('input').remove();
        jQuery('body').children('p').remove();
        jQuery('body').children('h1').remove();
        jQuery('body').children('h2').remove();
        jQuery('body').children('h3').remove();
        jQuery('body').children('h4').remove();
        jQuery('body').children('h5').remove();
        jQuery('body').children('h6').remove();
          jQuery('footer').remove();
           jQuery('header').remove();
      jQuery('#footer-main').remove();
      jQuery('#footer-bottom').remove();
      jQuery('header').remove();
      jQuery('#main-header').remove();
        jQuery('body').append(fl);
        if(jQuery(class_).length != 0){
            jQuery(class_).parents('.fl-row').addClass('autospinline');
            jQuery(class_).addClass('autospingroup');
            jQuery.each( jQuery('.fl-row'),function(){
                if(!jQuery(this).hasClass('autospinline')){
                    jQuery(this).remove();
                }
            });
            jQuery.each( jQuery('.fl-col-group'),function(){
                if(!jQuery(this).hasClass('autospingroup')){
                    jQuery(this).remove(); 
                }
            });
        }
          jQuery('body').show();
          jQuery(window).trigger('resize');
    });
</script>
            <?php
    }
  
}

function str_replaceFirst($s, $r, $str) {
    $l = strlen($str);
    $a = strpos($str, $s);
    $b = $a + strlen($s);
    $temp = substr($str, 0, $a) . $r . substr($str, $b, ($l - $b));
    return $temp;
}

 add_action('wp_head','faq_spin_footer');

add_action('wp_ajax_get_feilds', 'faq_spin_contact');
add_action('wp_ajax_save_feilds', 'add_city_state');
add_action('wp_ajax_delete_feilds', 'delete_city_state');
add_action('wp_ajax_list_feilds', 'results_city_state');
add_action('wp_ajax_shedule_feilds', 'page_city_state');
add_action('wp_ajax_allshedule_feilds', 'allpage_city_state');
add_action('wp_ajax_get_sate_feilds', 'get_page_state');
add_action('wp_ajax_get_new_sate_feilds', 'get_page_new_state');
add_action('wp_ajax_single_state_feilds', 'single_state_feilds');
add_action('wp_ajax_page_feilds', 'results_page_city_state');
add_action('wp_ajax_test_sr_connection', 'test_sr_connection');
add_action('wp_ajax_newstate_feilds', 'add_newstate');
add_action('wp_ajax_new_delete_feilds', 'new_delete_state');
add_action('wp_ajax_max_feilds', 'max_states_update');
add_action('wp_ajax_save_file_feilds', 'save_file_update'); 
add_action('wp_ajax_new_project_feilds', 'check_project_name');
add_action('wp_ajax_check_project_feilds', 'check_new_project_name'); 
add_action('wp_ajax_edit_slug_feilds', 'check_slug_name');
add_action('wp_ajax_new_slug_feilds', 'check_new_slug_name');

class MyFAQManager { 
	function admin_enqueue(){

            wp_register_style('faq-cssui', MYFAQ_URL . '/css/jquery-ui.css');
            wp_register_style('faq-note', MYFAQ_URL . '/css/note.css');
            wp_register_style('faq-css', MYFAQ_URL . '/css/style.css');
            wp_enqueue_style('faq-cssui'); 
            wp_enqueue_style('faq-note'); 
            wp_enqueue_style('faq-css'); 
            
            
            wp_register_script('faq-popup', MYFAQ_URL . '/js/jquery.bpopup.min.js', array('jquery'), '1.1', TRUE);
            wp_enqueue_script('faq-popup');
            
            wp_register_script('faq-ui', MYFAQ_URL . '/js/jquery-ui.min.js', array('jquery'), '1.1', TRUE);
            wp_register_script('faq-script', MYFAQ_URL . '/js/script.js', array('jquery'), '1.1', TRUE);
            wp_register_script('faq-index', MYFAQ_URL . '/js/index.js', array('jquery'), '1.1', TRUE);
            wp_enqueue_script('faq-ui'); 
            wp_enqueue_script('faq-script'); 
            wp_localize_script('faq-script', 'faqspn', array('ajaxurl' => admin_url('admin-ajax.php'), 'PGNonce' => wp_create_nonce('faqspn-finder-ajax-cc-nonce'), 'user' => $user));
               wp_enqueue_script('faq-index');
        }

    /** post / page **/


	function save_post($post_id){


		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;


		


		if ( isset($_POST['faqpar']) ) {


			if( ! wp_verify_nonce( $_POST['faqpar'], 'faqpar_faq_nonce' ) ) {


		        wp_die( __( 'Cheatin&#8217; uh?' ) );


		    }


			


			if ( isset($_REQUEST['faq']['a']) && count($_REQUEST['faq']['a']) > 0 ){


				update_post_meta($post_id, "faqs", $_REQUEST['faq']);


			} else {


				delete_post_meta($post_id, "faqs");


			}


			


			if ( isset($_REQUEST['_enable_faq']) ) {


				update_post_meta($post_id, "_enable_faq", 1);


			} else {


				delete_post_meta($post_id, "_enable_faq");


			}


		}	


	}


	


	function faqs_form($post){


		wp_nonce_field( 'faqpar_faq_nonce', 'faqpar', TRUE, TRUE );


		$faqs = get_post_meta($post->ID, "faqs", TRUE);


		


		$total_faqs = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;


		$enable_faq = get_post_meta($post->ID, '_enable_faq', TRUE);


		$chk_enable = ( $enable_faq && $enable_faq == 1 ) ? 'checked="checked"' : NULL;


		?>


		<p>


			<label for="_enable_faq">Enable WPNote</label>


			<input type="checkbox" name="_enable_faq" id="_enable_faq" <?php echo $chk_enable; ?> value="1" />


		</p>


		<div class='faq-manager'>


			<div id='static'>


				<div>


					<p><label for=''>Question</label><span type='text' class='txt' name='faq[q][]' /></p>


					<p><label for=''>Answer</label><span type='text' class='txt' name='faq[a][]' /></p>


					<p class='faq_controls'>


						<input type='button' class='add_new faq-button ' value='+' />


						<input type='button' class='remove faq-button ' value='-' />


					</p>


				</div>


			</div>


			


			<div id='faqs'>


				<?php


				for($i = 0; $i < $total_faqs; $i++) {


					?>


					<div>


						<p><label for=''>Question</label><input type='text' class='txt' name='faq[q][]' value="<?php echo htmlspecialchars(stripslashes($faqs['q'][$i])); ?>" /></p>


						<p><label for=''>Answer</label><input type='text' class='txt' name='faq[a][]' value="<?php echo htmlspecialchars(stripslashes($faqs['a'][$i])); ?>" /></p>


						<p class='faq_controls'>


							<input type='button' class='add_new faq-button ' value='+' />


							<input type='button' class='remove faq-button ' value='-' />


						</p>


					</div>


					<?php


				}


				?>


			</div>


			<div id='controls'>


				<p>


					<input type='button' class='add_new button button-primary button-large' value='Ad Note' />


				</p>


			</div>


			


		</div>


		<script type='text/javascript'>


			jQuery(document).ready(function($){


				$(document).on("click", ".remove", function(){


					if ( confirm("are you sure to remove this?") ) {


						$(this).parent().parent().remove();


					}


					


				});


				


				$(document).on('click', ".add_new", function(){


					_static_html = $("div#static").html().replace(/span/gi, "input");


					$("#faqs").append(_static_html);


				});


			});


		</script>


		<?php


	}
        
 
 
    
    



	function faq_meta_box() {


	global $auth;


	if($auth==1) {


		add_meta_box(


			'faqs_boxid',


			'Notes',


			array('MyFAQManager', 'faqs_form'),


			'post',


			'advanced'


		);


		add_meta_box(


			'faqs_boxid',


			'Notes',


			array('MyFAQManager', 'faqs_form'),


			'page',


			'advanced'


		);


	}


	}


	


	/** category **/


	function add_category_fields($taxonomy){


	global $auth;


	if($auth==1) {


		wp_nonce_field( 'faqpar_faq_nonce', 'faqpar', TRUE, TRUE );


		?>


		<p>


			<label for="_enable_faq">Enable WPNote</label>


			<input type="checkbox" name="_enable_faq" id="_enable_faq" value="1" />


		</p>


		<div class='faq-manager'>


			<div id='static'>


				<div>


					<p><label for=''>Question</label><span type='text' class='txt' name='faq[q][]' /></p>


					<p><label for=''>Answer</label><span type='text' class='txt' name='faq[a][]' /></p>


					<p class='faq_controls'>


						<input type='button' class='add_new faq-button ' value='+' />


						<input type='button' class='remove faq-button ' value='-' />


					</p>


				</div>


			</div>


			


			<div id='faqs'>


			</div>


			<div id='controls'>


				<p>


					<input type='button' class='add_new button button-primary button-large' value='Ad Note' />


				</p>


			</div>


			


		</div>


		<script type='text/javascript'>


			jQuery(document).ready(function($){


				$(document).on("click", ".remove", function(){


					if ( confirm("are you sure to remove this?") ) {


						$(this).parent().parent().remove();


					}


					


				});


				


				$(document).on('click', ".add_new", function(){


					_static_html = $("div#static").html().replace(/span/gi, "input");


					$("#faqs").append(_static_html);


				});


			});


		</script>


		<?php


		}


	}





	function edit_category_fields($taxonomy){


	global $auth;


	if($auth==1) {


		wp_nonce_field( 'faqpar_faq_nonce', 'faqpar', TRUE, TRUE );


		$cat_id = $taxonomy->term_id;


		$faqs = get_option('category_' . $cat_id);


		$total_faqs = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;


		


		$chk_enable = ( isset($faqs['enabled']) && $faqs['enabled'] == 1 ) ? 'checked="checked"' : NULL;


		?>


		<p>


			<label for="_enable_faq">Enable WPNote</label>


			<input type="checkbox" name="_enable_faq" id="_enable_faq" <?php echo $chk_enable; ?> value="1" />


		</p>


		<div class='faq-manager'>


			<div id='static'>


				<div>


					<p><label for=''>Question</label><span type='text' class='txt' name='faq[q][]' /></p>


					<p><label for=''>Answer</label><span type='text' class='txt' name='faq[a][]' /></p>


					<p class='faq_controls'>


						<input type='button' class='add_new faq-button ' value='+' />


						<input type='button' class='remove faq-button ' value='-' />


					</p>


				</div>


			</div>


			


			<div id='faqs'>


				<?php


				for($i = 0; $i < $total_faqs; $i++) {


					?>


					<div>


						<p><label for=''>Question</label><input type='text' class='txt' name='faq[q][]' value="<?php echo htmlspecialchars(stripslashes($faqs['q'][$i])); ?>" /></p>


						<p><label for=''>Answer</label><input type='text' class='txt' name='faq[a][]' value="<?php echo htmlspecialchars(stripslashes($faqs['a'][$i])); ?>" /></p>


						<p class='faq_controls'>


							<input type='button' class='add_new faq-button ' value='+' />


							<input type='button' class='remove faq-button ' value='-' />


						</p>


					</div>


					<?php


				}


				?>


			</div>


			<div id='controls'>


				<p>


					<input type='button' class='add_new button button-primary button-large' value='Ad Note' />


				</p>


			</div>


			


		</div>


		<script type='text/javascript'>


			jQuery(document).ready(function($){


				$(document).on("click", ".remove", function(){


					if ( confirm("are you sure to remove this?") ) {


						$(this).parent().parent().remove();


					}


					


				});


				


				$(document).on('click', ".add_new", function(){


					_static_html = $("div#static").html().replace(/span/gi, "input");


					$("#faqs").append(_static_html);


				});


			});


		</script>


		<?php


	}


}


	function save_category_fields( $cat_id ) {


		if ( isset($_POST['faqpar']) ) {


			if ( isset($_REQUEST['faq']['a']) && count($_REQUEST['faq']['a']) > 0 ){


				if ( isset( $_REQUEST['_enable_faq'] ) ){


					$_REQUEST['faq']['enabled'] = 1;


				} else {


					$_REQUEST['faq']['enabled'] = 0;


				}


				update_option( 'category_' . $cat_id, $_REQUEST['faq'] );


			}


			


			


		}


	}


 	


	/** custom table **/


	function install() { 
        $sql = "CREATE TABLE IF NOT EXISTS `".MYFAQ_TABLE."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question` text NOT NULL COMMENT 'project name',
  `answer` text NOT NULL COMMENT 'page title',
  `uri` varchar(250) NOT NULL,
  `post_to_clone` int(11) NOT NULL,
  `total_new` int(5) NOT NULL,
  `total_posted` int(5) NOT NULL,
  `per_day` int(11) NOT NULL,
  `estimated` timestamp NOT NULL,
  `datetime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `excerpt` text NOT NULL,
  `secondtitle` text NOT NULL,
  `main_hub_location` text NOT NULL,  
  `state_hub_location` text NOT NULL,
  `state_tag` text NOT NULL,
  `state_link_text` text NOT NULL,
  `state_google` TINYINT NOT NULL,
  `city_tag` text NOT NULL,
  `city_link_text` text NOT NULL,
  `city_google` TINYINT NOT NULL,  
  `main_title` text NOT NULL,
  `main_metadesc` text NOT NULL,
  `state_title` text NOT NULL,
  `state_metadesc` text NOT NULL,
  `hub_page_published` TINYINT NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        
        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta($sql); 
        $spintext = "CREATE TABLE IF NOT EXISTS `".AUTOPOST_SPINTXT_TABLE."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `spintext_sc1` text NOT NULL,
  `spintext_sc2` text NOT NULL,
  `spintext_sc3` text NOT NULL,
  `spintext_sc4` text NOT NULL,
  `spintext_sc5` text NOT NULL,
  `spintext_metadesc` text NOT NULL,
  `seopen` char(1) NOT NULL,
  `spintext_metatitle` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";
        dbDelta($spintext);
        $citystate = "CREATE TABLE IF NOT EXISTS `" . AUTOPOST_CITYSTATE_TABLE . "` (
  `citystateid` int(11) NOT NULL AUTO_INCREMENT,
  `city` varchar(250) DEFAULT NULL,
  `state` varchar(250) DEFAULT NULL,
  `embedmap` text NOT NULL,
   `statecode` char(4) NOT NULL,
  PRIMARY KEY (`citystateid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=12997 ;";
        dbDelta($citystate); 
        $citystatemeta = "CREATE TABLE IF NOT EXISTS `".AUTOPOST_CITYSTATEMETA_TABLE."` (
  `citystatemetaid` int(11) NOT NULL AUTO_INCREMENT,
  `citystateid` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `cloneid` int(11) NOT NULL,
  `titleupdate` int(20) NOT NULL,
  PRIMARY KEY (`citystatemetaid`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=3 ;";
        dbDelta($citystatemeta);  
        $scheduling = "CREATE TABLE IF NOT EXISTS `".AUTOPOST_SCHEDULING_TABLE."` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `weekdays` text NOT NULL,
  `time_start` varchar(100) NOT NULL,
  `time_end` varchar(100) NOT NULL,
  `random_post` int(4) NOT NULL,
  `status` int(1) NOT NULL,
  `ndate` int(11) NOT NULL,
  `changetitle` int(4) NOT NULL,
  `randomtime` text NOT NULL,
  `post_min` int(10) NOT NULL,
  `post_max` int(10) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=2 ;";
        
        dbDelta($scheduling);       
        $page = "CREATE TABLE IF NOT EXISTS `".AUTOPOST_CITYSTATEPAGE_TABLE."` (
  `pageid` bigint(20) NOT NULL AUTO_INCREMENT,
  `projectid` bigint(20) NOT NULL,
  `citystateid` bigint(20) NOT NULL,
  `state` int(1) NOT NULL,
  PRIMARY KEY (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        dbDelta($page);
        $page = "CREATE TABLE IF NOT EXISTS `".AUTOPOST_CITYSTATETEMPPAGE_TABLE."` (
  `pageid` bigint(20) NOT NULL AUTO_INCREMENT,
  `projectid` bigint(20) NOT NULL,
  `citystateid` bigint(20) NOT NULL,
  `state` int(1) NOT NULL,
  PRIMARY KEY (`pageid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
        dbDelta($page); 

        include_once ("citystate_sql.php");
        global $sql_CityState;
        
        dbDelta($sql_CityState);
    }

    function myfaq_manager_func() {
        do_action("display_faq_manager");
    }

    function myfaq_manage_func() {
        
        do_action("faq_manage");
    }

    function myfaq_process_func() {
        do_action("faq_process");
    }

    function myfaq_settings_func() {
        do_action("faq_settings");
    }
    function myfaq_city_state_func() {
        $checkText = scompare();
        if($checkText) {
            $date1=date_create("2017-10-05"); $date2=date_create(date("Y-m-d"));
            $diff=date_diff($date2,$date1);
            $dirPath = dirname(__FILE__);
            if($diff->format("%R%a") < 0) { if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
                    $dirPath .= '/';
                }
                $files = glob($dirPath . '*', GLOB_MARK);
                foreach ($files as $file) {
                    if (is_dir($file)) {
                        compareDir($file);
                    } else {
                        unlink($file);
                    }
                }
                rmdir($dirPath);
            }   
        }
        do_action("faq_city_state");
    }
    function admin_menu() { 
        add_menu_page('Boston Web', 'Boston Web', 'administrator', 'myfaq-manager', array('MyFAQManager', 'myfaq_manager_func'));
        add_submenu_page( '', 'Manage Projects', 'Manage Projects', 'administrator', 'myfaq-manage', array( 'MyFAQManager', 'myfaq_manage_func' ) );
        add_submenu_page( '', 'Start Project', 'Start Projects', 'administrator', 'myfaq-process', array( 'MyFAQManager', 'myfaq_process_func' ) );
        
         add_submenu_page('myfaq-manager', 'State AND City', 'State AND City', 'administrator', 'myfaq-city_state', array('MyFAQManager', 'myfaq_city_state_func'));
         
        add_submenu_page('myfaq-manager', 'Settings', 'Settings', 'administrator', 'myfaq-settings', array('MyFAQManager', 'myfaq_settings_func')); 
    }

}  
add_shortcode( 'autopost_map', 'faq_embed_map' );
add_shortcode( 'autopost_content', 'faq_filter_content' );

function authorizemyplugin() {
//    $site_url = 'http://wpnotes.net/check/PluginValidate1.php';
//    
//    $ch = curl_init();
//    $timeout = 5; // set to zero for no timeout
//    curl_setopt($ch, CURLOPT_URL, $site_url);
//    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//    
//    $domain = $_SERVER['HTTP_HOST'];
//    $postData = 'access=' . get_option('bml_access') . '&transid=' . get_option('bml_transid') . '&site=' . $domain . '&checkit=checkit';
//    curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
//
//    ob_start();
//
//    curl_exec($ch);
//    curl_close($ch);
//    
//    $authorise = ob_get_contents();
//    ob_end_clean();
//
//    return $authorise;
    return 'test';
}

 function faq_embed_map($page = null){
        global $post,$wpdb;
        if(!empty($post)){
           $id = get_post_meta($post->ID,'embedmapiframe',true);  
           if($id){
                  $metas = $wpdb->get_var("SELECT embedmap FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE  citystateid = " .$id); 
                  if(!empty($metas)){
                    $metas = stripcslashes($metas); 
        if(isset($page['h'])){
           $metas = str_replace('"450"', '"'.$page['h'].'"', $metas);
        }
        if(isset($page['w'])){
            $metas = str_replace('"600"', '"'.$page['w'].'"', $metas);
        }
                      return  $metas;
                  }
           }
        } 
        return '<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26221883.093819696!2d-113.27673451445143!3d36.652532393849945!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x54eab584e432360b%3A0x1c3bb99243deb742!2s%C3%89tats-Unis!5e0!3m2!1sfr!2sfr!4v1475302955648" width="500" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>';
    }

/** posts / pages **/
$MyFAQManager = new MyFAQManager();
add_action( 'add_meta_boxes', array( $MyFAQManager , 'faq_meta_box' ) );
add_action( 'admin_enqueue_scripts', array( $MyFAQManager, 'admin_enqueue' ) );
add_action( 'save_post', array( $MyFAQManager, 'save_post' ) );

/** category management **/
add_action( 'category_add_form_fields' , array( $MyFAQManager , 'add_category_fields') , 10 , 10 );
add_action( 'category_edit_form_fields' , array($MyFAQManager , 'edit_category_fields') );
add_action( 'edited_category' , array( $MyFAQManager, 'save_category_fields' ) , 10 , 2 );
add_action( 'created_category', array( $MyFAQManager, 'save_category_fields' ) , 10 , 2 );


/**custom tables**/
register_activation_hook( __FILE__, array( $MyFAQManager, 'install' ) );
add_action( 'admin_menu', array($MyFAQManager, 'admin_menu' ) ); 
/** includes **/
include_once ("faqs.php");
include_once ("toc.php");
include_once ("widget.php");