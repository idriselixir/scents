@props(['fragrance', 'hasReachedBagLimit' => false, 'feedback' => ''])

<div class="detail-photo {{ $fragrance->tone->cssClass() }}">
  <img src="{{ $fragrance->photoUrl() }}" width="480" height="480" alt="{{ __(':house :name bottle', ['house' => $fragrance->house, 'name' => $fragrance->name]) }}">
</div>
<div class="detail-copy">
  <p class="eyebrow">{{ $fragrance->house }} · {{ $fragrance->scentFamily->label }}</p>
  <h2 id="product-title">{{ $fragrance->name }}</h2>
  <p class="detail-description">{{ $fragrance->description }}</p>
  <dl class="note-list">
    @foreach ($fragrance->notesByStage() as $stage => $notes)
      <div><dt>{{ $stage }}</dt><dd>{{ $notes }}</dd></div>
    @endforeach
  </dl>
  <a class="detail-source text-link" href="{{ $fragrance->source_url }}" target="_blank" rel="noopener noreferrer">{{ __('Fragrance notes from :house', ['house' => $fragrance->house]) }} <span aria-hidden="true">↗</span></a>
  <p class="detail-format">{{ $fragrance->size }} · {{ $fragrance->concentration }}</p>
  <button type="button" class="btn btn-primary btn-block detail-add" wire:click="addViewedFragranceToBag" @disabled($hasReachedBagLimit)>
    <span>{{ $hasReachedBagLimit ? __('Bag limit reached') : __('Add to bag') }}</span>
    <strong class="num">{{ $fragrance->formattedPrice() }}</strong>
  </button>
  <p class="detail-feedback" role="status">{{ $feedback }}</p>
</div>
