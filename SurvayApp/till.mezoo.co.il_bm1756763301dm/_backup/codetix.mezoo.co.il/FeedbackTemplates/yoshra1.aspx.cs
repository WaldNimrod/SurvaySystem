using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using SurveyDataProvider;
using System.Collections;

namespace SurveyWeb.FeedbackTemplates
{
    public partial class yoshra1 : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {
            /*string rowData = "ser=null&SurveyId=1366688&ResponseId=1386157813_529f16f50c9af6.30863108&divisionId=1&" +
                             "PD_firstName=ישראל&PD_lastName=ישראלי&PD_IDNumber=12345678&PD_routeName=Programmers&attr_group=Programmers&PD_candidateID=3-000419&PD_organizationName=DEMO3&CompanyId=10&" +
                             "MZA_E1=1&MZA_E2=4&Social_desirability_ReRun=&MZA_1=1&MZA_2=8&MZA_3=8&MZA_4=7&MZA_5=8&MZA_6=8&MZA_7=4&MZA_8=2&MZA_9=6&MZA_10=4&MZA_11=7&MZA_12=2&MZA_13=8&MZA_14=6&MZA_15=3" +
                             "&SIG_E1=1&SIG_E2=2&SIG_E3=3&SIG_E4=4&SIG_1=2&SIG_2=2&SIG_3=4&SIG_4=2&SIG_5=2&SIG_6=1&SIG_7=1&SIG_8=3&SIG_9=2&SIG_10=4&SIG_11=2&SIG_12=2&SIG_13=4&SIG_14=3&SIG_15=3&SIG_16=1" + 
                             "&SIG_17=3&SIG_18=3&SIG_19=4&SIG_20=3&SIG_21=2&SIG_22=4&SIG_23=3&SIG_24=3&SIG_25=4&SIG_26=2&SIG_27=3&SIG_28=4&SIG_29=3&SIG_30=&SIG_31=2&SIG_32=4&SIG_33=3&SIG_34=2&SIG_35=" +
                             "&SIG_36=1&SIG_37=2&SIG_38=3&SIG_39=1&SIG_40=2&SIG_41=&SIG_42=3&SIG_43=1&SIG_44=3&SIG_45=2&SIG_46=4&SIG_47=2&SIG_48=3&SIG_49=1&SIG_50=3&SIG_51=3&SIG_52=1&SIG_53=2&SIG_54=2&SIG_55=4&SIG_56=2" +
                             "&Question_SKU=264&Language=English";*/

            string jsonRes = Request.QueryString["jsonStr"];

            TextData.InnerText = jsonRes;
        }
    }
}