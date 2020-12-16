<table class="table">
    <tr>
        <?php foreach ($data['headers'] as $header): ?>
            <th><?php print $header; ?></th>
        <?php endforeach; ?>
    </tr>
</table>
<!-- Update Modal (Popup)-->
<?php if (isset($data['forms']['update'])): ?>
    <div id="update-modal" class="modal">
        <div class="wrapper">
            <span class="close">&times;</span>
            <?php print $data['forms']['update']; ?>
        </div>
    </div>
<?php endif; ?>