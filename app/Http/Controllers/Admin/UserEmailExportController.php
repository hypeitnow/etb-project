<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class UserEmailExportController extends Controller
{
    public function __invoke(Request $request): StreamedResponse
    {
        $role = in_array($request->query('user_role'), User::roles(), true)
            ? (string) $request->query('user_role')
            : 'all';
        $marketingConsent = in_array($request->query('marketing_consent'), ['yes', 'no'], true)
            ? (string) $request->query('marketing_consent')
            : 'all';

        $fileName = 'etb-emails-'.now()->format('Y-m-d-His').'.csv';

        return response()->streamDownload(function () use ($role, $marketingConsent): void {
            $output = fopen('php://output', 'w');

            fputcsv($output, ['email', 'name', 'role', 'marketing_email_consent']);

            User::query()
                ->with('fanProfile')
                ->when($role !== 'all', fn ($query) => $query->where('role', $role))
                ->when($marketingConsent !== 'all', function ($query) use ($marketingConsent): void {
                    $query->whereHas('fanProfile', fn ($profileQuery) => $profileQuery->where('marketing_email_consent', $marketingConsent === 'yes'));
                })
                ->orderBy('email')
                ->chunk(200, function ($users) use ($output): void {
                    foreach ($users as $user) {
                        fputcsv($output, [
                            $user->email,
                            $user->name,
                            $user->role,
                            $user->fanProfile?->marketing_email_consent ? 'yes' : 'no',
                        ]);
                    }
                });

            fclose($output);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }
}
