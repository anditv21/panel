<h1 align="center">:zap: User Management Panel</h1>
<p align="center">
   |
  <a href="https://github.com/anditv21/panel/issues/">Issues</a> |
  <a href="https://github.com/znixbtw/php-panel-v2/">Original Panel</a> |
  <a href="https://github.com/anditv21/panel/tree/main/.themes/gamesense/">Gamesense Theme</a> |
  <a href="https://github.com/Phantom-1337/gamesensethemeforznixpanel">Theme</a> |
</p>

> Default login: `admin`:`admin`



<h3 align="center">Overview</h3>
<p align="center">
  <img src="https://i.imgur.com/VB2ial8.png" />
</p>


<details align="center">
   <summary>Preview</summary>
<p align="center">
<a href="https://i.ibb.co/vYTnFJd/image.png"><img src="https://i.ibb.co/vYTnFJd/image.png"></a>
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



### Features
###### AUTH
<details>
  <summary>Click to see Auth Features</summary>
<ul>
<li>Login (Remember Login) (Screenshot: https://bit.ly/3UEg4Xn)</li>
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


- Extract all files and upload them to your PHP host of choice
- Copy and paste db.sql into SQL import tab on phpmyadmin
- Change https://github.com/anditv21/panel/blob/main/src/app/core/Database#L5#L8 to your database credentials
- Rename https://github.com/anditv21/panel/blob/main/src/app/core/Database to Database.php
- Put your Loader in the main directory of the panel. (x.exe)
- Login with the default credentials
- Change the default password to a secure one
- Set https://github.com/anditv21/panel/blob/main/src/app/core/Config.php#L8 to your Website name
- Set a website description in https://github.com/anditv21/panel/blob/main/src/app/core/Config.php#L11
- Change https://github.com/anditv21/panel/blob/main/src/app/core/Config.php#L30 to a secure API key

<br>

- Rename https://github.com/anditv21/panel/blob/main/src/app/core/DiscordConfig to DiscordConfig.php
- Open https://discord.com/developers/applications and create a new Discord application
- Go to the Profile page and copy its URL. E.g: https://anditv.it/panel/profile.php (A valid SSL certificate is required)
- Go to the General Oauth2 Settings of your Discord application and click on "Add Redirect"
- Paste your Profile page url and hit "Save Changes"

- Fill in your discord log webhook url in DiscordConfig.php




</details>
</a>
</p>
</details>
