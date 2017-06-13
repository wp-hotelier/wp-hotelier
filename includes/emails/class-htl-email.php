<?php
/**
 * Email Class.
 *
 * @author   Benito Lopez <hello@lopezb.com>
 * @category Class
 * @package  Hotelier/Classes/Emails
 * @version  1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'HTL_Email' ) ) :

/**
 * HTL_Email Class
 */
class HTL_Email {

	/**
	 * Email method ID.
	 *
	 * @var String
	 */
	public $id;

	/**
	 * Email method title.
	 *
	 * @var string
	 */
	public $title;

	/**
	 * 'yes' if the method is enabled.
	 *
	 * @var string
	 */
	public $enabled;

	/**
	 * Plain text template path.
	 *
	 * @var string
	 */
	public $template_plain;

	/**
	 * HTML template path.
	 *
	 * @var string
	 */
	public $template_html;

	/**
	 * Template path.
	 *
	 * @var string
	 */
	public $template_base;

	/**
	 * Recipients for the email.
	 *
	 * @var string
	 */
	public $recipient;

	/**
	 * Heading for the email content.
	 *
	 * @var string
	 */
	public $heading;

	/**
	 * Subject for the email.
	 *
	 * @var string
	 */
	public $subject;

	/**
	 * The email object.
	 *
	 * @var object
	 */
	public $object;

	/**
	 * Strings to find in subjects/headings.
	 *
	 * @var array
	 */
	public $find;

	/**
	 * Strings to replace in subjects/headings.
	 *
	 * @var array
	 */
	public $replace;

	/**
	 * Mime boundary (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary;

	/**
	 * Mime boundary header (for multipart emails).
	 *
	 * @var string
	 */
	public $mime_boundary_header;

	/**
	 * True when email is being sent.
	 *
	 * @var bool
	 */
	public $sending;

	/**
	 *  List of preg* regular expression patterns to search for,
	 *  used in conjunction with $replace.
	 *  https://raw.github.com/ushahidi/wp-silcc/master/class.html2text.inc
	 *
	 *  @var array $search
	 *  @see $replace
	 */
	public $plain_search = array(
		"/\r/",                                          // Non-legal carriage return
		'/&(nbsp|#160);/i',                              // Non-breaking space
		'/&(quot|rdquo|ldquo|#8220|#8221|#147|#148);/i', // Double quotes
		'/&(apos|rsquo|lsquo|#8216|#8217);/i',           // Single quotes
		'/&gt;/i',                                       // Greater-than
		'/&lt;/i',                                       // Less-than
		'/&#38;/i',                                      // Ampersand
		'/&#038;/i',                                     // Ampersand
		'/&amp;/i',                                      // Ampersand
		'/&(copy|#169);/i',                              // Copyright
		'/&(trade|#8482|#153);/i',                       // Trademark
		'/&(reg|#174);/i',                               // Registered
		'/&(mdash|#151|#8212);/i',                       // mdash
		'/&(ndash|minus|#8211|#8722);/i',                // ndash
		'/&(bull|#149|#8226);/i',                        // Bullet
		'/&(pound|#163);/i',                             // Pound sign
		'/&(euro|#8364);/i',                             // Euro sign
		'/&#36;/',                                       // Dollar sign
		'/&[^&\s;]+;/i',                                 // Unknown/unhandled entities
		'/[ ]{2,}/'                                      // Runs of spaces, post-handling
	);

	/**
	 *  List of pattern replacements corresponding to patterns searched.
	 *
	 *  @var array $replace
	 *  @see $search
	 */
	public $plain_replace = array(
		'',                                             // Non-legal carriage return
		' ',                                            // Non-breaking space
		'"',                                            // Double quotes
		"'",                                            // Single quotes
		'>',                                            // Greater-than
		'<',                                            // Less-than
		'&',                                            // Ampersand
		'&',                                            // Ampersand
		'&',                                            // Ampersand
		'(c)',                                          // Copyright
		'(tm)',                                         // Trademark
		'(R)',                                          // Registered
		'--',                                           // mdash
		'-',                                            // ndash
		'*',                                            // Bullet
		'£',                                            // Pound sign
		'EUR',                                          // Euro sign. € ?
		'$',                                            // Dollar sign
		'',                                             // Unknown/unhandled entities
		' '                                             // Runs of spaces, post-handling
	);

