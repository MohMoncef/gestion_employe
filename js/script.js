// Main JavaScript file for Employee Management System

document.addEventListener('DOMContentLoaded', function() {
    // Initialize any tooltips or interactive elements
    initTooltips();
    
    // Setup form validation
    setupFormValidation();
    
    // Handle filter functionality
    setupFilters();
});

function initTooltips() {
    // Add tooltips to action buttons
    const editButtons = document.querySelectorAll('.btn-edit');
    const deleteButtons = document.querySelectorAll('.btn-delete');
    
    editButtons.forEach(btn => {
        btn.setAttribute('title', 'Modifier');
    });
    
    deleteButtons.forEach(btn => {
        btn.setAttribute('title', 'Supprimer');
    });
}

function setupFormValidation() {
    const forms = document.querySelectorAll('form');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = '#e74c3c';
                    
                    // Add error message if not already present
                    if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('span');
                        errorMsg.className = 'error-message';
                        errorMsg.style.color = '#e74c3c';
                        errorMsg.style.fontSize = '0.85rem';
                        errorMsg.textContent = 'Ce champ est obligatoire';
                        field.parentNode.insertBefore(errorMsg, field.nextSibling);
                    }
                } else {
                    field.style.borderColor = '#ddd';
                    
                    // Remove error message if exists
                    if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                        field.nextElementSibling.remove();
                    }
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires.');
            }
        });
    });
}

function setupFilters() {
    // Add filter functionality to tables
    const filterInputs = document.querySelectorAll('.table-filter');
    
    filterInputs.forEach(input => {
        input.addEventListener('keyup', function() {
            const filterValue = this.value.toLowerCase();
            const tableId = this.getAttribute('data-table');
            const table = document.getElementById(tableId);
            const rows = table.getElementsByTagName('tbody')[0].getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                const row = rows[i];
                let rowText = row.textContent || row.innerText;
                
                if (rowText.toLowerCase().indexOf(filterValue) > -1) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            }
        });
    });
}

// Date formatting helper
function formatDate(dateString) {
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    return `${day}/${month}/${year}`;
}

// Number formatting helper
function formatCurrency(amount) {
    return new Intl.NumberFormat('fr-FR', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount);
}

// Export data function
function exportToCSV(tableId, filename) {
    const table = document.getElementById(tableId);
    const rows = table.querySelectorAll('tr');
    let csvContent = '';
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const rowData = [];
        
        cols.forEach(col => {
            rowData.push(`"${col.textContent.replace(/"/g, '""')}"`);
        });
        
        csvContent += rowData.join(',') + '\n';
    });
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    
    if (link.download !== undefined) {
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', filename);
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
}