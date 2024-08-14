<div class="container">
    <div class="row">
        <div class="col">
            <input type="hidden" name="facility_code_rate_{{ $mflCode }}" id="facility_code_rate_{{ $mflCode }}" value="{{ $mflCode }}">

            <div class="{{ $searchRating > 0 ? 'rated' : 'rate' }}">
                @for($i=1; $i<=$searchCounter; $i++)
                    <label id="star_{{$i}}" class="star-rating-complete" title="text" data-toggle="modal" data-target="#modal_rating" onclick="document.getElementById('facility_code').value = '{{ $mflCode }}';document.getElementById('star{{$i}}').checked = true;">{{$i}} stars</label>
                @endfor
            </div>
        </div>
    </div>
</div>
