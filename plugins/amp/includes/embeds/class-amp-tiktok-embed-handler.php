<?php
/**
 * Class AMP_TikTok_Embed_Handler
 *
 * @package AMP
 */

use AmpProject\Dom\Document;

/**
 * Class AMP_TikTok_Embed_Handler
 */
class AMP_TikTok_Embed_Handler extends AMP_Base_Embed_Handler {

	/**
	 * Registers embed.
	 */
	public function register_embed() {
		// Not implemented.
	}

	/**
	 * Unregisters embed.
	 */
	public function unregister_embed() {
		// Not implemented.
	}

	/**
	 * Sanitize TikTok embeds to be AMP compatible.
	 *
	 * @param Document $dom DOM.
	 */
	public function sanitize_raw_embeds( Document $dom ) {
		$nodes = $dom->xpath->query( '//blockquote[ contains( @class, "tiktok-embed" ) ]' );

		foreach ( $nodes as $node ) {
			if ( ! $this->is_raw_embed( $node ) ) {
				continue;
			}

			$this->make_embed_amp_compatible( $node );
		}
	}

	/**
	 * Determine if the node has already been sanitized.
	 *
	 * @param DOMElement $node The DOMNode.
	 * @return bool Whether the node is a raw embed.
	 */
	protected function is_raw_embed( DOMElement $node ) {
		return ! $node->firstChild || ( $node->firstChild && 'amp-iframe' !== $node->firstChild->nodeName );
	}

	/**
	 * Make TikTok embed AMP compatible.
	 *
	 * @param DOMElement $blockquote_node The <blockquote> node to make AMP compatible.
	 */
	protected function make_embed_amp_compatible( DOMElement $blockquote_node ) {
		$dom      = $blockquote_node->ownerDocument;
		$video_id = $blockquote_node->getAttribute( 'data-video-id' );

		// If there is no video ID, stop here as its needed for the iframe `src` attribute.
		if ( empty( $video_id ) ) {
			return;
		}

		$this->remove_embed_script( $blockquote_node );

		$amp_iframe_node = AMP_DOM_Utils::create_node(
			Document::fromNode( $dom ),
			'amp-iframe',
			[
				'layout'  => 'fixed-height',

				/*
				 * The iframe dimensions cannot be derived from the embed, so we default to a dimension that should
				 * allow the embed to be fully shown.
				 */
				'height'  => 900,

				/*
				 * A `lang` query parameter is added to the URL via JS. This can't be determined here so it is not
				 * added. Whether it alters the embed in any way or not has not been determined.
				 */
				'src'     => 'https://www.tiktok.com/embed/v2/' . $video_id,
				'sandbox' => 'allow-scripts allow-same-origin allow-popups',
			]
		);

		// Find existing <section> node to use as the placeholder.
		foreach ( iterator_to_array( $blockquote_node->childNodes ) as $child ) {
			if ( ! ( $child instanceof DOMElement ) ) {
				continue;
			}

			// Append the placeholder if it was found.
			if ( 'section' === $child->nodeName ) {
				/**
				 * Placeholder to append to the iframe.
				 *
				 * @var DOMElement $placeholder_node
				 */
				$placeholder_node = $blockquote_node->removeChild( $child );
				$placeholder_node->setAttribute( 'placeholder', '' );
				$amp_iframe_node->appendChild( $placeholder_node );
				break;
			}
		}

		// On the non-amp page the embed is wrapped with a <blockquote>, so the same is done here.
		$blockquote_node->appendChild( $amp_iframe_node );
	}

	/**
	 * Remove TikTok's embed script if it exists.
	 *
	 * @param DOMElement $node The DOMNode to make AMP compatible.
	 */
	protected function remove_embed_script( DOMElement $node ) {
		$next_element_sibling = $node->nextSibling;
		while ( $next_element_sibling && ! ( $next_element_sibling instanceof DOMElement ) ) {
			$next_element_sibling = $next_element_sibling->nextSibling;
		}

		$script_src = 'tiktok.com/embed.js';

		// Handle case where script is wrapped in paragraph by wpautop.
		if ( $next_element_sibling instanceof DOMElement && 'p' === $next_element_sibling->nodeName ) {
			$children = $next_element_sibling->getElementsByTagName( '*' );
			if ( 1 === $children->length && 'script' === $children->item( 0 )->nodeName && false !== strpos( $children->item( 0 )->getAttribute( 'src' ), $script_src ) ) {
				$next_element_sibling->parentNode->removeChild( $next_element_sibling );
				return;
			}
		}

		// Handle case where script is immediately following.
		$is_embed_script = (
			$next_element_sibling instanceof DOMElement
			&&
			'script' === strtolower( $next_element_sibling->nodeName )
			&&
			false !== strpos( $next_element_sibling->getAttribute( 'src' ), $script_src )
		);
		if ( $is_embed_script ) {
			$next_element_sibling->parentNode->removeChild( $next_element_sibling );
		}
	}
}
