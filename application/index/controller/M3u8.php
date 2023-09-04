<?php


namespace app\index\controller;


use app\common\controller\Frontend;
use app\common\library\VideoUtil;
use app\common\model\SyncOrigins;
use app\common\model\SyncVideos;
use app\common\model\WebCategorys;
use app\common\model\WebVideoPurchase;
use Chrisyue\PhpM3u8\Facade\DumperFacade;
use Chrisyue\PhpM3u8\Facade\ParserFacade;
use Chrisyue\PhpM3u8\Stream\TextStream;
use think\exception\HttpResponseException;
use think\Request;
use think\Response;

class M3u8 extends Frontend
{

    public function __construct(Request $request = null)
    {
        parent::__construct($request);
    }

    protected $noNeedLogin = ['*'];
    protected $oneMinuteTsNumber = 20;

    /**
     * 处理M3U8文件
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Redirect|\think\response\View|\think\response\Xml
     * @throws \think\Exception
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function video()
    {
        $vid = $this->getUrlPathParams(0);
        $vid = substr($vid, 0, strpos($vid, '.'));
        if (empty($vid) || empty($video = SyncVideos::findSyncVideosByVid($vid))) {
            $this->redirect('/');
        }
        SyncVideos::plusOne($video['id'], 'views');
        $origin = SyncOrigins::findSyncOriginsByOriginIdCache($video['origin_id']);
        // 这个是视频m3u8文件列表信息
        $index_m3u8_url = $origin['video_url'] . $video['m3u8_url'];
        $index_contents = file_get_contents($index_m3u8_url, false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        $parser = new ParserFacade();
        $index_m3u8 = $parser->parse(new TextStream($index_contents));
        // 这个是视频m3u8文件信息
        $video_m3u8_url = $origin['video_url'] . $index_m3u8['EXT-X-STREAM-INF'][0]['uri'];
        $video_contents = file_get_contents($video_m3u8_url, false, stream_context_create([
            'ssl' => [
                'verify_peer' => false,
                'verify_peer_name' => false
            ]
        ]));
        // 验证用户是否可以完整观看视频
        $handle_m3u8 = true;
        $video = VideoUtil::getInstance()->processingVideo($video);
        if ($video['categorieModel'] == WebCategorys::MODE_FREE) {
            // 视频所属分类为免费
            $handle_m3u8 = false;
        } else {
            // 验证用户是否已登录
            if ($this->auth->isLogin()) {
                $user = $this->auth->getUser();
                if ($video['categorieModel'] == WebCategorys::MODE_VIP) {
                    // 视频所属分类为VIP
                    $handle_m3u8 = !$user->vip_mark;
                } else if ($video['categorieModel'] == WebCategorys::MODE_REFLECT) {
                    // 视频所属分类为点映
                    $handle_m3u8 = empty(WebVideoPurchase::findWebVideoPurchaseByUserId($user->id, $video['vid']));
                }
            }
        }
        // 处理m3u8文件
        $parser = new ParserFacade();
        $video_m3u8 = $parser->parse(new TextStream($video_contents));
        $first_segment = $video_m3u8['mediaSegments'][0];
        $first_segment['EXT-X-KEY'][0]['URI'] = $origin['video_url'] . $first_segment['EXT-X-KEY'][0]['URI'];
        foreach ($video_m3u8['mediaSegments'] as $index => $media_segment) {
            $media_segment['uri'] = $origin['video_url'] . $media_segment['uri'];
            // 超过的部分unset掉
            if ($handle_m3u8 && $index >= $this->oneMinuteTsNumber) {
                unset($video_m3u8['mediaSegments'][$index]);
            }
        }
        $text = new TextStream();
        $dumper = new DumperFacade();
        $dumper->dump($video_m3u8, $text);
        $headers = ['Content-Type' => 'application/vnd.apple.mpegurl'];
        return Response::create($text, $this->getResponseType())->header($headers);
    }

}