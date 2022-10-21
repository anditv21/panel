using Newtonsoft.Json.Linq;
using System;
using System.Collections.Generic;
using System.ComponentModel;
using System.Data;
using System.Drawing;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using System.Windows.Forms;
using loader;
using Newtonsoft.Json;

namespace loader
{
    public partial class Main : Form
    {
        public Main()
        {
            InitializeComponent();
        }

        public void loadcontent(JObject result)
        {
            UID.Text = result["uid"].ToString();
            Username.Text = result["username"].ToString();
            avatar.ImageLocation = result["avatarurl"].ToString();
        }
    }
}
