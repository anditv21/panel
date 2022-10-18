<h1 align="center">:zap: User Management Panel</h1>
<p align="center">
 | 
  <a href="#preview">Preview</a> |
  <a href="#setup">Setup</a> |
  <a href="#features">Features </a> |
  <a href="https://github.com/anditv21/panel/issues/">Issues</a> |
</p>

---
* Original Panel: https://github.com/znixbtw/php-panel-v2
* Panel Edit: https://github.com/Phantom-1337/gamesensethemeforznixpanel
* Old Panel: https://github.com/anditv21/znixv2-panel-edit-edit
> Default login: `admin`:`admin` <br />
---

### Overview
<p align="center">
  <img src="https://i.imgur.com/VB2ial8.png" />
</p>


<details align="center">
   <summary>Preview</summary>
<p align="center">
  <a href=https://i.ibb.co/897qQpS/image.png"><img src="https://i.ibb.co/897qQpS/image.png" /></a>
<a href="https://i.ibb.co/1Tx5BK7/image.png"><img src="https://i.ibb.co/1Tx5BK7/image.png" /></a>
<a href="https://i.ibb.co/QcdVwvZ/image.png"><img src="https://i.ibb.co/QcdVwvZ/image.png" /></a>
</p>
</details>

### Features
###### AUTH
<details>
  <summary>Click to see Auth Features</summary>
<ul>
<li>Login (Remember Login) (Screenshot: https://bit.ly/3QlQDaW)</li>
<li>Register (Invite only / can be deactivated) (Screenshot: https://bit.ly/3RziB40)</li>
<li>Banned Page (Screenshot: https://bit.ly/3erfTj1)</li>
</ul>
</details>

###### USER
<details>
  <summary>Click to see User Features</summary>
<ul>
Screenshot: https://bit.ly/3fx1Vg2 / https://bit.ly/3D1cXE6
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


###### Support/Admin-Panel
<details>
   <summary>Click to see Supporter/Admin-Panel Features</summary>
   <ul>
      <li>Screenshot: https://bit.ly/3QhZ7Qv / https://bit.ly/3REAwXf</li>
      <li>Disable Invite System (Admin only)</li>
      <li>Freeze all subscriptions (experimental) (Admin only)</li> 
      <li>Gift user subscription (Admin only) (Screenshot: https://bit.ly/3enQmqP)</li>

<ul>
 <li>Input options: </li>
 <ul><li> <code>LT for Lifetime </code> </li>
 <li> <code>T for a trail subscription (3 days)</code> </li>
 <li> <code>- to remove a users subscription</code> </li>
 <li> <code>Intager for custom amount in days</code> </li></ul>
</ul>
   </ul>
<ul>
<li>User-Ranges with buttons in User Table (Screenshot: https://bit.ly/3THQuSe)</li>
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
<li>Ban-Management panel (Admin only) (Screenshot: https://bit.ly/3AJVIUI)</li>
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


## Authors

## panel edit
👤 **anditv21**

* Website: [anditv.it](https://anditv.it)
* Github: [@anditv21](https://github.com/anditv21)

## original panel
👤 **znixbtw**

* Website: [znix.me](https://znix.me)
* Github: [@znixbtw](https://github.com/znixbtw)

### gamesense theme
👤 **index**

* Github: [@Phantom-1337](https://github.com/Phantom-1337)

<details>
   <summary>Setup</summary>


- Upload all files to your PHP host of choice
- Copy and paste db.sql into SQL import tab on phpmyadmin
- Change https://github.com/anditv21/panel/blob/main/app/core/Database#L5#L8 to your database credentials
- Rename https://github.com/anditv21/panel/blob/main/app/core/Database to Database.php
- Put your Loader in the main directory of the panel. (x.exe)
- Login with the default credentials
- Change the default password to a secure one
- Set https://github.com/anditv21/panel/blob/main/app/core/Config.php#L8 to your Website name
- Set a website description in https://github.com/anditv21/panel/blob/main/app/core/Config.php#L11
- Change https://github.com/anditv21/panel/blob/main/app/core/Config.php#L30 to a secure API key

<br>

- Rename https://github.com/anditv21/panel/blob/main/app/core/DiscordConfig to DiscordConfig.php
- Open https://discord.com/developers/applications and create a new Discord application
- Go to the Profile page and copy its URL. E.g: https://anditv.it/panel/profile.php
- Go to the General Oauth2 Settings of your Discord application and click on "Add Redirect"
- Paste your Profile page url and hit "Save Changes"

<br>
- Fill in your discord log webhook url in DiscordConfig.php



</details>
