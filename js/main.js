// DOM Elements
const themeToggle = document.getElementById('theme-toggle');
const particlesContainer = document.getElementById('particles-js');

// Theme Toggle
themeToggle.addEventListener('click', () => {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
});

// Check for saved theme preference
if (localStorage.getItem('theme') === 'light') {
    document.documentElement.classList.remove('dark');
}


    // Fix for particles.js sometimes not filling the screen
    window.addEventListener('resize', function() {
        if (typeof pJSDom !== 'undefined' && pJSDom.length > 0) {
            pJSDom[0].pJS.fn.vendors.destroypJS();
            pJSDom[0].pJS.fn.vendors.start();
        }
    });

    
// Smooth Scroll for Navigation Links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});

// Intersection Observer for Fade-in Animations
const observerOptions = {
    root: null,
    rootMargin: '0px',
    threshold: 0.1
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fade-in');
            observer.unobserve(entry.target);
        }
    });
}, observerOptions);

document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
});

// Form Handling
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const data = Object.fromEntries(formData.entries());

        try {
            // Handle different form types
            if (form.id === 'login-form') {
                await signIn(data.email, data.password);
            } else if (form.id === 'signup-form') {
                await signUp(data.email, data.password);
            } else if (form.id === 'project-form') {
                await addProject(data);
            } else if (form.id === 'event-form') {
                await addEvent(data);
            }

            // Show success message
            showNotification('Success!', 'success');
        } catch (error) {
            // Show error message
            showNotification(error.message, 'error');
        }
    });
});

// Notification System
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `fixed top-4 right-4 p-4 rounded-lg shadow-lg ${
        type === 'success' ? 'bg-green-500' :
        type === 'error' ? 'bg-red-500' :
        'bg-blue-500'
    } text-white`;
    notification.textContent = message;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('opacity-0', 'transition-opacity', 'duration-300');
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Mobile Menu Toggle
const mobileMenuButton = document.createElement('button');
mobileMenuButton.className = 'md:hidden p-2';
mobileMenuButton.innerHTML = '<i class="fas fa-bars"></i>';
document.querySelector('nav .flex').prepend(mobileMenuButton);

const mobileMenu = document.createElement('div');
mobileMenu.className = 'hidden md:hidden fixed inset-0 bg-secondary z-40';
mobileMenu.innerHTML = `
    <div class="container mx-auto px-4 py-8">
        <div class="flex justify-end">
            <button class="p-2" id="close-menu">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="flex flex-col space-y-4 mt-8">
            <a href="#about" class="text-xl">About</a>
            <a href="#pillars" class="text-xl">Pillars</a>
            <a href="#subgroups" class="text-xl">Sub-Groups</a>
            <a href="#projects" class="text-xl">Projects</a>
            <a href="#events" class="text-xl">Events</a>
            <a href="#blog" class="text-xl">Blog</a>
        </div>
    </div>
`;
document.body.appendChild(mobileMenu);

mobileMenuButton.addEventListener('click', () => {
    mobileMenu.classList.remove('hidden');
});

document.getElementById('close-menu').addEventListener('click', () => {
    mobileMenu.classList.add('hidden');
});

// Load Projects
async function loadProjects() {
    try {
        const projectsSnapshot = await getProjects();
        const projectsContainer = document.getElementById('projects-container');
        
        projectsSnapshot.forEach(doc => {
            const project = doc.data();
            const projectCard = createProjectCard(project);
            projectsContainer.appendChild(projectCard);
        });
    } catch (error) {
        showNotification('Error loading projects', 'error');
    }
}

// Create Project Card
function createProjectCard(project) {
    const card = document.createElement('div');
    card.className = 'bg-secondary rounded-lg p-6 card-hover';
    card.innerHTML = `
        <h3 class="text-xl font-headline font-semibold mb-2">${project.title}</h3>
        <p class="text-gray-400 mb-4">${project.description}</p>
        <div class="flex flex-wrap gap-2 mb-4">
            ${project.tags.map(tag => `
                <span class="bg-primary px-3 py-1 rounded-full text-sm">${tag}</span>
            `).join('')}
        </div>
        <a href="${project.githubUrl}" target="_blank" class="text-accent hover:underline">
            View on GitHub
        </a>
    `;
    return card;
}

// Initialize
document.addEventListener('DOMContentLoaded', () => {
    loadProjects();
}); 