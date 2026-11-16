<?php
/**
 * Shared helpers for this theme's standalone PHP test scripts (no
 * WordPress install or PHPUnit needed -- run directly via `php`,
 * matching this repo's existing convention of plain CLI scripts for
 * verification rather than a full WP test harness).
 */

$GLOBALS['__assert_checks'] = 0;
$GLOBALS['__assert_failures'] = 0;

function assert_equal( $expected, $actual, $label ) {
    $GLOBALS['__assert_checks']++;
    if ( $expected !== $actual ) {
        $GLOBALS['__assert_failures']++;
        echo "FAIL: $label -- expected " . var_export( $expected, true ) . ", got " . var_export( $actual, true ) . "\n";
    } else {
        echo "ok: $label\n";
    }
}

function assert_test_summary_and_exit() {
    $checks = $GLOBALS['__assert_checks'];
    $failures = $GLOBALS['__assert_failures'];
    echo "\n$checks checks, $failures failed.\n";
    exit( $failures > 0 ? 1 : 0 );
}

/**
 * add_action() is a no-op here -- these tests call
 * globalfxhub_enforce_content_language() directly rather than firing
 * a real pre_get_posts action, so the hook registration itself just
 * needs to not fatal.
 */
if ( ! function_exists( 'add_action' ) ) {
    function add_action( ...$args ) {}
}

/**
 * Minimal WP_Query stand-in: only query_vars (so isset() on it works
 * the same way it does on a real WP_Query), get(), and set() -- the
 * only things globalfxhub_enforce_content_language() in
 * inc/i18n-content-language.php touches.
 */
class Fake_WP_Query {
    public $query_vars = array();
    public function __construct( $args = array() ) {
        $this->query_vars = $args;
    }
    public function get( $key ) {
        return isset( $this->query_vars[ $key ] ) ? $this->query_vars[ $key ] : '';
    }
    public function set( $key, $value ) {
        $this->query_vars[ $key ] = $value;
    }
}
