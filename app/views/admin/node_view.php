<a href="<?=Admin::getURI('node_type', $node::getType())?>">
  back to <?= get_class($node) ?> list
</a>
<h2><?= get_class($node) ?> ID <?= $node->getID() ?></h2>
<h3>Data:</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th scope="col" class="col-xs-5">Key</th>
        <th scope="col" class="col-xs-5">Value</th>
        <th scope="col" class="col-xs-1">Indexed</th>
        <th scope="col" class="col-xs-1 text-center">Unset</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach($node->getDataArray() as $key => $value): ?>
        <tr>
          <th scope="row"><code><?=$key?></code></th>
          <th scope="row"><?=is_array($value) ? json_encode($value) : $value?></th>
          <th scope="row">
            <?=idx($node->getIndexedData(), $key) ? 'yes' : 'no'?>
          </th>
          <th scope="row">
            <form
              class="text-center"
              action="<?=AdminAjax::getURI('save', $node->getID())?>"
              method="POST">
              <?=GPSecurity::csrf()?>
              <input name="data_key_to_unset" type="hidden" value="<?=$key?>">
              <button class="btn btn-xs btn-danger">
                X
              </button>
            </form>
          </th>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<form action="<?=AdminAjax::getURI('save', $node->getID())?>" method="POST">
  <?=GPSecurity::csrf()?>
  <input name="data_key" type="text" placeholder="key">
  <input name="data_val" type="text" placeholder="val">
  <button class="btn btn-primary btn-sm">
    Set data
  </button>
</form>

<h3>Edges:</h3>
<div class="table-responsive">
  <table class="table table-bordered table-striped">
    <thead>
      <tr>
        <th scope="col" class="col-xs-6">Edge</th>
        <th scope="col" class="col-xs-5">To Node</th>
        <th scope="col" class="col-xs-1 text-center">Delete</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($node->getConnectedNodes($node::getEdgeTypes())
           as $e => $nodes): ?>
        <?php foreach ($nodes as $conn_node): ?>
          <tr>
            <th scope="row">
              <code><?=$node::getEdgeTypeByType($e)->getName().' - '.$e?></code>
            </th>
            <th scope="row">
              <a href="<?=Admin::getURI('node', $conn_node->getID())?>">
                Link
              </a>
              ID: <?=$conn_node->getID()?>
              type: <?=get_class($conn_node)?>
              <?=method_exists( $conn_node, '__toString' ) ? ' - '.$conn_node : ''?>
            </th>
            <th scope="row">
              <form
                class="text-center"
                action="<?=Admin::getURI('node', $node->getID())?>"
                method="POST">
                <?=GPSecurity::csrf()?>
                <input name="edge_type" type="hidden" value="<?=$e?>">
                <input
                  name="to_id"
                  type="hidden"
                  value="<?=$conn_node->getID()?>">
                <input name="delete" type="hidden">
                <button class="btn btn-xs btn-danger">
                  X
                </button>
              </form>
            </th>
          </tr>
        <?php endforeach; ?>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>
<form action="<?=AdminAjax::getURI('save', $node->getID())?>" method="POST">
  <?=GPSecurity::csrf()?>
  <input name="edge_type" type="text" placeholder="edge type">
  <input name="to_id" type="text" placeholder="To node ID">
  <button class="btn btn-primary btn-sm">
    Add edge
  </button>
</form>
