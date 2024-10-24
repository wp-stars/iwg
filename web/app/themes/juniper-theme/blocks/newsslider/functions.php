<?php

add_action( 'wp_enqueue_scripts', function () {
	if ( ! has_block( 'acf/newsslider' ) ) {
		return;
	}

	$time       = time();
	$theme_path = get_template_directory_uri();

	$style_file_path  = $theme_path . '/blocks/newsslider/style.css';
	$script_file_path = $theme_path . '/blocks/newsslider/script.js';

	if ( empty( file_get_contents( $style_file_path ) ) ) {
		return;
	}

	wp_enqueue_style('newsslider-css', $theme_path . '/blocks/newsslider/style.css', [], $time );

	if ( empty( file_get_contents( $script_file_path ) ) ) {
		return;
	}
	//	wp_enqueue_script('newsslider-js', $theme_path . '/blocks/newsslider/script.js', array(), $time, true);
});

add_action('admin_enqueue_scripts', function() {
	$theme_path = get_template_directory_uri();
	$time       = time();

	wp_enqueue_style('newsslider-css', $theme_path . '/blocks/newsslider/style.css', [], $time );
});

add_filter( 'timber/acf-gutenberg-blocks-data/newsslider',
	function ( $context ) {
		$number_of_posts = $context['fields']['number_of_posts'] ?: 9;

		$context['rendered_data'] = do_shortcode( '[newsslider number_of_posts=9]' );

		return $context;
	},
);
