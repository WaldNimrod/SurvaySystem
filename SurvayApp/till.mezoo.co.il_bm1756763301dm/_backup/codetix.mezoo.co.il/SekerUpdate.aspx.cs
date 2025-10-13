using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using SurveyDataProvider;
using System.Collections;
using System.Net;

namespace SurveyWeb
{
    public partial class SekerUpdate : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            try
            {
                WebClient Page = new WebClient();

                string rowData = "";

                Logger.LogLine(Server.MapPath("Logs/log.txt"), "Getting a Seker Request");

                Logger.LogLine(Server.MapPath("Logs/log.txt"), "RowData:");
                foreach (string key in Request.QueryString.AllKeys)
                {
                    rowData += key + "=" + Request.QueryString[key] + "&";
                    Logger.LogLine(Server.MapPath("Logs/log.txt"), key + " = " + Request.QueryString[key]);
                }
                rowData = (rowData.Contains('&'))?rowData.Substring(0, rowData.LastIndexOf('&')): rowData;

                Logger.LogLine(Server.MapPath("Logs/log.txt"), "rowData is: " + rowData);

                //Creating the json response string
                string jsonRes = "{";

                //Making a hashTable from the rowDataStr
                Hashtable rowTable = RowDataToTable(rowData);

                //Taking the necessary Data from the rowTable
                int companyID = int.Parse(rowTable["CompanyId"].ToString());
                int surveyID = int.Parse(rowTable["SurveyId"].ToString());
                string gismoResponderID = rowTable["ResponseId"].ToString();
                int attrGroup = DL.GetAttrGroupIDByName(rowTable["attr_group"].ToString());
                int divisionID = int.Parse(rowTable["divisionId"].ToString());

                string fileName = DateTime.Now.ToString("dd-MM-yyyy--HH-mm-ss") + "_" + 
                                  ((rowTable.ContainsKey("PD_firstName"))?rowTable["PD_firstName"].ToString():"*") + "_" +
                                  ((rowTable.ContainsKey("PD_lastName")) ? rowTable["PD_lastName"].ToString() : "*") + ".html";
                rowData += "&FileName=" + fileName;

                //Creating the responder in DB
                int responderID = DL.CreateResponder(divisionID, gismoResponderID);
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "New responderID is: " + responderID);

