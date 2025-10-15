<?php
defined('BASEPATH') OR exit('No direct script access allowed');
defined('DEBUG_REPORT') OR define('DEBUG_REPORT', 0); 

class Welcome extends CI_Controller
{


    public function __construct()
    {
        parent::__construct();
        //error_reporting(E_ALL);
        //ini_set('display_errors', 1);
        $this->load->database();
        $this->load->library('session');
        $this->load->helper('form');
        $this->load->helper('url');
        $this->load->model('dimensiondatagroup_m');
        $this->load->helper('mezoo_helper');
        $this->load->helper('email_helper');
        $this->load->model('dimensiontype_m');
        $this->load->model('dimensiondata_m');
        $this->load->model('feedback_m');
        $this->load->model('feedbackdim_m');
        $this->load->model('log_m');
        $this->load->model('question_m');
        $this->load->model('company_m');
        $this->load->model('division_m');
        $this->load->model('responder_m');
        $this->load->model('responderextradata_m');
        $this->load->library('ScoreCalculator');
    }

    public function reset()
    {
        $sql = file_get_contents('fresh.sql');
        $sqls = explode(';', $sql);
        foreach ($sqls as $statement) {
            $this->db->query($statement);
        }
    }
  
    public function upgrade()
    {
        $sql = file_get_contents('upgrade.sql');
        $sqls = explode(';', $sql);
        foreach ($sqls as $statement) {
            $this->db->query($statement);
        }
    }

    public function removekey()
    {


        $this->db->query('ALTER TABLE dimensiondatagroups DROP PRIMARY KEY;');
        echo 'ok';
    }

    public function refresh()
    {
        $sql = file_get_contents('refresh.sql');
        $sqls = explode(';', $sql);
        foreach ($sqls as $statement) {
            $this->db->query($statement);
        }
    }

    public function index()
    {     
        $this->load->helper('url');
        $result = array();
        $logs = array();
        $rowData = array();

        // Rate limit external API: 120 req/min per IP
        if (!$this->_rateLimitAllowed('api-index:' . $this->input->ip_address(), 120, 60)) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(429)
                ->set_output(json_encode(array('error' => 'rate limit exceeded')));
        }

        $logs[] = 'Getting a Seker Request';
        $logs[] = 'RowData:';
        foreach ($this->input->get() as $paramKey => $paramValue) {
            $rowData[] = $paramKey . '=' . $paramValue;
            $logs[] = $paramKey . ' = ' . $paramValue;
        }
        $logs[] = 'rowData is: ' . implode(',', $rowData);
        $fileName = $this->input->get('PD_IDNumber') . '-Y-' . @date('Ymd');
        $rowData[] = 'FileName=' . $fileName;

