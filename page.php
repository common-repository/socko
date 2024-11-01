<?php if (!defined('ABSPATH')) die(); ?>

<div class="wrap">
	<h1>Welcome to Socko!</h1>
</div>

<div class="card">
	<h2>What is "Socko" plugin?</h2>

	<p>
		With this little plugin, you will be able to share links that are being displayed as images on <b>Facebook</b> and <b>Skype</b>.
	</p>

	<p>
		This way, when any user clicks on your shared image from <b>Facebook</b> or <b>Skype</b>, he will be redirected to a link of your choice instead of seeing the actual image.
	</p>

	<form>
		<h3>How does it work?</h3>
		<p>
			It works by displaying images only when <b>Facebook</b> or <b>Skype</b> request it. If the request didn't come from one of those two, the image link will actually serve as a redirect.
		</p>

		<p>
			The whole process is actually pretty straightforward but hard to explain, so it's best for you just to test it out and see it in action.
		</p>

		<h3>How do I use it?</h3>
		<p>
			Simply go to your <b>Media Library</b>, select an image of your choice and inside of image details, fill in the redirection link and copy the Socko generated link.
			Just share it on <b>Facebook</b> or <b>Skype</b> and you'll see the magic immediately.
		</p>

		<p>
			<a class="button" href="<?php echo admin_url(); ?>upload.php">Open Media Library</a>
		</p>
	</form>
</div>