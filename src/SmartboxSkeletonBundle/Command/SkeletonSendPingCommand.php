<?php

namespace SmartboxSkeletonBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use SmartboxSkeletonBundle\Entity\PingMessage;
use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;

class SkeletonSendPingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('skeleton:send:ping')
            ->setDescription('Send a synchronous Ping.')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $requestHandler = $this->getContainer()->get('smartbox_skeleton_request_handler');
        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage('Ping');

        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => uniqid('', true),
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
