//This script for login form
function validateForm() {
    let email = document.getElementById('email').value;
    let password = document.getElementById('password').value;
    let emailError = document.getElementById('emailError');
    let passwordError = document.getElementById('passwordError');
    let valid = true;

    // Regular expression for basic email validation
    let emailPattern = /^[^ ]+@[^ ]+\.[a-z]{2,3}$/;

    // Check if email matches the pattern
    if (!email.match(emailPattern)) {
        // عرض رسالة الخطأ
        emailError.style.display = 'block';
        valid = false;
    } else {
        emailError.style.display = 'none';
    }

    // Password validation using the stronger criteria
    if (!validatePassword(password)) {
        passwordError.style.display = 'block';
        valid = false;
    } else {
        passwordError.style.display = 'none';
    }

    return valid;
}

function validatePassword(password) {
    // شروط التحقق من كلمة المرور
    const minLength = password.length >= 8; // لا تقل عن 8 أحرف
    const hasUpperCase = /[A-Z]/.test(password); // تحتوي على حرف كبير
    const hasLowerCase = /[a-z]/.test(password); // تحتوي على حرف صغير
    const hasNumber = /\d/.test(password); // تحتوي على رقم
    const hasSpecialChar = /[!@#$%^&*(),.?":{}|<>]/.test(password); // تحتوي على رمز خاص

    // إذا لم تتحقق الشروط، تعرض رسالة الخطأ
    if (!minLength || !hasUpperCase || !hasLowerCase || !hasNumber || !hasSpecialChar) {
        passwordError.innerText = 'Password must be at least 8 characters long, with one uppercase, one lowercase, one number, and one special character.';
        return false;
    }
    return true;
}

document.getElementById('togglePassword').addEventListener('click', function () {
    let passwordInput = document.getElementById('password');
    let icon = this.querySelector('i');

    // Toggle the type attribute using getAttribute() method
    if (passwordInput.getAttribute('type') === 'password') {
        passwordInput.setAttribute('type', 'text');
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        passwordInput.setAttribute('type', 'password');
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
});


 // this script for singup form
 
//  function validateForm() {
//     const username = document.getElementById('username').value;
//     const email = document.getElementById('email').value;
//     const password = document.getElementById('password').value;
//     let isValid = true;

//     // Username validation
//     const usernameError = document.getElementById('usernameError');
//     if (username.length < 3) {
//         usernameError.style.display = 'block';
//         isValid = false;
//     } else {
//         usernameError.style.display = 'none';
//     }

//     // Email validation
//     const emailError = document.getElementById('emailError');
//     const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
//     if (!emailRegex.test(email)) {
//         emailError.style.display = 'block';
//         isValid = false;
//     } else {
//         emailError.style.display = 'none';
//     }

//     // Password validation
//     const passwordError = document.getElementById('passwordError');
//     if (password.length < 6) {
//         passwordError.style.display = 'block';
//         isValid = false;
//     } else {
//         passwordError.style.display = 'none';
//     }

//     return isValid; // Prevent form submission if validation fails
// }

// function togglePassword() {
//     const passwordField = document.getElementById('password');
//     const toggleIcon = document.querySelector('.toggle-password');
//     if (passwordField.type === 'password') {
//         passwordField.type = 'text';
//         toggleIcon.classList.remove('fa-eye');
//         toggleIcon.classList.add('fa-eye-slash');
//     } else {
//         passwordField.type = 'password';
//         toggleIcon.classList.remove('fa-eye-slash');
//         toggleIcon.classList.add('fa-eye');
//     }
// }

//This Script is for Report page 
        const moodTrendsCtx = document.getElementById('moodTrendsChart').getContext('2d');
        const moodTrendsChart = new Chart(moodTrendsCtx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Mood Score',
                    data: [3, 4, 2, 5, 4, 3, 5],
                    borderColor: '#3498db',
                    fill: false,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        const activityImpactCtx = document.getElementById('activityImpactChart').getContext('2d');
        const activityImpactChart = new Chart(activityImpactCtx, {
            type: 'bar',
            data: {
                labels: ['Exercise', 'Reading', 'Meditation', 'Work', 'Social'],
                datasets: [{
                    label: 'Mood Improvement',
                    data: [5, 3, 4, 2, 3],
                    backgroundColor: '#2ecc71'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        const weeklySummaryCtx = document.getElementById('weeklySummaryChart').getContext('2d');
        const weeklySummaryChart = new Chart(weeklySummaryCtx, {
            type: 'pie',
            data: {
                labels: ['Happy', 'Neutral', 'Sad'],
                datasets: [{
                    label: 'Weekly Summary',
                    data: [50, 30, 20],
                    backgroundColor: ['#2ecc71', '#f1c40f', '#e74c3c']
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    }
                }
            }
        });

        function downloadReport() {
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF();
            doc.text("Weekly Mood Report", 10, 10);
            doc.text("Date Range: Last Week", 10, 20);
            doc.text("Mood Trends: [Sample data]", 10, 30);
            doc.text("Activity Impact: [Sample data]", 10, 40);
            doc.text("Weekly Summary: [Sample data]", 10, 50);
            doc.save('report.pdf');
        }
