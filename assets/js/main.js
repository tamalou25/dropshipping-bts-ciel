/**
 * JavaScript principal du site
 * @author tamalou25
 * @version 1.0
 */

// ============================================
// MOBILE MENU
// ============================================

const mobileToggle = document.getElementById('mobileToggle');
const mobileMenu = document.getElementById('mobileMenu');
const mobileClose = document.getElementById('mobileClose');

if (mobileToggle) {
  mobileToggle.addEventListener('click', () => {
    mobileMenu.classList.add('active');
  });
}

if (mobileClose) {
  mobileClose.addEventListener('click', () => {
    mobileMenu.classList.remove('active');
  });
}

// Fermer le menu en cliquant en dehors
if (mobileMenu) {
  document.addEventListener('click', (e) => {
    if (!mobileMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
      mobileMenu.classList.remove('active');
    }
  });
}

// ============================================
// GESTION PANIER (Ajout produit)
// ============================================

const addToCartForms = document.querySelectorAll('.add-to-cart-form');

addToCartForms.forEach(form => {
  form.addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const formData = new FormData(form);
    const button = form.querySelector('button[type="submit"]');
    const originalText = button.innerHTML;
    
    // Animation bouton
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout...';
    button.disabled = true;
    
    try {
      const response = await fetch(form.action, {
        method: 'POST',
        body: formData
      });
      
      if (response.ok) {
        // Succès
        button.innerHTML = '<i class="fas fa-check"></i> Ajouté !';
        button.classList.add('btn-success');
        
        // Mettre à jour le compteur du panier
        updateCartCount();
        
        // Réinitialiser après 2 secondes
        setTimeout(() => {
          button.innerHTML = originalText;
          button.classList.remove('btn-success');
          button.disabled = false;
        }, 2000);
      } else {
        throw new Error('Erreur lors de l\'ajout au panier');
      }
    } catch (error) {
      console.error(error);
      button.innerHTML = '<i class="fas fa-times"></i> Erreur';
      button.classList.add('btn-error');
      
      setTimeout(() => {
        button.innerHTML = originalText;
        button.classList.remove('btn-error');
        button.disabled = false;
      }, 2000);
    }
  });
});

// ============================================
// MISE À JOUR COMPTEUR PANIER
// ============================================

async function updateCartCount() {
  try {
    const response = await fetch(window.location.origin + '/dropshipping-bts-ciel/public/cart-count.php');
    const data = await response.json();
    
    const badges = document.querySelectorAll('.nav-icon .badge');
    badges.forEach(badge => {
      badge.textContent = data.count;
      
      // Animation
      badge.style.transform = 'scale(1.3)';
      setTimeout(() => {
        badge.style.transform = 'scale(1)';
      }, 300);
    });
  } catch (error) {
    console.error('Erreur mise à jour compteur:', error);
  }
}

// ============================================
// GESTION QUANTITÉ PANIER
// ============================================

const quantityInputs = document.querySelectorAll('.quantity-input');

quantityInputs.forEach(input => {
  input.addEventListener('change', async (e) => {
    const productId = e.target.dataset.productId;
    const quantity = parseInt(e.target.value);
    
    if (quantity < 1) {
      e.target.value = 1;
      return;
    }
    
    try {
      const formData = new FormData();
      formData.append('product_id', productId);
      formData.append('quantity', quantity);
      formData.append('action', 'update');
      
      const response = await fetch(window.location.href, {
        method: 'POST',
        body: formData
      });
      
      if (response.ok) {
        // Recharger la page pour mettre à jour les totaux
        location.reload();
      }
    } catch (error) {
      console.error('Erreur mise à jour quantité:', error);
    }
  });
});

// ============================================
// CONFIRMATION SUPPRESSION
// ============================================

const deleteButtons = document.querySelectorAll('.btn-delete');

deleteButtons.forEach(button => {
  button.addEventListener('click', (e) => {
    if (!confirm('Êtes-vous sûr de vouloir supprimer cet élément ?')) {
      e.preventDefault();
    }
  });
});

// ============================================
// VALIDATION FORMULAIRES
// ============================================

const forms = document.querySelectorAll('form[data-validate]');

forms.forEach(form => {
  form.addEventListener('submit', (e) => {
    const requiredInputs = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredInputs.forEach(input => {
      if (!input.value.trim()) {
        isValid = false;
        input.classList.add('error');
        
        // Retirer la classe d'erreur au focus
        input.addEventListener('focus', () => {
          input.classList.remove('error');
        }, { once: true });
      }
    });
    
    if (!isValid) {
      e.preventDefault();
      alert('Veuillez remplir tous les champs obligatoires');
    }
  });
});

// ============================================
// SMOOTH SCROLL
// ============================================

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

// ============================================
// ANIMATIONS AU SCROLL
// ============================================

const observerOptions = {
  threshold: 0.1,
  rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
  entries.forEach(entry => {
    if (entry.isIntersecting) {
      entry.target.style.opacity = '1';
      entry.target.style.transform = 'translateY(0)';
    }
  });
}, observerOptions);

// Observer tous les cards
document.querySelectorAll('.card, .product-card').forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(20px)';
  card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
  observer.observe(card);
});

// ============================================
// NOTIFICATIONS
// ============================================

function showNotification(message, type = 'success') {
  const notification = document.createElement('div');
  notification.className = `notification notification-${type}`;
  notification.textContent = message;
  notification.style.cssText = `
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 1rem 1.5rem;
    background: ${type === 'success' ? '#10b981' : '#ef4444'};
    color: white;
    border-radius: 0.5rem;
    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    z-index: 9999;
    animation: slideIn 0.3s ease;
  `;
  
  document.body.appendChild(notification);
  
  setTimeout(() => {
    notification.style.animation = 'slideOut 0.3s ease';
    setTimeout(() => notification.remove(), 300);
  }, 3000);
}

// Ajouter les animations CSS
const style = document.createElement('style');
style.textContent = `
  @keyframes slideIn {
    from {
      transform: translateX(100%);
      opacity: 0;
    }
    to {
      transform: translateX(0);
      opacity: 1;
    }
  }
  
  @keyframes slideOut {
    from {
      transform: translateX(0);
      opacity: 1;
    }
    to {
      transform: translateX(100%);
      opacity: 0;
    }
  }
  
  .error {
    border-color: #ef4444 !important;
  }
`;
document.head.appendChild(style);

console.log('🚀 Site dropshipping BTS CIEL chargé');