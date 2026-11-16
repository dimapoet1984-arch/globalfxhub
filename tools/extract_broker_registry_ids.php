<?php
/**
 * Standalone CLI script -- no WordPress, no network. Reads just the
 * raw regulator license fields (cysec, fca, seychelles) plus
 * slug/name out of functions.php's globalfxhub_get_brokers(), for the
 * registry-freshness checkers under tools/ to compare against each
 * regulator's own public register.
 *
 * Deliberately does NOT call globalfxhub_get_brokers() as-is: that
 * function's own last line pipes its result through
 * globalfxhub_attach_broker_scores() -> globalfxhub_broker_scores(),
 * which calls several other scoring helpers this script has no reason
 * to load just to read 3 plain data fields already present on each
 * broker literal -- so that one line is skipped, keeping this script
 * self-contained.
 *
 * Usage: php tools/extract_broker_registry_ids.php
 * Output: a JSON array on stdout, one object per broker:
 *   { "slug": "...", "name": "...", "cysec": "309/16"|null, "fca": "195355"|null, "seychelles": "SD033"|null }
 * A null (or the literal "—" placeholder some entries use) means that
 * broker doesn't claim that regulator's license at all.
 */

$functions_php_path = __DIR__ . '/../functions.php';
$src = file_get_contents( $functions_php_path );
if ( false === $src ) {
    fwrite( STDERR, "Could not read $functions_php_path\n" );
    exit( 1 );
}

function extract_fn_body( $src, $name ) {
    $start = strpos( $src, 'function ' . $name . '(' );
    if ( false === $start ) {
        fwrite( STDERR, "Could not find function $name in functions.php\n" );
        exit( 1 );
    }
    $brace = strpos( $src, '{', $start );
    $depth = 0;
    $i = $brace;
    do {
        if ( '{' === $src[ $i ] ) {
            $depth++;
        }
        if ( '}' === $src[ $i ] ) {
            $depth--;
        }
        $i++;
    } while ( $depth > 0 );
    return substr( $src, $start, $i - $start );
}

$body = extract_fn_body( $src, 'globalfxhub_get_brokers' );
$body = preg_replace(
    '/return array_map\( \'globalfxhub_attach_broker_scores\', \$brokers \);/',
    'return $brokers;',
    $body,
    1,
    $replacements
);
if ( 1 !== $replacements ) {
    fwrite( STDERR, "globalfxhub_get_brokers()'s score-attachment return line has changed shape -- update this script's regex to match.\n" );
    exit( 1 );
}

eval( $body );
$brokers = globalfxhub_get_brokers();

$out = array_map(
    function ( $b ) {
        return array(
            'slug'       => $b['slug'],
            'name'       => $b['name'],
            'cysec'      => $b['cysec'] ?? null,
            'fca'        => $b['fca'] ?? null,
            'seychelles' => $b['seychelles'] ?? null,
        );
    },
    $brokers
);

echo json_encode( $out );
