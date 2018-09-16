<?php
/**
 * Created by PhpStorm.
 * User: mel
 * Date: 16/09/18
 * Time: 17:29
 */

namespace SmartboxSkeletonBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use SmartboxSkeletonBundle\Entity\PingMessage;
use JMS\Serializer\SerializerBuilder;
use Smartbox\Integration\FrameworkBundle\Command\ConsumeCommand;
use Smartbox\Integration\FrameworkBundle\Components\Queues\QueueConsumer;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ApiControllerTest extends WebTestCase
{
    const NB_MESSAGES = 1;
    const URI = 'queue://api/normal/skeleton/v0/asyncping';
    protected $mockConsumer;
    protected $container;

    public function testApiAction()
    {
        $client = static::createClient();
        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage("Ping");
        $serializer = SerializerBuilder::create()->build();
        $content = $serializer->serialize($pingMessage, 'json');
        $client->request('POST', '/api/asyncping', [],[],[], $content );

        $this->assertContains('Accepted (Will be performed asynchronously)', $client->getResponse()->getContent());

        //consume
        $kernel = static::createKernel();
        $kernel->boot();
        $this->container = $kernel->getContainer();
        $this->setMockConsumer(self::NB_MESSAGES);
        $application = new Application(static::$kernel);
        $application->add(new ConsumeCommand());
        $command = $application->find('smartesb:consumer:start');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command' => $command->getName(),
            'uri' => self::URI, // argument
            '--killAfter' => self::NB_MESSAGES, // option
        ));

        $output = $commandTester->getDisplay();
        $this->assertContains('limited to', $output);
        $this->assertContains('Consumer was gracefully stopped', $output);

    }

    public function setMockConsumer($expirationCount)
    {

        $this->mockConsumer = $this
            ->getMockBuilder(QueueConsumer::class)
            ->setMethods(['consume', 'setExpirationCount'])
            ->getMock();
        $this->mockConsumer
            ->method('setExpirationCount')
            ->with($expirationCount);
        $this->mockConsumer
            ->method('consume')
            ->willReturn(true);

        $this->container->set('smartesb.consumers.queue', $this->mockConsumer);
        $this->container->set('doctrine', $this->createMock(RegistryInterface::class));
    }

}
