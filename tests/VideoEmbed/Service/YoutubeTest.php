<?php
namespace Grav\Plugin\VideoEmbed\Tests\VideoEmbed\Service;

class YoutubeTest extends AbstractServiceTest
{
    public function getPluginName()
    {
        return 'Youtube';
    }

    public function getVideoUrl($videoId, $urlQuery = null)
    {
        return '//youtube.com/embed/'.$videoId;
    }

    public function createMatchesArray($videoId, $urlQuery = null)
    {
        return [
            7 => $videoId,
            11 => $urlQuery
        ];
    }
}
