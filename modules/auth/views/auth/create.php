		<h1>Create A User Account</h1>
<?php echo html::script('media/js/jquery'); ?>
		<?php echo form::open(); ?>

			<fieldset><legend>User Details</legend>
								<label for="name" >Name</label>

								<input type="text" id="name" name="name" value="<?php echo $_POST['name'];?>"  />
				<?php
					if (isset($formerrors['name'])): ?>

								<p class="error"><?php echo $formerrors['name'];?></p>
				<?php
					endif;?>


				<label for="email" >Email Address</label>

				<input type="text" id="email" name="email" value="<?php echo $_POST['email'];?>"  />
<?php
	if (isset($formerrors['email'])): ?>

				<p class="error"><?php echo $formerrors['email'];?></p>
<?php
	endif;?>
				<label for="username" >Username</label>

				<input type="text" id="username" name="username" value="<?php echo $_POST['username'];?>"  />

<?php
	if (isset($formerrors['username'])): ?>

				<p class="error"><?php echo $formerrors['username'];?></p>
<?php
	endif;?>
				<label for="password" >Password</label>

				<input type="password" id="password" name="password" value="<?php echo $_POST['password'];?>"  />
<?php
	if (isset($formerrors['password'])): ?>

				<p class="error"><?php echo $formerrors['password'];?></p>
<?php
	endif;?>
			</fieldset>

			<fieldset><legend>Permissions (roles)</legend>
<?php
	foreach ($roleoptions as $role): ?>

				<label for="roles[]" ><?php echo $role->name;?></label>

				<input type="checkbox" name="roles[]" value=<?php echo '"'.$role->name.'"';?> />
<?php
	endforeach;

	if (isset($formerrors['roles'])): ?>

				<p class="error"><?php echo $formerrors['roles'];?></p>
<?php
	endif;?>
			</fieldset>

			<input type="submit" id="submit" name="submit" value="Create User"  />

		</form>
