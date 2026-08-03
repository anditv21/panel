# API

Endpoint:

```text
https://panel.example.com/api.php
```

Responses are JSON. Replace the example URL and keys with your own values from `LocalConfig.php`.

## User login

Request type: `POST`

Body type: `application/x-www-form-urlencoded`

| Name | Example value | Base64 |
| --- | --- | --- |
| `user` | `admin` | No |
| `pass` | `cGFzc3dvcmQ=` | Yes |
| `hwid` | `TVktSFdJRC0xMjM=` | Yes |
| `key` | `your-api-key` | No |

`pass` in this example decodes to `password`. `hwid` decodes to `MY-HWID-123`.

```bash
curl -X POST "https://panel.example.com/api.php" \
  -d "user=admin" \
  -d "pass=cGFzc3dvcmQ=" \
  -d "hwid=TVktSFdJRC0xMjM=" \
  -d "key=your-api-key"
```

The first successful request saves the HWID when the account does not have one yet.

Example response:

```json
{
  "status": "success",
  "username": "admin",
  "hwid": "MY-HWID-123",
  "sub": 30,
  "banned": 0
}
```

## Bot requests

Request type: `GET`

Base64: No

Every bot request needs these values:

| Name | Value |
| --- | --- |
| `bot` | `true` |
| `key` | Your bot key |
| `function` | Function name from the table below |

Bot requests are only accepted from the panel server itself or an IP added to the API whitelist.

| Function | Additional values |
| --- | --- |
| `usercount` | None |
| `linkedusers` | None |
| `getbydcid` | `dcid` |
| `generate_sub` | `dcid`, `time` |
| `generate_inv` | `dcid` |
| `get_user_notifications` | None |
| `set_delivered` | `id` |

Example user count request:

```bash
curl "https://panel.example.com/api.php?bot=true&key=your-bot-key&function=usercount"
```

Example Discord user request:

```bash
curl "https://panel.example.com/api.php?bot=true&key=your-bot-key&function=getbydcid&dcid=123456789"
```

Example response:

```json
{
  "status": "success",
  "text": 42
}
```
