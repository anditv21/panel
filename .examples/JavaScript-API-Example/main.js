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
    output: new Writable({
      write() {}
    })
  });
  
  process.stdout.write("[Login] Username >> ");
  rl.question("", (username) => {
    rl.stdoutMuted = true; // Set the stdoutMuted property to true to hide user input
    process.stdout.write("[Login] Password >> ");
    rl.question("", (password) => {
      rl.stdoutMuted = false; // Set the stdoutMuted property back to false
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
          if (jsonResponse.cheatversion !== VERSION) {
            console.log("You are using a outdated version.");
            console.log(apiresult.cheatversion);
            setTimeout(() => {
              process.exit();
            }, 500);
          }

          // print cheat status
          if (jsonResponse.cheatstatus === "0") {
            console.log("Status: Undetected");
          } else if (jsonResponse.cheatstatus === "1") {
            console.log("Status: Detected");
          } else if (jsonResponse.cheatmaintenance === "1") {
            console.log("Status: Maintenance");
          }

          // ban check
          if (jsonResponse.banned === "1") {
            console.log("You have been banned.");
            setTimeout(() => {
              process.exit();
            }, 500);
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
