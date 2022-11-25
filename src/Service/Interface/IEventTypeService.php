<?php

namespace App\Service\Interface;

use App\Entity\EventType as EventTypeEntity;

interface IEventTypeService
{
    /**
     * @param string $eventName
     * @param int $eventStatus
     * @return string
     */
    public function addEventType(string $eventName, int $eventStatus): string;

    /**
     * @param string $eventName
     * @return void
     */
    public function isEventTypeExistAlready(string $eventName): void;

    /**
     * @param string $eventName
     * @return EventTypeEntity|null
     */
    public function isActiveAndExistEventType(string $eventName): ?EventTypeEntity;
}