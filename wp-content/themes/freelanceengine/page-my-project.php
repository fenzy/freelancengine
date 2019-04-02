<?php
/**
 * Template Name: My Project
 */

if ( ! is_user_logged_in() ) {
	wp_redirect( et_get_page_link( 'login', array( 'ae_redirect_url' => get_permalink( $post->ID ) ) ) );
}

get_header();
global $wpdb, $wp_query, $ae_post_factory, $post, $current_user, $user_ID;
$user_role = ae_user_role( $user_ID );
define( 'NO_RESULT', __( '<span class="project-no-results">There are no activities yet.</span>', ET_DOMAIN ) );
$currency = ae_get_option( 'currency', array( 'align' => 'left', 'code' => 'USD', 'icon' => '$' ) );

?>
    <div class="fre-page-wrapper">
        <div class="fre-page-title">
            <div class="container">
                <h2><?php the_title(); ?></h2>
            </div>
        </div>
        <div class="fre-page-section">
            <div class="container">
                <div class="my-work-employer-wrap">
                    <div id="geocodepreview" style="height: 240px; position: relative; overflow: hidden; margin: 20px;"></div>
					<?php if ( fre_share_role() || $user_role == FREELANCER ) {
						fre_show_credit( FREELANCER );
					}/* else {
						fre_user_package_info( $user_ID );
					} */ ?>
                    <ul class="fre-tabs nav-tabs-my-work">
                        <li class="active"><a data-toggle="tab"
                                              href="#current-project-tab"><span><?php _e( 'Current Projects', ET_DOMAIN ); ?></span></a>
                        </li>
                        <li class="next"><a data-toggle="tab"
                                            href="#previous-project-tab"><span><?php _e( 'Previous Projects', ET_DOMAIN ); ?></span></a>
                        </li>
                    </ul>
                    <div class="fre-tab-content">
						<?php if ( fre_share_role() || $user_role == FREELANCER ) { ?>
                            <div id="current-project-tab" class="freelancer-current-project-tab fre-panel-tab active">
                                <div class="fre-work-project-box">
                                    <div class="work-project-filter">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                                                        <input type="text" class="search" name="s"
                                                               placeholder="<?php _e( 'Search projects by keywords', ET_DOMAIN ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Filter', ET_DOMAIN ); ?></label>
                                                        <select class="fre-chosen-single" name="bid_current_status">
                                                            <option value=""><?php _e( 'All Projects', ET_DOMAIN ); ?></option>
                                                            <option value="accept"><?php _e( 'Processing', ET_DOMAIN ); ?></option>
                                                            <option value="unaccept"><?php _e( 'Unaccepted', ET_DOMAIN ); ?></option>
                                                            <option value="disputing"><?php _e( 'Disputed', ET_DOMAIN ); ?></option>
                                                            <option value="publish"><?php _e( 'Active', ET_DOMAIN ); ?></option>
                                                            <option value="archive"><?php _e( 'Archived', ET_DOMAIN ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="clear-filter work-project-filter-clear secondary-color"
                                               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
                                        </form>
                                    </div>
                                </div>
                                <div class="fre-work-project-box">
									<?php
									$is_author   = is_author();
									$post_parent = array();
									$result      = $wpdb->get_col( "SELECT * FROM $wpdb->posts WHERE 1=1 AND post_type = 'project' AND post_status IN ( 'publish', 'close', 'archive', 'disputing' )" );
									if ( ! empty( $result ) ) {
										$post_parent = $result;
									}
									$freelancer_current_project_query = new WP_Query(
										array(
											'post_status'      => array(
												'publish',
												'accept',
												'unaccept',
												'disputing',
												'archive'
											),
											'post_type'        => BID,
											'author'           => $current_user->ID,
											'accepted'         => 1,
											'is_author'        => $is_author,
											'suppress_filters' => true,
											'orderby'          => 'date',
											'order'            => 'DESC'
										)
									);
									$post_object                      = $ae_post_factory->get( BID );
									$no_result_current                = '';
									?>
                                    <div class="current-freelance-project">
                                        <div class="fre-table">
                                            <div class="fre-table-head">
                                                <div class="fre-table-col project-title-col"><?php _e( 'Project Title', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-budget-col"><?php _e( 'Budget', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-open-col"><?php _e( 'Open Date', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-status-col"><?php _e( 'Status', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-address-col"><?php _e( 'Address', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-city-col"><?php _e( 'City', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-action-col"><?php _e( 'Action', ET_DOMAIN ); ?></div>
                                            </div>
                                            <div class="fre-current-table-rows" style="display: table-row-group;">
												<?php
												$postdata = array();
												if ( $freelancer_current_project_query->have_posts() ) {
												while ( $freelancer_current_project_query->have_posts() ) {
													$freelancer_current_project_query->the_post();
													$convert    = $post_object->convert( $post );
													$postdata[] = $convert;
													$bid_status = $convert->post_status;
													?>

                                                    <div class="fre-table-row">
                                                        <div class="fre-table-col project-title-col <?php if ( $bid_status == 'archive' )
															echo 'project-title-archive' ?>">
															<?php if ( $bid_status != 'archive' ) { ?>
                                                                <a  class="secondary-color" href="<?php echo $convert->project_link; ?>"><?php echo $convert->project_title; ?></a>
															<?php } else {
																echo $convert->project_title;
															} ?>
                                                            <span class="hidden latlng"><?php echo $convert->job_latlng; ?></span>
                                                        </div>

                                                        <div class="fre-table-col project-budget-col">
                                                            <span><?php _e( 'Budget', ET_DOMAIN ); ?></span><?php echo $convert->bid_budget; ?>
                                                        </div>

                                                        <div class="fre-table-col project-open-col">
                                                            <span><?php _e( 'Open on', ET_DOMAIN ); ?></span><?php echo $convert->post_date; ?>
                                                        </div>

                                                        <div class="fre-table-col project-status-col"><?php echo $convert->project_status_view; ?></div>
                                                        <div class="fre-table-col project-address-col"><?php echo $convert->job_address; ?></div>
                                                        <div class="fre-table-col project-city-col"><?php echo $convert->job_city; ?></div>

                                                        <div class="fre-table-col project-action-col">
															<?php
															if ( $bid_status == 'accept' ) {
																echo '<a href="' . add_query_arg( array( 'workspace' => 1 ), $convert->project_link ) . '" target="_blank">' . __( 'Workspace', ET_DOMAIN ) . '</a>';
															} else if ( $bid_status == 'unaccept' ) {
																echo '<p><i>';
																_e( 'Your bid is not accepted', ET_DOMAIN );
																echo '</i></p>';
															} else if ( $bid_status == 'publish' ) {
																echo '<a class="bid-action" data-action="cancel" data-bid-id="' . $convert->ID . '">' . __( 'Cancel Bid', ET_DOMAIN ) . '</a>';
															} else if ( $bid_status == 'disputing' or $bid_status == "disputed" ) {
																echo '<a href="' . add_query_arg( array( 'dispute' => 1 ), $convert->project_link ) . '" target="_blank">' . __( 'Dispute Page', ET_DOMAIN ) . '</a>';
															} else if ( $bid_status == 'archive' ) {
																echo '<a class="bid-action" data-action="remove" data-bid-id="' . $convert->ID . '">' . __( 'Remove', ET_DOMAIN ) . '</a>';
															}
															?>
                                                        </div>
                                                    </div>

                                                    <div class="fre-table-head">
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"><?php echo __( "Progress", ET_DOMAIN ); ?></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                    </div>



                                                    <div class="fre-table-row">
                                                    <?php
                                                    // show milestones
                                                    $milestones = array(
                                                        'post_type'      => 'ae_milestone',
                                                        'post_status'    => 'any',
                                                        'post_parent'    => $convert->post_parent,
                                                        'orderby'        => 'meta_value',
                                                        'order'          => 'ASC',
                                                        'meta_key'       => 'position_order'
                                                    );

                                                    $query = new WP_Query( $milestones );

                                                    if ( $query->have_posts() ) {
                                                        $mid = 0;
                                                        while ($query->have_posts() ) {
                                                            $mid ++;
                                                            $query->the_post();
                                                            $milestone_object = $ae_post_factory->get( 'ae_milestone' );
                                                            $milestone = $milestone_object->convert( $post ); ?>
                                                            <div class="fre-table-col">
                                                                <div>
                                                                <?php if ( $bid_status == 'accept' ) { ?>
                                                                <a href="#" class="job_milestone<?php echo $mid; ?>" id="<?php the_ID(); ?>" data-project-id="<?php echo $convert->project_id; ?>" data-toggle="modal" data-target="#modal_milestone<?php echo $mid; ?>"><?php the_title(); ?></a>
                                                                <?php } else { ?>
                                                                    <?php the_title(); ?>
                                                                <?php } ?>
                                                            </div>
                                                                <div>
                                                                <?php echo ($milestone->status_label == "Open")?"Not Completed":$milestone->status_label; ?>
                                                                </div>
                                                            </div>
                                                        <?php }
                                                    } else { ?>
                                                        <div class="fre-table-col"><?php echo __( "No milestones", ET_DOMAIN ); ?></div>
                                                    <?php } ?>

                                                    <?php

                                                    // Attachment file in workspace
                                                    $attachment_comments = get_comments( array(
                                                        'post_id'    => $convert->project_id,
                                                        'meta_query' => array(
                                                            array(
                                                                'key'     => 'fre_comment_file',
                                                                'value'   => '',
                                                                'compare' => '!='
                                                            )
                                                        )
                                                    ) );
                                                    $attachments         = array();
                                                    foreach ( $attachment_comments as $key => $value ) {
                                                        $file_arr = get_comment_meta( $value->comment_ID, 'fre_comment_file', true );
                                                        if ( is_array( $file_arr ) ) {
                                                            $attachment  = get_posts( array(
                                                                'post_type' => 'attachment',
                                                                'post__in'  => $file_arr
                                                            ) );
                                                            $attachments = wp_parse_args( $attachments, $attachment );
                                                        }
                                                    }
                                                    $attachments = array_reverse( $attachments );
                                                    $lock_class  = '';
                                                    if ( empty( $attachments ) ) {
                                                        $lock_class = 'lock-btn-disabled';
                                                    }

                                                    ?>

                                                    <ul class="hidden" id="upload<?php echo $convert->project_id; ?>">
                                                    <span class="project_id" data-project="<?php echo $convert->project_id; ?>"></span>
                                                    <?php

                                                    if ( ! empty( $attachments ) ) {
                                                        foreach ( $attachments as $key => $value ) {
                                                            $comment_file_id = get_post_meta( $value->ID, 'comment_file_id', true ); ?>
                                                            <li class="attachment-<?php echo $value->ID; ?>">
                                                                <p><?php echo $value->post_title ?>
                                                                    <span>
                                                                <?php
                                                                if ( $value->post_author == $user_ID && ! $value->post_parent && ( fre_share_role() || ae_user_role() == FREELANCER ) ) {
                                                                    echo '<a href="' . $value->guid . '" target="_blank"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>';
                                                                    //if ( $lock_file != 'lock' ) {
                                                                        echo '<a href="#" data-post-id="' . $value->ID . '" data-project-id="' . $convert->project_id . '" data-file-name="' . $value->post_title . '" class="delete-attach-file"><i class="fa fa-times" aria-hidden="true" data-post-id="' . $value->ID . '" data-project-id="' . $convert->project_id . '" data-file-name="' . $value->post_title . '"></i></a>';
                                                                    //}
                                                                } else {
                                                                    echo '<a href="' . $value->guid . '" target="_blank"><i class="fa fa-cloud-download" aria-hidden="true"></i></a>';
                                                                }
                                                                ?>
                                                            </span>
                                                                </p>
                                                                <span><?php echo get_the_date( 'F j, Y g:i A', $value->ID ); ?></span>
                                                            </li>
                                                        <?php }
                                                    } else {
                                                        _e( '<li class="no_file_upload"><i>No files have been uploaded.</i></li>', ET_DOMAIN );
                                                    } ?>
                                                    </ul>

                                                    </div>

												<?php } ?>
                                                    <script type="data/json"
                                                            id="current_project_post_data"><?php echo json_encode( $postdata ); ?></script>
												<?php } else {
													$no_result_current = NO_RESULT;
												}
												?>
                                            </div>
                                        </div>
										<?php
										if ( $no_result_current != '' ) {
											echo $no_result_current;
										}
										?>
                                    </div>
                                </div>
                                <div class="fre-paginations paginations-wrapper">
                                    <div class="paginations">
										<?php
										ae_pagination( $freelancer_current_project_query, get_query_var( 'paged' ) ); ?>
                                    </div>
                                </div>



                                <div class="modal fade in" id="modal_milestone1">

                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">

                                                <ul class="fre-tabs" style="float: left; margin-bottom: 0px;">
                                                    <li class="active"><a data-toggle="tab" href="#rfi"><span>RFI</span></a>
                                                    </li>
                                                    <li class="next"><a data-toggle="tab" href="#checked"><span>DOCUMENTS CHECKED</span></a>
                                                    </li>
                                                </ul>

                                                <button type="button" class="close" data-dismiss="modal">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>

                                            <div class="modal-body fre-panel-tab active" id="rfi">
                                                <form role="form" id="milestone1_msg_form" class="fre-modal-form" action="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>" method="post">
                                                    <div class="fre-input-field">
                                                        <label>Request for information and require permit request to be re-submitted</label>
                                                        <input type="hidden" class="comment_post_ID" name="comment_post_ID" value="">
                                                        <input type="hidden" name="method" value="create"/>
                                                        <input type="hidden" name="action" value="ae-sync-message"/>
                                                        <input type="hidden" name="miilestone_type" value="1" />
                                                        <br/>
                                                        <label>List reasons:</label>
                                                        <textarea name="comment_content" id="comment_content" class="content-chat" placeholder="required" style="height: 72px;" required=""></textarea>
                                                    </div>
                                                    <div class="fre-form-btn">
                                                        <button type="submit" class="fre-normal-btn btn-submit" id="send_rfi">Send RFI</button>
                                                        <span class="fre-form-close" data-dismiss="modal">Cancel</span>
                                                    </div>

                                                </form>
                                            </div>

                                            <div class="modal-body fre-panel-tab" id="checked">
                                                <form role="form" id="milestone1_form" class="fre-modal-form" action="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>" method="post">
                                                    <div class="fre-input-field">
                                                        <label>Permitting request will be accepted by permitting department</label>
                                                    </div>
                                                    <div class="fre-form-btn">
                                                        <button type="submit" class="fre-normal-btn btn-submit" id="miilestone_complete">Accept</button>
                                                        <span>&nbsp;Date: <?php echo date_i18n( 'F j Y' ); ?></span>
                                                    </div>

                                                    <input type="hidden" class="post_parent" name="post_parent" />
                                                    <input type="hidden" name="post_author" value="<?php echo $convert->post_author; ?>"/>
                                                    <input type="hidden" name="id" class="ms_id" />
                                                    <input type="hidden" name="ID" class="ms_id" />
                                                    <input type="hidden" name="index" class="ms_id" />
                                                    <input type="hidden" name="post_status" value="resolve"/>
                                                    <input type="hidden" name="post_type" value="ae_milestone"/>
                                                    <input type="hidden" name="post_date" value="<?php echo date_i18n( 'F j, Y' ); ?>"/>
                                                    <input type="hidden" name="do_action" value="resolve_milestone"/>
                                                    <input type="hidden" name="action" value="ae_sync_milestone"/>
                                                    <input type="hidden" name="method" value="update"/>

                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>

                                <div class="modal fade in" id="modal_milestone2">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <ul class="fre-tabs" style="float: left; margin-bottom: 0px;">
                                                    <li class="active"><a><span>Started Drawings</span></a></li>
                                                </ul>

                                                <button type="button" class="close" data-dismiss="modal">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>

                                            <div class="modal-body fre-panel-tab active">
                                                <form role="form" id="milestone2_form" class="fre-modal-form" action="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>" method="post">
                                                    <div class="fre-input-field">
                                                        <label>Drawings have been started or are not required</label>
                                                        <br/><br/>
                                                        <label>Estimated Completion Date</label>
                                                        <br/>
                                                        <input type="date" name="project_deadline" id="project_deadline" value="<?php echo date('Y-m-d'); ?>" />
                                                    </div>

                                                    <input type="hidden" class="post_parent" name="post_parent" />
                                                    <input type="hidden" name="post_author" value="<?php echo $convert->post_author; ?>"/>
                                                    <input type="hidden" name="id" class="ms_id" />
                                                    <input type="hidden" name="ID" class="ms_id" />
                                                    <input type="hidden" name="index" class="ms_id" />
                                                    <input type="hidden" name="post_status" value="resolve"/>
                                                    <input type="hidden" name="post_type" value="ae_milestone"/>
                                                    <input type="hidden" name="post_date" value="<?php echo date_i18n( 'F j, Y' ); ?>"/>
                                                    <input type="hidden" name="do_action" value="resolve_milestone"/>
                                                    <input type="hidden" name="action" value="ae_sync_milestone"/>
                                                    <input type="hidden" name="method" value="update"/>

                                                    <div class="fre-form-btn">
                                                        <button type="submit" class="fre-normal-btn btn-submit" id="btn_complete">Completed</button>
                                                        <span>&nbsp;Date: <?php echo date_i18n( 'F j Y' ); ?></span>
                                                    </div>

                                                </form>
                                            </div>

                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>

                                <div class="modal fade in" id="modal_milestone3">
                                    <div class="loading-blur loading" style="display: none;">
                                        <div class="loading-overlay" style="opacity: 0.5; background-color: rgb(255, 255, 255);"></div>
                                        <div class="fre-loading-wrap">
                                            <div class="fre-loading"></div>
                                        </div>
                                    </div>

                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <ul class="fre-tabs" style="float: left; margin-bottom: 0px;">
                                                    <li class="active"><a><span>Drawings Completed</span></a></li>
                                                </ul>

                                                <button type="button" class="close" data-dismiss="modal">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </div>

                                            <div class="modal-body fre-panel-tab active">
                                                <div class="fre-input-field">
                                                    <label>Drawings Completed - Attach the drawings and mark completed</label>
                                                </div>

                                                <!-- The file upload form used as target for the file upload widget -->
                                                <form role="form" class="fre-modal-form" id="fileupload" action="" method="POST" enctype="multipart/form-data">
                                                    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                                                    <div class="row fileupload-buttonbar">
                                                        <h4 style="float: left;">Project Files</h4>
                                                        <!-- The fileinput-button span is used to style the file input field as button -->
                                                        <span class="btn btn-success fileinput-button" style="float: right;">

                                                            <i class="glyphicon glyphicon-plus"></i>
                                                            <span>Add Drawing File...</span>
                                                            <!--input type="file" name="files[]" multiple-->
                                                            <input type="file" name="files" >
                                                        </span>
                                                    </div>
                                                    <!-- The table listing the files available for upload/download -->
                                                    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>

                                                    <input type="hidden" id="url" value="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>">
                                                    <input type="hidden" name="action" value="upload_project_files">
                                                    <input type="hidden" name="project_id" id="upload_project_id" />
                                                </form>
                                                
                                                <ul class="workspace-files-list" id="workspace_files_list" style="max-height: 300px; overflow-y: scroll;"></ul>

                                                <form role="form" id="milestone3_form" class="fre-modal-form" action="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>" method="post">
                                                    

                                                    
                                                    <input type="hidden" class="post_parent" name="post_parent" />
                                                    <input type="hidden" name="post_author" value="<?php echo $convert->post_author; ?>"/>
                                                    <input type="hidden" name="id" class="ms_id" />
                                                    <input type="hidden" name="ID" class="ms_id" />
                                                    <input type="hidden" name="index" class="ms_id" />
                                                    <input type="hidden" name="post_status" value="resolve"/>
                                                    <input type="hidden" name="post_type" value="ae_milestone"/>
                                                    <input type="hidden" name="post_date" value="<?php echo date_i18n( 'F j, Y' ); ?>"/>
                                                    <input type="hidden" name="do_action" value="resolve_milestone"/>
                                                    <input type="hidden" name="action" value="ae_sync_milestone"/>
                                                    <input type="hidden" name="method" value="update"/>

                                                    <div class="fre-form-btn">
                                                        <button type="submit" class="fre-normal-btn btn-submit" id="btn_complete3">Completed</button>
                                                        <span>&nbsp;Date: <?php echo date_i18n( 'F j Y' ); ?></span>
                                                    </div>

                                                </form>
                                            </div>

                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>

                                <div class="modal fade in" id="modal_delete_file">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                                <h4 class="modal-title">Delete the item</h4>
                                            </div>
                                            <div class="modal-body">
                                                <form action="<?php echo site_url() . '/wp-admin/admin-ajax.php'; ?>" role="form" id="form-delete-file" class="form-delete-file fre-modal-form">
                                                    <div class="fre-content-confirm">
                                                        <h2>Are your sure you want to delete this item?</h2>
                                                        <p>Once the item is deleted, it will be permanently removed from the site and its information won't be recovered.</p>
                                                    </div>
                                                    <input type="hidden" name="post_id" id="post_id">
                                                    <input type="hidden" name="file_name" id="file_name">
                                                    <input type="hidden" name="project_id" id="project_id">
                                                    <input type="hidden" name="action" value="free_remove_attack_file">
                                                    <div class="fre-form-btn">
                                                        <input class="fre-normal-btn btn-submit" type="submit" value="Confirm">
                                                        <span class="fre-form-close" data-dismiss="modal">Cancel</span>
                                                    </div>
                                                </form>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div>


								<?php
								wp_reset_postdata();
								wp_reset_query();
								?>
                            </div>
                            <div id="previous-project-tab" class="freelancer-previous-project-tab fre-panel-tab">
								<?php
								$is_author   = is_author();
								$post_parent = array();
								$result      = $wpdb->get_col( "SELECT * FROM $wpdb->posts WHERE 1=1 AND post_type = 'project' AND post_status IN ( 'complete', 'disputed' )" );
								if ( ! empty( $result ) ) {
									$post_parent = $result;
								}
								$freelancer_previous_project_query = new WP_Query( array(
									'post_status'      => array( 'complete', 'disputed' ),
									'post_type'        => BID,
									'author'           => $current_user->ID,
									'accepted'         => 1,
									'is_author'        => $is_author,
									'suppress_filters' => true,
									'orderby'          => 'date',
									'order'            => 'DESC'
								) );
								$post_object                       = $ae_post_factory->get( BID );
								$no_result_previous                = '';
								?>
                                <div class="fre-work-project-box">
                                    <div class="work-project-filter">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                                                        <input type="text" class="search" name="s"
                                                               placeholder="<?php _e( 'Search projects by keywords', ET_DOMAIN ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Status', ET_DOMAIN ); ?></label>
                                                        <select class="fre-chosen-single" name="bid_previous_status">
                                                            <option value=""><?php _e( 'All', ET_DOMAIN ); ?></option>
                                                            <option value="complete"><?php _e( 'Completed', ET_DOMAIN ); ?></option>
                                                            <option value="disputed"><?php _e( 'Resolved', ET_DOMAIN ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="clear-filter work-project-filter-clear secondary-color"
                                               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
                                        </form>
                                    </div>
                                </div>
                                <div class="fre-work-project-box">
                                    <div class="previous-freelance-project">
                                        <div class="fre-table">
                                            <div class="fre-table-head">
                                                <div class="fre-table-col project-title-col"><?php _e( 'Project Title', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-start-col"><?php _e( 'Start Date', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-status-col"><?php _e( 'Status', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-review-col"><?php _e( 'Review', ET_DOMAIN ); ?></div>
                                            </div>
                                            <div class="fre-previous-table-rows" style="display: table-row-group;">
												<?php
												$postdata = array();
												if ( $freelancer_previous_project_query->have_posts() ) {
												while ( $freelancer_previous_project_query->have_posts() ) {
													$freelancer_previous_project_query->the_post();
													$convert    = $post_object->convert( $post, 'thumbnail' );
													$postdata[] = $convert;
													?>

                                                    <div class="fre-table-row">
                                                        <div class="fre-table-col project-title-col">
                                                            <a  class="secondary-color" href="<?php echo $convert->project_link; ?>"><?php echo $convert->project_title; ?></a>
                                                            <span class="hidden latlng"><?php echo $convert->job_latlng; ?></span>
                                                        </div>
                                                        <div class="fre-table-col project-start-col"><?php echo $convert->project_post_date; ?></div>
                                                        <div class="fre-table-col project-status-col"><?php echo $convert->project_status_view; ?></div>
                                                        <div class="fre-table-col project-review-col">
															<?php if ( isset( $convert->win_disputed ) && $convert->win_disputed != '' ) {
																if ( $convert->win_disputed == FREELANCER ) {
																	echo '<i>';
																	_e( 'Won dispute', ET_DOMAIN );
																	echo '</i>';
																} else {
																	echo '<i>';
																	_e( 'Lost dispute', ET_DOMAIN );
																	echo '</i>';
																}
															} else { ?>
                                                                <span class="rate-it"
                                                                      data-score="<?php echo $convert->rating_score; ?>"></span>
																<?php if ( isset( $convert->project_comment ) && ! empty( $convert->project_comment ) ) { ?>
                                                                    <p><?php echo $convert->project_comment; ?></p>
																<?php }
															} ?>

                                                        </div>
                                                    </div>
												<?php } ?>
                                                    <script type="data/json"
                                                            id="previous_project_post_data"><?php echo json_encode( $postdata ); ?></script>
												<?php } else {
													$no_result_previous = NO_RESULT;
												}
												?>
                                            </div>
                                        </div>
										<?php
										if ( $no_result_previous != '' ) {
											echo $no_result_previous;
										}
										?>
                                    </div>
                                </div>
                                <div class="fre-paginations paginations-wrapper">
                                    <div class="paginations">
										<?php
										ae_pagination( $freelancer_previous_project_query, get_query_var( 'paged' ), 'page' );
										?>
                                    </div>
                                </div>
								<?php
								wp_reset_postdata();
								wp_reset_query();
								?>
                            </div>
						<?php } else { ?>
                            <div id="current-project-tab" class="employer-current-project-tab fre-panel-tab active">
								<?php
								$employer_current_project_query = new WP_Query(
									array(
										'post_status'      => array(
											'close',
											'disputing',
											'publish',
											'pending',
											'draft',
											'reject',
											'archive'
										),
										'is_author'        => true,
										'post_type'        => PROJECT,
										'author'           => $user_ID,
										'suppress_filters' => true,
										'orderby'          => 'date',
										'order'            => 'DESC'
									)
								);

								$post_object       = $ae_post_factory->get( PROJECT );
								$no_result_current = '';
								?>
                                <div class="fre-work-project-box">
                                    <div class="work-project-filter">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                                                        <input type="text" class="search" name="s"
                                                               placeholder="<?php _e( 'Search projects by keywords', ET_DOMAIN ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Status', ET_DOMAIN ); ?></label>
                                                        <select class="fre-chosen-single" name="project_current_status">
                                                            <option value=""><?php _e( 'All', ET_DOMAIN ); ?></option>
                                                            <option value="close"><?php _e( 'Processing', ET_DOMAIN ); ?></option>
                                                            <option value="disputing"><?php _e( 'Disputed', ET_DOMAIN ); ?></option>
                                                            <option value="publish"><?php _e( 'Active', ET_DOMAIN ); ?></option>
                                                            <option value="pending"><?php _e( 'Pending', ET_DOMAIN ); ?></option>
                                                            <option value="draft"><?php _e( 'Draft', ET_DOMAIN ); ?></option>
                                                            <option value="reject"><?php _e( 'Rejected', ET_DOMAIN ); ?></option>
                                                            <option value="archive"><?php _e( 'Archived', ET_DOMAIN ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="clear-filter work-project-filter-clear secondary-color"
                                               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
                                        </form>
                                    </div>
                                </div>
                                <div class="fre-work-project-box">
                                    <div class="current-employer-project">
                                        <div class="fre-table">
                                            <div class="fre-table-head">
                                                <div class="fre-table-col project-title-col"><?php _e( 'Project Title', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-budget-col"><?php _e( 'Budget', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-open-col"><?php _e( 'Open Date', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-status-col"><?php _e( 'Status', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-address-col"><?php _e( 'Address', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-city-col"><?php _e( 'City', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-action-col"><?php _e( 'Action', ET_DOMAIN ); ?></div>
                                            </div>
                                            <div class="fre-current-table-rows" style="display: table-row-group;">
												<?php

												if ( $employer_current_project_query->have_posts() ) {
													$postdata = array();
												while ( $employer_current_project_query->have_posts() ) {
													$employer_current_project_query->the_post();
													$convert        = $post_object->convert( $post, 'thumbnail' );
													$postdata[]     = $convert;
													$project_status = $convert->post_status;
													?>
                                                    <div class="fre-table-row">
                                                        <div class="fre-table-col project-title-col">
                                                            <a  class="secondary-color" href="<?php echo $convert->permalink; ?>"><?php echo $convert->post_title; ?></a>
                                                            <span class="hidden latlng"><?php echo $convert->job_latlng; ?></span>
                                                        </div>
                                                        <div class="fre-table-col project-budget-col">
                                                            <span><?php _e( 'Budget', ET_DOMAIN ); ?></span><?php echo $convert->budget; ?>
                                                        </div>
                                                        <div class="fre-table-col project-open-col">
                                                            <span><?php _e( 'Open on', ET_DOMAIN ); ?></span><?php echo $convert->post_date; ?>
                                                        </div>
                                                        <div class="fre-table-col project-status-col"><?php echo $convert->project_status_view; ?></div>
                                                        <div class="fre-table-col project-address-col"><?php echo $convert->job_address; ?></div>
                                                        <div class="fre-table-col project-city-col"><?php echo $convert->job_city; ?></div>
														<?php
														if ( $project_status == 'close' ) {
															echo '<div class="fre-table-col project-action-col">';
															echo '<a href="' . add_query_arg( array( 'workspace' => 1 ), $convert->permalink ) . '" target="_blank">' . __( 'Workspace', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'disputing' ) {
															echo '<div class="fre-table-col project-action-col">';
															echo '<a href="' . add_query_arg( array( 'dispute' => 1 ), $convert->permalink ) . '" target="_blank">' . __( 'Dispute Page', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'publish' ) {
															echo '<div class="fre-table-col project-action-col">';
															echo '<a class="project-action" data-action="archive" data-project-id="' . $convert->ID . '">' . __( 'Archive', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'pending' ) {
															echo '<div class="fre-table-col project-action-col">';
															echo '<a href="' . et_get_page_link( 'edit-project', array( 'id' => $convert->ID ) ) . '" target="_blank">' . __( 'Edit', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'draft' ) {
															echo '<div class="fre-table-col project-action-col project-action-two">';
															echo '<a href="' . et_get_page_link( 'submit-project', array( 'id' => $convert->ID ) ) . '" target="_blank">' . __( 'Edit', ET_DOMAIN ) . '</a>';
															echo '<a class="project-action" data-action="delete" data-project-id="' . $convert->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'reject' ) {
															echo '<div class="fre-table-col project-action-col">';
															echo '<a href="' . et_get_page_link( 'edit-project', array( 'id' => $convert->ID ) ) . '" target="_blank">' . __( 'Edit', ET_DOMAIN ) . '</a>';
															echo '</div>';
														} else if ( $project_status == 'archive' ) {
															echo '<div class="fre-table-col project-action-col project-action-two">';
															echo '<a href="' . et_get_page_link( 'submit-project', array( 'id' => $convert->ID ) ) . '" target="_blank">' . __( 'Renew', ET_DOMAIN ) . '</a>';
															echo '<a class="project-action" data-action="delete" data-project-id="' . $convert->ID . '">' . __( 'Delete', ET_DOMAIN ) . '</a>';
															echo '</div>';
														}
														?>
                                                    </div>

                                                    <div class="fre-table-head">
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col">
                                                            <?php echo __( "Progress", ET_DOMAIN ); ?>
                                                        </div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                        <div class="fre-table-col"></div>
                                                    </div>

                                                    <div class="fre-table-row">
                                                    <?php
                                                    // show milestones
                                                    $milestones = array(
                                                        'post_type'      => 'ae_milestone',
                                                        'post_status'    => 'any',
                                                        'post_parent'    => $convert->ID,
                                                        'orderby'        => 'meta_value',
                                                        'order'          => 'ASC',
                                                        'meta_key'       => 'position_order'
                                                    );

                                                    $query = new WP_Query( $milestones );

                                                    if ( $query->have_posts() ) {
                                                        while ($query->have_posts() ) {
                                                            $query->the_post();
                                                            $milestone_object = $ae_post_factory->get( 'ae_milestone' );
                                                            $milestone = $milestone_object->convert( $post ); ?>
                                                            <div class="fre-table-col">
                                                            <div><?php the_title(); ?></div>
                                                            <div><?php echo $milestone->status_label; ?></div>
                                                            </div>
                                                        <?php }
                                                    } else { ?>
                                                        <div class="fre-table-col"><?php echo __( "No milestones", ET_DOMAIN ); ?></div>
                                                    <?php } ?>
                                                    </div>
												<?php } ?>
                                                    <script type="data/json"
                                                            id="current_project_post_data"><?php echo json_encode( $postdata ); ?></script>
												<?php } else {
													$no_result_current = NO_RESULT;
												}
												?>
                                            </div>
                                        </div>
										<?php
										if ( $no_result_current != '' ) {
											echo $no_result_current;
										}
										?>
                                    </div>
                                </div>
                                <div class="fre-paginations paginations-wrapper">
                                    <div class="paginations">
										<?php ae_pagination( $employer_current_project_query, get_query_var( 'paged' ) ); ?>
                                    </div>
                                </div>
								<?php
								wp_reset_postdata();
								wp_reset_query();
								?>
                            </div>
                            <div id="previous-project-tab" class="employer-previous-project-tab fre-panel-tab">
								<?php
								$employer_previous_project_query = new WP_Query(
									array(
										'post_status'      => array( 'complete', 'disputed' ),
										'is_author'        => true,
										'post_type'        => PROJECT,
										'author'           => $user_ID,
										'suppress_filters' => true,
										'orderby'          => 'date',
										'order'            => 'DESC'
									)
								);
								$post_object                     = $ae_post_factory->get( PROJECT );
								$no_result_previous              = '';
								?>
                                <div class="fre-work-project-box">
                                    <div class="work-project-filter">
                                        <form>
                                            <div class="row">
                                                <div class="col-md-8 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Keyword', ET_DOMAIN ); ?></label>
                                                        <input type="text" class="search" name="s"
                                                               placeholder="<?php _e( 'Search projects by keywords', ET_DOMAIN ); ?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-4 col-sm-6 col-xs-12">
                                                    <div class="fre-input-field">
                                                        <label class="fre-field-title"><?php _e( 'Status', ET_DOMAIN ); ?></label>
                                                        <select class="fre-chosen-single"
                                                                name="project_previous_status">
                                                            <option value=""><?php _e( 'All', ET_DOMAIN ); ?></option>
                                                            <option value="complete"><?php _e( 'Completed', ET_DOMAIN ); ?></option>
                                                            <option value="disputed"><?php _e( 'Resolved', ET_DOMAIN ); ?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <a class="clear-filter work-project-filter-clear secondary-color"
                                               href=""><?php _e( 'Clear all filters', ET_DOMAIN ); ?></a>
                                        </form>
                                    </div>
                                </div>
                                <div class="fre-work-project-box">
                                    <div class="previous-employer-project">
                                        <div class="fre-table">
                                            <div class="fre-table-head">
                                                <div class="fre-table-col project-title-col"><?php _e( 'Project Title', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-start-col"><?php _e( 'Start Date', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-bid-col"><?php _e( 'Bid Won', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-status-col"><?php _e( 'Status', ET_DOMAIN ); ?></div>
                                                <div class="fre-table-col project-review-col"><?php _e( 'Review', ET_DOMAIN ); ?></div>
                                            </div>
                                            <div class="fre-previous-table-rows" style="display: table-row-group;">
												<?php
												if ( $employer_previous_project_query->have_posts() ) {
													$postdata = array();
												while ( $employer_previous_project_query->have_posts() ) {
													$employer_previous_project_query->the_post();
													$convert    = $post_object->convert( $post, 'thumbnail' );
													$postdata[] = $convert;
													?>
                                                    <div class="fre-table-row">
                                                        <div class="fre-table-col project-title-col">
                                                            <a  class="secondary-color" href="<?php echo $convert->permalink; ?>"><?php echo $convert->post_title; ?></a>
                                                            <span class="hidden latlng"><?php echo $convert->job_latlng; ?></span>
                                                        </div>
                                                        <div class="fre-table-col project-start-col"><?php echo $convert->post_date; ?></div>
                                                        <div class="fre-table-col project-bid-col">
                                                            <span><?php _e( 'Bid won:', ET_DOMAIN ); ?></span><b><?php echo $convert->bid_budget_text; ?></b><span><?php echo $convert->bid_won_date; ?></span>
                                                        </div>
                                                        <div class="fre-table-col project-status-col"><?php echo $convert->project_status_view; ?></div>
                                                        <div class="fre-table-col project-review-col">
															<?php if ( isset( $convert->win_disputed ) && $convert->win_disputed != '' ) {
																if ( $convert->win_disputed == EMPLOYER ) {
																	echo '<i>';
																	_e( 'Won dispute', ET_DOMAIN );
																	echo '</i>';
																} else {
																	echo '<i>';
																	_e( 'Lost dispute', ET_DOMAIN );
																	echo '</i>';
																}
															} else {
																if ( $convert->rating_score > 0 ) {
																	echo '<span class="rate-it" data-score="' . $convert->rating_score . '"></span>';
																} else {
																	_e( '<i>No rating & review yet.</i>', ET_DOMAIN );
																}
																if ( isset( $convert->project_comment ) && ! empty( $convert->project_comment ) ) {
																	echo '<p>' . $convert->project_comment . '</p>';
																}
															} ?>
                                                        </div>
                                                    </div>
												<?php } ?>
                                                    <script type="data/json"
                                                            id="previous_project_post_data"><?php echo json_encode( $postdata ); ?></script>
												<?php } else {
													$no_result_previous = NO_RESULT;
												}
												?>
                                            </div>
                                        </div>
										<?php
										if ( $no_result_previous != '' ) {
											echo $no_result_previous;
										}
										?>
                                    </div>
                                </div>
                                <div class="fre-paginations paginations-wrapper">
                                    <div class="paginations">
										<?php ae_pagination( $employer_previous_project_query, get_query_var( 'paged' ) ); ?>
                                    </div>
                                </div>
								<?php
								wp_reset_postdata();
								wp_reset_query();
								?>
                            </div>
						<?php } ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php get_footer(); ?>