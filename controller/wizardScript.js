async function fetchTable(condition, start_date, end_date, selectedDateRange, theads, tbodys) {
    let startDate = document.getElementById(start_date).value;
    let endDate = document.getElementById(end_date).value;

    // If only one date is provided, adjust the dates to the start and end of the respective month.
    if (startDate && !endDate) {
        let dateObj = new Date(startDate);
        startDate = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-01`;
        endDate = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).toISOString().slice(0, 10); // End of the month
    } else if (!startDate && endDate) {
        let dateObj = new Date(endDate);
        startDate = `${dateObj.getFullYear()}-${String(dateObj.getMonth() + 1).padStart(2, '0')}-01`;
        endDate = new Date(dateObj.getFullYear(), dateObj.getMonth() + 1, 0).toISOString().slice(0, 10); // End of the month
    }

    // if (!startDate || !endDate) {
    //     alert('Please select both start and end dates.');
    //     return;
    // }

    document.getElementById(selectedDateRange).textContent = `${startDate} to ${endDate}`;

    let apiName = null;

    switch (condition) {
        case "balance": apiName = "bankBalanceAPI"; break;
        case "expense": apiName = "expensesApi"; break;
        default: apiName = "fundInOutApi"; break;
    }

    const response = await fetch(`../server/${apiName}.php?start_date=${startDate}&end_date=${endDate}&condition=${condition}`);

    // if (condition === 'balance') {
    //     response = await fetch(`../server/bankBalanceAPI.php?start_date=${startDate}&end_date=${endDate}`);
    // } else {
    //     response = await fetch(`../server/fundInOutApi.php?start_date=${startDate}&end_date=${endDate}&condition=${condition}`);
    // }

    const data = await response.json();

    const thead = document.getElementById(theads);
    const tbody = document.getElementById(tbodys);

    thead.innerHTML = '';
    tbody.innerHTML = '';

    if (data.message) {
        thead.innerHTML = '<tr><th colspan="5">' + data.message + '</th></tr>';
        return;
    }

    // Populate table headers
    let headerRow = document.createElement('tr');
    data.thead.forEach(header => {
        let th = document.createElement('th');
        th.textContent = header;
        headerRow.appendChild(th);
    });
    thead.appendChild(headerRow);

    // Populate table body
    data.tbody.forEach(row => {
        let tr = document.createElement('tr');
        tr.innerHTML = row; // Insert HTML string directly
        tbody.appendChild(tr);
    });

    // Reset date input fields after search
    document.getElementById(start_date).value = '';
    document.getElementById(end_date).value = '';
}

function fetchTables() {
    fetchTable('in', 'start_date1', 'end_date1', 'selectedDateRange1', 'thead1', 'tbody1');
    fetchTable('out', 'start_date1', 'end_date1', 'selectedDateRange1', 'thead2', 'tbody2');
    fetchTable('balance', 'start_date1', 'end_date1', 'selectedDateRange1', 'thead3', 'tbody3');
}