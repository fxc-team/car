<?php
namespace Masonx\Car;

use GuzzleHttp\Client;
use Masonx\Car\Exceptions\CarException;

class Car
{
    public $accessKey = 'ZYu94k_NwToSS-sTnnb5_FL8W15FLrSh';
    public $secretKey = '1nqI1FlMGJ-ENHWc5Ay2iTDrOlZM4aOd';

    /**
     * 渠道
     *
     * @var string
     */
    protected $channel = '';

    /**
     * 接口地址
     *
     * @var string
     */
    private $apiUrl = 'https://cloud-api.che300.com/open/v1';

    /**
     * 接口名称
     *
     * @var string
     */
    protected $method = '';

    /**
     * 接口请求数据
     *
     * @var array
     */
    private $parameter = [];

    public function __construct()
    {
        header('Content-Type:application/x-www-form-urlencoded;charset=utf-8');
    }

    /**
     * 魔术方法
     *
     * @param $name
     * @param $arguments
     * @throws \Exception
     */
    public function __call($name, $arguments)
    {
        throw new CarException('方法不存在');
    }

    /**
     * 所有品牌列表
     *
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCarBrandList():array {

        $this->method = '/get-car-brand-list';
        return $this->request();
    }

    /**
     * 指定品牌车系列表
     *
     * @param $brandId 品牌ID
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCarSeriesList(string $brandId):array {

        $this->parameter = [
            'brand_id'=>$brandId
        ];
        $this->method = '/get-car-series-list';
        return $this->request();
    }

    /**
     * 指定车系车型列表
     *
     * @param $seriesId 车型ID
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCarModelList(string $seriesId):array {

        $this->parameter = [
            'series_id'=>$seriesId
        ];
        $this->method = '/get-car-model-list';
        return $this->request();
    }

    /**
     * 车系信息
     *
     * @param $seriesId 车系ID
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCarSeriesInfo(string $seriesId):array {

        $this->parameter = [
            'series_id'=>$seriesId
        ];
        $this->method = '/get-car-series-info';
        return $this->request();
    }

    /**
     * 车型详细信息
     *
     * @param $modelId 车型ID
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getCarModelInfo(string $modelId):array {

        $this->parameter = [
            'model_id'=>$modelId
        ];
        $this->method = '/get-car-model-info';
        return $this->request();
    }

    /**
     * 模糊搜索车型
     *
     * @param $keyword 关键词
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function searchModel(string $keyword):array {

        $this->parameter = [
            'keyword'=>$keyword
        ];
        $this->method = '/search-model';
        return $this->request();
    }

    /**
     * 根据名称识别车型
     *
     * @param string $brandName 品牌名称
     * @param string $seriesName 车系名称
     * @param string $modelName 车型名称
     * @param string $modelYear 车型年款，如2013
     * @param string $gearType 变速箱类型。可选值：手动 自动
     * @param string $makerType 厂商类型。可选值：国产 合资 进口
     * @param string $liter 排量，如1.5
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function identifyModelByName(string $brandName
        ,string $seriesName
        ,string $modelName
        ,string $modelYear
        ,string $gearType = ''
        ,string $makerType = ''
        ,string $liter = ''):array {

        //必须参数
        $this->parameter = [
            'brand_name'=>$brandName,
            'series_name'=>$seriesName,
            'model_name'=>$modelName,
            'model_year'=>$modelYear
        ];

        //不必须参数
        if(!empty($gearType)){
            $this->parameter['gear_type'] = $gearType;
        }
        if(!empty($makerType)){
            $this->parameter['maker_type'] = $makerType;
        }
        if(!empty($liter)){
            $this->parameter['liter'] = $liter;
        }

        $this->method = '/identify-model-by-name';
        return $this->request();
    }

    /**
     * 车型配置参数
     *
     * @param string $modelId 车型ID
     * @param string $keyType 返回的键值类型。可选值：en zh。默认：zh
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getModelParameters(string $modelId,string $keyType = ''):array {

        //必须参数
        $this->parameter = [
            'model_id'=>$modelId
        ];

        //不必须参数
        if(!empty($keyType)){
            $this->parameter['key_type'] = $keyType;
        }

        $this->method = '/get-model-parameters';
        return $this->request();
    }

    /**
     * 车型详细配置参数的最后更新时间
     *
     * @param string $modelId 车型ID
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getModelParamsUpdateTime(string $modelId):array {

        //必须参数
        $this->parameter = [
            'model_id'=>$modelId
        ];

        $this->method = '/get-model-params-update-time';
        return $this->request();
    }


    /**
     * 方法请求器
     *
     * @return array
     * @throws CarException
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function request(): array
    {
        try {
            date_default_timezone_set("PRC");

            $this->parameter['access_key'] = $this->accessKey;
            $this->parameter['timestamp'] = $this->getMillisecond();
            $digest      = $this->getSign($this->parameter);

            $client = new Client(['verify' => false]);

            $this->parameter['sn'] = $digest;
            $postData = [
                'form_params'=>$this->parameter
            ];

            $response = $client->request('post', trim($this->apiUrl . $this->method), $postData); //使用json请求
            $response = json_decode($response->getBody()->getContents(), true);
            $message = '操作成功';
            return $this->apiResponse($response['code'], $message, $response);
        } catch (\Exception $e) {
            throw new CarException($e->getMessage());
        }
    }

    /**
     * 获取签名
     *
     * @param string $data
     * @param string $timestamp
     * @return string
     */
    public function getSign($data){
        ksort($data);
        $query = $data;
        unset($query['timestamp']);
        $str = $data['timestamp'] . http_build_query($query) . $this->secretKey;
        return md5($str);
    }

    /**
     * 获取时间戳，毫秒级
     *
     * @return int
     */
    public function getMillisecond() {
        list($msec, $sec) = explode(' ', microtime());
        return intval(((float)$msec + (float)$sec) * 1000);
    }

    /**
     * 数据返回
     *
     * @param $code
     * @param $message
     * @param array $data
     * @return array
     */
    private function apiResponse($code, $message, $data = [])
    {
        $result = [
            'responseCode' => 20000,
            'responseMessage' => 'success',
            'responseData' => [
                'code' => $code,
                'message' => $message,
                'data' => $data,
                'channel' => $this->channel,
            ]
        ];
        return $result;
    }

}
