<?php

namespace Application\Controller;

use Application\Model\ElloreApi;
use Zend\Mvc\Controller\AbstractRestfulController;

class IndexController extends AbstractRestfulController
{

    /**
     * @var array $response response body
     */
    public $myResponse;

    /**
     * @var ElloreApi $elloreApi
     */
    public $elloreApi;

    public function __construct()
    {
        $this->elloreApi =  new ElloreApi();
    }

    public function indexAction()
    {
        $id      = (int) $this->params()->fromRoute('id', 0);
        $element = $this->params()->fromRoute('element', '');
        $r       = null;

        $posibleElements = array(
            'users',
            'cars'
        );

        if(!in_array($element, $posibleElements)){
            return $this->elloreApi->createResponse($r);
        }

        $data = $this->getData();

        $dataArray = json_decode($data, true);

        if($element == 'cars'){
            $dataArray = $this->elloreApi->selectCars($dataArray);
        }

        if(0 !== $id) {
            $r = $this->elloreApi->findById($dataArray, $id);
        } else {
            if(isset($_GET['find']) && $_GET['find'] != '') {
                $r = $this->elloreApi->find($dataArray, $this->cleanInput($_GET['find']));
            } else {
                $r = $dataArray;
            }
        }

        if(isset($_GET['sort']) && $_GET['sort'] != ''){
            $sort       = $this->cleanInput($_GET['sort']);
            $components = explode(':', $sort);

            if(count($components) == 2){
                $key = $components[0];

                $order = SORT_ASC;
                if($components[1] == 'desc'){
                    $order = SORT_DESC;
                }

                $r = $this->elloreApi->arraySort($r, $key, $order);
            }
        }

        return $this->elloreApi->createResponse($r);

    }

    /**
     * gets the data from source
     * @return string
     */
    public function getData(){
        $file = __DIR__ . '/../../../../public/data.json';
        if(is_file($file)) {
            return file_get_contents($file);
        }

        return '';
    }


    /**
     * cleans the user input for safety reasons
     * @param $input
     * @return string
     */
    public function cleanInput($input){
        return strip_tags(stripslashes($input));
    }

}
