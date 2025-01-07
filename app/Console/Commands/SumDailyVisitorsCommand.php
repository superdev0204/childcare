<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Visitors;
use App\Models\Visitorsummary;
use Carbon\Carbon;

class SumDailyVisitorsCommand extends Command
{
    protected $signature = 'custom:sum-daily-visitors-data';

    protected $description = 'Get the information of the day\'s visitors at 00:01:';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $today = Carbon::now()->format('Y-m-d');
        
        $visitors = Visitors::where('date', '<', $today)->get();

        if ($visitors->isNotEmpty()) {
            $this->info('Information of the day\'s visitors');
            foreach ($visitors as $visitor) {
                $month = explode('-', $visitor->date)[0] . '-' . explode('-', $visitor->date)[1];
                $visitorsummary = Visitorsummary::where('date', 'like', $month . '%')
                                                ->where('page_url', $visitor->page_url)
                                                ->first();

                if($visitorsummary){
                    $visitorsummary->visitor_count = $visitorsummary->visitor_count + $visitor->visitor_count;
                    $visitorsummary->save();
                }
                else{
                    Visitorsummary::create([
                        'page_url' => $visitor->page_url,
                        'date' => $month . '-01',
                        'visitor_count' => $visitor->visitor_count
                    ]);
                }

                // Delete visitors before 7 days
                $visitor->delete();
            }
        } else {
            $this->info('No visitors found for yesterday');
        }
    }
}