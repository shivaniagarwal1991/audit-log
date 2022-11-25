<?php

namespace App\Service;

use App\Contract\PaginationRule;
use Symfony\Component\HttpFoundation\Response;
use App\Service\Interface\IEventLogService;
use App\Entity\EventLog as EventLogEntity;
use App\Repository\EventLogRepository;
use App\Service\Interface\IEventTypeService;
use Symfony\Component\HttpKernel\Exception\HttpException;

class EventLogService implements IEventLogService
{
    private EventLogRepository $eventLogRepository;

    private IEventTypeService $eventTypeService;

    public function __construct(EventLogRepository $eventLogRepository, IEventTypeService $eventTypeService)
    {
        $this->eventLogRepository = $eventLogRepository;
        $this->eventTypeService = $eventTypeService;
    }

    /**
     * @param array $type
     * @param int $pageNo
     * @param int $pageSize
     * @return array
     */
    public function searchEventLog(array $type, int $pageNo, int $pageSize): array
    {
        $eventTypes = $this->eventTypeService->filterAllActiveEventType($type);
        $eventTypeIds = array_map(fn($eventObject) => $eventObject->getId(), $eventTypes);
        $offset = ($pageNo == PaginationRule::PAGE_NO) ? $pageNo-1 : ($pageNo-1) * $pageSize + 1;
        $eventLogData = $this->eventLogRepository->getEventLogs($eventTypeIds, $offset, $pageSize);
        if(count($eventLogData) > 0)
        {
            $formatData = $this->formatData($eventLogData, $eventTypes);
            return [
                'page_no' => $pageNo,
                'count' => count($formatData),
                'logs' => $formatData
            ];
        }
        throw new HttpException(404, 'no.record.type');
    }

    /**
     * @param string $eventType
     * @param string $timeStamp
     * @param string $detail
     * @return string
     */
    public function addEventLog(string $eventType, string $timeStamp, string $detail): string
    {
        $timestamp = ($timeStamp != '') ? strtotime(date('d-m-Y h:i:s'), $timeStamp) : strtotime(date('d-m-Y h:i:s'));
        $eventDetail = $this->eventTypeService->isActiveAndExistEventType($eventType);

        $entityLog = new EventLogEntity();
        $entityLog->setEventTypeId($eventDetail->getId());
        $entityLog->setDetail($detail);
        $entityLog->setTimestamp($timestamp);
        $this->eventLogRepository->save($entityLog, true);
        return Response::HTTP_CREATED;
    }

    /**
     * @param array $eventLogData
     * @param array $eventTypes
     * @return array
     */
    private function formatData(array $eventLogData, array $eventTypes): array
    {
        $records = [];
        $eventTypes = array_reduce($eventTypes, function($result, $eventType) {  $result[$eventType->getId()] = $eventType->getName(); return $result;});

        foreach($eventLogData as $logKey => $logValue) {
            $data = [];
            $data['event_name'] = $eventTypes[$logValue->getEventTypeId()];
            $data['timestamp'] =  date('d/M/Y h:i:s', $logValue->getTimestamp());
            $data['detail'] = $logValue->getDetail();
            array_push($records, $data);
        }
        return $records;
    }

}