<?php


namespace CustomLog;


class ChannelConfig
{
    public $name;
    public $level;
    public $days;
    public $path;

    public static function fromArray($array) : self
    {
        $config = new self();
        $config->level = $array['level'] ?? null;
        $config->days = $array['days'] ?? null;
        $config->path = $array['path'] ?? null;
        $config->name = $array['name'] ?? null;

        return $config;
    }
}