	/**
	 * Constructor
	 */
	public function __construct() {

		// Default template base if not declared in child constructor
		if ( is_null( $this->template_base ) ) {
			$this->template_base = HTL()->plugin_path() . '/templates/';
		}

		// Settings
		$this->email_type  = htl_get_option( 'emails_type', 'plain' );

		// Find/replace
		$this->find[ 'blogname' ]      = '{blogname}';
		$this->find[ 'site-title' ]    = '{site_title}';
		$this->replace[ 'blogname' ]   = $this->get_blogname();
		$this->replace[ 'site-title' ] = $this->get_blogname();

		// For multipart messages
		add_filter( 'phpmailer_init', array( $this, 'handle_multipart' ) );
	}

	/**
	 * handle_multipart function.
	 *
	 * @param PHPMailer $mailer
	 * @return PHPMailer
	 */
	public function handle_multipart( $mailer )  {

		if ( $this->sending && $this->get_email_type() == 'multipart' ) {

			$mailer->AltBody = wordwrap( preg_replace( $this->plain_search, $this->plain_replace, strip_tags( $this->get_content_plain() ) ) );
			$this->sending = false;
		}

		return $mailer;
	}

	/**
	 * format_string function.
	 *
	 * @param mixed $string
	 * @return string
	 */
	public function format_string( $string ) {
		return str_replace( apply_filters( 'hotelier_email_format_string_find', $this->find, $this ), apply_filters( 'hotelier_email_format_string_replace', $this->replace, $this ), $string );
	}

	/**
	 * get_subject function.
	 *
	 * @return string
	 */
	public function get_subject() {
		return apply_filters( 'hotelier_email_subject_' . $this->id, $this->format_string( $this->subject ), $this->object );
	}

	/**
	 * get_heading function.
	 *
	 * @return string
	 */
	public function get_heading() {
		return apply_filters( 'hotelier_email_heading_' . $this->id, $this->format_string( $this->heading ), $this->object );
	}

	/**
	 * get_recipient function.
	 *
	 * @return string
	 */
	public function get_recipient() {
		return apply_filters( 'hotelier_email_recipient_' . $this->id, $this->recipient, $this->object );
	}

	/**
	 * get_headers function.
	 *
	 * @return string
	 */
	public function get_headers() {
		return apply_filters( 'hotelier_email_headers', "Content-Type: " . $this->get_content_type() . "\r\n", $this->id, $this->object );
	}

	/**
	 * get_attachments function.
	 *
	 * @return string|array
	 */
	public function get_attachments() {
		return apply_filters( 'hotelier_email_attachments', array(), $this->id, $this->object );
	}

	/**
	 * get_type function.
	 *
	 * @return string
	 */
	public function get_email_type() {
		return $this->email_type ? $this->email_type : 'plain';
	}

	/**
	 * get_content_type function.
	 *
	 * @return string
	 */
	public function get_content_type() {
		switch ( $this->get_email_type() ) {
			case 'html' :
				return 'text/html';
			case 'multipart' :
				return 'multipart/alternative';
			default :
				return 'text/plain';
		}
	}

	/**
	 * Checks if this email is enabled and will be sent.
	 *
	 * @return bool
	 */
	public function is_enabled() {
		$enabled = $this->enabled ? true : false;

		return apply_filters( 'hotelier_email_enabled_' . $this->id, $enabled, $this->object );
	}

	/**
	 * get_blogname function.
	 *
	 * @return string
	 */
	public function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}

	/**
	 * get_content function.
	 *
	 * @return string
	 */
	public function get_content() {

		$this->sending = true;

		if ( $this->get_email_type() == 'plain' ) {
			$email_content = preg_replace( $this->plain_search, $this->plain_replace, strip_tags( $this->get_content_plain() ) );
		} else {
			$email_content = $this->get_content_html();
		}

		return wordwrap( $email_content, 70 );
	}

	/**
	 * get_content_plain function.
	 *
	 * @return string
	 */
	public function get_content_plain() {}

	/**
	 * get_content_html function.
	 *
	 * @return string
	 */
	public function get_content_html() {}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( esc_html( htl_get_option( 'emails_from_name', get_bloginfo( 'name', 'display' ) ) ), ENT_QUOTES );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return sanitize_email( htl_get_option( 'emails_from_email_address', get_option( 'admin_email' ) ) );
	}

	/**
	 * Send the email.
	 *
	 * @param string $to
	 * @param string $subject
	 * @param string $message
	 * @param string $headers
	 * @param string $attachments
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers, $attachments ) {

		add_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		add_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		add_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		$message = apply_filters( 'hotelier_mail_content', $message );
		$return  = wp_mail( $to, $subject, $message, $headers, $attachments );

		remove_filter( 'wp_mail_from', array( $this, 'get_from_address' ) );
		remove_filter( 'wp_mail_from_name', array( $this, 'get_from_name' ) );
		remove_filter( 'wp_mail_content_type', array( $this, 'get_content_type' ) );

		return $return;
	}
}

endif;
