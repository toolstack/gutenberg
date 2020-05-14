<?php
/**
 * Server-side rendering of the `core/query-loop` block.
 *
 * @package WordPress
 */

/**
 * Renders the `core/query-loop` block on the server.
 *
 * @param array    $attributes Block attributes.
 * @param string   $content    Block default content.
 * @param WP_Block $block      Block instance.
 *
 * @return string Returns the output of the query, structured using the layout defined by the block's inner blocks.
 */
function render_block_core_query_loop( $attributes, $content, $block ) {
	$page_key = 'query-' . $block->context['queryId'] . '-page';
	$page     = empty( $_GET[ $page_key ] ) ? 1 : filter_var( $_GET[ $page_key ], FILTER_VALIDATE_INT );
	$posts    = get_posts(
		array(
			'post_type'      => 'post',
			'posts_per_page' => $block->context['query']['per_page'],
			'offset'         => $block->context['query']['per_page'] * ( $page - 1 ) + $block->context['query']['offset'],
		)
	);

	$content = '';
	foreach ( $posts as $post ) {
		$content .= (
			new WP_Block(
				$block->parsed_block,
				array(
					'postType' => $post->post_type,
					'postId'   => $post->ID,
				)
			)
		)->render( array( 'dynamic' => false ) );
	}
	return $content;
}

/**
 * Registers the `core/query-loop` block on the server.
 */
function register_block_core_query_loop() {
	register_block_type_from_metadata(
		__DIR__ . '/query-loop',
		array(
			'render_callback'   => 'render_block_core_query_loop',
			'skip_inner_blocks' => true,
		)
	);
}
add_action( 'init', 'register_block_core_query_loop' );
