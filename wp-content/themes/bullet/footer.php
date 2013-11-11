<?php
/**
 * Displays the footer.
 *
 * @link http://codex.wordpress.org/Stepping_into_Templates#Basic_Template_Files
 * @package WordPress
 * @subpackage Bullet
 */
?>
		<footer role="contentinfo" class="footer">

			<div class="wrap">

				<div id="footer-outer">

					<div id="inner-footer">

						<?php if ( is_active_sidebar( 'footer1' ) ) dynamic_sidebar( 'footer1' ); ?>

						<p class="attribution">&copy; <?php bloginfo( 'name' ); ?></p>

					</div> <!-- end #inner-footer -->

				</div>

			</div>

		</footer> <!-- end footer -->

	</div> <!-- end #container -->

	<?php wp_footer(); // js scripts are inserted using this function ?>

</body>

</html>
