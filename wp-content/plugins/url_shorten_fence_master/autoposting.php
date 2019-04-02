<?php

header( 'Access-Control-Allow-Origin: *' );
require_once( dirname( __FILE__ ) . '/../../../wp-config.php' );
require_once( dirname( __FILE__ ) . '/widget.php' );
date_default_timezone_set( 'America/New_York' );
global $wpdb, $post;

$posttype = 'post';
$nextval  = true;
$spintax  = new Spintax();
$wpdb->query( "UPDATE " . AUTOPOST_SCHEDULING_TABLE . " SET status = '1' WHERE status='5' AND ndate < '" . ( strtotime( 'now' ) - 789 ) . "'" );

$now        = date( 'Y-m-d' ) . ' ';
$settings   = get_option( "faq_settings" );
$now_       = strtotime( date( 'Y-m-d H:i:s' ) );
$minutes    = intval( date( 'i', $now_ ) );
$rows       = $wpdb->get_row( "SELECT * FROM " . AUTOPOST_SCHEDULING_TABLE . " WHERE status = '1' AND ( ndate = '0' OR  ndate < '" . $now_ . "') AND randomtime like '%#" . $minutes . "#%' AND ( weekdays like '%" . date( "l" ) . "%' OR weekdays = '' )" );
$processing = false;
@ini_set( 'max_execution_time', 900 );
@ini_set( 'memory_limit', '10440M' );

