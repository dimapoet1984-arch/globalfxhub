<?php
/**
 * Content for the "safety-and-due-diligence.php" Learn cluster. Returns
 * array( slug => array( 'excerpt' => ..., 'content' => ..., 'byline' => ... ) )
 * for each of this cluster's 10 articles once drafted -- empty until then,
 * which globalfxhub_ensure_learn_articles() treats as "not ready yet",
 * never as a stub to publish.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function globalfxhub_learn_content_safety_and_due_diligence() {
    return array();
}
