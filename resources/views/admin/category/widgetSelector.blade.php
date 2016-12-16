<?php
function selectTree ($tree, $value) {
    foreach($tree as $leaf) {
        $tab = '';
        $tab = str_repeat('&nbsp;', ($leaf['level']) * 4);
        if($value == $leaf['id']) {
            echo '<option value="' . $leaf['id'] . '" selected>' . $tab . $leaf['name'] . '</option>';
        } else {
            echo '<option value="' . $leaf['id'] . '">' . $tab . $leaf['name'] . '</option>';
        }

        if(isset($leaf['branch'])) {
            selectTree($leaf['branch'], $value);
        }
    }
}
?>

<select id="{{ $select['id'] }}" name="{{ $select['name'] }}" class="{{ $select['class'] }}">
    @if($showRoot)
        <option value="0">/</option>
    @endif
    {{ selectTree($tree, $select['value']) }}
</select>
