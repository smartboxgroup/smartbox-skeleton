<?php

declare(strict_types=1);

namespace SmartboxSkeletonBundle\Command;

use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
use SmartboxSkeletonBundle\Entity\PingMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SkeletonSendPingCallbackCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('skeleton:send:pingCallback')
            ->setDescription('Send a synchronous Ping Callback.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $requestHandler = $this->getContainer()->get('smartesb_skeleton_request_handler');
        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage('Ping callback');

        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => \uniqid('', true),
            Context::ORIGINAL_FROM => 'api',
        ]);

        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            'ping',
            $pingMessage,
            [],
            $context,
            false
        );
        $response = $responseMessage->getBody();
        $responseContext = $responseMessage->getContext();
        $transactionId = $responseContext->get('transaction_id');
        $output->writeln('Transaction Id: '.$transactionId);
        $serializer = $this->getContainer()->get('jms_serializer');
        $json = $serializer->serialize($response, 'json');
        $output->writeln('Command result.'.$json);
    }
}
