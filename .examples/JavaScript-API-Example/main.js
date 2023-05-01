const readline = require("readline");
const base64 = require("base-64");
const https = require("https");
const { execSync } = require('child_process');

DOMAIN = "anditv.it";
API_KEY = "yes";
SUB_DIR = "/panel/";
VERSION = 1;

function getHardwareId() {
  try {
    const command = 'reg query HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Cryptography /v MachineGuid';
    const output = execSync(command, {
      encoding: 'utf8'
    });

    // Parse output to extract GUID value
    const regex = /REG_SZ\s+(.*)\s*/;
    const match = output.match(regex);
    if (!match || !match[1]) {
      throw new Error(`Not found: ${command}`);
    }

    return match[1];
  } catch (e) {
    console.error(`Error getting machine GUID: ${e}`);
    return null;
  }
}

function checksub(sub) {
  if (!sub) {
    return 0;
  } else {
    const value = Math.round(
      (new Date(sub) - new Date()) / (1000 * 60 * 60 * 24)
    );
    return parseInt(value);
  }
}


const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

process.stdout.write("[Login] Username >> ");
rl.on('line', (username) => {
  rl.stdoutMuted = true;
  rl.prompt();
  rl._writeToOutput = function _writeToOutput(stringToWrite) {
    if (rl.stdoutMuted)
      rl.output.write("*");
    else
      rl.output.write(stringToWrite);
  };
  rl.on('line', (password) => {
    rl.stdoutMuted = false;
    console.clear();

    const hwid = getHardwareId();

    // Encode the password in base64
    const base64_password = base64.encode(password);

    // Encode the hardware id in base64
    const base64_hwid = base64.encode(hwid);

    // Send the login request
    https.get(
      `https://${DOMAIN}/${SUB_DIR}/api.php?user=${username}&pass=${base64_password}&hwid=${base64_hwid}&key=${API_KEY}`,
      (res) => {
        let apiresult = "";
        res.on("data", (chunk) => {
          apiresult += chunk;
        });

        res.on("end", () => {
          const jsonResponse = JSON.parse(apiresult);
          //console.log(jsonResponse);
          rl.close();

          if (jsonResponse.status === "failed") {
            console.log("Username or password incorrect.");
            setTimeout(() => {
              process.exit();
            }, 500);
          }

          // version check
          if (jsonResponse.Systemversion !== VERSION) {
            console.log("You are using a outdated version.");
            console.log(jsonResponse.Systemversion);
            setTimeout(() => {
              process.exit();
            }, 500);
          }

          // print System status
          if (jsonResponse.Systemstatus === "0") {
            console.log("Status: Online");
          } else if (jsonResponse.Systemstatus === "1") {
            console.log("Status: Offline");
          } else if (jsonResponse.Systemmaintenance === "1") {
            console.log("Status: Maintenance");
          }

          // ban check
          if (jsonResponse.banned === "1") {
            console.log("You have been banned.");
            setTimeout(() => {
              process.exit();
            }, 500);
          }
          else
          {
            console.log("You are not banned.");
          }
          console.log(`You have ${checksub(jsonResponse.sub)} day/s sub left.`);

          // hwid check
          if (
            getHardwareId() === jsonResponse.hwid ||
            jsonResponse.hwid === null ||
            jsonResponse.hwid === ""
          ) {
            console.log("HWID does match.");
          } else {
            console.log("HWID does not match.");
            setTimeout(() => {
              process.exit();
            }, 500);
          }
        });
      }
    );
  });
});