if ( $rows ) {
    $wpdb->update(
        AUTOPOST_SCHEDULING_TABLE, array(
        'status' => '5',
        'ndate'  => $now_,
    ), array(
            'id' => $rows->id
        )
    );

    try {
        $rows                    = array( $rows );
        $pagecount               = 10;
        $data                    = array();
        $data[ 'email_address' ] = "info@tazar.com";
        if ( isset( $settings[ 'spin_email' ] ) ) {
            $data[ 'email_address' ] = $settings[ 'spin_email' ];
        }
        $data[ 'api_key' ] = "65d8001#5e4d6e0_0845d32?e6bdddc ";
        if ( isset( $settings[ 'splin_api_key' ] ) ) {
            $data[ 'api_key' ] = $settings[ 'splin_api_key' ];
        }
        $data[ 'action' ]             = "unique_variation_from_spintax";
        $data[ 'use_only_synonyms' ]  = "false";
        $data[ 'reorder_paragraphs' ] = "false";
        @ini_set( 'upload_max_filesize', '51200M' );

        foreach ( $rows as $row ) {
            $run = true;
            if ( ! is_null( $row->time_start ) && trim( $row->time_start ) != '' && ! is_null( $row->time_end ) && trim( $row->time_end ) != '' ) {
                $row->time_start = explode( ':', trim( $row->time_start ) );
                $row->time_end   = explode( ':', trim( $row->time_end ) );
                if ( $row->time_start[ 0 ] < 10 ) {
                    $row->time_start[ 0 ] = '0' . trim( $row->time_start[ 0 ] );
                }
                $row->time_start = implode( ':', $row->time_start );
                if ( $row->time_end[ 0 ] < 10 ) {
                    $row->time_end[ 0 ] = '0' . trim( $row->time_end[ 0 ] );
                }
                $row->time_end = implode( ':', $row->time_end );
                if ( strpos( $row->time_start, 'AM' ) !== false && ( strpos( $row->time_start, '12' ) !== false || strpos( $row->time_start, '00' ) !== false ) ) {
                    $row->time_start = str_replace( 'AM', '', $row->time_start );
                    $row->time_start = str_replace( '12', '00', $row->time_start );
                }
                if ( strpos( $row->time_end, 'AM' ) !== false && ( strpos( $row->time_end, '12' ) !== false || strpos( $row->time_end, '00' ) !== false ) ) {
                    $row->time_end = str_replace( '12', '00', $row->time_end );
                    $row->time_end = str_replace( 'AM', '', $row->time_end );
                }
                $start = strtotime( $now . str_replace( ' ', ':' . $pagecount . ' ', $row->time_start ) );
                $end   = strtotime( $now . str_replace( ' ', ':' . $pagecount . ' ', $row->time_end ) );
                if ( $start && $end && ( $start > $now_ || $end < $now_ ) ) {
                    $run        = false;
                    $processing = strtotime( 'now' );
                }
            } else {
                $run = true;
            }
            @ini_set( 'post_max_size', '51200M' );
            if ( $run ) {
                $project = $wpdb->get_row( "SELECT * FROM " . MYFAQ_TABLE . " WHERE  id = " . $row->project_id );
                if ( ! empty( $project ) ) {
                    $post = $wpdb->get_row( "SELECT * FROM " . AUTOPOST_SPINTXT_TABLE . " WHERE  project_id = " . $row->project_id );
                    if ( ! empty( $post ) ) {
						$bulk_count = 0;
						$possible = 0;
						if ($row->random_post < 60) {
							$bulk_count = 1;
						} else {
							$bulk_count = floor($row->random_post / 60);
							$possible = $row->random_post / 60 - $bulk_count;
							if (mt_rand() / mt_getrandmax() <= $possible) {
								$bulk_count++;
							}
						}

						for ( $j = 0; $j < $bulk_count; $j ++ ) {
							$citypage = $wpdb->get_row( "SELECT * FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE state = '0' AND  projectid = " . $row->project_id . " LIMIT 1" );
							if ( ! empty( $citypage ) ) {
								$state = $wpdb->get_row( "SELECT * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE  citystateid  = '" . $citypage->citystateid . "'" );
								if ( ! empty( $state ) ) {
									$meta     = $wpdb->get_row( "SELECT * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE  citystateid  = '" . $citypage->citystateid . "' AND project_id='" . $row->project_id . "'" );
									$metapost = array();
									if ( ! empty( $meta ) ) {
										$metapost = get_post( $meta->cloneid );
									}
									@ini_set( 'max_input_time', 172800 );
									set_time_limit( 172800 );
									$post_info = get_post( $project->post_to_clone );
									$option_   = get_option( 'ps_seo' );
									if ( ! is_null( $post_info ) && ! is_wp_error( $post_info ) ) {
										$posttype = $post_info->post_type;
										$apdatas  = get_autospin( $project->post_to_clone, false, $row->project_id );
										foreach ( $apdatas as $kap => $apdata ) {
											$apdata[ 'content' ] = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), $apdata[ 'content' ] ) ) );
											$apdata[ 'content' ] = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $apdata[ 'content' ] ) ) );
											$data[ 'text' ]      = str_replace( '[', '<hri>', str_replace( ']', '</hri>', $apdata[ 'content' ] ) );
											if ( trim( $apdata[ 'content' ] ) != '' ) {
												$apdatas[ $kap ][ 'content' ] = str_replace( '<hri>', '[', str_replace( '</hri>', ']', $spintax->process( $data[ 'text' ] ) ) );
											}
										}
										$apdatas_              = get_autospin( $project->post_to_clone, false, $row->project_id, $apdatas );
										$apdrafts              = get_autospin( $project->post_to_clone, true, $row->project_id, $apdatas );
										$post_                 = $post;
										$new_page              = array(
											'slug'    => str_replace( '[state-code]', strtolower( $state->statecode ), str_replace( '[state]', strtolower( $state->state ), str_replace( '[town]', strtolower( $state->city ), $project->uri ) ) ),
											'title'   => str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $project->answer ) ) ) ),
											'content' => str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_info->post_content ) ) ) )
										);
										$new_page[ 'slug' ]    = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $new_page[ 'slug' ] ) ) );
										$new_page[ 'title' ]   = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $new_page[ 'title' ] ) ) );
										$new_page[ 'content' ] = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $new_page[ 'content' ] ) ) );
										$data[ 'text' ]        = $spintax->process( $new_page[ 'title' ] );
										if ( $nextval ) {

											if ( $data[ 'text' ] != "" && $data[ 'text' ] != null ) {
												$new_page[ 'title' ] = $data[ 'text' ];
											}

											$data[ 'text' ] = str_replace( ' ', '-', $new_page[ 'slug' ] );
											if ( trim( $new_page[ 'slug' ] ) != '' ) {
												$new_page[ 'slug' ] = $spintax->process( $data[ 'text' ] );
											}

											$excerpt = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $project->excerpt ) ) ) );
											$excerpt = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $excerpt ) ) );

											if ( empty($metapost) ) {
												$result = wp_insert_post( array(
													'post_title'     => $new_page[ 'title' ],
													'post_type'      => $posttype,
													'post_name'      => $new_page[ 'slug' ],
													'post_excerpt'   => $excerpt,
													'comment_status' => 'closed',
													'ping_status'    => 'closed',
													'post_content'   => $new_page[ 'content' ],
													'post_status'    => 'publish',
													'post_author'    => 1,
													'menu_order'     => 0
												) );
												
												if ( $result && ! is_wp_error( $result ) ) {
													$processing = true;
													update_post_meta( $result, 'pageType', 'autoposting' );
													$id = $wpdb->insert(
														AUTOPOST_CITYSTATEMETA_TABLE, array(
															'citystateid' => $state->citystateid,
															'project_id'  => $row->project_id,
															'cloneid'     => $result,
															'titleupdate' => strtotime( 'now' ),
														)
													);
													if ( ! empty( $option_ ) ) {
														if ( isset( $option_[ $project->post_to_clone ] ) ) {
															$opval                  = $option_[ $project->post_to_clone ];
															$data[ 'text' ]         = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metadesc ) ) ) );
															$data[ 'text' ]         = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $data[ 'text' ] ) ) );
															$opval[ 'description' ] = $spintax->process( $data[ 'text' ] );
															$data[ 'text' ]         = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metatitle ) ) ) );
															$data[ 'text' ]         = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $data[ 'text' ] ) ) );
															$opval[ 'title' ]       = $spintax->process( $data[ 'text' ] );
															$opval[ 'enabled' ]     = '0';
															if ( $post_->seopen ) {
																$opval[ 'enabled' ] = '1';
															}
															$option_[ $result ] = $opval;
															update_option( 'ps_seo', $option_ );
														}
													}
													$metas = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE  post_id = " . $project->post_to_clone );
													foreach ( $metas as $meta ) {
														if ( $meta->meta_key != '_fl_builder_data' && $meta->meta_key != '_fl_builder_draft' ) {
															$wpdb->insert(
																$wpdb->prefix . 'postmeta', array(
																	'post_id'    => $result,
																	'meta_key'   => $meta->meta_key,
																	'meta_value' => $meta->meta_value,
																)
															);
														}
													}
													$seo = '0';
													if ( $post_->seopen ) {
														$seo = '1';
													}
													$wpdb->update(
														AUTOPOST_CITYSTATEPAGE_TABLE, array(
														'state' => '1'
													), array(
															'pageid' => $citypage->pageid
														)
													);

													add_post_meta( $result, 'ps_seo_enabled', $seo );
													update_post_meta( $result, 'ps_seo_enabled', $seo );
													add_post_meta( $result, '_fl_builder_enabled', '1' );
													update_post_meta( $result, '_fl_builder_enabled', '1' );
													add_post_meta( $result, 'city', $state->city );
													update_post_meta( $result, 'city', $state->city );
													add_post_meta( $result, 'state', $state->state );
													update_post_meta( $result, 'state', $state->state );
													add_post_meta( $result, '_fl_builder_data', $apdatas_ );
													update_post_meta( $result, '_fl_builder_data', $apdatas_ );
													add_post_meta( $result, '_fl_builder_draft', $apdrafts );
													update_post_meta( $result, '_fl_builder_draft', $apdrafts );
													add_post_meta( $result, 'autospinproject', $row->project_id );
													update_post_meta( $result, 'autospinproject', $row->project_id );
													add_post_meta( $result, 'embedmapiframe', $state->citystateid );
													update_post_meta( $result, 'embedmapiframe', $state->citystateid );
													$desc = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metadesc ) ) ) );
													$desc = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $desc ) ) );
													add_post_meta( $result, 'ps_seo_description', $desc );
													update_post_meta( $result, 'ps_seo_description', $desc );
													$title = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metatitle ) ) ) );
													$title = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $title ) ) );
													add_post_meta( $result, 'ps_seo_title', $title );
													update_post_meta( $result, 'ps_seo_title', $title );
													$date_ = strtotime( 'now' );
													$date_ = $date_ + 1;
													$wpdb->update(
														AUTOPOST_SCHEDULING_TABLE, array(
														'ndate' => $date_,
													), array(
															'id' => $row->id
														)
													);
												}
											} else {
												$wpdb->update( $wpdb->prefix . "posts", array(
													'post_title'   => $new_page[ 'title' ],
													'post_name'    => $new_page[ 'slug' ],
													'post_excerpt' => $excerpt,
													'post_content' => $new_page[ 'content' ],
													'post_status'  => 'publish',
												), array(
														'ID' => $metapost->ID
													)
												);
												$result     = $metapost->ID;
												$processing = true;
												$wpdb->update( AUTOPOST_CITYSTATEMETA_TABLE, array(
													'titleupdate' => strtotime( 'now' ),
												), array(
														'citystateid' => $state->citystateid,
														'project_id'  => $row->project_id,
														'cloneid'     => $result,
													)
												);
												if ( ! empty( $option_ ) ) {
													if ( isset( $option_[ $project->post_to_clone ] ) ) {
														$opval                  = $option_[ $project->post_to_clone ];
														$data[ 'text' ]         = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metadesc ) ) ) );
														$data[ 'text' ]         = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $data[ 'text' ] ) ) );
														$opval[ 'description' ] = $spintax->process( $data[ 'text' ] );
														$data[ 'text' ]         = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metatitle ) ) ) );
														$data[ 'text' ]         = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $data[ 'text' ] ) ) );
														$opval[ 'title' ]       = $spintax->process( $data[ 'text' ] );
														$opval[ 'enabled' ]     = '0';
														if ( $post_->seopen ) {
															$opval[ 'enabled' ] = '1';
														}
														$option_[ $result ] = $opval;
														update_option( 'ps_seo', $option_ );
													}
												}
												$metas = $wpdb->get_results( "SELECT * FROM " . $wpdb->prefix . "postmeta WHERE  post_id = " . $project->post_to_clone );
												foreach ( $metas as $meta ) {
													if ( $meta->meta_key != '_fl_builder_data' && $meta->meta_key != '_fl_builder_draft' ) {
														add_post_meta( $result, $meta->meta_key, $meta->meta_value );
														update_post_meta( $result, $meta->meta_key, $meta->meta_value );
													}
												}
												$seo = '0';
												if ( $post_->seopen ) {
													$seo = '1';
												}
												$wpdb->update(
													AUTOPOST_CITYSTATEPAGE_TABLE, array(
													'state' => '1'
												), array(
														'pageid' => $citypage->pageid
													)
												);
												add_post_meta( $result, 'ps_seo_enabled', $seo );
												update_post_meta( $result, 'ps_seo_enabled', $seo );
												add_post_meta( $result, '_fl_builder_enabled', '1' );
												update_post_meta( $result, '_fl_builder_enabled', '1' );
												add_post_meta( $result, 'city', $state->city );
												update_post_meta( $result, 'city', $state->city );
												add_post_meta( $result, 'state', $state->state );
												update_post_meta( $result, 'state', $state->state );
												add_post_meta( $result, '_fl_builder_data', $apdatas_ );
												update_post_meta( $result, '_fl_builder_data', $apdatas_ );
												add_post_meta( $result, '_fl_builder_draft', $apdrafts );
												update_post_meta( $result, '_fl_builder_draft', $apdrafts );
												add_post_meta( $result, 'autospinproject', $row->project_id );
												update_post_meta( $result, 'autospinproject', $row->project_id );
												add_post_meta( $result, 'embedmapiframe', $state->citystateid );
												update_post_meta( $result, 'embedmapiframe', $state->citystateid );
												$desc = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metadesc ) ) ) );
												$desc = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $desc ) ) );
												add_post_meta( $result, 'ps_seo_description', $desc );
												update_post_meta( $result, 'ps_seo_description', $desc );
												$title = str_replace( '[state-code]', ucwords( $state->statecode ), str_replace( '[state]', ucwords( $state->state ), str_replace( '[town]', ucwords( $state->city ), stripslashes( $post_->spintext_metatitle ) ) ) );
												$title = str_replace( '[STATE-CODE]', strtoupper( $state->statecode ), str_replace( '[STATE]', strtoupper( $state->state ), str_replace( '[TOWN]', strtoupper( $state->city ), $title ) ) );
												add_post_meta( $result, 'ps_seo_title', $title );
												update_post_meta( $result, 'ps_seo_title', $title );
												$date_ = strtotime( 'now' );

												$date_ = $date_ + 1;

												$wpdb->update(
													AUTOPOST_SCHEDULING_TABLE, array(
													'ndate' => $date_,
												), array(
														'id' => $row->id
													)
												);
											}
										}
									}
								} else {

								}
							}
						}
                    }

                    
                }
            }
            $set = array(
                'status' => '1',
            );
            if ( ! $processing ) {
                $set[ 'ndate' ] = $now_ + 7200;
            }
            $wpdb->update(
                AUTOPOST_SCHEDULING_TABLE, $set, array(
                    'id' => $row->id
                )
            );
        }




    } catch ( Exception $e ) {
        $set            = array(
            'status' => '1',
        );
        $set[ 'ndate' ] = strtotime( 'now' );
        $wpdb->update(
            AUTOPOST_SCHEDULING_TABLE, $set, array(
                'id' => $row->id
            )
        );
    }
}

