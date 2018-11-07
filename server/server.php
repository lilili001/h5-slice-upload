<?php
header("Access-Control-Allow-Origin: *");
header("Content-type: text/html; charset=utf-8");
include "helper.php";
define ('SITE_ROOT', realpath(dirname(__FILE__)));
define("FILEPATH",realpath(dirname(__FILE__)) . "/dir/files/" );
define("FRAGPATH",realpath(dirname(__FILE__)) . "/dir/frags/" );

require_once "../vendor/autoload.php";

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// create a log channel
$log = new Logger('my_app');
$log->pushHandler(new StreamHandler(__DIR__.'/my_app.log', Logger::DEBUG));

$file = $_FILES['file'];

//打印文件
$log->info('切片',$file);

$orgFileName = $_POST['filename'];
$log->info("orgFileName:" . $orgFileName);

//获取文件名称
$filename = explode("." , $_POST['filename']);

//获取文件后缀
$ext = $filename[1];
$filename = $filename[0];

$log->info("ext:" . $ext);

//新建frag 文件夹, 以filename为命名方式
if(!file_exists(FRAGPATH.$filename)){
    mkdir(FRAGPATH.$filename);
}

//将接收到的frag文件移入file文件夹中
//$frag_path = SITE_ROOT.'/dir/'.$filename.'/'.iconv('UTF-8','gbk',basename($_FILES['file']['tmp_name']));
$frag_path = FRAGPATH.$filename ."/". $_POST['fragname'];
try{
    if(move_uploaded_file($_FILES['file']['tmp_name'] , $frag_path)){
        echo response(["status" => "上传成功"]);
    }
}catch (Exception $e){
    throw new Exception();
}

//合并file文件夹中的frag为最终文件
if( $_POST['fragindex'] == $_POST["total"] -1 ){
    $blob = "";
    $fragDir = FRAGPATH.$filename;

    $handler = @opendir($fragDir);

    //获取filename
    while ( ( $fragFileName = readdir($handler) ) !== false ){
        $fp = fopen( FILEPATH . '/' . $orgFileName,"ab" );
        // 务必使用!==，防止目录下出现类似文件名“0”等情况
        if ($fragFileName !== "." && $fragFileName !== "..")
        {
            //方式一:
            //$blob .= file_get_contents( $fragDir . "/" . $fragFileName );

            //方式二:
            $value = $fragDir . "/" . $fragFileName;
            $handle = fopen($value,"rb");
            fwrite($fp,fread($handle,filesize($value)));
            fclose($handle);

            //删除切片文件
            @unlink($fragDir . "/" . $fragFileName);
        }
    }

    //合并切片到文件
    //file_put_contents( FILEPATH. "/" . $filename . ".". $ext , $blob );

    //删除切片文件夹
    @rmdir($fragDir);
}
