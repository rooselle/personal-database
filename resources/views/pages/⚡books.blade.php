<?php

use App\Models\Book;
use Flux\Flux;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\Computed;
use Livewire\Component;

new class extends Component
{
    public string $search = '';
    public string $displayMode = 'list';

    public string $title = '';
    public string $coverUrl = '';
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
            'coverUrl' => ['nullable', 'string', 'max:2048'],
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
            'cover_url' => $this->coverUrl ?: null,
            'author' => $this->author,
            'publisher' => $this->publisher,
            'year_published' => (int) $this->yearPublished,
            'finished_at' => $this->finishedAt,
            'rating' => (int) $this->rating,
            'is_favorite' => $this->isFavorite,
            'comment' => $this->comment ?: null,
        ]);

        $this->reset(['title', 'coverUrl', 'author', 'publisher', 'yearPublished', 'finishedAt', 'comment']);
        $this->rating = '3';
        $this->isFavorite = false;
        unset($this->books);

        Flux::modal('add-book')->close();
        Flux::toast(variant: 'success', text: __('Book added successfully.'));
    }

    public function deleteBook(int $id): void
    {
        Book::findOrFail($id)->delete();
        unset($this->books);
        Flux::toast(variant: 'success', text: __('Book deleted.'));
    }

    public function render(): \Illuminate\View\View
    {
        return $this->view()->title(__('Books'));
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
            <x-cutout-button icon="plus">{{ __('Add book') }}</x-cutout-button>
        </flux:modal.trigger>
    </div>

    <div class="flex items-center gap-2">
        <flux:input
            wire:model.live="search"
            placeholder="{{ __('Search by title, author or publisher…') }}"
            icon="magnifying-glass"
            clearable
            class="flex-1"
        />
        <div class="flex items-center gap-1 shrink-0">
            <flux:button
                wire:click="$set('displayMode', 'list')"
                variant="{{ $displayMode === 'list' ? 'filled' : 'ghost' }}"
                icon="list-bullet"
                size="sm"
                title="{{ __('List view') }}"
            />
            <flux:button
                wire:click="$set('displayMode', 'gallery')"
                variant="{{ $displayMode === 'gallery' ? 'filled' : 'ghost' }}"
                icon="squares-2x2"
                size="sm"
                title="{{ __('Gallery view') }}"
            />
        </div>
    </div>

    @if ($displayMode === 'list')
        <x-data-table>
            <x-slot:columns>
                <flux:table.column></flux:table.column>
                <flux:table.column>{{ __('Finished on') }}</flux:table.column>
                <flux:table.column>{{ __('Title') }}</flux:table.column>
                <flux:table.column>{{ __('Author') }}</flux:table.column>
                <flux:table.column>{{ __('Year') }}</flux:table.column>
                <flux:table.column>{{ __('Publisher') }}</flux:table.column>
                <flux:table.column>{{ __('Rating') }}</flux:table.column>
                <flux:table.column>{{ __('Comment') }}</flux:table.column>
                <flux:table.column></flux:table.column>
            </x-slot:columns>

            @forelse ($this->books as $book)
                <flux:table.row :key="$book->id">
                    <flux:table.cell class="w-10 pr-0">
                        @if ($book->cover_url)
                            <img
                                src="{{ $book->cover_url }}"
                                alt="{{ $book->title }}"
                                class="h-12 w-8 rounded object-cover shadow-sm"
                            />
                        @else
                            <div class="h-12 w-8 rounded bg-paper-200 flex items-center justify-center">
                                <flux:icon.book-open class="size-4 text-ink-300" />
                            </div>
                        @endif
                    </flux:table.cell>
                    <flux:table.cell class="text-sm text-ink-500 whitespace-nowrap">
                        {{ $book->finished_at->format('d/m/Y') }}
                    </flux:table.cell>
                    <flux:table.cell variant="strong">{{ $book->title }}</flux:table.cell>
                    <flux:table.cell>{{ $book->author }}</flux:table.cell>
                    <flux:table.cell class="text-sm text-ink-500">
                        {{ $book->year_published }}
                    </flux:table.cell>
                    <flux:table.cell class="text-sm">{{ $book->publisher }}</flux:table.cell>
                    <flux:table.cell>
                        <x-rating-stars :value="$book->rating" :favorite="$book->is_favorite" size="text-base" />
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
                            class="hover:text-rust-500!"
                        />
                    </flux:table.cell>
                </flux:table.row>
            @empty
                <flux:table.row>
                    <flux:table.cell colspan="9">
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
        </x-data-table>
    @else
        @if ($this->books->isEmpty())
            <div class="py-16 text-center">
                <flux:text>
                    @if ($search)
                        {{ __('No books found matching') }} "{{ $search }}".
                    @else
                        {{ __('No books yet. Add your first book!') }}
                    @endif
                </flux:text>
            </div>
        @else
            <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6">
                @foreach ($this->books as $book)
                    <x-media-card
                        :title="$book->title"
                        :meta="$book->author"
                        :cover-url="$book->cover_url"
                        :favorite="$book->is_favorite"
                        placeholder-icon="book-open"
                    >
                        <x-slot:delete>
                            <button
                                wire:click="deleteBook({{ $book->id }})"
                                wire:confirm="{{ __('Delete this book?') }}"
                                class="flex size-6 items-center justify-center rounded-full border-thin border-hairline bg-paper-0 text-ink-500 shadow-cutout-sm hover:text-rust-500"
                            >
                                <flux:icon.trash class="size-3.5" />
                            </button>
                        </x-slot:delete>

                        <x-slot:footer>
                            <x-rating-stars :value="$book->rating" size="text-xs" />
                        </x-slot:footer>
                    </x-media-card>
                @endforeach
            </div>
        @endif
    @endif

    <flux:modal name="add-book" class="md:w-[34rem] rounded-lg border-thick border-hairline bg-paper-0 shadow-modal">
        <div class="space-y-6">
            <div>
                <flux:heading size="lg">{{ __('Add a book') }}</flux:heading>
                <flux:text class="mt-1">{{ __('Record a book you\'ve finished reading.') }}</flux:text>
            </div>

            <form wire:submit="saveBook" class="space-y-4">
                <flux:input wire:model="title" :label="__('Title')" placeholder="{{ __('e.g. Normal People') }}" required />

                <flux:input
                    wire:model="coverUrl"
                    :label="__('Cover image URL')"
                    :description="__('Paste a link to a cover image (optional)')"
                    placeholder="https://..."
                    type="url"
                />

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
                    <flux:select wire:model.live="rating" :label="__('Rating')" required>
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
                        <flux:button variant="ghost" class="font-bold!">{{ __('Cancel') }}</flux:button>
                    </flux:modal.close>
                    <x-cutout-button type="submit">{{ __('Add book') }}</x-cutout-button>
                </div>
            </form>
        </div>
    </flux:modal>

</div>
