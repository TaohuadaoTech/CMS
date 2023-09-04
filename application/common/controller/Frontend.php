<?php

namespace app\common\controller;

use app\common\library\Auth;
use app\common\library\handle\ConfigurationHandle;
use app\common\library\Logger;
use app\common\library\Redis;
use app\common\model\WebAdvertisement;
use app\common\model\WebCategorys;
use app\common\model\WebLinks;
use app\common\model\WebSites;
use think\Config;
use think\Controller;
use think\Hook;
use think\Lang;
use think\Loader;
use think\Validate;

/**
 * 前台控制器基类
 */
class Frontend extends Controller
{

    /**
     * 布局模板
     * @var string
     */
    private $layout = '';

    /**
     * 无需登录的方法,同时也就不需要鉴权了
     * @var array
     */
    protected $noNeedLogin = [];

    /**
     * 无需鉴权的方法,但需要登录
     * @var array
     */
    protected $noNeedRight = ['*'];

    /**
     * URL路径参数
     * @var array
     */
    protected $urlPathParams = [];

    /**
     * 手机标识
     * @var bool
     */
    protected $mobileMark = false;

    /**
     * 显示顶部广告
     * @var bool
     */
    protected $showTopAdvertising = true;

    /**
     * 显示底部广告
     * @var bool
     */
    protected $showBottomAdvertising = true;

    /**
     * 显示友情链接
     * @var bool
     */
    protected $showFooterLike = true;

    /**
     * 显示左边广告
     * @var bool
     */
    protected $showLeftAdvertising = true;

    /**
     * 显示右边广告
     * @var bool
     */
    protected $showRightAdvertising = true;

    /**
     * 用户会员标记
     * @var bool
     */
    protected $userVipMark = false;

    /**
     * 权限Auth
     * @var Auth
     */
    protected $auth = null;

    public function _initialize()
    {
        //移除HTML标签
        $this->request->filter('trim,strip_tags,htmlspecialchars');
        $modulename = $this->request->module();
        $controllername = Loader::parseName($this->request->controller());
        $actionname = strtolower($this->request->action());

        // 检测IP是否允许
        check_ip_allowed();

        // 如果有使用模板布局
        if ($this->layout) {
            $this->view->engine->layout('layout/' . $this->layout);
        }
        $this->auth = Auth::instance();

        // token
        $token = $this->request->server('HTTP_TOKEN', $this->request->request('token', \think\Cookie::get('token')));

        $path = str_replace('.', '/', $controllername) . '/' . $actionname;
        // 设置当前请求的URI
        $this->auth->setRequestUri($path);
        // 检测是否需要验证登录
        if (!$this->auth->match($this->noNeedLogin)) {
            //初始化
            $this->auth->init($token);
            //检测是否登录
            if (!$this->auth->isLogin()) {
                $this->redirect('/');
//                $this->error(__('Please login first'), '/');
            }
            // 判断是否需要验证权限
            if (!$this->auth->match($this->noNeedRight)) {
                // 判断控制器和方法判断是否有对应权限
                if (!$this->auth->check($path)) {
                    $this->error(__('You have no permission'));
                }
            }
        } else {
            // 如果有传递token才验证是否登录状态
            if ($token) {
                $this->auth->init($token);
            }
        }

        $user = $this->auth->getUser();
        if ($user) {
            $user->salt = '';
            $user->password = '';
            if (empty($user->vip_expiration_time)) {
                $user->vip_mark = false;
                $user->vip_expiration_status = __('Not purchased');
            } else if ($user->vip_expiration_time <= time()) {
                $user->vip_mark = false;
                $user->vip_expiration_status = __('VIP expires');
            } else {
                $user->vip_mark = true;
                $user->vip_expiration_status = date('Y-m-d', $user->vip_expiration_time);
            }
            $this->userVipMark = $user->vip_mark;
        }
        $this->view->assign('user', $user);

        // 语言检测
        $lang = $this->request->langset();
        $lang = preg_match("/^([a-zA-Z\-_]{2,10})\$/i", $lang) ? $lang : 'zh-cn';

        $site = Config::get("site");

        $upload = \app\common\model\Config::upload();

        // 上传信息配置后
        Hook::listen("upload_config_init", $upload);

        // 配置信息
        $config = [
            'site'           => array_intersect_key($site, array_flip(['name', 'cdnurl', 'version', 'timezone', 'languages'])),
            'upload'         => $upload,
            'modulename'     => $modulename,
            'controllername' => $controllername,
            'actionname'     => $actionname,
            'jsname'         => 'frontend/' . str_replace('.', '/', $controllername),
            'moduleurl'      => rtrim(url("/{$modulename}", '', false), '/'),
            'language'       => $lang
        ];
        $config = array_merge($config, Config::get("view_replace_str"));

        Config::set('upload', array_merge(Config::get('upload'), $upload));

        // 配置信息后
        Hook::listen("config_init", $config);
        // 解析URL路径参数
        $this->handleUrlPathParams($path);
        // 加载当前控制器语言包
        $this->loadlang($controllername);
        $this->assign('site', $site);
        $this->assign('path', $path);
        $this->assign('config', $config);

        if ($this->request->isGet() && (!($this->request->isAjax() || $this->request->isPjax()))) {
            // 加载登录注册参数信息
            $this->loadLoginRegisterParams();
            // 检测访问设备类型
            $this->checkAccessDevice();
            // 加载当前使用的模板信息
            $this->loadCurrentTemplateInfo();
        }
        $this->assign('scheme', $this->request->scheme());
    }

