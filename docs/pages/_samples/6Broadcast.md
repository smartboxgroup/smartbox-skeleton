---
name: Create a new broadcast flow
title: Create a new broadcast flow
permalink: samples/broadcastflow
---

# Create a new broadcast flow

Say if we want to broadcast the same message to multiple system, this is quite easy as the Framework Bundle will handle all of the heavy lifting for us. 
A potential use case for this type of flow would be if we need to update product price information accross multiple systems, i.e. an e-commerce website & a stock management system.

So lets take the async ping flow included in the Skeleton Bundle and turn it into a broadcast flow. 

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

This could be modified to look like the following..

```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:camel="http://camel.apache.org/schema/spring"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd
       http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">

    <camelContext trace="false" xmlns="http://camel.apache.org/schema/spring">
    <route>
        <from uri="api://execute/skeleton/v0/asyncping"/>
        <multicast strategyRef="fireAndForget">
            <pipeline>
                <to uri="rest://remote_system_api/sendPingMessage">
                    <description>Sends a ping message to remote_system_api</description>
                </to>
            </pipeline>
            <pipeline>
                <to uri="rest://SystemA/sendPingMessage">
                    <description>Sends a ping message to SystemA</description>
                </to>
            </pipeline>
            <pipeline>
                <to uri="soap://SystemB/sendPingMessage">
                   <description>Sends a ping message to</description>
                </to>
            </pipeline>
            <pipeline>
                <to uri="rest://SystemC/sendPingMessage">
                    <description>Sends a ping message to SystemC</description>
                </to>
            </pipeline>
            <pipeline>
                <to uri="service://databaseService/savePingMessage">
                    <description>Saves a ping message to the database</description>
                </to>
            </pipeline>
        </multicast>
    </route>
</camelContext>

</beans>
```
As you can see if is quite simple to take advantage of this powerful feature. 
The above example broadcasts to multiple routes, including sending a soap request, multiple rest requests and finally calling an internal service to save the message in a database.
