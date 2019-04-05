<?php
namespace AppBundle\ThirdPartyService\Alibaba;

use AlibabaCloud\Client\AlibabaCloud;
use AlibabaCloud\Client\Exception\ClientException;
use AlibabaCloud\Client\Exception\ServerException;

//https://api.aliyun.com/?spm=a2c4g.11186623.2.14.1ad54e6al172Ob#/?product=Dysmsapi&lang=PHP
class AlibabaSms {
    private $accessKeyId;
    private $accessSecret;

    private $product = 'Dysmsapi';
    private $version = '2017-05-25';
    private $action = 'SendSms';
    private $method = 'POST';
    private $regionId = "cn-hangzhou";

    private $phoneNumbers;
    private $signName;
    private $templateCode;

    private $templateParam = "";
    private $smsUpExtendCode = "";
    private $outId = "";

    /**
     * AlibabaSms constructor.
     * @param $accessKeyId
     */
    public function __construct(array $options)
    {
        $this->accessKeyId  = $options['accesskey'];
        $this->accessSecret = $options['accessSecret'];

        $this->phoneNumbers = $options['accessSecret'];
        $this->signName     = $options['accessSecret'];
        $this->templateCode = $options['accessSecret'];
    }


    public function send(){
        AlibabaCloud::accessKeyClient($this->accessKeyId, $this->accessSecret)
            ->regionId($this->regionId)
            ->asGlobalClient();
        try {
            $result = AlibabaCloud::rpcRequest()
                ->product($this->product)
                // ->scheme('https') // https | http
                ->version($this->version)
                ->action($this->action)
                ->method($this->method)
                ->options(array(
                    'query' => array(
                        'RegionId'          => $this->regionId,
                        'PhoneNumbers'      => $this->phoneNumbers,
                        'SignName'          => $this->signName,
                        'TemplateCode'      => $this->templateCode,
                        'TemplateParam'     => $this->templateParam,
                        'SmsUpExtendCode'   => $this->smsUpExtendCode,
                        'OutId'             => $this->outId,
                    ),
                ))
                ->request();
            $resultData = $result->toArray();
            if($resultData['Code'] === "OK"){

            }
        } catch (ClientException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        } catch (ServerException $e) {
            echo $e->getErrorMessage() . PHP_EOL;
        }
    }
}
