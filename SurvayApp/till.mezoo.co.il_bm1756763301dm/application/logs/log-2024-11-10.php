<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2024-11-10 09:49:56 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'אג'רה %' OR LastName.val LIKE '%חג'אג'רה %' OR IDNumber.val LIKE '%...' at line 27 - Invalid query: 
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
                    INNER JOIN responderextradatas as IDNumber ON responders.id = IDNumber.responderId AND IDNumber.paramName = 'PD_IDNumber'
                    INNER JOIN responderextradatas as FirstName ON responders.id = FirstName.responderId AND FirstName.paramName = 'PD_firstName'
                    INNER JOIN responderextradatas as LastName ON responders.id = LastName.responderId AND LastName.paramName = 'PD_lastName'
                    INNER JOIN responderextradatas as CandidateID ON responders.id = CandidateID.responderId AND CandidateID.paramName = 'PD_candidateID'
                WHERE created BETWEEN 1725138000 AND 1731276000 AND (FirstName.val LIKE '%חג'אג'רה %' OR LastName.val LIKE '%חג'אג'רה %' OR IDNumber.val LIKE '%חג'אג'רה %')
                ORDER BY created desc

            
ERROR - 2024-11-10 09:49:56 --> Severity: error --> Exception: Call to a member function result_array() on bool /home/tillmezo/domains/till.mezoo.co.il/public_html/application/controllers/Welcome.php 433
ERROR - 2024-11-10 09:50:42 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'אג'רה%' OR LastName.val LIKE '%חג'אג'רה%' OR IDNumber.val LIKE '%ח...' at line 27 - Invalid query: 
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
                    INNER JOIN responderextradatas as IDNumber ON responders.id = IDNumber.responderId AND IDNumber.paramName = 'PD_IDNumber'
                    INNER JOIN responderextradatas as FirstName ON responders.id = FirstName.responderId AND FirstName.paramName = 'PD_firstName'
                    INNER JOIN responderextradatas as LastName ON responders.id = LastName.responderId AND LastName.paramName = 'PD_lastName'
                    INNER JOIN responderextradatas as CandidateID ON responders.id = CandidateID.responderId AND CandidateID.paramName = 'PD_candidateID'
                WHERE created BETWEEN 1725138000 AND 1731276000 AND (FirstName.val LIKE '%חג'אג'רה%' OR LastName.val LIKE '%חג'אג'רה%' OR IDNumber.val LIKE '%חג'אג'רה%')
                ORDER BY created desc

            
ERROR - 2024-11-10 09:50:42 --> Severity: error --> Exception: Call to a member function result_array() on bool /home/tillmezo/domains/till.mezoo.co.il/public_html/application/controllers/Welcome.php 433
ERROR - 2024-11-10 15:41:22 --> Severity: error --> Exception: Too few arguments to function Welcome::setRemarks(), 0 passed in /home/tillmezo/domains/till.mezoo.co.il/public_html/system/core/CodeIgniter.php on line 529 and exactly 1 expected /home/tillmezo/domains/till.mezoo.co.il/public_html/application/controllers/Welcome.php 624
