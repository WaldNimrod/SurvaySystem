<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScoreCalculator
{
    /**
     * Calculates dimensions, persists feedbackdims and returns result structure and logs
     *
     * @param int $feedbackId The feedback id to persist dimension results against
     * @param int $divisionId The division id for resolving the dimension data group
     * @return array [result(array), logs(array), totalDimRes(float), totalDimData(array)]
     */
    public function calculateAndPersist($feedbackId, $divisionId)
    {
        $CI = &get_instance();

        $logs = array();
        $result = array();

        $dimensionDataGroup = $CI->dimensiondatagroup_m->get_by(array('companyDivisionId' => $divisionId));
        $dims = $CI->dimensiondata_m->get_many_by(array('attrGroupId' => $dimensionDataGroup['id']));
        $logs[] = 'Dims count: ' . ($dims ? count($dims) : 0);
        $logs[] = 'Dims content: ' . ($dims ? json_encode($dims) : 'no dims');

        $mazDimRes = 0;
        $sigTotalVal = 0;
        $dimsRes = array();
        $sigDimData = null;
        $totalDimData = null;

        foreach ($dims as $dad) {
            $dimensionType = $CI->dimensiontype_m->get_by(array('id' => $dad['dimensionId']));
            $logs[] = 'Calculating Dim: ' . $dimensionType['name'];

            $questions = $CI->question_m->get_many_by(array('dimId' => $dimensionType['id']));

            if (count($questions) > 0) {
                $logs[] = 'Dim have ' . count($questions) . ' Questions';
                $dimTotalVal = 0;
                $dimTotalCount = 0;
                foreach ($questions as $q) {
                    if ($CI->input->get($q['questionName'])) {
                        ++$dimTotalCount;
                        $dimTotalVal += (int)$CI->input->get($q['questionName']);
                    }
                }
                $logs[] = 'dimTotalCount = ' . $dimTotalCount . ' And dimTotalVal = ' . $dimTotalVal;
                if ($dad['dimensionId'] == 1 || $dad['dimensionId'] == 3 || $dad['dimensionId'] == 4 || $dad['dimensionId'] == 5 || $dad['dimensionId'] == 6) {
                    $sigTotalVal += $dimTotalVal;
                }
                $responderAverageRes = $dimTotalVal / max(1, $dimTotalCount);
                $logs[] = 'responderDimAverage = (dimTotalVal / dimTotalCount) = (' . $dimTotalVal . ' / ' . max(1, $dimTotalCount) . ') = ' . $responderAverageRes;
                $dimRes = ($responderAverageRes - $dad['average']) / $dad['standardDeviation'];
                $logs[] = 'dimRes = (responderAverageRes - dad.average) / dad.standardDeviation = (' . $responderAverageRes . ' - ' . $dad['average'] . ') / ' . $dad['standardDeviation'] . ' = ' . $dimRes;
                $CI->feedbackdim_m->insert(array(
                    'feedbackId' => $feedbackId,
                    'dimId' => $dimensionType['id'],
                    'result' => $dimRes
                ));
                $dimsRes[] = array($dad, $dimRes);
                if ($dimensionType['id'] == 9) {
                    $mazDimRes = $dimRes;
                }
            } else {
                if ($dimensionType['id'] == 7) {
                    $sigDimData = $dad;
                }
                if ($dimensionType['id'] == 8) {
                    $totalDimData = $dad;
                }
            }
        }

        // Custom dims
        $logs[] = 'Calculating SigTotal Dim';
        $sigTotalAverage = $sigTotalVal / 56;
        $logs[] = 'sigTotalAverage = sigTotalVal / 56 = ' . $sigTotalVal . ' / 56 = ' . $sigTotalAverage;
        $sigDimRes = ($sigTotalAverage - $sigDimData['average']) / $sigDimData['standardDeviation'];
        $logs[] = 'sigDimRes = (sigTotalAverage - sigDimData.average) / sigDimData.standardDeviation = (' . $sigTotalAverage . ' - ' . $sigDimData['average'] . ') / ' . $sigDimData['standardDeviation'] . ' = ' . $sigDimRes;
        $CI->feedbackdim_m->insert(array(
            'feedbackId' => $feedbackId,
            'dimId' => $sigDimData['dimensionId'],
            'result' => $sigDimRes
        ));
        $dimsRes[] = array($sigDimData, $sigDimRes);

        $logs[] = 'Calculating sumTotal dim';
        $totalDimRes = ($sigDimRes + $mazDimRes) / 2;
        $logs[] = 'totalDimRes = (sigDimRes + mazDimRes) / 2 = (' . $sigDimRes . ' + ' . $mazDimRes . ') / 2 = ' . $totalDimRes;
        $CI->feedbackdim_m->insert(array(
            'feedbackId' => $feedbackId,
            'dimId' => $totalDimData['dimensionId'],
            'result' => $totalDimRes
        ));
        $dimsRes[] = array($totalDimData, $totalDimRes);

        $result['Dims'] = array();
        foreach ($dimsRes as $dim) {
            $temp = array('res' => $dim[1]);
            if ($dim[0]['threshold']) {
                $temp['threshold'] = $dim[0]['threshold'];
            }
            $dimensionType = $CI->dimensiontype_m->get_by(array('id' => $dim[0]['dimensionId']));
            $result['Dims']['dim_' . $dimensionType['name']] = $temp;
        }

        return array($result, $logs, $totalDimRes, $totalDimData);
    }

    /**
     * Calculate scores from a provided params array (keys like SIG_*, MZA_*), without persisting
     * Returns same tuple as calculateAndPersist: [result, logs, totalDimRes, totalDimData]
     */
    public function calculateFromParams(array $params, $divisionId)
    {
        $CI = &get_instance();

        $logs = array();
        $result = array();

        $dimensionDataGroup = $CI->dimensiondatagroup_m->get_by(array('companyDivisionId' => $divisionId));
        $dims = $CI->dimensiondata_m->get_many_by(array('attrGroupId' => $dimensionDataGroup['id']));
        $logs[] = 'Dims count: ' . ($dims ? count($dims) : 0);
        $logs[] = 'Dims content: ' . ($dims ? json_encode($dims) : 'no dims');

        $mazDimRes = 0;
        $sigTotalVal = 0;
        $dimsRes = array();
        $sigDimData = null;
        $totalDimData = null;

        foreach ($dims as $dad) {
            $dimensionType = $CI->dimensiontype_m->get_by(array('id' => $dad['dimensionId']));
            $logs[] = 'Calculating Dim: ' . $dimensionType['name'];

            $questions = $CI->question_m->get_many_by(array('dimId' => $dimensionType['id']));

            if (count($questions) > 0) {
                $logs[] = 'Dim have ' . count($questions) . ' Questions';
                $dimTotalVal = 0;
                $dimTotalCount = 0;
                foreach ($questions as $q) {
                    $key = $q['questionName'];
                    if (isset($params[$key]) && $params[$key] !== '') {
                        ++$dimTotalCount;
                        $dimTotalVal += (int)$params[$key];
                    }
                }
                $logs[] = 'dimTotalCount = ' . $dimTotalCount . ' And dimTotalVal = ' . $dimTotalVal;
                if ($dad['dimensionId'] == 1 || $dad['dimensionId'] == 3 || $dad['dimensionId'] == 4 || $dad['dimensionId'] == 5 || $dad['dimensionId'] == 6) {
                    $sigTotalVal += $dimTotalVal;
                }
                $responderAverageRes = $dimTotalVal / max(1, $dimTotalCount);
                $logs[] = 'responderDimAverage = (dimTotalVal / dimTotalCount) = (' . $dimTotalVal . ' / ' . max(1, $dimTotalCount) . ') = ' . $responderAverageRes;
                $sd = (float)$dad['standardDeviation'];
                $dimRes = ($sd != 0.0) ? (($responderAverageRes - $dad['average']) / $sd) : 0.0;
                $logs[] = 'dimRes = (responderAverageRes - dad.average) / dad.standardDeviation = (' . $responderAverageRes . ' - ' . $dad['average'] . ') / ' . ($sd ?: '0') . ' = ' . $dimRes;
                $dimsRes[] = array($dad, $dimRes);
                if ($dimensionType['id'] == 9) {
                    $mazDimRes = $dimRes;
                }
            } else {
                if ($dimensionType['id'] == 7) {
                    $sigDimData = $dad;
                }
                if ($dimensionType['id'] == 8) {
                    $totalDimData = $dad;
                }
            }
        }

        // Custom dims
        $logs[] = 'Calculating SigTotal Dim';
        $sigTotalAverage = $sigTotalVal / 56;
        $logs[] = 'sigTotalAverage = sigTotalVal / 56 = ' . $sigTotalVal . ' / 56 = ' . $sigTotalAverage;
        $sdSig = (float)$sigDimData['standardDeviation'];
        $sigDimRes = ($sdSig != 0.0) ? (($sigTotalAverage - $sigDimData['average']) / $sdSig) : 0.0;
        $logs[] = 'sigDimRes = (sigTotalAverage - sigDimData.average) / sigDimData.standardDeviation = (' . $sigTotalAverage . ' - ' . $sigDimData['average'] . ') / ' . ($sdSig ?: '0') . ' = ' . $sigDimRes;
        $dimsRes[] = array($sigDimData, $sigDimRes);

        $logs[] = 'Calculating sumTotal dim';
        $totalDimRes = ($sigDimRes + $mazDimRes) / 2;
        $logs[] = 'totalDimRes = (sigDimRes + mazDimRes) / 2 = (' . $sigDimRes . ' + ' . $mazDimRes . ') / 2 = ' . $totalDimRes;
        $dimsRes[] = array($totalDimData, $totalDimRes);

        $result['Dims'] = array();
        foreach ($dimsRes as $dim) {
            $temp = array('res' => $dim[1]);
            if ($dim[0]['threshold']) {
                $temp['threshold'] = $dim[0]['threshold'];
            }
            $dimensionType = $CI->dimensiontype_m->get_by(array('id' => $dim[0]['dimensionId']));
            $result['Dims']['dim_' . $dimensionType['name']] = $temp;
        }

        return array($result, $logs, $totalDimRes, $totalDimData);
    }
}


