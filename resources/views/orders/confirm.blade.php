<x-layouts.html :title="__('Confirm order')">
    <main class="bg-gray-900 w-full h-full flex justify-center items-center font-vazir px-3">
        <div class="w-auto min-h-[100px] px-6 p-3 bg-gray-800 text-gray-200 shadow-md rounded-lg flex flex-col items-center">
            <svg width="50" height="50" viewBox="0 0 18 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.7968 13.7754L10.2968 0.744187C9.73434 -0.224563 8.26559 -0.255813 7.70309 0.744187L0.203087 13.7754C-0.359413 14.7442 0.359337 15.9942 1.51559 15.9942H16.4843C17.6406 15.9942 18.3593 14.7754 17.7968 13.7754ZM9.01559 11.0567C9.79684 11.0567 10.4531 11.7129 10.4531 12.4942C10.4531 13.3067 9.79684 13.9317 9.01559 13.9317C8.20309 13.9317 7.57809 13.3067 7.57809 12.4942C7.57809 11.7129 8.20309 11.0567 9.01559 11.0567ZM7.64059 5.90044C7.60934 5.68169 7.79684 5.49419 8.01559 5.49419H9.98434C10.2031 5.49419 10.3906 5.68169 10.3593 5.90044L10.1406 10.1504C10.1093 10.3692 9.95309 10.4942 9.76559 10.4942H8.23434C8.04684 10.4942 7.89059 10.3692 7.85934 10.1504L7.64059 5.90044Z" fill="#E27730"/>
            </svg>
            <h1 class="font-bold text-3xl my-3">تغییر قیمت</h1>
            <p>سفارش شما دچار تغییر قیمت شده</p>
            <div class="mt-4 grid grid-cols-2 gap-3 text-gray-300 text-lg">
                <p>مبلغ قابل پرداخت</p>
                <p><strong>{{ number_format($order->amount - $order->amount_paid) }} ریال</strong></p>
            </div>
            <div class="col-span-full flex items-center justify-center">
                <a class="mt-3 px-3 py-1 font-bold bg-orange-400 text-gray-900 rounded-md shadow-md" href="{{ url()->full() }}">ادامه</a>
            </div>
        </div>
    </main>
</x-layouts.html>