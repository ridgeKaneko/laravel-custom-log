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

    protected function configurationFor($name)
    {
        $config = parent::configurationFor($name);

        if(!array_key_exists('name',$config)) {
            $config['name'] = $name;
        }

        return $config;
    }
}
