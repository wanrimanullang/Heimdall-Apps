<?php namespace App\SupportedApps\Nzbget;

class Nzbget extends \App\SupportedApps implements \App\EnhancedApps {

    public $config;

    public function test()
    {
        return parent::appTest($this->apiUrl('status'));
    }
   
    public function livestats()
    {
        $status = 'inactive';
        $res = parent::execute($this->apiUrl('status'));
        $details = json_decode($res->getBody());

        if($details) {
            $size = $details->result->RemainingSizeMB;
            $rate = $details->result->DownloadRate;
            $data['queue_size'] = format_bytes($size*1000*1000, false, ' <span>', '</span>');
            $data['current_speed'] = format_bytes($rate, false, ' <span>');
            $status = ($size > 0 || $rate > 0) ? 'active' : 'inactive';
        }

        return parent::getLiveStats($status, $data);
        
    }

    public function apiUrl($endpoint)
    {
        $config = $this->config;
        $url = $config->url;
        $username = $config->username;
        $password = $config->password;
        $rebuild_url = str_replace('http://', 'http://'.$username.':'.$password.'@', $url);
        $rebuild_url = str_replace('https://', 'https://'.$username.':'.$password.'@', $rebuild_url);
        $rebuild_url = rtrim($rebuild_url, '/');
        $api_url = $rebuild_url.'/jsonrpc/'.$endpoint;
        return $api_url;
    }
}
