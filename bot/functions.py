import aiohttp
from helpers.config import get_config_value
async def get_user_count():
    try:
        baseurl = "https://" + get_config_value("domain") + get_config_value("subfolder") + "/api.php?bot=true&key=" + get_config_value("api_key")
        url = baseurl + "&function=usercount"
        async with aiohttp.ClientSession() as session:
            response = await session.get(url=url)
            data = await response.json()
            count = data["text"]
            return count

    except:
        print("Error while getting user count.")