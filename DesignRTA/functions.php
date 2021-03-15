<?php
// change project status waiting to complete
// change project status waiting to complete
function custom_update_post() {

    $post_id = $_POST['post_id'];
    update_post_meta($post_id, '_upstream_project_status', 'azp39');
    // $post = array(
    //     'post_modified'  => date(),
    //     'post_modified_gmt'   => date(),
    //     'ID'          => $post_id, // $post->ID;
    // );
    // // update post
    // wp_update_post($post);


    $project_title = get_the_title($post_id);
    $current_user_id = get_current_user_id();
    $user_meta = get_userdata($current_user_id);
    $project_client_data = get_user_by('id', $current_user_id);

    // send an email for client
    $client_id = get_post_meta($post_id, '_upstream_project_manager', true);
    $manager_data = get_users(['role__in' => ['upstream_manager']]);
    $manager_email[] = '';
    $manager_number[] = '0000000000';
    foreach ($manager_data as $manager) {
        $managerId = $manager->ID;
        $manager_data = get_user_by('id', $managerId);
        $manager_email[] = $manager_data->user_email;
        $manager_number[] = $manager_data->phone_number;
    }
    $magnager_emails = implode(", ", $manager_email);
    $manager_numbers = implode(", ", $manager_number);

    $user_data = get_user_by('id', $client_id[0]);

    if (function_exists('twl_send_sms')) {
        $args = array(
            'number_to' => $manager_numbers,
            'message' => $project_title . " is approved",
        );
        twl_send_sms($args);
    }


    $to = $magnager_emails;
    $subject = $project_title . " is approved";
    $body = 'Your Project is approved by client Email=' . $project_client_data->user_email;
    //$headers = array('Content-Type: text/html; charset=UTF-8', 'From: DesignRTA &lt;requests@designrta.com');
    $headers[] = 'Content-Type: text/html; charset=UTF-8';
    $headers = "From: DesignRTA <$user_data->user_email>" . "\r\n";

    wp_mail($to, $subject, $body, $headers);

    wp_die();
}

add_action('wp_ajax_custom_update_post', 'custom_update_post');
add_action('wp_ajax_nopriv_custom_update_post', 'custom_update_post');


function custom_meta_values() {
    $meta = array(
        array(
            'key' => 'gpaz9',
            'name' => 'Design In Progress'
        ),
        array(
            'key' => '6ea5e',
            'name' => 'Ready For Approval'
        ),
        array(
            'key' => 'azp39',
            'name' => 'Completed'
        ),
        array(
            'key' => 'wfyaa',
            'name' => 'New Comment'
        ),
        array(
            'key' => 'cerwk',
            'name' => 'Revision Requested'
        ),
    );
    return $meta;
}
function filter_datewise_projects() {
    $filtered_project = array();

    $meta_values = custom_meta_values();
    foreach ($meta_values as $value) {
        $projectPost = get_posts(array(
            'post_type' => 'project',
            'posts_per_page' => -1,
            'meta_key' => '_upstream_project_status',
            'meta_value' => $value['key'],
        ));
        $filtered_project[] = array(
            'project_status' => $value['name'],
            'project_count' => count($projectPost)
        );
    }
    return $filtered_project;
}

function get_reply_comments($parent_comment_id, $file_value) {
    $child_comments = get_comments(
        array(
            'parent' => $parent_comment_id
        )
    );
?>
    <?php if ($child_comments) {
        foreach ($child_comments as $comment) {
    ?>
            <div class="o-comment s-status-approved" id="comment-<?php echo $comment->comment_ID ?>" data-id="<?php echo $comment->comment_ID ?>">
                <div class="o-comment__body">
                    <div class="o-comment__body__left">
                        <img class="o-comment__user_photo" src="http://2.gravatar.com/avatar/29618afe0a56b675c5d1de92d8783806?s=96&amp;d=mm&amp;r=g" width="30">
                    </div>
                    <div class="o-comment__body__right">
                        <div class="o-comment__body__head">
                            <div class="o-comment__user_name"><?php echo $comment->comment_author ?></div>
                            <div class="o-comment__reply_info"></div>
                            <div class="o-comment__date" data-toggle="tooltip" title="" data-original-title="<?php echo date_format(date_create($comment->comment_date), "F d, Y h:i a") ?>">16 hours ago</div>
                        </div>
                        <div class="o-comment__content">
                            <p><?php echo str_replace('&nbsp;', '', preg_replace('/(.zip|.jpg|.png|.jpeg|.csv)/', '', $comment->comment_content)) ?></p>
                        </div>
                        <div class="o-comment__body__footer">
                            <a data-item-id="<?php echo $file_value['id'] ?>" data-project-id="<?php echo get_the_ID() ?>" data-toggle="modal" data-target="#modal-reply_comment" href="#" class="o-comment-control project_file_comment_reply" data-action="comment.reply" data-nonce="ea14e753ee">
                                <i class="fa fa-reply"></i>&nbsp;
                                Reply
                            </a>

                            <!-- Upload Button -->
                            <a data-item-id="<?php echo $file_value['id'] ?>" data-file_id="<?php echo $file_value['file_id'] ?>" data-project-id="<?php echo get_the_ID() ?>" data-comment_id="<?php echo $comment->comment_ID ?>" href="#" class="comment_revision_file_upload">
                                <i class="fas fa-upload"></i>&nbsp;
                                Upload
                            </a>
                            <!-- End of Upload Button -->

                            <!-- View revison files button -->
                            <a data-item-id="<?php echo $file_value['id'] ?>" data-project-id="<?php echo get_the_ID() ?>" data-comment_id="<?php echo $comment->comment_ID ?>" data-toggle="modal" data-target="#modal-revision_store" href="#" class="revision_files">
                                <i class="fas fa-file-alt"></i>&nbsp;
                                Revisions
                                (<b class="revision_file_count"><?php echo get_revison_count(get_the_ID(), $comment->comment_ID) ?></b>)
                            </a>
                            <!-- End View revison files button  -->
                        </div>
                    </div>
                </div>

                <div class="o-comment-replies">
                    <?php get_reply_comments($comment->comment_ID, $file_value) ?>
                </div>
            </div>
        <?php } ?>

    <?php } ?>
<?php
}


