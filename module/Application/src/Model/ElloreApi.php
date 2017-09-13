<?php
/**
 * Created by PhpStorm.
 * User: JorgeR
 * Date: 12/09/2017
 * Time: 20:52
 */

namespace Application\Model;


use Zend\View\Model\JsonModel;

class ElloreApi
{
    /**
     * @var array $strings used in the response
     */
    public $strings;

    /**
     * @var Integer $statusCode stores the code of the status
     */
    public $statusCode;

    private $searchKeys;

    public function __construct()
    {
        $this->strings = [
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

        $this->searchKeys = array();

        $this->statusCode = 200;
    }

    /**
     * Create the response
     *
     * @param array $data
     * @return JsonModel
     */
    public function createResponse($data){
        if (is_array($data)) {
            $this->statusCode = 200;
        } else {
            $this->statusCode = 500;
            $errorKey     = $this->strings['errorKey'];
            $defaultError = $this->strings['defaultError'];
            $data[$errorKey] = $defaultError;
        }

        if(count($data) > 0) {
            $sendResponse[$this->strings['resultKey']] = $data;
        }else{
            $this->statusCode = 500;
            $sendResponse[$this->strings['resultKey']] = $this->strings['emptyData'];
        }

        $statusKey = $this->strings['statusKey'];
        if ($this->statusCode == 200) {
            $sendResponse[$statusKey] = $this->strings['statusOk'];
        } else {
            $sendResponse[$statusKey] = $this->strings['statusNok'];
        }


        return new JsonModel($sendResponse);
    }

    /**
     * Returns the element of the array which key id = $id
     * @param array $data
     * @param Integer $id
     * @return array
     */
    public function findById($data, $id){
        return array_values(
            array_filter(
                $data,
                function($arrayValue) use ($id) {
                    return $arrayValue['id'] == $id;
                }
            )
        );
    }

    /**
     * Returns a sub array of $data with the elements which value matches $search
     * @param array $data
     * @param mixed $search
     * @return array
     */
    public function find($data, $search){
        $this->findRecursively($data, $search);

        $r = array();
        foreach($this->searchKeys as $key){
            $r[] = $data[$key];
        }

        return $r;
    }

    /**
     * Search recursively in a array
     * @param $array
     * @param $search
     * @param array $keys
     * @return void
     */
    public function findRecursively($array, $search, $keys = array())
    {

        foreach($array as $key => $value) {
            if (is_array($value)) {
                $sub = $this->findRecursively($value, $search, array_merge($keys, array($key)));
                if (count($sub)) {
                    $this->searchKeys[] = $sub[0];
                }
            } elseif (strpos(strtoupper($value), strtoupper($search)) !== false) {
                return array_merge($keys, array($key));
            }
        }

        return;
    }

    /**
     * Returns an array sorted by keys
     * the first argument must be the array to be sorted
     * the next goes in pairs field, order
     *
     * @return mixed
     */
    public function arraySort(){
        $args = func_get_args();
        $data = array_shift($args);
        foreach ($args as $n => $field) {
            if (is_string($field)) {
                $tmp = array();
                foreach ($data as $key => $row)
                    $tmp[$key] = $row[$field];
                $args[$n] = $tmp;
            }
        }
        $args[] = &$data;

        call_user_func_array('array_multisort', $args);
        return array_pop($args);
    }

    public function selectCars($array){
        $cars = array();
        foreach($array as $user){
            foreach($user['cars'] as $car) {
                $cars[] = $car;
            }
        }

        return $cars;
    }
}