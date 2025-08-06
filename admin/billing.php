<?php include "includes/admin-header.php"; include "../config/db.php"; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-money-check-alt me-2"></i>Billing / Invoices</h2>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>User</th>
                    <th>Total</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT b.*, u.name as uname FROM bookings b JOIN users u ON b.user_id=u.id ORDER BY b.created_at DESC");
                    $bookings = $stmt->fetchAll();
                    
                    if (empty($bookings)) {
                        echo "<tr><td colspan='5' class='text-center py-4 text-muted'>";
                        echo "<i class='fas fa-inbox fa-2x mb-2'></i><br>";
                        echo "No billing records found.";
                        echo "</td></tr>";
                    } else {
                        foreach($bookings as $o) {
                            $statusClass = '';
                            switch($o['status']) {
                                case 'pending': $statusClass = 'badge-pending'; break;
                                case 'confirmed': $statusClass = 'badge-confirmed'; break;
                                case 'cancelled': $statusClass = 'badge-cancelled'; break;
                                case 'completed': $statusClass = 'badge-completed'; break;
                                default: $statusClass = 'bg-secondary';
                            }
                            
                            echo "<tr>";
                            echo "<td data-label='Order ID'><span class='badge bg-primary'>#{$o['id']}</span></td>";
                            echo "<td data-label='User'><strong>" . htmlspecialchars($o['uname']) . "</strong></td>";
                            echo "<td data-label='Total'><strong class='text-success'>₹" . number_format($o['total_price'], 2) . "</strong></td>";
                            echo "<td data-label='Date'><small class='text-muted'>" . date('M j, Y g:i A', strtotime($o['created_at'])) . "</small></td>";
                            echo "<td data-label='Actions'>";
                            echo "<div class='btn-group btn-group-sm'>";
                            echo "<a href='send-bill.php?id={$o['id']}' class='btn btn-outline-success' title='Send PDF Invoice'>";
                            echo "<i class='fas fa-file-pdf me-1'></i>Send PDF";
                            echo "</a>";
                            echo "<a href='manage-orders.php?view={$o['id']}' class='btn btn-outline-info' title='View Details'>";
                            echo "<i class='fas fa-eye'></i>";
                            echo "</a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='5' class='text-center py-4 text-danger'>";
                    echo "<i class='fas fa-exclamation-triangle fa-2x mb-2'></i><br>";
                    echo "Error loading billing records: " . htmlspecialchars($e->getMessage());
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

