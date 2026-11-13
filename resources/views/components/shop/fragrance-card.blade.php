@props(['fragrance', 'isCompared' => false, 'isComparisonFull' => false])

<article {{ $attributes->class('card fragrance') }}>
  <button class="packshot {{ $fragrance->tone->cssClass() }}" type="button" wire:click="showFragrance('{{ $fragrance->uuid }}')"
    aria-label="{{ __('Explore :house :name', ['house' => $fragrance->house, 'name' => $fragrance->name]) }}">
    <img src="{{ $fragrance->photoUrl() }}" alt="" width="480" height="480" loading="lazy">
    <span class="packshot-arrow" aria-hidden="true">↗</span>
  </button>
  <div class="fragrance-copy">
    <p class="eyebrow">{{ $fragrance->house }}</p>
    <h3><button class="fragrance-title" type="button" wire:click="showFragrance('{{ $fragrance->uuid }}')">{{ $fragrance->name }}</button></h3>
    <p class="fragrance-notes">{{ $fragrance->caption }}</p>
    <div class="fragrance-meta">
      <span>{{ $fragrance->size }} · {{ $fragrance->concentration }}</span>
      <strong class="num">{{ $fragrance->formattedPrice() }}</strong>
    </div>
    <label class="compare-choice">
      <input type="checkbox" wire:model.live="comparedUuids" value="{{ $fragrance->uuid }}"
        aria-label="{{ __('Compare :name', ['name' => $fragrance->name]) }}" @disabled($isComparisonFull && ! $isCompared)>
      {{ __('Compare') }}
    </label>
  </div>
</article>
