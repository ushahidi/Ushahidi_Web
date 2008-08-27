		<h1>Login</h1>

		<?php echo form::open(); ?>

			<fieldset><legend>User Details</legend>

				<label for="email" >Email Address</label>

				<input type="text" id="email" name="email" value="<?php echo $_POST['email'];?>"  />
<?php
	if (isset($formerrors['email'])): ?>

				<p style="color:#f00;"><?php echo $formerrors['email'];?></p>
<?php
	endif;?>
				<label for="password" >Password</label>

				<input type="password" id="password" name="password" value="<?php echo $_POST['password'];?>"  />
<?php
	if (isset($formerrors['password'])): ?>

				<p style="color:#f00;"><?php echo $formerrors['password'];?></p>
<?php
	endif;?>
				<input type="checkbox" id="remember" name="remember" value="1" checked="checked"  />

				<label for="remember" >Stay logged in on this computer?</label>

			</fieldset>

			<input type="submit" id="submit" name="submit" value="Submit"  />

		</form>
