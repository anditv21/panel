import aiohttp
import logging
from helpers.config import get_config_value

async def get_user_count():
    try:
        baseurl = "https://" + get_config_value("domain") + get_config_value("subfolder") + "/api.php?bot=true&key=" + get_config_value("api_key")
        url = baseurl + "&function=usercount"
        async with aiohttp.ClientSession() as session:
            response = await session.get(url=url)
            response.raise_for_status()
            data = await response.json()
            count = data.get("text")
            if count is not None:
                return count
            else:
                raise ValueError("Invalid response format: 'text' key not found in response data")

    except aiohttp.ClientError as client_error:
        print("Aiohttp client error: %s", client_error)
        raise 
    except ValueError as value_error:
        print("Value error: %s", value_error)
        raise 
    except Exception as e:
        print("Error while getting user count: %s", e)
        
async def get_user_avatar(dcid):
    try:
        baseurl = "https://" + get_config_value("domain") + get_config_value("subfolder")
        url = f"{baseurl}/api.php?bot=true&key={get_config_value('api_key')}&function=getbydcid&dcid={dcid}"
        async with aiohttp.ClientSession() as session:
            response = await session.get(url=url)
            response.raise_for_status()
            data = await response.json()
            avatar_url = data.get("avatar_url")
            if avatar_url is not None:
                return avatar_url
            else:
                raise ValueError("Invalid response format: 'avatar_url' key not found in response data")

    except aiohttp.ClientError as client_error:
        print("Aiohttp client error: %s", client_error)
        raise 
    except ValueError as value_error:
        print("Value error: %s", value_error)
        raise 
    except Exception as e:
        print("Error while getting user avatar: %s", e)