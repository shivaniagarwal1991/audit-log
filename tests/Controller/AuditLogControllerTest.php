<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AuditLogControllerTest extends WebTestCase
{
    private $client;
    private $url;
    public function setUp(): void
    {
        // This calls KernelTestCase::bootKernel(), and creates a
        // "client" that is acting as the browser
        $this->client = static::createClient([], [
            'HTTP_X_API_KEY'  => 'c0a062b7-b225-c294-b8a0-06b98931a45b1123'
        ]);

        $this->url = 'http://localhost:8001/v1/audit-log/';
        parent::setUp();
    }

    /**
     * @dataProvider provideAddEventTypeData
     */
    public function testAddEventType(string $expectedResult, array $requestParams): void
    {
        $this->client->request('POST', $this->url . 'event/type', [ 'body' => $requestParams]);
        $response = $this->client->getResponse();
        $responseData = json_decode($response->getContent(), true);

        $this->assertEquals($expectedResult, $responseData['message']);
    }


    public function provideAddEventTypeData()
    {
        yield 'add event type without name' => [
            'expecting_type_as_body_parameter',
            [],
        ];

        yield 'add event type without wrong key' => [
            'expecting_type_as_body_parameter',
            ["typess" => "test"]
        ];

    }
}