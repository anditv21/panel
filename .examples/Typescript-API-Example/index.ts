/* API Example made by https://github.com/ghostboy-dev/ */

import readline from "readline";
import { execSync } from "child_process";
import axios, { AxiosResponse } from "axios";
import { Buffer } from "buffer";


const DOMAIN: string = "anditv.dev";
const API_KEY: string = "yes";
const SUB_DIR: string = "panel";
const VERSION: number = 1;


interface UserInfo {
    status: string;
    uid: number;
    username: string;
    hwid: string;
    admin: number;
    supp: number | null;
    sub: string;
    banned: number;
    invitedBy: string;
    createdAt: string;
    avatarurl: string;
    frozen: number;
    Systemstatus: number;
    Systemversion: number;
    Systemmaintenance: number;
}



function base64Encode(content: string): string {
    return Buffer.from(content).toString('base64')
}

function getHardwareId(): string | null {
    try {
        const command: string = "reg query HKEY_LOCAL_MACHINE\\SOFTWARE\\Microsoft\\Cryptography /v MachineGuid";
        const output: string = execSync(command,
            {
                encoding: 'utf8'
            }
        );
  
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
};


function checksub(sub: string | null) {
    if (!sub) return 0;
    return Math.round((new Date(sub).getTime() - new Date().getTime()) / (1000 * 60 * 60 * 24));
};



async function main() {
    var userName: string = await(input("[Login] Username >> "));
    var password: string = await(input("[Login] Password >> "));

    var getUserHWID: string | null = getHardwareId();
    if (!getUserHWID) return;

    try {
        var login: AxiosResponse<any, any> = await axios.get(`https://${DOMAIN}/${SUB_DIR}/api.php?user=${userName}&pass=${base64Encode(password)}&hwid=${base64Encode(getUserHWID)}&key=${API_KEY}`);
        const userInfo: UserInfo = login.data as UserInfo;

        if (userInfo.status === "failed") {
            console.log("Username or password incorrect.");
            setTimeout(() => {
                process.exit();
            }, 500);
        };
      
        // version check
        if (userInfo.Systemversion !== VERSION) {
            console.log("You are using a outdated version.");
            console.log(userInfo.Systemversion);
            setTimeout(() => {
                process.exit();
            }, 500);
        };
      
        // print System status
        if (userInfo.Systemstatus === 0) {
            console.log("Status: Online");
        } else if (userInfo.Systemstatus === 1) {
            console.log("Status: Offline");
        } else if (userInfo.Systemmaintenance === 1) {
            console.log("Status: Maintenance");
        }
      
        // ban check
        if (userInfo.banned === 1) {
            console.log("You have been banned.");
            setTimeout(() => {
                process.exit();
            }, 500);
        } else {
            console.log("You are not banned.");
        };
    
        console.log(`You have ${checksub(userInfo.sub)} day/s sub left.`);
      
        // hwid check
        if (getHardwareId() === userInfo.hwid || userInfo.hwid === null || userInfo.hwid === "") {
            console.log("HWID does match.");
        } else {
            console.log("HWID does not match.");
            setTimeout(() => {
                process.exit();
            }, 500);
        }
    } catch (err) {
        console.log(err)
    };
};

main();



async function input(question: string): Promise<string> {
    var readLineInterface: readline.Interface = readline.createInterface({
        input: process.stdin,
        output: process.stdout,
    });
    
    return new Promise((resolve) => {
        readLineInterface.question(question, (answer: string) => {
            readLineInterface.close();
            resolve(answer);
        });
    });
};
