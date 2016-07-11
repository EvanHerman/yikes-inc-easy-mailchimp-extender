<?php
/**
 * List of Links to our Knowledge Base Articles
 */
$knowledge_base_links = array(
	array(
		'title' => esc_attr__( 'How do I change the submit button text?', 'yikes-inc-easy-mailchimp-extender' ),
		'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-change-the-submit-button-text/',
	),
	array(
		'title' => esc_attr__( 'How do I change the field labels?', 'yikes-inc-easy-mailchimp-extender' ),
		'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-change-the-field-labels/',
	),
	array(
		'title' => esc_attr__( "I don't see all of my MailChimp lists in the dropdown when I go to make a new form. Why?", 'yikes-inc-easy-mailchimp-extender' ),
		'href' => 'https://yikesplugins.com/support/knowledge-base/im-not-seeing-all-my-lists-on-the-drop-down-menu-when-i-go-to-make-a-new-form/',
	),
	array(
		'title' => esc_attr__( 'How do I add new fields to my opt-in form?', 'yikes-inc-easy-mailchimp-extender' ),
		'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-add-new-fields-to-my-form/',
	),
	array(
		'title' => esc_attr__( 'How do I place all of my form fields on one line?', 'yikes-inc-easy-mailchimp-extender' ),
		'href' => 'https://yikesplugins.com/support/knowledge-base/how-do-i-place-all-of-my-form-fields-on-one-line/',
	),
);

// Loop and display the knowledge base article links
if ( $knowledge_base_links && ! empty( $knowledge_base_links ) ) {
	printf( '<h2>' . esc_attr__( 'Popular Knowledge Base Articles', 'yikes-inc-easy-mailchimp-extender' ) . '</h2>' );
	printf( '<ol>' );
	foreach ( $knowledge_base_links as $kb_link ) {
		echo wp_kses_post( '<li><a href="' . esc_url( $kb_link['href'] ) . '" title="' . esc_attr( $kb_link['title'] ) . '" target="_blank">' . esc_attr( $kb_link['title'] ) . '</a></li>' );
	}
	printf( '</ol>' );
}
?>
