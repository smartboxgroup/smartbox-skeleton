---
name: Callback flow
title: Callback flow
permalink: samples/callbackflow
---

# Callback flow

When we enact an asynchronous flow, the message is prepared and create, sent to the queuing system, consumed and that is it. 
The calling system that triggered all of this by sending a request to our ESB will get a positive response back straight away once the message has been queued.
This means that it does not full know if the message has been delivered successfully by our ESB but assumes that it has been.
A callback allows us to send a message back to that system once we have delivered the original message. 
This means, although we will deliver the original message at some later time, the originating system can be made aware of the message's delivery status, positive or negative. 
A small caveat to this is that our callback doesn't necessarily have to go to the originating system, it can go to a third system.  
From the routing standpoint this callback is no different to any other flow. This means that the callback can be delivered asynchronously or synchronously. 
It also means that we can use the callback to write to any type of target, for example we can set our target system as a service that writes to a database, or we can broadcast to multiple target systems. 
With regard the Skeleton Bundle we don't really have an originating system as we are triggering the flow from a Symfony Command or our fake API/basic controller.
So, in our case we will send the callback to a basic controller in the SmartboxSkeletonBundle.
