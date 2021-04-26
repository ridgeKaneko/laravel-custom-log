<?php


namespace CustomLog;


use Illuminate\Log\LogManager;

class CustomLogManager extends LogManager
{
    /**
     * @var string 固定チャンネル名
     */
    protected $pinnedChannel;

    /**
     * チャンネルを固定
     *
     * @param string $channel
     */
    public function pinChannel(string $channel)
    {
        $this->pinnedChannel = $channel;
    }

    public function driver($driver = null)
    {
        return $this->get(($driver ?? $this->pinnedChannel) ?? $this->getDefaultDriver());
    }
}
