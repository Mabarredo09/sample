<?php
// dashboard.php - Main dashboard after login
require_once 'config.php';
requireLogin();

// Get user's loans as borrower
$borrowerLoans = [];
$sql = "SELECT l.LoanID, lt.TypeName, l.Amount, l.InterestRate, l.Term, l.StartDate, l.Status,
               u.FirstName AS LenderFirstName, u.LastName AS LenderLastName
        FROM Loans l
        JOIN LoanTypes lt ON l.LoanTypeID = lt.LoanTypeID
        LEFT JOIN Users u ON l.LenderID = u.UserID
        WHERE l.BorrowerID = ?
        ORDER BY l.CreationDate DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $borrowerLoans[] = $row;
    }

    $stmt->close();
}

// Get user's loans as lender
$lenderLoans = [];
$sql = "SELECT l.LoanID, lt.TypeName, l.Amount, l.InterestRate, l.Term, l.StartDate, l.Status,
               u.FirstName AS BorrowerFirstName, u.LastName AS BorrowerLastName
        FROM Loans l
        JOIN LoanTypes lt ON l.LoanTypeID = lt.LoanTypeID
        JOIN Users u ON l.BorrowerID = u.UserID
        WHERE l.LenderID = ?
        ORDER BY l.CreationDate DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $lenderLoans[] = $row;
    }

    $stmt->close();
}

// Get upcoming payments for borrower
$upcomingPayments = [];
$sql = "SELECT sp.PaymentID, sp.DueDate, sp.Amount, sp.Status, l.LoanID
        FROM ScheduledPayments sp
        JOIN PaymentSchedules ps ON sp.ScheduleID = ps.ScheduleID
        JOIN Loans l ON ps.LoanID = l.LoanID
        WHERE l.BorrowerID = ? AND sp.Status = 'pending' AND sp.DueDate >= CURDATE()
        ORDER BY sp.DueDate ASC
        LIMIT 5";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $upcomingPayments[] = $row;
    }

    $stmt->close();
}

