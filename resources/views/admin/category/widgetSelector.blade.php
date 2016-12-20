<select id="{{ $select['id'] }}" name="{{ $select['name'] }}" class="{{ $select['class'] }}">
    @if($showRoot)
        <option value="0">/</option>
    @endif
    {{ selectTree($tree, $select['value']) }}
</select>
