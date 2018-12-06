---
name: Create a new JSON Producer
title: Create a new JSON Producer
permalink: samples/jsonproducer
---

# Create a new JSON Producer

Here we will look at how we create a new producer, specifically a REST producer that will send JSON requests to a target system.
Please note that the code samples below are taken from what was built in the Skeleton Bundle. This should hopefully provide some consistency between the descriptions here and the actual code. 

The main functionality for creating a producer is included in the Framework Bundle so our new producer will extend that class:
``` use Smartbox\Integration\FrameworkBundle\Components\WebService\Rest\RestConfigurableProducer;```

Here is what our new producer will look like: (``` Producers/RemoteSystemApiProducer.php```)

```php
<?php

namespace SmartboxSkeletonBundle\Producers;

use Smartbox\Integration\FrameworkBundle\Components\WebService\Rest\RestConfigurableProducer;
use Smartbox\Integration\FrameworkBundle\Tools\SmokeTests\CanCheckConnectivityInterface;

/**
 * Class RemoteSystemApiProducer.
 */
class RemoteSystemApiProducer extends RestConfigurableProducer implements CanCheckConnectivityInterface
{
    /**
     * {@inheritdoc}
     */
    public function checkConnectivityForSmokeTest(array $config = array())
    {
        //not implemented for now
    }

    /**
     * {@inheritdoc}
     */
    public static function getConnectivitySmokeTestLabels()
    {
        return 'important';
    }
}

```

This, however, is not enough for the system to know how to route to this producer, hence we will need to add some configuration.
Firstly we should create a YAML configuration that will descrive the functions of the producer. 
In the Skeleton Bundle this is in the file: ``` app/config/producers/remote_system_api.yml```.

```yaml
 
 services:
     smartbox.clients.rest.remote_system_api:
         class: GuzzleHttp\Client
         arguments: [{timeout: 0, allow_redirects: false, verify: false}]
         lazy: true
         tags:
           - { name: mockable.rest_client, mockLocation: "external_system_response_cache_dir%/remoteSystemApi" }
 
 smartbox_integration_framework:
     mappings:
         pingToRemoteApi:
             message: obj.getMessage()
             time: obj.getTimestamp()
     producers:
         remote_system_api:
             class: SmartboxSkeletonBundle\Producers\RemoteSystemApiProducer
             description: producers to connection to a remote api
             calls:
               - [setName, ['remote_system_api'] ]
               - [setHttpClient, ['@smartbox.clients.rest.remote_system_api'] ]
               - [setValidator, ['@validator']]
               - [setHydrator,['@smartcore.hydrator.group_version'] ]
 
             options:
                 encoding: json
                 base_uri: '%remote_system_api.base.uri%'
                 authentication: 'none'
                 username: '%remote_system_api.username%'
                 password: '%remote_system_api.password%'
 
 
             methods:
                 sendPingMessage:
                     description: 'Send ping to a remote api'
                     steps:
                       - define:
                           PingMessage: "eval: body"
                       - request:
                           name: sendPingMessage
                           http_method: POST
                           uri: /smartbox-skeleton/web/remote/pong
                           body: "eval: mapper.map(PingMessage, 'pingToRemoteApi')"
                           validations:
                               - rule: "eval: responses['sendPingMessage']['statusCode'] == 200"
                                 message: "eval: 'Enexpected response from Web Api: ' ~ responses['sendPingMessage']['statusCode']"
                                 recoverable: true
                                 display_message: true
                     response:
                         body: "eval: responses['sendPingMessage']['body']"

 
 ```
 
 As you can see from looking at the YAML above, we have called our producer "remote_system_api" and link it to our PHP class on the line: ``` class: SmartboxSkeletonBundle\Producers\RemoteSystemApiProducer```.
 There are quite a number of configuations here but most noteworthy are the "mappings" and the "methods". Once you have set up your producer and configuration, you will most likely use this two frequently to transform content and send it via different methods to different end points. 
 At the time of writing we only have one method defined, "sendPingMessage". This method specifies where we are to send data and how. 
 
 The last peice of the pie is to specify a routing URI that will describe how we will route to our new producer. We have done this in the file: ``` app/config/routing_endpoints/routing_external_systems.yml```.
 
 ```yaml

remote_system_api:
  path: "rest://remote_system_api/{method}"
  defaults:
    _protocol: @smartesb.protocols.configurable.rest
    _producer: @smartesb.producers.remote_system_api
```

So, if we are defining a flow to send to our "sendPingMessage" method, we will route to the URI: 
``` rest://remote_system_api/sendPingMessage ```

The file "routing_external_systems.yml" is included in our file:  "routing_endpoints.yml"

```yaml
external_systems:
    resource: routing_external_systems.yml
    defaults:
    requirements:
      method: "[a-zA-Z0-9]+"

```

which in turn is included in the following files:

``` 
app/config/routing_endpoints_dev.yml

app/config/routing_endpoints_prod.yml

app/config/routing_endpoints_test.yml

```
by the line:
```yaml
imports:
    resource: routing_endpoints/routing_endpoints.yml
```

The above files are loaded by the Framework Bundle in the config file: ``` /vendor/smartbox/integration-framework-bundle/Resources/config/routing.yml```

```yaml
  smartesb.router.endpoints:
      class: %smartesb.internal_router.class%
      arguments:
        - '@service_container'
        - "%kernel.root_dir%/config/routing_endpoints_%kernel.environment%.yml"
        - %smartesb.router.endpoints.options%
      tags:
        - { name: monolog.logger, channel: "router.endpoints" }
```

The Framework Bundle will handle the creation of our producer during the compiler pass. As for the routing, the Camel Config Bundle will create the routes that we can then specify in our XML, for example:

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:camel="http://camel.apache.org/schema/spring" xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">

    <camelContext trace="false" xmlns="http://camel.apache.org/schema/spring">
        <route>
            <from uri="api://execute/skeleton/v0/ping"/>
            <to uri="rest://remote_system_api/sendPingMessage" />
        </route>
    </camelContext>
</beans>
```


With regard to the "mappings" section defined in the YAML configuation for the producer, this allows us to transform our Entity's fields to meet the requirements of the remote systems API endpoint. 
In our example we have the following:
```yaml
mappings:
     pingToRemoteApi:
         message: obj.getMessage()
         time: obj.getTimestamp()
```

This mapping is refrerenced in the "request" part of of the "sendPingMessage" method:

``` body: "eval: mapper.map(PingMessage, 'pingToRemoteApi')"```


However, if the remote endpoint expected the message to be named as "ping_message" we can easily match their expectation by altering our YAML configuration. 
```yaml
mappings:
     pingToRemoteApi:
         ping_message: obj.getMessage()
         time: obj.getTimestamp()
```
For another example, say that the target system requires the date in a particular format, we can do that transformation before we send it. 
```yaml
mappings:
     pingToRemoteApi:
         ping_message: obj.getMessage()
         time: mapper.formatDate('Y/m/d', obj.getTimestamp())
```


```yaml
mappings:
     pingToRemoteApi:
         ping_message: obj.getMessage()
         time: mapper.formatDate('Y/m/d H:i:s', obj.getTimestamp())
```

Note that we can use the default Symfony Expression Language functions here and also custom expressions that have been added to the Framework Bundle:

``` /vendor/smartbox/integration-framework-bundle/Tools/Evaluator/ExpressionEvaluator.php```

and 

``` /vendor/smartbox/integration-framework-bundle/Tools/Evaluator/CustomExpressionLanguageProvider.php```

Have a look at these classes to see what they have to offer. 