                //***Calculating the Dimentions of the Survey***
                //Creating a new feedback record
                int feedbackID = DL.InsertFeedback(responderID, surveyID, rowData, fileName);
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "New FeedbackID is: " + feedbackID);

                //***Saving the responder Personal Details***
                //Taking all the Keys that starts with "PD" (for Personal Details Values)

                jsonRes += "\"PD\": { ";

                foreach (string key in rowTable.Keys)
                {
                    if (key.StartsWith("PD_"))
                    {
                        string PDTitle = DL.InsertResponderPDValue(responderID, key, rowTable[key].ToString());
                        if (PDTitle != null && PDTitle != "")
                        {
                            //Add PDTitle to the response
                            jsonRes += "\"" + key + "\": \"" + PDTitle + ";" + rowTable[key].ToString() + "\"";
                            jsonRes += " , ";
                        }
                    }
                }

                jsonRes = jsonRes.Substring(0, jsonRes.LastIndexOf(','));
                jsonRes += " },";

                //Getting the Dimentions by Survey
                List<DimAttrData> Dims = DL.GetDimentionsBySurveyID(surveyID, attrGroup);
                Hashtable DimsRes = new Hashtable();

                DimAttrData SigDimData = null;
                DimAttrData MazDimData = null;
                double MazDimRes = 0;
                DimAttrData TotalDimData = null;

                double sigTotalVal = 0;

                //Getting the questions names (keys) of every Dimention and calculating its value
                foreach (DimAttrData dad in Dims)
                {
                    Logger.LogLine(Server.MapPath("Logs/log.txt"), "Calculating Dim: " + DL.GetDimName(dad.dim_id));
                    if (dad.dim_id == 9)
                    {
                        MazDimData = dad;
                    }

                    List<Question> qs = DL.GetQuestionsOfDim(dad.dim_id);

                    //Check if have questions to this dimention
                    if (qs.Count > 0)
                    {
                        Logger.LogLine(Server.MapPath("Logs/log.txt"), "Dim have " + qs.Count + " Questions");
                        double dimTotalVal = 0; // For the total sum of questions answers
                        double dimTotalCount = 0; //How many question has been answered

                        //Calculating the average of Dim for this responder answers
                        foreach (Question q in qs)
                        {
                            //check if the responder has been aswered this question
                            if (rowTable[q.question_name] != null && rowTable[q.question_name].ToString() != "")
                            {
                                dimTotalCount++;
                                dimTotalVal += int.Parse(rowTable[q.question_name].ToString());
                            }
                        }
                        Logger.LogLine(Server.MapPath("Logs/log.txt"), "DimTotalCount = " + dimTotalCount + " And DimTotalVal = " + dimTotalVal);

                        if (dad.dim_id == 1 || dad.dim_id == 3 || dad.dim_id == 4 || dad.dim_id == 5 || dad.dim_id == 6)
                        {
                            sigTotalVal += dimTotalVal;
                        }

                        //The Averege of the Responder: (Sum of values) / (sum of answered questions)
                        double responderAveregeRes = dimTotalVal / dimTotalCount;
                        Logger.LogLine(Server.MapPath("Logs/log.txt"), "ResponderDimAverege = (dimTotalVal / dimTotalCount) = (" + dimTotalVal + " / " + dimTotalCount + ") = " + responderAveregeRes);

                        //The Resoult of a dim: (ResponderAvarege - DimAverege) / DimStandardDeviation
                        double DimRes = (responderAveregeRes - dad.average) / dad.standard_deviation;
                        Logger.LogLine(Server.MapPath("Logs/log.txt"), "DimRes = (responderAveregeRes - dad.average) / dad.standard_deviation = (" + responderAveregeRes + " - " + dad.average + ") / " + dad.standard_deviation + " = " + DimRes);

                        //Saving the result to the DB
                        DL.InsertFeedbackDimData(feedbackID, dad.dim_id, DimRes);
                        DimsRes.Add(dad, DimRes);

                        if (dad.dim_id == 9)
                        {
                            MazDimRes = DimRes;
                        }

                    }
                    else
                    {
                        //No questions for this dimention (a costum dimention)
                        if (dad.dim_id == 7)
                        {
                            SigDimData = dad;
                        }
                        if (dad.dim_id == 8)
                        {
                            TotalDimData = dad;
                        }
                    }
                }

                //Calculate Custom dims
                //Signonot Total
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "Calculating SigTotal Dim");
                double sigTotalAverege = sigTotalVal / ((double)56);
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "sigTotalAverege = sigTotalVal / ((double)56) = " + sigTotalVal + " / 56) = " + sigTotalAverege);
                double SigDimRes = (sigTotalAverege - SigDimData.average) / SigDimData.standard_deviation;
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "SigDimRes = (sigTotalAverege - SigDimData.average) / SigDimData.standard_deviation = (" + sigTotalAverege + " - " + SigDimData.average + ") / " + SigDimData.standard_deviation + " = " + SigDimRes);

                DL.InsertFeedbackDimData(feedbackID, SigDimData.dim_id, SigDimRes);
                DimsRes.Add(SigDimData, SigDimRes);

                //SumTotal
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "Calculating SumTotal Dim");
                double TotalDimRes = (SigDimRes + MazDimRes) / ((double)2);
                Logger.LogLine(Server.MapPath("Logs/log.txt"), "TotalDimRes = (SigDimRes + MazDimRes) / ((double)2) = (" + SigDimRes + " + " + MazDimRes + ") / 2 = " + TotalDimRes);

                DL.InsertFeedbackDimData(feedbackID, TotalDimData.dim_id, TotalDimRes);
                DimsRes.Add(TotalDimData, TotalDimRes);

                jsonRes += "\"Dims\": { ";

                foreach (DimAttrData dim in DimsRes.Keys)
                {
                    jsonRes += "\"dim_" + DL.GetDimName(dim.dim_id) + "\": { \"res\" : \"" + DimsRes[dim] + "\"";
                    if (dim.threshold != null)
                    {
                        jsonRes += " , \"threshold\": \"" + dim.threshold.ToString() + "\"";
                    }
                    jsonRes += "}";
                    jsonRes += " , ";
                }

                jsonRes = jsonRes.Substring(0, jsonRes.LastIndexOf(','));
                jsonRes += " },";

                jsonRes += "\"Social_desirability_ReRun\": \"" + ((rowTable["Social_desirability_ReRun"].ToString() == "") ? "false" : rowTable["Social_desirability_ReRun"].ToString()) + "\"";
                jsonRes += ",";

                jsonRes = jsonRes.Substring(0, jsonRes.LastIndexOf(','));
                jsonRes += "}";

                //TextData.InnerText = jsonRes;

                String strPathAndQuery = HttpContext.Current.Request.Url.PathAndQuery;
                String strUrl = HttpContext.Current.Request.Url.AbsoluteUri.Replace(strPathAndQuery, "/");
                Page.DownloadFile(strUrl + "FeedbackTemplates/yoshra1.aspx?jsonStr=" + Server.UrlEncode(jsonRes), Server.MapPath("Uploads\\" + fileName));
            }
            catch (Exception ex)
            {
                Logger.LogError(Server.MapPath("Logs/log.txt"), "Exeption on calc", ex);
            }
        }

        private Hashtable RowDataToTable(string rowData)
        {
            Hashtable ht = new Hashtable();

            int nextKeyIndx = 0;

            while (nextKeyIndx != -1)
            {
                int keyFinIndx = rowData.IndexOf("=", nextKeyIndx) - 1;
                string keyName = rowData.Substring(nextKeyIndx, keyFinIndx - nextKeyIndx + 1);

                int valStartIndx = keyFinIndx + 2;
                int valFinIndx = rowData.IndexOf("&", valStartIndx) - 1;
                valFinIndx = (valFinIndx < 0) ? rowData.Length - 1 : valFinIndx;

                string val = rowData.Substring(valStartIndx, valFinIndx - valStartIndx + 1);

                ht.Add(keyName, val);

                nextKeyIndx = rowData.IndexOf("&", valFinIndx + 1) + 1;
                if (nextKeyIndx == 0)
                {
                    nextKeyIndx = -1;
                }
            }

            return ht;
        }
    }
}