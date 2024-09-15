<?php
header('Content-Type: application/json');
require 'db.php';
date_default_timezone_set('Asia/Kolkata');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            $id = $_GET['id'];
            $sql = "SELECT * FROM transactions WHERE id=$id";
            $result = $conn->query($sql);
            echo json_encode($result->fetch_assoc());
        } elseif (isset($_GET['picklist_name'])) {
            $picklist_name = $_GET['picklist_name'];
            $parent_value = isset($_GET['parent_value']) ? $_GET['parent_value'] : null;
            if ($parent_value) {
                $sql = "SELECT picklist_value FROM picklist_values WHERE picklist_name='$picklist_name' AND parent_picklist_value='$parent_value'";
            } else {
                $sql = "SELECT picklist_value FROM picklist_values WHERE picklist_name='$picklist_name'";
            }
            $result = $conn->query($sql);
            $values = [];
            while ($row = $result->fetch_assoc()) {
                $values[] = $row['picklist_value'];
            }
            echo json_encode($values);
        } else {
            $transactions = [];
            $balances = [];
            $date_filter = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');

            $sql = "SELECT * FROM transactions WHERE transaction_date='$date_filter'";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $transactions[] = $row;
            }

            $sql = "SELECT * FROM bank_balances";
            $result = $conn->query($sql);
            while ($row = $result->fetch_assoc()) {
                $balances[] = $row;
            }

            echo json_encode(['transactions' => $transactions, 'balances' => $balances]);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents("php://input"), true);
        $transaction_type = $data['transaction_type'];
        $category = $data['category'];
        $sub_category = $data['sub_category'];
        $bank = $data['bank'];
        $amount = $data['amount'];
        $reference_number = $data['reference_number'];
        $remarks = $data['remarks'];
        $timestamp = date('Y-m-d H:i:s'); // Adding timestamp

        $sql = "INSERT INTO transactions (transaction_type, category, sub_category, bank, amount, reference_number, remarks, timestamp)
                VALUES ('$transaction_type', '$category', '$sub_category', '$bank', $amount, '$reference_number', '$remarks', '$timestamp')";
        $conn->query($sql);

        updateBankBalance($bank, $transaction_type, $amount);

        echo json_encode(["message" => "Transaction added successfully"]);
        break;

    case 'PUT':
        $data = json_decode(file_get_contents("php://input"), true);
        $id = $data['id'];
        $transaction_type = $data['transaction_type'];
        $category = $data['category'];
        $sub_category = $data['sub_category'];
        $bank = $data['bank'];
        $amount = $data['amount'];
        $reference_number = $data['reference_number'];
        $remarks = $data['remarks'];
        $timestamp = date('Y-m-d H:i:s'); // Adding timestamp

        editTransaction($id, $transaction_type, $category, $sub_category, $bank, $amount, $reference_number, $remarks, $timestamp);
        echo json_encode(["message" => "Transaction updated successfully"]);
        break;

    case 'DELETE':
        $id = $_GET['id'];
        deleteTransaction($id);
        echo json_encode(["message" => "Transaction deleted successfully"]);
        break;
}

function updateBankBalance($bank, $transaction_type, $amount) {
    global $conn;
    $sign = ($transaction_type == 'IN') ? 1 : -1;
    $amount = $sign * $amount;

    $sql = "SELECT * FROM bank_balances WHERE bank='$bank'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $sql = "UPDATE bank_balances SET balance = balance + $amount WHERE bank='$bank'";
    } else {
        $sql = "INSERT INTO bank_balances (bank, balance) VALUES ('$bank', $amount)";
    }
    $conn->query($sql);
}

function editTransaction($id, $transaction_type, $category, $sub_category, $bank, $amount, $reference_number, $remarks, $timestamp) {
    global $conn;

    $sql = "SELECT * FROM transactions WHERE id=$id";
    $result = $conn->query($sql);
    $transaction = $result->fetch_assoc();

    if ($transaction) {
        $old_amount = $transaction['amount'];
        $old_type = $transaction['transaction_type'];
        $old_bank = $transaction['bank'];

        updateBankBalance($old_bank, ($old_type == 'IN' ? 'OUT' : 'IN'), $old_amount);

        $sql = "UPDATE transactions SET 
                    transaction_type='$transaction_type', 
                    category='$category', 
                    sub_category='$sub_category', 
                    bank='$bank', 
                    amount=$amount, 
                    reference_number='$reference_number', 
                    remarks='$remarks',
                    timestamp='$timestamp' 
                WHERE id=$id";
        $conn->query($sql);

        updateBankBalance($bank, $transaction_type, $amount);
    }
}

function deleteTransaction($id) {
    global $conn;

    $sql = "SELECT * FROM transactions WHERE id=$id";
    $result = $conn->query($sql);
    $transaction = $result->fetch_assoc();

    if ($transaction) {
        $amount = $transaction['amount'];
        $transaction_type = $transaction['transaction_type'];
        $bank = $transaction['bank'];

        updateBankBalance($bank, ($transaction_type == 'IN' ? 'OUT' : 'IN'), $amount);

        $sql = "DELETE FROM transactions WHERE id=$id";
        $conn->query($sql);
    }
}
?>
