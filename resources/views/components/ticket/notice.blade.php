<section class="w-full font-vazir mx-auto 2xl:w-4/5 bg-gray-700 text-gray-200 rounded-xl px-6 py-4">
    <div dir="rtl" class="font-vazir">
        <strong class="block text-2xl font-bold">قوانین و مقررات</strong>
        <ul class="list-disc list-inside my-2">
            <li>هزینه های کنسلی طبق قوانین ایرلاین محاسبه میگردد.</li>
            <li>مسافر گرامی، شما می بایستی 2 ساعت قبل از زمان پرواز در فرودگاه حضور داشته باشید.</li>
            <li>در صورت ایجاد هرگونه محدودیت در پذیرش مسافر، این شرکت هیچگونه مسئولیتی در این خصوص نخواهد داشت و کلیه خسارات متوجه خریدار می باشد.</li>
        </ul>
        @if ($meta->online_check_in->active)
            <hr class="block my-4">
            <strong class="block text-2xl mt-3 font-bold">@lang('parto.online_check_in_required')</strong>
            <ul class="list-disc list-inside my-2">
                <li>@lang('parto.online_check_in_message', ['airline' => $airline])</li>
            </ul>
        @endif
        @if (count($notes))
            <hr class="block my-4">
            <strong class="block text-2xl mt-3 font-bold">نکات پرواز</strong>
            <ul class="list-disc list-inside my-2">
                @foreach ($notes as $note)
                    <li>{{ $note }}</li>
                @endforeach
            </ul>
        @endif
    </div>
</section>