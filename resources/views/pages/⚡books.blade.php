<?php

use App\Models\Book;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Title('Books')] class extends Component
{
    public string $search = '';

    public string $title = '';
    public string $author = '';
    public string $publisher = '';
    public string $yearPublished = '';
    public string $finishedAt = '';
    public string $rating = '3';
    public bool $isFavorite = false;
    public string $comment = '';

    #[Computed]
    public function books(): Collection
    {
        return Book::query()
            ->when($this->search, fn ($q) => $q->where(function ($q) {
                $q->where('title', 'like', '%'.$this->search.'%')
                    ->orWhere('author', 'like', '%'.$this->search.'%')
                    ->orWhere('publisher', 'like', '%'.$this->search.'%');
            }))
            ->orderBy('finished_at', 'desc')
            ->get();
    }

    public function updatedRating(): void
    {
        if ($this->rating < 5) {
            $this->isFavorite = false;
        }
    }

    public function saveBook(): void
    {
        $this->validate([
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'publisher' => ['required', 'string', 'max:255'],
            'yearPublished' => ['required', 'integer', 'min:1000', 'max:'.date('Y')],
            'finishedAt' => ['required', 'date', 'before_or_equal:today'],
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'isFavorite' => ['boolean'],
            'comment' => ['nullable', 'string', 'max:1000'],
        ]);

        Book::create([
            'title' => $this->title,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'year_published' => (int) $this->yearPublished,
            'finished_at' => $this->finishedAt,
            'rating' => (int) $this->rating,
            'is_favorite' => $this->isFavorite,
            'comment' => $this->comment ?: null,
        ]);

        $this->reset(['title', 'author', 'publisher', 'yearPublished', 'finishedAt', 'comment']);
        $this->rating = '3';
        $this->isFavorite = false;
        unset($this->books);

        Flux::modal('add-book')->close();
        Flux::toast(variant: 'success', text: 'Book added successfully.');
    }

    public function deleteBook(int $id): void
    {
        Book::findOrFail($id)->delete();
        unset($this->books);
        Flux::toast(variant: 'success', text: 'Book deleted.');
    }
}; ?>

<div class="flex h-full w-full flex-1 flex-col gap-4 p-4">

        <div class="flex items-center justify-between">
            <div>
                <flux:heading size="xl">{{ __('Books') }}</flux:heading>
                <flux:text class="mt-1">
                    {{ $this->books->count() }} {{ __('book') }}{{ $this->books->count() !== 1 ? 's' : '' }}
                    @if ($search) {{ __('matching your search') }} @endif
                </flux:text>
            </div>

            <flux:modal.trigger name="add-book">
                <flux:button variant="primary" icon="plus">{{ __('Add book') }}</flux:button>
            </flux:modal.trigger>
        </div>

        <flux:input
            wire:model.live="search"
            placeholder="{{ __('Search by title, author or publisher…') }}"
            icon="magnifying-glass"
            clearable
        />

        <flux:table>
            <flux:table.columns>
                <flux:table.column>{{ __('Finished') }}</flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Author') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Publisher') }}</flux:table.column>
                <flux:table.column>{{ __('Rating') }}</flux:table.column>
                <flux:table.column>{{ __('Comment') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </flux:table.columns>

            <flux:table.rows>
                @forelse ($this->books as $book)
                    <flux:table.row :key="$book->id">
                        <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400 whitespace-nowrap">
                            {{ $book->finished_at->format('d/m/Y') }}
                        </flux:table.cell>
                        <flux:table.cell variant="strong">{{ $book->title }}</flux:table.cell>
                        <flux:table.cell>{{ $book->author }}</flux:table.cell>
                        <flux:table.cell class="text-sm text-zinc-500 dark:text-zinc-400">
                            {{ $book->year_published }}
                        </flux:table.cell>
                        <flux:table.cell class="text-sm">{{ $book->publisher }}</flux:table.cell>
                        <flux:table.cell>
                            <div class="flex items-center gap-0.5 text-base leading-none">
                                @for ($i = 1; $i <= 5; $i++)
                                    <span class="{{ $i <= $book->rating ? 'text-amber-400' : 'text-zinc-200 dark:text-zinc-700' }}">★</span>
                                @endfor
                                @if ($book->is_favorite)
                                    <span class="text-rose-500 ml-1">♥</span>
                                @endif
                            </div>
                        </flux:table.cell>
                        <flux:table.cell class="max-w-xs">
                            @if ($book->comment)
                                <flux:text class="text-sm truncate">{{ $book->comment }}</flux:text>
                            @endif
                        </flux:table.cell>
                        <flux:table.cell>
                            <flux:button
                                wire:click="deleteBook({{ $book->id }})"
                                wire:confirm="{{ __('Delete this book?') }}"
                                variant="ghost"
                                size="sm"
                                icon="trash"
                            />
                        </flux:table.cell>
                    </flux:table.row>
                @empty
                    <flux:table.row>
                        <flux:table.cell colspan="8">
                            <div class="py-12 text-center">
                                <flux:text>
                                    @if ($search)
                                        {{ __('No books found matching') }} "{{ $search }}".
                                    @else
                                        {{ __('No books yet. Add your first book!') }}
                                    @endif
                                </flux:text>
                            </div>
                        </flux:table.cell>
                    </flux:table.row>
                @endforelse
            </flux:table.rows>
        </flux:table>

        {{-- Add Book Modal --}}
        <flux:modal name="add-book" class="md:w-[34rem]">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">{{ __('Add a book') }}</flux:heading>
                    <flux:text class="mt-1">{{ __('Record a book you\'ve finished reading.') }}</flux:text>
                </div>

                <form wire:submit="saveBook" class="space-y-4">
                    <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Normal People') }}" required />

                    <div class="grid grid-cols-2 gap-4">
                        <flux:input wire:model="author" :label="__('Author')" placeholder="{{ __('e.g. Sally Rooney') }}" required />
                        <flux:input
                            wire:model="yearPublished"
                            :label="__('Year first published')"
                            type="number"
                            min="1000"
                            :max="date('Y')"
                            placeholder="{{ date('Y') }}"
                            required
                        />
                    </div>

                    <flux:input
                        wire:model="publisher"
                        :label="__('Publishing house')"
                        placeholder="{{ __('e.g. Faber & Faber') }}"
                        required
                    />

                    <flux:input wire:model="finishedAt" :label="__('Date finished')" type="date" required />

                    <div class="space-y-3">
                        <flux:select wire:model.live="rating" :label="__('Rating')">
                            <flux:select.option value="1">★☆☆☆☆ — {{ __('I hated it') }}</flux:select.option>
                            <flux:select.option value="2">★★☆☆☆ — {{ __('I didn\'t like it') }}</flux:select.option>
                            <flux:select.option value="3">★★★☆☆ — {{ __('I didn\'t like it much') }}</flux:select.option>
                            <flux:select.option value="4">★★★★☆ — {{ __('I liked it') }}</flux:select.option>
                            <flux:select.option value="5">★★★★★ — {{ __('I really liked it') }}</flux:select.option>
                        </flux:select>

                        @if ($rating == '5')
                            <flux:checkbox wire:model="isFavorite" :label="__('♥ One of my best reads of the year')" />
                        @endif
                    </div>

                    <flux:textarea
                        wire:model="comment"
                        :label="__('Comment')"
                        :placeholder="__('Any thoughts… (optional)')"
                        rows="2"
                    />

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:modal.close>
                            <flux:button variant="ghost">{{ __('Cancel') }}</flux:button>
                        </flux:modal.close>
                        <flux:button type="submit" variant="primary">{{ __('Add book') }}</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>

    </div>
</div>
