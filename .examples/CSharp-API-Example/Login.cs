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

namespace loader
{
    public partial class Login : Form
    {
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


        private void iconButton1_Click(object sender, EventArgs e)
        {
            string username;
            string password;
            string hwid;
            string apiurl;

            username = usernametextbox.Text;

            //get bytes from password & convert it to base64
            var passwordbytes = System.Text.Encoding.UTF8.GetBytes(passwordtextbox.Text);
            password = System.Convert.ToBase64String(passwordbytes);


            hwid = get_machine_guid();

            //get bytes from hwid & convert it to base64
            var hwidbystes = System.Text.Encoding.UTF8.GetBytes(passwordtextbox.Text);
            hwid = System.Convert.ToBase64String(hwidbystes);


            //the url of your api file
            apiurl = "https://anditv.it/panel/api.php";


            try
            {

                HttpWebRequest httpWebRequest = WebRequest.Create(apiurl + "?user=" + username + "&pass=" + password + "&hwid=" + hwid + "&key=yes")
                as HttpWebRequest;
                httpWebRequest.Method = "GET";
                httpWebRequest.ContentType = "application/json";
                JObject jobject = JObject.Parse(new StreamReader((httpWebRequest.GetResponse() as HttpWebResponse).GetResponseStream()).ReadToEnd());
                if (jobject["status"].ToString() == "failed")
                {
                    MessageBox.Show(jobject["error"].ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                    Environment.Exit(1);
                }
                else
                {
                    checkversion(jobject);
                    this.Hide();
                    var main = new Main();
                    main.Closed += (s, args) => this.Close();
                    main.loadcontent(jobject);
                    main.Show();
                }
            }
            catch (Exception error)
            {
                MessageBox.Show(error.ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Error);
                Environment.Exit(1);
            }
        }

        public static void checkversion(JObject jobject)
        {
            string currentversion = "1";
            if (jobject["cheatversion"].ToString() != currentversion)
            {
                MessageBox.Show("Update found" + Environment.NewLine + "Version: " + jobject["cheatversion"].ToString(), "anditv21`s panel edit", MessageBoxButtons.OK, MessageBoxIcon.Information);
                Environment.Exit(1);
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
    }
}
