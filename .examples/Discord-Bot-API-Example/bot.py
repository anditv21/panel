import asyncio
import os
import platform
import sys
from datetime import datetime
from urllib.parse import quote

import discord
from colorama import Fore
from discord.ext import commands, tasks

from helpers.config import get_config_value
from helpers.general import (clear_console)
from functions import get_user_count, get_linked_users

sys.dont_write_bytecode = True


token = get_config_value("token")

class Bot(commands.Bot):
    def __init__(self, *, intents: discord.Intents):

        super().__init__(command_prefix=commands.when_mentioned_or("$$"), intents=intents)

    async def setup_hook(self):
        clear_console()
        print("[BOT] Loading cogs...")
        for filepath in os.listdir('cogs'):
            for filename in os.listdir(f'cogs/{filepath}'):
                if filename.endswith('.py'):
                    filename = filename.replace('.py', '')
                    try:
                        await bot.load_extension(f'cogs.{filepath}.{filename}')
                    except Exception as error:
                        print(f'Failed to load cogs.{filepath}.{filename}: {error}')                      
        print("[BOT] Connecting to discord...")
        await self.tree.sync()


intents = discord.Intents.all()
intents.presences = True
intents.members = True
intents.guilds = True
bot = Bot(intents=intents)
bot.remove_command("help")


@bot.event
async def on_ready():
    await bot.change_presence(status=discord.Status.idle)
    await bot.change_presence(status=discord.Status.idle, activity=discord.Activity(type=discord.ActivityType.watching, name="anditv.dev"),)
    print(f'[BOT] has connected as {bot.user} via discord.py {discord.__version__}')

    bg_task.start()



@tasks.loop(seconds=5)
async def bg_task():
    await bot.wait_until_ready()

    # Get linked users from the API
    linked_users_response = await get_linked_users()

    usercount = await get_user_count()
    rename_users = get_config_value("rename_users")

    status_list = [
        (discord.Status.dnd, discord.Activity(
            type=discord.ActivityType.watching, name="github.com/anditv21/panel")),
        (discord.Status.dnd, discord.Activity(
            type=discord.ActivityType.watching, name="anditv.dev")),
        (discord.Status.dnd, discord.Activity(
            type=discord.ActivityType.watching, name=f"{usercount} users"))
    ]

    current_index = 0
        while not bot.is_closed() and current_index < len(status_list):
            status, activity = status_list[current_index]
            try:
                await bot.change_presence(status=status, activity=activity)
                if rename_users == "true" or rename_users == "True":
                    guild = bot.get_guild(int(get_config_value("guild_id")))
                    if guild:
                        for user_data in linked_users_response:
                            dcid = user_data['dcid']
                            display_name = user_data.get('displayname')
                            username = user_data.get('username')

                            member = guild.get_member(int(dcid))
                            if member:
                                if member.id == int(dcid):
                                    if display_name:
                                        nick = f"{display_name} ({user_data['uid']})"
                                    else:
                                        nick = f"{username} ({user_data['uid']})"
                                    await member.edit(nick=nick)
                                else:
                                    print(f"Member ID mismatch: {member.id} != {dcid}")
                            else:
                                print(f"Member not found: {dcid}")
                    else:
                        print(f"Guild not found {get_config_value('guild_id')}")

                await asyncio.sleep(5)
            except discord.HTTPException as e:
                print(f"Error occurred while changing presence: {e}")

            current_index += 1


"""
@bot.event
async def on_message(message):
    print(str(message.content))
"""

bot.run(token=token, log_level=40)
