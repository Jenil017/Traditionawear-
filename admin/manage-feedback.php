<?php include "includes/admin-header.php"; include "../config/db.php"; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-comments me-2"></i>Customer Feedback</h2>
</div>

<div class="admin-table">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>ID</th>
                    <th>User</th>
                    <th>Product</th>
                    <th>Rating</th>
                    <th>Feedback</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody class="table-light">
                <?php
                try {
                    $q = "SELECT f.*, u.name as uname, p.product_name FROM feedback f
                        JOIN users u ON f.user_id=u.id
                        JOIN products p ON f.product_id=p.id
                        ORDER BY f.created_at DESC";
                    $stmt = $pdo->query($q);
                    $feedbacks = $stmt->fetchAll();
                    
                    if (empty($feedbacks)) {
                        echo "<tr><td colspan='7' class='text-center py-4 text-muted'>";
                        echo "<i class='fas fa-comment-slash fa-2x mb-2'></i><br>";
                        echo "No feedback found.";
                        echo "</td></tr>";
                    } else {
                        foreach($feedbacks as $f) {
                            // Generate star rating display
                            $stars = '';
                            for ($i = 1; $i <= 5; $i++) {
                                if ($i <= $f['rating']) {
                                    $stars .= '<i class="fas fa-star text-warning"></i>';
                                } else {
                                    $stars .= '<i class="far fa-star text-muted"></i>';
                                }
                            }
                            
                            // Truncate feedback for table display
                            $truncatedFeedback = strlen($f['feedback']) > 80 
                                ? substr($f['feedback'], 0, 80) . '...' 
                                : $f['feedback'];
                            
                            echo "<tr>";
                            echo "<td data-label='ID'><span class='badge bg-secondary'>#{$f['id']}</span></td>";
                            echo "<td data-label='User'><strong>" . htmlspecialchars($f['uname']) . "</strong></td>";
                            echo "<td data-label='Product'><strong>" . htmlspecialchars($f['product_name']) . "</strong></td>";
                            echo "<td data-label='Rating'><div class='d-flex align-items-center'>{$stars}<span class='ms-2 badge bg-info'>{$f['rating']}/5</span></div></td>";
                            echo "<td data-label='Feedback'><span title='" . htmlspecialchars($f['feedback']) . "'>" . htmlspecialchars($truncatedFeedback) . "</span></td>";
                            echo "<td data-label='Date'><small class='text-muted'>" . date('M j, Y g:i A', strtotime($f['created_at'])) . "</small></td>";
                            echo "<td data-label='Actions'>";
                            echo "<div class='btn-group btn-group-sm'>";
                            echo "<button class='btn btn-outline-info' onclick='viewFeedback({$f['id']})' title='View Full Feedback'>";
                            echo "<i class='fas fa-eye'></i>";
                            echo "</button>";
                            echo "<a href='mailto:" . htmlspecialchars($f['uname']) . "' class='btn btn-outline-success' title='Reply to Customer'>";
                            echo "<i class='fas fa-reply'></i>";
                            echo "</a>";
                            echo "<a href='feedback-delete.php?id={$f['id']}' class='btn btn-outline-danger' onclick='return confirm(\"Are you sure you want to delete feedback from: " . htmlspecialchars($f['uname']) . "?\")' title='Delete Feedback'>";
                            echo "<i class='fas fa-trash'></i>";
                            echo "</a>";
                            echo "</div>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                } catch (PDOException $e) {
                    echo "<tr><td colspan='7' class='text-center py-4 text-danger'>";
                    echo "<i class='fas fa-exclamation-triangle fa-2x mb-2'></i><br>";
                    echo "Error loading feedback: " . htmlspecialchars($e->getMessage());
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>
<!-- Feedback Details Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Full Feedback Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="feedbackContent"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteFeedbackModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-danger">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete this feedback?</p>
                <p><strong>From:</strong> <span id="deleteFeedbackUser"></span></p>
                <p class="text-muted">This action cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a href="#" id="confirmDeleteFeedbackBtn" class="btn btn-danger">Delete</a>
            </div>
        </div>
    </div>
</div>




