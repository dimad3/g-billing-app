function toggleNameFields() {
    const entityType = document.getElementById('entity_type').value;
    const nameField = document.getElementById('name_field');
    const firstNameField = document.getElementById('first_name_field');
    const lastNameField = document.getElementById('last_name_field');

    // Get input elements
    const nameInput = document.getElementById('name');
    const firstNameInput = document.getElementById('first_name');
    const lastNameInput = document.getElementById('last_name');

    if (entityType === 'individual') {
        nameField.classList.add('hidden');
        firstNameField.classList.remove('hidden');
        lastNameField.classList.remove('hidden');

        // Clear name input
        if (nameInput) nameInput.value = '';
    } else if (entityType === 'legal_entity') {
        nameField.classList.remove('hidden');
        firstNameField.classList.add('hidden');
        lastNameField.classList.add('hidden');

        // Clear first name & last name inputs
        if (firstNameInput) firstNameInput.value = '';
        if (lastNameInput) lastNameInput.value = '';
    }
}

// Attach function to `window` so it's globally accessible
window.toggleNameFields = toggleNameFields;

/**
 * The `loadLegalForm()` function dynamically populates the `legal_form` dropdown based on the selected value of the `entity_type` dropdown.
 *
 * Usage:
 * This function is typically called during the `DOMContentLoaded` event to ensure the event listener is set up as soon as the page loads.
 */
function loadLegalForm() {
    // Retrieve the `entity_type` and `legal_form` dropdown elements
    const entityTypeSelect = document.getElementById('entity_type');
    const legalFormSelect = document.getElementById('legal_form');

    // Add an event listener to the `entity_type` dropdown
    entityTypeSelect.addEventListener('change', handleentityTypeChange);

    /**
     * Handles the change event for the `entity_type` dropdown.
     */
    function handleentityTypeChange() {
        const entityType = this.value; // Get the currently selected user type

        // Fetch legal forms from the server API
        fetch(`/api/legal-forms?entity_type=${entityType}`)
            .then(response => response.json()) // Parse the response as JSON
            .then(updateLegalForms) // Update the `legal_form` dropdown with the fetched data
            .catch(error => console.error('Error fetching legal forms:', error)); // Handle any errors
    }

    /**
     * Updates the `legal_form` dropdown with the provided legal forms data.
     *
     * @param {Object} data - An object of legal forms fetched from the server, where keys are form codes and values are display names.
     */
    function updateLegalForms(data) {
        // console.log('Data type:', typeof data, 'Data value:', data);
        legalFormSelect.innerHTML = ''; // Clear all existing options in the dropdown

        // Add an empty option at the beginning (e.g., "Select Legal Form")
        const emptyOption = document.createElement('option');
        emptyOption.value = ''; // Set the value of the empty option to an empty string
        emptyOption.textContent = ''; // Set the display text for the empty option
        legalFormSelect.appendChild(emptyOption); // Append the empty option to the dropdown

        // Iterate through the fetched legal forms and create <option> elements for each
        Object.entries(data).forEach(([key, value]) => {
            const option = document.createElement('option');
            option.value = key;
            option.textContent = value;
            legalFormSelect.appendChild(option);
        });
    }
}

// Attach function to `window` so it's globally accessible
window.loadLegalForm = loadLegalForm;

/**
 * Bank Account Manager Alpine.js Component
 * Manages dynamic bank account fields with Alpine.js
 */
document.addEventListener('alpine:init', () => {
    Alpine.data('bankAccountManager', (initialAccounts = []) => ({
        accounts: [],
        errors: {},

        init() {
            // Get old input data if available
            const oldInput = this.getOldInput();

            if (oldInput && oldInput.length > 0) {
                // Use old input data after validation error
                this.accounts = oldInput;
            } else if (initialAccounts.length > 0) {
                // Use existing accounts from database
                this.accounts = initialAccounts.map(account => ({
                    id: account.id || null,
                    bank_id: account.bank_id || '',
                    bank_account: account.bank_account || ''
                }));
            } else {
                // No accounts, initialize empty array
                this.accounts = [];
            }

            // Add an empty account if there are none
            if (this.accounts.length === 0) {
                this.addBankAccount();
            }

            // Get errors from Laravel if they exist in the page
            this.loadValidationErrors();
        },

        getOldInput() {
            try {
                // Check if oldBankAccounts is available in the page
                if (typeof window.oldBankAccounts !== 'undefined' && Array.isArray(window.oldBankAccounts)) {
                    return window.oldBankAccounts;
                }
                return null;
            } catch (e) {
                console.error('Error loading old input data', e);
                return null;
            }
        },

        loadValidationErrors() {
            // Try to parse errors from a global variable or hidden input
            try {
                // From a global variable set in your layout
                if (typeof window.laravelErrors !== 'undefined') {
                    this.errors = window.laravelErrors;
                }
            } catch (e) {
                console.error('Error loading validation errors', e);
                this.errors = {};
            }
        },

        getError(index, field) {
            const key = `bank_accounts.${index}.${field}`;
            return this.errors[key] ? this.errors[key][0] : null;
        },

        addBankAccount() {
            this.accounts.push({
                id: null,
                bank_id: '',
                bank_account: ''
            });
        },

        removeAccount(index) {
            this.accounts.splice(index, 1);

            // Ensure there's always at least one empty account field
            if (this.accounts.length === 0) {
                this.addBankAccount();
            }
        }
    }));
});

// Ensure the function runs on DOMContentLoaded
document.addEventListener('DOMContentLoaded', function () {
    const entityTypeSelect = document.getElementById('entity_type');
    // const legalFormSelect = document.getElementById('legal_form');

    // Check if the entityTypeSelect element exists before proceeding
    if (!entityTypeSelect) {
        return; // Exit function if `entity_type` is not found
    }

    // null checks to safely access elements
    if (entityTypeSelect) {
        toggleNameFields(); // Call toggleNameFields
        loadLegalForm();    // Call loadLegalForm
    } else {
        console.warn('entity_type select element not found.');
    }
});
