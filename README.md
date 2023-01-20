
<h1 align="center">:zap: User Management Panel</h1>

<p align="center">
| <a  href="https://github.com/anditv21/panel/issues/new?assignees=&labels=bug&template=bug_report.md&title=%5BBUG%5D">Bug report</a> |
<a  href="https://github.com/znixbtw/php-panel-v2/">Original Panel</a> |
<a  href="https://github.com/anditv21/panel/tree/main/.examples/">Loader/API Examples</a> |

</p>

<p align="center">
Default login: <code>admin</code>:<code>admin</code>
</p>

  
  

<h3 align="center">Overview</h3>

<p align="center">

<img  src="https://i.imgur.com/VB2ial8.png" />

</p>

<details align="center">

<summary>Preview</summary>

<p align="center">

<a  href="https://i.ibb.co/RyvRyDB/image.png"><img src="https://i.ibb.co/RyvRyDB/image.png" /></a>

<a  href="https://i.ibb.co/1Tx5BK7/image.png"><img src="https://i.ibb.co/1Tx5BK7/image.png" /></a>

<a  href="https://i.ibb.co/QcdVwvZ/image.png"><img src="https://i.ibb.co/QcdVwvZ/image.png" /></a>
<a  href="https://github.com/anditv21/panel/raw/main/.examples/CSharp-API-Example/img/readme1.png"><img src="https://github.com/anditv21/panel/raw/main/.examples/CSharp-API-Example/img/readme1.png" /></a>
  

</details>

  

### FAQ

<details>

<summary>Click to view FAQ</summary>

<h3>What exactly does the panel do?</h3>

<p>Basically it is a system to license software. <br>

Originally it was developed by <a href="https://github.com/znixbtw/">@znix</a> to license cheating software for e.g. CSGO.</p>

  

<h3>Why do updates come so rarely?</h3>

<p>Because I work on the panel only when I feel like it in my free time. :)</p>

<h3>Why are parts of the code so messy?</h3>

<p>Parts of the code are from an old project and have not been improved yet. <br> Feel free to create a pull request with improvements. ¯\_(ツ)_/¯ </p>

<h3>How do I update the panel?</h3>

<p>Download the latest release. And drag all files and folders except "/app/core" (this would overwrite your config and db infos) to your server.</p>

<h3>hOw dO I SeTuP ThE mOdErN ThEmE?</h3>

<p>Just like the default theme :)</p>
<br>
</details>

  
  

### Features

###### AUTH

<details>

<summary>Click to see Auth Features</summary>

<ul>

<li>Login (Multiple device remember Login) (Screenshot: https://bit.ly/3GUeex5)</li>

<li>Register (Invite only / can be deactivated) (Screenshot: https://bit.ly/3ZrXndf)</li>

<li>Banned Page (Screenshot: https://bit.ly/3vYaHse)</li>

</ul>

</details>

  

###### USER

<details>

<summary>Click to see User Features</summary>

<ul>

Screenshot: https://bit.ly/3W3SBQj / https://bit.ly/3D1cXE6

<li>Change password</li>

<li>Activate multiple subscription´s with code (30/90 days)</li>

<li>Activate Trail subscription´s with code (3 days)</li>

<li>Download loader (Needs a sub)</li>

<li>Set a Profile Picture</li>

<ul>

<li>Get Profile Picture from Discord (currently only static)</li></ul>



</ul>

</details>

  

###### USER-LOGS

  

<details>

<summary>Click to see a preview</summary>

<img  src="https://i.ibb.co/GHbF6Ly/opera-Bei-I6vs-O9-Z.png">

</details>

  

###### Support/Admin-Panel

<details>

<summary>Click to see Supporter/Admin-Panel Features</summary>

<ul>

<li>Screenshot: https://bit.ly/3GXtf21 / https://bit.ly/3IC7O8a</li>

<li>Disable Invite System (Admin only)</li>

<li>Freeze all subscriptions (experimental) (Admin only)</li>

<li>Gift user subscription (Admin only) (Screenshot: https://bit.ly/3ivNJ8K)</li>

  




  

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

<li>Set cheat detected/undetected/version/maintenance/non-maintenance (Admin only)</li>

  

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

There are already <a  href="https://github.com/anditv21/panel/tree/main/.examples/">API examples</a> for some popular languages

</details>

  
  
###### Credits
  <details>

<summary>Credits</summary>


<ul>

<li><a href="https://github.com/znixbtw/">@znixbtw</a> for his awesome original <a href="https://github.com/znixbtw/php-panel-v2/">panel</a>.</li>
<li><a href="https://github.com/Phantom-1337/">@Phantom-1337</a> and <a href="https://github.com/sxck1337/">@sxck1337</a> for their cool themes.</li>


  

</ul>



</details>


---

  
  

## Setup

<details>

  

<summary>Setup</summary>


<ol>
  <li>Download the latest Release ZIP and the db.sql file from <a href="https://github.com/anditv21/panel/releases/latest/">here.</a></li>
  <li>Extract the files and upload them to your PHP host.</li>
  <li>Create a new database in PHPMyAdmin, import the contents of the db.sql.</li>
  <li>Rename Database file to Database.php.</li>
  <li>Edit the Database.php file to include your database credentials.</li>
  <li>Upload the x.exe file in the panel's main directory.</li>
  <li>Log in to the panel using default credentials, then change the password to a secure one.</li>
  <li>Edit the Config.php file to set the name and description of your website, and set a secure API key for authenticating requests.</li>
  <li>Rename the DiscordConfig file to DiscordConfig.php.</li>
  <li>Create a new Discord application to integrate with the panel.</li>
  <li>Copy the panel's profile URL and add it as a redirect URL in the Discord application.</li>
  <li>Fill in your Discord logging webhook URL(s) in the DiscordConfig.php file to allow the panel to send messages to Discord.</li>
</ol>

  
<p>NOTE: Make sure php has full access to /usercontent/avatar otherwise no avatar can be downloaded from discord.</p>
</details>

</a>

</p>

</details>
