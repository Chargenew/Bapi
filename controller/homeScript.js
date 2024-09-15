async function fetchPicklistValues(picklistName, parentValue = null) {
    let url = `../server/api.php?picklist_name=${picklistName}`;
    if (parentValue) {
        url += `&parent_value=${parentValue}`;
    }
    const response = await fetch(url);
    return response.json();
}

async function populateDropdown(selectElementId, picklistName, parentValue = null) {
    const selectElement = document.getElementById(selectElementId);
    selectElement.innerHTML = '';
    const values = await fetchPicklistValues(picklistName, parentValue);
    values.forEach(value => {
        const option = document.createElement('option');
        option.value = value;
        option.textContent = value;
        selectElement.appendChild(option);
    });
}

async function fetchTransactions(date) {
    const response = await fetch(`../server/api.php?date=${date}`);
    const data = await response.json();
    return data.transactions;
}

async function fetchBalances() {
    const response = await fetch('../server/api.php');
    const data = await response.json();
    return data.balances;
}

function populateTable(tableId, data, columns) {
    const tableBody = document.getElementById(tableId);
    tableBody.innerHTML = '';
    data.forEach(item => {
        const row = document.createElement('tr');
        columns.forEach(column => {
            const cell = document.createElement('td');
            cell.textContent = item[column];
            row.appendChild(cell);
        });

        // Add delete button with onclick event
        // const deleteButton = document.createElement('button');
        // deleteButton.textContent = 'Delete';
        // deleteButton.addEventListener('click', () => deleteTransaction(item.id));
        // const actionCell = document.createElement('td');
        // actionCell.appendChild(deleteButton);
        // row.appendChild(actionCell);

        tableBody.appendChild(row);
    });
}

async function deleteTransaction(id) {
    if (confirm('Are you sure you want to delete this transaction?')) {
        await fetch(`server/api.php?id=${id}`, {
            method: 'DELETE'
        });

        populateTransactions();
        populateBalances();
    }
}

document.getElementById('transaction-form').addEventListener('submit', async (event) => {
    event.preventDefault();

    const id = document.getElementById('transaction-id').value;
    const transactionType = document.getElementById('transaction-type').value;
    const category = document.getElementById('category').value;
    const subCategory = document.getElementById('sub-category').value;
    const bank = document.getElementById('bank').value;
    const amount = document.getElementById('amount').value;
    const referenceNumber = document.getElementById('reference-number').value;
    const remarks = document.getElementById('remarks').value;
    const timestamp = document.getElementById('timestamp').value;

    const payload = {
        transaction_type: transactionType,
        category: category,
        sub_category: subCategory,
        bank: bank,
        amount: parseFloat(amount),
        reference_number: referenceNumber,
        remarks: remarks,
        timestamp: timestamp
    };

    let method = 'POST';
    let url = '../server/api.php';

    if (id) {
        payload.id = parseInt(id);
        method = 'PUT';
        url += `?id=${id}`;
    }

    await fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    });

    document.getElementById('transaction-form').reset();
    populateTransactions();
    populateBalances();
});

async function populateTransactions() {
    const date = document.getElementById('transaction-date').value;
    const transactions = await fetchTransactions(date);
    populateTable('transactions-table', transactions, ['id', 'transaction_type', 'category', 'sub_category', 'bank', 'amount', 'reference_number', 'remarks', 'timestamp']);
}

async function populateBalances() {
    const balances = await fetchBalances();
    populateTable('balances-table', balances, ['bank', 'balance']);
}

async function populateCategory() {
    const transactionType = document.getElementById('transaction-type').value;
    await populateDropdown('category', 'category', transactionType);
    await populateSubCategory();
}

async function populateSubCategory() {
    const category = document.getElementById('category').value;
    await populateDropdown('sub-category', 'sub_category', category);
}

document.addEventListener('DOMContentLoaded', async () => {
    await populateDropdown('transaction-type', 'transaction_type');
    await populateDropdown('bank', 'bank');

    document.getElementById('transaction-type').addEventListener('change', populateCategory);
    document.getElementById('category').addEventListener('change', populateSubCategory);

    populateTransactions();
    populateBalances();
});