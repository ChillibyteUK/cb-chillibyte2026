<footer id="footer">
	<section class="footer-main">
		<div class="container py-5">
			<div class="row">
				<div class="col-12 col-lg-5">
					<img
						src="<?= esc_url( get_template_directory_uri() . '/img/chillibyte.svg' ); ?>"
						alt="<?= esc_attr( get_bloginfo( 'name' ) ); ?>"
						width="270"
						height="57">
					<div class="mt-3 mb-4 has-grey-700-text has-small-font-size">
						Part of the <a href="https://www.broadlightgroup.co.uk/" target="_blank" rel="noopener noreferrer">Broadlight Group</a>.
					</div>
					<!-- <div class="footer-partners">
						<a href="https://www.google.com/partners/agency?id=7053046863" target="_blank">
							<img src="https://www.gstatic.com/partners/badge/images/2025/PartnerBadgeClickable.svg">
						</a>
						<img src="<?= esc_url( get_template_directory_uri() . '/img/bing.png' ); ?>" alt="Bing Partner Logo" width="131" height="69">
					</div> -->
				</div>
				<div class="col-12 col-md-6 col-lg-2">
					<div class="navbar-title">Our office</div>
					<?php
					$address = cb_chillibyte_2026_get_setting( 'address' );
					if ( $address ) {
						echo '<address>' . nl2br( esc_html( $address ) ) . '</address>';
					}
					?>
				</div>
				<div class="col-12 col-md-6 col-lg-3">
					<div class="navbar-title">Get in touch</div>
					<div>
						<?= do_shortcode('[contact_email_icon]'); ?>
					</div>
					<div>
						<?= do_shortcode('[contact_phone_icon]'); ?>
					</div>
				</div>
				<div class="col-12 col-md-6 col-lg-2">
					<div class="navbar-title">Quick Links</div>
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'navbar-nav navbar-nav--column',
							'container'      => false,
							'fallback_cb'    => false,
						)
					);
					?>
				</div>
			</div>
		</div>
	</section>
	<section id="colophon">
		<div class="container py-2">
			<?php
			wp_nav_menu(
				array(
					'theme_location' => 'colophon',
					'menu_class'     => 'd-flex flex-wrap justify-content-center gap-3 navbar-nav',
					'container'      => false,
					'fallback_cb'    => false,
				)
			);
			?>
		</div>
		<div class="text-center pb-3">&copy; <?= esc_html( gmdate( 'Y' ) ); ?> Big Byte Media Ltd, a UK Ltd Company Reg. 08554557 | VAT Reg. GB 164 6261 07</div>
	</section>
</footer>

<?php wp_footer(); ?>
</body>
</html>
