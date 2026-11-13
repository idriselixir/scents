<div>
  {{-- Header --}}
  <header class="site-header">
    <div class="wrap header-inner">
      <a class="wordmark" href="#" aria-label="{{ __('After Note home') }}">after<span>note</span><sup>✳</sup></a>
      <nav aria-label="{{ __('Main navigation') }}">
        <a href="#collection">{{ __('The Collection') }}</a>
        <a href="#scent-finder">{{ __('Scent Finder') }}</a>
      </nav>
      <button class="bag-button" type="button" id="open-bag" data-open-dialog="bag-dialog"
        aria-label="{{ trans_choice('Open bag, :count item|Open bag, :count items', $bagItemCount) }}">
        <span class="bag-icon" aria-hidden="true"></span><span>{{ __('Bag') }}</span><span id="bag-count" class="num bag-count" aria-hidden="true">{{ $bagItemCount }}</span>
      </button>
    </div>
  </header>

  <main id="main">
    {{-- Hero --}}
    <section class="hero" aria-labelledby="hero-title">
      <img class="campaign-photo" src="{{ asset('assets/campaign.webp') }}" alt="" width="1500" height="1000" fetchpriority="high">
      <div class="hero-inner wrap">
        <div class="hero-content">
          <p class="eyebrow">{{ __('ARABIC PERFUMERY · THE AFTER NOTE EDIT') }}</p>
          <h1 id="hero-title">{!! __('The scent<br>that <em>stays.</em>') !!}</h1>
          <p class="hero-description">{{ __('From a first hit of citrus to the last trace of oud. Discover Afnan and Lattafa by the notes you love.') }}</p>
          <div class="hero-actions">
            <a class="btn btn-primary" href="#collection">{{ __('Explore the collection') }} <span aria-hidden="true">↗</span></a>
            <a class="text-link" href="#scent-finder">{{ __('Find my scent') }} <span aria-hidden="true">↓</span></a>
          </div>
        </div>
        <p class="campaign-caption">{{ __('AMBER / CITRUS / CEDAR') }}</p>
      </div>
    </section>

    {{-- Scent index --}}
    <nav class="scent-index wrap" id="scent-index" aria-label="{{ __('Shop by scent family') }}">
      @foreach ($families as $scentFamily)
        <a href="#collection" class="family-link" wire:key="index-{{ $scentFamily->uuid }}" wire:click="browseFamily('{{ $scentFamily->slug }}')">
          <strong>{{ $scentFamily->label }}</strong><span aria-hidden="true">↗</span>
          <small>{{ $scentFamily->example_notes }}</small>
        </a>
      @endforeach
    </nav>

    {{-- Collection --}}
    <section id="collection" class="collection wrap" aria-labelledby="collection-title">
      <div class="collection-intro">
        <div>
          <p class="eyebrow"><span class="rule" aria-hidden="true"></span> {{ __('THE COLLECTION') }}</p>
          <h2 id="collection-title">{!! __('The fragrance <em>wardrobe.</em>') !!}</h2>
        </div>
        <p>{{ __('Search by bottle, house or note. Compare up to three fragrances side by side.') }}</p>
      </div>
      <div class="catalog-toolbar">
        <div class="filters" id="filters" role="group" aria-label="{{ __('Filter by scent family') }}">
          <button class="btn btn-ghost filter" type="button" wire:click="selectFamily('{{ $allFamilies }}')"
            aria-pressed="{{ $family === $allFamilies ? 'true' : 'false' }}">{{ __('All scents') }}</button>
          @foreach ($families as $scentFamily)
            <button class="btn btn-ghost filter" type="button" wire:key="filter-{{ $scentFamily->uuid }}"
              wire:click="selectFamily('{{ $scentFamily->slug }}')"
              aria-pressed="{{ $family === $scentFamily->slug ? 'true' : 'false' }}">{{ $scentFamily->label }}</button>
          @endforeach
        </div>
        <label class="search"><span class="sr-only">{{ __('Search fragrances') }}</span><input id="search" class="input" type="search" wire:model.live.debounce.150ms="search" placeholder="{{ __('Search vanilla, oud, Afnan…') }}"></label>
      </div>
      <div class="catalog-status">
        <p class="result-count" id="result-count" role="status">{{ trans_choice(':count fragrance|:count fragrances', $fragrances->count()) }}</p>
        <button class="btn btn-ghost" id="open-compare" type="button" data-open-dialog="compare-dialog"
          @disabled($comparedFragrances->count() < 2)>{{ __('Compare (:count/:limit)', ['count' => $comparedFragrances->count(), 'limit' => $comparisonLimit]) }}</button>
      </div>
      <div id="products" class="products">
        @forelse ($fragrances as $fragrance)
          <x-shop.fragrance-card :fragrance="$fragrance" wire:key="card-{{ $fragrance->uuid }}"
            :is-compared="in_array($fragrance->uuid, $comparedUuids, true)"
            :is-comparison-full="$comparedFragrances->count() >= $comparisonLimit" />
        @empty
          <p class="catalog-empty">{{ __('No fragrances match.') }} <button class="text-link" type="button" wire:click="clearFilters">{{ __('Clear filters') }}</button></p>
        @endforelse
      </div>
    </section>

    {{-- Scent finder --}}
    <section id="scent-finder" class="finder-section wrap" aria-labelledby="finder-title">
      <div class="finder-photo">
        <img src="{{ asset('assets/notes.webp') }}" alt="{{ __('Blood orange, vanilla pods and cinnamon bark on dark volcanic stone') }}" width="1200" height="800" loading="lazy">
      </div>
      <div class="finder-content">
        <p class="eyebrow"><span class="rule" aria-hidden="true"></span> {{ __('THE SCENT FINDER') }}</p>
        <h2 id="finder-title">{!! __('What do you<br><em>feel like wearing?</em>') !!}</h2>
        <p>{{ __('Choose a scent profile to see three bottles to try.') }}</p>
        <div id="finder-options" class="finder-options" role="group" aria-label="{{ __('Choose a scent mood') }}">
          @foreach ($families as $scentFamily)
            <button class="btn btn-ghost" type="button" wire:key="mood-{{ $scentFamily->uuid }}"
              wire:click="selectMood('{{ $scentFamily->slug }}')"
              aria-pressed="{{ $mood === $scentFamily->slug ? 'true' : 'false' }}">{{ $scentFamily->mood }}</button>
          @endforeach
        </div>
        {{-- shop.css only draws the result's rule when it is :not(:empty), so no whitespace inside --}}
        <div id="finder-result" class="finder-result" aria-live="polite">@foreach ($finderMatches as $fragrance)<article class="finder-match" wire:key="match-{{ $fragrance->uuid }}">
            <div><h3>{{ $fragrance->name }}</h3><p>{{ $fragrance->caption }}</p></div>
            <button class="text-link" type="button" wire:click="showFragrance('{{ $fragrance->uuid }}')" aria-label="{{ __('Explore :name', ['name' => $fragrance->name]) }}">{{ __('Explore') }} <span aria-hidden="true">↗</span></button>
          </article>@endforeach</div>
      </div>
    </section>
  </main>

  {{-- Footer --}}
  <footer class="wrap site-footer">
    <a class="wordmark" href="#" aria-label="{{ __('After Note home') }}">after<span>note</span><sup>✳</sup></a>
    <p>{{ __('Store preview · Prices in KSh are illustrative.') }}</p>
    <a href="#main" class="text-link">{{ __('Back to top') }} <span aria-hidden="true">↑</span></a>
  </footer>

  {{-- Dialogs: wire:ignore.self keeps the browser's `open` state through re-renders --}}
  <dialog id="product-dialog" class="product-dialog" aria-labelledby="product-title" wire:ignore.self>
    <form method="dialog" class="dialog-close"><button type="submit" aria-label="{{ __('Close fragrance details') }}">×</button></form>
    <div id="product-detail">
      @if ($viewedFragrance)
        <x-shop.product-detail :fragrance="$viewedFragrance" :has-reached-bag-limit="$bag->hasReachedLimit($viewedFragrance)" :feedback="$addedToBagFeedback" />
      @endif
    </div>
  </dialog>
  <dialog id="compare-dialog" aria-labelledby="compare-title" wire:ignore.self>
    <form method="dialog" class="dialog-close"><button type="submit" aria-label="{{ __('Close comparison') }}">×</button></form>
    <div class="compare-content">
      <h2 id="compare-title">{{ __('Compare fragrances') }}</h2>
      <div id="compare-results">
        <x-shop.comparison :fragrances="$comparedFragrances" />
      </div>
    </div>
  </dialog>
  <dialog id="bag-dialog" class="bag-dialog" aria-labelledby="bag-title" wire:ignore.self>
    <form method="dialog" class="dialog-close"><button type="submit" aria-label="{{ __('Close bag') }}">×</button></form>
    <h2 id="bag-title">{{ __('Your bag') }}</h2>
    <x-shop.bag :lines="$bagLines" :subtotal="$bagSubtotal" />
  </dialog>
  <p class="sr-only" id="bag-announcement" role="status">{{ $bagAnnouncement }}</p>
</div>
