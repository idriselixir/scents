@props(['fragrances'])

<div class="compare-grid" style="--compared:{{ $fragrances->count() }}">
  @foreach ($fragrances as $fragrance)
    <article class="compare-item" wire:key="compared-{{ $fragrance->uuid }}">
      <div class="detail-photo {{ $fragrance->tone->cssClass() }}"><img src="{{ $fragrance->photoUrl() }}" alt="" width="480" height="480"></div>
      <h3>{{ $fragrance->name }}</h3>
      <p class="eyebrow">{{ $fragrance->house }}</p>
      <dl>
        @foreach ($fragrance->notesByStage() as $stage => $notes)
          <dt>{{ $stage }}</dt><dd>{{ $notes }}</dd>
        @endforeach
        <dt>{{ $fragrance->size }}</dt><dd class="num">{{ $fragrance->formattedPrice() }}</dd>
      </dl>
    </article>
  @endforeach
</div>
<button class="text-link" type="button" id="clear-comparison" wire:click="clearComparison">{{ __('Clear selection') }}</button>
