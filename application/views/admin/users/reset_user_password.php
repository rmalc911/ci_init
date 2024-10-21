<form action="<?= ad_base_url('users/reset_password') ?>" method="post" style="font-size: 16px;">
	<input class="swal2-input" id="password-input" type="password" name="password" placeholder="New Password" style="display: flex; width: 100%; margin: 20px 0;" required>
	<input type="hidden" name="user_id" value="<?= $user_id ?>">
	<button type="submit" class="swal2-confirm swal2-styled" aria-label="" style="display: inline-block;">Submit</button>
</form>
