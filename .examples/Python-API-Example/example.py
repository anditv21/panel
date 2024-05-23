import sys
import subprocess
import base64
import requests
import json
import datetime
import time
import winreg
from getpass import getpass

# Constants
DOMAIN = 'anditv.it'
SUB_DIR = "/panel/"
API_KEY = 'yes' # api key from config file on panel
VERSION = '1'

def main():
    username = input(f'[Login] Username >> ')
    password = getpass(f'[Login] Password >> ')
    apiresult = json.loads(str(send_login_request(username, password, get_machine_guid())))


    if (apiresult["status"] == "failed"):
        print("Username or password incorrect.")
        time.sleep(5)
        sys.exit()


    # version check
    if(str(apiresult["Systemversion"]) != VERSION):
        print("You are using a outdated version.")
        print(apiresult["Systemversion"])
        time.sleep(5)
        sys.exit()


    # print System status
    if(apiresult["Systemstatus"] == "0"):
        print("Status: Online")
    elif(apiresult["Systemstatus"] == "1"):
        print("Status: Offline")
    elif(apiresult["Systemmaintenance"] == "1"):
        print("Status: Maintenance")

    # ban check
    if(apiresult["banned"] == "1"):
        print("Account is bannedad.")
        time.sleep(5)
        sys.exit()
    else:
        print("Account is not banned.")


    print(f"You have {checksub(apiresult['sub'])} day/s sub left.")


        # hwid check
    if(get_machine_guid() == apiresult["hwid"]):
        print("HIWD does match.")
    elif(apiresult["hwid"] == None or ""):
        print("HIWD does match.")
    else:
        print("HWID does not match.")
        time.sleep(5)
        sys.exit()



def get_hardware_id():
    try:
        output = subprocess.check_output(['wmic', 'csproduct', 'get', 'uuid'])
        hwid = output.decode('utf-8').split('\r\n')[1].strip('\r').strip()
        return hwid
    except subprocess.CalledProcessError as e:
        # Log the error and return None
        return None

def send_login_request(username, password, hwid):
    # Encode the password in base64
    password_bytes = password.encode('ascii')
    base64_bytes = base64.b64encode(password_bytes)
    password = base64_bytes.decode('ascii')

    # Encode the hardware id in base64
    hwid_bytes = hwid.encode('ascii')
    base64_hwid = base64.b64encode(hwid_bytes)
    hwid = base64_hwid.decode('ascii')


    # Send the login request
    apiresult = requests.get(f'https://{DOMAIN}/{SUB_DIR}api.php?user={username}&pass={password}&hwid={hwid}&key={API_KEY}')
    return apiresult.text

def check_subscription_expired(subscription_date):
    curr_date = datetime.datetime.now()
    sub_date = datetime.datetime.strptime(subscription_date, '%Y-%m-%d')
    return curr_date >= sub_date

def checksub(sub):
    if not sub:
        return 0
    else:
        value = (datetime.datetime.strptime(sub, '%Y-%m-%d') - datetime.datetime.now()).days
        return value





def get_machine_guid():
    try:
        location = r"SOFTWARE\Microsoft\Cryptography"
        name = "MachineGuid"

        with winreg.OpenKey(winreg.HKEY_LOCAL_MACHINE, location, 0, winreg.KEY_READ | winreg.KEY_WOW64_64KEY) as key:
            try:
                machine_guid = winreg.QueryValueEx(key, name)[0]
            except OSError:
                raise Exception(f"Not found: {location}\\{name}")

        return machine_guid
    except Exception as e:
        print(f"Error getting machine GUID: {e}")
        return None


if __name__ == "__main__":
    main()
