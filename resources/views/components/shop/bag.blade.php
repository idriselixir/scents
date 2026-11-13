@props(['lines', 'subtotal'])

@use('App\Models\Fragrance')
@use('App\Services\BagService')

<div id="bag-contents">
  @if ($lines->isNotEmpty())
    <ul class="bag-list">
      @foreach ($lines as $line)
        @php($fragrance = $line['fragrance'])
        <li class="bag-row" wire:key="bag-{{ $fragrance->uuid }}">
          <div class="bag-photo {{ $fragrance->tone->cssClass() }}"><img src="{{ $fragrance->photoUrl() }}" width="480" height="480" alt=""></div>
          <div>
            <p class="eyebrow">{{ $fragrance->house }}</p>
            <h3>{{ $fragrance->name }}</h3>
            <div class="quantity">
              <button type="button" data-quantity="-1" wire:click="changeBagQuantity('{{ $fragrance->uuid }}', -1)"
                aria-label="{{ $line['quantity'] === 1 ? __('Remove :name', ['name' => $fragrance->name]) : __('Decrease quantity of :name', ['name' => $fragrance->name]) }}">−</button>
              <span class="num">{{ $line['quantity'] }}</span>
              <button type="button" data-quantity="1" wire:click="changeBagQuantity('{{ $fragrance->uuid }}', 1)"
                aria-label="{{ __('Increase quantity of :name', ['name' => $fragrance->name]) }}"
                @disabled($line['quantity'] >= BagService::MAXIMUM_QUANTITY_PER_FRAGRANCE)>+</button>
            </div>
          </div>
          <strong class="num">{{ Fragrance::formatPrice($line['total']) }}</strong>
        </li>
      @endforeach
    </ul>
  @else
    <p class="empty-bag">{{ __('Your bag is empty.') }}</p>
    <button class="btn btn-primary" type="button" data-browse>{{ __('Browse fragrances') }} <span aria-hidden="true">↗</span></button>
  @endif
</div>
<div id="bag-summary" class="bag-summary" @if ($lines->isEmpty()) hidden @endif>
  <div class="bag-total"><strong>{{ __('Subtotal') }}</strong><strong id="subtotal" class="num">{{ Fragrance::formatPrice($subtotal) }}</strong></div>
  <p class="hint">{{ __('This is a store preview. You can build a bag here. Orders and payments aren’t available yet.') }}</p>
</div>
