<?php

add_action( 'after_setup_theme', 'child_theme_setup_before_parent', 0 );
add_action( 'after_setup_theme', 'child_theme_setup1', 11 );
add_action( 'after_setup_theme', 'child_theme_setup2', 14 );

add_action( 'wp', 'child_theme_conditional_setup' );

function child_theme_conditional_setup() {
	if ( is_single() ) {
		add_action( 'hybopress_after_content_sidebar_wrap', 'child_theme_after_content_sidebar_wrap_markup_open', 5 );
		add_action( 'hybopress_after_content_sidebar_wrap', 'child_theme_after_content_sidebar_wrap_markup_close', 15 );

		add_action( 'hybopress_after_content_sidebar_wrap', 'child_theme_do_author_post_meta_share', 7 );

		remove_action( 'hybopress_after_entry', 'hybopress_do_social_share', 7 );
		remove_action( 'hybopress_after_entry', 'hybopress_do_author_box_single', 8 );

		remove_action( 'hybopress_after_entry', 'hybopress_get_comments_template' );
		add_action( 'hybopress_after_content_sidebar_wrap', 'child_theme_get_comments_template', 9 );

		remove_action( 'hybopress_post_meta_categories', 'hybopress_do_meta_categories' );
		remove_action( 'hybopress_post_meta_tags', 'hybopress_do_meta_tags' );
	}
}

function child_theme_setup_before_parent() {

	if ( ! defined( 'SCRIPT_DEBUG' ) ) {
		define( 'SCRIPT_DEBUG', false );
	}

}

function child_theme_setup1() {

	// Register site styles and scripts
	add_action( 'wp_enqueue_scripts', 'child_theme_register_styles' );

	// Enqueue site styles and scripts
	add_action( 'wp_enqueue_scripts', 'child_theme_enqueue_styles' );

}

function child_theme_register_styles() {
	wp_register_style( 'child-fonts', '//fonts.googleapis.com/css?family=Arimo:400,700|Lato:300' );

	$main_styles = trailingslashit( CHILD_THEME_URI ) . "assets/css/child-style.css";

	wp_register_style(
		sanitize_key(  'child-style' ), esc_url( $main_styles ), array( 'skin' ), PARENT_THEME_VERSION, esc_attr( 'all' )
	);
}

function child_theme_enqueue_styles() {
	wp_enqueue_style( 'child-fonts' );
	wp_enqueue_style( 'child-style' );
}

function child_theme_setup2() {
	add_filter( 'hybopress_use_cache', 'child_theme_use_cache' );

	remove_action( 'comment_form_defaults', 'hybopress_override_comment_form_defaults' );
	add_action( 'comment_form_defaults', 'child_theme_override_comment_form_defaults' );

	add_filter( 'hybopress_social_share_title', 'child_theme_social_share_title' );

	add_filter( 'hybopress_social_share_link_html', 'child_theme_social_share_link_html', 10, 4 );

	remove_action( 'hybopress_before_loop', 'hybopress_loop_meta' );
	add_action( 'hybopress_before_loop', 'child_theme_do_search_box' );
}

function child_theme_do_search_box() {
	if ( is_search() ) {
		locate_template( array( 'misc/search-box.php' ), true );
	}
}

function child_theme_social_share_link_html( $social_link, $icon, $icon_classes, $target ) {

	$icon_title        = $icon['title'];
	$icon_name         = $icon['name'];
	$icon_class        = $icon['icon'];

	$icon_classes = 'share-' . $icon_class;

	$social_link = '<a
	class = "' . $icon_classes . '"
	href  = "' . hybopress_get_share_url( $icon_class ) . '"
	title = "' . wp_strip_all_tags( $icon_title ) . '" ' . $target . '
	>' . $icon_name . '</a>';

	return $social_link;
}

function child_theme_use_cache( $use_cache ) {
	return false;
}

function child_theme_after_content_sidebar_wrap_markup_open() {
	printf( '<div class="%1$s">', 'single-extras clearfix' );

		printf( '<div class="%1$s">', 'container-fluid single-extras-inside' );

			printf( '<div class="%1$s">', 'row' );

				printf( '<div class="%1$s">', 'content-area' );

}

function child_theme_after_content_sidebar_wrap_markup_close() {
				echo '</div> <!-- .content-area .col-xs-12 -->';

			echo '</div> <!-- .row -->';

		echo '</div> <!-- .container-fluid -->';

	echo '</div> <!-- .single-extras -->';
}

