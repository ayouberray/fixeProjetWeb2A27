// Validation du formulaire d'avis
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('avisForm');
    if (!form) return;
    
    form.addEventListener('submit', function(e) {
        let errors = [];
        
        const note = document.querySelector('input[name="note"]:checked')?.value;
        const satisfaction = document.getElementById('satisfaction')?.value;
        
        if (!note) {
            errors.push("Veuillez sélectionner une note");
        }
        
        if (!satisfaction) {
            errors.push("Veuillez sélectionner un niveau de satisfaction");
            highlightError('satisfaction');
        } else {
            removeError('satisfaction');
        }
        
        if (errors.length > 0) {
            e.preventDefault();
            showAvisErrors(errors);
            return false;
        }
    });
    
    // Animation des étoiles
    const stars = document.querySelectorAll('.star-rating i');
    stars.forEach((star, index) => {
        star.addEventListener('mouseover', function() {
            for (let i = 0; i <= index; i++) {
                stars[i].classList.add('fas');
                stars[i].classList.remove('far');
                stars[i].style.color = '#f59e0b';
            }
        });
        
        star.addEventListener('mouseout', function() {
            const checked = document.querySelector('input[name="note"]:checked');
            const checkedValue = checked ? parseInt(checked.value) : 0;
            
            for (let i = 0; i < stars.length; i++) {
                if (i < checkedValue) {
                    stars[i].classList.add('fas');
                    stars[i].classList.remove('far');
                    stars[i].style.color = '#f59e0b';
                } else {
                    stars[i].classList.add('far');
                    stars[i].classList.remove('fas');
                    stars[i].style.color = '';
                }
            }
        });
        
        star.addEventListener('click', function() {
            const value = parseInt(this.getAttribute('data-value'));
            const radio = document.querySelector(`input[name="note"][value="${value}"]`);
            if (radio) radio.checked = true;
        });
    });
});

function showAvisErrors(errors) {
    const oldAlert = document.querySelector('.alert-error');
    if (oldAlert) oldAlert.remove();
    
    const alertDiv = document.createElement('div');
    alertDiv.className = 'alert alert-error';
    alertDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i><div>' + errors.join('<br>') + '</div>';
    
    const form = document.getElementById('avisForm');
    if (form) {
        form.insertBefore(alertDiv, form.firstChild);
        setTimeout(() => alertDiv.remove(), 5000);
    }
}