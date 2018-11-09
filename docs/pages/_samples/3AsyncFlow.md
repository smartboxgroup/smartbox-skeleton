---
name: Create a new async flow
title: Create a new async flow
permalink: samples/asyncflow
---

# Create a new async flow

As was mentioned in the section on creating a synchronous flow, the only thing that dictates whether a flow is synchronous or asychronous is a header that we set before we pass the message to the enpoint factory.
In the case of creating an asynchronous flow we will set the header "async" to true.
This can be seen in use in the Symfony Command "SkeletonSendAsyncPingCommand".

```php
        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            'asyncping',
            $pingMessage,
            [],
            $context,
            true
        );
```

As was mentioned before, this really should be part of a configuration for a specific API endpoint, however at this time we do not have that implemented in the Skeleton Bundle.
With regard to the Camel XML for the ping async flow(AsyncPing.xml), there really is little difference in how it operates compare to the synchronous version(Ping.xml).

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:camel="http://camel.apache.org/schema/spring" xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">

    <camelContext trace="false" xmlns="http://camel.apache.org/schema/spring">
        <route>
            <from uri="api://execute/skeleton/v0/asyncping"/>
            <to uri="rest://remote_system_api/sendPingMessage" />
        </route>
    </camelContext>
</beans>

```

Once we receive a request to create an asynchronous message, this is then sent to a queuing system to be processed at a later time by our consumers. 
This is mocked somewhat in our Symfony Command.
See the section "Set up workers to consume messages" to understand more about how consumers work.
 