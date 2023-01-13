namespace loader
{
    partial class Main
    {
        /// <summary>
        /// Required designer variable.
        /// </summary>
        private System.ComponentModel.IContainer components = null;

        /// <summary>
        /// Clean up any resources being used.
        /// </summary>
        /// <param name="disposing">true if managed resources should be disposed; otherwise, false.</param>
        protected override void Dispose(bool disposing)
        {
            if (disposing && (components != null))
            {
                components.Dispose();
            }
            base.Dispose(disposing);
        }

        #region Windows Form Designer generated code

        /// <summary>
        /// Required method for Designer support - do not modify
        /// the contents of this method with the code editor.
        /// </summary>
        private void InitializeComponent()
        {
            System.ComponentModel.ComponentResourceManager resources = new System.ComponentModel.ComponentResourceManager(typeof(Main));
            this.welcome = new System.Windows.Forms.Label();
            this.statustext = new System.Windows.Forms.Label();
            this.subtext = new System.Windows.Forms.Label();
            this.Inviter = new System.Windows.Forms.Label();
            this.status = new System.Windows.Forms.Label();
            this.avatar = new System.Windows.Forms.PictureBox();
            this.panel1 = new System.Windows.Forms.Panel();
            this.pictureBox3 = new System.Windows.Forms.PictureBox();
            this.pictureBox2 = new System.Windows.Forms.PictureBox();
            this.pictureBox1 = new System.Windows.Forms.PictureBox();
            ((System.ComponentModel.ISupportInitialize)(this.avatar)).BeginInit();
            this.panel1.SuspendLayout();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox3)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox2)).BeginInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).BeginInit();
            this.SuspendLayout();
            // 
            // welcome
            // 
            this.welcome.AutoSize = true;
            this.welcome.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.857143F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.welcome.ForeColor = System.Drawing.SystemColors.Window;
            this.welcome.Location = new System.Drawing.Point(112, 262);
            this.welcome.Name = "welcome";
            this.welcome.Size = new System.Drawing.Size(111, 29);
            this.welcome.TabIndex = 10;
            this.welcome.Text = "welcome";
            // 
            // statustext
            // 
            this.statustext.AutoSize = true;
            this.statustext.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.857143F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.statustext.ForeColor = System.Drawing.SystemColors.Window;
            this.statustext.Location = new System.Drawing.Point(195, 457);
            this.statustext.Name = "statustext";
            this.statustext.Size = new System.Drawing.Size(75, 29);
            this.statustext.TabIndex = 11;
            this.statustext.Text = "status";
            // 
            // subtext
            // 
            this.subtext.AutoSize = true;
            this.subtext.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.857143F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.subtext.ForeColor = System.Drawing.SystemColors.Window;
            this.subtext.Location = new System.Drawing.Point(112, 329);
            this.subtext.Name = "subtext";
            this.subtext.Size = new System.Drawing.Size(52, 29);
            this.subtext.TabIndex = 14;
            this.subtext.Text = "sub";
            // 
            // Inviter
            // 
            this.Inviter.AutoSize = true;
            this.Inviter.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.857143F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.Inviter.ForeColor = System.Drawing.SystemColors.Window;
            this.Inviter.Location = new System.Drawing.Point(110, 393);
            this.Inviter.Name = "Inviter";
            this.Inviter.Size = new System.Drawing.Size(77, 29);
            this.Inviter.TabIndex = 15;
            this.Inviter.Text = "Inviter";
            // 
            // status
            // 
            this.status.AutoSize = true;
            this.status.Font = new System.Drawing.Font("Microsoft Sans Serif", 9.857143F, System.Drawing.FontStyle.Regular, System.Drawing.GraphicsUnit.Point, ((byte)(0)));
            this.status.ForeColor = System.Drawing.SystemColors.Window;
            this.status.Location = new System.Drawing.Point(110, 457);
            this.status.Name = "status";
            this.status.Size = new System.Drawing.Size(85, 29);
            this.status.TabIndex = 16;
            this.status.Text = "Status:";
            // 
            // avatar
            // 
            this.avatar.Location = new System.Drawing.Point(152, 105);
            this.avatar.Margin = new System.Windows.Forms.Padding(4);
            this.avatar.Name = "avatar";
            this.avatar.Size = new System.Drawing.Size(173, 137);
            this.avatar.TabIndex = 9;
            this.avatar.TabStop = false;
            // 
            // panel1
            // 
            this.panel1.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(32)))), ((int)(((byte)(32)))), ((int)(((byte)(32)))));
            this.panel1.Controls.Add(this.pictureBox3);
            this.panel1.Controls.Add(this.pictureBox2);
            this.panel1.Location = new System.Drawing.Point(-13, -1);
            this.panel1.Name = "panel1";
            this.panel1.Size = new System.Drawing.Size(481, 52);
            this.panel1.TabIndex = 154;
            // 
            // pictureBox3
            // 
            this.pictureBox3.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(16)))), ((int)(((byte)(16)))), ((int)(((byte)(16)))));
            this.pictureBox3.Image = global::loader.Properties.Resources.x;
            this.pictureBox3.Location = new System.Drawing.Point(392, 2);
            this.pictureBox3.Name = "pictureBox3";
            this.pictureBox3.Size = new System.Drawing.Size(70, 47);
            this.pictureBox3.TabIndex = 5;
            this.pictureBox3.TabStop = false;
            this.pictureBox3.Click += new System.EventHandler(this.pictureBox3_Click_1);
            // 
            // pictureBox2
            // 
            this.pictureBox2.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(34)))), ((int)(((byte)(34)))), ((int)(((byte)(34)))));
            this.pictureBox2.Image = global::loader.Properties.Resources.logo;
            this.pictureBox2.Location = new System.Drawing.Point(199, 9);
            this.pictureBox2.Name = "pictureBox2";
            this.pictureBox2.Size = new System.Drawing.Size(42, 38);
            this.pictureBox2.TabIndex = 4;
            this.pictureBox2.TabStop = false;
            // 
            // pictureBox1
            // 
            this.pictureBox1.Image = global::loader.Properties.Resources.slider;
            this.pictureBox1.InitialImage = ((System.Drawing.Image)(resources.GetObject("pictureBox1.InitialImage")));
            this.pictureBox1.Location = new System.Drawing.Point(-6, 50);
            this.pictureBox1.Margin = new System.Windows.Forms.Padding(1);
            this.pictureBox1.Name = "pictureBox1";
            this.pictureBox1.Size = new System.Drawing.Size(527, 5);
            this.pictureBox1.TabIndex = 153;
            this.pictureBox1.TabStop = false;
            // 
            // Main
            // 
            this.AutoScaleDimensions = new System.Drawing.SizeF(11F, 24F);
            this.AutoScaleMode = System.Windows.Forms.AutoScaleMode.Font;
            this.BackColor = System.Drawing.Color.FromArgb(((int)(((byte)(16)))), ((int)(((byte)(16)))), ((int)(((byte)(16)))));
            this.ClientSize = new System.Drawing.Size(446, 543);
            this.Controls.Add(this.panel1);
            this.Controls.Add(this.pictureBox1);
            this.Controls.Add(this.status);
            this.Controls.Add(this.Inviter);
            this.Controls.Add(this.subtext);
            this.Controls.Add(this.statustext);
            this.Controls.Add(this.welcome);
            this.Controls.Add(this.avatar);
            this.FormBorderStyle = System.Windows.Forms.FormBorderStyle.None;
            this.Margin = new System.Windows.Forms.Padding(6);
            this.Name = "Main";
            this.StartPosition = System.Windows.Forms.FormStartPosition.CenterScreen;
            this.Text = "Main";
            this.MouseDown += new System.Windows.Forms.MouseEventHandler(this.Main_MouseDown);
            ((System.ComponentModel.ISupportInitialize)(this.avatar)).EndInit();
            this.panel1.ResumeLayout(false);
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox3)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox2)).EndInit();
            ((System.ComponentModel.ISupportInitialize)(this.pictureBox1)).EndInit();
            this.ResumeLayout(false);
            this.PerformLayout();

        }

        #endregion
        private System.Windows.Forms.PictureBox avatar;
        private System.Windows.Forms.Label welcome;
        private System.Windows.Forms.Label statustext;
        private System.Windows.Forms.Label subtext;
        private System.Windows.Forms.Label Inviter;
        private System.Windows.Forms.Label status;
        private System.Windows.Forms.Panel panel1;
        private System.Windows.Forms.PictureBox pictureBox3;
        private System.Windows.Forms.PictureBox pictureBox2;
        private System.Windows.Forms.PictureBox pictureBox1;
    }
}