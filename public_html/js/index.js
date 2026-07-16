const inputs = document.querySelectorAll('.input');
const form = document.querySelector('form');
const selects = document.querySelectorAll('.select');
// const [firstname, email, phone, city, entity, setor, cargo] = inputs;
const checkbox = document.querySelector('input[type="checkbox"]');


for (let input of inputs) {
    input.addEventListener('focus', () => {
        let parent = input.parentElement;
        let label = parent.querySelector('label')
        label.className = "label focus"
        label.style.visibility = "visible"
    })

    input.addEventListener('blur', () => {
        let parent = input.parentElement;
        let label = parent.querySelector('label')
        label.className = "label";
        label.style.visibility = "hidden"
    })
}

form.addEventListener('submit', (e) => {
    e.preventDefault();
    validateInputs();

})

function validateInputs() {

    for (let input of inputs) {

        if (input.name !== "website") {
            if (input.value.trim() === "") showError(input, "Este campo é obrigatório");
            else {
                showSuccess(input)
            }
        }
    }

    for (let select of selects) {

        if (select.name !== "website") {
            if (select.value.trim() === "") showError(select, "Este campo é obrigatório");
            else {
                showSuccess(select)
            }
        }
    }

}

function showError(input, message) {
    const formControl = input.parentElement;
    const errorMessage = formControl.querySelector('small');
    errorMessage.innerText = message;
    formControl.className = "form-control text error"
}

function showSuccess(input) {
    const formControl = input.parentElement;
    formControl.className = "form-control text"
}

function HabiDsabi() {
    if (document.getElementById('habi').checked == true) {
        document.getElementById('gravar').disabled = ""
    }
    if (document.getElementById('habi').checked == false) {
        document.getElementById('gravar').disabled = "disabled"
    }
    if (!checkbox.checked) showError(checkbox, "Você concorda?")
    else {
        showSuccess(checkbox);
    }
}

function assinar() {

    var input1 = document.querySelector("#firstname");
    // var input2 = document.querySelector("#lastname");
    var input3 = document.querySelector("#email");
    var input4 = document.querySelector("#phone");
    var input5 = document.querySelector("#city");
    var input6 = document.querySelector("#entity");
    var input7 = document.querySelector("#setor");
    var input8 = document.querySelector("#cargo");
    var checkbox = document.querySelector("#habi");

    var firstname = input1.value;
    // var nome2 = input2.value;
    var email = input3.value;
    var phone = input4.value;
    var city = input5.value;
    var entity = input6.value;
    var setor = input7.value;
    var cargo = input8.value;
    var habi = checkbox.value;

    if (!firstname == "" || !firstname == null) {

        if (!phone == "" || !phone == null) {

            if (!city == "" || !city == null) {

                if (!entity == "" || !entity == null) {

                    if (!setor == "" || !setor == null) {

                        if (!cargo == "" || !cargo == null) {

                            // const arr = [firstname, email, contato, cidade, entidade, setor, cargo];

                            // console.log(arr)
                            window.location.href = 'form_inscricao.php';

                        }

                    }

                }

            }
        }

    }
}

function goBack() {
    window.history.back()
}
