---
name: CamelConfig
title: FlowsBuilder
permalink: camel/flows-builder
---

# How FlowsBuilder compiler pass works
One key factor to be able to translate the Apache Camel xml flows into Symfony services is the ```FlowsBuilder``` compiler pass.
This is executed when the symfony container is generated the first time the application is executed.

## Processor definition registry
The first thing that happen in this compiler pass is to get the list of the different ```ProcessorDefinition``` the bundle supports.
A ```ProcessorDefintion``` is a service that defines how a particular element in the Apache Camel xml flow needs to be parsed. 
For instance a ```multicast``` element in the xml flow will be parsed in a different way than a ```recipientList``` element.
All these ```ProcessorDefintion``` services are defined in CamelConfig in the file ```Resources/config/services.yml``` and tagged
with the name ```smartesb.definitions``` that is the tag name the compiler pass uses to find these services. See below some examples:
```yaml
services:
  smartesb.definitions.multicast:
    class: Smartbox\Integration\CamelConfigBundle\ProcessorDefinitions\MulticastDefinition
    tags:
     - { name: smartesb.definitions, nodeName: multicast }
    calls:
     - [setProcessorClass, ["Smartbox\Integration\FrameworkBundle\Core\Processors\Routing\Multicast"]]

  smartesb.definitions.pipeline:
    class: Smartbox\Integration\CamelConfigBundle\ProcessorDefinitions\PipelineDefinition
    tags:
     - { name: smartesb.definitions, nodeName: pipeline }
    calls:
     - [setProcessorClass, ["Smartbox\Integration\FrameworkBundle\Core\Processors\Routing\Pipeline"]]

  smartesb.definitions.recipient_list:
    class: Smartbox\Integration\CamelConfigBundle\ProcessorDefinitions\RecipientListDefinition
    tags:
     - { name: smartesb.definitions, nodeName: recipientList }
    calls:
     - [setEvaluator, ["@smartesb.util.evaluator"]]
     - [setProcessorClass, ["Smartbox\Integration\FrameworkBundle\Core\Processors\Routing\RecipientList"]]
```
Another important point when these services are defined is to add the ```nodeName```. This is the name that the node has in the flow xml file
as you can see in the following example for ```multicast``` and ```pipeline```
```xml
<?xml version="1.0" encoding="UTF-8"?>
<beans xmlns="http://www.springframework.org/schema/beans"
       xmlns:camel="http://camel.apache.org/schema/spring"
       xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
       xsi:schemaLocation="http://www.springframework.org/schema/beans http://www.springframework.org/schema/beans/spring-beans-3.0.xsd
       http://camel.apache.org/schema/spring http://camel.apache.org/schema/spring/camel-spring.xsd">

    <camelContext trace="false" xmlns="http://camel.apache.org/schema/spring">
        <route>
            <from uri="api://execute/skeleton/v0/broadcastping"/>
            <multicast strategyRef="fireAndForget">
                <pipeline>
                    <to uri="rest://remote_system_api/sendPingMessage">
                        <description>Sends a ping message to remote_system_api</description>
                    </to>
                </pipeline>
            </multicast>
        </route>
    </camelContext>
</beans>
```

With these ```ProcessorDefintion``` the next stage is to register these in the ```processorDefintionRegistry```. 
This service will be used in other steps of the complier pass to get references of these definitions.

## Flows directory and flows version
Once all this has been done, the next stage is to get the directories and the version of the flows xml files  that should
be loaded in the application. The ```flows_directories``` and ```frozen_flows_directory``` fields are defined in CamelConfig 
config and the ```flows_version``` is defined in FrameworkBundle config. To get this information, the compiler pass is getting 
the extension class of both bundles
```php
// This loads the class DependencyInjection/SmartboxIntegrationCamelConfigExtension.php from CamelConfig bundle
$extension = $container->getExtension('smartbox_integration_camel_config');

// This loads the class DependencyInjection/SmartboxIntegrationFrameworkExtension.php from Framework bundle
$frameworkExtension = $container->getExtension('smartbox_integration_framework');
```
and from these 2 classes, the compiler pass will call different methods to get this information.

