<?php
namespace ApplicationTest\Model;

use Application\Model\ElloreApi;
use PHPUnit\Framework\TestCase;

class ElloreApiTest extends TestCase
{
    public $elloreApi;

    public function setUp()
    {
        $this->elloreApi =  new ElloreApi();

        parent::setUp();
    }

    public function testConstruct()
    {
        $strings =  [
            'statusKey'             => 'status',
            'statusOk'              => 'OK',
            'statusNok'             => 'NOK',
            'resultKey'             => 'result',
            'messageKey'            => 'message',
            'defaultMessage'        => 'No data',
            'emptyData'             => 'No data',
            'errorKey'              => 'error',
            'defaultError'          => 'Unable to load data',
            'pageNotFoundKey'       => 'Request Not Found',
        ];

        $this->assertEquals($strings, $this->elloreApi->strings);
        $this->assertEquals(200, $this->elloreApi->statusCode);
    }

    public function testCreateResponseReturnJsonModel()
    {
        $this->assertEquals('Zend\View\Model\JsonModel', get_class($this->elloreApi->createResponse()));
    }

    public function testEmptyDataReturn()
    {
        $response = $this->elloreApi->createResponse();

        $this->assertEquals(500, $this->elloreApi->statusCode);
        $this->assertEquals('NOK', $response->status);
        $this->assertEquals(array('error' => 'Unable to load data'), $response->result);

    }

    public function testEmptyArrayDataReturn()
    {
        $response = $this->elloreApi->createResponse(array());

        $this->assertEquals(500, $this->elloreApi->statusCode);
        $this->assertEquals('No data', $response->result);

    }

    public function testArrayWithDataReturn()
    {

        $data = $this->loadData();

        $response = $this->elloreApi->createResponse($data);


        $this->assertEquals(200, $this->elloreApi->statusCode);
        $this->assertEquals('OK', $response->status);
        $this->assertEquals(array('id', 'name', 'cars'), array_keys($response->result[0]));
        $this->assertEquals(array('id', 'make', 'model', 'numberplate'), array_keys($response->result[0]['cars'][0]));


    }

    public function testFindByIdReturn()
    {
        $data = $this->loadData();

        $result = $this->elloreApi->findById($data, 2);

        $this->assertEquals($data[1], $result[0]);

    }

    /**
     * @dataProvider wrongId
     */
    public function testFindByIdReturnWithWrongData($value)
    {
        $data = $this->loadData();

        $result = $this->elloreApi->findById($data, $value);

        $this->assertEquals(null, $result[0]);

    }

    /**
     * @dataProvider searches
     */
    public function testFind($search, $number_of_matches)
    {
        $data = $this->loadData();

        $result = $this->elloreApi->find($data, $search);

        $this->assertEquals(count($result), $number_of_matches);
    }

    public function testSortUsersByName()
    {
        $data =  $this->loadData();

        $result = $this->elloreApi->arraySort($data, 'name', SORT_DESC);

        $this->assertEquals(5, $result[0]['id']);
        $this->assertEquals(1, $result[1]['id']);
        $this->assertEquals(2, $result[2]['id']);
        $this->assertEquals(3, $result[3]['id']);
        $this->assertEquals(4, $result[4]['id']);
    }

    public function testSortAscByDefault()
    {
        $data =  $this->loadData();

        $result = $this->elloreApi->arraySort($data, 'name');

        $this->assertEquals(4, $result[0]['id']);
        $this->assertEquals(3, $result[1]['id']);
        $this->assertEquals(2, $result[2]['id']);
        $this->assertEquals(1, $result[3]['id']);
        $this->assertEquals(5, $result[4]['id']);
    }

