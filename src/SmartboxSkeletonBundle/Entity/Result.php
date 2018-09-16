<?php

namespace SmartboxSkeletonBundle\Entity;

use Smartbox\CoreBundle\Type\SerializableInterface;
use Smartbox\CoreBundle\Type\Entity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * Class Result
 * @package SmartboxSkeletonBundle\Entity
 */
class Result extends Entity implements SerializableInterface
{
    /**
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(min="1")
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\Groups({"list", "public", "logs"})
     * @JMS\SerializedName("transactionId")
     *
     * @var string
     */
    protected $transactionId;

    /**
     * @Assert\Type(type="\DateTime")
     * @Assert\NotBlank
     * @JMS\Type("DateTime")
     * @JMS\Expose
     * @JMS\Groups({"list", "public", "logs"})
     * @JMS\SerializedName("timestamp")
     *
     * @var \DateTime
     */
    protected $timestamp;

    /**
     * @Assert\Type(type="integer")
     * @JMS\Type("integer")
     * @JMS\Expose
     * @JMS\Groups({"list", "public", "logs"})
     * @JMS\SerializedName("code")
     *
     * @var int
     */
    protected $code;

    /**
     * @Assert\Type(type="string")
     * @JMS\Type("string")
     * @JMS\Expose
     * @JMS\Groups({"list", "public", "logs"})
     * @JMS\SerializedName("message")
     *
     * @var string
     */
    protected $message;

    /**
     * @return mixed
     */
    public function getTransactionId()
    {
        return $this->transactionId;
    }

    /**
     * @param mixed $transactionId
     *
     * @return $this
     */
    public function setTransactionId($transactionId)
    {
        $this->transactionId = $transactionId;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTimestamp()
    {
        return $this->timestamp;
    }

    /**
     * @param mixed $timestamp
     *
     * @return $this
     */
    public function setTimestamp($timestamp)
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param int $code
     *
     * @return $this
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param mixed $message
     *
     * @return $this
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }
}