// Get recent transactions
$recentTransactions = [];
$sql = "SELECT ap.TransactionID, ap.Amount, ap.PaymentDate, ap.PaymentMethod, l.LoanID
        FROM ActualPayments ap
        JOIN Loans l ON ap.LoanID = l.LoanID
        WHERE (l.BorrowerID = ? OR l.LenderID = ?)
        ORDER BY ap.PaymentDate DESC
        LIMIT 5";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("ii", $_SESSION['user_id'], $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $recentTransactions[] = $row;
    }

    $stmt->close();
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Lending System</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <?php include 'includes/header.php'; ?>

    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["user_name"]); ?>!</h1>

        <div class="dashboard-summary">
            <div class="card">
                <h3>My Loans (as Borrower)</h3>
                <p><?php echo count($borrowerLoans); ?> active loans</p>
            </div>
            <div class="card">
                <h3>My Investments (as Lender)</h3>
                <p><?php echo count($lenderLoans); ?> active investments</p>
            </div>
            <div class="card">
                <h3>Upcoming Payments</h3>
                <p><?php echo count($upcomingPayments); ?> payments due</p>
            </div>
        </div>

        <div class="dashboard-content">
            <!-- Borrower Loans Section -->
            <section class="loans-section">
                <h2>My Loans</h2>
                <?php if (empty($borrowerLoans)): ?>
                    <p>You don't have any loans yet. <a href="apply_loan.php">Apply for a loan</a>.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Rate</th>
                                    <th>Term</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th>Lender</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($borrowerLoans as $loan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($loan['LoanID']); ?></td>
                                        <td><?php echo htmlspecialchars($loan['TypeName']); ?></td>
                                        <td><?php echo '$' . number_format($loan['Amount'], 2); ?></td>
                                        <td><?php echo $loan['InterestRate'] . '%'; ?></td>
                                        <td><?php echo htmlspecialchars($loan['Term']); ?> months</td>
                                        <td><?php echo date('M d, Y', strtotime($loan['StartDate'])); ?></td>
                                        <td><span
                                                class="status-<?php echo strtolower($loan['Status']); ?>"><?php echo htmlspecialchars($loan['Status']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($loan['LenderFirstName'] . ' ' . $loan['LenderLastName']); ?>
                                        </td>
                                        <td>
                                            <a href="view_loan.php?id=<?php echo $loan['LoanID']; ?>" class="btn btn-sm">View
                                                Details</a>
                                            <?php if ($loan['Status'] == 'Active'): ?>
                                                <a href="make_payment.php?loan=<?php echo $loan['LoanID']; ?>"
                                                    class="btn btn-sm btn-primary">Make Payment</a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Lender Investments Section -->
            <section class="loans-section">
                <h2>My Investments</h2>
                <?php if (empty($lenderLoans)): ?>
                    <p>You haven't invested in any loans yet. <a href="browse_loans.php">Browse available loans</a>.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Rate</th>
                                    <th>Term</th>
                                    <th>Start Date</th>
                                    <th>Status</th>
                                    <th>Borrower</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lenderLoans as $loan): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($loan['LoanID']); ?></td>
                                        <td><?php echo htmlspecialchars($loan['TypeName']); ?></td>
                                        <td><?php echo '$' . number_format($loan['Amount'], 2); ?></td>
                                        <td><?php echo $loan['InterestRate'] . '%'; ?></td>
                                        <td><?php echo htmlspecialchars($loan['Term']); ?> months</td>
                                        <td><?php echo date('M d, Y', strtotime($loan['StartDate'])); ?></td>
                                        <td><span
                                                class="status-<?php echo strtolower($loan['Status']); ?>"><?php echo htmlspecialchars($loan['Status']); ?></span>
                                        </td>
                                        <td><?php echo htmlspecialchars($loan['BorrowerFirstName'] . ' ' . $loan['BorrowerLastName']); ?>
                                        </td>
                                        <td>
                                            <a href="view_loan.php?id=<?php echo $loan['LoanID']; ?>" class="btn btn-sm">View
                                                Details</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Upcoming Payments Section -->
            <section class="payments-section">
                <h2>Upcoming Payments</h2>
                <?php if (empty($upcomingPayments)): ?>
                    <p>You have no upcoming payments due.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Payment ID</th>
                                    <th>Loan ID</th>
                                    <th>Due Date</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($upcomingPayments as $payment): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($payment['PaymentID']); ?></td>
                                        <td><?php echo htmlspecialchars($payment['LoanID']); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($payment['DueDate'])); ?></td>
                                        <td><?php echo '$' . number_format($payment['Amount'], 2); ?></td>
                                        <td><span
                                                class="status-<?php echo strtolower($payment['Status']); ?>"><?php echo htmlspecialchars($payment['Status']); ?></span>
                                        </td>
                                        <td>
                                            <a href="make_payment.php?payment=<?php echo $payment['PaymentID']; ?>"
                                                class="btn btn-sm btn-primary">Pay Now</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>

            <!-- Recent Transactions Section -->
            <section class="transactions-section">
                <h2>Recent Transactions</h2>
                <?php if (empty($recentTransactions)): ?>
                    <p>You don't have any recent transactions.</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Transaction ID</th>
                                    <th>Loan ID</th>
                                    <th>Amount</th>
                                    <th>Date</th>
                                    <th>Method</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($transaction['TransactionID']); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['LoanID']); ?></td>
                                        <td><?php echo '$' . number_format($transaction['Amount'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($transaction['PaymentDate'])); ?></td>
                                        <td><?php echo htmlspecialchars($transaction['PaymentMethod']); ?></td>
                                        <td>
                                            <a href="view_transaction.php?id=<?php echo $transaction['TransactionID']; ?>"
                                                class="btn btn-sm">View Receipt</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </section>
        </div>
    </div>

    <?php include 'includes/footer.php'; ?>

    <script src="js/script.js"></script>
</body>

</html>