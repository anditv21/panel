import base64
import json
import platform
import sys
from datetime import datetime
from typing import Literal
from urllib.parse import urlparse

import aiohttp
import cpuinfo
import discord
from discord import app_commands
from discord.ext import commands
from functions import get_user_avatar, generate_sub, generate_inv

sys.dont_write_bytecode = True

class Util(commands.Cog):
    def __init__(self, bot):
        self.bot = bot

    @app_commands.command(name="avatar", description="Shows the avatar of a user")
    @app_commands.describe(member="The member whose avatar you want to view")
    async def avatar(self, interaction: discord.Interaction, member: discord.Member = None):
        if member is None:
            member = interaction.user

        try:
            avatar_url = await get_user_avatar(str(member.id))
        except Exception as e:
            await interaction.response.send_message(content=f"An error occurred while fetching the avatar: {e}")
            return

        embed = discord.Embed(
            title=f"{member.display_name}'s Avatar",
            description=f"[Download Avatar]({avatar_url})",
            color=0x00EFDB
        ).set_author(
            name=f"{member.display_name}'s avatar",
            url=f"https://discord.com/users/{member.id}",
            icon_url=avatar_url
        ).set_image(
            url=avatar_url
        ).set_footer(
            text=f"Requested by {interaction.user.display_name}",
            icon_url=interaction.user.avatar
        )
        await interaction.response.send_message(embed=embed)

    @app_commands.command(name="generate_sub", description="Generates a new sub")
    async def gen_sub(self, interaction: discord.Interaction, time: Literal["Trail", "1m", "3m"]):
        member = interaction.user
        inv = await generate_sub(time, member.id)
        embed = discord.Embed(description=inv, color=0x00D9FF)
        await interaction.response.send_message(embed=embed, ephemeral=True)

    @app_commands.command(name="generate_inv", description="Generates a new invitation")
    async def gen_inv(self, interaction: discord.Interaction):
        member = interaction.user
        inv = await generate_inv(member.id)
        embed = discord.Embed(description=inv, color=0x00D9FF)
        await interaction.response.send_message(embed=embed, ephemeral=True)

    @app_commands.command(name="ping", description="Pong")
    async def ping(self, interaction: discord.Interaction):

        # Calculate the ping in milliseconds
        ping_ms = round(self.bot.latency * 1000)

        if ping_ms <= 50:
            color = 0x44FF44
        elif ping_ms <= 100:
            color = 0xFFD000
        elif ping_ms <= 200:
            color = 0xFF6600
        else:
            color = 0x990000

        embed = discord.Embed(title="PING", description=f"Pong! The ping is **{ping_ms}** milliseconds!", color=color,)

        await interaction.response.send_message(embed=embed)

async def setup(bot):
    await bot.add_cog(Util(bot))