<h3>
  <form action="<?=Admin::getURI('node_type', $data['type'])?>" method="POST">
    <?=$data['name']?>
    <button class="btn btn-primary">
      New <?=$data['name']?>
    </button>
    <input name="type" type="hidden" value="<?=$data['type']?>">
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
      <? foreach ($data['nodes'] as $node): ?>
        <tr>
          <td>
            <a href="<?=Admin::getURI('node', $node->getID())?>">
              <?=$node->getID()?>
            </a>
          </td>
          <td><?=$node->getJSONData()?></td>
          <td>
            <button type="button" class="btn btn-sm">Edit</button>
          </td>
          <td>
            <button type="button" class="btn btn-sm btn-danger">Delete</button>
          </td>
        </tr>
      <? endforeach; ?>
    <tbody>
  </table>
</div>
