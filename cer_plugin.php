<?php
/**
 * Plugin Name: Comment Email Reply
 * Plugin URI:  http://kilozwo.de/wordpress-comment-email-reply-plugin
 * Description: Simply notifies comment-author via email if someone replies to his comment. Zero Configuration. Available in English and German. More languages welcome.
 * Version:     1.0.4
 */

load_plugin_textdomain('cer_plugin', false, basename( dirname( __FILE__ ) ) . '/languages' );

# Fire Email when comments is inserted and is already approved.
add_action('wp_insert_comment','cer_comment_notification',99,2);
add_filter('wp_mail_content_type', function($contentType) { return 'text/html'; });


function cer_comment_notification($commentId, $comment) {
    if ($comment->comment_approved == 1 && $comment->comment_parent > 0) {
        $parent = get_comment($comment->comment_parent);

        $mailcontent =
				'<style type="text/css">
				#mgy{margin:0;padding:0;font-size:16px;line-height:1.8;color:#444}
				#mgy .a{width:100%}
				#mgy .b{width:520px;margin:0 auto;padding:20px}
				#mgy .c{clear:both}
				#mgy .d,#mgy .n{margin:0 0 24px}
				#mgy .e{border-top:1px solid #DDD}
				#mgy .f{margin-bottom:10px}
				#mgy .g{margin:0 5px 0 0}
				#mgy .h{width:140px;height:32px;line-height:32px;padding:2px;border-radius:30px;color:#FFF;background:#52C69C;box-shadow:1px 1px 0 #DDD}
				#mgy .h:hover{background:#57AD68}
				#mgy .j{margin:0 6px}
				#mgy .k{text-align:center;text-decoration:none}
				#mgy .l{display:block}
				#mgy .m{color:#57AD68}
				#mgy .o{margin:5px 0}
				#mgy .p{color:#5A5A5A}
				#mgy .q{background:#F1F1F1;border-radius:4px;padding:2px 8px}
				#mgy .s{color:#2458A1}
				@media screen and (max-width: 620px) {
					#mgy{font-size:16px;line-height:2}
					#mgy .b{width:100%;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}
				}
				</style>
				<div id="mgy">
				<div class="a"><div class="b"><div class="o"><span class="g">'
				.$parent->comment_author.
				':</span>你好</div>'.
				'<div class="f"><span class="g s">'.$comment->comment_author.'</span>在'.
                '<a class="j" href="'.get_permalink($parent->comment_post_ID).'">'.get_the_title($parent->comment_post_ID).'</a>中 <span class="f">对你发表的评论:</span></div>'.
                '<div class="f q">'. esc_html($parent->comment_content) .'</div>'.
				'<div class="f">作了如下回复:</div><div class="d s q">'
				. esc_html($comment->comment_content) .
				'</div>'.
				'<a class="d h k l" href="'.get_comment_link( $parent->comment_ID ).'">继续回复他/她</a>'.
				'<div class="f">感谢支持，谢谢！</div>'.'<div class="e"><span class="p">A<span class="m">A</span>A</span></div></div></div></div>';
		$email = $parent->comment_author_email;
		$title ='来自 ['.get_option('blogname') . '] 的评论回复';
		wp_mail($email, $title, $mailcontent);
    }
}

# Fire Email when comments gets approved later.
add_action('wp_set_comment_status','cer_comment_status_changed',99,2);

function cer_comment_status_changed($commentId, $comment_status) {
    $comment = get_comment($commentId);
    if ($comment_status == 'approve') {
        cer_comment_notification($comment->comment_id, $comment);        
    } 
}
?>
