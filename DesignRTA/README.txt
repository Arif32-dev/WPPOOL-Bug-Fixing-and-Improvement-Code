# designrta

code changed at single-project.php file starting from 
<div id="discussion" class="row"> 
</div>
area

upstream plugin bug fixed at class-up-comments.php at line 392 - 395 comment this code 
  if (!upstream_can_access_field('publish_project_discussion', $commentTargetItemType, $item_id, UPSTREAM_ITEM_TYPE_PROJECT, $project_id, 'comments', UPSTREAM_PERMISSIONS_ACTION_EDIT, true)) {
     throw new \Exception(__("You're not allowed to do this.", 'upstream'));
    }


upstream plugin bug fixed at class-up-comments.php at line 360 comment this code 
|| !isset($_POST['nonce'])
and this code at line 367
|| !check_ajax_referer('upstream:project.add_comment_reply:' . $_POST['parent_id'], 'nonce', false)


upstream plugin bug fixed at class-up-comments.php at line 266 - 268 comment this code 
  if (!check_ajax_referer($nonceIdentifier, 'nonce', false)) {
         throw new \Exception(__("Invalid nonce.", 'upstream'));
    }

upstream plugin bug fixed at class-up-comments.php at line 238 comment this code 
|| !isset($_POST['nonce'])
