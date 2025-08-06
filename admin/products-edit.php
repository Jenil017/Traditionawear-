<?php include "includes/admin-header.php"; include "../config/db.php";
$id = (int)$_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM products WHERE id=?"); $stmt->execute([$id]);
if(!$p = $stmt->fetch()) die("Product not found");

if($_SERVER['REQUEST_METHOD']=='POST'){
    // Image upload (optional)
    $imgName = $p['image_url'];
    if(!empty($_FILES['image']['name'])){
        $up = '../assets/images/'.basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $up);
        $imgName = "assets/images/".basename($_FILES['image']['name']);
    }
    $stmt = $pdo->prepare("UPDATE products SET product_number=?,category_id=?,product_name=?,description=?,image_url=?,size=?,color=?,price_per_day=?,quantity_available=?,status=? WHERE id=?");
    $stmt->execute([
        $_POST['product_number'], $_POST['category_id'], $_POST['product_name'], $_POST['description'], $imgName,
        $_POST['size'], $_POST['color'], $_POST['price_per_day'],
        $_POST['quantity_available'], $_POST['status'], $id
    ]);
    echo "<div class='alert alert-success'>Updated!</div>";
}
?>
<h2>Edit Product</h2>
<div class="card" style="max-width:700px;margin:auto;">
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Product Number:</label>
        <input name="product_number" value="<?=htmlspecialchars($p['product_number'])?>", class="form-control" required>
        <small class="text-muted">Unique product identifier</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Category:</label>
        <select name="category_id" class="form-select">
          <?php foreach($pdo->query("SELECT id,name FROM categories") as $c){
            $sel = ($c['id']==$p['category_id'])?'selected':'';
            echo "<option value='{$c['id']}' $sel>{$c['name']}</option>"; } ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Name:</label>
        <input name="product_name" value="<?=htmlspecialchars($p['product_name'])?>", class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description:</label>
        <textarea name="description" class="form-control"><?=htmlspecialchars($p['description'])?></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Image:</label>
        <input type="file" name="image" class="form-control"> <?php $images = array_map(function($img) {
    $img = ltrim(trim($img), '.');
    if (strpos($img, '/rtwrs_web/') !== 0) {
        $img = '/rtwrs_web/' . ltrim($img, '/');
    }
    return $img;
}, explode(',', $p['image_url'])); ?>
<small class="d-block mt-2">Current Images:</small>
        <div class="row mt-2">
          <?php foreach($images as $img): ?>
            <div class="col-md-4 mb-2">
              <img src="<?= htmlspecialchars($img) ?>" alt="Product Image" class="img-fluid rounded border" style="width:100%; height:auto; max-height:200px; object-fit:contain;">
            </div>
          <?php endforeach; ?>
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label">Size:</label>
        <input name="size" value="<?=$p['size']?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Color:</label>
        <input name="color" value="<?=$p['color']?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Price/Day:</label>
        <input type="number" name="price_per_day" value="<?=$p['price_per_day']?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Qty:</label>
        <input type="number" name="quantity_available" value="<?=$p['quantity_available']?>" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Status:</label>
        <select name="status" class="form-select">
          <option value="active" <?=$p['status']=='active'?'selected':''?>>Active</option>
          <option value="inactive" <?=$p['status']=='inactive'?'selected':''?>>Inactive</option>
        </select>
      </div>
      <button type="submit" class="btn btn-primary">Update</button>
    </form>
  </div>
</div>