## Current flows
The third step in this process is to load the current version of the flows if the flow version setup doesn't belong to
any frozen flow version.

In this step, the first thing that happens is to get all the ```xml``` filenames inside the ```flows_directories``` path.
For each ```xml``` file, the compiler pass will load the content of the ```xml``` file and will try to build the flow 
using ```Itineraries``` and ```Processors```. In a flow xml file we can find 3 different elements:
- "from" uri node
- "to" uri node (Endpoint)
- another kind of node like ```multicast```, ```recipientList```, ```pipeline```, ```transformer``` and so on. (Processor)

This can be observed in the following example:
```xml
<from uri="api://execute/skeleton/v0/broadcastping"/> <!-- "from" uri node -->
<multicast strategyRef="fireAndForget"> <!-- "multicast" processor -->
    <pipeline> <!-- "pipeline" processor -->
        <to uri="rest://remote_system_api/sendPingMessage"> <!-- "to" uri node -->
            <description>Sends a ping message to remote_system_api</description>
        </to>
    </pipeline>
</multicast>
```

Each flow will have a unique name inside the application. This name is based on the name of the flow file and the version of 
the flow.
The way to connect all the xml nodes in a flow is through ```Itineraries```. An ```Itinerary``` (full namespace 
```Smmartbox\Integration\FrameworkBundle\Core\Itinerary\Itinerary```) is a service where a list of processor ids are stored.
Each xml node has a processor id after the flow is built and the ```Itinerary``` tells which is the next step to be executed 
in a flow.

For each flow, a main ```Itinerary``` is generated assigning the uri defined in the ```from``` xml node.

If an xml node is referring to the ```to``` uri node, then an ```EndpointProcessor``` is built assigning the ```to``` uri, generating and
and registering a processor id for that endpoint that will be added to the ```Itinerary```.

If an xml node is referring to something different than the ```from``` or ```to``` uri nodes, then a ```Processor``` is built.
To determine which ```Processor``` needs to be built, the compiler pass will use the ```processorDefintionRegistry``` service mentioned
early to try to get the ```Definition``` of that node.  With that ```Definition```, the compiler pass knows how that xml node needs 
to be parsed and configured so that at the end the ```Processor``` can be built with a processor id that will be added to the 
```Itinerary```. For instance, if the xml node is defined as ```multicast```, then the compiler
pass will get the ```MulticastDefinition``` and from there the ```MulticastProcessor``` will be built.

## Frozen flows
The last step executed in the ```FlowsBuilder``` compiler pass is to load the different frozen versions of the flows that are 
defined in ```frozen_flows_directory``` path in the same way it was loaded the current version of the flows.

## Built flow example
If we take the example of the ```broadcastping``` flow xml mentioned before in this documentation, at the end of the compiler pass 
execution we will have something like the following in ```appProjectContainer.php``` file ```/app/cache``` folder

Associate the ```from``` uri of the xml node to the main ```Itinerary``` id
```php
protected function getSmartesb_Map_ItinerariesService()
{
    $this->services['smartesb.map.itineraries'] = $instance = new \Smartbox\Integration\FrameworkBundle\Configurability\Routing\ItinerariesMap();
        
    $instance->addItinerary('v0-api://execute/skeleton/v0/broadcastping', '_sme_it_v0.v0_broadcast_ping'); // Main Itinerary id

    return $instance;
}
```

Main ```Itinerary``` created for that particular flow
```php
protected function getSmeItV0_V0BroadcastPingService()
{
    $this->services['_sme_it_v0.v0_broadcast_ping'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Itinerary\Itinerary('v0_broadcast_ping'); // Unique flow name

    $instance->id = '_sme_it_v0.v0_broadcast_ping';
    $instance->addProcessorId('v0._sme_pr_broadcast_ping_1'); // Next step to be executed in the flow

    return $instance;
}
```