function child_theme_do_author_post_meta_share() {
	if ( ! hybopress_show_post_meta_list() ) {
		return;
	}

	?>
	<ul class="post-meta-list col-sm-3 clearfix">
		<?php

		if ( hybopress_show_author_box() ) {
			printf( '<li>' );

				printf( '<div class="%s">', 'meta-title' );

					echo __( 'Author', 'hybopress' );

				echo '</div>';

				printf( '<span class="%s">', 'vcard author post-author' );

					printf( '<span class="%s">', 'fn' );

						printf( '<a class="%1$s" rel="%2$s" href="%3$s" title="%4$s">', 'author-posts-url text-capitalize', 'author', trailingslashit( get_author_posts_url( get_the_author_meta( 'ID' ) ) ), __( 'Posts by', 'hybopress' ) . '&nbsp;' . get_the_author() );

							echo get_the_author();

						echo '</a>';

					echo '</span>';

				echo '</span>';

			echo '</li>';
		}

		if ( 1 !== get_theme_mod( 'disable_categories_meta' ) && is_single() ) {
			printf( '<li>' );

				$category_title = sprintf( '<div class="%s">', 'meta-title' );

				$category_title .= __( 'Category', 'hybopress' );

				$category_title .= '</div>';

				hybrid_post_terms( array( 'taxonomy' => 'category', 'text' => $category_title . ' %s', 'sep' => _x( '<br />', 'taxonomy terms separator', 'hybopress' ) ) );

			echo '</li>';
		}

		if ( 1 !== get_theme_mod( 'disable_tags_meta' ) && is_single() ) {
			printf( '<li>' );

				$tag_title = sprintf( '<div class="%s">', 'meta-title' );

				$tag_title .= __( 'Tags', 'hybopress' );

				$tag_title .= '</div>';

				hybrid_post_terms( array( 'taxonomy' => 'post_tag', 'text' => $tag_title . ' %s','sep' => _x( '<br />', 'taxonomy terms separator', 'hybopress' ) ) );

			echo '</li>';
		}

		if ( 1 === get_theme_mod( 'enable_social_share_icons' ) && is_single() ) {

			printf( '<li class="%s">', 'share' );

				printf( '<div class="%s">', 'meta-title' );

					echo __( 'Share', 'hybopress' );

				echo '</div>';

				echo do_shortcode( '[hybopress_social_icons icons_type="share" area="social_share" /]' );

			echo '</li>';

		}


			?>
	</ul>
	<?php
}

function child_theme_get_comments_template() {

	//only show it on singular pages / posts
	if ( ! is_singular() ) {
		return;
	}

	//checking if singular page is being viewed than check if comments are enabled
	//on pages or not and show accordingly, otherwise show comments on single posts

	if ( ( is_page() &&  ( 1 === get_theme_mod( 'enable_page_comments' ) ) ) || ! is_page() ) {

	$grid_class = 'col-xs-12';

	if ( hybopress_show_post_meta_list() ) {
		$grid_class = 'col-sm-9';
	}

		printf( '<div class="%s">', 'comments-template-wrap ' . $grid_class );

			comments_template( '', true ); // Loads the comments.php template.

		echo '</div>';
	}

}

function hybopress_show_post_meta_list() {
	$show_post_meta_box_list = false;

	if( hybopress_show_author_box() ) {
		$show_post_meta_box_list = true;
	}

	if( 1 !== get_theme_mod( 'disable_categories_meta' ) ) {
		$show_post_meta_box_list = true;
	}

	if( 1 !== get_theme_mod( 'disable_tags_meta' ) ) {
		$show_post_meta_box_list = true;
	}

	if( 1 === get_theme_mod( 'enable_social_share_icons' ) && is_single() ) {
		$show_post_meta_box_list = true;
	}

	return $show_post_meta_box_list;
}

function child_theme_override_comment_form_defaults( $defaults ) {

	$defaults['class_submit'] = $defaults['class_submit'] . ' btn btn-primary';

	foreach ( $defaults['fields'] as $key => $field ) {
		$defaults['fields'][$key] = child_theme_make_comment_field_horizontal( $field );
	}

	$defaults['comment_field']        = child_theme_make_comment_field_horizontal( $defaults['comment_field'] );
	$defaults['logged_in_as']         = hybopress_make_comment_notes_help_block( $defaults['logged_in_as'] );
	$defaults['comment_notes_before'] = hybopress_make_comment_notes_help_block( $defaults['comment_notes_before'] );
	$defaults['comment_notes_after']  = hybopress_make_comment_notes_help_block( $defaults['comment_notes_after'] );

	return $defaults;

}


/**
* Rewrite markup to strip paragraph and wrap in horizontal form block markup.
*
* @param string $field
*
* @return string
*/

function child_theme_make_comment_field_horizontal( $field ) {

	$field = preg_replace( '|<p class="(.*?)">|', '<div class="$1 form-group">', $field );

	$field =
	strtr(
		$field,
		array(
			'<label'    => '<label class="col-lg-12 col-md-12 col-sm-12 col-xs-12 text-left "', //control-label
			'<input'    => '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 "><input class="form-control"',
			'<textarea' => '<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12 "><textarea cols="45" rows="8" class="form-control"',
			'</p>'      => '</div>',
		)
	);

	$field .= '</div>';

	return $field;

}

function child_theme_social_share_title( $share_title ) {

	return '';
}
