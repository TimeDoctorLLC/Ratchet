<?php
namespace Ratchet\Wamp;
use Ratchet\ConnectionInterface;

/**
 * A topic/channel containing connections that have subscribed to it
 */
class Topic implements \IteratorAggregate, \Countable {
    private $id;

    private $subscribers;
    /**
     * @var array
     */
    private $options;

    /**
     * @param string $topicId Unique ID for this object
     */
    public function __construct($topicId) {
        $this->id = $topicId;
        $this->subscribers = new \SplObjectStorage;
    }

    /**
     * @return string
     */
    public function getId() {
        return $this->id;
    }

    public function __toString() {
        return $this->getId();
    }

    /**
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * @param array $options
     */
    public function setOptions($options)
    {
        $this->options = $options;
    }

    /**
     * Send a message to all the connections in this topic
     * @param string|array $msg Payload to publish
     * @param array $exclude A list of session IDs the message should be excluded from (blacklist)
     * @param array $eligible A list of session Ids the message should be send to (whitelist)
     * @return Topic The same Topic object to chain
     */
    public function broadcast($msg, $kwargs, array $exclude = array(), array $eligible = array())
    {
        $useEligible = (bool)count($eligible);
        foreach ($this->subscribers as $client) {
            if (in_array($client->WAMP->sessionId, $exclude)) {
                continue;
            }

            if ($useEligible && !in_array($client->WAMP->sessionId, $eligible)) {
                continue;
            }

            $client->event($this->id, $msg, $kwargs);
        }

        return $this;
    }

    /**
     * @param  WampConnection $conn
     * @return boolean
     */
    public function has(ConnectionInterface $conn) {
        return $this->subscribers->contains($conn);
    }

    /**
     * @param WampConnection $conn
     * @return Topic
     */
    public function add(ConnectionInterface $conn) {
        $this->subscribers->attach($conn);

        return $this;
    }

    /**
     * @param WampConnection $conn
     * @return Topic
     */
    public function remove(ConnectionInterface $conn) {
        if ($this->subscribers->contains($conn)) {
            $this->subscribers->detach($conn);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator() {
        return $this->subscribers;
    }

    /**
     * {@inheritdoc}
     */
    public function count() {
        return $this->subscribers->count();
    }
}
