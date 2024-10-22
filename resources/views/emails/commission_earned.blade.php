@component('mail::message')
# {{ $userName }} 様

おめでとうございます！新しいアフィリエイト報酬を獲得しました。

**獲得したアフィリエイト報酬金額**: ¥{{ number_format($commissionAmount, 0) }}

引き続き、今後のご活躍を期待しております。

@component('mail::button', ['url' => route('login')])
ログインして詳細を見る
@endcomponent

今後ともよろしくお願い申し上げます。

{{ config('app.name') }}
@endcomponent