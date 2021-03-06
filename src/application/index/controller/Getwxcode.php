<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020 2020NCOV All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: zhangqixun <zhangqx@ss.pku.edu.cn>
// +----------------------------------------------------------------------

namespace app\index\controller;

use think\Request;
use think\Db;
use think\Session;
use think\Controller;
use \think\Config;
use app\index\service\Http;

class Getwxcode extends Controller
{

    public function index()
    {
        return;
    }

    public function getAccessToken()
    {
        $appid = Config::get('wechat_appid');
        $secret = Config::get('wechat_secret');
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . $appid . "&secret=" . $secret;
        $res = json_decode(Http::get_request($url));
        $access_token = @$res->access_token;
        return $access_token;
    }

    public function get_image()
    {
        $id = trim($this->request->param('id', '100000001', 'intval'));
        $access_token = $this->getAccessToken();
        $url = "https://api.weixin.qq.com/wxa/getwxacodeunlimit?access_token=" . $access_token;
        $data['scene'] = $id;
        //小程序路径
        $data['path'] = 'pages/info/info';
        //二维码大小
        $data['width'] = '430';
        $res = $this->postUrl($url, json_encode($data));
        $path = $id . '.jpg';
        file_put_contents($path, $res);
        $return['status_code'] = 2000;
        $return['msg'] = 'ok';
        $return['img'] = "/" . $path;
        echo "<img src='" . $return['img'] . "' />";
        exit;
    }

    // 实现Post请求
    public function postUrl($url, $data)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE);
        if (!empty($data)) {
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        $output = curl_exec($curl);
        curl_close($curl);
        return $output;
    }

}
