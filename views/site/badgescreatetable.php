<table id = "badges-form-table" class="table table-striped table-bordered participants-table">  
    <tr class = "dataset-table-header-row">
        <th class = "dataset-header-column">
            Name
        </th>
        <th class = "dataset-header-column">
            Type
        </th>
        <th class = "dataset-header-column">
            Size
        </th>
        <th class = "dataset-header-column">
            Image Preview
        </th>
        <th class = "dataset-header-column">
            Public
        </th>
    </tr>
    <?php foreach ($badgesNew as $key => $newBadge): ?>
        <tr>
            <td id = "newBadge-<?=$key?>">
                <?= $form->field($newBadge, "name[$key]")->textInput([])->label(false) ?>
            </td>
            <td id = "newBadge-type-<?=$key?>"></td>
            <td id = "newBadge-size-<?=$key?>"></td>
            <td> 
                <img class="badge-image-preview" id="newBadge-preview-<?=$key?>" src="#" alt="" />
            </td>
            <td id = "newBadge-<?=$key?>">
                <?= $form->field($newBadge, "allowusers[$key]")->dropDownList([ 1 => 'Yes', 0 => 'No'])->label(false) ?>
            </td>
        </tr>
    <?php endforeach; ?>
</table>