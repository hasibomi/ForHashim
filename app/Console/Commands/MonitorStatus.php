<?php

namespace App\Console\Commands;

use App\Event;
use App\Record;
use Illuminate\Console\Command;
use App\Libraries\UptimeRobot;
use Illuminate\Console\Scheduling\Schedule;

class MonitorStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'monitor:status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Read the status of the monitor ID';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @param Schedule $schedule
     * @param Event $event
     * @param Record $record
     */
    public function handle(Schedule $schedule, Event $event, Record $record)
    {
        $uptimeRobot = new UptimeRobot(env('UPTIMEROBOT_API'));
        $getMonitors = simplexml_load_string($uptimeRobot->getMonitors());

        foreach($getMonitors->monitor as $monitor)
        {
            if($monitor['status'] == 9)
            {
                // Record the restart
                $event->create([
                    'machine_name' => 'ubuntu-512mb-sgp1-01',
                    'event' => 'restart'
                ]);


                // Check for the restart signal.
                $check = $event->where('event', 'restart');
                // Check for the record of recent restart
                $recentRestart = $record->where('event', 'restart');

                // If there is a recent record but no signal found
                if($recentRestart->count() > 0 && $check->count() < 6)
                {
                    \Mail::send('Email.Down', [], function($message)
                    {
                        $message->to('hashimalhadad@gmail.com', 'Hashim Ibrahim')->subject('About your server');
                    });
                }
                else
                {
                    if($schedule->exec('envoy run monitorStatus'))
                    {
                        \Log::info('Envoy ran @ ' . \Carbon\Carbon::now());
                        \Mail::send('Email.Server', ['time' => \Carbon\Carbon::now()], function($message)
                        {
                            $message->to('hashimalhadad@gmail.com', 'Hashim Ibrahim')->subject('About your server');
                        });

                        // Save this restart to db
                        $record->create(['event' => 'restart']);
                    }
                    else
                    {
                        \Log::info('Envoy is not working @ ' . \Carbon\Carbon::now());
                    }

                    $check->delete();
                }
            }
            else
            {
                \Log::info('Monitor status is good @ ' . \Carbon\Carbon::now());
            }
        }
    }
}
