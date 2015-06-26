FORMAT: 1A
HOST: http://syrup.keboola.com/oauth

# OAuth Manager
Create and manage Credentials for API resources utilizing OAuth 1.0 and 2.0


## TODO
- scope is missing

# Group API

## Generate OAuth token for OAuth 1.0 applications [/{version}{?token,id,api}]

### Generate token from a web form/UI [POST]

+ Parameters

    + version: `oauth20` (enum[string], required) - OAuth version string

        + Members
            + `oauth10` - For OAuth 1.0
            + `oauth20` - For OAuth 2.0

    + token = `` (required, string, `305-78945-rg48re4g86g48gwgr48e6`) ... Your KBC Token

    + id = `` (required, string, `main`) ... Credentials configuration identifier to be saved with the result

    + api = `` (required, string, `yourApp`) ... Identifier of the API

    + description = `` (optional, string, `Someone's account`) ... Credentials description (eg. account name)

+ Request (multipart/form-data; boundary=----WebKitFormBoundaryC5GD12ZfR1D8yZIt)
    + Body

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="token"

            305-78954-d54f6ew4f84ew6f48ewq4f684q
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="id"

            main
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="api"

            yourApp
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt
            Content-Disposition: form-data; name="description"

            Credentials description (eg. account name)
            ------WebKitFormBoundaryC5GD12ZfR1D8yZIt--

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "id": {
                        "type": "string",
                        "required": true
                    },
                    "token": {
                        "type": "string",
                        "required": true
                    },
                    "api": {
                        "type": "string",
                        "required": true
                    },
                    "description": {
                        "type": "string",
                        "required": false
                    }
                }
            }

+ Response 201 (application/json)

        {
            "status": "ok",
            "...": "(results from the API containing token etc)"
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

## Retrieve credentials [/credentials/{api}/{id}]

### Get Credentials [GET]

+ Request (application/json)

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token

    + Body

            {
                "includeApiDetail": 0
            }

    + Schema

            {
                "type": "object",
                "required": true,
                "properties": {
                    "includeApiDetail": {
                        "type": "bool",
                        "required": false
                    }
                }
            }

+ Response 201 (application/json)

        {
          "access_token": "dsjioafhoiy832yt598y7895y",
          "refresh_token": "kf98v0894u8j580jy8902xyjciurewc",
          "token_type": "Bearer"
        }

### Delete credentials [DELETE]

+ Request

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token


+ Response 204

## List credentials [/credentials/{api}]

### Get Credentials list for the project [GET]

+ Request

    + Headers

            Accept: application/json
            X-StorageApi-Token: Your-Sapi-Token

+ Response 200 (application/json)

        [
            {
                "oauth_version": "2.0",
                "api": "wr-dropbox",
                "consumer_key": "w51y7j30ovhe412",
                "data": "{\"access_token\":\"fwg4w8g64rgew46g486w4g648wr4g8r4ew6g486w48g6w6\",\"token_type\":\"bearer\",\"uid\":\"42586988\"}",
                "project": "305",
                "id": "test",
                "description": "Kachna's Dropbox"
            }
        ]

## Add API

Add a new API configuration / consumer keys

Can be added per project by a project admin, or a KBC-wide by Keboola devs
