using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;
using System.Web.UI;
using System.Web.UI.WebControls;
using SurveyDataProvider;

namespace SurveyWeb
{
    public partial class Mashovim : System.Web.UI.Page
    {
        protected void Page_Load(object sender, EventArgs e)
        {

        }
        protected void Button1_Click(object sender, EventArgs e)
        {
            if (txt_pass.Text == "mezoo")
            {
                lbl_list.Visible = true;
                lb_mashov.Visible = true;
                btn_showMashov.Visible = true;

                List<Feedback> flist = DL.GetAllFeedbacks();

                lb_mashov.Items.Clear();

                foreach (Feedback f in flist)
                {
                    lb_mashov.Items.Add(f.fileName);
                }
            }
        }
        protected void lb_mashov_SelectedIndexChanged(object sender, EventArgs e)
        {
            
        }

        protected void btn_showMashov_Click(object sender, EventArgs e)
        {
            if (lb_mashov.SelectedIndex != -1)
            {
                ClientScript.RegisterStartupScript(GetType(), "SomeNameForThisScript",
               "window.open('Uploads/" + lb_mashov.SelectedValue + "');", true);
            }
        }
    }
}