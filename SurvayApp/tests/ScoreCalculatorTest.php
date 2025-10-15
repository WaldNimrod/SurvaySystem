<?php
declare(strict_types=1);

// Minimal harness without PHPUnit dependency; exit non-zero on mismatch
// Usage: php SurvayApp/tests/ScoreCalculatorTest.php

define('CI_TEST', true);

function ci_boot(): CI_Controller {
    // Bootstrap CodeIgniter index.php
    $_SERVER['CI_ENV'] = 'development';
    chdir(__DIR__ . '/../till.mezoo.co.il_bm1756763301dm');
    require_once 'index.php';
    return get_instance();
}

function assertNearlyEqual($a, $b, $eps = 1e-6) {
    if (abs($a - $b) > $eps) {
        fwrite(STDERR, "Assertion failed: $a != $b\n");
        exit(1);
    }
}

$CI = ci_boot();

// Pick 3 known feedback IDs from DB
$ids = $CI->db->query("SELECT id FROM feedbacks ORDER BY id DESC LIMIT 3")->result_array();
if (count($ids) < 3) {
    fwrite(STDERR, "Not enough feedbacks to test.\n");
    exit(1);
}

foreach ($ids as $row) {
    $id = (int)$row['id'];
    $feedback = $CI->feedback_m->get($id);
    $params = [];
    parse_str((string)$feedback['rowData'], $params);
    $responder = $CI->responder_m->get($feedback['responderId']);
    $divisionId = $responder ? (int)$responder['divisionId'] : 0;
    list($calcResult, $calcLogs, $totalDimRes, $totalDimData) = $CI->scorecalculator->calculateFromParams($params, $divisionId);

    // If feedback.json exists, compare sumTotal when present
    $stored = json_decode((string)$feedback['json'], true);
    if (is_array($stored) && isset($stored['Dims']['dim_sumTotal']['res'])) {
        $expected = (float)$stored['Dims']['dim_sumTotal']['res'];
        assertNearlyEqual((float)$totalDimRes, $expected, 1e-3);
    }
}

echo "ScoreCalculator tests passed for 3 feedbacks.\n";


