import sys
import subprocess
import base64
import requests
import json
import datetime
import time
from getpass import getpass

# Constants
DOMAIN = 'anditv.it' 
SUB_DIR = "/panel/"
API_KEY = 'yes' # api key from config file on panel
VERSION = '1'

def main():
    username = input(f'[Login] Username >> ')
    password = getpass(f'[Login] Password >> ') 
    apiresult = json.loads(str(send_login_request(username, password, hwid)))
    
    
    if (apiresult["status"] == "failed"):
        print("Username or password incorrect.")
        time.sleep(500)
        sys.exit()
        
        
    # version check
    if(str(apiresult["cheatversion"]) != VERSION):
        print("You are using a outdated version.")
        print(apiresult["cheatversion"]) 
        time.sleep(500)
        sys.exit()
            
        
    # print cheat status
    if(apiresult["cheatstatus"] == "0"):
        print("Status: Undetected")
    elif(apiresult["cheatstatus"] == "1"):
        print("Status: Detected")
    elif(apiresult["cheatmaintenance"] == "1"):
        print("Status: Maintenance")    
        
    # ban check
    if(apiresult["banned"] == "1"):
        print("You have been banned.")
        time.sleep(500)
        sys.exit()
        
    
    print(f"You have {checksub(apiresult['sub'])} day/s sub left.")           
    
    
        # hwid check
    if(get_hardware_id() == apiresult["hwid"]):
        print("HIWD does match.")
    elif(apiresult["hwid"] == None or ""):
        print("HIWD does match.")
    else:
        print("HWID does not match.")      
        time.sleep(500)
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
    
    


# Get the hardware id
hwid = get_hardware_id()
if hwid is None:
    # Log the error and exit
    print("HWID not found.")
    time.sleep(500)
    sys.exit()

if __name__ == "__main__":
    main()
