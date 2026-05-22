@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="config('app.name')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground overflow-hidden">
            <img src="/images/book-stack.png" alt="App logo" class="size-6 object-contain" />
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="config('app.name')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-md bg-accent-content text-accent-foreground overflow-hidden">
            <img src="/images/book-stack.png" alt="App logo" class="size-6 object-contain" />
        </x-slot>
    </flux:brand>
@endif