/* Publish state hubs */
$fp = fopen('/home/u3jtf68/public_html/wp-content/plugins/me.txt', 'a+');

/* get google shorten url */
$home_url = home_url() . '/';

$sql = "SELECT * FROM " . MYFAQ_TABLE ." ORDER BY id DESC";
$projects = $wpdb->get_results($sql);

if ($projects) {
	foreach ($projects as $project){
		$total_new  = $wpdb->get_var("SELECT count(citystateid) FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE state='0' AND projectid = '" .$project->id  . "'");
        $total_posted  = $wpdb->get_var("SELECT count(citystateid) FROM " . AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE state='1' AND projectid = '" .$project->id  . "' "); 

        // Publish once completed
        if( $total_posted > 0 && $total_new == 0 && $project->hub_page_published == 0) {
        	/* get selected states */
			$db_selected_states = $wpdb->get_results("SELECT DISTINCT state, statecode FROM ".AUTOPOST_CITYSTATE_TABLE." WHERE citystateid IN (SELECT citystateid FROM ".AUTOPOST_CITYSTATEPAGE_TABLE." WHERE projectid = ".$project->id.")");
			
			$selected_states = array();
            foreach($db_selected_states as $state){
            	$selected_states[] = $state->state;
                /* add empty state page */
                $state_url = str_replace('[state]', strtolower($state->state), $project->state_hub_location);
				$state_title = str_replace('[state]', $state->state, $project->state_title);

				$state_content = 
				"<div class='fl-builder-content fl-builder-content-primary locations container'>
					<div class='fl-row fl-row-full-width fl-row-bg-none'>
						<div class='fl-row-content-wrap'>
							<div class='fl-row-content fl-row-fixed-width fl-node-content'>
								<div class='fl-col-group'>
									<div class='fl-col col-xs-12'>
										<div class='fl-col-content fl-node-content'>
											<div class='fl-module fl-module-rich-text'>
												<div class='fl-module-content fl-node-content'>
													<div class='fl-rich-text'><p><strong>". $state_title ."</strong></p>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
								<div class='fl-col-group'>";

				/* loop cities */
				$sql = "SELECT * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid IN (SELECT citystateid FROM " .AUTOPOST_CITYSTATEPAGE_TABLE . " WHERE projectid = '" . $project->id . "') AND state = '" . $state->state ."'";

				$db_cities = $wpdb->get_results($sql);
				if(empty($db_cities)){
					$state_content .= "No Cities";
				}
				
				//cols
				$col_cities = array_chunk($db_cities, ceil(count($db_cities) / 4));

				foreach ($col_cities as $cities) {

					$col_content =
						"<div class='fl-col col-sm-3'>
							<div class='fl-col-content fl-node-content'>
								<div class='fl-module fl-module-rich-text'>
									<div class='fl-module-content fl-node-content'>
										<div class='fl-rich-text'><p><span style='color: #23a334;font-size: 12px;'><strong>";

					foreach($cities as $city){
						$postid_ = $wpdb->get_var("SELECT cloneid FROM ".AUTOPOST_CITYSTATEMETA_TABLE." WHERE project_id = '".$project->id."' AND citystateid = '".$city->citystateid."'");
				       	$gpost = get_post($postid_);
					    if(!empty($gpost)){
					    	$tag = str_replace('[town]', $city->city, str_replace('[state-code]', $state->statecode, str_replace('[state]', $state->state, $project->city_tag)));
					    	$city_link_text = str_replace('[town]', $city->city, str_replace('[state-code]', $state->statecode, str_replace('[state]', $state->state, $project->city_link_text)));

					    	$c_url = get_permalink($gpost->ID);

					        if ($project->city_google == 1) {
					        	$c_url = get_bulk_url($c_url) . '#' . $tag;
					        } else {
					        	$c_url = $c_url . '#' . $tag;

					        }
					        $col_content .= "<a class='city-location' href='" . $c_url . "'>" . $city_link_text . "</a><br>";

					    }
					    
					}

					$col_content .= "</strong></span></p>
										</div>
									</div>
								</div>
							</div>
						</div>";

					$state_content .= $col_content;

				}

				$state_content .= "</div></div></div></div></div>";

				$state_posts = get_posts( array( 'name' => $state_url, 'post_type' => 'page') );
				
				if( $state_posts)
				{
				    $state_post = $state_posts[0];
				    $result = wp_update_post( array(
				    	'ID'			 => $state_post->ID,
						'post_title'     => $state_title,
						'post_type'      => 'page',
						'post_name'      => $state_url,
						'post_excerpt'   => '',
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => $state_content,
						'post_status'    => 'publish',
						'post_author'    => 1,
						'menu_order'     => 0
					) );


					if (!is_null(get_post_meta($result, '__fl_builder_enabled'))) {
						update_post_meta( $result, '_fl_builder_enabled', '0' );
						update_post_meta( $result, '_fl_builder_data', '' );
						update_post_meta( $result, '_fl_builder_data_settings', '' );
						update_post_meta( $result, '_fl_builder_draft', '' );
						update_post_meta( $result, '_fl_builder_draft_settings', '' );
					} else {
						add_post_meta( $result, '_fl_builder_enabled', '0' );
					}
					
					if (!is_null(get_post_meta($result, '_wp_page_template'))) {
						update_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
					} else {
						add_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
					}
					
					if (!is_null(get_post_meta($result, 'ps_seo_enabled'))) {
						update_post_meta( $result, 'ps_seo_enabled', '1' );
					} else {
						add_post_meta( $result, 'ps_seo_enabled', '1' );
					}

					$state_metadesc = str_replace('[state]', $state->state, $project->state_metadesc);
					if (!is_null(get_post_meta($result, 'ps_seo_description'))) {
						update_post_meta( $result, 'ps_seo_description', $state_metadesc );
					} else {
						add_post_meta( $result, 'ps_seo_description', $state_metadesc );
					}

					if (!is_null(get_post_meta($result, 'ps_seo_title'))) {
						update_post_meta( $result, 'ps_seo_title', $state_title );
					} else {
						add_post_meta( $result, 'ps_seo_title', $state_title );
					}

				} else {
					$result = wp_insert_post( array(
						'post_title'     => $state_title,
						'post_type'      => 'page',
						'post_name'      => $state_url,
						'post_excerpt'   => '',
						'comment_status' => 'closed',
						'ping_status'    => 'closed',
						'post_content'   => $state_content,
						'post_status'    => 'publish',
						'post_author'    => 1,
						'menu_order'     => 0
					) );

					add_post_meta( $result, '_fl_builder_enabled', '0' );
					add_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
					add_post_meta( $result, 'ps_seo_enabled', '1' );
					
					$state_metadesc = str_replace('[state]', $state->state, $project->state_metadesc);
					add_post_meta( $result, 'ps_seo_description', $state_metadesc );
					add_post_meta( $result, 'ps_seo_title', $state_title );
				}
				/* end state page */
            }

        	/* create state HUB */
        	$state_hub_url = $project->main_hub_location;
			$state_hub_title = $project->main_title;
			$state_hub_content = 
			"<div class='fl-builder-content fl-builder-content-primary locations container'>
				<div class='fl-row fl-row-full-width fl-row-bg-none'>
					<div class='fl-row-content-wrap'>
						<div class='fl-row-content fl-row-fixed-width fl-node-content'>
							<div class='fl-col-group text-center'>
								<div class='fl-col'>
									<div class='fl-col-content fl-node-content'>
										<div class='fl-module fl-module-rich-text'>
											<div class='fl-module-content fl-node-content'>
												<div class='fl-rich-text'><strong><h3 style='color: #23a334; margin: 0 0 50px;'>". $state_hub_title ."</h3></strong>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class='fl-col-group'>";

			/* loop states */
			$db_states = $wpdb->get_results("SELECT DISTINCT state FROM " . AUTOPOST_CITYSTATE_TABLE);
			
			//cols
			$col_states = array_chunk($db_states, ceil(count($db_states) / 4));

			foreach ($col_states as $states) {

				$col_content =
					"<div class='fl-col col-sm-3'>
						<div class='fl-col-content fl-node-content'>
							<div class='fl-module fl-module-rich-text'>
								<div class='fl-module-content fl-node-content'>
									<div class='fl-rich-text'><p><span style='font-size: 12px;'><strong>";

				foreach($states as $state){
					$state_url = str_replace('[state]', strtolower($state->state), $project->state_hub_location);
					$state_tag = str_replace('[state]', $state->state, $project->state_tag);
					$state_link_text = str_replace('[state]', $state->state, $project->state_link_text);
					$show_url = $home_url . $state_url;

					if (in_array($state->state, $selected_states)) {
						if ($project->state_google == 1) {
						    $show_url = get_bulk_url($show_url);
						}

						$col_content .= "<a href='" . $show_url . '#' . $state_tag  . "'>" . $state_link_text . "</a><br>";
					} else {
						$col_content .= $state_link_text . "<br>";
					}
				}

				$col_content .= "</strong></span></p>
									</div>
								</div>
							</div>
						</div>
					</div>";

				$state_hub_content .= $col_content;

			}

			$state_hub_content .= "</div></div></div></div></div>";

			$state_hub_posts = get_posts( array( 'name' => $state_hub_url, 'post_type' => 'page') );
			if( $state_hub_posts)
			{
			    $state_hub_post = $state_hub_posts[0];
			    $result = wp_update_post( array(
			    	'ID'			 => $state_hub_post->ID,
					'post_title'     => $state_hub_title,
					'post_type'      => 'page',
					'post_name'      => $state_hub_url,
					'post_excerpt'   => '',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => $state_hub_content,
					'post_status'    => 'publish',
					'post_author'    => 1,
					'menu_order'     => 0
				) );

				if (!is_null(get_post_meta($result, '__fl_builder_enabled'))) {
					update_post_meta( $result, '_fl_builder_enabled', '0' );
					update_post_meta( $result, '_fl_builder_data', '' );
					update_post_meta( $result, '_fl_builder_data_settings', '' );
					update_post_meta( $result, '_fl_builder_draft', '' );
					update_post_meta( $result, '_fl_builder_draft_settings', '' );
				} else {
					add_post_meta( $result, '_fl_builder_enabled', '0' );
				}
				
				if (!is_null(get_post_meta($result, '_wp_page_template'))) {
					update_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
				} else {
					add_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
				}
				
				if (!is_null(get_post_meta($result, 'ps_seo_enabled'))) {
					update_post_meta( $result, 'ps_seo_enabled', '1' );
				} else {
					add_post_meta( $result, 'ps_seo_enabled', '1' );
				}
				
				$main_metadesc = $project->main_metadesc;

				if (!is_null(get_post_meta($result, 'ps_seo_description'))) {
					update_post_meta( $result, 'ps_seo_description', $main_metadesc );
				} else {
					add_post_meta( $result, 'ps_seo_description', $main_metadesc );
				}

				if (!is_null(get_post_meta($result, 'ps_seo_title'))) {
					update_post_meta( $result, 'ps_seo_title', $state_hub_title );
				} else {
					add_post_meta( $result, 'ps_seo_title', $state_hub_title );
				}
				
			} else {
				$result = wp_insert_post( array(
					'post_title'     => $state_hub_title,
					'post_type'      => 'page',
					'post_name'      => $state_hub_url,
					'post_excerpt'   => '',
					'comment_status' => 'closed',
					'ping_status'    => 'closed',
					'post_content'   => $state_hub_content,
					'post_status'    => 'publish',
					'post_author'    => 1,
					'menu_order'     => 0
				) );

				add_post_meta( $result, '_fl_builder_enabled', '0' );
				add_post_meta( $result, '_wp_page_template', 'templates/template-full-width-infinite.php' );
				add_post_meta( $result, 'ps_seo_enabled', '1' );
				$main_metadesc = $project->main_metadesc;
				add_post_meta( $result, 'ps_seo_description', $main_metadesc );
				add_post_meta( $result, 'ps_seo_title', $state_hub_title );
			}

			/* Update HUB published status */
			$wpdb->update(MYFAQ_TABLE, array('hub_page_published' => 1), array('id' => $project->id));
			
			/* end state HUB page */
        }
	}
}

fclose($fp);
