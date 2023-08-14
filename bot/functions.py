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
        logging.error("Aiohttp client error: %s", client_error)
        raise 
    except ValueError as value_error:
        logging.error("Value error: %s", value_error)
        raise 
    except Exception as e:
        logging.error("Error while getting user count: %s", e)