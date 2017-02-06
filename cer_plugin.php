<?php
/**
 * Plugin Name: Comment Email Reply
 * Plugin URI: http://kilozwo.de/wordpress-comment-email-reply-plugin
 * Description: Simply notifies comment-author via email if someone replies to his comment. Zero Configuration. Available in English and German. More languages welcome.
 * Version: 1.0.5
 */

add_filter('wp_mail_content_type', function($contentType) { return 'text/html'; });
function cer_comment_notification($comment_id) {
    $comment_object = get_comment($comment_id);
	if ($comment_object->comment_approved == 1 && $comment_object->comment_parent > 0) {
        $comment_parent = get_comment($comment_object->comment_parent);

        $mailcontent =
				'<html>
				<head>
				<style>
				#mgy{margin:0;padding:0;font-size:16px;font-size:1rem;line-height:1.8;color:#444}
				#mgy .a{width:100%}
				#mgy .b{width:520px;margin:0 auto;padding:20px}
				#mgy .c{clear:both}
				#mgy .d,#mgy .n{margin:0 0 24px}
				#mgy .e{border-top:1px solid #DDD}
				#mgy .f{margin-bottom:10px}
				#mgy .g{margin:0 5px 0 0}
				#mgy .h{width:100px;height:32px;line-height:32px;padding:2px;border-radius:30px;color:#FFF;background:#52C69C;box-shadow:1px 1px 0 #DDD}
				#mgy .h:hover{background:#57AD68}
				#mgy .j{margin:0 6px;border-bottom:1px dotted;text-decoration:none}
				#mgy .k{text-align:center;text-decoration:none}
				#mgy .l{display:block}
				#mgy .m{color:#57AD68}
				#mgy .o{margin:5px 0}
				#mgy .p{color:#5A5A5A}
				#mgy .q{background:#F0F0F0;border-radius:4px;padding:8px;line-height:1.5rem;line-height:24px}
				#mgy .s{color:#2458A1}
				@media screen and (max-width:720px) {
					#mgy{font-size:18px;font-size:1.125rem;line-height:2}
					#mgy .b{width:100%;-moz-box-sizing:border-box;-webkit-box-sizing:border-box;box-sizing:border-box}
				}
				</style>
				</head>
				<body>
				<div id="mgy">
				<div class="a"><div class="b"><div class="o"><span class="g">'
				.$comment_parent->comment_author.
				':</span>你好</div>'.
				'<div class="f"><span class="g s">'.$comment_object->comment_author.'</span>在'.
                '<a class="j" href="'.get_permalink($comment_parent->comment_post_ID).'">'.get_the_title($comment_parent->comment_post_ID).'</a>中 <span class="f">对你发表的评论:</span></div>'.
                '<div class="f q">'. esc_html($comment_parent->comment_content) .'</div>'.
				'<div class="f">作了如下回复:</div><div class="d s q">'
				. esc_html($comment_object->comment_content) .
				'</div>'.
				'<a class="d h k l" href="'.get_comment_link( $comment_parent->comment_ID ).'">继续回复</a>'.
				'<div class="f">感谢支持，谢谢！</div>'.'<div class="e"><span class="p">MALI<span class="m">YA</span>NA</span></div></div></div></div>
				</body></html>';
		$email = $comment_parent->comment_author_email;
		$title ='来自 ['.get_option('blogname') . '] 的评论回复';
		wp_mail($email, $title, $mailcontent);
    }
}
add_action('comment_mail_notify','cer_comment_notification');

function comment_mail_notify_schedule($comment_id) {
	wp_schedule_single_event( time()+5, 'comment_mail_notify', array($comment_id));
}
add_action('wp_insert_comment','comment_mail_notify_schedule',99,2);

function cer_comment_status_changed($comment_id, $comment_status) {
	$comment_object = get_comment($comment_id);
	if ($comment_status == 'approve') {
		cer_comment_notification($comment_object->comment_ID, $comment_object);
	}
}
# Fire Email when comments gets approved later.
add_action('wp_set_comment_status','cer_comment_status_changed',99,2);
?>
