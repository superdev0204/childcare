<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\DB;
use App\Models\States;
use App\Models\Facility;
use App\Models\Counties;
use App\Models\Cities;
use App\Models\Zipcodes;

class GenerateSitemapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'custom:generate-sitemap';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate XML sitemap';

    const MAX_LINK_COUNT = 50000;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->generateMainSitemap();
        $this->generateCitySitemap();
        $this->generateZipcenterSitemap();
        $this->generateZiphomeSitemap();
        $this->generateProviderSitemap();
        
        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('sitemapindex');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        foreach (File::glob(public_path('sitemap_*.xml')) as $filename) {
            if (basename($filename) == 'sitemap_index.xml') {
                continue;
            }
        
            $xml->startElement('sitemap');
            $xml->writeElement('loc', 'https://' . env('DOMAIN') . '/' . basename($filename));
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        $content = $xml->outputMemory();

        $filename = 'sitemap_index.xml';
        file_put_contents(public_path() . '/' . $filename, $content);

        $this->info(sprintf('%s has been generated.', $filename));
    }

    private function generateMainSitemap()
    {
        $linkCount = 0;

        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        
        $uris = [
            '/', '/state', '/homecare', '/resources/', '/classifieds',
            '/about', '/contact', '/feedback', '/wesupport', '/faqs',
            '/privacy'
        ];

        foreach ($uris as $uri) {
            $linkCount++;

            $xml->startElement('url');
            $xml->writeElement('loc', 'https://' . env('DOMAIN') . $uri);
            $xml->endElement();
        }

        $states = States::where('center_count', '>', 0)
                        ->orWhere('homebase_count', '>', 0)
                        ->get();

        foreach ($states as $state) {
            if ($state->center_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/state/' . $state->statefile;

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }

            if ($state->homebase_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/' . $state->statefile . '_homecare';

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }
        }

        $counties = Counties::where('center_count', '>', 0)
                            ->orWhere('homebase_count', '>', 0)
                            ->get();

        foreach ($counties as $county) {
            if ($county->center_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/county/' . $county->county_file;

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }

            if ($county->homebase_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/' . $county->statefile . '_homecare/' . $county->county_file . '_county';

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }
        }

        $xml->endElement();
        $xml->endDocument();

        $content = $xml->outputMemory();

        $filename = 'sitemap_main.xml';
        file_put_contents(public_path() . '/'. $filename , $content);

        $this->info(sprintf('%s has been generated. Link count = %s', $filename, $linkCount));
    }

    private function generateCitySitemap()
    {
        $linkCount = 0;

        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');

        $cities = Cities::where('filename', '<>', '')
                        ->where(function ($query) {
                            $query->where('center_count', '>', 0)
                                ->orWhere('homebase_count', '>', 0);
                        })
                        ->get();
        
        foreach ($cities as $city) {
            if ($city->center_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/' . $city->statefile . '/' . $city->filename . '_childcare';

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }

            if ($city->homebase_count > 0) {
                $linkCount++;

                $xml->startElement('url');
                $location = 'https://' . env('DOMAIN') . '/' . $city->statefile . '_homecare/' . $city->filename . '_city';

                $xml->writeElement('loc', $location);
                $xml->endElement();
            }
        }

        $xml->endElement();
        $xml->endDocument();

        $content = $xml->outputMemory();

        $filename = 'sitemap_city.xml';
        file_put_contents(public_path() . '/'. $filename , $content);

        $this->info(sprintf('%s has been generated. Link count = %s', $filename, $linkCount));
    }

    private function generateZipcenterSitemap()
    {
        $linkCount = 0;

        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        
        $zipcodes = DB::table('zipcodes as z')
            ->select('z.statefile', 'z.zipcode')
            ->join('states as s', function ($join) {
                $join->on('z.state', '=', 's.state_code')
                    ->where('s.country', '=', 'US');
            })
            ->where('z.center_count', '>', 0)
            ->get();

        foreach ($zipcodes as $zipcode) {
            $linkCount++;

            $xml->startElement('url');
            $location = 'https://' . env('DOMAIN') . '/' . $zipcode->statefile . '/' . $zipcode->zipcode . '_childcare';

            $xml->writeElement('loc', $location);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        $content = $xml->outputMemory();

        $filename = 'sitemap_zipcenter.xml';
        file_put_contents(public_path() . '/'. $filename , $content);

        $this->info(sprintf('%s has been generated. Link count = %s', $filename, $linkCount));
    }

    private function generateZiphomeSitemap()
    {
        $linkCount = 0;

        $xml = new \XMLWriter();

        $xml->openMemory();
        $xml->startDocument('1.0', 'UTF-8');
        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        
        $zipcodes = DB::table('zipcodes as z')
            ->select('z.statefile', 'z.zipcode')
            ->join('states as s', function ($join) {
                $join->on('z.state', '=', 's.state_code')
                    ->where('s.country', '=', 'US');
            })
            ->where('z.homebase_count', '>', 0)
            ->get();
        
        foreach ($zipcodes as $zipcode) {
            $linkCount++;

            $xml->startElement('url');
            $location = 'https://' . env('DOMAIN') . '/' . $zipcode->statefile . '_homecare/' . $zipcode->zipcode . '_zipcode';

            $xml->writeElement('loc', $location);
            $xml->endElement();
        }

        $xml->endElement();
        $xml->endDocument();

        $content = $xml->outputMemory();

        $filename = 'sitemap_ziphome.xml';
        file_put_contents(public_path() . '/'. $filename , $content);

        $this->info(sprintf('%s has been generated. Link count = %s', $filename, $linkCount));
    }

    private function generateProviderSitemap()
    {
        $providerCount = Facility::where('approved', '>', 0)->count();
        
        $totalLinkCount = 0;
        $maxLinkCount = $providerCount > self::MAX_LINK_COUNT ? self::MAX_LINK_COUNT : $providerCount;

        for ($i = 0; $i < ceil($providerCount / $maxLinkCount); $i++) {
            $linkCount = 0;

            $xml = new \XMLWriter();

            $xml->openMemory();
            $xml->startDocument('1.0', 'UTF-8');
            $xml->startElement('urlset');
            $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
            
            $offset = 0;
            $batchSize = 1000;

            while ($offset < $maxLinkCount) {
                $providers = Facility::where('approved', '>', 0)
                                    ->offset($offset + $i * $maxLinkCount)
                                    ->limit($batchSize)
                                    ->get();

                if (!count($providers)) {
                    break;
                }

                foreach ($providers as $provider) {
                    $linkCount++;

                    $xml->startElement('url');
                    $location = 'https://' . env('DOMAIN') . '/provider_detail/' . $provider->filename;

                    $xml->writeElement('loc', $location);
                    $xml->endElement();
                }

                $offset += $batchSize;
            }

            $xml->endElement();
            $xml->endDocument();

            $content = $xml->outputMemory();

            $filename = 'sitemap_provider' . ($i + 1) . '.xml';
            file_put_contents(public_path() . '/'. $filename , $content);

            $this->info(sprintf('%s has been generated. Link count = %s', $filename, $linkCount));

            $totalLinkCount += $linkCount;
        }

        $this->info('Provider sitemap files have been generated. Total link count = ' . $totalLinkCount);
    }
}
