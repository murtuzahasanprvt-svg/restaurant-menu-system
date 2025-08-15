/**
 * Default Theme JavaScript
 */

// Theme-specific initialization
function initializeTheme() {
    initializeThemeToggles();
    initializeAnimations();
    initializeLazyLoading();
    initializeAccessibility();
}

// Theme toggle functionality
function initializeThemeToggles() {
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', toggleTheme);
        
        // Check for saved theme preference
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            document.body.setAttribute('data-theme', savedTheme);
        }
    }
}

function toggleTheme() {
    const body = document.body;
    const currentTheme = body.getAttribute('data-theme');
    const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
    
    body.setAttribute('data-theme', newTheme);
    localStorage.setItem('theme', newTheme);
    
    showNotification(`Switched to ${newTheme} theme`, 'info');
}

// Animation initialization
function initializeAnimations() {
    // Animate elements on scroll
    const animatedElements = document.querySelectorAll('.animate-on-scroll');
    
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animated');
                observer.unobserve(entry.target);
            }
        });
    }, {
        threshold: 0.1
    });
    
    animatedElements.forEach(element => {
        observer.observe(element);
    });
}

// Lazy loading for images
function initializeLazyLoading() {
    const lazyImages = document.querySelectorAll('img[data-src]');
    
    const imageObserver = new IntersectionObserver((entries, observer) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const img = entry.target;
                img.src = img.dataset.src;
                img.removeAttribute('data-src');
                observer.unobserve(img);
            }
        });
    });
    
    lazyImages.forEach(img => {
        imageObserver.observe(img);
    });
}

// Accessibility improvements
function initializeAccessibility() {
    // Add keyboard navigation support
    document.addEventListener('keydown', function(event) {
        // Escape key to close modals and sidebars
        if (event.key === 'Escape') {
            closeAllModals();
            closeCart();
        }
        
        // Enter key on clickable elements
        if (event.key === 'Enter') {
            const focusedElement = document.activeElement;
            if (focusedElement.classList.contains('clickable')) {
                focusedElement.click();
            }
        }
    });
    
    // Add ARIA labels to dynamic elements
    addAriaLabels();
}

function addAriaLabels() {
    // Add ARIA labels to cart buttons
    const cartButtons = document.querySelectorAll('.add-to-cart-btn');
    cartButtons.forEach(button => {
        const itemName = button.dataset.itemName;
        if (itemName) {
            button.setAttribute('aria-label', `Add ${itemName} to cart`);
        }
    });
    
    // Add ARIA labels to remove buttons
    const removeButtons = document.querySelectorAll('.remove-from-cart');
    removeButtons.forEach(button => {
        const itemName = button.dataset.itemName;
        if (itemName) {
            button.setAttribute('aria-label', `Remove ${itemName} from cart`);
        }
    });
}

function closeAllModals() {
    const modals = document.querySelectorAll('.modal.show');
    modals.forEach(modal => {
        const modalInstance = bootstrap.Modal.getInstance(modal);
        if (modalInstance) {
            modalInstance.hide();
        }
    });
}

// Smooth scrolling for anchor links
function initializeSmoothScrolling() {
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });
}

// Parallax scrolling effect
function initializeParallax() {
    const parallaxElements = document.querySelectorAll('.parallax');
    
    window.addEventListener('scroll', () => {
        const scrolled = window.pageYOffset;
        
        parallaxElements.forEach(element => {
            const speed = element.dataset.speed || 0.5;
            const yPos = -(scrolled * speed);
            element.style.transform = `translateY(${yPos}px)`;
        });
    });
}

// Tooltips enhancement
function enhanceTooltips() {
    const tooltips = document.querySelectorAll('[data-bs-toggle="tooltip"]');
    tooltips.forEach(tooltip => {
        // Add custom tooltip styling
        tooltip.addEventListener('shown.bs.tooltip', function() {
            const tooltipElement = document.querySelector('.tooltip');
            if (tooltipElement) {
                tooltipElement.classList.add('custom-tooltip');
            }
        });
    });
}

// Form validation enhancement
function enhanceFormValidation() {
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                
                // Focus on first invalid field
                const firstInvalid = form.querySelector(':invalid');
                if (firstInvalid) {
                    firstInvalid.focus();
                    showNotification('Please fill in all required fields correctly', 'error');
                }
            }
        });
    });
}

// Print functionality
function initializePrintFunctionality() {
    const printButtons = document.querySelectorAll('.print-btn');
    printButtons.forEach(button => {
        button.addEventListener('click', function() {
            const printContent = this.dataset.printContent;
            if (printContent) {
                const content = document.querySelector(printContent);
                if (content) {
                    printContent(content);
                }
            }
        });
    });
}

function printContent(content) {
    const printWindow = window.open('', '_blank');
    const printDocument = content.cloneNode(true);
    
    // Add print-specific styles
    const printStyles = `
        <style>
            body { font-family: Arial, sans-serif; }
            .no-print { display: none !important; }
            .print-only { display: block !important; }
            @media print {
                body { margin: 0; padding: 20px; }
                .print-break { page-break-before: always; }
            }
        </style>
    `;
    
    printWindow.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Print</title>
            ${printStyles}
        </head>
        <body>
            ${printDocument.innerHTML}
        </body>
        </html>
    `);
    
    printWindow.document.close();
    printWindow.print();
    printWindow.close();
}

// Export functions for global use
window.initializeTheme = initializeTheme;
window.toggleTheme = toggleTheme;
window.printContent = printContent;