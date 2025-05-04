document.addEventListener('DOMContentLoaded', function () {
    // Check if we're on a page with the document form elements
    if (isDocumentFormPage()) {
        processClientDefaults(); // Ensure function runs when the page loads
        getClientDefaults(); // Ensure function runs when the page loads
        initializeTable(); // Initialize calculations when page loads
    }
});

// Helper function to determine if we're on a document form page
function isDocumentFormPage() {
    // Check for presence of key elements that are specific to the document form
    return document.querySelector('tbody tr[data-row-index]') !== null ||
        document.getElementById('summary-row') !== null;
}

// Global variable to store client default values
window.clientDefaults = {
    discountRate: 0,
    dueDays: 0
};


// processing items calculations ---------------------------------------

// Initialize the table and calculations
function initializeTable() {
    const rows = document.querySelectorAll('tbody tr[data-row-index]');
    // console.log('Initial rows found:', rows.length);

    // Add event listeners to all input fields
    addEventListenersToInputs();

    // If no rows exist, add an empty row
    if (rows.length === 0) {
        // console.log('No rows found, adding initial row');
        addRow();
    } else {
        // Calculate all row values
        rows.forEach(row => {
            const index = row.getAttribute('data-row-index');
            if (index !== null) {
                calculateRowValues(parseInt(index));
            }
        });
    }

    // Calculate summary values after initializing the table
    // console.log('Calculating summary values');
    calculateSummaryValues();
}

// Add event listeners to all input fields
function addEventListenersToInputs() {
    const rows = document.querySelectorAll('tbody [data-row-index]');
    rows.forEach(row => {
        const index = row.getAttribute('data-row-index');
        if (index !== null) {
            const quantityInput = row.querySelector(`input[name="items[${index}][quantity]"]`);
            const priceInput = row.querySelector(`input[name="items[${index}][price]"]`);
            const discountRateInput = row.querySelector(`input[name="items[${index}][discount_rate]"]`);
            const taxRateInput = row.querySelector(`input[name="items[${index}][tax_rate]"]`);

            if (quantityInput) quantityInput.addEventListener('input', () => calculateRowValues(parseInt(
                index)));
            if (priceInput) priceInput.addEventListener('input', () => calculateRowValues(parseInt(index)));
            if (discountRateInput) discountRateInput.addEventListener('input', () => calculateRowValues(
                parseInt(index)));
            if (taxRateInput) taxRateInput.addEventListener('input', () => calculateRowValues(parseInt(
                index)));
        }
    });

    // Add event listener to advance paid input
    const advancePaidInput = document.getElementById('advance_paid');
    if (advancePaidInput) {
        advancePaidInput.addEventListener('input', () => calculatePayableAmount());
    }
}

// Add new row function
function addRow() {
    const tbody = document.querySelector('tbody');
    if (!tbody) return; // Safety check

    const rows = Array.from(tbody.querySelectorAll('tr[data-row-index]'));
    const newRowIndex = rows.length > 0 ?
        parseInt(rows[rows.length - 1].getAttribute('data-row-index')) + 1 : 0;

    const newRow = document.createElement('tr');
    newRow.className = '';
    newRow.setAttribute('data-row-index', newRowIndex);
    // console.log('Added new row at index:', newRowIndex);

    // Get the default tax rate from a global variable
    const defaultTaxRate = window.defaultTaxRate || 0;

    // Get the default discount rate either from client defaults or fallback to 0
    const discountRate = window.clientDefaults?.discountRate || 0;

    newRow.innerHTML = `
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">${newRowIndex + 1}</td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[${newRowIndex}][name]" value=""
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[${newRowIndex}][unit]" value=""
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[${newRowIndex}][quantity]" value="0" autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[${newRowIndex}][price]" value="0.00" autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right">
        </td>

        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-amount="${newRowIndex}">0.00</td>

        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="text" name="items[${newRowIndex}][discount_rate]" value="${discountRate}" autocomplete='off'
                class="no-border px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right">
        </td>

        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-discount="${newRowIndex}">0.00</td>
        <td class="px-1 py-1 border border-gray-100 text-blue-700 text-right" data-net-amount="${newRowIndex}">0.00</td>

        <td class="px-0 py-0 border border-gray-100 text-gray-400 text-center">
            <input type="no-border text" name="items[${newRowIndex}][tax_rate]" value="${defaultTaxRate}"
                class="px-1 py-1 w-full border-0 text-gray-800 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 outline-none text-right">
        </td>
        <td class="px-0 py-0 border border-gray-100 text-center">
            <button type="button" class="text-red-300 hover:text-red-700" onclick="deleteRow(this)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </td>
    `;

    // Insert before the summary row
    const summaryRow = document.getElementById('summary-row');
    if (summaryRow) {
        tbody.insertBefore(newRow, summaryRow);
    } else {
        tbody.appendChild(newRow);
    }

    // Add event listeners to the new row
    addEventListenersToInputs();
}

