<?php
/*
Template Name: autopostingtemplate
*/
 wp_head(); 
   get_post($_GET['postid']);
 the_post(); 
global $post;
?> 
<style>
    #wpadminbar{
        display:none !important;
    }
    #main {
    padding: 0 !important;
}
html {
    margin-top: 0  !important;
}
.container {
    width: 100% !important;
}
.autospinrow{
    margin:0 !important;
}
#content{
    padding:0 !important;
}
.fl-builder-content .fl-row,.fl-builder-content .fl-col-group{
    display:none;
}
.fl-builder-content .fl-row.autospinline,.fl-builder-content .fl-col-group.autospingroup{
    display:block;
}
</style>
<div id="main" class="container"> 
    <div class="row autospinrow">
        <article id="content" class="col-md-12">

            <div <?php post_class(); ?>>
                <?php the_content(); ?>
            </div>

        </article>
        
    </div>
 
</div>
<script>
    var class_ = '.fl-node-<?php if(isset($_GET['spinid'])){ echo $_GET['spinid']; }else{echo  'bend'; }  ?>';
    jQuery(document).ready(function(){
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
    });
</script>
<?php wp_footer();
 