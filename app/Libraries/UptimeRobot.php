<?php namespace App\Libraries;

class UptimeRobot
{
    /**
     * @var bool
     */
    private $format;

    /**
     * @var
     */
    private $api;

    /**
     * UptimeRobot constructor.
     *
     * @param $api
     * @param bool $format
     */
    public function __construct($api, $format = false)
    {
        $this->api = $api;

        if($format == false)
        {
            $this->format = 'xml';
        }
        else
        {
            $this->format = $format;
        }
    }

    private function client($method, $type = 'get')
    {
        $url = 'https://api.uptimerobot.com/' . $method . '?apiKey='. $this->api .'&format=' . $this->format;
        $curl = curl_init();

        curl_setopt($curl,CURLOPT_URL,$url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_HEADER, false);

        $output = curl_exec($curl);

        curl_close($curl);
        return $output;
    }

    public function getMonitors()
    {
        return $this->client('getMonitors');
    }
}