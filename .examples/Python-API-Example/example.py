import os
import subprocess
import base64
import requests
import json
import datetime

def main():
    username = input(f'[Login] Username >> ')
    password = input(f'[Login] Password >> ')    
    print(send_login_request(username, password, hwid))
    
def get_config():
    # Read the configuration file
    with open('config.json', 'r') as f:
        config = json.load(f)
        DOMAIN = config["DOMAIN"]
        API_KEY = config["API_KEY"]
    return config

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
    r = requests.get(f'https://{DOMAIN}/api.php?user={username}&pass={password}&hwid={hwid}&key={API_KEY}')
    return r.text

def check_subscription_expired(subscription_date):
    curr_date = datetime.datetime.now()
    sub_date = datetime.datetime.strptime(subscription_date, '%Y-%m-%d')
    return curr_date >= sub_date

# Constants
DOMAIN = 'anditv.it/panel' 
API_KEY = 'yes' # api key from config file on panel

# Get the hardware id
hwid = get_hardware_id()
if hwid is None:
    # Log the error and exit
    print("HWID not found.")
    os._exit(0)

if __name__ == "__main__":
    main()