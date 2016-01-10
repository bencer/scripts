<?php

namespace serverdensity;
require __DIR__.'/vendor/autoload.php';
use serverdensity\Client;

$DEVICE = $argv[1];

if (isset($_SERVER["SDTOKEN"])) {
   $TOKEN = $_SERVER["SDTOKEN"];
} else {
   $TOKEN = ''; // you can hardcore the API Token here
}

if (empty($TOKEN)) {
    die("Server Density API Token missing.");
}

// configure time period for the report
$end_date = time(); // now
$start_date = $end_date - (60*60*24*31); // last 31 days

$client = new Client();
$client->authenticate($TOKEN);

// find deviceId
$filter = array(
    "name" => $DEVICE,
    "type" => "device"
);
$fields = array("name", "type", "_id");
$device = $client->api('devices')->search($filter, $fields);
$device_id = $device[0]['_id'];

// available metrics
$available = $client->api('metrics')->available($device_id, $start_date, $end_date);
//print_r($available);


// NETWORK TRAFFIC
$table = array();
$filter = array(
    'networkTraffic' => [
        'eth0' => ['rxMBitS', 'txMBitS']
    ]
);
$metrics = $client->api('metrics')->metrics($device_id, $filter, $start_date, $end_date);
//print_r($metrics);
$data = $client->api('metrics')->formatMetrics($metrics);
//print_r($data);

$points = $data[0];
$graphData = array();
foreach($points['data'] as $i => $array){
    // this is because Javascript takes months zero based
    $date = explode(" ", date("Y m d H i s", $array['x']));
    --$date[1];
    $datestr = implode(", ", $date);
    $row = array(
        //array('v' => date("Y-m-d H:i", $array['x'])),
        array('v' => "Date(" .  $datestr . ")"),
        array('v' => $array['y']),
        array('v' => $data[1]['data'][$i]['y']) // FIXME ugly HACK 
    );
    $graphData[] = array('c' => $row);
}
$table['cols'] = array(
    array('id' => '', 'label' => 'date', 'type' => 'datetime'),
    array('id' => '', 'label' => 'eth0 rx (mbps)', 'type' => 'number'),
    array('id' => '', 'label' => 'eth0 tx (mbps)', 'type' => 'number'),
);
$table['rows'] = $graphData;
file_put_contents('network.json', json_encode($table));


// DISK USAGE
$table = array();
$filter = array(
    'diskUsage' => [
        '/' => ['cp']
    ]
);
$metrics = $client->api('metrics')->metrics($device_id, $filter, $start_date, $end_date);
//print_r($metrics);
$data = $client->api('metrics')->formatMetrics($metrics);
//print_r($data);

$points = $data[0];
$graphData = array();
foreach($points['data'] as $i => $array){
    // this is because Javascript takes months zero based
    $date = explode(" ", date("Y m d H i s", $array['x']));
    --$date[1];
    $datestr = implode(", ", $date);
    $row = array(
        array('v' => "Date(" .  $datestr . ")"),
        array('v' => $array['y'])
    );
    $graphData[] = array('c' => $row);
}
$table['cols'] = array(
    array('id' => '', 'label' => 'date', 'type' => 'datetime'),
    array('id' => '', 'label' => '/ usage %', 'type' => 'number'),
);
$table['rows'] = $graphData;
file_put_contents('diskusage.json', json_encode($table));


// load
$table = array();
$filter = array(
    'loadAvrg' => ['loadAvrg']
);
$metrics = $client->api('metrics')->metrics($device_id, $filter, $start_date, $end_date);
//print_r($metrics);
$data = $client->api('metrics')->formatMetrics($metrics);
//print_r($data);
$points = $data[0];
$graphData = array();
foreach($points['data'] as $i => $array){
    // this is because Javascript takes months zero based
    $date = explode(" ", date("Y m d H i s", $array['x']));
    --$date[1];
    $datestr = implode(", ", $date);
    $row = array(
        array('v' => "Date(" .  $datestr . ")"),
        array('v' => $array['y'])
    );
    $graphData[] = array('c' => $row);
}
$table['cols'] = array(
    array('id' => '', 'label' => 'date', 'type' => 'datetime'),
    array('id' => '', 'label' => 'load', 'type' => 'number'),
);
$table['rows'] = $graphData;
file_put_contents('load.json', json_encode($table));


