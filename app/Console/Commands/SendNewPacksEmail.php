<?php

namespace App\Console\Commands;
use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Pack;
use App\Mail\NewPackNotification;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

class SendNewPacksEmail extends Command
{
    protected $signature = 'email:send-new-packs';
    protected $description = 'Отправить уведомления о новых паках';

    public function handle()
    {
        $newPacks = Pack::where('created_at', '>=', Carbon::now()->subDay())
            ->where('status', 'posted')
            ->get();
            
        if ($newPacks->isEmpty()) {
            $this->info('Новых паков за сутки нет.');
            return;
        }
        
        $users = User::where('subscribed', true)->get();
        foreach ($users as $user) {
            foreach ($newPacks as $pack) {
                Mail::to($user->email)->send(new NewPackNotification($pack));
            }
        }
        $this->info('Уведомления отправлены!');
    }
}