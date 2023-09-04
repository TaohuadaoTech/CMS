<?php

namespace app\index\controller;

use app\common\controller\Frontend;
use app\common\library\Ems as Emslib;
use app\common\library\handle\ConfigurationHandle;
use app\common\model\User;
use fast\Http;
use think\Lang;
use think\Response;
use think\Validate;

/**
 * Ajax异步请求接口
 * @internal
 */
class Ajax extends Frontend
{

    protected $noNeedLogin = ['lang', 'upload', 'send_email_code'];

    /**
     * 加载语言包
     */
    public function lang()
    {
        $this->request->get(['callback' => 'define']);
        $header = ['Content-Type' => 'application/javascript'];
        if (!config('app_debug')) {
            $offset = 30 * 60 * 60 * 24; // 缓存一个月
            $header['Cache-Control'] = 'public';
            $header['Pragma'] = 'cache';
            $header['Expires'] = gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        }

        $controllername = input("controllername");
        $this->loadlang($controllername);
        //强制输出JSON Object
        return jsonp(Lang::get(), 200, $header, ['json_encode_param' => JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE]);
    }

    /**
     * 生成后缀图标
     */
    public function icon()
    {
        $suffix = $this->request->request("suffix");
        $suffix = $suffix ? $suffix : "FILE";
        $data = build_suffix_image($suffix);
        $header = ['Content-Type' => 'image/svg+xml'];
        $offset = 30 * 60 * 60 * 24; // 缓存一个月
        $header['Cache-Control'] = 'public';
        $header['Pragma'] = 'cache';
        $header['Expires'] = gmdate("D, d M Y H:i:s", time() + $offset) . " GMT";
        $response = Response::create($data, '', 200, $header);
        return $response;
    }

    /**
     * 上传文件
     */
    public function upload()
    {
        return action('api/common/upload');
    }

    /**
     * 发送邮箱验证码
     */
    public function send_email_code()
    {
        if (ConfigurationHandle::getSystemConfigAntiMachineSwitch()) {
            $token = $this->request->param('token');
            if (empty($token)) {
                $this->error(__('Unknown data format'));
            }
            $recaptcha_verify_api = ConfigurationHandle::getBasicRecaptchaVerifyApi();
            $response = Http::post($recaptcha_verify_api, ['secret' => ConfigurationHandle::getSystemConfigGoogleRecaptchaKey(), 'response' => $token]);
            $response_array = json_decode($response, true);
            $google_recaptcha_score = ConfigurationHandle::getSystemConfigGoogleRecaptchaScore() ?: '0.5';
            if ((!$response_array['success']) || $response_array['score'] < $google_recaptcha_score) {
                $this->error(__('verify_fail'));
            }
        }
        $email = $this->request->post("email");
        $event = $this->request->post("event");
        $event = $event ? $event : 'register';
        if (empty($email)) {
            $this->error(__('Unknown data format'));
        }
        if (!Validate::is($email, "email")) {
            $this->error(__('Email is incorrect'));
        }
        if ($event) {
            $userinfo = User::getByEmail($email);
            if ($event == 'register' && $userinfo) {
                //已被注册
                $this->error(__('已被注册'));
            } elseif (in_array($event, ['changeemail']) && $userinfo) {
                //被占用
                $this->error(__('已被占用'));
            } elseif (in_array($event, ['changepwd', 'resetpwd']) && !$userinfo) {
                //未注册
                $this->error(__('未注册'));
            }
        }
        $ret = Emslib::send($email, null, $event);
        if ($ret) {
            $this->success(__('send_success'));
        }
        $this->error(__('mail_code_find_fail'));
    }

}
