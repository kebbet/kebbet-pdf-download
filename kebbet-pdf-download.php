<?php
/**
 * Plugin Name: Kebbet plugins - PDF download-attribute
 * Plugin URI:  https://github.com/kebbet/kebbet-pdf-download
 * Description: Adds the download attribute to all links linking a pdf-file.
 * Version:     20210519.01
 * Author:      Erik Betshammar
 * Author URI:  https://verkan.se
 *
 * @author      Erik Betshammar
 * @package     kebbet-pdf-download
 *
 * Based on https://gist.github.com/kisabelle/8186897
 * From https://wordpress.org/support/topic/add-code-before-each-image/
 */

namespace kebbet\mu\pdf_download;

/**
 * Find all links to pdf-files and insert a `download` attribute.
 *
 * @param string $content The non filtered content of a post.
 * @return string The modified content.
 */
function insert_download_attribute( $content ) {
	// Check if we're inside the main loop in a single Post.
	if ( is_singular() && in_the_loop() && is_main_query() ) {

		/**
		 * Build a regex.
		 *
		 * @source https://www.sitepoint.com/community/t/find-urls-in-a-string-with-preg-match-all/6826/4
		 */
		$href_regex  = '<';         // 1 start of the tag.
		$href_regex .= '\\s*';      // 2 zero or more whitespace
		$href_regex .= 'a';         // 3 the a of the tag itself
		$href_regex .= '\\s+';      // 4 one or more whitespace
		$href_regex .= '[^>]*';     // 5 zero or more of any character that is _not_ the end of the tag
		$href_regex .= 'href';      // 6 the href bit of the tag
		$href_regex .= '\\s*';      // 7 zero or more whitespace
		$href_regex .= '=';         // 8 the = of the tag
		$href_regex .= '\\s*';      // 9 zero or more whitespace
		$href_regex .= '[\\"]?';    // 10 none or one of " or '
		$href_regex .= '(';         // 11 opening parenthesis, start of the bit we want to capture
		$href_regex .= '[^\\" >]+'; // 12 one or more of any character _except_ our closing characters
		$href_regex .= '.pdf)';     // 13 closing parenthesis, end of the bit we want to capture
		$href_regex .= '[\\" >]';   // 14 closing chartacters of the bit we want to capture

		$regex  = '/';              // Regex start delimiter.
		$regex .= $href_regex;
		$regex .= '/';              // Regex end delimiter.
		$regex .= 'i';              // Pattern Modifier - makes regex case insensative.
		$regex .= 's';              // Pattern Modifier - makes a dot metacharater in the pattern.
		// Match all characters, including newlines.
		$regex .= 'U';              // Pattern Modifier - makes the regex ungready.

		preg_match_all( $regex, $content, $pdf_matches );

		if ( $pdf_matches ) {
			$count_matches = count( $pdf_matches[0] );
			for ( $i = 0; $i < $count_matches; $i++ ) {
				// Add download attr to string.
				$href_old = $pdf_matches[0][ $i ];
				$href_new = $href_old . ' download';

				// Make the replacement.
				$content = str_replace( $href_old, $href_new, $content );
			}
		}
	}
	return $content;
}
add_filter( 'the_content', __NAMESPACE__ . '\insert_download_attribute', -10 );
