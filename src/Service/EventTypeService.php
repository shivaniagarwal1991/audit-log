<?php

namespace App\Service;

use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Interface\IEventTypeService;
use App\Entity\EventType as EventTypeEntity;
use App\Repository\EventTypeRepository;

class EventTypeService implements IEventTypeService
{
    private EventTypeRepository $eventTypeRepository;

    public function __construct(EventTypeRepository $eventTypeRepository)
    {
        $this->eventTypeRepository = $eventTypeRepository;
    }

    /**
     * @param string $eventName
     * @param int $eventStatus
     * @return string
     */
    public function addEventType(string $eventName, int $eventStatus): string
    {
        $eventStatus = ($eventStatus == 0) ? EventTypeEntity::STATUS_INACTIVE: EventTypeEntity::STATUS_ACTIVE;
        $this->isEventTypeExistAlready($eventName);

        $entityType = new EventTypeEntity();
        $entityType->setName(strtolower($eventName));
        $entityType->setStatus($eventStatus);
        $this->eventTypeRepository->save($entityType, true);
        return Response::HTTP_CREATED;
    }

    /**
     * @param string $eventName
     * @return void
     */
    public function isEventTypeExistAlready(string $eventName): void
    {
        $isEventNameAvailable = $this->eventTypeRepository->findByFields(['name' => strtolower($eventName)]);
        if(count($isEventNameAvailable) > 0) {
            throw new HttpException(Response::HTTP_CONFLICT, 'type.already.exist');
        }
    }

    /**
     * @param string $eventName
     * @return EventTypeEntity|null
     */
    public function isActiveAndExistEventType(string $eventName): ?EventTypeEntity
    {
        $isEventNameAvailable = $this->eventTypeRepository->findByFields([
                                'name' => strtolower($eventName),
                                'status' => EventTypeEntity::STATUS_ACTIVE]);

        if(count($isEventNameAvailable) == 0) {
            throw new HttpException(Response::HTTP_CONFLICT, 'type.not.exist');
        }
        return $isEventNameAvailable[0];
    }

    /**
     * @param array $eventName
     * @return array
     */
    public function filterAllActiveEventType(array $eventName=[]): array
    {
        $isEventNameAvailable = $this->eventTypeRepository->getEventTypeWithStatus($eventName, EventTypeEntity::STATUS_ACTIVE);
        if(count($isEventNameAvailable) == 0) {
            throw new HttpException(Response::HTTP_CONFLICT, 'no.active.type');
        }
        return $isEventNameAvailable;
    }
}