# מודל נתונים (ERD טקסטואלי)

- companies(id, parentId, login, password, ...)
- divisions(id, companyId, name)
- dimensiondatagroups(id, attrGroupName, companyDivisionId)
- dimensiondatas(id, dimensionId, attrGroupId, average, standardDeviation, threshold)
- dimensiontypes(id, name, surveyTypeId)
- questions(id, dimId, questionName, ...)
- responders(id, divisionId, gismoId)
- responderextradatas(id, responderId, paramName, val)
- feedbacks(id, responderId, surveyId, rowData, fileName, json, created, remarks, socialDes, finalGroup, url)
- feedbackdims(id, feedbackId, dimId, result)

הערה: מנוע MyISAM — ללא FK/טרנזקציות.
