<?php

namespace App\Service\Kafka;

use RdKafka\Producer;
use RdKafka\Conf;

class KafkaProducerService
{
    private Producer $producer;

    public function __construct(string $brokerList = 'localhost:9092')
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $brokerList);
        $conf->set('compression.codec', 'gzip');
        $conf->set('batch.num.messages', '1000');

        $this->producer = new Producer($conf);
    }

    public function sendMessage(
        string $topicName,
        string $message,
        ?string $key = null,
        ?int $partition = null,
        ?string $msgOpaque = null
    ): void {
        $topic = $this->producer->newTopic($topicName);

        // Если партиция не указана, используется балансировка по ключу
        if ($partition === null) {
            $partition = RD_KAFKA_PARTITION_UA; // Автоматический выбор
        }

        $topic->produce(
            $partition,
            0,
            $message,
            $key,
            $msgOpaque
        );

        $this->producer->poll(0);
        $this->producer->flush(10000); // timeout 10s
    }

    public function sendToSpecificPartition(
        string $topicName,
        string $message,
        int $partition,
        ?string $key = null
    ): void {
        $topic = $this->producer->newTopic($topicName);

        $topic->produce(
            $partition,
            0,
            $message,
            $key
        );

        $this->producer->poll(0);
        $this->producer->flush(10000);
    }

    public function sendBatch(string $topicName, array $messages): void
    {
        $topic = $this->producer->newTopic($topicName);

        foreach ($messages as $messageData) {
            $topic->produce(
                $messageData['partition'] ?? RD_KAFKA_PARTITION_UA,
                0,
                $messageData['message'],
                $messageData['key'] ?? null,
                $messageData['headers'] ?? []
            );
        }

        $this->producer->poll(0);
        $this->producer->flush(10000);
    }

    public function __destruct()
    {
        $this->producer->flush(10000);
    }
}
