

// smooth scroll
$(document).ready(function(){
    $(".navbar .nav-link").on('click', function(event) {

        if (this.hash !== "") {

            event.preventDefault();

            var hash = this.hash;

            $('html, body').animate({
                scrollTop: $(hash).offset().top
            }, 700, function(){
                window.location.hash = hash;
            });
        } 
    });
});

// navbar toggle
$('#nav-toggle').click(function(){
    $(this).toggleClass('is-active')
    $('ul.nav').toggleClass('show');
});

//  function generateOptionInputs() {
//             var numOptions = document.getElementById('optionNum').value;
//             var optionsContainer = document.getElementById('optionsContainer');
//             optionsContainer.innerHTML = '';

//             for (var i = 0; i < numOptions; i++) {
//                 var optionInput = document.createElement('input');
//                 optionInput.type = 'text';
//                 optionInput.name = 'options[]';
//                 optionInput.placeholder = 'Option ' + (i + 1);
//                 optionInput.required = true;
//                 optionsContainer.appendChild(optionInput);

//                 var radioInput = document.createElement('input');
//                 radioInput.type = 'radio';
//                 radioInput.name = 'correct_option';
//                 radioInput.value = i;
//                 radioInput.required = true;
//                 optionsContainer.appendChild(radioInput);

//                 optionsContainer.appendChild(document.createElement('br'));
//             }
//         }

   function confirmAction(event, action) {
        const confirmation = confirm(`Are you sure you want to ${action} this category?`);
        if (!confirmation) {
            event.preventDefault();
        }
    }
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.getElementsByClassName('alert');
    Array.from(alerts).forEach(alert => {
        setTimeout(function() {
            alert.style.display = 'none';
            alert.remove();
        }, 3000);
    });
});