    /**
     * 获取URL路径参数
     * @param $index
     * @return mixed|null
     */
    public function getUrlPathParams($index)
    {
        try {
            return $this->urlPathParams[$index];
        } catch (\Exception $ex) {

        }
        return null;
    }

    /**
     * 获取URL路径Int参数
     * @param $index
     * @return mixed|null
     */
    public function getUrlPathParamsInt($index)
    {
        try {
            $params = $this->urlPathParams[$index];
            if (is_numeric($params)) {
                return $params;
            }
        } catch (\Exception $ex) {

        }
        return null;
    }

    /**
     * 解析URL路径参数
     * @param $path
     */
    protected function handleUrlPathParams($path)
    {
        try {
            $uri = $this->request->path();
            $url_params = str_replace("index/{$path}", "", $uri);
            $params_array = explode("/", $url_params);
            foreach ($params_array as $param) {
                $param = trim($param);
                if ($param != '') {
                    $this->urlPathParams[] = $param;
                }
            }
        } catch (\Exception $ex) {
            Logger::error('Frontend::handleUrlPathParams => 解析URL路径参数失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

    /**
     * 加载登录注册信息
     */
    protected function loadLoginRegisterParams()
    {
        if (!$this->auth->isLogin()) {
            // 邮箱验证是否开启
            $this->view->assign('mailSwitch', ConfigurationHandle::getSystemConfigMailSwitch());
            $this->assignconfig('mailSwitch', ConfigurationHandle::getSystemConfigMailSwitch());
            // 是否开启邮箱后缀白名单
            if (ConfigurationHandle::getSystemConfigMailSuffixWhitelistSwitch()) {
                $mail_subffix_array = explode("\r", ConfigurationHandle::getSystemConfigMailSuffixWhitelist());
                foreach ($mail_subffix_array as $key => $value) {
                    $mail_subffix_array[$key] = trim($value);
                }
                $this->view->assign('mailSuffixWhitelist', $mail_subffix_array);
            }
            // 是否开启防机器人
            $anti_machine_switch = ConfigurationHandle::getSystemConfigAntiMachineSwitch();
            if ($anti_machine_switch) {
                $this->view->assign('googleRecaptchaSiteKey', ConfigurationHandle::getSystemConfigGoogleRecaptchaSiteKey());
                $this->assignconfig('googleRecaptchaSiteKey', ConfigurationHandle::getSystemConfigGoogleRecaptchaSiteKey());
            }
            $this->view->assign('antiMachineSwitch', $anti_machine_switch);
            $this->assignconfig('antiMachineSwitch', $anti_machine_switch);
        }
    }

    /**
     * 加载模板信息
     */
    protected function loadCurrentTemplateInfo()
    {
        try {
            $domain_name = $this->request->host(true);
            $redis_key = Redis::REDIS_KEY_WEB_SITE_TEMPLATE_PATH . $domain_name;
            $redis_value = Redis::getInstance()->get($redis_key);
            if (empty($redis_value)) {
                $site = WebSites::findWebSitesByDomain($domain_name);
                if (empty($site)) {
                    return;
                }
                $site_array = $site->toArray();
                $app_download_url = urlencode("{$this->request->scheme()}://{$domain_name}/index/app/download.html");
                $template_basic_path = str_replace('{name}', $site_array['model_path'], ConfigurationHandle::getBasicTemplateBasicPath());
                $site_array['theme'] = $site_array['model_theme'];
                $site_array['template_path'] = $template_basic_path;
                $site_array['layout_template_path'] = $template_basic_path . 'layout/default.html';
                $site_array['logo'] = empty($site_array['logo']) ? '/assets/img/default/logo.png' : $site_array['logo'];
                $site_array['icon'] = empty($site_array['icon']) ? '/assets/img/default/favicon.ico' : $site_array['icon'];
//                $site_array['app_download_qrcode'] = "{$this->request->scheme()}://{$domain_name}/qrcode/build?text={$app_download_url}";
                Redis::getInstance()->setex($redis_key, Redis::REDIS_KEY_EXPIRE_ONE_MONTH, json_encode($site_array, JSON_UNESCAPED_UNICODE));
            } else {
                $site_array = json_decode($redis_value, true);
            }
            $this->view->assign('webSite', $site_array);
            $this->view->setTemplatePath($site_array['template_path']);
            $this->view->engine->layout($site_array['layout_template_path']);
            // 加载公共页面数据
            $this->loadPublicPageData();
        } catch (\Throwable $ex) {
            Logger::error('Frontend::loadCurrentTemplate => 加载模板信息失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

    /**
     * 加载公共页面数据
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function loadPublicPageData()
    {
        // 获取分类信息
        $array = (new WebCategorys())->findWebCategorysByCache();
        if (!empty($array)) {
            $category_array = $childs_array = [];
            foreach ($array as $value) {
                if ($value['belong_to']) {
                    $childs_array[$value['belong_to']][] = $value;
                } else {
                    $category_array[] = $value;
                }
            }
            foreach ($category_array as &$category) {
                if (isset($childs_array[$category['id']])) {
                    $category['childs'] = $childs_array[$category['id']];
                } else {
                    $category['childs'] = [];
                }
            }
            $this->view->assign('categoryArray', $category_array);
        }
        // 顶部广告
        if ($this->showTopAdvertising) {
            $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::TOP_ID, $this->mobileMark);
            if (!empty($array)) {
                $this->assign('topAdvertising', $array);
            }
        }
        // 显示底部广告
        if ($this->showBottomAdvertising) {
            $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::BOTTOM_ID, $this->mobileMark);
            if (!empty($array)) {
                $this->assign('bottomAdvertising', $array);
            }
        }
        // 友情链接
        if ($this->showFooterLike) {
            $array = WebLinks::findAllWebLinks();
            if (!empty($array)) {
                $this->assign('footerLike', $array);
            }
        }
        // 左边广告
        if ($this->showLeftAdvertising && (!$this->mobileMark)) {
            $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::LEFT_ID, $this->mobileMark);
            if (!empty($array)) {
                $this->assign('leftAdvertising', $array);
            }
        }
        // 右边广告
        if ($this->showRightAdvertising && (!$this->mobileMark)) {
            $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::RIGHT_ID, $this->mobileMark);
            if (!empty($array)) {
                $this->assign('rightAdvertising', $array);
            }
        }
        // 获取联盟JS
        $array = WebAdvertisement::findWebAdvertisementById(WebAdvertisement::ALLIANCE_JS_ID, $this->mobileMark);
        if (!empty($array)) {
            $this->assign('allianceJs', $array);
        }
    }

    /**
     * 检测访问设备类型
     */
    protected function checkAccessDevice()
    {
        try {
            $this->mobileMark = $this->request->isMobile();
            $this->view->assign('mobileMark', $this->mobileMark);
            $this->assignconfig('mobileMark', $this->mobileMark);
        } catch (\Throwable $ex) {
            Logger::error('Frontend::checkAccessDevice => 检测访问设备类型失败: ' . $ex->getMessage());
            Logger::error($ex->getTraceAsString());
        }
    }

    /**
     * 加载语言文件
     * @param string $name
     */
    protected function loadlang($name)
    {
        $name = Loader::parseName($name);
        $name = preg_match("/^([a-zA-Z0-9_\.\/]+)\$/i", $name) ? $name : 'index';
        $lang = $this->request->langset();
        $lang = preg_match("/^([a-zA-Z\-_]{2,10})\$/i", $lang) ? $lang : 'zh-cn';
        Lang::load(APP_PATH . $this->request->module() . '/lang/' . $lang . '/' . str_replace('.', '/', $name) . '.php');
    }

    /**
     * 渲染配置信息
     * @param mixed $name  键名或数组
     * @param mixed $value 值
     */
    protected function assignconfig($name, $value = '')
    {
        $this->view->config = array_merge($this->view->config ? $this->view->config : [], is_array($name) ? $name : [$name => $value]);
    }

    /**
     * 刷新Token
     */
    protected function token()
    {
        $token = $this->request->param('__token__');

        //验证Token
        if (!Validate::make()->check(['__token__' => $token], ['__token__' => 'require|token'])) {
            $this->error(__('Token verification error'), '', ['__token__' => $this->request->token()]);
        }

        //刷新Token
        $this->request->token();
    }

    /**
     * 获取分页条
     * @param $pn
     * @param $page_size
     * @param $total
     * @param $url
     * @return string
     */
    protected function getPageHtml($pn, $page_size, $total, $url)
    {
        $before_button_num = 0;
        $button_num = $this->mobileMark ? 2 : 4;
        $total_pn = $total % $page_size == 0 ? intval($total / $page_size) : intval($total / $page_size) + 1;
        if ($pn == 1 && $pn >= $total_pn) {
            return '';
        }
        $html  = '<div id="pagination">';
        $html .= '<nav aria-label="Page navigation" class="pb-1 custom-pnvigation">';
        $html .= '<ul class="pagination justify-content-center align-items-center">';
        $html .= '<li class="d-md-block d-none"><span>共' . $total . '条</span></li>';
        $html .= '<li class="border-item ' . ($pn <= 1 ? 'disabled' : '') . '"><span data-page-url="' . $url . '/' . ($pn - 1) . '/' . $page_size . '.html">上一页</span></li>';
        for ($i = $pn - $button_num; $i < $pn; $i++) {
            if ($i >= 1) {
                $before_button_num++;
                $html .= '<li class="border-item"><span data-page-url="' . $url . '/' . $i . '/' . $page_size . '.html">' . $i . '</span></li>';
            }
        }
        $html .= '<li class="border-item active"><span data-page-url="' . $url . '/' . $pn . '/' . $page_size . '.html">' . $pn . '</span></li>';
        for ($j = $pn + 1; $j <= $pn + ($button_num * 2) - $before_button_num && $j <= $total_pn; $j++) {
            $html .= '<li class="border-item"><span data-page-url="' . $url . '/' . $j . '/' . $page_size . '.html">' . $j . '</span></li>';
        }
        $html .= '<li class="border-item ' . ($pn >= $total_pn ? 'disabled' : '') . '"><span data-page-url="' . $url . '/' . ($pn + 1) . '/' . $page_size . '.html">下一页</span></li>';
        $html .= '<li class="plan-text d-none d-md-block"><span>跳到第</span></li>';
        $html .= '<li class="d-none d-md-block"><input type="number" name="pn" class="form-control" data-max-pn="' . $total_pn . '" /></li>';
        $html .= '<li class="plan-text d-none d-md-block"><span>页</span></li>';
        $html .= '<li class="d-none d-md-block"><span class="btn sure" data-page-url="' . $url . '/{pn}/' . $page_size . '.html">确定</span></li>';
        $html .= '</ul></nav><div id="skip-none">';
        $html .= '<nav aria-label="Page navigation" class="py-4 custom-pnvigation new-py-4">';
        $html .= '<ul class="pagination justify-content-center align-items-center">';
        $html .= '<li class="d-md-block"><span>共 ' . $total_pn . ' 页&emsp;</span></li>';
        $html .= '<li class="plan-text  d-md-block"><span>跳到第</span></li>';
        $html .= '<li class="d-md-block"><input type="number" name="pn" class="form-control" data-max-pn="' . $total_pn . '" /></li>';
        $html .= '<li class="plan-text  d-md-block"><span>页</span></li>';
        $html .= '<li class="d-md-block"><span class="btn sure" data-page-url="' . $url . '/{pn}/' . $page_size . '.html">确定</span></li>';
        $html .= '</nav></div></div>';
        return $html;
    }

}
