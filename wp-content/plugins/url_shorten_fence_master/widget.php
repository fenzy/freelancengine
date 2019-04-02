<?php
$ui_themes = array(
	'black-tie' => "Blank Tie",
	'blitzer' => "Blitzer",
	'cupertino' => "Cupertino",
	'dark-hive' => "Dark Hive",
	'dot-luv' => "Dot Luv",
	'eggplant' => "Eggplant",
	'excite-bike' => "Excite Bike",
	'flick' => "Flick",
	'hot-sneaks' => "Hot Sneaks",
	'humanity' => "Humanity",
	'le-frog' => "Le Frog",
	'mint-choc' => "Mint Choc",
	'overcast' => "Overcast",
	'pepper-grinder' => "Pepper Grinder",
	'redmond' => "Redmond",
	'smoothness' => "Smoothness",
	'south-street' => "South Street",
	'start' => "Start",
	'sunny' => "Sunny",
	'swanky-purse' => "Swanky Purse",
	'trontastic' => "Trontastic",
	'ui-darkness' => "UI Darkness",
	'ui-lightness' => "UI Lightness",
	'vader' => "Veder"
);
$faq_before_widget = $faq_after_widget = $faq_before_title = $faq_after_title = $faq_title = $faq_total_faq = $faq_custom_css = "";
class FAQ_Widget extends WP_Widget {
    function FAQ_Widget() {  
	global $auth;
		if($auth==1) {	
        $widget_ops = array( 'classname' => 'faq_widget', 'description' => 'A widget that displays the Notes according to Post/Page/Category or Primary ' );  
        $control_ops = array( 'width' => 400, 'height' => 450, 'id_base' => 'faq_widget-widget' );  
        $this->WP_Widget( 'faq_widget-widget', 'WPNote Widget', $widget_ops, $control_ops );  
    }
	}
	
