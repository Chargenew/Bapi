<?php
header('Content-Type: application/json');
require 'db.php';
date_default_timezone_set('Asia/Kolkata');

$method = $_SERVER['REQUEST_METHOD'];

$response = [];

switch ($method) {
    case 'GET':
        if (isset($_GET["start_date"]) && isset($_GET["end_date"])) {
            $startDate = $conn->real_escape_string($_GET["start_date"]);
            $endDate = $conn->real_escape_string($_GET["end_date"]);

            $sql = "
                (
                    SELECT b.bank, 
                        SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.before_bal ELSE 0 END) AS OpeningBalance, 
                        SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.after_bal ELSE 0 END) AS ClosingBalance, 
                        SUM(CASE WHEN t.transaction_type = 'OUT' AND t.category != 'Exchange' THEN t.amount ELSE 0 END) AS FundOut, 
                        SUM(CASE WHEN t.transaction_type = 'IN' AND t.category != 'Exchange' THEN t.amount ELSE 0 END) AS FundIn
                    FROM bank_balances b
                    LEFT JOIN transactions t ON b.bank = t.bank AND t.transaction_date BETWEEN '$startDate' AND '$endDate'
                    GROUP BY b.bank
                )
                UNION ALL
                (
                    SELECT 'Total' AS bank,
                        SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.before_bal ELSE 0 END) AS OpeningBalance, 
                        SUM(CASE WHEN t.transaction_type = 'OUT' THEN t.after_bal ELSE 0 END) AS ClosingBalance, 
                        SUM(CASE WHEN t.transaction_type = 'OUT' AND t.category != 'Exchange' THEN t.amount ELSE 0 END) AS FundOut, 
                        SUM(CASE WHEN t.transaction_type = 'IN' AND t.category != 'Exchange' THEN t.amount ELSE 0 END) AS FundIn
                    FROM transactions t
                    WHERE t.transaction_date BETWEEN '$startDate' AND '$endDate'
                );
            ";

            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $rows = $result->fetch_all(MYSQLI_ASSOC);

                // Define the headers
                $headers = ["Bank", "Opening Balance", "Closing Balance", "Fund OUT", "Fund IN", "Check", "Day Fund Hold"];

                // Prepare table rows
                $tbodyRows = [];
                foreach ($rows as $row) {
                    // Calculate the "Check" column
                    $check = $row['OpeningBalance'] - $row['ClosingBalance'] - $row['FundOut'] + $row['FundIn'];
                    $dayFund = $row["FundIn"] - $row["FundOut"];

                    $tr = '<tr>';
                    $tr .= '<td>' . htmlspecialchars($row['bank']) . '</td>';
                    $tr .= '<td>' . htmlspecialchars(number_format($row['OpeningBalance'], 2)) . '</td>';
                    $tr .= '<td>' . htmlspecialchars(number_format($row['ClosingBalance'], 2)) . '</td>';
                    $tr .= '<td>' . htmlspecialchars(number_format($row['FundOut'], 2)) . '</td>';
                    $tr .= '<td>' . htmlspecialchars(number_format($row['FundIn'], 2)) . '</td>';
                    $tr .= '<td>' . htmlspecialchars(number_format($check, 2)) . '</td>'; // Add "Check" column
                    $tr .= '<td>' . htmlspecialchars(number_format($dayFund, 2)) . '</td>'; // Add "DayFund Hold" column
                    $tr .= '</tr>';

                    $tbodyRows[] = $tr;
                }

                // Return JSON with headers and rows
                $response = [
                    'thead' => $headers,
                    'tbody' => $tbodyRows
                ];
            } else {
                $response = ['message' => 'No records found'];
            }
        } else {
            $response = ['message' => 'Missing start_date or end_date'];
        }
        break;

    default:
        $response = ['message' => 'Invalid request method'];
        break;
}

echo json_encode($response);

$conn->close();
?>
