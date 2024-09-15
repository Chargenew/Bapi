<?php
header('Content-Type: application/json');
require 'db.php';
date_default_timezone_set('Asia/Kolkata');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET["start_date"]) && isset($_GET["end_date"]) && isset($_GET["condition"])) {
            $startDate = $_GET["start_date"];
            $endDate = $_GET["end_date"];
            $condition = $_GET["condition"];

            // Fetch the distinct subcategories for API, and distinct categories for Retail and Exchange
            $apiQuery = "";
            if ($condition == "in") {
                $apiQuery = "SELECT DISTINCT sub_category FROM transactions WHERE category = 'API' AND transaction_type = 'IN' AND transaction_date BETWEEN '$startDate' AND '$endDate';";

            } elseif ($condition == "out") {
                $apiQuery = "SELECT DISTINCT sub_category FROM transactions WHERE category = 'API' AND transaction_type = 'OUT' AND transaction_date BETWEEN '$startDate' AND '$endDate';";
            }
            $apiResult = $conn->query($apiQuery);

            $apiHeaders = [];
            if ($apiResult->num_rows > 0) {
                while ($row = $apiResult->fetch_assoc()) {
                    $apiHeaders[] = $row['sub_category'];
                }
            }

            // Fetch Retail and Exchange headers
            $otherQuery = "";
            if ($condition == "in") {
                $otherQuery = "SELECT DISTINCT category FROM transactions WHERE (category = 'Retail' OR category = 'Exchange') AND transaction_type = 'IN' AND transaction_date BETWEEN '$startDate' AND '$endDate';";
            
            } elseif ($condition == "out") {
                $otherQuery = "SELECT DISTINCT category FROM transactions WHERE (category = 'Lapus' OR category = 'Exchange') AND transaction_type = 'OUT' AND transaction_date BETWEEN '$startDate' AND '$endDate';";
            }
            $otherResult = $conn->query($otherQuery);

            $otherHeaders = [];
            if ($otherResult->num_rows > 0) {
                while ($row = $otherResult->fetch_assoc()) {
                    $otherHeaders[] = $row['category'];
                }
            }

            // Merge the headers into a single array
            $headers = array_merge(['Categories'], $otherHeaders, $apiHeaders);

            // Create a structure to store the amounts by category/subcategory
            $rows = [];
            $totals = [];

            // Initialize empty rows and totals for each header
            foreach ($headers as $header) {
                $rows[$header] = [];
                $totals[$header] = 0; // Initialize totals for each category/subcategory
            }

            $grandTotal = 0; // This will exclude 'Exchange'

            // Fetch amounts for Retail and Exchange categories
            $query = "";
            if ($condition == "in") {
                $query = "SELECT category, amount FROM transactions WHERE (category = 'Retail' OR category = 'Exchange') AND transaction_type = 'IN' AND transaction_date BETWEEN '$startDate' AND '$endDate' ORDER BY category;";
                
            } elseif ($condition == "out") {
                $query = "SELECT category, amount FROM transactions WHERE (category = 'Lapus' OR category = 'Exchange') AND transaction_type = 'OUT' AND transaction_date BETWEEN '$startDate' AND '$endDate' ORDER BY category;";
            }
            $result = $conn->query($query);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $category = $row['category'];
                    $amount = $row['amount'];

                    // Add the amount to the respective category row
                    $rows[$category][] = $amount;

                    // Add to the respective category's total
                    $totals[$category] += $amount;

                    // Add to grand total excluding 'Exchange'
                    if ($category !== 'Exchange') {
                        $grandTotal += $amount;
                    }
                }
            }

            // Fetch amounts for API subcategories
            $apiQuery = "";
            if ($condition == "in") {
                $apiQuery = "SELECT sub_category, amount FROM transactions WHERE category = 'API' AND transaction_type = 'IN' AND transaction_date BETWEEN '$startDate' AND '$endDate' ORDER BY sub_category;";
                
            } elseif ($condition == "out") {
                $apiQuery = "SELECT sub_category, amount FROM transactions WHERE category = 'API' AND transaction_type = 'OUT' AND transaction_date BETWEEN '$startDate' AND '$endDate' ORDER BY sub_category;";
            }
            $apiResult = $conn->query($apiQuery);

            if ($apiResult->num_rows > 0) {
                while ($row = $apiResult->fetch_assoc()) {
                    $sub_category = $row['sub_category'];
                    $amount = $row['amount'];

                    // Add the amount to the respective API subcategory row
                    $rows[$sub_category][] = $amount;

                    // Add to the respective subcategory's total
                    $totals[$sub_category] += $amount;

                    // Add to the grand total
                    $grandTotal += $amount;
                }
            }

            // Prepare table body rows where each row corresponds to one transaction, ensuring that amounts align with their categories
            // Prepare table body rows
            $tbodyRows = [];
            $maxRowCount = max(array_map('count', $rows)); // Get the maximum row count to ensure even rows

            for ($i = 0; $i < $maxRowCount; $i++) {
                $tr = '<tr>';
                $tr .= '<td></td>'; // Empty first column for "Categories"
                foreach (array_slice($headers, 1) as $header) {
                    $tr .= isset($rows[$header][$i]) ? "<td>{$rows[$header][$i]}</td>" : '<td></td>';
                }
                $tr .= '</tr>';
                $tbodyRows[] = $tr;
            }

            // Add the total row at the end
            $totalRow = '<tr><td><strong>Total</strong></td>';
            foreach (array_slice($headers, 1) as $header) {
                $totalRow .= "<td><strong>{$totals[$header]}</strong></td>";
            }
            $totalRow .= '</tr>';

            // Add the grand total (excluding Exchange)
            $grandTotalRow = "<tr><td colspan='" . count($headers) . "'><strong>Grand Total (Excluding Exchange): {$grandTotal}</strong></td></tr>";

            // Append the totals row and grand total row to the tbody rows
            $tbodyRows[] = $totalRow;
            $tbodyRows[] = $grandTotalRow;

            // Return the generated headers and rows in JSON format
            echo json_encode([
                "thead" => $headers, 
                "tbody" => $tbodyRows // Ensure tbody is an array
            ]);

        }
        break;
}
?>
