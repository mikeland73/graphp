<h3>
  <form action="<?=Admin::getURI('node_type', $type)?>" method="POST">
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
        <th>Edit</th>
        <th>Delete</th>
      </tr>
    </thead>
    <tbody>
      <? foreach ($nodes as $node): ?>
        <tr>
          <td>
            <a href="<?=Admin::getURI('node', $node->getID())?>">
              <?=$node->getID()?>
            </a>
          </td>
          <td><?=$node->getJSONData()?></td>
          <td>
            <a
              class="btn btn-sm btn-default active"
              href="<?=Admin::getURI('node', $node->getID())?>">
              Edit
            </a>
          </td>
          <td>
            <form action="<?=Admin::getURI('node_type', $type)?>" method="POST">
              <input
                name="delete_node_id"
                type="hidden"
                value="<?=$node->getID()?>"
              />
              <button class="btn btn-sm btn-danger">Delete</button>
            </form>
          </td>
        </tr>
      <? endforeach; ?>
    <tbody>
  </table>
</div>
