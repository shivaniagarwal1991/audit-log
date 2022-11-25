<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

use App\Validator\ValidateRequestHeader;
use App\Validator\ValidateEventTypeRequest;

use App\Service\Interface\IEventTypeService;
use App\Service\Interface\IEventLogService;
use App\Contract\PaginationRule;

class AuditLogController extends AbstractController
{
    use ValidateRequestHeader;
    use ValidateEventTypeRequest;

    private IEventTypeService $eventTypeService;

    private IEventLogService $eventLogService;

    public function __construct(IEventLogService $eventLogService, IEventTypeService $eventTypeService)
    {
        $this->eventLogService = $eventLogService;
        $this->eventTypeService = $eventTypeService;
    }


    #[Route('/v1/audit-log/search', name: 'v1_audit_log_search', methods: 'GET')]

    public function actionSearchLog(Request $request): JsonResponse
    {
        $this->hasValidRequestHeader($request->headers->get('x-api-key'));
        $requestParam = $request->query->all();
        $type = (!empty($requestParam['type'])) ? $requestParam['type'] : [];
        $pageNo = (!empty($requestParam['page_no'])) ? $requestParam['page_no'] : PaginationRule::PAGE_NO;
        $pageSize = (!empty($requestParam['page_size'])) ? $requestParam['page_size'] : PaginationRule::PAGE_SIZE;

        $data = $this->eventLogService->searchEventLog(type:$type, pageNo:$pageNo, pageSize:$pageSize);
        return new JsonResponse([
            'status_code' => 200,
            'data' => $data]);

    }

    #[Route('/v1/audit-log/event/type', name: 'v1_audit_log_event_type', methods: 'POST')]

    public function actionEventType(Request $request): JsonResponse
    {
        $this->hasValidRequestHeader($request->headers->get('x-api-key'));
        $bodyRequest = json_decode($request->getContent(), true);
        try {
            $this->hasValidEventTypeRequest($bodyRequest);
        }catch(\Exception $e) {
            return new JsonResponse(['status_code' => $e->getCode(), 'message' => $e->getMessage()]);
        }
        $status = (isset($bodyRequest['status'])) ? $bodyRequest['status'] : 1;
        $responseCode = $this->eventTypeService->addEventType(eventName: $bodyRequest['type'], eventStatus: $status );
       
        return new JsonResponse(['status_code' => $responseCode, 'message' => "successfully.type.created"]);
    }

    #[Route('/v1/audit-log/event', name: 'v1_audit_log_event', methods: 'POST')]

    public function actionEventLog(Request $request): JsonResponse
    {
        $this->hasValidRequestHeader($request->headers->get('x-api-key'));
        $bodyRequest = json_decode($request->getContent(), true);
        $this->hasValidEventTypeRequest($bodyRequest);
        $this->hasValidDetailInRequest($bodyRequest);

        $timeStamp = (!empty($bodyRequest['timeStamp'])) ? $bodyRequest['timeStamp'] :'';
        $responseCode = $this->eventLogService->addEventLog(eventType: $bodyRequest['type'], timeStamp: $timeStamp, detail: $bodyRequest['detail'] );

        return new JsonResponse(['status_code' => $responseCode, 'message' => "successfully.log.created"]);
    }

}
