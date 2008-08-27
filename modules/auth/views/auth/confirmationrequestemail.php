<p><?php echo $user->username; ?>, please confirm your registration by clicking the link below or copy-and-pasting it to your browser.</p>

<?php echo html::anchor('auth_demo/confirm_user/'.$user->key); ?>
