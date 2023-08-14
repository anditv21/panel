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


sys.dont_write_bytecode = True

class Util(commands.Cog):
    def __init__(self, bot):
        self.bot = bot

    @app_commands.command(name="avatar", description="Shows the avatar of a user")
    @app_commands.describe(member="The member whose avatar you want to view")
    async def avatar(self, interaction: discord.Interaction, member: discord.Member = None):
        if member is None:
            member = interaction.user

        embed = discord.Embed(
            title=f"Download {member.display_name}'s Avatar", 
            url=member.
            avatar,
            color=0x00EFDB
        ).set_author(
            name=f"{member.display_name}'s avatar",
            url=f"https://discord.com/users/{member.id}", 
            icon_url=member.avatar
        ).set_image(
            url=member.avatar
        ).set_footer(
            text=f"Requested by {interaction.user.name}",
            icon_url=interaction.user.avatar
        )
        await interaction.response.send_message(embed=embed)

    @app_commands.command(name="userinfo", description="Shows information about a user")
    @app_commands.describe(member="About which member do you want to get infos?")
    async def userinfo(self, interaction: discord.Interaction, member: discord.Member = None):
        if member is None:
            member = interaction.user

        user_created_at = member.created_at.strftime("%b %d, %Y %I:%M %p")
        joined_at = member.joined_at.strftime("%b %d, %Y %I:%M %p")

        embed = discord.Embed(
            color=member.color
        ).set_thumbnail(
            url=member.display_avatar
        ).set_author(
            name=f"{member.display_name}'s Info",
            icon_url=member.avatar
        ).add_field(
            name="Name",
            value=f"```{member.name}```",
            inline=False
        )   .add_field(
            name="Display Name",
            value=f"```{member.display_name}```",
            inline=False
        ).add_field(
            name="Global Name",
            value=f"```{member.global_name}```",
            inline=False
        ).add_field(
            name="ID",
            value=f"```{member.id}```",
            inline=False
        ).add_field(
            name="Creation",
            value=f"```{user_created_at}```",
            inline=False
        ).add_field(
            name="Avatar",
            value=f"[Click here]({member.avatar})",
            inline=False
        ).add_field(
            name="Joined",
            value=f"{joined_at}",
            inline=True
        ).add_field(
            name="Nickname",
            value=f"{member.nick}",
            inline=True
        ).add_field(
            name="Highest Role",
            value=f"{member.top_role.mention}",
            inline=True
        )
        await interaction.response.send_message(embed=embed, ephemeral=False)

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