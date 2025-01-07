<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckFacilityWebsiteCommand extends Command
{
    protected $signature = 'custom:check-facility-website {--limit= : Limit number of records} {--states= : Comma-separated list of states}';
    protected $description = 'Check facility website URLs';

    public function handle()
    {
        $query = DB::table('facility')
            ->select('id', 'filename', 'website')
            ->where('approved', '>', 0)
            ->where('website', '<>', '');

        if ($this->option('limit')) {
            $query->limit($this->option('limit'));
        }

        if ($states = $this->option('states')) {
            $states = array_map('trim', explode(',', $states));
            $query->whereIn('state', $states);
        }

        $rows = $query->get();

        $filename = base_path('datafiles/broken-links/invalid-websites.csv');
        $file = fopen($filename, 'w');
        fwrite($file, join(";", ['Filename', 'Website', 'Status Code', 'Redirect URL']) . "\n");

        if ($rows->isEmpty()) {
            $this->info('Nothing Found');
            return;
        }

        $result = [];

        foreach ($rows as $row) {
            $url = $row->website;
            $filename = $row->filename;

            if (!filter_var($url, FILTER_VALIDATE_EMAIL)) {
                continue;
            }

            $httpCode = $result[$url]['httpCode'] ?? null;
            $redirectUrl = $result[$url]['redirectUrl'] ?? null;

            if (!isset($httpCode)) {
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.7.5) Gecko/20041107 Firefox/1.0");
                curl_setopt($ch, CURLOPT_HEADER, true);
                curl_setopt($ch, CURLOPT_NOBODY, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
                curl_setopt($ch, CURLOPT_TIMEOUT, 120);
                $response = curl_exec($ch);
                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $redirectUrl = curl_getinfo($ch, CURLINFO_REDIRECT_URL);

                if ($httpCode == 0) {
                    $this->info(curl_error($ch));
                }

                curl_close($ch);

                $result[$url]['httpCode'] = $httpCode;
                $result[$url]['redirectUrl'] = $redirectUrl;
            }

            $this->info('Filename: ' . $filename . '; URL: ' . $url . '; Status Code: ' . $httpCode);

            if ($httpCode == 200) {
                continue;
            }

            if ($httpCode == 301 || $httpCode == 302) {
                $strippedUrl = str_replace(['http://', 'https://', 'www.'], '', trim($url, '/'));
                $strippedRedirectUrl = str_replace(['http://', 'https://', 'www.'], '', trim($redirectUrl, '/'));

                if ($strippedUrl == $strippedRedirectUrl) {
                    $this->info(sprintf('Facility ID = "%s". Old website = "%s". New website = "%s"', $row->id, $url, $redirectUrl));
                    DB::table('facility')->where('id', $row->id)->update(['website' => $redirectUrl]);

                    continue;
                }
            }

            fwrite($file, join(";", [$filename, $url, $httpCode, $redirectUrl]) . "\n");
        }

        fclose($file);
    }
}
