<?php
function getPhpErrorLogDir() {
  $errorLogDir = ini_get('error_log');
  $notFound = true;

  if (empty($errorLogDir)) {
      $serverSoftware = strtolower($_SERVER['SERVER_SOFTWARE']);

      if (strpos($serverSoftware, 'nginx') !== false) {
          $errorLogDir = '/var/log/nginx/error.log';
      } elseif (strpos($serverSoftware, 'apache') !== false) {
          $errorLogDir = '/var/log/apache2/error.log';
      } elseif (strpos($serverSoftware, 'lighttpd') !== false) {
          $errorLogDir = '/var/log/lighttpd/error.log';
      }
      
      if (!empty($errorLogDir)) {
          $notFound = false;
      }
  } else {
      if (file_exists($errorLogDir)) {
          $notFound = false;
      }
  }

  return $notFound ? 'Error log not found' : $errorLogDir;
}



echo '<title>Panel-Setup Help</title><style>body {background-color: #141617;}</style>';

// Check PHP version
if (version_compare(phpversion(), '8.0.0', '<')) {
  echo "<p style='color: white;'>❌ Please change your PHP version to 8.0 or higher</p>";
}
else
{
  echo "<p style='color: white;'>✅ Using PHP 8.0 or higher</p>";
}
// Check if Database.php and DiscordConfig.php exist
if (file_exists('app/core/Database.php') && file_exists('app/core/DiscordConfig.php')) {
  echo "<p style='color: white;'>✅ Files have been renamed</p>";
} else {
  echo "<p style='color: white;'>❌ Rename ";
  if (!file_exists('app/core/Database.php') && !file_exists('app/core/DiscordConfig.php')) {
    echo "<b>app/core/Database to Database.php and app/core/DiscordConfig to DiscordConfig.php</b>";
  } else {
    if (!file_exists('app/core/Database.php')) {
      echo "<b>app/core/Database to Database.php</b>";
    }
    if (!file_exists('app/core/DiscordConfig.php')) {
      echo "<b>app/core/DiscordConfig to DiscordConfig.php</b>";
    }
  }
  echo "</p>";
}


// Check if dc app details are set
if (file_exists('app/core/DiscordConfig.php')) {
  require_once('app/core/DiscordConfig.php');
  if (defined('client_id') && defined('client_secret') && client_id != '1234' && client_secret != 'yoursecret') {
    echo "<p style='color: white;'>✅ Discord Application details are set</p>";
  } else {
    echo "<p style='color: white;'>❌ Set Discord Application details in <b>app/core/DiscordConfig.php</b></p>";
  }
} 


// Check if database credentials are set
if (file_exists('app/core/Database.php')) {
  require_once('app/core/Database.php');
  $class = new ReflectionClass('Database');
  $properties = $class->getProperties(ReflectionProperty::IS_PRIVATE);
  $missingProperties = [];
  foreach ($properties as $property) {
    $property->setAccessible(true);
    if (!$property->getValue(new Database)) {
      array_push($missingProperties, $property->getName());
    }
  }
  if (!empty($missingProperties)) {
    echo "<p style='color: white;'>❌ Set ";
    foreach ($missingProperties as $property) {
      echo "$property ";
    }
    echo "in <b>app/core/Database.php</b></p>";
  } else {
    echo "<p style='color: white;'>✅ Database credentials are set</p>";
  }
}

// Check if x.exe exists
if (file_exists('x.exe')) {
  echo "<p style='color: white;'>✅ Loader file exists</p>";
} else {
  echo "<p style='color: white;'>❌ Loader file (x.exe) does not exist</p>";
}

// Check if SUB_DIR constant matches the parent folder name
if (file_exists('app/core/Config.php')) {
  require_once('app/core/Config.php');
  $parentDir = basename(dirname(__FILE__, 1));
  if (defined('SUB_DIR') && SUB_DIR == '/'.$parentDir || SUB_DIR == "") {
    echo "<p style='color: white;'>✅ Subfolder is correctly set</p>";
  } else {
    echo "<p style='color: white;'>❌ Folder name and SUB_DIR constant do not match. Replace '".SUB_DIR."' with '/$parentDir' in <b>app/core/Config.php</b></p>";
  }
} else {
  echo "<p style='color: white;'>❌ Config file not found</p>";
}


// Check if PHP has read and write access to /usercontent/avatar
$avatarDir = 'usercontent/avatar';
if (is_writable($avatarDir) && is_readable($avatarDir)) {
  echo "<p style='color: white;'>✅ PHP has read and write access to <b>/$avatarDir</b></p>";
} else {
  echo "<p style='color: white;'>❌ PHP does not have read and write access to <b>/$avatarDir</b></p>";
}


$errorLogDir = getPhpErrorLogDir();
echo "<p style='color: white;'>⚠️ PHP Error Log Directory: <b>" . $errorLogDir."</b></p>";

echo "<br><h3 style='color: white;'><b>You still having problems? Then maybe the <a href='https://github.com/anditv21/panel/wiki/Common-issues' target='_blank'>wiki</a> will help you</b></h3>";
?>
