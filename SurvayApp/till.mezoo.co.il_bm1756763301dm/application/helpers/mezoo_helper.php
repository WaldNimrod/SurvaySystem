<?php
function mail_utf8($to, $subject, $body, $cc = false)
{
    if (!valid_email($to)) {
        return;
    }
    $from = 'no-reply@mezoo.co.il';
    $build = '';
    if (is_array($body)) {
        foreach ($body as $k => $v) {
            $build .= $k . ': ' . $v . '<br/>';
        }
        $build .= 'URL: ' . $_SERVER['REQUEST_URI'] . '<br/>';
        $body = $build;
    }

    $headers = "From: $from\r\n";
    if ($cc && valid_email($cc)) {
        $headers .= 'Cc: ' . $cc . "\r\n";
    }
    $headers .= "MIME-Version: 1.0\r\n";
    $boundary = uniqid("HTMLEMAIL");
    $headers .= "Content-Type: multipart/alternative;" .
        "boundary = $boundary\r\n\r\n";
    $headers .= "This is a MIME encoded message.\r\n\r\n";
    $headers .= "--$boundary\r\n" .
        "Content-Type: text/plain; UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $headers .= chunk_split(base64_encode(strip_tags($body)));
    $headers .= "--$boundary\r\n" .
        "Content-Type: text/html; charset=UTF-8\r\n" .
        "Content-Transfer-Encoding: base64\r\n\r\n";
    $headers .= chunk_split(base64_encode($body));

    $debugHeaders = "From: $from\r\n";
    $debugHeaders .= "MIME-Version: 1.0\r\n";
    $debugHeaders .= "Content-Type: text/html; charset=UTF-8\r\n";

    $result = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, $debugHeaders);
    return $result;
}

function get_pd_label($pdType)
{
    switch ($pdType) {
        case 'PD_firstName':
            return 'שם פרטי';
            break;
        case 'PD_lastName':
            return 'שם משפחה';
            break;
        case 'PD_IDNumber':
            return 'תעודת זהות';
            break;
        case 'PD_routeName':
            return 'קבוצת יחוס';
            break;
        case 'PD_candidateID':
            return 'מספר מועמד';
            break;
        case 'PD_eventID':
            return 'אירוע מספר';
            break;
        case 'PD_organizationName':
            return 'שם הארגון';
            break;
        case 'PD_date':
            return 'תאריך';
            break;
        case 'PD_lang':
            return 'שפה';
            break;
        case 'PD_logs':
            return 'לוג';
            break;
    }
}

function fix_link($link)
{

    if ($_SERVER['HTTP_HOST'] === 'survay.mezoo.co.il') {
        $frag = 'survay.mezoo.co.il';
    }
    if ($_SERVER['HTTP_HOST'] === 'mhkl.mezoo.co.il') {
        $frag = 'mhkl.mezoo.co.il';
    }
    if ($_SERVER['HTTP_HOST'] === 'till.mezoo.co.il') {
        $frag = 'till.mezoo.co.il';
    }
    if ($_SERVER['HTTP_HOST'] === 'www.till.mezoo.co.il') {
        $frag = 'www.till.mezoo.co.il';
    }
    if ($_SERVER['HTTP_HOST'] === 'www.codetix.mezoo.co.il') {
        $frag = 'www.codetix.mezoo.co.il';
    }
    if ($_SERVER['HTTP_HOST'] === 'codetix.mezoo.co.il') {
        $frag = 'codetix.mezoo.co.il';
    }
    return (str_replace('127.0.0.1', 'localhost',
        str_replace('185.37.151.145', @$frag, $link)));
}

/**
 * Sanitize a scalar value to be safe for CSV cells (mitigate formula injection in spreadsheet apps)
 * - If the value starts with =, +, -, @ it will be prefixed with a single quote
 */
function sanitize_for_csv_cell($value)
{
    if (is_string($value)) {
        $trimmed = ltrim($value);
        if (preg_match('/^[=+\-@]/', $trimmed) === 1) {
            return "'" . $value;
        }
        return $value;
    }
    return $value;
}