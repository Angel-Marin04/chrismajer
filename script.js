// ===========================
// HAMBURGER MENU TOGGLE
// ===========================
const hamburger = document.querySelector('.hamburger');
const navMenu = document.querySelector('.nav-menu');

if (hamburger && navMenu) {
    hamburger.addEventListener('click', (e) => {
        e.stopPropagation();
        hamburger.classList.toggle('active');
        navMenu.classList.toggle('active');
        console.log('Menu toggled. Active:', navMenu.classList.contains('active'));
    });

    // Close menu when a link is clicked
    document.querySelectorAll('.nav-menu a').forEach(link => {
        link.addEventListener('click', (e) => {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
            console.log('Menu closed from link click');
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', (e) => {
        if (!e.target.closest('.navbar')) {
            hamburger.classList.remove('active');
            navMenu.classList.remove('active');
        }
    });
}

// ===========================
// SMOOTH SCROLL NAVIGATION
// ===========================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        const href = anchor.getAttribute('href');
        if (href !== '#') {
            e.preventDefault();
            const target = document.querySelector(href);
            if (target) {
                target.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        }
    });
});

// ===========================
// ACTIVE LINK HIGHLIGHTING
// ===========================
window.addEventListener('scroll', () => {
    const scrollPosition = window.scrollY + 150;
    
    document.querySelectorAll('section[id]').forEach(section => {
        const sectionTop = section.offsetTop;
        const sectionBottom = sectionTop + section.offsetHeight;
        
        if (scrollPosition >= sectionTop && scrollPosition < sectionBottom) {
            document.querySelectorAll('.nav-link').forEach(link => {
                link.classList.remove('active');
            });
            const activeLink = document.querySelector(`.nav-link[href="#${section.id}"]`);
            if (activeLink) {
                activeLink.classList.add('active');
            }
        }
    });
});

// ===========================
// FORM VALIDATION & SUBMISSION
// ===========================
const contactForms = document.querySelectorAll('#contactForm');

contactForms.forEach(contactForm => {
    contactForm.addEventListener('submit', function(e) {
        const firstName = this.querySelector('#firstName')?.value.trim() || '';
        const lastName = this.querySelector('#lastName')?.value.trim() || '';
        const email = this.querySelector('#email')?.value.trim() || '';
        const phone = this.querySelector('#phone')?.value.trim() || '';
        const message = this.querySelector('#message')?.value.trim() || '';
        
        // Validation
        if (!firstName || !lastName || !email || !phone || !message) {
            e.preventDefault();
            alert('Por favor completa todos los campos requeridos (*)');
            return false;
        }
        
        // Email validation
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Por favor ingresa un email válido');
            return false;
        }
        
        // Allow form submission to Formspree
        return true;
    });
});

// ===========================
// NAVBAR SCROLL EFFECT
// ===========================
const navbar = document.querySelector('.navbar');
if (navbar) {
    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.style.boxShadow = '0 4px 20px rgba(0, 0, 0, 0.15)';
        } else {
            navbar.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.08)';
        }
    });
}
