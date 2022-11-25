<?php

namespace App\Service\Interface;

interface IEventLogService
{
    /**
     * @param string $eventType
     * @param string $timeStamp
     * @param string $detail
     * @return string
     */
    public function addEventLog(string $eventType, string $timeStamp, string $detail): string;

    /**
     * @param array $type
     * @param int $pageNo
     * @param int $pageSize
     * @return array
     */
    public function searchEventLog(array $type, int $pageNo, int $pageSize): array;
}