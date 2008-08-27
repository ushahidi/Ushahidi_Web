		<h1>Register A New Account</h1>

		<?php echo form::open(); ?>

			<fieldset><legend>User Details</legend>

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
				<label for="password_confirm" >Confirm Password</label>

				<input type="password" id="password_confirm" name="password_confirm" value=""  />
<?php
	if (isset($formerrors['password_confirm'])): ?>

				<p class="error"><?php echo $formerrors['password_confirm'];?></p>

<?php
	endif;?>
			</fieldset>

			<input type="submit" id="submit" name="submit" value="Submit"  />

		</form>
