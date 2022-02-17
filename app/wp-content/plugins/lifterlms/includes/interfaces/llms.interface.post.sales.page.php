<?php
/**
 * LifterLMS Post Model Sales Page Functions
 *
 * @package LifterLMS/Interfaces
 *
 * @since 3.20.0
 * @version 5.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * LLMS_Interface_Post_Sales_Page
 *
 * @since 3.20.0
 * @deprecated 5.3.0 Use {@see LLMS_Trait_Sales_Page}.
 */
interface LLMS_Interface_Post_Sales_Page {

	/**
	 * Get the URL to a WP Page or Custom URL when sales page redirection is enabled
	 *
	 * @since 3.20.0
	 *
	 * @return string
	 */
	public function get_sales_page_url();

	/**
	 * Determine if sales page redirection is enabled
	 *
	 * @since 3.20.0
	 *
	 * @return string
	 */
	public function has_sales_page_redirect();

}