First step in the ```Itinerary```, defined as next step in the main ```Itinerary```. In this case is to execute the ```Multicast``` processor
```php
protected function getV0_SmePrBroadcastPing1Service()
{
    $this->services['v0._sme_pr_broadcast_ping_1'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Processors\Routing\Multicast();

    $instance->id = 'v0._sme_pr_broadcast_ping_1';
    $instance->setEventDispatcher($this->get('event_dispatcher'));
    $instance->setMessageFactory($this->get('smartesb.message_factory'));
    $instance->setValidator($this->get('smartcore.validation.validator'));
    $instance->setAggregationStrategy('fireAndForget');
    $instance->addItinerary($this->get('_sme_it_v0.v0.010fa5ac574e2312fd000e5994a5e222b0fc23d1')); // New itinerary added for pipeline node

    return $instance;
}
```

Sub ```Itinerary``` defined related to the ```pipeline``` node
```php
protected function getSmeItV0_V0_010fa5ac574e2312fd000e5994a5e222b0fc23d1Service()
{
    $this->services['_sme_it_v0.v0.010fa5ac574e2312fd000e5994a5e222b0fc23d1'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Itinerary\Itinerary('v0.010fa5ac574e2312fd000e5994a5e222b0fc23d1');

    $instance->id = '_sme_it_v0.v0.010fa5ac574e2312fd000e5994a5e222b0fc23d1';
    $instance->addProcessorId('v0._sme_pr_broadcast_ping_2'); // Next step to be executed in the flow

    return $instance;
}
```

Second step in the ```Itinerary```, defined as next step in the sub ```Itinerary``` of the ```pipeline``` node. In this 
case is to execute the ```Pipeline``` processor
```php
protected function getV0_SmePrBroadcastPing2Service()
{
    $this->services['v0._sme_pr_broadcast_ping_2'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Processors\Routing\Pipeline();

    $instance->id = 'v0._sme_pr_broadcast_ping_2';
    $instance->setEventDispatcher($this->get('event_dispatcher'));
    $instance->setMessageFactory($this->get('smartesb.message_factory'));
    $instance->setValidator($this->get('smartcore.validation.validator'));
    $instance->setItinerary($this->get('_sme_it_v0.v0.422cc2456d19812c769153fead550a26b388687a'));

    return $instance;
}
```

Sub ```Itinerary``` defined related to the ```to``` node
```php
protected function getSmeItV0_V0_422cc2456d19812c769153fead550a26b388687aService()
{
    $this->services['_sme_it_v0.v0.422cc2456d19812c769153fead550a26b388687a'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Itinerary\Itinerary('v0.422cc2456d19812c769153fead550a26b388687a');

    $instance->id = '_sme_it_v0.v0.422cc2456d19812c769153fead550a26b388687a';
    $instance->addProcessorId('v0._sme_pr_broadcast_ping_3'); // Next step to be executed in the flow

    return $instance;
}
```

Third, and last step in the ```Itinerary```, defined as next step in the sub ```Itinerary``` of the ```to``` node.
In this case is to execute the ```Endpoint``` processor
```php
protected function getV0_SmePrBroadcastPing3Service()
{
    $this->services['v0._sme_pr_broadcast_ping_3'] = $instance = new \Smartbox\Integration\FrameworkBundle\Core\Processors\EndpointProcessor();

    $instance->id = 'v0._sme_pr_broadcast_ping_3';
    $instance->setEventDispatcher($this->get('event_dispatcher'));
    $instance->setMessageFactory($this->get('smartesb.message_factory'));
    $instance->setValidator($this->get('smartcore.validation.validator'));
    $instance->setEndpointFactory($this->get('smartesb.endpoint_factory'));
    $instance->setURI('rest://remote_system_api/sendPingMessage');
    $instance->setDescription('Sends a ping message to remote_system_api');

    return $instance;
}
```