// Calculate values for a specific row
function calculateRowValues(index) {
    // Select the table row based on its data-row-index attribute
    const row = document.querySelector(`tr[data-row-index="${index}"]`);
    if (!row) return; // Exit early if the row doesn't exist

    // Select input elements for quantity, price, and discount rate within the row
    const quantityInput = row.querySelector(`input[name="items[${index}][quantity]"]`);
    const priceInput = row.querySelector(`input[name="items[${index}][price]"]`);
    const discountRateInput = row.querySelector(`input[name="items[${index}][discount_rate]"]`);

    // Parse and sanitize numeric values from the input fields
    const quantity = quantityInput ? (parseFloat(quantityInput.value.replace(/,/g, '')) || 0) : 0;
    const price = priceInput ? (parseFloat(priceInput.value.replace(/,/g, '')) || 0) : 0;
    const discountRate = discountRateInput ? (parseFloat(discountRateInput.value.replace(/,/g, '')) || 0) : 0;

    // Calculate row-level financial values
    const amount = round(quantity * price, 2);
    const discount = round((amount * discountRate) / 100, 2);
    const netAmount = round(amount - discount, 2);

    // Select output fields for displaying calculated values
    const amountField = row.querySelector(`[data-amount="${index}"]`);
    const discountField = row.querySelector(`[data-discount="${index}"]`);
    const netAmountField = row.querySelector(`[data-net-amount="${index}"]`);

    // Update output fields with formatted values
    if (amountField) amountField.textContent = formatNumber(amount);
    if (discountField) discountField.textContent = formatNumber(discount);
    if (netAmountField) netAmountField.textContent = formatNumber(netAmount);

    // Recalculate overall document summary (e.g., totals in the footer)
    calculateSummaryValues();
}

// Calculate all summary values
function calculateSummaryValues() {
    // Calculate sub totals (amount, discount, net amount)
    calculateSubTotals();

    // Calculate total
    calculateTotal();

    // Calculate payable amount
    calculatePayableAmount();
}

