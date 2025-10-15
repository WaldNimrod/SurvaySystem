<?php
// Minimal importer: pulls first 100 feedbacks rows from legacy dump and seeds local DB

declare(strict_types=1);

$dumpPath = __DIR__ . '/../till.mezoo.co.il_bm1756763301dm/databases/tillmezo_a.sql';

$dbHost = getenv('MEZOO_DB_HOST') ?: '127.0.0.1';
$dbPort = (int)(getenv('MEZOO_DB_PORT') ?: '3337');
$dbUser = getenv('MEZOO_DB_USER') ?: 'root';
$dbPass = getenv('MEZOO_DB_PASS') ?: 'root';
$dbName = getenv('MEZOO_DB_NAME') ?: 'mezoo';

$pdo = new PDO("mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4", $dbUser, $dbPass, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
]);

// Ensure base tables exist (no-ops if already created)
$pdo->exec("CREATE TABLE IF NOT EXISTS companies (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  login VARCHAR(191) NOT NULL,
  password VARCHAR(191) DEFAULT NULL,
  contactEmail VARCHAR(191) DEFAULT NULL,
  contactName VARCHAR(191) DEFAULT NULL,
  parentId INT UNSIGNED DEFAULT NULL,
  lastPasswordChange INT UNSIGNED NOT NULL DEFAULT 0,
  PRIMARY KEY (id),
  UNIQUE KEY uq_login (login)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS divisions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  companyId INT UNSIGNED NOT NULL,
  name VARCHAR(191) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_company (companyId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS responders (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  divisionId INT UNSIGNED NOT NULL,
  gismoId VARCHAR(191) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_division (divisionId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS responderextradatas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  responderId INT UNSIGNED NOT NULL,
  paramName VARCHAR(191) NOT NULL,
  val TEXT,
  PRIMARY KEY (id),
  KEY idx_responder (responderId),
  KEY idx_param (paramName)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS feedbacks (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  responderId INT UNSIGNED NOT NULL,
  surveyId INT UNSIGNED DEFAULT NULL,
  rowData TEXT,
  fileName VARCHAR(191) DEFAULT NULL,
  created INT UNSIGNED DEFAULT NULL,
  url TEXT,
  json LONGTEXT,
  socialDes VARCHAR(32) DEFAULT NULL,
  finalGroup INT DEFAULT NULL,
  remarks TEXT,
  PRIMARY KEY (id),
  KEY idx_responder (responderId),
  KEY idx_created (created)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Scoring-related tables
$pdo->exec("CREATE TABLE IF NOT EXISTS dimensiontypes (
  id INT UNSIGNED NOT NULL,
  name VARCHAR(191) NOT NULL,
  PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS dimensiondatagroups (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  companyDivisionId INT UNSIGNED NOT NULL,
  PRIMARY KEY (id),
  KEY idx_div (companyDivisionId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS dimensiondatas (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  attrGroupId INT UNSIGNED NOT NULL,
  dimensionId INT UNSIGNED NOT NULL,
  average DECIMAL(10,4) NOT NULL DEFAULT 0,
  standardDeviation DECIMAL(10,4) NOT NULL DEFAULT 1,
  threshold DECIMAL(10,4) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY idx_group (attrGroupId),
  KEY idx_dim (dimensionId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS questions (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  dimId INT UNSIGNED NOT NULL,
  questionName VARCHAR(191) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_dim (dimId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

$pdo->exec("CREATE TABLE IF NOT EXISTS feedbackdims (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  feedbackId INT UNSIGNED NOT NULL,
  dimId INT UNSIGNED NOT NULL,
  result DECIMAL(10,4) NOT NULL,
  PRIMARY KEY (id),
  KEY idx_feedback (feedbackId),
  KEY idx_dim (dimId)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;");

// Seed default dimension types if empty
$countTypes = (int)$pdo->query("SELECT COUNT(*) c FROM dimensiontypes")->fetch()['c'];
if ($countTypes === 0) {
    $types = [
        1 => 'Dim1',
        3 => 'Dim3',
        4 => 'Dim4',
        5 => 'Dim5',
        6 => 'Dim6',
        7 => 'SigTotal',
        8 => 'SumTotal',
        9 => 'Maz',
    ];
    $stmtType = $pdo->prepare("INSERT INTO dimensiontypes (id, name) VALUES (:id, :name)");
    foreach ($types as $idTyp => $name) {
        $stmtType->execute([':id' => $idTyp, ':name' => $name]);
    }
}

$fh = fopen($dumpPath, 'r');
if (!$fh) {
    fwrite(STDERR, "Cannot open dump file: {$dumpPath}\n");
    exit(1);
}

$inserted = 0;
$pdo->beginTransaction();

// Upsert helper statements
$stmtInsCompany = $pdo->prepare("INSERT INTO companies (id, login, contactName) VALUES (:id, :login, :contactName)
  ON DUPLICATE KEY UPDATE login=VALUES(login), contactName=VALUES(contactName)");
$stmtInsDivision = $pdo->prepare("INSERT INTO divisions (id, companyId, name) VALUES (:id, :companyId, :name)
  ON DUPLICATE KEY UPDATE companyId=VALUES(companyId), name=VALUES(name)");
$stmtInsResponder = $pdo->prepare("INSERT INTO responders (id, divisionId, gismoId) VALUES (:id, :divisionId, :gismoId)
  ON DUPLICATE KEY UPDATE divisionId=VALUES(divisionId), gismoId=VALUES(gismoId)");
$stmtInsExtra = $pdo->prepare("INSERT INTO responderextradatas (responderId, paramName, val) VALUES (:responderId, :paramName, :val)");
$stmtInsFeedback = $pdo->prepare("INSERT INTO feedbacks (id, responderId, surveyId, rowData, fileName, created, url, socialDes, finalGroup)
  VALUES (:id, :responderId, :surveyId, :rowData, :fileName, :created, :url, :socialDes, :finalGroup)
  ON DUPLICATE KEY UPDATE responderId=VALUES(responderId)");

// Helpers for groups/dims/questions
$stmtFindGroup = $pdo->prepare("SELECT id FROM dimensiondatagroups WHERE companyDivisionId = :divisionId LIMIT 1");
$stmtInsGroup = $pdo->prepare("INSERT INTO dimensiondatagroups (companyDivisionId) VALUES (:divisionId)");
$stmtInsDimData = $pdo->prepare("INSERT INTO dimensiondatas (attrGroupId, dimensionId, average, standardDeviation, threshold)
  VALUES (:groupId, :dimensionId, :average, :sd, :threshold)");
$stmtInsQuestion = $pdo->prepare("INSERT INTO questions (dimId, questionName) VALUES (:dimId, :questionName)");

while (!feof($fh) && $inserted < 100) {
    $line = fgets($fh);
    if ($line === false) break;
    if (strpos($line, 'INSERT INTO `feedbacks` VALUES') === false) {
        continue;
    }
    // Extract first 4 values: (id,responderId,surveyId,'rowData', ...)
    if (!preg_match("/INSERT INTO `feedbacks` VALUES \((\d+),(\d+),(\d+),\'(.*?)\'/u", $line, $m)) {
        continue;
    }
    $id = (int)$m[1];
    $responderId = (int)$m[2];
    $surveyId = (int)$m[3];
    $rowData = $m[4];

    // Parse key=value&key2=value2 ... into array
    $params = [];
    parse_str(str_replace(['\\n', "\n"], '', $rowData), $params);

    $companyId = isset($params['CompanyId']) ? (int)$params['CompanyId'] : 1;
    $divisionId = isset($params['divisionId']) ? (int)$params['divisionId'] : 1;
    $gismoId = isset($params['ResponseId']) ? (string)$params['ResponseId'] : null;
    $fileName = isset($params['PD_IDNumber']) ? ($params['PD_IDNumber'] . '-Y-' . date('Ymd')) : null;
    $created = time();
    $url = isset($params['URL']) ? (string)$params['URL'] : null;
    $socialDes = isset($params['Social_desirability_ReRun']) ? strtolower((string)$params['Social_desirability_ReRun']) : null;

    // Seed company and division (names best-effort)
    $companyName = isset($params['PD_organizationName']) ? (string)$params['PD_organizationName'] : ('company_' . $companyId);
    $stmtInsCompany->execute([
        ':id' => $companyId,
        ':login' => $companyName,
        ':contactName' => $companyName,
    ]);
    $divisionName = isset($params['PD_routeName']) ? (string)$params['PD_routeName'] : ('division_' . $divisionId);
    $stmtInsDivision->execute([
        ':id' => $divisionId,
        ':companyId' => $companyId,
        ':name' => $divisionName,
    ]);

    // Ensure a dimension data group exists for this division
    $stmtFindGroup->execute([':divisionId' => $divisionId]);
    $groupId = $stmtFindGroup->fetchColumn();
    if (!$groupId) {
        $stmtInsGroup->execute([':divisionId' => $divisionId]);
        $groupId = (int)$pdo->lastInsertId();

        // Seed minimal dimension data rows (averages/stdevs)
        $defaults = [
            ['dimensionId' => 1, 'avg' => 50, 'sd' => 10, 'th' => null],
            ['dimensionId' => 3, 'avg' => 50, 'sd' => 10, 'th' => null],
            ['dimensionId' => 4, 'avg' => 50, 'sd' => 10, 'th' => null],
            ['dimensionId' => 5, 'avg' => 50, 'sd' => 10, 'th' => null],
            ['dimensionId' => 6, 'avg' => 50, 'sd' => 10, 'th' => null],
            // 7 and 8 are special (no questions) and used for totals
            ['dimensionId' => 7, 'avg' => 0, 'sd' => 1, 'th' => null],
            ['dimensionId' => 8, 'avg' => 0, 'sd' => 1, 'th' => 0.0],
            ['dimensionId' => 9, 'avg' => 50, 'sd' => 10, 'th' => null],
        ];
        foreach ($defaults as $d) {
            $stmtInsDimData->execute([
                ':groupId' => $groupId,
                ':dimensionId' => $d['dimensionId'],
                ':average' => $d['avg'],
                ':sd' => $d['sd'],
                ':threshold' => $d['th'],
            ]);
        }

        // Optionally seed a few placeholder questions for dims that use questions (not for 7/8)
        foreach ([1,3,4,5,6,9] as $dimIdQ) {
            for ($i=1; $i<=5; $i++) {
                $stmtInsQuestion->execute([':dimId' => $dimIdQ, ':questionName' => sprintf('Q%d_%d', $dimIdQ, $i)]);
            }
        }
    }

    // Seed responder
    $stmtInsResponder->execute([
        ':id' => $responderId,
        ':divisionId' => $divisionId,
        ':gismoId' => $gismoId,
    ]);

    // PD_* extras
    foreach ($params as $k => $v) {
        if (strpos($k, 'PD_') === 0) {
            $stmtInsExtra->execute([
                ':responderId' => $responderId,
                ':paramName' => (string)$k,
                ':val' => is_scalar($v) ? (string)$v : json_encode($v, JSON_UNESCAPED_UNICODE),
            ]);
        }
    }

    // Feedback record
    $stmtInsFeedback->execute([
        ':id' => $id,
        ':responderId' => $responderId,
        ':surveyId' => $surveyId,
        ':rowData' => $rowData,
        ':fileName' => $fileName,
        ':created' => $created,
        ':url' => $url,
        ':socialDes' => $socialDes,
        ':finalGroup' => null,
    ]);

    $inserted++;
}

$pdo->commit();
fclose($fh);

fwrite(STDOUT, "Imported feedbacks: {$inserted}\n");



