@if(config('anti-scam.scam.active',false))
    @php
          $key = config('anti-scam.key');

          if (empty($key)){
                throw new Attargah\AntiScam\Exceptions\EmptyKeyException();
          }

          $letters = 'abcdefghijklmnopqrstuvwxyz';

          $classes = collect(range(1, 5))->map(function () use ($letters) {
             return $letters[random_int(0, strlen($letters) - 1)] . \Illuminate\Support\Str::random(10);
          });

          $inputs = config('anti-scam.scam.inputs');
          if (config('anti-scam.scam.order_random')){
                  $inputs = collect($inputs)->shuffle();
          }


          $x = random_int(0, $inputs->count() - 1);

          $hash = \Illuminate\Support\Facades\Hash::make($key.','.$x.','.$inputs[$x]['name']);

    @endphp

    <style>
        {{ $classes->map(fn($c) => '.' . $c)->implode(', ') }}
{
        @if(config('anti-scam.scam.display.active',false))
             {!! config('anti-scam.scam.display.css','') !!}
        @endif

         @if(config('anti-scam.scam.off_screen.active',true))
           {!! config('anti-scam.scam.off_screen.css','') !!}
         @endif

}
    </style>

    @foreach($inputs as $key=>$input)
        <div class="{{$classes[$key]}}" {!! config('anti-scam.scam.attributes.div','')  !!}>
            <label for="{{$input['id']}}" {!! config('anti-scam.scam.attributes.label','')   !!}>{{$input['label']}}</label>
            <input id="{{$input['id']}}" type="text" name="{{$input['name']}}"
                   @if($key == $x) value="{{$hash}}" @endif {!! config('anti-scam.scam.attributes.input','') !!}>
        </div>
    @endforeach
    <input type="hidden" name="form_identity" value="{{$identity}}">
@endif
