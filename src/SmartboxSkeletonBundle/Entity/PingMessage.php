<?php

declare(strict_types=1);

namespace SmartboxSkeletonBundle\Entity;

use JMS\Serializer\Annotation as JMS;
use Smartbox\CoreBundle\Type\Entity;
use Smartbox\CoreBundle\Type\SerializableInterface;

/**
 * Class PingMessage.
 */
class PingMessage extends Entity implements SerializableInterface
{
    /**
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     *
     * @var string
     */
    private $message;

    /**
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\Groups({"logs"})
     *
     * @var string
     */
    private $timestamp;

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param string $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;
    }
}
