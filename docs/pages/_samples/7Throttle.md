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

.....