	function widget( $args, $instance ) {
		extract($instance);
		extract( $args );
		global $faq_before_widget, $faq_after_widget, $faq_before_title, $faq_after_title, $faq_title, $faq_total_faqs, $faq_custom_css;
		$faq_before_title = $before_title;
		$faq_after_title = $after_title;
		$faq_before_widget = $before_widget;
		$faq_after_widget = $after_widget;
		$faq_title = $title; 
		$faq_total_faqs = $total_faqs;
		$faq_custom_css = $custom_css;
		
		wp_enqueue_script( 'jquery' );
		wp_enqueue_script( 'jquery-ui-accordion' );
		wp_enqueue_style( 'faq-jquery-ui', "http://code.jquery.com/ui/1.10.3/themes/{$theme}/jquery-ui.css", array(), date("Ymdhms") );
		
		$own_FAQs = $show_faq = FALSE;
		$settings = get_option("faq_settings");
		if ( !$settings ) {
			$settings['home'] = FALSE;
			$settings['categories'] = FALSE;
			$settings['posts'] = FALSE;
			$settings['pages'] = FALSE;
		}
		if ( is_single() || is_page() || is_category() ) { // post / page comes / category
			if ( is_single() ) {
				global $post;
				$enabled_faq = get_post_meta($post->ID, '_enable_faq', TRUE);
				if ( $enabled_faq && $enabled_faq == 1 ) {
					$faqs = get_post_meta($post->ID, "faqs", TRUE);
					$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
					if ( $total ) {
						do_action("display-faqs", $faqs);
					} else {
						$category = get_the_category();
						$faqs = get_option( 'category_' . $category[0]->term_id );
						
						if ( isset($faqs['enabled']) && $faqs['enabled'] == 1 ){
							$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
							if ( $total ) {
								do_action("display-faqs", $faqs);
							}
						} 
					}
				} else{
					$category = get_the_category();
					$faqs = get_option( 'category_' . $category[0]->term_id );
					
					if ( isset($faqs['enabled']) && $faqs['enabled'] == 1 ){
						$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
						if ( $total ) {
							do_action("display-faqs", $faqs);
						}
					} else if ( $settings['posts'] ) {
						do_action('display-primary-faqs', $total_faqs);
					}
				}
			} else if ( is_page() ) {
				
				global $post;
				$enabled_faq = get_post_meta($post->ID, '_enable_faq', TRUE);
				if ( $enabled_faq && $enabled_faq == 1 ) {
					$faqs = get_post_meta($post->ID, "faqs", TRUE);
					$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
					if ( $total ) {
						do_action("display-faqs", $faqs);
					} elseif ( $settings['pages'] ) {
						do_action('display-primary-faqs', $total_faqs);
					}
				} elseif ( $settings['pages'] ) {
					do_action('display-primary-faqs', $total_faqs);
				}
			} else if ( is_category() ) {
				$cat_id = get_cat_id( single_cat_title( "", FALSE ) );
				$faqs = get_option( 'category_' . $cat_id );
				if ( isset($faqs['enabled']) && $faqs['enabled'] == 1 ){
					$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
					if ( $total ) {
						do_action("display-faqs", $faqs); 
					} elseif ( $settings['categories'] ) {
						do_action('display-primary-faqs', $total_faqs);
					}
				}  elseif ( $settings['categories'] ) {
					do_action('display-primary-faqs', $total_faqs);
				}
			}
		} else if ( ( is_home() || is_front_page() ) && $settings['home'] ) {
			do_action('display-primary-faqs', $total_faqs);
		} 
		return; 
	}
	
	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}
	
	function form( $instance ) {
	global $auth;
		if($auth==1) {
		$title = isset( $instance[ 'title' ] ) ? $instance[ 'title' ] :  NULL;
		$total_faqs = isset( $instance[ 'total_faqs' ] ) ? $instance[ 'total_faqs' ] :  5;
		$theme = isset( $instance[ 'theme' ] ) ? $instance[ 'theme' ] :  "black-tie";
		$custom_css = isset( $instance[ 'custom_css' ] ) ? $instance[ 'custom_css' ] :  NULL;
		global $ui_themes;
		?>
		<p>
			<label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'total_faqs' ); ?>">Total Number of Notes:</label> 
			<input class="widefat" id="<?php echo $this->get_field_id( 'total_faqs' ); ?>" name="<?php echo $this->get_field_name( 'total_faqs' ); ?>" type="text" value="<?php echo esc_attr( $total_faqs ); ?>" />
		</p>
		<p>
		<label for="<?php echo $this->get_field_id( 'theme' ); ?>">jQuery Accordion Theme:</label>
		<select class="widefat" id="<?php echo $this->get_field_id( 'theme' ); ?>" name="<?php echo $this->get_field_name( 'theme' ); ?>">
			<?php
			foreach($ui_themes as $ui_theme => $name) {
				$sel = $ui_theme == $theme ? "selected='selected'" : NULL;
				echo "<option value='{$ui_theme}' {$sel}>{$name}</option>";
			}
			?>
		</select> 
		</p>
		<p>
			<label for="<?php echo $this->get_field_id( 'custom_css' ); ?>">Custom CSS:</label> 
			<textarea class="widefat" style="height:200px;" id="<?php echo $this->get_field_id( 'custom_css' ); ?>" name="<?php echo $this->get_field_name( 'custom_css' ); ?>"><?php echo esc_attr( $custom_css ); ?></textarea>
			<strong>
				Custom CSS structure: 
<code>
<pre>
h3.question-heading {
	font-family: Tahoma;
	font-size: 14px; 
	color: #B52700;
}

div.answer-contents {
	font-family: Tahoma;
	font-size: 14px;
	font-weight: normal;
	color: #B35B22;
}
</pre>
</code>
			</strong>
		</p>
		<?php 
		
	}
	
} 
}
function register_faq_widget() {  
global $auth;
		if($auth==1) {
    register_widget( 'FAQ_Widget' );  
	}
}  
add_action( 'widgets_init', 'register_faq_widget' ); 



function GetSyncronizeDataFromMasterSite($url){
  $data_string=array();
    $url_master=$url.'/wp-content/plugins/WPNotes/ajaxfile.php';
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $url_master);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
      curl_setopt($ch, CURLOPT_TIMEOUT, 5);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array(
           'Content-Type: application/json',
           'Content-Length: ' . strlen($data_string))
         );
      $data = curl_exec($ch);
      $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      curl_close($ch);
      $response=json_decode($data,true);
      return $response;
}
add_action('GetSyncronizeDataFromMasterSite','GetSyncronizeDataFromMasterSite');

