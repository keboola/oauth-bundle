FORMAT: 1A
HOST: http://syrup.keboola.com/oauth

# oauth

# Database structure

The database should contain the following tables:
## OAuth 1.0 consumers
- cols:
    - api identifier
    - api friendly name (optional?)
    - requestTokenUrl
    - accessTokenUrl
    - authenticateUrl - needs to contain the oauthToken in a parameter - use %%?
    - consumerKey
    - consumerSecret

## OAuth 2.0 consumers
- cols:
    - api identifier
    - api friendly name (optional?)
    - tokenUrl
    - oauthUrl - needs redirect URL (always the same here?), clientId, and optionally the security/session hash
    - clientId
    - clientSecret

## User credentials
- cols:
    - OAuth version
    - consumer link (name+consumer key?)
    - JSON encoded credentials - result of the OAuth process, storing everything the API returns


## TODO
- scope is missing

# Group API

## Generate OAuth token [/oauth{?token,id,api}]

### Generate token from a web form/UI [POST]

+ Parameters
    + token = `` (required, string, `305-78945-rg48re4g86g48gwgr48e6`) ... Your KBC Token

    + id = `` (required, string, `main`) ... Credentials configuration identifier to be saved with the result

    + api = `` (required, string, `yourApp`) ... Identifier of the API

+ Request (multipart/form-data; boundary=----WebKitFormBoundaryC5GD12ZfR1D8yZIt)
    + Body

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="token"

            305-78954-d54f6ew4f84ew6f48ewq4f684q
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="config"

            main
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "id": {
                        "type": "string",
                        "required": true
                    }
                    "token": {
                        "type": "string",
                        "required": true
                    }
                    "api": {
                        "type": "string",
                        "required": true
                    }
                }
            }

+ Response 201 (application/json)

        {
            "status": "ok"
        }

### Generate token manually [GET]

+ Parameters
    + token = `` (required, string, `305-78945-rg48re4g86g48gwgr48e6`) ... Your KBC Token

    + id = `` (required, string, `main`) ... Credentials configuration identifier to be saved with the result

    + api = `` (required, string, `yourApp`) ... Identifier of the API

+ Response 201 (application/json)

    {
      "status": "ok",
      "access_token": "dsjioafhoiy832yt598y7895y",
      "refresh_token": "kf98v0894u8j580jy8902xyjciurewc",
      "token_type": "Bearer"
    }

## Retrieve credentials [/fetch{?token,api,id}]

+ Request (application/json)

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token

    + Body

            {
                "api": "yourApp",
                "id": "main"
            }

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "id": {
                        "type": "string",
                        "required": true
                    }
                    "api": {
                        "type": "string",
                        "required": true
                    }
                }
            }

+ Response 201 (application/json)

    {
      "access_token": "dsjioafhoiy832yt598y7895y",
      "refresh_token": "kf98v0894u8j580jy8902xyjciurewc",
      "token_type": "Bearer"
    }

## Add API

Add a new API configuration / consumer keys

Can be added per project by a project admin, or a KBC-wide by Keboola devs