// Calculate sub totals
function calculateSubTotals() {
    // console.log('Starting calculateSubTotals');

    // Initialize variables
    let sumOfQuantity = 0;
    let sumOfAmount = 0;
    let sumOfDiscount = 0;
    let totalNetAmount = 0;

    // Get all rows
    const rows = document.querySelectorAll('tbody tr[data-row-index]');
    // console.log('Calculating sub totals for rows:', rows.length);

    // Create an array to store the items
    const items = [];

    // Iterate through each row and collect data
    rows.forEach(row => {
        const index = row.getAttribute('data-row-index');

        // console.log('Processing row index:', index);

        // Check if index is not null
        if (index !== null) {
            const quantityField = row.querySelector(`input[name="items[${index}][quantity]"]`);
            const amountField = row.querySelector(`[data-amount="${index}"]`);
            const discountField = row.querySelector(`[data-discount="${index}"]`);
            const netAmountField = row.querySelector(`[data-net-amount="${index}"]`);
            const taxRateField = row.querySelector(`input[name="items[${index}][tax_rate]"]`);

            // console.log(`Row ${index}:`, {
            //     quantityField: quantityField?.outerHTML || 'Not Found',
            //     amountField: amountField?.outerHTML || 'Not Found',
            //     discountField: discountField?.outerHTML || 'Not Found',
            //     netAmountField: netAmountField?.outerHTML || 'Not Found',
            //     taxRateField: taxRateField?.outerHTML || 'Not Found'
            // });

            // Check if all fields are present
            if (quantityField && amountField && discountField && netAmountField && taxRateField) {
                const quantity = parseFloat(quantityField.value.replace(/,/g, '')) || 0;
                const amount = parseFloat(amountField.textContent.replace(/,/g, '')) || 0;
                const discount = parseFloat(discountField.textContent.replace(/,/g, '')) || 0;
                const netAmount = parseFloat(netAmountField.textContent.replace(/,/g, '')) || 0;
                const taxRate = parseFloat(taxRateField.value.replace(/,/g, '')) || 0;

                // console.log(
                //     `Row ${index}: Quantity: ${quantity}, Amount: ${amount}, Discount: ${discount}, Net Amount: ${netAmount}, Tax Rate: ${taxRate}`
                //     );

                // Store item data for VAT calculation
                items.push({
                    netAmount: netAmount,
                    taxRate: taxRate
                });

                // Aggregate Column values (except VAT which will be calculated separately)
                sumOfQuantity += quantity;
                sumOfAmount += amount;
                sumOfDiscount += discount;
                totalNetAmount += netAmount;
            }
        }
    });

    // Calculate VAT using the new grouping method
    const vat = calculateVATByGroup(items);

    // Update individual row VAT displays for consistency
    rows.forEach(row => {
        const index = row.getAttribute('data-row-index');
        if (index !== null) {
            const netAmountField = row.querySelector(`[data-net-amount="${index}"]`);
            const taxRateField = row.querySelector(`input[name="items[${index}][tax_rate]"]`);

            if (netAmountField && taxRateField) {
                const netAmount = parseFloat(netAmountField.textContent.replace(/,/g, '')) || 0;
                const taxRate = parseFloat(taxRateField.value.replace(/,/g, '')) || 0;
            }
        }
    });

    // Update sub totals rows - with null checks
    const sumOfQuantityEl = document.getElementById('sumOfQuantity');
    const sumOfAmountEl = document.getElementById('sumOfAmount');
    const sumOfDiscountEl = document.getElementById('sumOfDiscount');
    const totalNetAmountEl = document.getElementById('totalNetAmount');
    const vatEl = document.getElementById('vat');

    // Only update elements if they exist
    if (sumOfQuantityEl) sumOfQuantityEl.textContent = sumOfQuantity;
    if (sumOfAmountEl) {
        sumOfAmountEl.textContent = formatNumber(sumOfAmount);
        sumOfAmountEl.setAttribute('data-value', sumOfAmount);
    }
    if (sumOfDiscountEl) {
        sumOfDiscountEl.textContent = formatNumber(sumOfDiscount);
        sumOfDiscountEl.setAttribute('data-value', sumOfDiscount);
    }
    if (totalNetAmountEl) {
        totalNetAmountEl.textContent = formatNumber(totalNetAmount);
        totalNetAmountEl.setAttribute('data-value', totalNetAmount);
    }
    if (vatEl) {
        vatEl.textContent = formatNumber(vat);
        vatEl.setAttribute('data-value', vat);
    }
}

// Calculate VAT by grouping items with the same tax rate
function calculateVATByGroup(items) {
    // Create an object to store summed netAmounts by tax rate
    const groupedByTaxRate = {};

    // Group items by tax rate and sum their netAmounts
    items.forEach(item => {
        const taxRate = parseFloat(item.taxRate) || 0;
        const netAmount = parseFloat(item.netAmount) || 0;

        // Initialize group if it doesn't exist
        if (!groupedByTaxRate[taxRate]) {
            groupedByTaxRate[taxRate] = 0;
        }

        // Add netAmount to the group
        groupedByTaxRate[taxRate] += netAmount;
    });

    // Calculate tax subtotal for each group and sum them up
    let totalVAT = 0;

    // Calculate VAT for each tax rate group
    for (const taxRate in groupedByTaxRate) {
        const totalNetAmount = groupedByTaxRate[taxRate];
        const taxSubTotal = round((totalNetAmount * parseFloat(taxRate)) / 100, 2);
        totalVAT += taxSubTotal;
    }

    return totalVAT;
}