function display_primary_faqs_func($limit) {
	global $faq_before_widget, $faq_after_widget, $faq_before_title, $faq_after_title, $faq_title, $faq_total_faqs, $faq_custom_css, $wpdb;

        $settings = get_option("faq_settings");
        $settingscheck = isset($settings['mastersitedata']) && $settings['mastersitedata'] == TRUE ?: FALSE;
        $settingscheckurl = isset($settings['mastersitedataurl']) ? $settings['mastersitedataurl'] : FALSE;
        if($settingscheck && strlen($settingscheckurl) > 1){
             $datas=GetSyncronizeDataFromMasterSite($settingscheckurl);
             $accordion = "";
	     if ( $datas ) {
		$unique = rand(1, 99999);
		foreach($datas as $faq) {
			$accordion .= "<h3 class='question-heading'>".spin( stripslashes($faq['question']))."</h3>";
			$accordion .= "<div class='answer-contents'>". spin(stripslashes($faq['answer']))."</div>";
		}
             }
        }else{
             $sql = "SELECT * FROM " . MYFAQ_TABLE . " ORDER BY rand() LIMIT {$limit}";
	     $faqs = $wpdb->get_results($sql);
	     $accordion = "";
	     if ( $faqs ) {
		$unique = rand(1, 99999);
		foreach($faqs as $faq) {
			$accordion .= "<h3 class='question-heading'>". spin(stripslashes($faq->question))."</h3>";
			$accordion .= "<div class='answer-contents'>". spin(stripslashes($faq->answer))."</div>";
		}
              }
        }
		if ( $accordion != "" ) {
			echo $faq_before_widget;
			if ( !empty($faq_title) ) { echo $faq_before_title . $faq_title . $faq_after_title; }
			?>
			<div id='faq-accordion-<?php echo $unique;?>'><?php echo $accordion;?></div>
			<script type='text/javascript'>
				jQuery(document).ready(function($){
					$("#faq-accordion-<?php echo $unique;?>").accordion({
						heightStyle: "content",
			            autoHeight: true,
			        	clearStyle: true,
			        	collapsible: true,
			        	active: false
					});
				});
			</script>
			<style>
.ui-icon {
				display:none !important;
				}
h3.question-heading {margin-bottom:0px !important;margin-top: 1px!important;    padding: 12px 10px!important;}
				.ui-accordion .ui-accordion-icons {
				padding-left:10px !important;
				}
</style>
			<?php
			if ( !empty( $faq_custom_css ) ) :
			?>
			<style type='text/css'>
				<?php echo $custom_css;?>
			</style>
			<?php
			endif;
			echo $faq_after_widget;
		}
}
add_action("display-primary-faqs", "display_primary_faqs_func", 10, 2);

function display_faqs_func( $faqs ) {
	global $faq_before_widget, $faq_after_widget, $faq_before_title, $faq_after_title, $faq_title, $faq_total_faqs, $faq_custom_css;
	$total = $faqs && isset($faqs['a']) && count($faqs['a']) > 0 ? count($faqs['a']) : 0;
	if ( $total < 1 ){
		return; 
	}
	$unique = rand(1, 99999);
	$accordion = "";
	$faqs = shuffle_faqs($faqs);
	for($i = 0; $i < $faq_total_faqs; $i++) {
		if ( isset($faqs['q'][$i]) && $faqs['q'][$i] != "" && $faqs['a'][$i] != "" ) {
			$accordion .= "<h3 class='question-heading'>{$faqs['q'][$i]}</h3>";
			$accordion .= "<div class='answer-contents'>{$faqs['a'][$i]}</div>";
		}
	}
	if ( $accordion != "" ) {
		echo $faq_before_widget;
		if ( !empty($faq_title) ) { echo $faq_before_title . $faq_title . $faq_after_title; }
		?>
		<div id='faq-accordion-<?php echo $unique;?>'><?php echo $accordion;?></div>
		<script type='text/javascript'>
			jQuery(document).ready(function($){
				$("#faq-accordion-<?php echo $unique;?>").accordion({
					heightStyle: "content",
		            autoHeight: true,
		        	clearStyle: true,
		        	collapsible: true,
	        		active: false
				});
			});
		</script>
		<style>
.ui-icon {
				display:none !important;
				}
h3.question-heading {margin-bottom:0px !important;margin-top: 1px!important;    padding: 12px 10px!important;}
				.ui-accordion .ui-accordion-icons {
				padding-left:10px !important;
				}
</style>
		<?php
		if ( !empty( $faq_custom_css ) ) :
		?>
		<style type='text/css'>
			<?php echo $custom_css;?>
		</style>
		<?php
		endif;
		echo $faq_after_widget;
	}
}
add_action("display-faqs", "display_faqs_func", 10, 2);


function shuffle_faqs($faqs){
	$question = $faqs['q'];
	$answers = $faqs['a'];
	$total_faqs = count($question);
	for($i=0; $i<$total_faqs; $i++){
		$random_keys[] = $i;
	}
	
	shuffle($random_keys);
	foreach($random_keys as $key=>$val){
		if ( isset($faqs['q'][$val]) ) {
			$random_faqs['q'][] = $faqs['q'][$val];
			$random_faqs['a'][] = $faqs['a'][$val];
		}
	}
	return $random_faqs;
} 