<?php

namespace ApplicationTest\Controller;

use Application\Controller\IndexController;
use Zend\Stdlib\ArrayUtils;
use Zend\Test\PHPUnit\Controller\AbstractHttpControllerTestCase;
use Helmich\JsonAssert\JsonAssertions;

class IndexControllerTest extends AbstractHttpControllerTestCase
{

    use JsonAssertions;

    public function setUp()
    {
        // The module configuration should still be applicable for tests.
        // You can override configuration here with test case specific values,
        // such as sample view templates, path stacks, module_listener_options,
        // etc.
        $configOverrides = [];

        $this->setApplicationConfig(ArrayUtils::merge(
            include __DIR__ . '/../../../../config/application.config.php',
            $configOverrides
        ));

        parent::setUp();
    }

    public function testIndexActionCanBeAccessed()
    {
        $this->dispatch('/', 'GET');
        $this->assertResponseStatusCode(200);
        $this->assertModuleName('application');
        $this->assertControllerName(IndexController::class); // as specified in router's controller name alias
        $this->assertControllerClass('IndexController');
        $this->assertMatchedRouteName('application');
    }

    public function testInvalidElementDoesNotCrash()
    {
        $this->dispatch('/invalid', 'GET');
        $this->assertResponseStatusCode(200);
    }

    public function testUsersElementGetJSON()
    {
        $this->dispatch('/users', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatches($json, [
            '$.result[:1].id'                   => 1,
            '$.result[:1].name'                 => 'Teet Järveküla',
            '$.result[:1].cars[:1].id'          => 1,
            '$.result[:1].cars[:1].make'        => 'Lada',
            '$.result[:1].cars[:1].model'       => '2101',
            '$.result[:1].cars[:1].numberplate' => '123ASD',
            '$.result[:1].cars[:2].id'          => 2,
            '$.result[:1].cars[:2].make'        => 'Kia',
            '$.result[:1].cars[:2].model'       => 'Sorento',
            '$.result[:1].cars[:2].numberplate' => '534TTT',
            ]
        );
    }

    public function testUsersElementGetByIdJSON()
    {
        $this->dispatch('/users/2', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatches($json, [
            '$.result[:1].id'                   => 2,
            '$.result[:1].name'                 => 'Pille Purk',
            '$.result[:1].cars[:1].id'          => 3,
            '$.result[:1].cars[:1].make'        => 'Skoda',
            '$.result[:1].cars[:1].model'       => 'Octavia',
            '$.result[:1].cars[:1].numberplate' => '999GLF',
            '$.result[:1].cars[:2].id'          => 4,
            '$.result[:1].cars[:2].make'        => 'Kia',
            '$.result[:1].cars[:2].model'       => 'Sorento',
            '$.result[:1].cars[:2].numberplate' => '555TFF',
            ]
        );
    }

    public function testUsersGetCorrectSchema()
    {
        $this->dispatch('/users', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatchesSchema($json, [
            'type'       => 'object',
            'required'   => ['status', 'result'],
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'minLength' => 2
                ],
                'result' => [
                    'type'        => 'array',
                    'minItems'    => 1,
                    'uniqueItems' => true,
                    'items'       => [
                        'type'       => 'object',
                        'required'   => ['id', 'name', 'cars'],
                        'properties' => [
                            'id'   => ['type' => 'number'],
                            'name' => ['type' => 'string'],
                            'cars' => [
                                'type'        => 'array',
                                'minItems'    => 1,
                                'uniqueItems' => true,
                                'items'       => [
                                    'type'       => 'object',
                                    'required'   => ['id', 'make', 'model', 'numberplate'],
                                    'properties' => [
                                        'id'          => ['type' => 'number'],
                                        'make'        => ['type' => 'string'],
                                        'model'       => ['type' => 'string'],
                                        'numberplate' => ['type' => 'string'],
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ]);

    }

    public function testCarsElemetGetJSON()
    {
        $this->dispatch('/cars', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatches($json, [
                '$.result[:1].id'                   => 1,
                '$.result[:1].make'        => 'Lada',
                '$.result[:1].model'       => '2101',
                '$.result[:1].numberplate' => '123ASD'
            ]
        );
    }

    public function testCarsGetCorrectSchema()
    {
        $this->dispatch('/cars', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatchesSchema($json, [
            'type' => 'object',
            'required' => ['status', 'result'],
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'minLength' => 2
                ],
                'result' => [
                    'type' => 'array',
                    'minItems' => 1,
                    'uniqueItems' => true,
                    'items' => [
                        'type'       => 'object',
                        'required'   => ['id', 'make', 'model', 'numberplate'],
                        'properties' => [
                            'id' => ['type' => 'number'],
                            'make' => ['type' => 'string'],
                            'model' => ['type' => 'string'],
                            'numberplate' => ['type' => 'string'],
                        ]
                    ]
                ]
            ]
        ]);
    }

    public function testInvalidElementGetCorrectSchema()
    {
        $this->dispatch('/invalid', 'GET');
        $this->assertResponseStatusCode(200);

        $json = $this->getResponse()->getContent();

        $this->assertJsonDocumentMatchesSchema($json, [
            'type'       => 'object',
            'required'   => ['status', 'result'],
            'properties' => [
                'status' => [
                    'type' => 'string',
                    'minLength' => 2
                ],
                'result' => [
                    'type'       => 'object',
                    'required'   => ['error'],
                    'properties' => [
                        'error' => [
                            'type' => 'string'
                        ]
                    ]
                ]
            ]
        ]);
    }
}
