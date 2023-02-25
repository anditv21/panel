<?php
echo '<style>body {background-color: #141617;}</style>';

// Check if Database.php and DiscordConfig.php exist
if (file_exists('app/core/Database.php') && file_exists('app/core/DiscordConfig.php')) {
  echo "<p style='color: white;'>✅ Files have been renamed</p>";
} else {
  echo "<p style='color: white;'>❌ Rename ";
  if (!file_exists('app/core/Database.php')) {
    echo "app/core/Database to Database.php";
  }
  if (!file_exists('app/core/DiscordConfig.php')) {
    echo "app/core/DiscordConfig to DiscordConfig.php";
  }
  echo "</p>";
}

// Check if dc app details are set
require_once('app/core/DiscordConfig.php');
if (defined('client_id') && defined('client_secret') && client_id != '1234' && client_secret != 'yoursecret') {
  echo "<p style='color: white;'>✅ Discord Application details are set</p>";
} else {
  echo "<p style='color: white;'>❌ Set Discord Application details in app/core/DiscordConfig.php</p>";
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
    echo "in app/core/Database.php</p>";
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
    echo "<p style='color: white;'>❌ Folder name and SUB_DIR constant do not match. Replace '".SUB_DIR."' with '/$parentDir' in app/core/Config.php </p>";
  }
} else {
  echo "<p style='color: white;'>❌ Config file not found</p>";
}


// Check if PHP has read and write access to /usercontent/avatar
$avatarDir = 'usercontent/avatar';
if (is_writable($avatarDir) && is_readable($avatarDir)) {
  echo "<p style='color: white;'>✅ PHP has read and write access to /$avatarDir</p>";
} else {
  echo "<p style='color: white;'>❌ PHP does not have read and write access to /$avatarDir</p>";
}


?>
