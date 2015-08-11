<form method="POST" action="<?=Users::URI()->create()?>">
  <label>New User</label>
  <label>Email:</label>
  <input type="email" name="email" required>
  <label>Password:</label>
  <input type="password" name="password" required>
  <button>Submit</button>
  <?=GPSecurity::csrf()?>
</form>

<form method="POST" action="<?=Users::URI()->login()?>">
  <label>Existing User</label>
  <label>Email:</label>
  <input type="email" name="email" required>
  <label>Password:</label>
  <input type="password" name="password" required>
  <button>Submit</button>
  <?=GPSecurity::csrf()?>
</form>