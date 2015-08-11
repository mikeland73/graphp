<h3>Node Types</h3>
<div class="table-responsive">
  <table class="table table-striped">
    <thead>
      <tr>
        <th>Type</th>
        <th>Name</th>
        <th>Count</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($types as $id => $name): ?>
        <tr>
          <td><a href="<?=Admin::URI()->node_type($id)?>"><?=$id?></a></td>
          <td><a href="<?=Admin::URI()->node_type($id)?>"><?=$name?></a></td>
          <td><?=idx($counts, $id, 0)?></td>
        </tr>
      <?php endforeach; ?>
    <tbody>
  </table>
</div>
