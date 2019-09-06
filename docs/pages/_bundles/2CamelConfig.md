---
name: CamelConfig
title: CamelConfig
permalink: bundles/camelconfig
---

# CamelConfig

## The goal of the bundle
CamelConfig is a bundle designed to parse the Apache Camel xml flows and translate this into Symfony services.

## Main features
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
        $bundles = array(
            new Smartbox\Integration\CamelConfigBundle\SmartboxIntegrationCamelConfigBundle(),
            (...)
```

And you're all set.

## Usages
## How FlowsBuilder compiler pass works
To see more details on how the ```FlowsBuilder``` compiler pass works click here [here](/smartesb-skeleton/camel/flows-builder)

## How to build a flow
## Use eclipse to build a flow
## How to add a new component of the camel definition
## How to freeze a flow version

You can see the details of freezing a flow [here](/smartesb-skeleton/samples/freezeflows) but it all boils down to run the following command:

```bash
php app/console smartesb:flows:freeze
```

The freeze flows command extends Symfony's Command class so it's automatically registered in your application's console.

This will generate all the files in your configured frozen flows folder. Then update the frozen flows version in your `parameters.yml` to make sure that you're using the frozen ones and not the real one. 

