---
name: Throttle a flow
title: Throttle a flow
permalink: samples/throttle
---

# Throttle a flow

Throttling a flow can allow us to control the rate at which we send requests to a target. 
This is very important as it allows us to meet the processing rate of the target system and essentially prevent our ESB from hammering a system with requests. 
The throttle limit can be set in a flow and as such will allow us to dictate a different speed for different targets.

Here is an example of our Async Ping flow being throttled. 

```xml

<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:camel="http://camel.apache.org/schema/spring" xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans.xsd http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">

    <camelContext trace="false" xmlns="http://camel.apache.org/schema/spring">
        <route>
            <from uri="api://execute/skeleton/v0/asyncping"/>
            <throttle timePeriodMillis="1000" asyncDelayed="true">
                <simple>'2'</simple>
                <to uri="rest://remote_system_api/sendPingMessage">
                    <description>Throttled async ping flow which will only send 2 message per second. </description>
                </to>
            </throttle>
        </route>
    </camelContext>
</beans>

```

This will send 2 messages per second to the target system. 
Note that in a broadcast we can throttle at different rates to different remote systems.