add_action('wp_ajax_create_zip_file_for_selected', 'create_zip_file_for_selected');
add_action('wp_ajax_nopriv_create_zip_file_for_selected', 'create_zip_file_for_selected');

function create_zip_file_for_selected() {

    $file_name = time() . '_all-files-downloads.zip';

    # define file array

    $seleted_files = $_POST['files'];

    if (empty($seleted_files)) {
        echo 'NO FILES';
        die();
    }

    # create new zip object
    $zip = new ZipArchive();

    # create a temp file & open it

    $zip->open($file_name, ZipArchive::CREATE);

    # loop through each file
    foreach ($seleted_files as $file) {
        # download file
        $download_file = file_get_contents($file);

        #add it to the zip
        $zip->addFromString(basename($file), $download_file);
    }

    # close zip
    $zip->close();

    update_option('designrta_last_download_file', $file_name);

    echo admin_url($file_name);
    die();
}

add_action('wp_ajax_save_discussion_revison', 'save_discussion_revison');
add_action('wp_ajax_nopriv_save_discussion_revison', 'save_discussion_revison');

function save_discussion_revison() {

    if ($_POST['action'] != 'save_discussion_revison') {
        echo json_encode([
            'response' => 'invalid_action',
        ]);
        wp_die();
    }

    extract($_POST['attachment_obj']);

    if (!isset($attachment_id) or !isset($post_id) or !isset($comment_id)) {
        echo json_encode([
            'response' => 'missing_parameter',
        ]);
        wp_die();
    }

    $get_meta = get_post_meta(intval($post_id), '_revision_file');

    if ($get_meta) {
        foreach ($get_meta as $key => $meta) {
            if ($meta['attachment_id'] == $attachment_id && $comment_id == $meta['comment_id']) {
                echo json_encode([
                    'response' => 'file_exists',
                ]);
                wp_die();
            }
        }
    }

    $meta_value = [
        'attachment_id' => $attachment_id,
        'upload_date' => $upload_date,
        'uploader_name' => $uploader_name,
        'attachment_filename' => $attachment_filename,
        'attachment_url' => $attachment_url,
        'post_id' => $post_id,
        'comment_id' => $comment_id,
        'file_id' => $file_id
    ];

    $add_meta = add_post_meta(intval($post_id), '_revision_file',  $meta_value);


    if ($add_meta) {
        if (send_cross_client_and_designer_email(intval($post_id))) {
            echo json_encode([
                'response' => 'success',
                'msg' => 'mail_sent'
            ]);
            wp_die();
        };
    } else {
        echo json_encode([
            'response' => 'success',
            'msg' => 'mail_sending_failed'
        ]);
        wp_die();
    }

    wp_die();
}

function send_cross_client_and_designer_email($post_id) {
    if (in_array('upstream_client_user', wp_get_current_user()->roles)) {
        $designer_emails = [];
        $designer_array = get_users([
            'role' => 'upstream_manager'
        ]);
        if ($designer_array) {
            foreach ($designer_array as $key => $designer) {
                $designer_emails[] = $designer->data->user_email;
            }
            $subject = '' . get_the_title($post_id) . ' got a revision file uploaded by client';
            $body = '' . get_the_title($post_id) . ' got a revision file uploaded by client. <br><a href="https://www.designrta.com/login">LOGIN</a> to view.';
            //$headers = array('Content-Type: text/html; charset=UTF-8', 'From: DesignRTA &lt;requests@designrta.com');
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: DesignRTA <requests@designrta.com>';
            if (wp_mail(
                $designer_emails,
                $subject,
                $body,
                $headers
            )) {
                return true;
            } else {
                return false;
            }
        }
    }
    if (in_array('upstream_manager', wp_get_current_user()->roles)) {
        $client_email = get_userdata(get_post_meta(get_the_ID(), '_upstream_project_client_users', true)[0])->data->user_email;

        if ($client_email) {

            $subject = '' . get_the_title($post_id) . ' got a revision file uploaded by designer';
            $body = '' . get_the_title($post_id) . ' got a revision file uploaded by a designer. <br><a href="https://www.designrta.com/login">LOGIN</a> to view.';
            //$headers = array('Content-Type: text/html; charset=UTF-8', 'From: DesignRTA &lt;requests@designrta.com');
            $headers[] = 'Content-Type: text/html; charset=UTF-8';
            $headers[] = 'From: DesignRTA <requests@designrta.com>';
            if (wp_mail(
                $client_email,
                $subject,
                $body,
                $headers
            )) {
                return true;
            } else {
                return false;
            }
        }
    }
}

