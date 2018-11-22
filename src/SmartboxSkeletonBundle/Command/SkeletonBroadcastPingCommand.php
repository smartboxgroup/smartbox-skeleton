<?php

declare(strict_types=1);

namespace SmartboxSkeletonBundle\Command;

use Smartbox\Integration\FrameworkBundle\Core\Messages\Context;
use SmartboxSkeletonBundle\Entity\PingMessage;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SkeletonBroadcastPingCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('skeleton:send:broadcast-ping')
            ->setDescription('Broadcast Pings.')
        ;
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $requestHandler = $this->getContainer()->get('smartesb_skeleton_request_handler');
        $pingMessage = new PingMessage();
        $now = new \DateTime();
        $pingMessage->setTimestamp($now->getTimestamp());
        $pingMessage->setMessage('BroadcastPing');

        $context = new Context([
            Context::FLOWS_VERSION => '0',
            Context::TRANSACTION_ID => \uniqid('', true),
            Context::ORIGINAL_FROM => 'api',
        ]);

        $responseMessage = $requestHandler->handleCall(
            'skeleton',
            'v0',
            'broadcastping',
            $pingMessage,
            [],
            $context,
            true
        );

        $responseContext = $responseMessage->getContext();
        $transactionId = $responseContext->get('transaction_id');
        $output->writeln('Transaction Id: '.$transactionId);
    }
}
