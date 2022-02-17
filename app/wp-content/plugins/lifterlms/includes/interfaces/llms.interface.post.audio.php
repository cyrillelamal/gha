<?php
/**
 * LifterLMS Post Model Audio Embeds
 *
 * @package LifterLMS/Interfaces
 *
 * @since 3.17.0
 * @version 5.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * LLMS_Interface_Post_Audio interface
 *
 * @since 3.17.0
 * @deprecated 5.3.0 Use {@see LLMS_Trait_Audio_Video_Embed}.
 */
interface LLMS_Interface_Post_Audio {

	/**
	 * Attempt to get oEmbed for an audio provider
	 *
	 * Falls back to the [audio] shortcode if the oEmbed fails
	 *
	 * @since 3.17.0
	 *
	 * @return string
	 */
	public function get_audio();

}
