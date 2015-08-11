<h3>
  <form action="<?=AdminAjax::URI()->create($type)?>" method="POST">
    <?=GPSecurity::csrf()?>
    <?=$name?>
    <button class="btn btn-primary">
      New <?=$name?>
    </button>
    <input name="create" type="hidden">
  </form>
</h3>

<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>ID</th>
        <th>Data</th>
        <th>Updated</th>
        <th>Edit</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($nodes as $node): ?>
        <tr>
          <td>
            <a href="<?=Admin::URI()->node($node->getID())?>">
              <?=$node->getID()?>
            </a>
          </td>
          <td><?=substr($node->getJSONData(), 0, 128)?></td>
          <td><?=$node->getUpdated()?></td>
          <td>
            <a
              class="btn btn-sm btn-default active"
              href="<?=Admin::URI()->node($node->getID())?>">
              Edit
            </a>
          </td>
          <td>
            <form action="<?=AdminAjax::URI()->delete($type)?>" method="POST">
              <?=GPSecurity::csrf()?>
              <input
                name="delete_node_id"
                type="hidden"
                value="<?=$node->getID()?>"
              />
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <tbody>
  </table>
</div>
