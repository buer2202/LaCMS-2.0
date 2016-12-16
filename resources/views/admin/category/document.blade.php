<div class="list-group">
    <a href="javascript:void(0)" class="list-group-item {{ $category->document_id == '' ? 'active' : '' }}" data-id="">无</a>
    @foreach($document as $val)
    <a href="javascript:void(0)" class="list-group-item {{ $category->document_id == $val->id ? 'active' : '' }}" data-id="{{ $val->id }}">{{ mb_substr($val->title, 0, 36) }}</a>
    @endforeach
</div>
