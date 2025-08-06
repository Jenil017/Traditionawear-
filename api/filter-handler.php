<?php
require_once '../config/db.php';

header('Content-Type: application/json');

$type = $_GET['type'] ?? $_POST['type'] ?? '';

try {
    switch ($type) {
        case 'categories':
            $stmt = $pdo->prepare("SELECT DISTINCT c.id, c.name FROM categories c 
                                   JOIN products p ON p.category_id = c.id 
                                   WHERE p.status = 'active' AND p.quantity_available > 0");
            $stmt->execute();
            $categories = $stmt->fetchAll();
            echo json_encode($categories);
            break;

        case 'sizes':
            $stmt = $pdo->query("SELECT size FROM products WHERE status = 'active' AND quantity_available > 0");
            $sizes = [];
            while ($row = $stmt->fetch()) {
                $product_sizes = explode(',', $row['size']);
                foreach ($product_sizes as $size) {
                    $size = trim($size);
                    if ($size) {
                        $sizes[$size] = true;
                    }
                }
            }
            $sizes = array_keys($sizes);
            sort($sizes, SORT_NATURAL);
            echo json_encode($sizes);
            break;

        case 'colors':
            $sql = "SELECT color FROM products WHERE status = 'active' AND quantity_available > 0";
            $params = [];

            if (!empty($_GET['category'])) {
                $sql .= " AND category_id = ?";
                $params[] = $_GET['category'];
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $colors = [];
            while ($row = $stmt->fetch()) {
                $product_colors = explode(',', $row['color']);
                foreach ($product_colors as $color) {
                    $color = trim(strtolower($color));
                    if ($color) {
                        $colors[$color] = true;
                    }
                }
            }
            $colors = array_keys($colors);
            sort($colors);
            echo json_encode($colors);
            break;

        case 'price-range':
            $stmt = $pdo->query("SELECT MIN(price_per_day) as min_price, MAX(price_per_day) as max_price 
                                FROM products WHERE status = 'active' AND quantity_available > 0");
            $range = $stmt->fetch();
            echo json_encode($range);
            break;

        case 'products':
        default:
            $where = ["status = 'active'", "quantity_available > 0"];
            $params = [];

            if (!empty($_POST['category'])) {
                $where[] = "category_id = ?";
                $params[] = $_POST['category'];
            }

            if (!empty($_POST['size'])) {
                $where[] = "FIND_IN_SET(?, size)";
                $params[] = $_POST['size'];
            }

            if (!empty($_POST['color'])) {
                $where[] = "FIND_IN_SET(?, color)";
                $params[] = $_POST['color'];
            }

            if (!empty($_POST['minPrice'])) {
                $where[] = "price_per_day >= ?";
                $params[] = $_POST['minPrice'];
            }

            if (!empty($_POST['maxPrice'])) {
                $where[] = "price_per_day <= ?";
                $params[] = $_POST['maxPrice'];
            }

            $sql = "SELECT * FROM products WHERE " . implode(" AND ", $where) . " ORDER BY created_at DESC";
            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);
            $products = $stmt->fetchAll();

            echo json_encode($products);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>