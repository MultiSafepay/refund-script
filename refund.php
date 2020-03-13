<?php

const LIVE_URL = 'https://api.multisafepay.com/v1/json/';
const TEST_URL = 'https://testapi.multisafepay.com/v1/json/';

$filename = 'refund.csv';
$refundData = [];
$apiKey = $argv[1];

//Get the data
if (($h = fopen("{$filename}", "r")) !== false) {
    while (($data = fgetcsv($h, 256, ",")) !== false) {
        $refundData[] = $data;
    }
    // Close the file
    fclose($h);
}

//Unset first row (input fields)
unset($refundData[0]);

//format amount
foreach ($refundData as $key => $value) {
    $amount = $value[1];
    $formattedAmount = $value[1];
    $amount = trim(str_replace('€', '', $amount));
    $amount = trim(str_replace(',', '.', $amount));
    $refundData[$key][1] = (float)$amount;
}

foreach ($refundData as $key => $value) {
    $orderId = $value[0];
    $amount = $value[1];
    $description = $value[2];
    $url = LIVE_URL . "orders/" . $orderId . "/refunds";

    $jsonData = json_encode([
        'currency' => 'EUR',
        'amount' => $amount * 100,
        'description' => $description
    ]);

    $ch = curl_init($url);

    $request_headers = [
        'Accept: application/json',
        'api_key:' . $apiKey,
        'Content-Type: application/json'
    ];

    $request_headers[] = 'Content-Type: application/json';
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
    curl_setopt($ch, CURLOPT_ENCODING, "");
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ch, CURLOPT_HTTPHEADER, $request_headers);
    $body = curl_exec($ch);
    sleep(1);

    $refundData[$key]['order_id'] = $value[0];
    $refundData[$key]['amount'] = $value[1];
    $refundData[$key]['description'] = $value[2];

    unset($refundData[$key][0]);
    unset($refundData[$key][1]);
    unset($refundData[$key][2]);

    if (curl_errno($ch)) {
        $refundData[$key]['message'] = "Unable to communicate with the MultiSafepay payment server (" . curl_errno($ch) . "): " . curl_error($ch) . ".";
        $refundData[$key]['transaction_id'] = "";
        $refundData[$key]['refund_id'] = "";
        continue;
    }

    print_r($body);

    $returnData = json_decode($body);

    if ($returnData->success === false) {
        $refundData[$key]['message'] = $returnData->error_code .": ". $returnData->error_info;
        $refundData[$key]['transaction_id'] = "";
        $refundData[$key]['refund_id'] = "";
        continue;
    };

    $refundData[$key]['message'] = "";
    $refundData[$key]['transaction_id'] = $returnData->data->transaction_id;
    $refundData[$key]['refund_id'] = $returnData->data->refund_id;

}

$fp = fopen('results.json', 'w');
fwrite($fp, json_encode($refundData));
fclose($fp);
