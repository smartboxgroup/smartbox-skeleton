---
name: Create a new sync flow
title: Create a new sync flow
permalink: samples/syncflow
---

# Create a new sync flow

The Framework Bundle allows us to specify whether our endpoint will behave in a synchronous or asynchronous manner. 
Synchronous flows expect to recieve a request, make a request and then provide a response while maintaining the initial connection. 
This is generally used in GET requests, where information is required at the time of the request. 
Specifying whether a flow is synchronous or asynchronous should be tied to the API endpoint that we expose for the entry point to the flow, however, 
in the Skeleton Bundle we are not doing this in the best manner, but instead we make this decision simply in the Controller or the Command instead of an API configuration. 


This is "faked" somewhat in the Skeleton Bundle as we have a Symfony Command that allows us to trigger a synchronous flow, 
and also in our Controller ``` Controller/ApiController.php``` we make the assumption that whenever we receive a GET request, 
this is for a synchronous flow and if we receive a POST request, that that is for an asynchronous request.
Both the Command and the Controller make a call to the ``` Services/RequestHandlerService.php``` Service that handles the requests to the enpoint factory. 
This will be clarified below with code samples.

In the Skeleton Bundle we define our flow ``` Ping.xml``` as follows:

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

This will route to our ``` remote_system_api``` producer. To understand more about how to create that producer please see the documentation "Create a new JSON Producer".


```php

 } elseif ('GET' == $request->getMethod()) {//assumed always sync for demo
....
$responseMessage = $this->send($methodName, $data, false);
....

```
Where the third parameter "false" tells the called functions that ``` async = false```.

The main call to the service is placed in function in the Controller called ``` send ``` .

```php

protected function send($methodName, $data, $async)
    {
        $requestHandler = $this->get('smartesb_skeleton_request_handler');
        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => uniqid('', true),
            Context::ORIGINAL_FROM => 'api',
        ]);

        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            $methodName,
            $data,
            [],
            $context,
            $async
        );

        return $responseMessage;
    }

```

In both our Command and Controller we make a call to the function ``` handleCall``` in the ``` RequestHandlerService``` Service.
```php 
 
 public function handleCall(
         $serviceName,
         $apiVersion,
         $methodName,
         $messageBody,
         $messageHeaders,
         $context,
         $async = false
     ) {
         $apiPrefix = 'api';
         $fromUri = $apiPrefix.'://entry/'.$serviceName.'/'.$apiVersion.'/'.$methodName;
         $helper = $this->getContainer()->get('smartesb.helper');
         $messageFactory = $helper->getMessageFactory();
         $contextExtra = [];
         $contextExtra['from'] = $fromUri;
         $priority = 'normal';
         $contextExtra['api_mode'] = 'real';
         $contextExtra['priority'] = $priority;
         $context = new Context(array_merge($context->toArray(), $contextExtra));
         $messageHeaders[Message::HEADER_FROM] = $fromUri;
         $messageHeaders['api_mode'] = 'real';
         $messageHeaders['async'] = $async ? 'true' : 'false';
         $message = $messageFactory->createMessage($messageBody, $messageHeaders, $context);
         $endpoint = $this->getContainer()->get('smartesb.endpoint_factory')->createEndpoint($message->getHeader(Message::HEADER_FROM), EndpointFactory::MODE_CONSUME);
         $resultMessage = $endpoint->handle($message);
 
         return $resultMessage;
     }
 
 ```

As you can see our default is to make a synchronous call. 
In this function we set the message header ``` $messageHeaders['async']``` which will be later interpreted by the Service ``` smartesb.endpoint_factory```).

This same call call is made from the Symfony Command: ``` Command/SkeletonSendPingCommand.php```.

```php

$responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            'ping',
            $pingMessage,
            [],
            $context,
            false
        );
        
```

So, to sum up, the deciding factor between a synchronous flow and asynchronous flow is simply a message header that we set. 
This should be associated with an api configuration but currently in the Skeleton Bundle we are not doing this. 
The RequestHandlerService in the Skeleton Bundle handles the creation and preparation of the message to be passed to the Enpoint Factory. 
