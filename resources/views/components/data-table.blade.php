{{--
    "Personal Database" allow a user to register the books they've read
    and the TV shows and films they've watched, to rate them and to search
    among them.
    Copyright (C) 2026 roselle (chloe@roselle.co)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU Affero General Public License as
    published by the Free Software Foundation, either version 3 of the
    License, or (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU Affero General Public License for more details.

    You should have received a copy of the GNU Affero General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
--}}

<flux:table container:class="border-thick border-hairline rounded-lg overflow-hidden bg-paper-0 shadow-cutout-sm">
    <flux:table.columns class="bg-mustard-100 [&>tr>th]:border-hairline! [&>tr>th]:border-b-[2.5px]! [&>tr>th]:first:ps-3.5! [&>tr>th]:last:pe-3.5! *:font-extrabold *:uppercase! *:tracking-wider! *:text-xs">
        {{ $columns }}
    </flux:table.columns>

    <flux:table.rows class="**:border-soft! [&>tr>td]:first:ps-3.5! [&>tr>td]:last:pe-3.5!">
        {{ $slot }}
    </flux:table.rows>
</flux:table>
