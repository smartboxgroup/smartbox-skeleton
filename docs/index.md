---
permalink: /
title: Smartbox Integrations Framework Skeleton
---

Smartbox Integrations Framework Skeleton
========
[![Build Status](https://travis-ci.com/smartboxgroup/smartesb-skeleton.svg?branch=master)](https://travis-ci.com/smartboxgroup/smartesb-skeleton)  [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/smartboxgroup/smartbox-skeleton/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/smartboxgroup/smartbox-skeleton/?branch=master) [![Coverage Status](https://coveralls.io/repos/github/smartboxgroup/smartbox-skeleton/badge.svg?branch=master)](https://coveralls.io/github/smartboxgroup/smartbox-skeleton?branch=master)

A Symfony project to get you up and running quickly with the Smartbox Enterprise Service Bus.
This project will demonstrate a bare bones setup of a the Smartbox Open Source bundles.

### Current status

This is the initial version of the skeleton bundle and will require more documentation, code cleanups, more tests and examples of functionality.

This documentation (and the best place to get started) can be found [here on our GitHub Pages](https://smartboxgroup.github.io/smartesb-skeleton/). 

For further information on the Smartbox Integration Framework Bundle please read the [setup guide](https://raw.githubusercontent.com/smartboxgroup/integration-framework-bundle/master/README.md). 

You can access the Smartbox open source bundle via our [GitHub](https://github.com/smartboxgroup/) or using [Packagist.org](https://packagist.org/packages/smartbox/).

### Getting started:
The Skeleton Bundle was built to demonstrate the features of the SmartESB, and as such some things are not as they would be in the real world. 
For example, instead of connecting to an actual remote target system to demonstrate a JSON producer, we connect to a Controller included in this Bundle.
The remote system is mocked by the bundle `SmartboxSkeletonRemoteDemoBundle`.
The main code for the Skeleton project is in the `SmartboxSkeletonBundle`.
This serves the propose of allowing us to demonstrate these features without depending on external systems.

Descriptions will be added to code blocks in time. Please be patient... Or feel free to contribute :D


#### To send pings via the command line and consume use the following:

* `php app/console skeleton:send:ping`
* `php app/console skeleton:send:async-ping`
* `php app/console smartesb:consumer:start queue://api/normal/skeleton/v0/asyncping`

#### To send a synchronous ping via the web api endpoint:

Send a GET request to:
* `http://localhost/skeleton/web/app_dev.php/api/ping`

#### To send an asynchronous ping message via the web api endpoint:
Send a POST request to:

* `http://localhost/skeleton/web/app_dev.php/api/asyncping`

With a PingMessage body:
```json
{
  "_type": "SmartboxSkeletonBundle\\Entity\\PingMessage",
  "message": "Ping",
  "timestamp": "1537122194"
}
```

## Contributing

Please have a play with the code and submit pull requests as you see fit.
Try to keep to the following guidelines.

  - Provide a meaningful description of the changes and a rationale for them
  - Use meaningful branch names e.g. `bugfix/someDescription` or `feature/someDescription`
  - Add test cases for new code
  - Comply with the php-cs standards for Symfony (e.g. `php-cs-fixer someclass.php --rules=@Symfony`)

## Licence

This bundle is distributed under [MIT license](license). © 2019 Smartbox Group LTD.
