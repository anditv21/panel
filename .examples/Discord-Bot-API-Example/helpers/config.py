import json
import os
import sys
from datetime import datetime

sys.dont_write_bytecode = True
time = datetime.now().strftime('%d.%m.%Y %H:%M:%S')

def get_config_value(key: str) -> str:
    config_path = "config.json"
    if not os.path.isfile(config_path):
        parent_dir = os.path.dirname(os.path.abspath(__file__))
        config_path = os.path.join(parent_dir, "..", "config.json")

        if not os.path.isfile(config_path):
            print("[ERROR] config.json not found in the current directory or its parent directory. Please make sure the file exists.")
            sys.exit()

    with open(config_path, "r", encoding="UTF-8") as configfile:
        config = json.load(configfile)
        value = config.get(key)
        if value is None:
            print(f"[ERROR] Value for key '{key}' is missing from config.json. Please check the configuration file and try again.")
            sys.exit()

    return value