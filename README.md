FORMAT: 1A
HOST: http://syrup.keboola.com/oauth

# OAuth Manager
Create and manage Credentials for API resources utilizing OAuth 1.0 and 2.0


## TODO
- scope is missing

# Group API

## Generate OAuth token for OAuth 1.0 applications [/{version}{?token,id,api,description}]

### Generate token from a web form/UI [POST]

+ Parameters

    + version: `auth20` (enum[string], required) - OAuth version string

        + Members
            + `auth10` - For OAuth 1.0
            + `auth20` - For OAuth 2.0

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
          "data": "{\"access_token\":\"gg486w4g8wr46g48r4w86g468rw486g4-g1w23gwgw\",\"token_type\":\"bearer\",\"uid\":\"654987312\"}",
          "description": "test",
          "consumer_key": "f1f86w1f6w8efw",
          "oauth_version": "2.0",
          "creator": "{\"id\":\"321\",\"description\":\"kachnuela@example.com\"}"
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
            "data": "{\"access_token\":\"g4r8w6g48rw4g8w46g8w4g4re4g6ew4g8r64wgw-eg4w86ggwgg\",\"token_type\":\"bearer\",\"uid\":\"456789123\"}",
            "description": "test",
            "id": "test",
            "creator": "{\"id\":\"321\",\"description\":\"kachnuela@example.com\"}"
          }
        ]

## Add API

Add a new API configuration / consumer keys

Can be added per project by a project admin, or a KBC-wide by Keboola devs
