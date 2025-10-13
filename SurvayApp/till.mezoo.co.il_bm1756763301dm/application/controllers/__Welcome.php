<?php
defined('BASEPATH') OR exit('No direct script access allowed');

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

        $dimensionDataGroup = $this->dimensiondatagroup_m->get_by(array('companyDivisionId' => $this->input->get('divisionId')));
        $dims = $this->dimensiondata_m->get_many_by(array(
            'attrGroupId' => $dimensionDataGroup['id']
        ));
        $mazDimRes = 0;
        $sigTotalVal = 0;
        $dimsRes = array();

        foreach ($dims as $dad) {

            $dimensionType = $this->dimensiontype_m->get_by(array('id' => $dad['dimensionId']));

            $logs[] = 'Calculating Dim: ' . $dimensionType['name'];
            //Getting the questions names (keys) of every Dimention and calculating its value
            $questions = $this->question_m->get_many_by(array(
                'dimId' => $dimensionType['id']
            ));

            //Check if have questions to this dimention
            if (count($questions) > 0) {
                $logs[] = 'Dim have ' . count($questions) . ' Questions';
                $dimTotalVal = 0; // For the total sum of questions answers
                $dimTotalCount = 0; //How many question has been answered
                //Calculating the average of Dim for this responder answers
                foreach ($questions as $q) {

                    // check if the responder has been aswered this question
                    if ($this->input->get($q['questionName'])) {
                        ++$dimTotalCount;
                        $dimTotalVal += (int)$this->input->get($q['questionName']);
                    }
                }
                $logs[] = 'dimTotalCount = ' . $dimTotalCount . ' And dimTotalVal = ' . $dimTotalVal;
                if ($dad['dimensionId'] == 1 || $dad['dimensionId'] == 3 || $dad['dimensionId'] == 4 || $dad['dimensionId'] == 5 || $dad['dimensionId'] == 6) {
                    $sigTotalVal += $dimTotalVal;
                }
                //The Average of the Responder: (Sum of values) / (sum of answered questions)
                $responderAverageRes = $dimTotalVal / $dimTotalCount;
                $logs[] = 'responderDimAverage = (dimTotalVal / dimTotalCount) = (' . $dimTotalVal . ' / ' . $dimTotalCount . ') = ' . $responderAverageRes;
                //The Resoult of a dim: (ResponderAvarege - DimAverege) / DimStandardDeviation
                $dimRes = ($responderAverageRes - $dad['average']) / $dad['standardDeviation'];
                $logs[] = 'dimRes = (responderAverageRes - dad.average) / dad.standardDeviation = (' . $responderAverageRes . ' - ' . $dad['average'] . ') / ' . $dad['standardDeviation'] . ' = ' . $dimRes;
                //Saving the result to the DB
                $feedbackDimId = $this->feedbackdim_m->insert(array(
                    'feedbackId' => $feedbackId,
                    'dimId' => $dimensionType['id'],
                    'result' => $dimRes
                ));
                $dimsRes[] = array($dad, $dimRes);
                if ($dimensionType['id'] == 9) {
                    $mazDimRes = $dimRes;
                }


            } else {
                //No questions for this dimention (a costum dimention)

                if ($dimensionType['id'] == 7) {
                    $sigDimData = $dad;
                }
                if ($dimensionType['id'] == 8) {
                    $totalDimData = $dad;
                }
            }
        }
        //Calculate Custom dims
        //Signonot Total
        $logs[] = 'Calculating SigTotal Dim';

        $sigTotalAverage = $sigTotalVal / 56;
        $logs[] = 'sigTotalAverage = sigTotalVal / 56 = ' . $sigTotalVal . ' / 56 = ' . $sigTotalAverage;

        $sigDimRes = ($sigTotalAverage - $sigDimData['average']) / $sigDimData['standardDeviation'];
        $logs[] = 'sigDimRes = (sigTotalAverage - sigDimData.average) / sigDimData.standardDeviation = (' . $sigTotalAverage . ' - ' . $sigDimData['average'] . ') / ' . $sigDimData['standardDeviation'] . ' = ' . $sigDimRes;
        $feedbackDimId = $this->feedbackdim_m->insert(array(
            'feedbackId' => $feedbackId,
            'dimId' => $sigDimData['dimensionId'],
            'result' => $sigDimRes
        ));
        $dimsRes[] = array($sigDimData, $sigDimRes);
        // SumTotal
        $logs[] = 'Calculating sumTotal dim';

        $totalDimRes = ($sigDimRes + $mazDimRes) / 2;
        $logs[] = 'totalDimRes = (sigDimRes + mazDimRes) / 2 = (' . $sigDimRes . ' + ' . $mazDimRes . ') / 2 = ' . $totalDimRes;
        $feedbackDimId = $this->feedbackdim_m->insert(array(
            'feedbackId' => $feedbackId,
            'dimId' => $totalDimData['dimensionId'],
            'result' => $totalDimRes
        ));
        $dimsRes[] = (array($totalDimData, $totalDimRes));
        $result['Dims'] = array();
        foreach ($dimsRes as $dim) {
            $temp = array(
                'res' => $dim[1]
            );
            if ($dim[0]['threshold']) {
                $temp['threshold'] = $dim[0]['threshold'];
            }

            $dimensionType = $this->dimensiontype_m->get_by(array('id' => $dim[0]['dimensionId']));

            $result['Dims']['dim_' . $dimensionType['name']] = $temp;
        }
        $result['Social_desirability_ReRun'] = $this->input->get('Social_desirability_ReRun');


        $response = array();
        $response['hasError'] = false;


        $company = $this->company_m->get($this->input->get('CompanyId'));
        $adminCompany = $this->company_m->get_by(array('login' => 'admin'));
        if (!$company) {
            $response['hasError'] = true;
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
            if ($existingIdNumber) {
                $response['hasError'] = true;
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
            if (!$dimensionDataGroup) {
                $response['hasError'] = true;
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

        $this->feedback_m->update($feedbackId, array(
            'json' => json_encode($result),
            'socialDes' => strtolower($result['Social_desirability_ReRun']),
            'finalGroup' => $finalGroup
        ));

        if ($_SERVER['HTTP_HOST'] === 'localhost') {
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
        $error = false;
        if ($this->input->post('username') && $this->input->post('password')) {
            $company = $this->company_m->get_by(array(
                'login' => $this->input->post('username'),
                'password' => $this->input->post('password')
            ));
            if (!$company) {
                $error = true;
                $this->log_m->insert(array(
                    'username' => $this->input->post('username'),
                    'created' => @date('Y-m-d H:i:s'),
                    'result' => 'No'
                ));
            } else {
                if (!$company['lastPasswordChange']) {
                    $this->company_m->update($company['id'], array('lastPasswordChange' => time()));
                }
                $this->log_m->insert(array(
                    'username' => $this->input->post('username'),
                    'created' => @date('Y-m-d H:i:s'),
                    'result' => 'Yes'
                ));
                $this->session->set_userdata('currentUser', $company['id']);
                redirect(fix_link(site_url('welcome/admin')));
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

            foreach ($data['companies'] as $key => $company) {
                $data['divisions'] = $data['divisions'] + $this->_getDivisions($company['id']);
            }
            if (!$data['admin']) {
                unset($data['companies']);
            }
            if (!$this->input->get('daterange')) {
                $_GET['daterange'] = @date("d.m.Y", strtotime("-7 day")) . ' - ' . @date("d.m.Y");
            }

            $query = $this->_getQuery();


            $queryRes = $this->db->query($query);
            $data['results'] = $queryRes->result_array();
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
        $params = array();
        parse_str($feedback['rowData'], $params);
        $array['PD']['PD_lang'] = 'שפה;'.$params['Language'];
        $companyId = $array['CompanyId'];
        if (isset($_GET['d'])) {
            header('Content-disposition: attachment; filename=' . $_GET['fileName'] . '.html');
            header('Content-type: text/html');
        }

        $final = json_encode($array);
        $this->load->view('report', array('result' => $final, 'companyId' => $companyId));
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
        if ($this->input->get('sortKey')) {
            $_GET['sortKey'] = $this->input->get('sortKey');
        } else {
            $_GET['sortKey'] = 'created';
        }
        if ($this->input->get('sortOrder')) {
            $_GET['sortOrder'] = $this->input->get('sortOrder');
        } else {
            $_GET['sortOrder'] = 'desc';
        }
        $where = array();
        if ($this->input->get('company')) {
            $where[] = 'companyId = ' . $this->input->get('company');
        }
        if ($this->input->get('division')) {
            $where[] = 'divisionId = ' . $this->input->get('division');
        }
        if ($this->input->get('socialDes')) {
            $where[] = 'feedbacks.socialDes = \'' . $this->input->get('socialDes') . '\'';
        }
        if ($this->input->get('finalGroup')) {
            $where[] = 'feedbacks.finalGroup = \'' . $this->input->get('finalGroup') . '\'';
        }
        if ($this->input->get('daterange')) {
            $split = explode(' - ', $this->input->get('daterange'));
            $from = @strtotime($split[0]);
            $to = @strtotime($split[1]) + (60 * 60 * 24);
            $where[] = 'created BETWEEN ' . $from . ' AND ' . $to;
        }
        if ($this->input->get('freetext')) {
            $where[] = '(FirstName.val LIKE \'%' . $this->input->get('freetext') . '%\' OR LastName.val LIKE \'%' . $this->input->get('freetext') . '%\' OR IDNumber.val LIKE \'%' . $this->input->get('freetext') . '%\')';
        }
        return '
                SELECT
                    responders.id as responderId,
                    feedbacks.id as feedbackId,
                    feedbacks.finalGroup as finalGroup,
                    feedbacks.fileName as fileName,
                    feedbacks.socialDes as socialDes,
                    feedbacks.remarks as remarks,
                    responders.divisionId as divisionId,
                    feedbacks.json as json,
                    divisions.name as divisionName,
                    divisions.companyId as companyId,
                    companies.login as companyName,
                    feedbacks.created as created,
                    IDNumber.val as idNumber,
                    FirstName.val as firstName,
                    LastName.val as lastName,
                    CandidateID.val as candidateId,
                    feedbacks.rowData as rowData
                FROM feedbacks
                    INNER JOIN responders ON responders.id = feedbacks.responderId
                    INNER JOIN divisions ON responders.divisionId = divisions.id
                    INNER JOIN companies ON divisions.companyId = companies.id
                    INNER JOIN responderextradatas as IDNumber ON responders.id = IDNumber.responderId AND IDNumber.paramName = \'PD_IDNumber\'
                    INNER JOIN responderextradatas as FirstName ON responders.id = FirstName.responderId AND FirstName.paramName = \'PD_firstName\'
                    INNER JOIN responderextradatas as LastName ON responders.id = LastName.responderId AND LastName.paramName = \'PD_lastName\'
                    INNER JOIN responderextradatas as CandidateID ON responders.id = CandidateID.responderId AND CandidateID.paramName = \'PD_candidateID\'
                ' . (count($where) > 0 ? 'WHERE ' . implode(' AND ', $where) : '') . '
                ORDER BY ' . $_GET['sortKey'] . ' ' . $_GET['sortOrder'] . '

            ';
    }

    public function export()
    {

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
                'language' => $arr['Language'],
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

        $final = array_merge(array(array_keys($tmp)), $out);
        $output = fopen("php://output", 'w');
        header('Content-Encoding: UTF-8');
        header('Content-type: text/csv; charset=UTF-8');
        header('Content-Disposition: attachment; filename=export.csv');
        fputs($output, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));


        foreach ($final as $row) {
            fputcsv($output, $row);
        }
        fclose($output);
    }

    public function setRemarks($feedbackId)
    {

        $this->feedback_m->update($feedbackId, array('remarks' => file_get_contents('php://input')));
    }

    public function changePassword()
    {

        $this->company_m->update($this->session->userdata('currentUser'), array('lastPasswordChange' => time(), 'password' => file_get_contents('php://input')));
    }

}
