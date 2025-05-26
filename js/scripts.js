const singUpButton =document.getElementById('singUpButton');
const singInButton =document.getElementById('singInButton');
const singUPForm =document.getElementById('signup');
const singInForm =document.getElementById('login');
singUpButton.addEventListener('click',function(){
    singInForm.style.display="none";
    singUPForm.style.display="block";
})

singInButton.addEventListener('click',function(){
    singInForm.style.display="block";
    singUPForm.style.display="none";
})
