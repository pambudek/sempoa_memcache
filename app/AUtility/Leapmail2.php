<?php
/**
 * Created by PhpStorm.
 * User: elroy
 * Date: 3/22/16
 * Time: 4:00 PM
 */
use \Mailjet\Resources;


class Leapmail2 {

    public $fromEmail = "info@sempoasip.com";
    public $fromName = "Sempoa SIP";

    function sendHTMLEmail($email, $judul, $isi,$isiHTML){

        $mj = new Mailjet();

        $apikey = $mj->apiKey;
        $apisecret = $mj->secretKey;

        $mj = new \Mailjet\Client($apikey, $apisecret);

        $body = [
            'FromEmail' => $this->fromEmail,
            'FromName' => $this->fromName,
            'Subject' => $judul,
            'Text-part' => $isi,
            'Html-part' => $isiHTML,
            'Recipients' => [['Email' => $email]]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        return $response;

//        $body = [
//            'FromEmail' => "pilot@mailjet.com",
//            'FromName' => "Mailjet Pilot",
//            'Subject' => "Your email flight plan!",
//            'Text-part' => "Dear passenger, welcome to Mailjet! May the delivery force be with you!",
//            'Html-part' => "<h3>Dear passenger, welcome to Mailjet!</h3><br />May the delivery force be with you!",
//            'Recipients' => [
//                [
//                    'Email' => "passenger@mailjet.com"
//                ]
//            ]
//        ];
//        $response = $mj->post(Resources::$Email, ['body' => $body]);
//        $response->success() && var_dump($response->getData());
    }
} 