import os
import platform
import sys
from datetime import datetime

from colorama import Fore
from discord.ext import commands

sys.dont_write_bytecode = True

def clear_console():
    try:
        if platform.system() == "Windows":
            os.system("cls")
            print("")
        else:
            os.system("clear")
            print("")
    except Exception as e:
        print(f"Error: {e}")