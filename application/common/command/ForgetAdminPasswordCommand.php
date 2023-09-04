<?php


namespace app\common\command;


use fast\Random;
use think\console\Command;
use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\Db;

class ForgetAdminPasswordCommand extends Command
{

    protected function configure()
    {
        $this->setName('forget:admin:password')
            ->addOption('username', 'u', Option::VALUE_OPTIONAL, 'username', '')
            ->setDescription('Reset the super administrator account password');
    }

    /**
     * 重置密码
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    protected function execute(Input $input, Output $output)
    {
        $username = $input->getOption('username');
        if (empty($username)) {
            $group = Db::name('auth_group')->where('pid', 0)->where('rules', '*')->find();
            if (empty($group)) {
                throw new \RuntimeException("找不到默认用户，请使用参数 -u[您的用户名]");
            }
            $access = Db::name('auth_group_access')->where('group_id', $group['id'])->find();
            if (empty($access)) {
                throw new \RuntimeException("找不到默认用户，请使用参数 -u[您的用户名]");
            }
            $admin_id = $access['uid'];
        } else {
            $admin = Db::name('admin')->where('username', $username)->find();
            if (empty($admin)) {
                throw new \RuntimeException("用户[$username]不存在，请确认");
            }
            $admin_id = $admin['id'];
        }
        $salt = Random::alnum();
        $password = Random::alnum(12);
        $admin = Db::name('admin')->find($admin_id);
        Db::name('admin')->where('id', $admin_id)->setField([
            'salt' => $salt,
            'password' => md5(md5($password) . $salt)
        ]);
        $output->highlight("账号: {$admin['username']}");
        $output->highlight("密码: {$password}");
    }

}