<?php

namespace App\Http\Controllers\Admin;

use getID3;
use JohnLui\AliyunOSS;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Request;

class BaseController extends Controller
{


    public function __construct()
    {



    }


    //获取所有输入统一接口
    public function getInput($name,$html=1){

        $name = Request::input($name,"");

        if($html){
            return htmlspecialchars($name);
        }
        return $name;

    }


    public function getPwd($str){

        return hash("sha256", substr(md5($str),0,12));

    }

    public function pupload(){
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        /*
        // Support CORS
        header("Access-Control-Allow-Origin: *");
        // other CORS headers if any...
        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
            exit; // finish preflight CORS requests here
        }
        */
        /*$targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";*/

        $date_dir = date("Ymd");
        $targetDir = Config::get('constants.UPLOAD_DIR') . $date_dir;
//        $targetDir = "files/". date("Ymd");
        $cleanupTargetDir = true; // Remove old files//移除旧文件
        $cleanupTargetDir = true; // Remove old files//移除旧文件
        $cleanupTargetDir = true; // Remove old files//移除旧文件
        $maxFileAge = 5 * 3600; // Temp file age in seconds

        // Create target dir如果目录不存在就创建一个
        if (!file_exists($targetDir)) {
            @mkdir($targetDir, 0777, true);
        }

        // Get a file name获取传入的文件名
//        dump($_REQUEST);
        if (isset($_REQUEST["name"])) {
            $fileName = $_REQUEST["name"];
        } else if (!empty($_FILES)) {
            $fileName = $_FILES["file"]["name"];
        } else {
            $fileName = uniqid("file_");
            $arr = ["status" => 0, "msg" => ["code" => 100, "message" => "没找到文件！"]];
            return $arr;

        }

        //生成文件路径
        $filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
        // Chunking might be enabled检查是否有大文件分块上传
        $chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
        $chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 0;//把大文件总共分成多少小块文件

        // Remove old temp files移除临时文件
        if ($cleanupTargetDir) {
            if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
                $arr = ["status" => 0, "msg" => ["code" => 100, "message" => "打开临时目录失败"]];
                return $arr;
            }

            while (($file = readdir($dir)) !== false) {//遍历$targetDir目录下的所有文件
                $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

                // If temp file is current file proceed to the next（如果临时文件是当前文件，则继续进行下一个）
                if ($tmpfilePath == "{$filePath}.part") {
                    continue;
                }

                // Remove temp file if it is older than the max age and is not the current file（删除临时文件，如果它比最大年龄老，而不是当前的文件）
                if (preg_match('/\.part$/', $file) && (filemtime($tmpfilePath) < time() - $maxFileAge)) {
                    @unlink($tmpfilePath);//删除重复（文件名字相同）的文件
                }
            }
            closedir($dir);

        }

        // Open temp file打开临时文件，即（如果没有{$filePath}.part这个文件，就创建一个）
//        if (!$out = @fopen("{$filePath}.part", $chunks ? "ab" : "wb")) {
        if (!$out = @fopen("{$filePath}", $chunks ? "ab" : "wb")) {
            $arr = ["status" => 0, "msg" => ["code" => 102, "message" => "打开输出流失败"]];
            return $arr;
        }

        if (!empty($_FILES)) {//如果multipart为true，则走这个条件
            //is_uploaded_file() 函数判断指定的文件是否是通过 HTTP POST 上传的
            if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
                $arr = ["status" => 0, "msg" => ["code" => 103, "message" => "无法移动上传的文件"]];
                return $arr;
            }

