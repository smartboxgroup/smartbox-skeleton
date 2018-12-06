---
name: Freeze flows
title: Freeze flows
permalink: samples/freezeflows
---

# Freeze flows

As part of the SmartESB we have the ability to freeze different versions of flows. 
This is very useful for for a number of reasons, the main one being it allows us to set in stone what flows we will release.
So if we froze the current flows in the Skeleton Bundle, they would be at version 0, and the "real" flows would be at version 1.
This means that for our production environments we can point to (in parameters.yml) version 0 of the flows because we know they have been tested, but on our development we can point to version 1 and take advantage of new flows that were added since the freeze point.
This can work quite well with most development life cycles.

So, now to the part on how to freeze the flows, this is done using a Symfony Command in the class FreezeFlowsCommand.php in the Camel Config Bundle. 
We run the following and it will take care of the rest.

```bash
php app/console smartesb:flows:freeze
```

Depending on your configuration and where you have set the location of "frozen_flows_directory" in config.yml, you can go to that directory and now you will see a new folder for your frozen flows.
The version of the new frozen flows will be the current version plus one.

To change between version you can modify "flows.version: " in parameters.yml to point to the version of the flows you want. 
If you set the number to a flow version that does not exist it will default to the "real" flows.
