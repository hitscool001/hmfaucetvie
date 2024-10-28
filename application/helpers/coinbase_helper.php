<?php
defined('BASEPATH') or exit('No direct script access allowed');

function coinBaseGetTime()
{
    return json_decode(file_get_contents("https://api.coinbase.com/v2/time"), true)["data"]["epoch"];
}

function coinBaseSendPayment($to, $currency, $amount, $accountId, $api, $secret)
{
    $timestamp = coinBaseGetTime();
    $curl = curl_init();
    $path = "/v2/accounts/" . $accountId . "/transactions";
    $url = "https://api.coinbase.com" . $path;
    $params = ['type' => 'send', 'to' => $to, 'amount' => $amount, 'currency' => $currency];
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_USERAGENT, 'local server',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                "CB-VERSION: 2022-01-10",
                "CB-ACCESS-SIGN:" . hash_hmac('sha256', $timestamp . "POST" . $path . json_encode($params), $secret),
                "CB-ACCESS-KEY:" . $api, "CB-ACCESS-TIMESTAMP:" . $timestamp, 'Content-Type: application/json'
            ),
            CURLOPT_SSL_VERIFYPEER => false
        )
    );
    $res = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($res);
    if (isset($result->errors)) {
        return [
            'success' => FALSE,
            'message' => $result->errors[0]->message
        ];
    }
    return [
        'success' => TRUE,
    ];
}

function coinBaseCheckBalance($accountId, $api, $secret)
{
    $timestamp = coinBaseGetTime();
    $curl = curl_init();
    $path = "/v2/accounts/" . $accountId;
    $url = "https://api.coinbase.com" . $path;
    curl_setopt_array(
        $curl,
        array(
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_URL => $url,
            CURLOPT_USERAGENT, 'local server',
            CURLOPT_HTTPHEADER => array(
                "CB-VERSION: 2022-01-10",
                "CB-ACCESS-SIGN:" . hash_hmac('sha256', $timestamp . "GET" . $path, $secret),
                "CB-ACCESS-KEY:" . $api, "CB-ACCESS-TIMESTAMP:" . $timestamp, 'Content-Type: application/json'
            ),
            CURLOPT_SSL_VERIFYPEER => false
        )
    );
    $res = curl_exec($curl);
    curl_close($curl);
    $result = json_decode($res);
    return $result;
}