    public function testSelectCars()
    {
        $data = $this->loadData();

        $cars = $this->elloreApi->selectCars($data);

        $expectedCars = [
            [
                'id'=> 1,
                'make'=> 'Lada',
                'model'=> '2101',
                'numberplate'=> '123ASD'
            ],
            [
                'id'=> 2,
                'make'=> 'Kia',
                'model'=> 'Sorento',
                'numberplate'=> '534TTT'
            ],
            [
                'id'=> 3,
                'make'=> 'Skoda',
                'model'=> 'Octavia',
                'numberplate'=> '999GLF'
            ],
            [
                'id'=> 4,
                'make'=> 'Kia',
                'model'=> 'Sorento',
                'numberplate'=> '555TFF'
            ],
            [
                'id'=> 5,
                'make'=> 'Lada',
                'model'=> '2101',
                'numberplate'=> '445KKK'
            ],
            [
                'id'=> 6,
                'make'=> 'Audi',
                'model'=> 'A6',
                'numberplate'=> '997HHH'
            ],
            [
                'id'=> 7,
                'make'=> 'BMW',
                'model'=> '760',
                'numberplate'=> '444RRR'
            ],
            [
                'id'=> 8,
                'make'=> 'Audi',
                'model'=> 'A6',
                'numberplate'=> '876OUI'
            ],
            [
                'id'=> 9,
                'make'=> 'BMW',
                'model'=> '740',
                'numberplate'=> '112YUI'
            ]
        ];
        $this->assertEquals($expectedCars, $cars);
    }


    private function loadData(){
        return [
            [
                'id'=> 1,
                'name'=> 'Teet Järveküla',
                'cars'=> [
                    [
                    'id'=> 1,
                    'make'=> 'Lada',
                    'model'=> '2101',
                    'numberplate'=> '123ASD'
                    ],
                    [
                    'id'=> 2,
                    'make'=> 'Kia',
                    'model'=> 'Sorento',
                    'numberplate'=> '534TTT'
                    ]
                ]
            ],
            [
                'id'=> 2,
                'name'=> 'Pille Purk',
                'cars'=> [
                    [
                    'id'=> 3,
                    'make'=> 'Skoda',
                    'model'=> 'Octavia',
                    'numberplate'=> '999GLF'
                    ],
                    [
                    'id'=> 4,
                    'make'=> 'Kia',
                    'model'=> 'Sorento',
                    'numberplate'=> '555TFF'
                    ]
                ]
            ],
            [
                'id'=> 3,
                'name'=> 'Mati Kaal',
                'cars'=> [
                    [
                    'id'=> 5,
                    'make'=> 'Lada',
                    'model'=> '2101',
                    'numberplate'=> '445KKK'
                    ],
                    [
                    'id'=> 6,
                    'make'=> 'Audi',
                    'model'=> 'A6',
                    'numberplate'=> '997HHH'
                    ]
                ]
            ],
            [
                'id'=> 4,
                'name'=> 'Külli Kukk',
                'cars'=> [
                    [
                    'id'=> 7,
                    'make'=> 'BMW',
                    'model'=> '760',
                    'numberplate'=> '444RRR'
                    ],
                    [
                    'id'=> 8,
                    'make'=> 'Audi',
                    'model'=> 'A6',
                    'numberplate'=> '876OUI'
                    ]
                ]
            ],
            [
                'id'=> 5,
                'name'=> 'Teet Kruus',
                'cars'=> [
                    [
                    'id'=> 9,
                    'make'=> 'BMW',
                    'model'=> '740',
                    'numberplate'=> '112YUI'
                    ]
                ]
            ]
        ];
    }


    /**
     * DATA PROVIDERS
     */


    public function wrongId(){
        return array(
            array('a'),
            array(105),
            array(null),
            array(false)
        );
    }

    /**
     * Returns an array with the search string and the number of matches
     * @return array
     */
    public function searches(){
        return array(
            array('Teet', 2),
            array('Teet Järveküla', 1),
            array('teet', 2),
            array('Lada', 2),
            array('lada', 2),
            array('9', 3),
            array('KK', 2),
            array('nonsense', 0),
            array('', 0),
            array(null, 0),
            array(false, 0),
        );
    }
}