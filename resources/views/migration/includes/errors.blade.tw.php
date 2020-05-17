@if(session('responseErrors'))
<div class="relative px-3 py-3 mb-4 border rounded text-red-darker border-red-dark bg-red-lighter">
    @foreach(session('responseErrors') as $error)
        <p>{!! $error !!}</p>
    @endforeach
</div>
@endif

@if($errors->any())
<div class="relative px-3 py-3 mb-4 border rounded text-red-darker border-red-dark bg-red-lighter">
    @foreach($errors->all() as $error)
        <p>{!! $error !!}</p>
    @endforeach
</div>
@endif