            // Read binary input stream and append it to temp file读取二进制输入流并将其追加到临时文件中
            if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
                $arr = ["status" => 0, "msg" => ["code" => 101, "message" => "打开输入流失败"]];
                return $arr;
            }
        } else {//如果multipart为false，则走这个条件
            if (!$in = @fopen("php://input", "rb")) {
                $arr = ["status" => 0, "msg" => ["code" => 101, "message" => "打开输入流失败"]];
                return $arr;
            }
        }

        while ($buff = fread($in, 4096)) {//循环读取，一次读取一行内容(4096代表一行)
            fwrite($out, $buff);
        }

        @fclose($out);
        @fclose($in);

        // Check if file has been uploaded检查所以分块文件是否已全部被上传
        if (!$chunks || $chunk == $chunks - 1) {
            // Strip the temp .part suffix off关 闭带有临时.part后缀的文件，并重命名
            //$extArr = explode("\\",$filePath);

            $extArr = explode(".", $filePath);
            $ext = strtolower($extArr[count($extArr) - 1]);

            $time_long = '0';

            if ($ext=='mp4') {
                $fileName = 'video/' . date("Y") . "/" . date("m") . "/" . date("d") . "/" . md5(date('YmdHis') . '_' . mt_rand(100, 999)) . "." . $ext;
                if (!is_dir(Config::get('constants.UPLOAD_DIR') . "/" . 'video/' . date("Y") . "/" . date("m") . "/" . date("d"))) {
                    mkdir(Config::get('constants.UPLOAD_DIR') . "/" . 'video/' . date("Y") . "/" . date("m") . "/" . date("d"), 0777, true);
                }


            } else {
                $fileName = 'images/' . date("Y") . "/" . date("m") . "/" . date("d") . "/" . md5(date('YmdHis') . '_' . mt_rand(100, 999)) . "." . $ext;
                if (!is_dir(Config::get('constants.UPLOAD_DIR') . "/" . 'images/' . date("Y") . "/" . date("m") . "/" . date("d"))) {
                    mkdir(Config::get('constants.UPLOAD_DIR') . "/" . 'images/' . date("Y") . "/" . date("m") . "/" . date("d"), 0777, true);
                }
            }

            rename("{$filePath}", Config::get('constants.UPLOAD_DIR') . $fileName);

            if ($ext=='mp4') {
                $new = new getID3();

                $getID3 = $new->analyze(Config::get('constants.UPLOAD_DIR') . $fileName);

                $time_long = $getID3['playtime_seconds'];      //获取mp3的长度信息

            }



            $server_address = config('alioss.ALIOSS_SERVER');

            $oss_client = AliyunOSS::boot('北京', '经典网络', false, config('alioss.ALIOSS_KEYID'), config('alioss.ALIOSS_KEYSECRET'));

            $oss_client->setBucket(config('alioss.ALIOSS_BUCKETNAME'));

            $option = [];
            if (($ext) == 'png') {
                $option = [
                    'ContentType' => 'image/png',
                ];
            } elseif ($ext == 'jpg') {
                $option = [
                    'ContentType' => 'image/jpeg',
                ];
            } elseif ($ext == 'jpeg') {
                $option = [
                    'ContentType' => 'image/jpeg',
                ];
            } elseif ($ext == 'mp4') {
                $option = [
                    'ContentType' => 'video/mp4',
                ];
            }

            $result = $oss_client->uploadFile($fileName, Config::get('constants.UPLOAD_DIR') . $fileName, $option);

            //阿里 OSS 图片上传
            if ($result) {

                @unlink(Config::get('constants.UPLOAD_DIR') . $fileName);

                $url = $oss_client->getPublicUrl($fileName);

                $data[] = $url;

                return [
                    'errno' => '0',
                    'src' => $url,
                    'status' => 1,
                    'msg' => '上传成功',
                    'code' => '1',
                    'data' => $data,
                    'timeLong' => $time_long
                ];
            } else {
                return ['status' => '0', 'msg' => '上传失败'];
            }

        }

    }

    public function writeLog($content)
    {

        $fb = fopen('../text.log', 'a+');

        fwrite($fb, $content . "\r\n");
        fwrite($fb, date('Y-m-d H:i:s') . "\r\n");

        fclose($fb);

    }



}
