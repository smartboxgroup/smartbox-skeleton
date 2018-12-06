---
name: Record a Response
title: Record a Response
permalink: samples/recordresponse
---

# Record a Response

Built into the Framework bundle is the ability to record a serialized version of the response from a target system. 
This only applies to the test environment(`APP_ENV=test`) as the functionality is built into the mock clients:
```bash
Smartbox\Integration\FrameworkBundle\Tools\MockClients\FakeRestClient

Smartbox\Integration\FrameworkBundle\Tools\MockClients\FakeSoapClient

``` 

To tell the system to record a response when running a command or when running a test we need to set an environment variable `RECORD_RESPONSE` as true. 
So, if we want to record the response of the ping flow, trigger by the Symfony command we can do the following:

```bash
RECORD_RESPONSE=true MOCKS_ENABLED=false php app/test_console skeleton:send:ping
```
This will create the file `app/Resources/ExternalSystemsResponsesCache/remoteSystemApi/POST_smartesb_skeleton_web_remote_pong.json`

The directory `remoteSystemApi` is specified in the producer YAML configuration file `app/config/producers/remote_system_api.yml`:

```yaml
services:
    smartbox.clients.rest.remote_system_api:
        class: GuzzleHttp\Client
        arguments: [{timeout: 0, allow_redirects: false, verify: false}]
        lazy: true
        tags:
          - { name: mockable.rest_client, mockLocation: "%external_system_responses_cache_dir%/remoteSystemApi" }
```

The contents of the file `POST_smartesb_skeleton_web_remote_pong.json` are as follows at the time of recording:
```json
{
  "status": 200,
  "headers": {
    "Date": [
      "Thu, 06 Dec 2018 08:54:46 GMT"
    ],
    "Server": [
      "Apache\/2.4.29 (Ubuntu)"
    ],
    "Cache-Control": [
      "no-cache"
    ],
    "Content-Length": [
      "97"
    ],
    "Content-Type": [
      "application\/json"
    ]
  },
  "body": "{\"_type\":\"SmartboxSkeletonBundle\\\\Entity\\\\PingMessage\",\"message\":\"Pong\",\"timestamp\":\"1544086486\"}",
  "version": "1.1",
  "reason": "OK"
}
```

Please note that the above and also the contents of the file have been prettified to making the contents clearer to read, the actual response will not be formatted this way. 