<?

class Admin extends GPController {

  public function index() {
    $data = ['content' => GP::view('admin/node_view', [], true)];
    GP::view('layout/main', $data);
  }

}
