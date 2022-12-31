<h1 align="center">:zap: User Management Panel</h1>
<p align="center">
   |
  <a href="https://github.com/anditv21/panel/issues/">Issues</a> |
  <a href="https://github.com/znixbtw/php-panel-v2/">Original Panel</a> |
  <a href="https://github.com/anditv21/panel/tree/main/.themes/gamesense/">Gamesense Theme</a> |
</p>
<p align="center">
 Default login: <code>admin</code>:<code>admin</code>
</p>


<h3 align="center">Overview</h3>
<p align="center">
  <img src="https://i.imgur.com/VB2ial8.png" />
</p>


<details align="center">
   <summary>Preview</summary>
<p align="center">
<a href="https://i.ibb.co/HFkPDbL/image.png"><img src="https://i.ibb.co/HFkPDbL/image.png"></a>
<a href="https://i.ibb.co/D1vBv3d/image.png"><img src="https://i.ibb.co/D1vBv3d/image.png"></a>
<a href="https://i.ibb.co/7KjsQzc/image.png"><img src="https://i.ibb.co/7KjsQzc/image.png"><h1 align="center">:zap: User Management Panel</h1>
<p align="center">
   |
  <a href="https://github.com/anditv21/panel/issues/">Issues</a> |
  <a href="https://github.com/znixbtw/php-panel-v2/">Original Panel</a> |
  <a href="https://github.com/anditv21/panel/tree/main/.themes/gamesense/">Gamesense Theme</a> |
  <a href="https://github.com/Phantom-1337/gamesensethemeforznixpanel">Theme</a> |
</p>
</details>

### FAQ
<details>
   <summary>Click to view FAQ</summary>
<p>
<h3>Why do updates come so rarely?</h3>
<p>Because I work on the panel only when I feel like it in my free time :)</p>
<h3>Why are parts of the code so messy?</h3>
<p>Feel free to create a pull request with improvements. ¯\_(ツ)_/¯ </p>


<br>
<br>
<br>
<br>
</p>
</details>


### Features
###### AUTH
<details>
  <summary>Click to see Auth Features</summary>
<ul>
<li>Login (Remember Login) (Screenshot: https://bit.ly/3uweFYx)</li>
<li>Register (Invite only / can be deactivated) (Screenshot: https://bit.ly/3FqPU6a)</li>
<li>Banned Page (Screenshot: https://bit.ly/39USjsR)</li>
</ul>
</details>

###### USER
<details>
  <summary>Click to see User Features</summary>
<ul>
Screenshot: https://bit.ly/3VGk2QY / https://bit.ly/3D1cXE6
<li>Change password</li>
<li>Activate multiple subscription´s with code (30/90 days)</li>
<li>Activate Trail subscription´s with code (3 days)</li>
<li>Download loader (Needs a sub)</li>
<li>Set a Profile Picture</li>
<ul>
<li>
 Get Profile Picture from Discord (currently only static)</li></ul>
<li>Shoutbox</li>

<ul><li>View user profiles

</li></ul>

</ul>
</details>

###### USER-LOGS

<details>
  <summary>Click to see a preview</summary>
<img src="https://i.ibb.co/txhMf6J/image.png">
</details>

###### Support/Admin-Panel
<details>
   <summary>Click to see Supporter/Admin-Panel Features</summary>
   <ul>
      <li>Screenshot: https://bit.ly/3Be6xQ5 / https://bit.ly/3iFlmVx</li>
      <li>Disable Invite System (Admin only)</li>
      <li>Freeze all subscriptions (experimental) (Admin only)</li> 
      <li>Gift user subscription (Admin only) (Screenshot: https://bit.ly/3utA7gA)</li>

<ul>
 <li>Input options: </li>
 <ul><li> <code>LT for Lifetime </code> </li>
 <li> <code>T for a trail subscription (3 days)</code> </li>
 <li> <code>- to remove a users subscription</code> </li>
 <li> <code>Intager for custom amount in days</code> </li></ul>
</ul>
   </ul>
<ul>
<li>User-Ranges with buttons in User Table (Screenshot: https://bit.ly/3BdxSSB)</li>
    <ul><li>Input options: </li><ul>


<li><code>1-10 10-20 20-30 30-40 40-50</code> </li>
<li><code>custom</code> </li>
<li><code>ALL</code> </li>
 </ul>
</ul>
</ul>

<li>View a users last known IP address </li>
<li>Password Reset (Admin only)</li>
<li>Set News</li>
<li>Ban-Management panel (Admin only) (Screenshot: https://bit.ly/3VS78if)</li>
<li>Generate invite code</li>
<li>Generate subscription code (Admin only)</li>
<li>Ban/unban user (Admin only)</li>
<li>Make user admin/non-admin </li>
<li>Make user supporter/non-supp </li>
<li>Reset HWID</li>
<li>Set cheat detected/undetected/version/maintenance/non-maintenance  (Admin only)</li>

</details>


###### API
<details>
   <summary>API</summary>
Note: User pass and hwid has to be sent in base64 format.
<ul>
<li>Sends user data in JSON format on call</li>
	<ul><li>Usage: <code>api.php?user={username}&pass={password}&hwid={hwid}&key={key}</code></li>
	<li>Example: <code>api.php?user=admin&pass=YWRtaW4=&hwid=aHdpZA==&key=yes</code></li></ul>

</ul>
There are already <a href="https://github.com/anditv21/panel/issues/7#issuecomment-1262149890">API examples</a> for some popular languages
</details>


---


## Setup
<details>

   <summary>Setup</summary>
<h3>NOTE: Make sure php has full access to /usercontent/avatar
otherwise no avatar can be downloaded from discord.</h3>
<br>
<br>
<br>
<ol>
   <li>Download the latest Release ZIP for your theme and the db.sql from <a href="https://github.com/anditv21/panel/releases/latest/">here.</a> </li>
   <li>Extract all of the files from the downloaded archive and upload them to your PHP host.</li>
   <li>Open the PHPMyAdmin interface for your host, and create a new database. Then, copy and paste the contents of the db.sql file into the SQL import tab on PHPMyAdmin. This will create the necessary tables and structures in the database.</li>
   <li>Open the Database.php file located at https://github.com/anditv21/panel/blob/main/src/app/core/Database and modify lines 5-8 to include your database credentials.</li>
   <li>Upload the x.exe file (also known as the Loader) in the main directory of the panel.</li>
   <li>Use the default credentials to log in to the panel, and then change the default password to a secure one.</li>
   <li>Open the Config.php file located at https://github.com/anditv21/panel/blob/main/src/app/core/Config.php and modify line 8 to set the name of your website and set a description for your website on line 11.</li>
   <li>In the Config.php file, modify line 30 to set a secure API key. This key will be used to authenticate requests to the panel's API.</li>
   <li>Open the DiscordConfig.php file located at https://github.com/anditv21/panel/blob/main/src/app/core/DiscordConfig and rename it to DiscordConfig.php.</li>
   <li>Go to https://discord.com/developers/applications and create a new Discord application. This will allow you to integrate your panel with Discord.</li>
   <li>On the profile page of the panel, copy the URL. For example: https://anditv.it/panel/user/profile.php.</li>
   <li>In the general OAuth2 settings of your Discord application, click on "Add Redirect" and paste the URL copied before. Hit "Save Changes".</li>
   <li>Finally, open the DiscordConfig.php file and fill in your Discord logging webhook URL(s) This will allow the panel to send messages to Discord when certain events occur.</li>
</ol>

</details>
</a>
</p>
</details>
