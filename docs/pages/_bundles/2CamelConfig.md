---
name: CamelConfig
title: CamelConfig
permalink: bundles/camelconfig
---

# CamelConfig

## The goal of the bundle
CamelConfig is a bundle designed to parse the Apache Camel XML flows and translate this into Symfony services.  

## Main features

* Easy to use and implement.
* Route versioning.
* Full support for complex routing, like multicasts or recipient lists.
* Clear route definition, based on Apache's specification.
* Fully compatible with Symfony 2.8. (Compatibility with 3.4 is part of [the roadmap](smartesb-skeleton/roadmap))
* Battle tested.

## Installation
Installation is as easy as installing a new bundle in your Symfony application. First start by running `composer`:

`$ composer require smartbox/camel-config-bundle`

Then you need to enable the bundle in your kernel.

```php
// app/AppKernel.php

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = [(
            new Smartbox\Integration\CamelConfigBundle\SmartboxIntegrationCamelConfigBundle(),
            (...)
```

And you're all set.

## Usages
## How FlowsBuilder compiler pass works
To see more details on how the ```FlowsBuilder``` compiler pass works click [here](/smartesb-skeleton/camel/flows-builder)

## How to build a flow

For asynchronous flows follow [this guide](/smartesb-skeleton/samples/asyncflow), for synchronous, [this one](/smartesb-skeleton/samples/syncflow).

## Use Eclipse to build a flow

[JBoss Tools](http://marketplace.eclipse.org/content/jboss-tools) is a plugin for Eclipse that can aid you visually in the process of designing Apache Camel flows. 

After installing it from Eclipse's Marketplace you can create a new file by selecting `Red Hat Fuse/Camel XML File`. This will create a base file but you can get creative **by dragging and dropping** the various components of the routes.

![Sample Camel Routing](/smartesb-skeleton/assets/images/camel_routing_sample.jpg)

To create a simple direct routing, just drag two `Direct` components one after the other. Eclipse **will join them for you**. Clicking on the components and opening the properties view will allow you to edit the uri, description and ID.

![Camel Route Direct](/smartesb-skeleton/assets/images/camel_single_route.jpg)

Once you're happy with the flow, you can export it by copying and pasting the source code present **in the "Source" tab**.

```
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
    xmlns:camel="http://camel.apache.org/schema/spring"
    xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd        http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">
    <camelContext id="camelContext-1d64046c-4742-4234-b309-9caffff27908"
        trace="false" xmlns="http://camel.apache.org/schema/spring">
        <route id="_route1">
            <from id="_from1" uri="direct:name"/>
            <to id="_to1" uri="direct:name"/>
        </route>
    </camelContext>
</beans>
```

## How to add a new component of the camel definition
## How to freeze a flow version

You can see the details of freezing a flow [here](smartesb-skeleton/samples/freezeflows) but it all boils down to run the following command:

```bash
php app/console smartesb:flows:freeze
```

The freeze flows command extends Symfony's Command class so it's automatically registered in your application's console.

This will generate all the files in your configured frozen flows folder. Then update the frozen flows version in your `parameters.yml` to make sure that you're using the frozen ones and not the real one. 

