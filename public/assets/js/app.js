const close = document.getElementById('close');
const message = document.querySelector('.message');

if (close) {
    close.addEventListener("click", ()=> {
        close.style.display = 'none';
        message.remove();
    })
}

//Form
let email = document.getElementById('email');
let firstname = document.getElementById('firstname');
let lastname = document.getElementById('lastname');
let password = document.getElementById('password');
let passwordRepeat = document.getElementById('password-repeat');
let btnValidate = document.getElementById('submit');
let contain = document.querySelector('.contain');


//checking form fields
if (btnValidate) {
    btnValidate.addEventListener("click", function (e) {
        if (email.value === "" || firstname.value === "" || lastname.value === "" || passwordRepeat.value === "") {
            e.preventDefault();
            contain.innerHTML = "Veuillez remplir tout les champs";
            contain.style.color = "red";
            contain.style.textAlign = "center";
            contain.style.padding = "2rem";
            deleteMessage(contain);
        }
        else if(email.value.length >= 5 || email.value.length <= 70 || firstname.value.length >= 2 || firstname.value.length <= 150 ||
        lastname.value.length >= 2 || lastname.value.length <= 150 || password.value.length >= 7 || password.value.length <= 70) {
            e.preventDefault();
            contain.innerHTML = "Votre email doit comporter entre 5 et 70 caractères, votre prénom doit être compris entre 2 et 150 caractères" +
                "et votre password doit être compris enter 7 et 70 caractères";
            contain.style.color = "red";
            contain.style.textAlign = "center";
            contain.style.padding = "2rem";
            deleteMessage(contain);
        }
    })
}

// time message error
function deleteMessage(contain) {
    let timeout = setTimeout(function () {
        contain.innerHTML = "";
        clearTimeout(timeout);
    }, 3000);
}


