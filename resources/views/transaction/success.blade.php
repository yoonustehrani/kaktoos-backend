<x-layouts.html :title="__('Transaction successfull')">
    <x-slot:head>
        @vite('resources/css/app.css')
    </x-slot:head>
    <main class="bg-gray-900 w-full h-full flex justify-center items-center font-vazir px-3">
        <div class="w-auto min-h-[100px] px-6 p-3 bg-gray-800 text-gray-200 shadow-md rounded-lg flex flex-col items-center">
            <svg width="50" height="50" viewBox="0 0 17 17" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="fill-emerald-700" d="M17 8.5C17 3.83871 13.1613 0 8.5 0C3.80444 0 0 3.83871 0 8.5C0 13.1956 3.80444 17 8.5 17C13.1613 17 17 13.1956 17 8.5ZM7.50605 13.0242C7.3004 13.2298 6.92339 13.2298 6.71774 13.0242L3.15323 9.45968C2.94758 9.25403 2.94758 8.87702 3.15323 8.67137L3.94153 7.91734C4.14718 7.67742 4.48992 7.67742 4.69556 7.91734L7.12903 10.3165L12.2702 5.1754C12.4758 4.93548 12.8185 4.93548 13.0242 5.1754L13.8125 5.92944C14.0181 6.13508 14.0181 6.5121 13.8125 6.71774L7.50605 13.0242Z"/>
            </svg>
            <h1 class="font-bold text-3xl text-emerald-700 my-3">تراکنش موفق</h1>
            <div class="mt-4 grid grid-cols-2 text-wrap break-words gap-3 text-gray-300 text-lg">
                <p>مبلغ</p>
                <p>{{ number_format($trx->amount) }} {{ \App\Payment\IranianCurrency::tryFrom($request->currency)?->getDisplayFa() }}</p>
                <p>وضعیت تایید تراکنش</p>
                <p>{{ $verification_status->getDisplayFa() }}</p>
                <p>تاریخ و ساعت پرداخت</p>
                <p class="text-right" dir="ltr">{{ \Morilog\Jalali\Jalalian::fromCarbon($trx->paid_at) }}</p>
                <p>تاریخ و ساعت تایید تراکنش</p>
                <p class="text-right" dir="ltr">{{ \Morilog\Jalali\Jalalian::fromCarbon($trx->verified_at) }}</p>
                <p>شناسه تراکنش درگاه</p>
                <p>{{ $request->purchaseId }}</p>
                <p>شناسه مرجع درگاه</p>
                <p>{{ $trx->meta->psp_ref }}</p>
                <p>کد رهگیری درگاه (RRN)</p>
                <p>{{ $trx->meta->psp_rrn }}</p>
                <p>آدرس IP کاربر</p>
                <p>{{ $trx->payer_ip }}</p>
                <p>از شماره کارت</p>
                <p class="text-right" dir="ltr">{{ $trx->meta->card_number }}</p>
                <div class="col-span-full flex items-center justify-center">
                    <a class="mt-3 px-3 py-1 bg-emerald-800 rounded-md shadow-md" href="{{ $url }}">ادامه دهید</a>
                </div>
                <p class="col-span-full text-center">پس از ۱۰ ثانیه به طور خودکار به صفحه بعدی هدایت می شوید.</p>
            </div>
        </div>
    </main>
    <script>
        setTimeout(function() {
           window.location.href = "{{ $url }}"
       }, 10 * 1000);
    </script>
</x-layouts.html>