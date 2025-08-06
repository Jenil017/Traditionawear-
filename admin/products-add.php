<?php include "includes/admin-header.php"; include "../config/db.php";
if($_SERVER['REQUEST_METHOD']=='POST'){
    // Handle image upload (single for demo; for multiple, use array and loop)
    $imgName = '';
    if(!empty($_FILES['image']['name'])){
        $up = '../assets/images/'.basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $up);
        $imgName = "assets/images/".basename($_FILES['image']['name']);
        // Always store as assets/images/...
    }
    // When displaying, always normalize to webroot-relative:
    $displayImg = $imgName ? ('/rtwrs_web/' . ltrim($imgName, '/')) : '';

    $stmt = $pdo->prepare("INSERT INTO products 
    (product_number, category_id, product_name, description, image_url, size, color, price_per_day, quantity_available, status)
    VALUES (?,?,?,?,?,?,?,?,?,?)");
    $stmt->execute([
        $_POST['product_number'], $_POST['category_id'], $_POST['product_name'], $_POST['description'],
        $imgName, $_POST['size'], $_POST['color'],
        $_POST['price_per_day'], $_POST['quantity_available'], 'active'
    ]);
    echo "<div class='alert alert-success'>Product added!</div>";
}
?>
<h2>Add Product</h2>
<div class="card" style="max-width:700px;margin:auto;">
  <div class="card-body">
    <form method="POST" enctype="multipart/form-data">
      <div class="mb-3">
        <label class="form-label">Product Number:</label>
        <input name="product_number" class="form-control" placeholder="e.g. PRD001" required>
        <small class="text-muted">Unique product identifier (e.g. PRD001, PRD002)</small>
      </div>
      <div class="mb-3">
        <label class="form-label">Category:</label>
        <select name="category_id" class="form-select">
          <?php foreach($pdo->query("SELECT id,name FROM categories") as $c)
            echo "<option value='{$c['id']}'>{$c['name']}</option>"; ?>
        </select>
      </div>
      <div class="mb-3">
        <label class="form-label">Name:</label>
        <input name="product_name" class="form-control" required>
      </div>
      <div class="mb-3">
        <label class="form-label">Description:</label>
        <textarea name="description" class="form-control"></textarea>
      </div>
      <div class="mb-3">
        <label class="form-label">Image:</label>
        <input type="file" name="image" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Size:</label>
        <input name="size" placeholder="eg. S,M,L" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Color:</label>
        <input name="color" placeholder="eg. blue,red" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Price/Day:</label>
        <input type="number" name="price_per_day" class="form-control">
      </div>
      <div class="mb-3">
        <label class="form-label">Qty:</label>
        <input type="number" name="quantity_available" class="form-control">
      </div>
      <button type="submit" class="btn btn-success">Add Product</button>
    </form>
  </div>
</div>
<?php include "includes/admin-footer.php"; ?>