// Calculate total
function calculateTotal() {
    // Get elements with null checks
    const totalNetAmountEl = document.getElementById('totalNetAmount');
    const vatEl = document.getElementById('vat');
    const totalEl = document.getElementById('total');

    if (!totalNetAmountEl || !vatEl || !totalEl) return;

    // Get sub total and VAT
    const totalNetAmount = parseFloat(totalNetAmountEl.getAttribute('data-value')) || 0;
    const vat = parseFloat(vatEl.getAttribute('data-value')) || 0;

    // Calculate total
    const total = round(totalNetAmount + vat, 2);

    // Update total display
    totalEl.textContent = formatNumber(total);
    totalEl.setAttribute('data-value', total);
}

// Calculate payable amount
function calculatePayableAmount() {
    // Get elements with null checks
    const totalEl = document.getElementById('total');
    const advancePaidEl = document.getElementById('advance_paid');
    const payableAmountEl = document.getElementById('payableAmount');

    if (!totalEl || !advancePaidEl || !payableAmountEl) return;

    // Get total and advance paid
    const total = parseFloat(totalEl.getAttribute('data-value')) || 0;
    const advancePaid = parseFloat(advancePaidEl.value.replace(/,/g, '')) || 0;

    // Calculate payable amount
    const payableAmount = round(total - advancePaid, 2);

    // Update payable amount display
    payableAmountEl.textContent = formatNumber(payableAmount);
    payableAmountEl.setAttribute('data-value', payableAmount);
}

// Helper function to round to 2 decimal places
function round(value, decimals) {
    return Number(Math.round(value + 'e' + decimals) + 'e-' + decimals);
}

// Helper function to format numbers with commas
function formatNumber(value, decimalPlaces = 2, enforceTwoDecimals = true) {
    let formattedValue;

    if (enforceTwoDecimals) {
        formattedValue = value.toFixed(2); // Always enforce two decimal places
    } else {
        formattedValue = value.toFixed(decimalPlaces); // Use specified decimal places
    }

    return formattedValue.replace(/\d(?=(\d{3})+\.)/g, '$&,');
}

// Delete row function
function deleteRow(button) {
    const row = button.closest('tr');
    if (!row) return; // Safety check

    row.remove();

    // Renumber rows
    const rows = document.querySelectorAll('tbody tr[data-row-index]');
    rows.forEach((row, index) => {
        // Store the old index for reference
        const oldIndex = row.getAttribute('data-row-index');

        // Update row index
        row.setAttribute('data-row-index', index);
        const firstCell = row.querySelector('td:first-child');
        if (firstCell) firstCell.textContent = index + 1;

        // Update input names
        const inputs = row.querySelectorAll('input[name^="items["]');
        inputs.forEach(input => {
            const name = input.getAttribute('name');
            const newName = name.replace(/items\[\d+\]/, `items[${index}]`);
            input.setAttribute('name', newName);
        });

        // Update data attributes
        const dataElements = row.querySelectorAll(
            '[data-amount], [data-discount], [data-net-amount]');
        dataElements.forEach(el => {
            if (el.hasAttribute('data-amount')) {
                el.setAttribute('data-amount', index);
            }
            if (el.hasAttribute('data-discount')) {
                el.setAttribute('data-discount', index);
            }
            if (el.hasAttribute('data-net-amount')) {
                el.setAttribute('data-net-amount', index);
            }
        });
    });

    // Remove old event listeners and add new ones with correct indices
    // This is the key fix to ensure calculateRowValues works properly after deletion
    addEventListenersToInputs();

    // Recalculate summary values
    calculateSummaryValues();
}

// processing client defaults ---------------------------------------

function getClientDefaults() {
    const clientIdInput = document.getElementById('client_id'); // Retrieve the `client_id` element from the DOM
    if (!clientIdInput || !clientIdInput.value) return;

    // Fetch the client's Default Values from the server API
    fetch(`/api/clients/${clientIdInput.value}/default-values`)
        .then(response => response.json()) // Parse the response as JSON
        .then(data => {
            console.log('Client defaults received:', data);

            // Store client's default values in the global variables
            window.clientDefaults.discountRate = data.discount_rate || 0;
            window.clientDefaults.dueDays = data.due_days || 0;
        })
        .catch(error => console.error('Error fetching client default values:', error)); // Handle any errors
}

