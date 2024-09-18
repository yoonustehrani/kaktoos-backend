<x-layouts.html :title="__('Transaction successfull')">
    <main class="bg-gray-900 w-full h-screen flex justify-center items-center font-vazir">
        <div class="w-auto min-h-[100px] px-6 py-3 bg-gray-800 text-gray-200 shadow-md rounded-lg flex flex-col items-center">
            <svg width="60" height="60" viewBox="0 0 13 13" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path class="fill-red-600" d="M9 7.8c0 .1 0 .2-.2.3l-.7.7-.3.2c-.2 0-.3 0-.4-.2L6 7.4 4.6 8.8a.5.5 0 01-.7 0l-.7-.7-.2-.3c0-.2 0-.3.2-.4L4.6 6 3.2 4.6a.5.5 0 010-.7l.7-.7.3-.2c.2 0 .3 0 .4.2L6 4.6l1.4-1.4a.5.5 0 01.7 0l.7.7.2.3c0 .2 0 .3-.2.4L7.4 6l1.4 1.4.2.4zM12 6A6 6 0 100 6a6 6 0 0012 0z"/>
            </svg>
            <h1 class="font-bold text-3xl text-red-600 my-3">تراکنش ناموفق</h1>
            <div class="mt-4 grid grid-cols-2 gap-3 text-gray-300 text-lg">
                <p>مبلغ</p>
                <p>{{ number_format($trx->amount) }} {{ \App\Payment\IranianCurrency::tryFrom($request->currency)?->getDisplayFa() }}</p>
                <p>شناسه تراکنش درگاه</p>
                <p>{{ $request->purchaseId }}</p>
                <p>علت رد تراکنش</p>
                <p>{{ $trx->status_notes }}</p>
                <p>آدرس IP کاربر</p>
                <p>{{ $request->payerIp }}</p>
                <div class="col-span-full flex items-center justify-center">
                    <a class="mt-3 px-3 py-1 bg-red-900 rounded-md shadow-md" href="{{ $url }}">تراکنش مجدد</a>
                </div>
            </div>
        </div>
    </main>
</x-layouts.html>