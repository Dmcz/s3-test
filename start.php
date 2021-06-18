<?php



ini_set('display_errors', 'on');
ini_set('display_startup_errors', 'on');

date_default_timezone_set('Asia/Shanghai');

error_reporting(E_ALL);

! defined('BASE_PATH') && define('BASE_PATH', dirname(__FILE__));

require BASE_PATH . '/vendor/autoload.php';

$envFilePath = './.env';

if (is_file($envFilePath)) {
    $env = parse_ini_file($envFilePath, true);    //解析env文件,name = PHP_KEY
    foreach ($env as $key => $val) {
        $name = strtoupper($key);
        if (is_array($val)) {
            foreach ($val as $k => $v) {    //如果是二维数组 item = PHP_KEY_KEY
                $item = $name . '_' . strtoupper($k);
                putenv("$item=$v");
            }
        } else {
            putenv("$name=$val");
        }
    }
}

use Aws\S3\S3Client;


$endpoint = getenv('ENDPOINT');
$accessKey = getenv('ACCESS_KEY');
$accessSecret = getenv('ACCESS_SECRET');
$region = getenv('ACCESS_SECRET');
$bucket = getenv('BUCKET');
$use_path_style_endpoint = getenv('USE_PATH_STYLE_ENDPOINT');


$config = [
    'endpoint' => $endpoint,
    'credentials' => [
        'key'    => $accessKey,
        'secret' => $accessSecret,
    ],
    'version' => 'latest',
    'region'  => $region
];

if($use_path_style_endpoint){
    $config['use_path_style_endpoint'] = true;
}

$client = new S3Client($config);

$action = $_SERVER['argv'][1]??'';

if($action == 'upload'){

    $result = $client->putObject([
        'Bucket' => $bucket,
        'Key'    => 'test.jpg',
        'SourceFile'   => BASE_PATH . DIRECTORY_SEPARATOR .'upload.jpg',
    ]);

    echo  $result->get('ObjectURL') . PHP_EOL;
}else if($action == 'download'){
    $option = [
        'Bucket' => $bucket,
        'Key'    => 'test.jpg',
        'SaveAs' => 'download.jpg'
    ];

    $result = $client->getObject($option);

    echo BASE_PATH . DIRECTORY_SEPARATOR . 'download.jpg' . PHP_EOL;
}