function processClientDefaults() {
    const clientIdInput = document.getElementById('client_id'); // Retrieve the `client_id` element from the DOM
    // Ensure clientIdInput exists before proceeding
    if (!clientIdInput) return; // Exit function if `client_id` is not found

    // Add an event listener to the `client_id` dropdown
    clientIdInput.addEventListener('change', function () {
        fetchClientDefaults(this.value);
    });

    const clientId = clientIdInput.value; // Get the currently selected client ID
    // Ensure clientId exists before proceeding
    if (!clientId) return; // Exit function if `clientId` is not found

    function fetchClientDefaults(clientId) {
        // Fetch the client's Default Values from the server API
        fetch(`/api/clients/${clientId}/default-values`)
            .then(response => response.json()) // Parse the response as JSON
            .then(data => {
                console.log('Client defaults received:', data);

                // Store client's default values in the global variables
                window.clientDefaults.discountRate = data.discount_rate || 0;
                window.clientDefaults.dueDays = data.due_days || 0;

                // Update the due date based on the fetched data
                updateDueDate(data.due_days || 0);

                // Update discount rate for the first item row
                updateAllItemsDiscountRate(data.discount_rate || 0);
            })
            .catch(error => console.error('Error fetching client default values:', error)); // Handle any errors
    }
}

/**
 * Updates the `due_date` input with the provided due days.
 * @param {Number} dueDays - The number of due days to add to document date.
 */
function updateDueDate(dueDays) {
    // Retrieve the `due_date` and `document_date` input elements
    const dueDateInput = document.getElementById('due_date');
    const documentDateInput = document.getElementById('document_date');

    // Only proceed if both inputs exist
    if (!dueDateInput || !documentDateInput) {
        return;
    }

    // Parse the document date (assuming it's in 'YYYY-MM-DD' format)
    const documentDate = new Date(documentDateInput.value);

    // If the `document_date` input is empty or invalid,
    // set the value of the `due_date` input to the current date
    if (isNaN(documentDate.getTime())) {
        const today = new Date();
        const formattedDate = today.toISOString().split('T')[0];
        dueDateInput.value = formattedDate;
        return;
    }

    // Create a new date based on document date
    const dueDate = new Date(documentDate);

    // Add due days to the document date
    dueDate.setDate(documentDate.getDate() + Number(dueDays));

    // Format back to 'YYYY-MM-DD'
    const formattedDueDate = dueDate.toISOString().split('T')[0];

    // Assign to input field
    dueDateInput.value = formattedDueDate;
}

/**
 * Updates the discount rate for all item rows.
 * @param {Number} discountRate - The discount rate to apply.
 */
function updateAllItemsDiscountRate(discountRate) {
    // Find all item rows
    const rows = document.querySelectorAll('tbody tr[data-row-index]');

    if (rows.length > 0) {
        // Loop through all rows and update discount rates
        rows.forEach(row => {
            const rowIndex = row.getAttribute('data-row-index');
            const discountRateInput = row.querySelector(`input[name="items[${rowIndex}][discount_rate]"]`);

            if (discountRateInput) {
                // Set the value of the discount rate input
                discountRateInput.value = discountRate;

                // Recalculate row values to update all dependent fields
                if (typeof calculateRowValues === 'function') {
                    calculateRowValues(parseInt(rowIndex));
                }
            }
        });

        // Calculate summary values after updating all rows
        if (typeof calculateSummaryValues === 'function') {
            calculateSummaryValues();
        }
    } else {
        // If no rows exist yet, this will be handled when rows are added
        console.log('No item rows found. Discount rate will be applied when rows are added.');
    }
}

// Expose functions to global scope for use in HTML elements
window.addRow = addRow;
window.deleteRow = deleteRow;
window.calculateRowValues = calculateRowValues;
window.calculateSummaryValues = calculateSummaryValues;
window.processClientDefaults = processClientDefaults;
