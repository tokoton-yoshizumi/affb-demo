<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class CommissionEarned extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $commission;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $commission
     */
    public function __construct($user, $commission)
    {
        $this->user = $user;
        $this->commission = $commission;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->markdown('emails.commission_earned')
            ->subject('アフィリエイト報酬獲得のお知らせ')
            ->with([
                'userName' => $this->user->name,
                'commissionAmount' => $this->commission,
            ]);
    }
}
