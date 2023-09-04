<?php

namespace app\common\command;

use app\common\library\VideoSync;
use think\console\Command;
use think\console\Input;
use think\console\Output;

class VideoSyncCommand extends Command
{

    protected function configure()
    {
        $this->setName('video:sync')
            ->setDescription('Video Sync');
    }

    /**
     * 同步片源
     * @param Input $input
     * @param Output $output
     * @return int|void|null
     */
    protected function execute(Input $input, Output $output)
    {
        $time = time();
        $output->writeln('开始同步片源信息');
        try {
            $totla = VideoSync::allSync(0);
            if (is_numeric($totla)) {
                $second = time() - $time;
                $message = "片源同步完成，共同步片源: {$totla}，共耗时: {$second}秒";
            } else {
                $message = "片源同步进行中，请稍后再试";
            }
            $output->writeln($message);
        } catch (\Exception $ex) {
            $output->writeln('片源同步失败: ' . $ex->getMessage());
            $output->writeln($ex->getTraceAsString());
        }
    }

}