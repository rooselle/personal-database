<flux:table container:class="border-thick border-hairline rounded-lg overflow-hidden bg-paper-0 shadow-cutout-sm">
    <flux:table.columns class="bg-mustard-100 [&>tr>th]:border-hairline! [&>tr>th]:border-b-[2.5px]! [&>tr>th]:first:ps-3.5! [&>tr>th]:last:pe-3.5! *:font-extrabold *:uppercase! *:tracking-wider! *:text-xs">
        {{ $columns }}
    </flux:table.columns>

    <flux:table.rows class="**:border-soft! [&>tr>td]:first:ps-3.5! [&>tr>td]:last:pe-3.5!">
        {{ $slot }}
    </flux:table.rows>
</flux:table>
