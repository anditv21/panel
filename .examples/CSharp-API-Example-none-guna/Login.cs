using Microsoft.Win32;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using System.Net;
using Newtonsoft.Json;
using Newtonsoft.Json.Linq;
using static System.Net.Mime.MediaTypeNames;
using System.IO;
using System.Security.Cryptography.X509Certificates;
using System.Diagnostics;
using System.Xml.Linq;

namespace loader
{
    public partial class Login : Form
    {
        // https://www.codeproject.com/Articles/11114/Move-window-form-without-Titlebar-in-C
        public const int WM_NCLBUTTONDOWN = 0xA1;
        public const int HT_CAPTION = 0x2;

        [System.Runtime.InteropServices.DllImport("user32.dll")]
        public static extern int SendMessage(IntPtr hWnd, int Msg, int wParam, int lParam);
        [System.Runtime.InteropServices.DllImport("user32.dll")]
        public static extern bool ReleaseCapture();

        public Login()
        {
            InitializeComponent();
        }

        public static string get_machine_guid()
        {
            string location = @"SOFTWARE\Microsoft\Cryptography";
            string name = "MachineGuid";

            using (RegistryKey localMachineX64View = RegistryKey.OpenBaseKey(RegistryHive.LocalMachine, RegistryView.Registry64))
            {
                using (RegistryKey rk = localMachineX64View.OpenSubKey(location))
                {
                    if (rk == null)
                        throw new KeyNotFoundException(string.Format("Not Found: {0}", location));

                    object machineGuid = rk.GetValue(name);
                    if (machineGuid == null)
                        throw new IndexOutOfRangeException(string.Format("Not Found: {0}", name));

                    return machineGuid.ToString();
                }
            }
        }


        public static void checkversion(JObject apiresult)
        {
            string currentversion = "1";
            if (apiresult["cheatversion"].ToString() != currentversion)
            {
                MessageBox.Show("Update found" + Environment.NewLine + "Version: " + apiresult["cheatversion"].ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Information);
                Environment.Exit(1);
            }

        }

        public static bool checkhwid(string hwid)
        {
            if (get_machine_guid().ToString() == hwid || string.IsNullOrEmpty(hwid))
            {
                return true;
            }
            else if (get_machine_guid().ToString() != hwid)
            {
                MessageBox.Show("Error: HWID doesn´t match.", "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                return false;
            }
            else
            {
                return true;
            }
        }


        private void usernametextbox_MouseClick(object sender, MouseEventArgs e)
        {
            usernametextbox.Text = string.Empty;
        }

        private void passwordtextbox_MouseClick(object sender, MouseEventArgs e)
        {
            passwordtextbox.Text = string.Empty;
        }

        private void Login_MouseDown(object sender, MouseEventArgs e)
        {
            if (e.Button == MouseButtons.Left)
            {
                ReleaseCapture();
                SendMessage(Handle, WM_NCLBUTTONDOWN, HT_CAPTION, 0);
            }
        }


        private void pictureBox3_Click(object sender, EventArgs e)
        {
            Environment.Exit(0);
        }

        private void loginbtn_Click(object sender, EventArgs e)
        {
            string username;
            string password;
            string hwid;
            string apiurl;
            string key;

            username = usernametextbox.Text;

            //get bytes from password & convert it to base64
            var passwordbytes = System.Text.Encoding.UTF8.GetBytes(passwordtextbox.Text);
            password = System.Convert.ToBase64String(passwordbytes);


            hwid = get_machine_guid();

            //get bytes from hwid & convert it to base64
            var hwidbystes = System.Text.Encoding.UTF8.GetBytes(hwid);
            hwid = System.Convert.ToBase64String(hwidbystes);


            //the url of your api file
            apiurl = "https://anditv.it/panel/api.php";

            //the api key from the config
            key = "yes";

            try
            {

                HttpWebRequest httpWebRequest = WebRequest.Create(apiurl + "?user=" + username + "&pass=" + password + "&hwid=" + hwid + "&key=" + key)
                as HttpWebRequest;
                httpWebRequest.Method = "GET";
                httpWebRequest.ContentType = "application/json";
                JObject apiresult = JObject.Parse(new StreamReader((httpWebRequest.GetResponse() as HttpWebResponse).GetResponseStream()).ReadToEnd());
                if (apiresult["status"].ToString() == "failed")
                {
                    MessageBox.Show(apiresult["error"].ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    Environment.Exit(1);
                }
                else if (apiresult["banned"].ToString() == "1")
                {
                    MessageBox.Show("You have been banned.", "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    Environment.Exit(1);
                }
                else
                {
                    checkversion(apiresult);
                    if (!checkhwid(apiresult["hwid"].ToString()))
                    {
                        Environment.Exit(1);
                    }
                    this.Hide();
                    var main = new Main();
                    main.Closed += (s, args) => this.Close();
                    main.loadcontent(apiresult);
                    main.Show();
                }
            }
            catch (Exception error)
            {
                MessageBox.Show(error.ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                Environment.Exit(1);
            }
        }
    }
}
