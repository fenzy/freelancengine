<?php

header('Access-Control-Allow-Origin: *');
require_once(dirname(__FILE__) . '/../../../wp-config.php');
require_once(dirname(__FILE__) . '/widget.php');
global $wpdb, $post;
$posttype = 'post';
$nextval = true;
$spintax = new Spintax();
$data = array();
$data['email_address'] = "info@tazar.com";
$data['api_key'] = "65d8001#5e4d6e0_0845d32?e6bdddc ";
$data['action'] = "unique_variation_from_spintax";
$data['use_only_synonyms'] = "false";
$data['reorder_paragraphs'] = "false";
@ini_set('upload_max_filesize', '51200M');


$results = $wpdb->get_results("SELECT * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE titleupdate !=0 AND titleupdate IS NOT NULL AND titleupdate < '" . (strtotime('now') - 86399) . "' AND project_id IN (SELECT project_id FROM " . AUTOPOST_SCHEDULING_TABLE . " WHERE changetitle = '24') LIMIT 50");
foreach ($results as $result) {
    $project = $wpdb->get_row("SELECT * FROM " . MYFAQ_TABLE . " WHERE id = " . $result->project_id);
    if (!empty($project)) {
        $state = $wpdb->get_row("SELECT * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid = " . $result->citystateid);
        if (!empty($state)) {
            $data['text'] = str_replace('[state-code]', ucwords($state->statecode), str_replace('[town]', ucwords($state->city), str_replace('[state]', ucwords($state->state), stripslashes($project->secondtitle))));
            if (trim($data['text']) != '') {
                $data['text'] = $spintax->process($data['text']);
                 $wpdb->update(
                           $wpdb->prefix.'posts', array( 
        'post_title' =>$data['text']
                            ),array(
                                 'ID' => $result->cloneid
                            )
                    ); 
                 $wpdb->update(
                        AUTOPOST_CITYSTATEMETA_TABLE, array( 
        'titleupdate' =>'0'
                            ),array(
                                 'citystatemetaid' =>$result->citystatemetaid
                            )
                    ); 
            } 
        }
    }
}
$results = $wpdb->get_results("SELECT * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE titleupdate !=0 AND titleupdate IS NOT NULL AND titleupdate < '" . (strtotime('now') - 259199) . "' AND project_id IN (SELECT project_id FROM " . AUTOPOST_SCHEDULING_TABLE . " WHERE changetitle = '72') LIMIT 50");
foreach ($results as $result) {
    $project = $wpdb->get_row("SELECT * FROM " . MYFAQ_TABLE . " WHERE id = " . $result->project_id);
    if (!empty($project)) {
        $state = $wpdb->get_row("SELECT * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid = " . $result->citystateid);
        if (!empty($state)) {
            $data['text'] = str_replace('[state-code]', ucwords($state->statecode), str_replace('[town]', ucwords($state->city), str_replace('[state]', ucwords($state->state), stripslashes($project->secondtitle))));
            if (trim($data['text']) != '') {
                $data['text'] = $spintax->process($data['text']);
                 $wpdb->update(
                           $wpdb->prefix.'posts', array( 
        'post_title' =>$data['text']
                            ),array(
                                 'ID' => $result->cloneid
                            )
                    ); 
                  $wpdb->update(
                        AUTOPOST_CITYSTATEMETA_TABLE, array( 
        'titleupdate' =>'0'
                            ),array(
                                 'citystatemetaid' =>$result->citystatemetaid
                            )
                    ); 
            } 
        }
    }
}
$results = $wpdb->get_results("SELECT * FROM " . AUTOPOST_CITYSTATEMETA_TABLE . " WHERE titleupdate !=0 AND titleupdate IS NOT NULL AND titleupdate < '" . (strtotime('now') - 172799) . "' AND project_id IN (SELECT project_id FROM " . AUTOPOST_SCHEDULING_TABLE . " WHERE changetitle = '48') LIMIT 50");
foreach ($results as $result) {
    $project = $wpdb->get_row("SELECT * FROM " . MYFAQ_TABLE . " WHERE id = " . $result->project_id);
    if (!empty($project)) {
        $state = $wpdb->get_row("SELECT * FROM " . AUTOPOST_CITYSTATE_TABLE . " WHERE citystateid = " . $result->citystateid);
        if (!empty($state)) {
            $data['text'] = str_replace('[state-code]', ucwords($state->statecode), str_replace('[town]', ucwords($state->city), str_replace('[state]', ucwords($state->state), stripslashes($project->secondtitle))));
            if (trim($data['text']) != '') {
                $data['text'] = $spintax->process($data['text']);
                 $wpdb->update(
                           $wpdb->prefix.'posts', array( 
        'post_title' =>$data['text']
                            ),array(
                                 'ID' => $result->cloneid
                            )
                    ); 
                  $wpdb->update(
                        AUTOPOST_CITYSTATEMETA_TABLE, array( 
        'titleupdate' =>'0'
                            ),array(
                                 'citystatemetaid' =>$result->citystatemetaid
                            )
                    ); 
            } 
        }
    }
}
?> 