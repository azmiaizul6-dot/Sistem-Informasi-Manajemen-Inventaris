// ========================================
// MAIN JAVASCRIPT
// ========================================

// Toggle Dark Mode
function toggleDarkMode() {
    const body = document.body;
    const isDarkMode = body.classList.contains('light-mode');
    
    if (isDarkMode) {
        body.classList.remove('light-mode');
        localStorage.setItem('theme', 'dark');
    } else {
        body.classList.add('light-mode');
        localStorage.setItem('theme', 'light');
    }
}

// Load theme dari localStorage
function loadTheme() {
    const theme = localStorage.getItem('theme') || 'dark';
    if (theme === 'light') {
        document.body.classList.add('light-mode');
    }
}

// Toggle Mobile Sidebar
function toggleMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.toggle('active');
    }
}

// Close Mobile Sidebar
function closeMobileSidebar() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar) {
        sidebar.classList.remove('active');
    }
}

// Show Alert
function showAlert(message, type = 'success') {
    const alertContainer = document.getElementById('alert-container');
    if (!alertContainer) return;
    
    const alertClass = {
        'success': 'alert-success',
        'danger': 'alert-danger',
        'warning': 'alert-warning',
        'info': 'alert-info'
    }[type] || 'alert-info';
    
    const alertIcon = {
        'success': '✓',
        'danger': '✕',
        'warning': '!',
        'info': 'ℹ'
    }[type];
    
    const alertHTML = `
        <div class="alert ${alertClass} animate-slide-in">
            <div class="alert-icon">${alertIcon}</div>
            <div class="alert-message">${message}</div>
        </div>
    `;
    
    alertContainer.insertAdjacentHTML('beforeend', alertHTML);
    
    // Auto remove setelah 5 detik
    setTimeout(() => {
        const alerts = alertContainer.querySelectorAll('.alert');
        if (alerts.length > 0) {
            alerts[0].remove();
        }
    }, 5000);
}

// Format Currency
function formatCurrency(value) {
    return new Intl.NumberFormat('id-ID', {
        style: 'currency',
        currency: 'IDR',
        minimumFractionDigits: 0
    }).format(value);
}

// Format Date
function formatDate(date) {
    return new Date(date).toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    });
}

// Confirm Delete
function confirmDelete(itemName = 'item') {
    return confirm(`Apakah Anda yakin ingin menghapus ${itemName}? Tindakan ini tidak dapat dibatalkan.`);
}

// Copy to Clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showAlert('Teks berhasil disalin!', 'success');
    });
}

// Export to CSV
function exportToCSV(filename, tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    let csv = [];
    const rows = table.querySelectorAll('tr');
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('td, th');
        const csvRow = Array.from(cols).map(col => {
            let text = col.textContent.trim();
            // Escape quotes
            text = text.replace(/"/g, '""');
            return `"${text}"`;
        });
        csv.push(csvRow.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = filename || 'export.csv';
    link.click();
}

// Search Table
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    input.addEventListener('keyup', function() {
        const filter = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    });
}

// Form Validation
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        if (input.value.trim() === '') {
            input.classList.add('border-red-500');
            isValid = false;
        } else {
            input.classList.remove('border-red-500');
        }
    });
    
    return isValid;
}

// Debounce Function
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

// Calculate Stats
function calculateStats(values) {
    const sum = values.reduce((a, b) => a + b, 0);
    const average = sum / values.length;
    const max = Math.max(...values);
    const min = Math.min(...values);
    
    return { sum, average, max, min };
}

// Init on load
document.addEventListener('DOMContentLoaded', function() {
    loadTheme();
    
    // Close sidebar ketika klik di luar
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const sidebarToggle = document.getElementById('sidebar-toggle');
        
        if (sidebar && sidebarToggle && !sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
            closeMobileSidebar();
        }
    });
});

// Fetch API Helper
async function fetchAPI(url, options = {}) {
    try {
        const response = await fetch(url, {
            headers: {
                'Content-Type': 'application/json',
                ...options.headers
            },
            ...options
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        return await response.json();
    } catch (error) {
        console.error('Fetch error:', error);
        showAlert('Terjadi kesalahan: ' + error.message, 'danger');
        throw error;
    }
}

// Chart Helper (untuk integrasi dengan Chart.js)
function initChart(canvasId, config) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return null;
    
    if (typeof Chart === 'undefined') {
        console.error('Chart.js tidak ditemukan');
        return null;
    }
    
    return new Chart(canvas, config);
}