function get_revison_count($post_id, $comment_id) {
    $revison_count = 0;

    $get_meta = get_post_meta(intval($post_id), '_revision_file');

    if ($get_meta) {
        foreach ($get_meta as $key => $meta) {
            if ($meta['comment_id'] == $comment_id) {
                $revison_count += 1;
            }
        }
        return $revison_count;
    } else {
        return $revison_count;
    }
}


add_action('wp_ajax_get_discussion_revison', 'get_revison_by_commnent');
add_action('wp_ajax_nopriv_get_discussion_revison', 'get_revison_by_commnent');

function get_revison_by_commnent() {
    $revisions = [];
    if ($_POST['action'] != 'get_discussion_revison') {
        wp_die('invalid_action');
    }


    extract($_POST['data']);

    $get_meta = get_post_meta(intval($post_id), '_revision_file');

    if (isset($_POST['project_revison']) && $_POST['project_revison']) {
        if ($get_meta) {
            foreach ($get_meta as $key => $meta) {
                if ($meta['file_id'] == $file_id) {
                    $revisions[] = $meta;
                }
            }
        }
        if ($revisions) {
            $output = [
                'response' => 'success',
                'revision' => $revisions
            ];
            echo json_encode($output);
        } else {
            $output = [
                'response' => 'empty',
                'revision' => $revisions
            ];
            echo json_encode($output);
        }
    } else {

        if ($get_meta) {
            foreach ($get_meta as $key => $meta) {
                if ($meta['comment_id'] == $comment_id) {
                    $revisions[] = $meta;
                }
            }
        }
        if ($revisions) {
            $output = [
                'response' => 'success',
                'revision' => $revisions
            ];
            echo json_encode($output);
        } else {
            $output = [
                'response' => 'empty',
                'revision' => $revisions
            ];
            echo json_encode($output);
        }
    }

    wp_die();
}


function view_revision_button($post_id, $file_id) {
    $revision_count = 0;

    $get_meta = get_post_meta(intval($post_id), '_revision_file');

    if ($get_meta) {
        foreach ($get_meta as $key => $meta) {
            if ($meta['file_id'] == $file_id) {
                $revision_count += 1;
            }
        }
    }

    if ($revision_count > 0) {
        return ' <button data-project-id="' . $post_id . '" data-file_id="' . $file_id . '" data-toggle="modal" data-target="#modal-view_revisions" href="#" class="view_revisions btn btn-outline-primary">
                        View Revisions <b class="revision_file_count">' . $revision_count . '</b>
                    </button>';
    } else {
        return '<div class="alert alert-dark" role="alert">
                            <b>No revisions</b>
                    </div>';
    }
}



add_action('wp_insert_post', 'update_designrta_post_meta', 99);

function update_designrta_post_meta($post_id) {
    if (get_post_type($post_id) == 'project') {
        $current_user = wp_get_current_user();
        if (in_array('upstream_client_user', $current_user->roles)) {
            update_post_meta($post_id, '_upstream_project_client_users', [get_current_user_id()]);
            update_post_meta($post_id, '_upstream_project_client', get_client_id($current_user->data->display_name));
            update_post_meta($post_id, '_upstream_project_status', 'gpaz9');
            update_post_meta($post_id, '_upstream_project_start', time());
            update_post_meta($post_id, '_upstream_project_end', (time() + (86400 * 2)));
        }
    }
}

function get_client_id($name) {
    $client_id = null;
    $client_objects = get_posts([
        'post_type' => 'client',
        's' => $name
    ]);
    if ($client_objects) {
        $client_id = $client_objects[0]->ID;
    }
    return $client_id;
}

add_action('wp_insert_comment', 'update_post_meta_on_revison');

function update_post_meta_on_revison($comment_id) {
    $comment = get_comment($comment_id);
    $post_id = $comment->comment_post_ID;
    if (get_post_type($post_id) != 'project') return;
    $current_user = wp_get_current_user();
    if (in_array('upstream_client_user', $current_user->roles)) {
        if ($comment->comment_parent) return;
        update_post_meta(intval($post_id), '_upstream_project_status', 'cerwk');
    }
}

function project_status_based_on_user($status_name) {
    if ($status_name == 'Revision Requested') {
        $current_user = wp_get_current_user();
        if (in_array('upstream_client_user', $current_user->roles)) {
            return 'In Revision';
        } else {
            return esc_html($status_name);
        }
    } else {
        return esc_html($status_name);
    }
}
