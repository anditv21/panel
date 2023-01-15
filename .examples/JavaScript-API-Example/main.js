const readline = require("readline");
const base64 = require("base-64");
const https = require("https");

DOMAIN = "anditv.it";
API_KEY = "yes";
SUB_DIR = "/panel/";
VERSION = 1;

function getHardwareId() {
  try {
    const { execSync } = require("child_process");
    const output = execSync("wmic csproduct get uuid").toString();
    const hwid = output.split("\r\n")[1].trim();
    return hwid;
  } catch (e) {
    // Log the error and return null
    return null;
  }
}

function checkSubscriptionExpired(subscriptionDate) {
  const currDate = new Date();
  const subDate = new Date(subscriptionDate);
  return currDate >= subDate;
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
  output: process.stdout,
});

rl.question("[Login] Username >> ", (username) => {
  rl.question("[Login] Password >> ", (password) => {
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