// CPU USAGE
$table = array();
$filter = array(
    'cpuStats' => [
        'ALL' => ['sys', 'usr', 'iowait', 'steal', 'soft', 'nice', 'gnice', 'guest', 'irq', 'idle']
    ]
);
$metrics = $client->api('metrics')->metrics($device_id, $filter, $start_date, $end_date);
//print_r($metrics);
$data = $client->api('metrics')->formatMetrics($metrics);
//print_r($data);

$points = $data[0];
$graphData = array();
foreach($points['data'] as $i => $array){
    // this is because Javascript takes months zero based
    $date = explode(" ", date("Y m d H i s", $array['x']));
    --$date[1];
    $datestr = implode(", ", $date);
    $row = array(
        array('v' => "Date(" .  $datestr . ")"),
        array('v' => $array['y']),
        array('v' => $data[1]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[2]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[3]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[4]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[5]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[6]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[7]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[8]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[9]['data'][$i]['y']) // FIXME ugly HACK 
    );
    $graphData[] = array('c' => $row);
}
$table['cols'] = array(
    array('id' => '', 'label' => 'date', 'type' => 'datetime'),
    array('id' => '', 'label' => 'gnice', 'type' => 'number'),
    array('id' => '', 'label' => 'guest', 'type' => 'number'),
    array('id' => '', 'label' => 'idle', 'type' => 'number'),
    array('id' => '', 'label' => 'iowait', 'type' => 'number'),
    array('id' => '', 'label' => 'interrupts', 'type' => 'number'),
    array('id' => '', 'label' => 'nice', 'type' => 'number'),
    array('id' => '', 'label' => 'soft', 'type' => 'number'),
    array('id' => '', 'label' => 'steal', 'type' => 'number'),
    array('id' => '', 'label' => 'system', 'type' => 'number'),
    array('id' => '', 'label' => 'user', 'type' => 'number'),
);
$table['rows'] = $graphData;
file_put_contents('cpu.json', json_encode($table));


// MEMORY USAGE
$table = array();
$filter = array(
    'memory' => [
        'memPhysUsed' => ['memPhysUsed'],
        'memPhysFree' => ['memPhysFree'],
        'memPhysCached' => ['memPhysCached']
    ]
);
$metrics = $client->api('metrics')->metrics($device_id, $filter, $start_date, $end_date);
//print_r($metrics);
$data = $client->api('metrics')->formatMetrics($metrics);
//print_r($data);

$points = $data[0];
$graphData = array();
foreach($points['data'] as $i => $array){
    // this is because Javascript takes months zero based
    $date = explode(" ", date("Y m d H i s", $array['x']));
    --$date[1];
    $datestr = implode(", ", $date);
    $row = array(
        array('v' => "Date(" .  $datestr . ")"),
        array('v' => $array['y']),
        array('v' => $data[1]['data'][$i]['y']), // FIXME ugly HACK 
        array('v' => $data[2]['data'][$i]['y']) // FIXME ugly HACK 
    );
    $graphData[] = array('c' => $row);
}
$table['cols'] = array(
    array('id' => '', 'label' => 'date', 'type' => 'datetime'),
    array('id' => '', 'label' => 'used', 'type' => 'number'),
    array('id' => '', 'label' => 'free', 'type' => 'number'),
    array('id' => '', 'label' => 'cached', 'type' => 'number'),
);
$table['rows'] = $graphData;
file_put_contents('memory.json', json_encode($table));

