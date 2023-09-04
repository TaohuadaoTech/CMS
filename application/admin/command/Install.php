<?php

namespace app\admin\command;

use app\common\library\handle\ConfigurationHandle;
use app\common\library\SiteUtil;
use app\common\library\StandApi;
use fast\Random;
use PDO;
use think\Config;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;
use think\Exception;
use think\Lang;
use think\Request;
use think\View;

class Install extends Command
{
    protected $model = null;
    /**
     * @var \think\View 视图类实例
     */
    protected $view;

    /**
     * @var \think\Request Request 实例
     */
    protected $request;

    protected function configure()
    {
        $config = Config::get('database');
        $this
            ->setName('install')
            ->addOption('hostname', 'a', Option::VALUE_OPTIONAL, 'mysql hostname', $config['hostname'])
            ->addOption('hostport', 'o', Option::VALUE_OPTIONAL, 'mysql hostport', $config['hostport'])
            ->addOption('database', 'd', Option::VALUE_OPTIONAL, 'mysql database', $config['database'])
            ->addOption('prefix', 'r', Option::VALUE_OPTIONAL, 'table prefix', $config['prefix'])
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, 'mysql username', $config['username'])
            ->addOption('password', 'p', Option::VALUE_OPTIONAL, 'mysql password', $config['password'])
            ->addOption('force', 'f', Option::VALUE_OPTIONAL, 'force override', false)
            ->setDescription('New installation of TaohuadaoCMS');
    }

    /**
     * 命令行安装
     */
    protected function execute(Input $input, Output $output)
    {
        define('INSTALL_PATH', APP_PATH . 'admin' . DS . 'command' . DS . 'Install' . DS);
        // 覆盖安装
        $force = $input->getOption('force');
        $hostname = $input->getOption('hostname');
        $hostport = $input->getOption('hostport');
        $database = $input->getOption('database');
        $prefix = $input->getOption('prefix');
        $username = $input->getOption('username');
        $password = $input->getOption('password');

        $installLockFile = INSTALL_PATH . "install.lock";
        if (is_file($installLockFile) && !$force) {
            throw new Exception("\nTaohuadaoCMS already installed!\nIf you need to reinstall again, use the parameter --force=true ");
        }

        $adminUsername = 'admin';
        $adminPassword = Random::alnum(10);
        $adminEmail = 'admin@admin.com';
        $siteName = __('Peach Blossom Island');

        $adminName = $this->installation($hostname, $hostport, $database, $username, $password, $prefix, $adminUsername, $adminPassword, $adminEmail, null, $siteName);
        if ($adminName) {
            $output->highlight("Admin url:http://www.yoursite.com/{$adminName}");
        }

        $output->highlight("Admin username:{$adminUsername}");
        $output->highlight("Admin password:{$adminPassword}");

        \think\Cache::rm('__menu__');

        $output->info("Install Successed!");
    }

    /**
     * PC端安装
     */
    public function index()
    {
        $this->loadlang();
        $this->view = View::instance(Config::get('template'), Config::get('view_replace_str'));

        $installLockFile = INSTALL_PATH . "install.lock";

        if (is_file($installLockFile)) {
            echo __('The system has been installed. If you need to reinstall, please remove %s first', 'install.lock');
            exit;
        }
        $output = function ($code, $msg, $url = null, $data = null) {
            return json(['code' => $code, 'msg' => $msg, 'url' => $url, 'data' => $data]);
        };

        if ($this->request->isPost()) {
            $mysqlHostname = $this->request->post('mysqlHostname', '127.0.0.1');
            $mysqlHostport = $this->request->post('mysqlHostport', '3306');
            $hostArr = explode(':', $mysqlHostname);
            if (count($hostArr) > 1) {
                $mysqlHostname = $hostArr[0];
                $mysqlHostport = $hostArr[1];
            }
            $mysqlUsername = $this->request->post('mysqlUsername', 'root');
            $mysqlPassword = $this->request->post('mysqlPassword', '');
            $mysqlDatabase = $this->request->post('mysqlDatabase', '');
            $mysqlPrefix = $this->request->post('mysqlPrefix', 'fa_');
            $adminUsername = $this->request->post('adminUsername', 'admin');
            $adminPassword = $this->request->post('adminPassword', '');
            $adminPasswordConfirmation = $this->request->post('adminPasswordConfirmation', '');
            $adminEmail = $this->request->post('adminEmail', 'admin@admin.com');
            $backgroundPath = $this->request->post('backgroundPath', '');
            $siteName = $this->request->post('siteName', __('Peach Blossom Island'));

            if ($adminPassword !== $adminPasswordConfirmation) {
                return $output(0, __('The two passwords you entered did not match'));
            }

            // 填充默认值
            if (empty($adminUsername)) {
                $adminUsername = 'admin';
            }
            if (empty($adminPassword)) {
                $adminPassword = Random::alnum(8);
            }
            if (empty($siteName)) {
                $siteName = __('Peach Blossom Island');
            }

            $adminName = '';
            try {
                $adminName = $this->installation($mysqlHostname, $mysqlHostport, $mysqlDatabase, $mysqlUsername, $mysqlPassword, $mysqlPrefix, $adminUsername, $adminPassword, $adminEmail, $backgroundPath, $siteName);
            } catch (\PDOException $e) {
                return $output(0, $e->getMessage());
            } catch (\Exception $e) {
                return $output(0, $e->getMessage());
            } catch (\Throwable $e) {
                return $output(0, $e->getMessage());
            }
            $data = [
                'domain' => request()->domain(),
                'adminName' => $adminName,
                'adminUsername' => $adminUsername,
                'adminPassword' => $adminPassword
            ];
            sleep(2); // 安装成功后立即点击进入后台可能会出现数据库账号密码错误问题，这里等待两秒钟后返回成功可以解决这个问题
            return $output(1, __('Install Successed'), null, $data);
        } else {
            $errInfo = '';
//            try {
//                $this->checkenv();
//            } catch (\Exception $e) {
//                $errInfo = $e->getMessage();
//            }
            $rundata = $this->getrunenvdata();
            $stand = StandApi::getInstance()->getSystemInstruction();
            \think\Cache::set(ConfigurationHandle::getBasicCacheKeyDefaultTempate(), (isset($stand['template']) ? $stand['template'] : 0), 86400);
            return $this->view->fetch(INSTALL_PATH . "install.html", ['errInfo' => $errInfo, 'rundata' => $rundata, 'stand' => $stand, 'defaultBackgroundPath' => Random::alnum(10), 'backgroundPathReg' => ConfigurationHandle::getBasicBackgroundPathReg()]);
        }
    }

    /**
     * 执行安装
     */
    protected function installation($mysqlHostname, $mysqlHostport, $mysqlDatabase, $mysqlUsername, $mysqlPassword, $mysqlPrefix, $adminUsername, $adminPassword, $adminEmail = null, $backgroundPath = null, $siteName = null)
    {
        $this->checkruneveisok();

        if ($mysqlDatabase == '') {
            throw new Exception(__('Please input correct database'));
        }
        if (!preg_match("/^\w{3,12}$/", $adminUsername)) {
            throw new Exception(__('Please input correct username'));
        }
        if (!preg_match("/^[\S]{6,16}$/", $adminPassword)) {
            throw new Exception(__('Please input correct password'));
        }
        $weakPasswordArr = ['123456', '12345678', '123456789', '654321', '111111', '000000', 'password', 'qwerty', 'abc123', '1qaz2wsx'];
        if (in_array($adminPassword, $weakPasswordArr)) {
            throw new Exception(__('Password is too weak'));
        }
        if ($siteName == '' || preg_match("/fast" . "admin/i", $siteName)) {
            throw new Exception(__('Please input correct website'));
        }
        if (!empty($backgroundPath)) {
            if (in_array($backgroundPath, ['superadmin', 'fastadmin', 'baseadmin', 'masteradmin'])) {
                throw new Exception(__('The background path is too simple, please re -enter or leave a blank default value'));
            }
            if (!preg_match("/" . ConfigurationHandle::getBasicBackgroundPathReg() . "/u", $backgroundPath)) {
                throw new Exception(__('The background path can only be composed of numbers and lowercase letters, and the length is from 7 to 32'));
            }
        }

        $core_sql = file_get_contents(INSTALL_PATH . 'core.sql');

        $core_sql = str_replace("`fa_", "`{$mysqlPrefix}", $core_sql);

        // 先尝试能否自动创建数据库
        $config = Config::get('database');
        try {
            $pdo = new PDO("{$config['type']}:host={$mysqlHostname}" . ($mysqlHostport ? ";port={$mysqlHostport}" : ''), $mysqlUsername, $mysqlPassword);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->query("CREATE DATABASE IF NOT EXISTS `{$mysqlDatabase}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

            // 连接install命令中指定的数据库
            $dbConfig = [
                'type'     => "{$config['type']}",
                'hostname' => "{$mysqlHostname}",
                'hostport' => "{$mysqlHostport}",
                'database' => "{$mysqlDatabase}",
                'username' => "{$mysqlUsername}",
                'password' => "{$mysqlPassword}",
                'prefix'   => "{$mysqlPrefix}",
            ];
            $instance = Db::connect($dbConfig);

            // 查询一次SQL,判断连接是否正常
            $instance->execute("SELECT 1");

            // 调用原生PDO对象进行批量查询
            $instance->getPdo()->exec($core_sql);

            register_shutdown_function(function () use ($instance, $mysqlPrefix, $dbConfig) {
                $domain = $this->request ? $this->request->host(true) : null;
                if (function_exists('fastcgi_finish_request')) {
                    session_write_close();
                    SiteUtil::initSiteData($instance, $mysqlPrefix, $domain, 10800);
                } else {
                    $dbConfig['key'] = Random::alnum(32);
                    \think\Cache::set(ConfigurationHandle::getBasicCacheKeyMysqlConnectData(), json_encode($dbConfig), 300);
                    $url = $this->request->domain() . '/api/site/initsite?key=' . $dbConfig['key'];
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 5);
                    curl_setopt($curl, CURLOPT_TIMEOUT, 5);
                    curl_exec($curl);
                    curl_close($curl);
                }
            });
        } catch (\PDOException $e) {
            throw new Exception($e->getMessage());
        }
        // 后台入口文件
        $adminFile = ROOT_PATH . 'public' . DS . 'admin.php';

        // 数据库配置文件
        $dbConfigFile = APP_PATH . 'database.php';
        $dbConfigText = @file_get_contents($dbConfigFile);
        $callback = function ($matches) use ($mysqlHostname, $mysqlHostport, $mysqlUsername, $mysqlPassword, $mysqlDatabase, $mysqlPrefix) {
            $field = "mysql" . ucfirst($matches[1]);
            $replace = $$field;
            if ($matches[1] == 'hostport' && $mysqlHostport == 3306) {
                $replace = '';
            }
            return "'{$matches[1]}'{$matches[2]}=>{$matches[3]}Env::get('database.{$matches[1]}', '{$replace}'),";
        };
        $dbConfigText = preg_replace_callback("/'(hostname|database|username|password|hostport|prefix)'(\s+)=>(\s+)Env::get\((.*)\)\,/", $callback, $dbConfigText);

        // 检测能否成功写入数据库配置
        $result = @file_put_contents($dbConfigFile, $dbConfigText);
        if (!$result) {
            throw new Exception(__('The current permissions are insufficient to write the file %s', 'application/database.php'));
        }

        // 设置新的Token随机密钥key
        $oldTokenKey = config('token.key');
        $newTokenKey = \fast\Random::alnum(32);
        $coreConfigFile = CONF_PATH . 'config.php';
        $coreConfigText = @file_get_contents($coreConfigFile);
        $coreConfigText = preg_replace("/'key'(\s+)=>(\s+)'{$oldTokenKey}'/", "'key'\$1=>\$2'{$newTokenKey}'", $coreConfigText);

        $result = @file_put_contents($coreConfigFile, $coreConfigText);
        if (!$result) {
            throw new Exception(__('The current permissions are insufficient to write the file %s', 'application/config.php'));
        }

        $avatar = '/assets/img/avatar.png';
        // 变更默认管理员密码
        $adminPassword = $adminPassword ? $adminPassword : Random::alnum(8);
        $adminEmail = $adminEmail ? $adminEmail : "admin@admin.com";
        $newSalt = substr(md5(uniqid(true)), 0, 6);
        $newPassword = md5(md5($adminPassword) . $newSalt);
        $data = ['username' => $adminUsername, 'email' => $adminEmail, 'avatar' => $avatar, 'password' => $newPassword, 'salt' => $newSalt];
        $instance->name('admin')->where('username', 'admin')->update($data);

        // 变更前台默认用户的密码,随机生成
        $newSalt = substr(md5(uniqid(true)), 0, 6);
        $newPassword = md5(md5(Random::alnum(8)) . $newSalt);
        $instance->name('user')->where('username', 'admin')->update(['avatar' => $avatar, 'password' => $newPassword, 'salt' => $newSalt]);

        // 修改后台入口
        $adminName = '';
        if (is_file($adminFile)) {
            $adminName = (empty($backgroundPath) ? Random::alpha(10) : $backgroundPath) . '.php';
            rename($adminFile, ROOT_PATH . 'public' . DS . $adminName);
        }

        //修改站点名称
        if ($siteName != config('site.name')) {
            $instance->name('config')->where('name', 'name')->update(['value' => $siteName]);
            $siteConfigFile = CONF_PATH . 'extra' . DS . 'site.php';
            $siteConfig = include $siteConfigFile;
            $configList = $instance->name("config")->select();
            foreach ($configList as $k => $value) {
                if (in_array($value['type'], ['selects', 'checkbox', 'images', 'files'])) {
                    $value['value'] = is_array($value['value']) ? $value['value'] : explode(',', $value['value']);
                }
                if ($value['type'] == 'array') {
                    $value['value'] = (array)json_decode($value['value'], true);
                }
                $siteConfig[$value['name']] = $value['value'];
            }
            $siteConfig['name'] = $siteName;
            file_put_contents($siteConfigFile, '<?php' . "\n\nreturn " . var_export_short($siteConfig) . ";\n");
        }

        $installLockFile = INSTALL_PATH . "install.lock";
        //检测能否成功写入lock文件
        $result = @file_put_contents($installLockFile, 1);
        if (!$result) {
            throw new Exception(__('The current permissions are insufficient to write the file %s', 'application/admin/command/Install/install.lock'));
        }

        try {
            //删除安装脚本
            @unlink(ROOT_PATH . 'public' . DS . 'install.php');
        } catch (\Exception $e) {

        }

        return $adminName;
    }

    /**
     * 检测环境
     */
    protected function checkenv()
    {
        // 检测目录是否存在
        $checkDirs = [
            'thinkphp',
            'vendor',
            'public' . DS . 'assets' . DS . 'libs'
        ];

        //数据库配置文件
//        $dbConfigFile = APP_PATH . 'database.php';

//        if (version_compare(PHP_VERSION, '7.2.0', '<')) {
//            throw new Exception(__("The current version %s is too low, please use PHP 7.2 or higher", PHP_VERSION));
//        }
        if (!extension_loaded("PDO")) {
            throw new Exception(__("PDO is not currently installed and cannot be installed"));
        }
//        if (!is_really_writable($dbConfigFile)) {
//            throw new Exception(__('The current permissions are insufficient to write the configuration file application/database.php'));
//        }
        foreach ($checkDirs as $k => $v) {
            if (!is_dir(ROOT_PATH . $v)) {
                throw new Exception(__('Please go to the official website to download the full package or resource package and try to install'));
                break;
            }
        }
        return true;
    }

    /**
     * 加载语言文件
     */
    protected function loadlang()
    {
        define('INSTALL_PATH', APP_PATH . 'admin' . DS . 'command' . DS . 'Install' . DS);

        $this->request = Request::instance();
        $lang = $this->request->langset();
        $lang = preg_match("/^([a-zA-Z\-_]{2,10})\$/i", $lang) ? $lang : 'zh-cn';
        if (!$lang || in_array($lang, ['zh-cn', 'zh-hans-cn'])) {
            Lang::load(INSTALL_PATH . 'zh-cn.php');
        }
    }

    /**
     * 获取运行环境数据
     * @return array
     */
    protected function getrunenvdata()
    {
        $data_map = [];
        // 检测环境
        $php_version_data = ['name' => __('PHP version'), 'current' => PHP_VERSION, 'success' => true];
        if (IS_WIN) {
            $version = '8.0.0';
            $php_version_data['required'] = __('PHP 8.0 version');
        } else {
            $version = '7.4.0';
            $php_version_data['required'] = __('PHP 7.4 version');
        }
        if (version_compare(PHP_VERSION, $version, '<')) {
            $php_version_data['success'] = false;
        }
        $data_map['runenv'] = [$php_version_data];
        $data_map['php_version'] = $php_version_data['success'];
        // 目录权限检测
        $path_dirs = [
            'public',
            'runtime',
            'application' . DS . 'config.php',
            'application' . DS . 'database.php',
            'application' . DS . 'extra' . DS . 'site.php',
            'application' . DS . 'admin' . DS . 'command' . DS . 'Install',
        ];
        foreach ($path_dirs as $path) {
            if (is_really_writable(ROOT_PATH . $path)) {
                $current = __('Read and write');
                $success = true;
            } else {
                $success = false;
                $current = __('Read only');
            }
            $data_map['pathenv'][] = ['path' => $path, 'required' => __('Read and write'), 'current' => $current, 'success' => $success];
        }
        // 扩展是否安装
        $extensions = ['redis', 'fileinfo'];
        if (!IS_WIN) {
            $extensions = array_merge($extensions, ['gmp']);
        }
        foreach ($extensions as $extension) {
            if (extension_loaded($extension)) {
                $current = __('Installed');
                $success = true;
            } else {
                $success = false;
                $current = __('Not Installed');
            }
            $data_map['extensionenv'][] = ['name' => $extension, 'current' => $current, 'success' => $success];
        }
        // 禁用函数是否删除
        $functions = ['putenv'];
        if (!IS_WIN) {
            $functions = array_merge($functions, ['pcntl_fork', 'pcntl_wait', 'pcntl_signal', 'pcntl_signal_dispatch']);
        }
        foreach ($functions as $function) {
            if (function_exists($function)) {
                $current = __('Deleted');
                $success = true;
            } else {
                $current = __('Not deleted');
                $success = false;
            }
            $data_map['functionenv'][] = ['name' => $function, 'current' => $current, 'success' => $success];
        }
        // 返回检查结果
        return $data_map;
    }

    /**
     * 检测运行环境是否OK
     * @throws Exception
     */
    protected function checkruneveisok()
    {
        $this->checkenv();
        $rundata = $this->getrunenvdata();
        $runenv = $rundata['runenv'];
        foreach ($runenv as $item) {
            if (!$item['success']) {
                throw new Exception(__('PHP operating environment detection cannot be passed'));
            }
        }
        $pathenv = $rundata['pathenv'];
        foreach ($pathenv as $item) {
            if (!$item['success']) {
                throw new Exception(__('%s Insufficient directory permissions', $item['path']));
            }
        }
        $extensionenv = $rundata['extensionenv'];
        foreach ($extensionenv as $item) {
            if (!$item['success']) {
                throw new Exception(__('%s Extension and not installed', $item['name']));
            }
        }
        $functionenv = $rundata['functionenv'];
        foreach ($functionenv as $item) {
            if (!$item['success']) {
                throw new Exception(__('%s The disable function is not deleted', $item['name']));
            }
        }
    }

    /**
     * 检查运行环境API
     * @return \think\response\Json
     */
    public function checkrunenv()
    {
        $errInfo = '';
        $this->loadlang();
        try {
            $this->checkruneveisok();
        } catch (\Throwable $ex) {
            $errInfo = $ex->getMessage();
        }
        $result = [
            'code' => empty($errInfo) ? 1 : 0,
            'msg' => $errInfo,
            'time' => time(),
            'data' => null
        ];
        return json($result);
    }

    /**
     * 测试数据库连接状态
     * @return \think\response\Json
     */
    public function testdatabase()
    {
        $errInfo = '';
        $this->loadlang();
        if ($this->request->isPost()) {
            try {
                $config = Config::get('database');
                $mysqlHostname = $this->request->post('mysqlHostname', '127.0.0.1');
                $mysqlHostport = $this->request->post('mysqlHostport', '3306');
                $hostArr = explode(':', $mysqlHostname);
                if (count($hostArr) > 1) {
                    $mysqlHostname = $hostArr[0];
                    $mysqlHostport = $hostArr[1];
                }
                $mysqlUsername = $this->request->post('mysqlUsername', 'root');
                $mysqlPassword = $this->request->post('mysqlPassword', '');
                $pdo = new PDO("{$config['type']}:host={$mysqlHostname}" . ($mysqlHostport ? ";port={$mysqlHostport}" : ''), $mysqlUsername, $mysqlPassword);
                $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                $pdo->query("SELECT 1");
            } catch (\Throwable $ex) {
                $errInfo = $ex->getMessage();
            }
        } else {
            $errInfo = __('Illegal request');
        }
        $result = [
            'code' => empty($errInfo) ? 1 : 0,
            'msg' => $errInfo,
            'time' => time(),
            'data' => null
        ];
        return json($result);
    }

}
