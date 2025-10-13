<%@ Page Language="C#" AutoEventWireup="true" CodeBehind="Mashovim.aspx.cs" Inherits="SurveyWeb.Mashovim" %>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head id="Head1" runat="server">
    <title></title>
</head>
<body>
    <form id="form1" runat="server">
    <div>
    
        <asp:Label ID="Label1" runat="server" Text="Password: "></asp:Label>
        <asp:TextBox ID="txt_pass" runat="server"></asp:TextBox>
        <asp:Button ID="Button1" runat="server" onclick="Button1_Click" 
            Text="submit password" />
        <br />
        <asp:Button ID="btn_showMashov" runat="server" onclick="btn_showMashov_Click" 
            Text="Show mashov" Visible="False" />
        <br />
        <asp:Label ID="lbl_list" runat="server" Text="List of mashovim" Visible="False"></asp:Label>
    </div>
    <asp:ListBox ID="lb_mashov" runat="server" Visible="False" Height="670px" 
        Width="459px">
    </asp:ListBox>
    </form>
</body>
</html>