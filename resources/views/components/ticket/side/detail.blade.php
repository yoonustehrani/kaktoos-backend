<div class="flex flex-wrap gap-2 items-center w-full">
    @isset($icon)
        <x-dynamic-component :component="'icons.'.$icon" class="w-6 h-6"/>
    @endisset
    <strong>{{ $title }}</strong>
    <p class="bg-gray-200 grow w-auto px-2 py-1 italic rounded-md">{{ $slot }}</p>
</div>