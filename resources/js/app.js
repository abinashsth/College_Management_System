import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Initialize masking functionality
document.addEventListener('DOMContentLoaded', function() {
    // Function to initialize masks based on data attributes
    function initMasks() {
        // Find elements with data-mask attribute
        const maskElements = document.querySelectorAll('[data-mask]');
        
        maskElements.forEach(element => {
            const maskType = element.getAttribute('data-mask');
            
            element.addEventListener('input', function(e) {
                const value = e.target.value;
                let maskedValue = value;
                
                // Apply different masks based on the mask type
                switch(maskType) {
                    case 'phone':
                        maskedValue = maskPhone(value);
                        break;
                    case 'date':
                        maskedValue = maskDate(value);
                        break;
                    case 'time':
                        maskedValue = maskTime(value);
                        break;
                    case 'currency':
                        maskedValue = maskCurrency(value);
                        break;
                    case 'percentage':
                        maskedValue = maskPercentage(value);
                        break;
                    // Add more mask types as needed
                }
                
                if (maskedValue !== value) {
                    e.target.value = maskedValue;
                }
            });
        });
    }
    
    // Mask functions for different types
    function maskPhone(value) {
        // Remove non-digits
        const digits = value.replace(/\D/g, '');
        
        // Apply mask format: (123) 456-7890
        if (digits.length <= 3) {
            return digits;
        } else if (digits.length <= 6) {
            return `(${digits.slice(0, 3)}) ${digits.slice(3)}`;
        } else {
            return `(${digits.slice(0, 3)}) ${digits.slice(3, 6)}-${digits.slice(6, 10)}`;
        }
    }
    
    function maskDate(value) {
        // Remove non-digits
        const digits = value.replace(/\D/g, '');
        
        // Apply mask format: MM/DD/YYYY
        if (digits.length <= 2) {
            return digits;
        } else if (digits.length <= 4) {
            return `${digits.slice(0, 2)}/${digits.slice(2)}`;
        } else {
            return `${digits.slice(0, 2)}/${digits.slice(2, 4)}/${digits.slice(4, 8)}`;
        }
    }
    
    function maskTime(value) {
        // Remove non-digits
        const digits = value.replace(/\D/g, '');
        
        // Apply mask format: HH:MM
        if (digits.length <= 2) {
            return digits;
        } else {
            return `${digits.slice(0, 2)}:${digits.slice(2, 4)}`;
        }
    }
    
    function maskCurrency(value) {
        // Remove non-digits except decimal point
        const cleanValue = value.replace(/[^\d.]/g, '');
        // Ensure only one decimal point
        const parts = cleanValue.split('.');
        let result = parts[0];
        if (parts.length > 1) {
            result += '.' + parts[1];
        }
        
        // Add commas for thousands
        const formattedInteger = result.split('.')[0].replace(/\B(?=(\d{3})+(?!\d))/g, ',');
        const decimal = result.includes('.') ? '.' + result.split('.')[1] : '';
        
        return formattedInteger + decimal;
    }
    
    function maskPercentage(value) {
        // Remove non-digits except decimal point
        const cleanValue = value.replace(/[^\d.]/g, '');
        
        // Format as percentage (up to 2 decimal places)
        const parts = cleanValue.split('.');
        let result = parts[0];
        if (parts.length > 1) {
            result += '.' + parts[1].substring(0, 2);
        }
        
        return result + '%';
    }
    
    // Initialize masks
    initMasks();
    
    // Create a global function for toggling mask visibility
    window.toggleMask = function(field) {
        if (field.type === 'password') {
            field.type = 'text';
        } else {
            field.type = 'password';
        }
        
        // Toggle icon if exists (assuming Font Awesome is used)
        const icon = field.nextElementSibling?.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-eye');
            icon.classList.toggle('fa-eye-slash');
        }
    };

    // Sidebar dropdown functionality
    window.toggleDropdown = function(id) {
        const dropdown = document.getElementById(id);
        if (dropdown) {
            dropdown.classList.toggle('hidden');
            
            // Toggle chevron icon
            const button = document.querySelector(`[onclick="toggleDropdown('${id}')"]`);
            if (button) {
                const chevron = button.querySelector('.fa-chevron-down');
                if (chevron) {
                    chevron.classList.toggle('rotate-180');
                }
            }
        }
    };
});
