<?php
// Debug for db.php include path
$dbPath = realpath(__DIR__ . '/../config/db.php');
echo "<pre>Resolved db.php path: ".$dbPath."\n";
echo "File exists: ".(file_exists($dbPath) ? 'YES' : 'NO')."</pre>";
require $dbPath;
require "../vendor/fpdf/fpdf.php";
require "../vendor/PHPMailer/PHPMailerAutoload.php"; // Or composer autoload if you use it

$id = (int)($_GET['id'] ?? 0);

// Fetch booking/details as before
$stmt = $pdo->prepare("SELECT b.*,u.name as uname,u.email,u.address,p.product_name,p.price_per_day
   FROM bookings b JOIN users u ON b.user_id=u.id JOIN products p ON b.product_id=p.id WHERE b.id=?");
$stmt->execute([$id]);
if(!$b = $stmt->fetch()) { exit("Booking not found"); }

// 1. Generate PDF file in memory
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(100,10,"Rameshwar Traditional Wear - Invoice",0,1);
$pdf->SetFont('Arial','',12);
$pdf->Cell(100,8,"Order #: ".$b['id'],0,1);
$pdf->Cell(100,8,"Customer: ".$b['uname'],0,1);
$pdf->Cell(100,8,"Email: ".$b['email'],0,1);
$pdf->Cell(100,8,"Product: ".$b['product_name'],0,1);
$pdf->Cell(100,8,"Rental Dates: {$b['start_date']} to {$b['end_date']}",0,1);
$pdf->Cell(100,8,"Total: ₹" . number_format($b['total_price'],2),0,1);
// ...you can add more styling/rows as needed

// Output PDF to a variable
$pdfdoc = $pdf->Output('', 'S'); // S = return as string (no download)

// 2. Prepare and Send Email (with PHPMailer)
$mail = new PHPMailer();
$mail->setFrom('traditionawear2025@gmail.com', 'Rameshwar Traditional Wear');
$mail->addAddress($b['email'], $b['uname']);
$mail->Subject = "Your Rental Invoice (Order #{$b['id']})";
$mail->Body = "Dear {$b['uname']},\n\nThanks for your booking. Attached is your invoice.\n\nRegards,\nRameshwar Traditional Wear";
$mail->addStringAttachment($pdfdoc, "invoice_{$b['id']}.pdf"); // attach PDF from string
if ($mail->send()) {
    echo "<div class='alert alert-success'>Invoice sent to {$b['email']}!</div>";
} else {
    echo "<div class='alert alert-danger'>Failed to send: ".$mail->ErrorInfo."</div>";
}
?> 
 