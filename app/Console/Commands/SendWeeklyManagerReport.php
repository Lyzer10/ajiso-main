<?php

namespace App\Console\Commands;

use App\Models\Beneficiary;
use App\Models\Dispute;
use App\Models\DisputeStatus;
use App\Models\User;
use App\Services\SmsService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class SendWeeklyManagerReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reports:weekly-managers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send weekly organization summary SMS to managers.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if (env('SEND_NOTIFICATIONS') != true) {
            return Command::SUCCESS;
        }

        $to = Carbon::now();
        $from = $to->copy()->subDays(7);

        $periodLabel = $from->format('d/m/Y') . ' - ' . $to->format('d/m/Y');

        $totalCases = Dispute::count();
        $newCases = Dispute::whereBetween('reported_on', [$from->format('Y-m-d'), $to->format('Y-m-d')])->count();
        $totalBeneficiaries = Beneficiary::count();
        $newBeneficiaries = Beneficiary::whereBetween('created_at', [$from->format('Y-m-d'), $to->format('Y-m-d')])->count();

        $statusCounts = Dispute::select('dispute_status_id')
            ->whereBetween('reported_on', [$from->format('Y-m-d'), $to->format('Y-m-d')])
            ->get()
            ->groupBy('dispute_status_id')
            ->map->count();

        $statuses = DisputeStatus::get(['id', 'dispute_status']);
        $statusSummary = [];
        foreach ($statuses as $status) {
            $statusSummary[] = $status->dispute_status . ': ' . ($statusCounts->get($status->id, 0));
        }

        $message = 'AJISO Weekly Report (' . $periodLabel . '): ' .
            'New cases: ' . $newCases . ', Total cases: ' . $totalCases . ', ' .
            'New beneficiaries: ' . $newBeneficiaries . ', Total beneficiaries: ' . $totalBeneficiaries . '. ' .
            'Status (7 days): ' . implode(', ', $statusSummary) . '.';

        $managers = User::whereHas('role', function ($q) {
                $q->whereIn('role_abbreviation', ['superadmin', 'admin']);
            })
            ->select(['id', 'tel_no'])
            ->get();

        $sms = new SmsService();

        foreach ($managers as $manager) {
            $dest = SmsService::normalizeRecipient($manager->tel_no);
            if (!$dest) {
                continue;
            }
            $sms->sendSMS(['recipient_id' => $manager->id, 'dest_addr' => $dest], $message);
        }

        return Command::SUCCESS;
    }
}