        if (!$this->input->get('PD_IDNumber')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(array('error' => 'ID Number is missing, aborting.')));
            exit;
        }
        if (!$this->input->get('CompanyId')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(array('error' => 'Company ID is missing, aborting.')));
            exit;
        }

        if (!$this->input->get('divisionId')) {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(401)
                ->set_output(json_encode(array('error' => 'Division ID is missing, aborting.')));
            exit;
        }

        $existingIdNumber = $this->responderextradata_m->get_by(array(
            'paramName' => 'PD_IDNumber',
            'val' => $this->input->get('PD_IDNumber')
        ));

        $responderId = $this->responder_m->insert(array(
            'divisionId' => $this->input->get('divisionId'),
            'gismoId' => $this->input->get('ResponseId')
        ));
        $logs[] = 'New responderId is: ' . $responderId;

        $feedbackId = $this->feedback_m->insert(array(
            'responderId' => $responderId,
            'surveyId' => $this->input->get('SurveyId'),
            'rowData' => implode('&', $rowData),
            'fileName' => $fileName,
            'created' => time(),
            'url' => $_SERVER['REQUEST_URI']
        ));
        $logs[] = 'New feedbackId is: ' . $feedbackId;
        $result['PD'] = array();

        $result['PD']['PD_date'] = get_pd_label('PD_date') . ';' . @date('d-m-Y H:i:s');
        foreach ($this->input->get() as $paramKey => $paramValue) {
            if (strpos($paramKey, 'PD_') === 0) {
                $responderExtraDataId = $this->responderextradata_m->insert(array(
                    'responderId' => $responderId,
                    'paramName' => $paramKey,
                    'val' => $paramValue
                ));
                $result['PD'][$paramKey] = get_pd_label($paramKey) . ';' . $paramValue;
            }
        }

        list($calcResult, $calcLogs, $totalDimRes, $totalDimData) = $this->scorecalculator->calculateAndPersist($feedbackId, (int)$this->input->get('divisionId'));
        $logs = array_merge($logs, $calcLogs);
        $result = array_merge($result, $calcResult);
        $result['Social_desirability_ReRun'] = $this->input->get('Social_desirability_ReRun');


        $response = array();
        $response['hasError'] = false;


        $company = $this->company_m->get($this->input->get('CompanyId'));
        $adminCompany = $this->company_m->get_by(array('login' => 'admin'));
        if (!$company) {
            $response['hasError'] = true;
            $response['errors'][] = "Company Id ".$this->input->get('CompanyId') ." not found";
            mail_utf8(
                $adminCompany['contactEmail'],
                'Codetix - Survey error (ID: ' . $this->input->get('CompanyId') . ')',
                array(
                    'Error Type' => 'Company ' . $this->input->get('CompanyId') . ' not found',
                    'Date' => @date('Y-m-d H:i'),
                    'Private name' => $this->input->get('PD_firstName'),
                    'Last name' => $this->input->get('PD_lastName'),
                    'ID Number' => $this->input->get('PD_IDNumber')
                )
            );
        } else {
            $division = $this->division_m->get_by(array(
                'id' => $this->input->get('divisionId'),
                'companyId' => $company['id']
            ));
            if (!$division) {
                $response['hasError'] = true;
                 $response['errors'][] = "Division Id ".$this->input->get('divisionId') ." not found";
                mail_utf8(
                    $company['contactEmail'],
                    'Codetix - Survey error (ID: ' . $this->input->get('CompanyId') . ')',
                    array(
                        'Error Type' => 'Division ' . $this->input->get('divisionId') . ' not found',
                        'Date' => @date('Y-m-d H:i'),
                        'Company name' => $company['contactName'],
                        'Private name' => $this->input->get('PD_firstName'),
                        'Last name' => $this->input->get('PD_lastName'),
                        'ID Number' => $this->input->get('PD_IDNumber')
                    ),
                    $adminCompany['contactEmail']
                );
            }
            // Resolve dimension data group by division; attr_group param is optional and may be absent
            $dimensionDataGroup = $this->dimensiondatagroup_m->get_by(array('companyDivisionId' => (int)$this->input->get('divisionId')));

            if ($existingIdNumber) {
                $response['hasError'] = true;
                $response['errors'][] = "PD_IDNumber '".$this->input->get('PD_IDNumber')."' already exist";
                
                mail_utf8(
                    $company['contactEmail'],
                    'Codetix - Survey error (' . $company['contactName'] . ')',
                    array(
                        'Error Type' => 'Existing ID number',
                        'Date' => @date('Y-m-d H:i'),
                        'Company name' => $company['contactName'],
                        'Division name' => $division['name'],
                        'Private name' => $this->input->get('PD_firstName'),
                        'Last name' => $this->input->get('PD_lastName'),
                        'ID Number' => $this->input->get('PD_IDNumber')
                    ),
                    $adminCompany['contactEmail']
                );
            }
            // Only warn on attribute group if client provided attr_group but no group was resolved for this division
            $appDev = (bool)$this->config->item('app_dev_mode');
            if ($this->input->get('attr_group') !== null && $this->input->get('attr_group') !== '' && !$dimensionDataGroup) {
                $response['hasError'] = true;
                $response['errors'][] = 'Attribute group ' . $this->input->get('attr_group') . ' not found';
                if (!$appDev) {
                mail_utf8(
                    $company['contactEmail'],
                    'Codetix - Survey error (' . $company['contactName'] . ')',
                    array(
                        'Error Type' => 'Attribute group ' . $this->input->get('attr_group') . ' not found',
                        'Date' => @date('Y-m-d H:i'),
                        'Company name' => $company['contactName'],
                        'Division name' => $division['name'],
                        'Private name' => $this->input->get('PD_firstName'),
                        'Last name' => $this->input->get('PD_lastName'),
                        'ID Number' => $this->input->get('PD_IDNumber')
                    ),
                    $adminCompany['contactEmail']
                );
                }
            }
        }
        $result['CompanyId'] = $company['id'];

        if (strtolower($result['Social_desirability_ReRun']) == 'false' || strtolower($result['Social_desirability_ReRun']) == 'yes') {
            if ($totalDimRes <= $totalDimData['threshold']) {
                $finalGroup = 2;
            } else {
                $finalGroup = 3;
            }
        } else {
            $finalGroup = 1;
        }

        $logs[] = 'if Social_desirability_ReRun is NOT false/yes then finalGroup = 1';
        $logs[] = 'Otherwise, if totalDimRes <= totalDimData.threshold then finalGroup = 2, else finalGroup = 3';
        $logs[] = 'Social_desirability_ReRun = ' . $result['Social_desirability_ReRun'];
        $logs[] = 'totalDimRes = ' . $totalDimRes;
        $logs[] = 'totalDimData.threshold = ' . $totalDimData['threshold'];
        $logs[] = 'finalGroup = ' . $finalGroup;

        if (defined('DEBUG_REPORT') && DEBUG_REPORT === 1) {
            $result['PD']['PD_logs'] = get_pd_label('PD_logs') . ';' . implode('\n', $logs);
        }
        
        $this->feedback_m->update($feedbackId, array(
            'json' => json_encode($result),
            'socialDes' => strtolower($result['Social_desirability_ReRun']),
            'finalGroup' => $finalGroup
        ));

        $appDev = (bool)$this->config->item('app_dev_mode');
        if ($appDev) {
            $this->load->view('report', array('result' => json_encode($result), 'companyId' => $result['CompanyId']));
        } else {
            return $this->output
                ->set_content_type('application/json')
                ->set_status_header(200)
                ->set_output(json_encode($response));
        }
    }

    public function login()
    {
        $appDev = (bool)$this->config->item('app_dev_mode');
        // Dev-only bypass on POST
        if ($appDev && strtoupper($this->input->method()) === 'POST') {
            $this->session->set_userdata('currentUser', 1);
            redirect(fix_link(site_url('welcome/admin')));
            return;
        }

        // Production/staging: process login with bcrypt + rate limit
        $error = false;
        if (!$appDev && strtoupper($this->input->method()) === 'POST') {
            // Basic rate limit: 10 attempts per 10 minutes per IP
            if (!$this->_rateLimitAllowed('login:' . $this->input->ip_address(), 10, 600)) {
                $error = 'Too many attempts. Try again later.';
            } else {
                $login = trim((string)$this->input->post('login'));
                $password = (string)$this->input->post('password');
                if ($login !== '' && $password !== '') {
                    $company = $this->company_m->get_by(array('login' => $login));
                    if ($company) {
                        $hash = (string)$company['password'];
                        $ok = false;
                        if ($hash !== '' && strlen($hash) > 20) {
                            $ok = password_verify($password, $hash);
                        } else {
                            // Legacy fallback: plain match (for imported old data)
                            $ok = hash_equals($hash, $password);
                        }
                        if ($ok) {
                            $this->session->set_userdata('currentUser', (int)$company['id']);
                redirect(fix_link(site_url('welcome/admin')));
                            return;
            }
        }
                }
                $error = 'Invalid credentials';
            }
        }

        $this->load->view('admin_template', array('view' => 'admin/login', 'error' => $error));
    }

    public function admin()
    {

        if ($this->session->userdata('currentUser')) {
            $currentCompany = $this->company_m->get($this->session->userdata('currentUser'));
            $lastPasswordChange = $currentCompany['lastPasswordChange'];
            if ($currentCompany['parentId']) {
                $currentCompany = $this->company_m->get($currentCompany['parentId']);
            }
            $data = array();
            $data['admin'] = $currentCompany['login'] === 'admin' || $currentCompany['login'] === 'admin2' || $currentCompany['login'] === 'admin3';
            $data['dataGroups'] = array();
            $data['divisions'] = array();
            $data['companies'] = $this->_getCompanies();

            $data['company'] = $currentCompany;
            $data['appDev'] = (bool)$this->config->item('app_dev_mode');

            foreach ($data['companies'] as $key => $company) {
                $data['divisions'] = $data['divisions'] + $this->_getDivisions($company['id']);
            }
            if (!$data['admin']) {
                unset($data['companies']);
            }
            // In dev mode, do not force a default date range so all imported data is visible
            $appDev = (bool)$this->config->item('app_dev_mode');
            if (!$appDev && !$this->input->get('daterange')) {
                $_GET['daterange'] = @date("d.m.Y", strtotime("-7 day")) . ' - ' . @date("d.m.Y");
            }

            $query = $this->_getQuery();


            $queryRes = $this->db->query($query);
            $data['results'] = $queryRes->result_array();
            // Pagination meta
            $perPage = (int)$this->input->get('perPage');
            if ($perPage <= 0 || $perPage > 500) { $perPage = 100; }
            $page = (int)$this->input->get('page');
            if ($page <= 0) { $page = 1; }
            $total = $this->_getCount();
            $data['pagination'] = array(
                'total' => $total,
                'perPage' => $perPage,
                'page' => $page,
                'pages' => max(1, (int)ceil($total / $perPage)),
            );
            $this->load->view('admin_template', array('view' => 'admin/admin', 'data' => $data, 'lastPasswordChange' => $lastPasswordChange));
        } else {
            redirect(fix_link(site_url('welcome/login')));
        }
    }

    public function generate($id)
    {

        $decode = urldecode($this->input->get('json'));
        $array = json_decode($decode, true);
        $feedback = $this->feedback_m->get_by(array('id' => $id));
        if (!$array || !is_array($array)) {
            // Fallbacks: try stored JSON; if missing, build minimal PD from DB so we can still render/save a file
            if (!empty($feedback['json'])) {
                $array = json_decode($feedback['json'], true);
            }
            if (!$array || !is_array($array)) {
                $array = array('PD' => array(), 'Dims' => array());
                $responder = $this->responder_m->get($feedback['responderId']);
                if ($responder) {
                    $extras = $this->responderextradata_m->get_many_by(array('responderId' => $responder['id']));
                    foreach ($extras as $ex) {
                        $array['PD'][$ex['paramName']] = get_pd_label($ex['paramName']) . ';' . $ex['val'];
                    }
                    $division = $this->division_m->get($responder['divisionId']);
                    if ($division) {
                        $array['CompanyId'] = $division['companyId'];
                    }
                }
            }
        }
        $params = array();
        parse_str($feedback['rowData'], $params);
        $lang = isset($params['Language']) ? (string)$params['Language'] : '';
        $array['PD']['PD_lang'] = 'שפה;'.$lang;
        $companyId = $array['CompanyId'];
        // Enrich with DimMeta for dev/debug (reference group stats)
        $divisionId = 0;
        if (!empty($params['divisionId'])) {
            $divisionId = (int)$params['divisionId'];
        } else if (!empty($feedback['responderId'])) {
            $responder = $this->responder_m->get($feedback['responderId']);
            $divisionId = $responder ? (int)$responder['divisionId'] : 0;
        }
        if ($divisionId > 0) {
            $dimMeta = array();
            $group = $this->dimensiondatagroup_m->get_by(array('companyDivisionId' => $divisionId));
            if ($group) {
                $dims = $this->dimensiondata_m->get_many_by(array('attrGroupId' => $group['id']));
                foreach ($dims as $d) {
                    $dtype = $this->dimensiontype_m->get_by(array('id' => $d['dimensionId']));
                    $key = isset($dtype['name']) ? ('dim_' . $dtype['name']) : ('dim_' . (string)$d['dimensionId']);
                    $dimMeta[$key] = array(
                        'dimensionId' => (int)$d['dimensionId'],
                        'average' => (float)$d['average'],
                        'standardDeviation' => (float)$d['standardDeviation'],
                        'threshold' => $d['threshold'] !== null ? (float)$d['threshold'] : null,
                        'name' => isset($dtype['name']) ? $dtype['name'] : (string)$d['dimensionId'],
                    );
                }
                // View meta for personal/company/division labels
                $divisionRow = $this->division_m->get($divisionId);
                $companyRow = $divisionRow ? $this->company_m->get($divisionRow['companyId']) : null;
                $array['ViewMeta'] = array(
                    'companyId' => $divisionRow ? (int)$divisionRow['companyId'] : 0,
                    'companyName' => $companyRow ? (string)$companyRow['login'] : '',
                    'divisionName' => $divisionRow ? (string)$divisionRow['name'] : '',
                    'routeName' => isset($array['PD']['PD_routeName']) ? (string)substr(strstr($array['PD']['PD_routeName'], ';'), 1) : '',
                    'firstName' => isset($array['PD']['PD_firstName']) ? (string)substr(strstr($array['PD']['PD_firstName'], ';'), 1) : '',
                    'lastName' => isset($array['PD']['PD_lastName']) ? (string)substr(strstr($array['PD']['PD_lastName'], ';'), 1) : '',
                    'idNumber' => isset($array['PD']['PD_IDNumber']) ? (string)substr(strstr($array['PD']['PD_IDNumber'], ';'), 1) : '',
                    'date' => isset($array['PD']['PD_date']) ? (string)substr(strstr($array['PD']['PD_date'], ';'), 1) : @date('d-m-Y H:i:s'),
                );
            }
            if (!empty($dimMeta)) {
                $array['DimMeta'] = $dimMeta;
            }
        }
        $final = json_encode($array);

        // In dev mode: render to string and save to a writable folder for direct access
        $appDev = (bool)$this->config->item('app_dev_mode');
        $fileBase = isset($_GET['fileName']) && $_GET['fileName'] !== '' ? preg_replace('/[^A-Za-z0-9_\-\.]/', '_', $_GET['fileName']) : ('report_'.$id);
        if ($appDev) {
            $html = $this->load->view('report', array('result' => $final, 'companyId' => $companyId), true);
            $dir = rtrim(FCPATH, '/').'/tmpp/reports';
            if (!is_dir($dir)) {
                @mkdir($dir, 0775, true);
            }
            $target = $dir.'/'.$fileBase.'.html';
            @file_put_contents($target, $html);
            // Redirect to the saved static file for easy access
            redirect(base_url('tmpp/reports/'.$fileBase.'.html'));
            return;
        }

        // Fallback (prod): stream as inline or attachment
        if (isset($_GET['d'])) {
            header('Content-disposition: attachment; filename=' . $fileBase . '.html');
            header('Content-type: text/html');
        }
        $this->load->view('report', array('result' => $final, 'companyId' => $companyId));
    }

    // Dev-only: Recalculate scores for a given feedback id from stored rowData and return JSON (result + logs)
    public function recalc($id)
    {
        $appDev = (bool)$this->config->item('app_dev_mode');
        if (!$appDev) {
            show_404();
            return;
        }

        // Early schema checks to avoid fatal DB errors
        $requiredTables = array('feedbacks', 'responders', 'dimensiondatagroups', 'dimensiondatas');
        foreach ($requiredTables as $t) {
            if (!$this->db->table_exists($t)) {
                return $this->output->set_content_type('application/json')->set_status_header(500)
                    ->set_output(json_encode(array('error' => 'missing table', 'table' => $t)));
            }
        }

        $feedback = $this->feedback_m->get_by(array('id' => (int)$id));
        if (!$feedback) {
            return $this->output->set_content_type('application/json')->set_status_header(404)
                ->set_output(json_encode(array('error' => 'feedback not found', 'id' => (int)$id)));
        }

        $params = array();
        parse_str((string)$feedback['rowData'], $params);
        $divisionId = 0;
        if (!empty($params['divisionId'])) {
            $divisionId = (int)$params['divisionId'];
        } else {
            $responder = $this->responder_m->get($feedback['responderId']);
            $divisionId = $responder ? (int)$responder['divisionId'] : 0;
        }
        if ($divisionId <= 0) {
            return $this->output->set_content_type('application/json')->set_status_header(400)
                ->set_output(json_encode(array('error' => 'divisionId is missing for this feedback', 'feedbackId' => (int)$id)));
        }

        // Safe load of dimension group and dims
        try {
            $dimensionDataGroup = $this->dimensiondatagroup_m->get_by(array('companyDivisionId' => $divisionId));
        } catch (\Throwable $e) {
            return $this->output->set_content_type('application/json')->set_status_header(500)
                ->set_output(json_encode(array('error' => 'failed loading dimension group', 'message' => $e->getMessage())));
        }
        if (!$dimensionDataGroup) {
            return $this->output->set_content_type('application/json')->set_status_header(404)
                ->set_output(json_encode(array('error' => 'dimension data group not found for divisionId', 'divisionId' => $divisionId)));
        }
        try {
            $dims = $this->dimensiondata_m->get_many_by(array('attrGroupId' => $dimensionDataGroup['id']));
        } catch (\Throwable $e) {
            return $this->output->set_content_type('application/json')->set_status_header(500)
                ->set_output(json_encode(array('error' => 'failed loading dimension data', 'message' => $e->getMessage())));
        }
        if (!$dims || count($dims) === 0) {
            return $this->output->set_content_type('application/json')->set_status_header(404)
                ->set_output(json_encode(array('error' => 'no dimension data defined for group', 'groupId' => $dimensionDataGroup['id'])));
        }
        $dimTypeIds = array();
        foreach ($dims as $d) { $dimTypeIds[] = (int)$d['dimensionId']; }
        if (!(in_array(7, $dimTypeIds, true) && in_array(8, $dimTypeIds, true))) {
            return $this->output->set_content_type('application/json')->set_status_header(400)
                ->set_output(json_encode(array('error' => 'required dimension types 7/8 not configured for this division', 'divisionId' => $divisionId)));
        }

        try {
            list($calcResult, $calcLogs, $totalDimRes, $totalDimData) = $this->scorecalculator->calculateFromParams($params, $divisionId);
        } catch (\Throwable $e) {
            return $this->output->set_content_type('application/json')->set_status_header(500)
                ->set_output(json_encode(array('error' => 'calculation failed', 'message' => $e->getMessage())));
        }
        $payload = array(
            'feedbackId' => (int)$id,
            'divisionId' => $divisionId,
            'result' => $calcResult,
            'totalDimRes' => $totalDimRes,
            'totalDimData' => $totalDimData,
            'logs' => $calcLogs,
        );
        return $this->output->set_content_type('application/json')->set_status_header(200)
            ->set_output(json_encode($payload));
    }

    public function logout()
    {
        $this->session->unset_userdata(array('currentUser'));
        redirect(fix_link(site_url('welcome/login')));
    }

    public function _getCompanies()
    {
        $companies = $this->company_m->get_all();
        $filteredCompanies = array();
        foreach ($companies as $company) {
            $filteredCompanies[] = $company;
        }
        return $filteredCompanies;
    }

    public function _getDivisions($companyId)
    {
        $divisions = $this->division_m->get_many_by(array('companyId' => $companyId));
        $filteredDivisions = array();
        foreach ($divisions as $division) {
            $filteredDivisions[] = $division;
        }
        return $filteredDivisions;
    }

    private function _getQuery()
    {
        // Determine sorting in a safe, whitelisted manner
        $sortKey = $this->input->get('sortKey') ? $this->input->get('sortKey') : 'created';
        $sortOrder = strtolower($this->input->get('sortOrder')) === 'asc' ? 'asc' : 'desc';

        // Map allowed sort keys to real column names
        $sortMap = array(
            'feedbackId' => 'feedbacks.id',
            'divisionName' => 'divisions.name',
            'firstName' => 'FirstName.val',
            'lastName' => 'LastName.val',
            'candidateId' => 'CandidateID.val',
            'idNumber' => 'IDNumber.val',
            'created' => 'feedbacks.created'
        );
        $sortColumn = isset($sortMap[$sortKey]) ? $sortMap[$sortKey] : 'feedbacks.created';

        // Build query using Query Builder to avoid SQL injection
        $this->db->select('responders.id as responderId');
        $this->db->select('feedbacks.id as feedbackId');
        $this->db->select('feedbacks.finalGroup as finalGroup');
        $this->db->select('feedbacks.fileName as fileName');
        $this->db->select('feedbacks.socialDes as socialDes');
        $this->db->select('feedbacks.remarks as remarks');
        $this->db->select('responders.divisionId as divisionId');
        $this->db->select('feedbacks.json as json');
        $this->db->select('divisions.name as divisionName');
        $this->db->select('divisions.companyId as companyId');
        $this->db->select('companies.login as companyName');
        $this->db->select('feedbacks.created as created');
        $this->db->select('IDNumber.val as idNumber');
        $this->db->select('FirstName.val as firstName');
        $this->db->select('LastName.val as lastName');
        $this->db->select('CandidateID.val as candidateId');
        $this->db->select('feedbacks.rowData as rowData');

        $this->db->from('feedbacks');
        $this->db->join('responders', 'responders.id = feedbacks.responderId');
        $this->db->join('divisions', 'responders.divisionId = divisions.id');
        $this->db->join('companies', 'divisions.companyId = companies.id');
        $this->db->join('responderextradatas as IDNumber', "responders.id = IDNumber.responderId AND IDNumber.paramName = 'PD_IDNumber'");
        $this->db->join('responderextradatas as FirstName', "responders.id = FirstName.responderId AND FirstName.paramName = 'PD_firstName'");
        $this->db->join('responderextradatas as LastName', "responders.id = LastName.responderId AND LastName.paramName = 'PD_lastName'");
        $this->db->join('responderextradatas as CandidateID', "responders.id = CandidateID.responderId AND CandidateID.paramName = 'PD_candidateID'");

        // Filters
        if ($this->input->get('company')) {
            $this->db->where('divisions.companyId', (int)$this->input->get('company'));
        }
        if ($this->input->get('division')) {
            $this->db->where('responders.divisionId', (int)$this->input->get('division'));
        }
        if ($this->input->get('socialDes') !== null && $this->input->get('socialDes') !== '') {
            $this->db->where('feedbacks.socialDes', $this->input->get('socialDes'));
        }
        if ($this->input->get('finalGroup') !== null && $this->input->get('finalGroup') !== '') {
            $this->db->where('feedbacks.finalGroup', $this->input->get('finalGroup'));
        }
        if ($this->input->get('daterange')) {
            $split = explode(' - ', $this->input->get('daterange'));
            $from = @strtotime($split[0]);
            $to = @strtotime($split[1]) + (60 * 60 * 24);
            if ($from && $to) {
                $this->db->where('feedbacks.created >=', $from);
                $this->db->where('feedbacks.created <=', $to);
            }
        }
        if ($this->input->get('freetext')) {
            $text = $this->input->get('freetext');
            $this->db->group_start();
            $this->db->like('FirstName.val', $text);
            $this->db->or_like('LastName.val', $text);
            $this->db->or_like('IDNumber.val', $text);
            $this->db->group_end();
        }

        $this->db->order_by($sortColumn, $sortOrder);

        // Pagination (to avoid memory exhaustion when many rows)
        $perPage = (int)$this->input->get('perPage');
        if ($perPage <= 0 || $perPage > 1000) { $perPage = 100; }
        $page = (int)$this->input->get('page');
        if ($page <= 0) { $page = 1; }
        $offset = ($page - 1) * $perPage;
        $this->db->limit($perPage, $offset);

        // Return compiled SQL string to keep current call-site intact
        $sql = $this->db->get_compiled_select();
        $this->db->reset_query();
        return $sql;
    }

    private function _getCount()
    {
        $sortKey = $this->input->get('sortKey') ? $this->input->get('sortKey') : 'created';
        $sortOrder = strtolower($this->input->get('sortOrder')) === 'asc' ? 'asc' : 'desc';
        $sortMap = array(
            'feedbackId' => 'feedbacks.id',
            'divisionName' => 'divisions.name',
            'firstName' => 'FirstName.val',
            'lastName' => 'LastName.val',
            'candidateId' => 'CandidateID.val',
            'idNumber' => 'IDNumber.val',
            'created' => 'feedbacks.created'
        );
        $sortColumn = isset($sortMap[$sortKey]) ? $sortMap[$sortKey] : 'feedbacks.created';

        $this->db->select('COUNT(*) as total');
        $this->db->from('feedbacks');
        $this->db->join('responders', 'responders.id = feedbacks.responderId');
        $this->db->join('divisions', 'responders.divisionId = divisions.id');
        $this->db->join('companies', 'divisions.companyId = companies.id');
        $this->db->join('responderextradatas as IDNumber', "responders.id = IDNumber.responderId AND IDNumber.paramName = 'PD_IDNumber'");
        $this->db->join('responderextradatas as FirstName', "responders.id = FirstName.responderId AND FirstName.paramName = 'PD_firstName'");
        $this->db->join('responderextradatas as LastName', "responders.id = LastName.responderId AND LastName.paramName = 'PD_lastName'");
        $this->db->join('responderextradatas as CandidateID', "responders.id = CandidateID.responderId AND CandidateID.paramName = 'PD_candidateID'");

        if ($this->input->get('company')) {
            $this->db->where('divisions.companyId', (int)$this->input->get('company'));
        }
        if ($this->input->get('division')) {
            $this->db->where('responders.divisionId', (int)$this->input->get('division'));
        }
        if ($this->input->get('socialDes') !== null && $this->input->get('socialDes') !== '') {
            $this->db->where('feedbacks.socialDes', $this->input->get('socialDes'));
        }
        if ($this->input->get('finalGroup') !== null && $this->input->get('finalGroup') !== '') {
            $this->db->where('feedbacks.finalGroup', $this->input->get('finalGroup'));
        }
        if ($this->input->get('daterange')) {
            $split = explode(' - ', $this->input->get('daterange'));
            $from = @strtotime($split[0]);
            $to = @strtotime($split[1]) + (60 * 60 * 24);
            if ($from && $to) {
                $this->db->where('feedbacks.created >=', $from);
                $this->db->where('feedbacks.created <=', $to);
            }
        }
        if ($this->input->get('freetext')) {
            $text = $this->input->get('freetext');
            $this->db->group_start();
            $this->db->like('FirstName.val', $text);
            $this->db->or_like('LastName.val', $text);
            $this->db->or_like('IDNumber.val', $text);
            $this->db->group_end();
        }

        $this->db->order_by($sortColumn, $sortOrder);
        $row = $this->db->get()->row_array();
        $this->db->reset_query();
        return (int)$row['total'];
    }

    public function export()
    {
        ini_set('display_errors', 1);
       
        set_time_limit(0);
        $query = $this->_getQuery();
        $queryRes = $this->db->query($query);
        $results = $queryRes->result_array();
        $out = array();

        foreach ($results as $result) {
            $decoded = json_decode($result['json'], true);
            parse_str($result['rowData'], $arr);
            $tmp = array(
                'companyName' => $result['companyName'],
                'divisionName' => $result['divisionName'],
                'firstName' => $result['firstName'],
                'lastName' => $result['lastName'],
                'language' => isset($arr['Language']) ? $arr['Language'] : '',
                'candidateId' => $result['candidateId'],
                'idNumber' => $result['idNumber'],
                'created' => @date('d-m-Y', $result['created']),
                'finalGroup' => $result['finalGroup'],
                'socialDes' => $result['socialDes']
            );

            foreach ($arr as $key => $value) {
                if (strpos($key, 'PD') === 0) {
                    $tmp[$key] = $value;
                }
            }

            $tmp['rowData'] = $result['rowData'];
            $tmp['finalGroup'] = $result['finalGroup'];
            $tmp['socialDes'] = $result['socialDes'];
            foreach ($decoded['Dims'] as $key => $dim) {
                $tmp[$key] = $dim['res'];
            }

            $dimensionDataGroup = $this->dimensiondatagroup_m->get_by(array('companyDivisionId' => $result['divisionId']));
            $dims = $this->dimensiondata_m->get_many_by(array(
                'attrGroupId' => $dimensionDataGroup['id']
            ));
            foreach ($dims as $key => $dim) {
                $tmp['dim' . $dim['dimensionId'] . 'Average'] = $dim['average'];
                $tmp['dim' . $dim['dimensionId'] . 'StandardDeviation'] = $dim['standardDeviation'];
            }

            foreach ($arr as $key => $value) {
                if (strpos($key, 'MZA') === 0 || strpos($key, 'SIG') === 0) {
                    $tmp[$key] = $value;
                }
            }


            $out[] = $tmp;
        }

            // Header row
        $final = array_merge(array(array_keys($tmp)), $out);
        $output = fopen("php://output", 'w');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=export.csv');
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));


        foreach ($final as $row) {
            $sanitized = array();
            foreach ($row as $cell) {
                $sanitized[] = sanitize_for_csv_cell((string)$cell);
            }
            fputcsv($output, $sanitized);
        }
        fclose($output);
    }

    // Distribution report (HTML): builds histograms from current admin filters
    public function distribution()
    {
        if (!$this->session->userdata('currentUser')) {
            redirect(fix_link(site_url('welcome/login')));
            return;
        }
        $maxRows = (int)$this->config->item('distribution_max_rows');
        if ($maxRows <= 0) { $maxRows = 3000; }
        $bins = (int)$this->config->item('distribution_bins');
        if ($bins <= 0) { $bins = 12; }
        $rangeMin = -3.0; $rangeMax = 3.0; $binWidth = ($rangeMax - $rangeMin) / $bins;

        // Base results and total
        $query = $this->_getQuery();
        $queryRes = $this->db->query($query);
        $rows = $queryRes->result_array();
        $total = $this->_getCount();

        // Aggregate structures
        $dimsSet = array();
        $overall = array();
        $byFinalGroup = array('1' => array(), '2' => array(), '3' => array());
        $byCompany = array();
        $byDivision = array();
        $rowsUsed = 0;

        foreach ($rows as $r) {
            if ($rowsUsed >= $maxRows) { break; }
            $decoded = json_decode($r['json'], true);
            if (!is_array($decoded) || !isset($decoded['Dims']) || !is_array($decoded['Dims'])) { continue; }
            $finalGroup = isset($r['finalGroup']) ? (string)$r['finalGroup'] : '';
            $companyName = isset($r['companyName']) ? (string)$r['companyName'] : '';
            $divisionName = isset($r['divisionName']) ? (string)$r['divisionName'] : '';

            foreach ($decoded['Dims'] as $dimKey => $dimVal) {
                $z = isset($dimVal['res']) ? (float)$dimVal['res'] : null;
                if ($z === null || !is_numeric($z)) { continue; }
                if ($z < $rangeMin) { $z = $rangeMin; }
                if ($z > $rangeMax) { $z = $rangeMax; }
                $idx = (int)floor(($z - $rangeMin) / $binWidth);
                if ($idx >= $bins) { $idx = $bins - 1; }

                $dimsSet[$dimKey] = true;
                if (!isset($overall[$dimKey])) { $overall[$dimKey] = array_fill(0, $bins, 0); }
                $overall[$dimKey][$idx] += 1;

                if (isset($byFinalGroup[$finalGroup])) {
                    if (!isset($byFinalGroup[$finalGroup][$dimKey])) { $byFinalGroup[$finalGroup][$dimKey] = array_fill(0, $bins, 0); }
                    $byFinalGroup[$finalGroup][$dimKey][$idx] += 1;
                }
                if ($companyName !== '') {
                    if (!isset($byCompany[$companyName])) { $byCompany[$companyName] = array(); }
                    if (!isset($byCompany[$companyName][$dimKey])) { $byCompany[$companyName][$dimKey] = array_fill(0, $bins, 0); }
                    $byCompany[$companyName][$dimKey][$idx] += 1;
                }
                if ($divisionName !== '') {
                    if (!isset($byDivision[$divisionName])) { $byDivision[$divisionName] = array(); }
                    if (!isset($byDivision[$divisionName][$dimKey])) { $byDivision[$divisionName][$dimKey] = array_fill(0, $bins, 0); }
                    $byDivision[$divisionName][$dimKey][$idx] += 1;
                }
            }
            $rowsUsed++;
        }

        $dims = array_keys($dimsSet);
        sort($dims);

        $payload = array(
            'meta' => array(
                'bins' => $bins,
                'range' => array($rangeMin, $rangeMax),
                'maxRows' => $maxRows,
                'rowsUsed' => $rowsUsed,
                'total' => (int)$total,
                'truncated' => ($rowsUsed < (int)$total)
            ),
            'dims' => $dims,
            'overall' => $overall,
            'byFinalGroup' => $byFinalGroup,
            'byCompany' => $byCompany,
            'byDivision' => $byDivision
        );

        $this->load->view('distribution', array('data' => json_encode($payload)));
    }

    // CSV export of distribution (Excel-friendly)
    public function distribution_export()
    {
        if (!$this->session->userdata('currentUser')) {
            redirect(fix_link(site_url('welcome/login')));
            return;
        }
        $maxRows = (int)$this->config->item('distribution_max_rows');
        if ($maxRows <= 0) { $maxRows = 3000; }
        $bins = (int)$this->config->item('distribution_bins');
        if ($bins <= 0) { $bins = 12; }
        $rangeMin = -3.0; $rangeMax = 3.0; $binWidth = ($rangeMax - $rangeMin) / $bins;

        $query = $this->_getQuery();
        $queryRes = $this->db->query($query);
        $rows = $queryRes->result_array();
        $total = $this->_getCount();

        $dimsSet = array();
        $overall = array();
        $byFinalGroup = array('1' => array(), '2' => array(), '3' => array());
        $byCompany = array();
        $byDivision = array();
        $groupCounts = array('overall' => 0, 'fg:1' => 0, 'fg:2' => 0, 'fg:3' => 0);
        $companyCounts = array();
        $divisionCounts = array();
        $rowsUsed = 0;

        foreach ($rows as $r) {
            if ($rowsUsed >= $maxRows) { break; }
            $decoded = json_decode($r['json'], true);
            if (!is_array($decoded) || !isset($decoded['Dims']) || !is_array($decoded['Dims'])) { continue; }
            $finalGroup = isset($r['finalGroup']) ? (string)$r['finalGroup'] : '';
            $companyName = isset($r['companyName']) ? (string)$r['companyName'] : '';
            $divisionName = isset($r['divisionName']) ? (string)$r['divisionName'] : '';
            $groupCounts['overall']++;
            if (isset($groupCounts['fg:'.$finalGroup])) { $groupCounts['fg:'.$finalGroup]++; }
            if ($companyName !== '') { if (!isset($companyCounts[$companyName])) { $companyCounts[$companyName] = 0; } $companyCounts[$companyName]++; }
            if ($divisionName !== '') { if (!isset($divisionCounts[$divisionName])) { $divisionCounts[$divisionName] = 0; } $divisionCounts[$divisionName]++; }

            foreach ($decoded['Dims'] as $dimKey => $dimVal) {
                $z = isset($dimVal['res']) ? (float)$dimVal['res'] : null;
                if ($z === null || !is_numeric($z)) { continue; }
                if ($z < $rangeMin) { $z = $rangeMin; }
                if ($z > $rangeMax) { $z = $rangeMax; }
                $idx = (int)floor(($z - $rangeMin) / $binWidth);
                if ($idx >= $bins) { $idx = $bins - 1; }

                $dimsSet[$dimKey] = true;
                if (!isset($overall[$dimKey])) { $overall[$dimKey] = array_fill(0, $bins, 0); }
                $overall[$dimKey][$idx] += 1;

                if (isset($byFinalGroup[$finalGroup])) {
                    if (!isset($byFinalGroup[$finalGroup][$dimKey])) { $byFinalGroup[$finalGroup][$dimKey] = array_fill(0, $bins, 0); }
                    $byFinalGroup[$finalGroup][$dimKey][$idx] += 1;
                }
                if ($companyName !== '') {
                    if (!isset($byCompany[$companyName])) { $byCompany[$companyName] = array(); }
                    if (!isset($byCompany[$companyName][$dimKey])) { $byCompany[$companyName][$dimKey] = array_fill(0, $bins, 0); }
                    $byCompany[$companyName][$dimKey][$idx] += 1;
                }
                if ($divisionName !== '') {
                    if (!isset($byDivision[$divisionName])) { $byDivision[$divisionName] = array(); }
                    if (!isset($byDivision[$divisionName][$dimKey])) { $byDivision[$divisionName][$dimKey] = array_fill(0, $bins, 0); }
                    $byDivision[$divisionName][$dimKey][$idx] += 1;
                }
            }
            $rowsUsed++;
        }

        // Stream CSV
        $output = fopen("php://output", 'w');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=distribution.csv');
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));

        // Meta
        fputcsv($output, array('meta', 'bins', $bins));
        fputcsv($output, array('meta', 'rangeMin', $rangeMin));
        fputcsv($output, array('meta', 'rangeMax', $rangeMax));
        fputcsv($output, array('meta', 'maxRows', $maxRows));
        fputcsv($output, array('meta', 'rowsUsed', $rowsUsed));
        fputcsv($output, array('meta', 'total', (int)$total));
        fputcsv($output, array());

        // Header for bins
        $header = array('kind','key','dimension','binIndex','binStart','binEnd','count','percent');
        fputcsv($output, $header);

        $dims = array_keys($dimsSet); sort($dims);
        $binStart = function($i) use ($rangeMin,$binWidth){ return $rangeMin + $i*$binWidth; };
        $binEnd = function($i) use ($rangeMin,$binWidth){ return $rangeMin + ($i+1)*$binWidth; };

        // Overall
        foreach ($dims as $dkey) {
            $counts = isset($overall[$dkey]) ? $overall[$dkey] : array_fill(0, $bins, 0);
            for ($i=0;$i<$bins;$i++) {
                $cnt = (int)$counts[$i];
                $pct = $groupCounts['overall'] > 0 ? round(($cnt / $groupCounts['overall']) * 100, 2) : 0;
                fputcsv($output, array('overall','all',$dkey,$i,$binStart($i),$binEnd($i),$cnt,$pct));
            }
        }

        // FinalGroup
        foreach (array('1','2','3') as $fg) {
            foreach ($dims as $dkey) {
                $counts = isset($byFinalGroup[$fg][$dkey]) ? $byFinalGroup[$fg][$dkey] : array_fill(0, $bins, 0);
                for ($i=0;$i<$bins;$i++) {
                    $cnt = (int)$counts[$i];
                    $den = isset($groupCounts['fg:'.$fg]) ? (int)$groupCounts['fg:'.$fg] : 0;
                    $pct = $den > 0 ? round(($cnt / $den) * 100, 2) : 0;
                    fputcsv($output, array('finalGroup',$fg,$dkey,$i,$binStart($i),$binEnd($i),$cnt,$pct));
                }
            }
        }

        // Company
        foreach ($byCompany as $ckey => $map) {
            $den = isset($companyCounts[$ckey]) ? (int)$companyCounts[$ckey] : 0;
            foreach ($dims as $dkey) {
                $counts = isset($map[$dkey]) ? $map[$dkey] : array_fill(0, $bins, 0);
                for ($i=0;$i<$bins;$i++) {
                    $cnt = (int)$counts[$i];
                    $pct = $den > 0 ? round(($cnt / $den) * 100, 2) : 0;
                    fputcsv($output, array('company',$ckey,$dkey,$i,$binStart($i),$binEnd($i),$cnt,$pct));
                }
            }
        }

        // Division
        foreach ($byDivision as $dname => $map) {
            $den = isset($divisionCounts[$dname]) ? (int)$divisionCounts[$dname] : 0;
            foreach ($dims as $dkey) {
                $counts = isset($map[$dkey]) ? $map[$dkey] : array_fill(0, $bins, 0);
                for ($i=0;$i<$bins;$i++) {
                    $cnt = (int)$counts[$i];
                    $pct = $den > 0 ? round(($cnt / $den) * 100, 2) : 0;
                    fputcsv($output, array('division',$dname,$dkey,$i,$binStart($i),$binEnd($i),$cnt,$pct));
                }
            }
        }

        fclose($output);
    }

    public function setRemarks($feedbackId)
    {

        $this->feedback_m->update($feedbackId, array('remarks' => file_get_contents('php://input')));
    }

    public function changePassword()
    {
        $raw = trim((string)file_get_contents('php://input'));
        if ($raw !== '') {
            $this->company_m->update($this->session->userdata('currentUser'), array(
                'lastPasswordChange' => time(),
                'password' => password_hash($raw, PASSWORD_BCRYPT)
            ));
        }
    }

    // Simple DB-backed rate limit (k, window_start, count). Limit applies per (key,window)
    private function _rateLimitAllowed($key, $limit, $windowSeconds)
    {
        try {
            $this->db->query("CREATE TABLE IF NOT EXISTS ratelimits (
                k VARCHAR(100) NOT NULL,
                window_start INT UNSIGNED NOT NULL,
                count INT UNSIGNED NOT NULL DEFAULT 0,
                PRIMARY KEY (k, window_start)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
            $now = time();
            $windowStart = $now - ($now % $windowSeconds);
            $this->db->query(
                "INSERT INTO ratelimits (k, window_start, count) VALUES (?, ?, 1)
                 ON DUPLICATE KEY UPDATE count = count + 1",
                array($key, $windowStart)
            );
            $row = $this->db->query(
                "SELECT count FROM ratelimits WHERE k = ? AND window_start = ?",
                array($key, $windowStart)
            )->row_array();
            return isset($row['count']) ? ((int)$row['count'] <= (int)$limit) : true;
        } catch (\Throwable $e) {
            // On error, fail-open to avoid blocking functionality
            return true;
        }
    }

}