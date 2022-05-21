const close = document.getElementById('close');
const message = document.querySelector('.message-error, .message-success');

if (close) {
    function closeMessage() {
        close.style.display = 'none';
        message.remove();
    }
    // Closing manually and by timeout.
    close.addEventListener("click", () => closeMessage());
    setTimeout(() => closeMessage(), 6000);
}

//Form
let email = document.getElementById('email');
let firstname = document.getElementById('firstname');
let lastname = document.getElementById('lastname');
let password = document.getElementById('password');
let passwordRepeat = document.getElementById('password-repeat');
let btnValidate = document.getElementById('submit');
let contain = document.querySelector('.contain');


//checking form fields if form is register-form.
if (btnValidate && document.getElementById('form-register')) {

    contain.style.color = "red";
    contain.style.textAlign = "center";
    contain.style.padding = "2rem";

    btnValidate.addEventListener("click", function (e) {
        // Checking fields are not empty.
        if (email.value === "" || firstname.value === "" || lastname.value === "" || passwordRepeat.value === "") {
            e.preventDefault();
            contain.innerHTML = "Veuillez remplir tout les champs";
            deleteMessage(contain);
        }
        // Checking fields length.
        else if(
            (email.value.length <= 5 || email.value.length >= 70) ||
            (firstname.value.length <= 2 || firstname.value.length >= 150) ||
            (lastname.value.length <= 2 || lastname.value.length >= 150)
        ) {
            e.preventDefault();
            contain.innerHTML = "Votre email doit comporter entre 5 et 70 caractères et votre prénom doit être compris entre 2 et 150 caractères";
            deleteMessage(contain);
        }
        // checking password strength.
        else if (!password.value.match(/^(?=.*[0-9])(?=.*[!@#$%^&*])[a-zA-Z0-9!@#$%^&*]{7,150}$/)){
            e.preventDefault();
            contain.innerHTML =
                "Votre mot de passe doit contenir entre 7 et 150 caractères, avoir une majuscule, une minuscule, " +
                "un nombre et un caractère spécial";
        }
    })
}

// time message error
function deleteMessage(contain) {
    let timeout = setTimeout(function () {
        contain.innerHTML = "";
        clearTimeout(timeout);
    }, 6000);
}


