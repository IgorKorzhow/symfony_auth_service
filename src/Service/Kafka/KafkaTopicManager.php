<?php

namespace App\Service\Kafka;

use RdKafka\Conf;
use RdKafka\Exception;
use RdKafka\Producer;

class KafkaTopicManager
{
    private Producer $producer;

    public function __construct(string $brokerList = 'localhost:9092')
    {
        $conf = new Conf();
        $conf->set('metadata.broker.list', $brokerList);

        $this->producer = new Producer($conf);
    }

    /**
     * @throws Exception
     */
    public function createTopic(
        string $topicName,
        int $partitions = 1,
        int $replicationFactor = 1,
        array $config = []
    ): void {
        // Для создания топиков нужно использовать AdminClient
        // Или создавать их через команды Kafka

        // Проверяем существование топика
        $metadata = $this->producer->getMetadata(true, null, 5000);

        foreach ($metadata->getTopics() as $topic) {
            if ($topic->getTopic() === $topicName) {
                throw new \Exception("Topic {$topicName} already exists");
            }
        }

        // В production лучше создавать топики заранее через kafka-topics.sh
        // или использовать Kafka AdminClient
    }

    /**
     * @throws Exception
     */
    public function getTopicInfo(string $topicName): array
    {
        $metadata = $this->producer->getMetadata(true, null, 5000);

        foreach ($metadata->getTopics() as $topic) {
            if ($topic->getTopic() === $topicName) {
                $partitions = [];
                foreach ($topic->getPartitions() as $partition) {
                    $partitions[] = [
                        'id' => $partition->getId(),
                        'leader' => $partition->getLeader(),
                        'replicas' => $partition->getReplicas(),
                        'isrs' => $partition->getIsrs()
                    ];
                }

                return [
                    'name' => $topicName,
                    'partitions' => $partitions,
                    'partition_count' => count($partitions)
                ];
            }
        }

        throw new \Exception("Topic {$topicName} not found");
    }

    /**
     * @throws Exception
     */
    public function getPartitionForKey(string $topicName, string $key): int
    {
        $topicInfo = $this->getTopicInfo($topicName);
        $partitionCount = $topicInfo['partition_count'];

        // Простой алгоритм хеширования (как в Kafka)
        return abs(crc32($key)) % $partitionCount;
    }

    /**
     * @throws Exception
     */
    public function listTopics(): array
    {
        $metadata = $this->producer->getMetadata(true, null, 5000);
        $topics = [];

        foreach ($metadata->getTopics() as $topic) {
            $topics[] = [
                'name' => $topic->getTopic(),
                'partition_count' => count($topic->getPartitions())
            ];
        }

        return $topics;
    }
}
