<?php



/*
Plugin Name:  Post Complete Tracker
Plugin URI:   https://www.webklient.cz/pluginy
Description:  Adds a "Complete" button after the post content for logged users. After clicking change to "Completed". 
Version:      1.0
Author:       Webklient.cz
Author URI:   https://www.webklient.cz
License:      GPL2
License URI:  https://www.gnu.org/licenses/gpl-2.0.html
Text Domain:  post-complete-tracker
*/

function post_completion_tracker_button($content) {
    global $post;
    if (is_single() && is_user_logged_in()) {
        $completed = get_post_meta($post->ID, 'completed', true);
        if ($completed) {
            $content .= '<button id="post-complete-button-' . $post->ID . '" onclick="postComplete(' . $post->ID . ')" disabled>Completed</button>';
        } else {
            $content .= '<button id="post-complete-button-' . $post->ID . '" onclick="postComplete(' . $post->ID . ')">Complete?</button>';
        }
    }
    return $content;
}

add_filter('the_content', 'post_completion_tracker_button');

function post_completion_tracker_script() {
    if (is_user_logged_in()) {
        ?>
        <script>
        function postComplete(post_id) {
            jQuery.post(
                '<?php echo admin_url('admin-ajax.php'); ?>',
                {
                    action: 'post_complete',
                    post_id: post_id
                },
                function(response) {
                    jQuery('#post-complete-button-' + post_id).text('Completed');
                }
            );
        }
        </script>
        <?php
    }
}
add_action('wp_footer', 'post_completion_tracker_script');

function post_complete_ajax_handler() {
    if (isset($_POST['post_id'])) {
        $user_id = get_current_user_id();
        $post_id = intval($_POST['post_id']);
        $time = time();
        add_user_meta( $user_id, 'completed_post', array( 'post_id' => $post_id, 'timestamp' => $time ) );
    }
    die();
}

add_action('wp_ajax_post_complete', 'post_complete_ajax_handler');
add_action('wp_ajax_nopriv_post_complete', 'post_complete_ajax_handler');


?>